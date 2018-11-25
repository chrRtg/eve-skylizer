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

    /**
     * Session manager.
     *
     * @var Zend\Session\Container;
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

        // make a call
        //$res = $esi->invoke($method, $request, $params);
        $try_cnt = 0;
        $max_tries = 5;

        do {
            try {
                $res = $esi->invoke($method, $request, $params);
            } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
                // ref: https://github.com/eveseat/eseye/wiki/Handling-Exceptions

                $esirepsonse = $e->getEsiResponse();
                $err_code = $e->getCode();

                $this->logger->debug('publicRequest :: RequestFailedException (' .$err_code. '): ' . print_r($esirepsonse, true));

                echo PHP_EOL . '#E(' . $err_code . ')[' . $esirepsonse->error_limit . '][' . $request . ']';

                switch ((int) $err_code) {
                case 502:
                    break;
                default:
                    // @todo maybe implement a better error handling (message - re-login)
                    // rethrow exception - to be handled by the global exception handling
                    throw $e;
                  break; 
                }

                $try_cnt++;
                sleep(1);
                continue;
            }
            // if not caught return the result
            return($res);
            
        } while ($try_cnt < $max_tries);

        // If we reach this point we found a exception to be handled by the global exception handling
        throw $e;
        return (false);
    }

    /**
     * Send an authentificated request to Eve ESI
     * 
     * @param  string Method of the request (e.g. get, post )
     * @param  string the request (e.g. /characters/{character_id} )
     * @param  array variables from the request and their values (e.g. ['character_id' => 90585056] )
     * @return type
     */
    public function authedRequest($method, $request, $params)
    {
        // does the application is authed against Eve SSO?
        if (empty($this->sessionContainer->eveauth['eve_app']['client_id']) || empty($this->sessionContainer->eveauth['eve_user']['token'])) {
            throw new \Exception('authedRequest to Eve ESI without having client_id and user-token');
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

        // Instantiate a new Eseye instance.
        $esi = new Eseye($authentication);

        // make a call
        try {
            $res = $esi->invoke($method, $request, $params);
        } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
            // ref: https://github.com/eveseat/eseye/wiki/Handling-Exceptions

            $esirepsonse = $e->getEsiResponse();
            $err_code = $e->getCode();

            // @todo maybe implement a better error handling (message - re-login)
            $this->logger->debug('authedRequest :: RequestFailedException (' .$err_code. '): ' . print_r($esirepsonse, true));

            throw $e;
        }

        return($res);
    }

}
