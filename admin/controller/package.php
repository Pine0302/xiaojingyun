<?php
/*
* 套餐相关
* $Author: 痴心绝对 $
* 2015-01-11  $
*/

class control_package extends control_base {
	var $model;

	function __construct() {
        //echo '-----------';
		require_once('model/package.php');
		$this->model = new model_package();
    }

	function buy(){
		$this->model->buy();
		if($_REQUEST['from'] == '1'){
			$url = "?m=package&a=pay&from=1";
		}else{
			$url = "?m=package&a=pay";
		}
		header('location:'.$url);exit;
	}

	function index(){
		//echo 123;
		//die();
		$aa = 'test';
	    $result = $this->model->index();
		include('view/index.htm');

	}








}

