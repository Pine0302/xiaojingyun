<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/23
 * Time: 16:20
 */
class model_integral
{
    var $db;

    function __construct()
    {
        $this->db = DB::getInstance();
    }

}