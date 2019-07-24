<?php
/**
 * Created by PhpStorm.
 * User: keshtgar
 * Date: 6/1/19
 * Time: 11:51 AM
 */

class UserOperationBaseVo
{
    protected $client_id;
    protected $client_secret;

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function setClientSecret($client_secret) {
        $this->client_secret = $client_secret;
    }

}