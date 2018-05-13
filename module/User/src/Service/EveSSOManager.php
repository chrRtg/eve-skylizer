<?php

namespace User\Service;

use Zend\Authentication\Result;
use Evelabs\OAuth2\Client\Provider\EveOnline;


/**
 * The EveSSOManager is to support an EVE-SSO login. 
 * @see https://support.eveonline.com/hc/en-us/articles/205381192-Single-Sign-On-SSO-
 * 
 * After a successfull confirmation of your identity by EVE-SSO as the "identity provider"
 * this class also checks against some configured rules like your player name or his 
 * coporation name if the EVE-user is allowed to access this application.
 * @see config/autoload/local.php - Section "eve_sso"
 *
 */
class EveSSOManager {

	// Constants returned by the access filter.
	const ACCESS_GRANTED = 1; // Access to the page is granted.
	const AUTH_REQUIRED = 2; // Authentication is required to see the page.
	const ACCESS_DENIED = 3; // Access to the page is denied.

	/**
	 * Authentication service.
	 * @var \Zend\Authentication\AuthenticationService
	 */

	private $authService;

	/**
	 * RBAC manager.
	 * @var \User\Service\RbacManager
	 */
	private $rbacManager;

	/**
	 * Session manager.
	 * @var \Zend\Session\Container;
	 */
	private $sessionContainer;

	/**
	 * User manager.
	 * @var \User\Service\UserManager
	 */
	private $userManager;

	/**
	 *
	 * @var \Application\Service\EveEsiManager
	 */
	private $eveESIManager;
	
	/**
	 *
	 * @var \Application\Controller\Plugin\LoggerPlugin
	 */
	private $logger;

	/**
	 *
	 * @var \Evelabs\OAuth2\Client\Provider\EveOnline
	 */
	private $evesso_provider;
	
	/**
	 * 
	 * @var bool
	 */
	private $isAdmin = false;

	/**
	 *
	 * @var string
	 */
	private $responseMessage = '';

	/**
	 * 
	 * @var array 
	 */
	private $config;

	/**
	 * 
	 * @var array 
	 */
	private $evesso_config;
	

	/**
	 * Constructs the service.
	 */
	public function __construct($authService, $rbacManager, $sessionContainer, $config, $userManager, $eveESIManager, $logger)
	{
		$this->authService = $authService;
		$this->rbacManager = $rbacManager;
		$this->config = $config;
		$this->sessionContainer = $sessionContainer;
		$this->userManager = $userManager;
		$this->eveESIManager = $eveESIManager;
		$this->logger = $logger;

		// get EveSSO Config
		if (isset($this->config['eve_sso'])) {
			$this->evesso_config = $this->config['eve_sso'];
		} else {
			$this->evesso_config = [];
		}
		// how to set & read session vars
		// $this->sessionContainer->testvar = 12;
		if ($this->evesso_config) {
			$this->evesso_provider = new \Evelabs\OAuth2\Client\Provider\EveOnline([
				'clientId' => $this->evesso_config['clientId'],
				'clientSecret' => $this->evesso_config['clientSecret'],
				'redirectUri' => $this->evesso_config['redirectUri'],
			]);
		} else {
			$this->logger->crit('no config for eve_sso found');
		}
	}

	/**
	 * Oauth2 login with Eve-Online SSO service.
	 * 
	 * 
	 * @param \Zend\Mvc\Controller\Plugin\Params $url_param
	 * @return string
	 */
	public function eveSsoLogin($url_param)
	{
		if (!$url_param->fromQuery('code')) {

			if ($this->sessionContainer->token) {
				// we are still logged in
				// Refresh token if required
				$this->refreshSsoToken();

				return('viewuser');
			}

			// here we can set requested scopes but it is optional
			// make sure you have them enabled on your app page at
			// https://developers.eveonline.com/applications/
			$options = [
				'scope' => $this->evesso_config['scope'] // only ask for what we really need
			];

			// If we don't have an authorization code then get one
			$authUrl = $this->evesso_provider->getAuthorizationUrl($options);
			$this->sessionContainer->oauth2state = $this->evesso_provider->getState();
			$this->sessionContainer->token = null;
			$this->sessionContainer->eveauth = null;
			header('Location: ' . $authUrl);
			exit;

			// Check given state against previously stored one to mitigate CSRF attack
		} elseif (empty($url_param->fromQuery('state')) || ($url_param->fromQuery('state') !== $this->sessionContainer->oauth2state)) {
			$this->sessionContainer->oauth2state = null;
			return('invalid_state');
		} else {
			if (!$this->sessionContainer->token) {
				// Try to get an access token (using the authorization code grant)
				$this->sessionContainer->token = $this->evesso_provider->getAccessToken('authorization_code', [
					'code' => $url_param->fromQuery('code')
				]);
			}

			// Refresh token if required
			//$this->refreshSsoToken();
			$this->logger->debug("token: " . print_r($this->sessionContainer->token, true));

			// successfull authentification against Eve SSO
			$this->sessionContainer->eveauth = array();
			$this->sessionContainer->eveauth['eve_app']['client_id'] = $this->evesso_config['clientId'];
			$this->sessionContainer->eveauth['eve_app']['client_secret'] = $this->evesso_config['clientSecret'];
			$this->sessionContainer->eveauth['eve_app']['client_scope'] = $this->evesso_config['scope'];
			$this->sessionContainer->eveauth['eve_user']['token'] = $this->sessionContainer->token->getToken();
			$this->sessionContainer->eveauth['eve_user']['refresh_token'] = $this->sessionContainer->token->getRefreshToken();
			$this->sessionContainer->eveauth['eve_user']['token_expires'] = $this->sessionContainer->token->getExpires();

			$authres = $this->authenticateEveChar();

			if (!$authres) {
				$this->clearIdentity();
				return('auth_fail');
			}
			return('viewuser');
		}

		return('unknown');
	}

	/**
	 * Get character information from EVE-ESI and persist in local session.
	 * Writes array to session $this->sessionContainer->eveauth
	 * Also authenticate identity to Zend/Authentification/Adapter
	 * 
	 * @return boolean true on success
	 */
	private function authenticateEveChar()
	{
		$sso_user = $this->evesso_provider->getResourceOwner($this->sessionContainer->token);

		if (empty($sso_user)) {
			$this->responseMessage = 'Can not get User-Ressource from EveSSO';
			return(false);
		}

		// get char data from ESI
		$character = $this->eveESIManager->publicRequest('get', '/characters/{character_id}', [
			'character_id' => $sso_user->getCharacterId(),
		]);

		// get his corporation details from ESI
		$corporation = $this->eveESIManager->publicRequest('get', '/corporations/{corporation_id}', [
			'corporation_id' => $character->corporation_id,
		]);


		$this->logger->debug('EveSSO, checkCredentials for user: __' . $sso_user->getCharacterName() . '__  belongs to: __' . ($corporation->name ? $corporation->name : 'ERR:not-resolved') . '__');

		// check if user is allowed to log in (by player name or because of his corporation
		if (isset($corporation->name) && !$this->checkEveCredentials($sso_user->getCharacterName(), $corporation->name)) {
			$this->logger->notice("login attempt failed for " . $sso_user->getCharacterName() . " [" . $corporation->name . "]");
			$this->responseMessage = 'This user is not allowed to use this tool.';
			return(false);
		}

		$appuser = $this->userManager->getOrAddUser($sso_user->getCharacterId(), $character, $corporation, $this->isAdmin);
		$this->logger->debug("got/created user from db: " . $appuser->getEveUsername());

		if (empty($appuser)) {
			$this->responseMessage = 'Can not fetch local User. This is most probably an error you should report to the administrator';
			return(false);
		}

		// store users details and character to session
		$this->sessionContainer->eveauth['app']['eve_name'] = $sso_user->getCharacterName();
		$this->sessionContainer->eveauth['app']['eve_id'] = $sso_user->getCharacterId();
		$this->sessionContainer->eveauth['app']['appusr_id'] = $appuser->getId();
		$this->sessionContainer->eveauth['app']['character'] = $character;
		$this->sessionContainer->eveauth['app']['corporation'] = $corporation;


		// connect to Zend/Authenfication
		$authAdapter = $this->authService->getAdapter();
		$authAdapter->setEveId($sso_user->getCharacterId());
		$authAdapter->setEveName($sso_user->getCharacterName());
		$result = $this->authService->authenticate();
		if (!isset($result) || $result->getCode() != 1 ) {
			$this->logger->notice("login attempt failed for " . $sso_user->getCharacterName() . " [" . $corporation->name . "], reason: " . print_r($result->getMessages(),true));
			$this->responseMessage = $result->getMessages();
			return(false);
		}

		return(true);
	}

	/**
	 * Check credentials in local configuration, see ['eve_sso']['auth']
	 *  
	 * @param string $user
	 * @param string $corporation
	 * @return boolean	true if user is allowed to authenticate
	 */
	private function checkEveCredentials($user, $corporation)
	{

		if ($this->evesso_config['auth']) {
			$auth = $this->evesso_config['auth'];
		} else {
			$this->logger->crit("configuration missing for ['eve_sso']['auth']");
			return(false);
		}

		$authenticated = false;
		$this->isAdmin = false;
		
		// handle denied users 
		if (isset($auth['user_deny']) && in_array($user, $auth['user_deny'])) {
			$this->logger->debug("auth DENIED by user_deny");
			$authenticated = false;
		}

		// first check if user is authenticated by corp or username
		if (isset($auth['corp_allow']) && in_array($corporation, $auth['corp_allow'])) {
			$this->logger->debug("auth true by corp_allow");
			$authenticated = true;
		} else if (isset($auth['user_allow']) && in_array($user, $auth['user_allow'])) {
			$this->logger->debug("auth true by user_allow");
			$authenticated = true;
		} else if (isset($auth['admin']) && in_array($user, $auth['admin'])) {
			$this->logger->debug("auth true by admin");
			$this->isAdmin = true;
			return(true);
		} else if (isset($auth['allow_all']) && $auth['allow_all'] === 'YeS') {
			$this->logger->debug("auth true for anybody");
			$authenticated = true;
			return(true);
		}

		return($authenticated);
	}

	/**
	 * Check, if user is authenticated against EVE SSO.
	 * 
	 * Silently call refreshSsoToken() to refresh the token if he had expired.
	 *  
	 * @return bool true if authenticated
	 */
	public function hasIdentity()
	{
		if (isset($this->sessionContainer) && isset($this->sessionContainer->token) && $this->sessionContainer->token) {
			// check, if token has to be renewed
			$this->refreshSsoToken();
			return(true);
		}

		return(false);
	}

	/**
	 * Logout
	 * 
	 * Clear the Eve-SSO session as well the Zend/Authentication
	 */
	public function clearIdentity()
	{
		$this->sessionContainer->token = false;
		$this->sessionContainer->oauth2state = false;
		$this->sessionContainer->eveauth = false;

		$this->authService->clearIdentity();
	}

	/**
	 * Get full Character information
	 * 
	 * @return array Eve characater name, ID and more, if not authenticated return false
	 */
	public function getFullIdentity()
	{
		if ($this->hasIdentity() && !empty($this->sessionContainer->eveauth['app'])) {
			return($this->sessionContainer->eveauth['app']);
		}

		return(false);
	}

	/**
	 * Get the users identity - the Eve Username
	 * 
	 * @return string
	 */
	public function getIdentity()
	{
		$char = $this->getFullIdentity();

		if (!$char || !isset($char['eve_name'])) {
			return false;
		}

		return($char['eve_name']);
	}

	/**
	 * Get the users Eve player ID
	 * 
	 * @return string
	 */
	public function getIdentityID()
	{
		$char = $this->getFullIdentity();

		if (!$char || !isset($char['eve_id'])) {
			return false;
		}

		return($char['eve_id']);
	}

	/**
	 * Get the Eve players Corporation name
	 * 
	 * @return string
	 */
	public function getIdentityCorp()
	{
		$char = $this->getFullIdentity();

		if (!$char || !isset($char['corporation']->name)) {
			return false;
		}

		return($char['corporation']->name);
	}

	/**
	 * This is a simple access control filter. It is able to restrict unauthorized
	 * users to visit certain pages.
	 * 
	 * This method uses the 'access_filter' key in the config file and determines
	 * whenther the current visitor is allowed to access the given controller action
	 * or not. It returns true if allowed; otherwise false.
	 * 
	 * @param string $controllerName
	 * @param string $actionName
	 * @return constant authenfication result
	 * @throws \Exception
	 */
	public function filterAccess($controllerName, $actionName)
	{
		//$this->logger->debug('AUTH (has:'.$this->hasIdentity().'): __' . $this->getIdentity() . '__ ask to access controller:__' . $controllerName . '__ with action __' . $actionName . '__');

		if ($this->isAdmin === true) {
			$this->logger->debug('AUTH FULL by admin');
			return self::ACCESS_GRANTED;
		}

		// if expired, try to refrest the token
		$this->refreshSsoToken();


		if (isset($this->config['access_filter']['controllers'][$controllerName])) {
			$items = $this->config['access_filter']['controllers'][$controllerName];
			//$this->logger->debug('role check');
			foreach ($items as $item) {
				$actionList = $item['actions'];
				$allow = $item['allow'];
				if (is_array($actionList) && in_array($actionName, $actionList) ||
					$actionList == '*') {
					//$this->logger->debug('found in action list');					
					if ($allow == '*') {
						// Anyone is allowed to see the page.
						//$this->logger->debug('allowed anyone');					
						return self::ACCESS_GRANTED;
					} else if (!$this->hasIdentity()) {
						//$this->logger->debug('allowed all with * identity');
						// Only authenticated user is allowed to see the page.
						return self::AUTH_REQUIRED;
					}

					if ($allow == '@') {
						//$this->logger->debug('allowed all with @ identity');
						// Any authenticated user is allowed to see the page.
						return self::ACCESS_GRANTED;
					} else if (substr($allow, 0, 1) == '@') {
						//$this->logger->debug('allowed for specific user');
						// Only the user with specific identity is allowed to see the page.
						$identity = substr($allow, 1);
						if ($this->getIdentity() == $identity) {
							//$this->logger->debug('... granted');
							return self::ACCESS_GRANTED;
						} else {
							//$this->logger->debug('... denied');
							return self::ACCESS_DENIED;
						}
					} else if (substr($allow, 0, 1) == '+') {
						// Only the user with this permission is allowed to see the page.
						$permission = substr($allow, 1);
						//$this->logger->debug('allowed for specific permission __'.$permission.'__');
						if ($this->rbacManager->isGranted(null, $permission)) {
							//$this->logger->debug('... granted');
							return self::ACCESS_GRANTED;
						} else {
							//$this->logger->debug('... denied');
							return self::ACCESS_DENIED;
						}
					} else {
						throw new \Exception('Unexpected value for "allow" - expected ' .
						'either "?", "@", "@identity" or "+permission"');
					}
				}
			}
		}

		$this->logger->info('AUTH (not set in config): __' . $this->getIdentity() . '__ ask to access controller:__' . $controllerName . '__ with action __' . $actionName . '__');

		if (!$this->hasIdentity()) {
			return self::AUTH_REQUIRED;
		} else {
			return self::ACCESS_DENIED;
		}

		// Permit access to this page.
		return self::ACCESS_GRANTED;
	}

	/**
	 * Get location of character in the EVE universe from ESI
	 * 
	 * @return array location ID and name or false if not authenticated or not in eve
	 */
	public function getUserLocation()
	{

		if (!$this->hasIdentity()) {
			return(false);
		}

		// get char data from ESI
		$location = $this->eveESIManager->authedRequest('get', '/characters/{character_id}/location/', [
			'character_id' => $this->getIdentityID(),
		]);

		return ($location);
	}

	/**
	 * Get the Eve player location as a systemID
	 * 
	 * @return boolean
	 */
	public function getUserLocationAsSystemID()
	{

		$location = $this->getUserLocation();

		if (empty($location)) {
			return false;
		}

		return($location->solar_system_id);
	}

	/**
	 * Refresh SSO token if expired.
	 * 
	 * Usually called only by hasIdentity()
	 * 
	 * @return void
	 */
	private function refreshSsoToken()
	{

		if (!empty($this->sessionContainer->token) && $this->sessionContainer->token->hasExpired()) {
			// This is how you refresh your access token once you have it
			$new_token = $this->evesso_provider->getAccessToken('refresh_token', [
				'refresh_token' => $this->sessionContainer->token->getRefreshToken()
			]);
			// Purge old access token and store new access token to your data store.
			$this->sessionContainer->token = $new_token;
		}
	}

	public function getMessage()
	{
		return($this->responseMessage);
	}

}
