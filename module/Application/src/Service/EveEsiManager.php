<?php

namespace Application\Service;

use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

/**
 * Description of EveEsiManager
 *
 * @author chr
 */
class EveEsiManager
{

    private $err_code = 0;
    private $err_msg = '';
    /**
     * Session manager.
     *
     * @var Laminas\Session\Container;
     */
    private $sessionContainer;
    private $logger;

    /**
     * Constructs the service.
     */
    public function __construct($sessionContainer, $logger)
    {
        $this->sessionContainer = $sessionContainer;
        $this->logger = $logger;

        $configuration = \Seat\Eseye\Configuration::getInstance();

        // disable Eseye internal logging
        $configuration->logger = \Seat\Eseye\Log\NullLogger::class;

        $configuration->http_user_agent = 'Skylizer';

        // set cache dir
        $configuration->file_cache_location = getcwd() . '/data/cache/esicache/';
    }

    /**
     * Send a request to Eve ESI which does not require authentification
     *
     * If ESI answers with an error code of 502 the request will be repeated five times.
     *
     * @param  string Method of the request (e.g. get, post )
     * @param  string the request (e.g. /characters/{character_id} )
     * @param  array variables from the request and their values (e.g. ['character_id' => 90585056] )
     * @return type
     */
    public function publicRequest($method, $request, $params)
    {
        $esi = new Eseye();

        $this->resetError();

        // make a call
        //$res = $esi->invoke($method, $request, $params);
        $try_cnt = 0;
        $max_tries = 5;

        do {
            try {
                $res = $esi->invoke($method, $request, $params);
            } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
                // ref: https://github.com/eveseat/eseye/wiki/Handling-Exceptions
                $error_msg = 'publicRequest :: RequestFailedException :: ' . $e->getEsiResponse()->error() . ' (' . $e->getEsiResponse()->getErrorCode() . ')';
                $this->logger->debug($error_msg . print_r($e->getEsiResponse(), true));

                if ((int) $e->getEsiResponse()->getErrorCode() != 502) {
                    $this->setError(6, $error_msg);
                    return false;
                }
    
                $try_cnt++;
                usleep(1000000);
                continue;
            }
            // if not caught return the result
            return ($res);
        } while ($try_cnt < $max_tries);

        // If we reach this point we found a exception to be handled by the global exception handling
        throw $e;
    }


    /**
     * Search Eve ESI /search endpoint
     *
     * @param string category, type of entitiy to search for
     * @param string search term
     * @return type
     */
    public function search($category, $term)
    {
        $esi = new Eseye();

        $this->resetError();

        $query = [
            'categories' => [$category],
            'strict' => 'false',
            'search' => $term
        ];

        try {
            $res = $esi->setQueryString($query)->invoke('get', '/search/');
        } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
            $error_msg = 'search :: RequestFailedException :: ' . $e->getEsiResponse()->error() . ' (' . $e->getEsiResponse()->getErrorCode() . ')';
            $this->logger->debug($error_msg . print_r($e->getEsiResponse(), true));

            $this->setError($e->getEsiResponse()->getErrorCode(), $error_msg);
            return false;
        }
        // if not caught return the result
        return ($res);
    }

    /**
     * Send an authentificated request to Eve ESI
     *
     * @param  string Method of the request (e.g. get, post )
     * @param  string the request (e.g. /characters/{character_id} )
     * @param  array variables from the request and their values (e.g. ['character_id' => 90585056] )
     * @param object EsiAuthentication
     * @return type
     */
    public function authedRequest($method, $request, $params, $authentication = null, $page = null)
    {
        $this->resetError();

        if (!$authentication) {
            // does the application is authed against Eve SSO?
            if (empty($this->sessionContainer->eveauth['eve_app']['client_id']) || empty($this->sessionContainer->eveauth['eve_user']['token'])) {
                $this->setErr(903, 'authedRequest to Eve ESI without having client_id and user-token');
                return false;
            }

            // Prepare an authentication container for Eseye
            $authentication = new EsiAuthentication(
                [
                    'client_id' => $this->sessionContainer->eveauth['eve_app']['client_id'],
                    'secret' => $this->sessionContainer->eveauth['eve_app']['client_secret'],
                    'scopes' => $this->sessionContainer->eveauth['eve_app']['client_scope'],
                    'access_token' => $this->sessionContainer->eveauth['eve_user']['token'],
                    'refresh_token' => $this->sessionContainer->eveauth['eve_user']['refresh_token'],
                    'token_expires' => date('Y-m-d H:i:s', $this->sessionContainer->eveauth['eve_user']['token_expires']),
                ]
            );
        }

        // $this->logger->debug('authedRequest: ' . print_r($authentication, true));

        // Instantiate a new Eseye instance.
        $esi = new Eseye($authentication);

        if ($page && \is_int($page)) {
            $esi->page($page);
        }

        // make a call
        try {
            $res = $esi->invoke($method, $request, $params);
        } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
            // ref: https://github.com/eveseat/eseye/wiki/Handling-Exceptions
            $error_msg = 'authedRequest :: RequestFailedException :: ' . $e->getEsiResponse()->error() . ' (' . $e->getEsiResponse()->getErrorCode() . ')';
            $this->logger->debug($error_msg . print_r($e->getEsiResponse(), true));

            $this->setError($e->getEsiResponse()->getErrorCode(), $error_msg);
            return false;
        }

        return ($res);
    }


    private function setCode($val = 0)
    {
        $this->err_code = $val;
    }

    public function getCode()
    {
        return $this->err_code;
    }

    private function setMessage($msg = '')
    {
        $this->err_msg = $msg;
    }

    public function getMessage()
    {
        return $this->err_msg;
    }

    private function setError($code, $msg)
    {
        $this->setCode($code);
        $this->setMessage($msg);
    }


    private function resetError()
    {
        $this->setCode();
        $this->setMessage();
    }
}
