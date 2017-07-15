<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 04/07/2017
 * Time: 22:22
 */

namespace Ramble\Controllers;


use Monolog\Logger;

class SimpleAuthorisation implements AuthorisationInterface {
    private $user = null;
    private $password = null;
    /**
     * @var Logger
     */
    private $logger = null;

    public function __construct(string $user, string $password, Logger $logger) {
        $this->user = $user;
        $this->password = $password;
        $this->logger = $logger;
    }

    public function checkAuthentication($user, $pass): bool {
        $verified = (strcmp($user, $this->user) == 0) && password_verify($pass, $this->password);
        if($this->logger != null) {
            if (!$verified) {
                $this->logger->warn('[XMLRPC] Bad login details used.', ['Username' => $user, 'Password' => preg_replace('/./', '*', $pass)]);
            } else {
                $this->logger->debug('[XMLRPC] Successful login', ['Username' => $user]);
            }
        }
        return $verified;
    }
}