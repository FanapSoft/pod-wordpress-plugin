<?php
/**
 * Created by PhpStorm.
 * User: keshtgar
 * Date: 6/1/19
 * Time: 11:50 AM
 */
require_once("UserOperationBaseVo.php");

class UserProfileVo extends UserOperationBaseVo
{
    public function objectToArray()
    {
        return array_filter(get_object_vars($this), 'strlen');
    }

}