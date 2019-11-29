<?php
/**
 * User: chy
 * Date: 2018/5/10
 * Time: 14:34
 * Explain: 基类
 */

class HyBase extends control_base
{
    public function __construct()
    {
        parent::__construct();


        //$data['data']=file_get_contents('php://input', true);
        //$data = $_REQUEST['data'];
        //$this->data  = json_decode($data['data'],true);

        if($_REQUEST['data']){
            $this->data  = json_decode($_REQUEST['data'],true);
        }else{
            $this->data  = $_REQUEST;
        }
    }

}