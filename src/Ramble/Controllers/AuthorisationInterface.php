<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 04/07/2017
 * Time: 22:22
 */

namespace Ramble\Controllers;


interface AuthorisationInterface {
    public function checkAuthentication($user, $pass) : bool;
}