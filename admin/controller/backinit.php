    <?php

class control_backinit{
    /*
	* 登陆验证
	* $Author: djy $
	* 2017-09-7  $
    */
    function back_init()
    {   
		//var_dump($_SESSION);
        if(empty($_SESSION["C_id"])){
           json_out(array('errcode' => 600,'errmsg'=>'登录已经超时，请重新登录！'));
        };

        $c_id = $_SESSION["C_id"];
        
        $customer_id_en = passport_encrypt((string)$c_id);
        json_out(array('errcode' => 0,'customer_id'=>"'".$customer_id_en."'"));
        
    }   

}

