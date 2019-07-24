<?php
/**
 * Created by PhpStorm.
 * User: keshtgar
 * Date: 5/28/19
 * Time: 12:47 PM
 */

class BaseInfo
{
    public  $serverType = "Sandbox";
    public  $_token_;
    public  $_token_issuer_;




    public function setServerType($serverType) {
        $this->serverType = $serverType;
    }

    public function setApiToken($apiToken) {
        $this->_token_ = $apiToken;
    }

    public function setTokenIssuer($tokenIssuer) {
        $this->_token_issuer_ = $tokenIssuer;
    }


    public function getServerType() {
        if ($this->serverType) {
            return $this->serverType;
        }
        else {
            throw new Exception("Server Type is not set! Please set it and try again.", BaseService::VALIDATION_ERROR_CODE);
        }
    }

    public function getApiToken() {
        if ($this->_token_) {
            return $this->_token_;
        }
        else {
            throw new Exception("_token_ Id is not set! Please set it and try again.", BaseService::VALIDATION_ERROR_CODE);
        }
    }

    public function getTokenIssuer() {
        if ($this->_token_issuer_) {
            return $this->_token_issuer_;
        }
        else {
            throw new Exception("_token_issuer_ is not set! Please set it and try again.", BaseService::VALIDATION_ERROR_CODE);
        }
    }


}