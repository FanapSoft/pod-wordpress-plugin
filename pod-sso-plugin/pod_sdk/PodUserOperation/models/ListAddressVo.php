<?php
/**
 * Created by PhpStorm.
 * User: keshtgar
 * Date: 6/1/19
 * Time: 4:54 PM
 */

class ListAddressVo
{
    protected $offset;
    protected $size;

    public function setOffset($offset) {
        $this->offset = $offset;
    }

    public function setSize($size) {
        $this->size = $size;
    }

    public function objectToArray()
    {
        return array_filter(get_object_vars($this), 'strlen');
    }

}