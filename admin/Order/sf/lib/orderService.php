<?php

class orderService {
	
	public function base64($str){ // base64转码
	
		return base64_encode($str);
		
	}
	
	public function _md5($str){ // md5加密并转大写
	
		return strtoupper(md5($str));
		
	}
	
	/**
	 * 获取订单数据
	 * @param $xml XML字符串
	 * @param $checkword 密钥
	 */
	 
	public function getOrderData($xml,$arr){
		$md5Data = $this->_md5($xml.$arr['verifyCode']); 
		
		$verifyCode = $this->base64($md5Data);
		
		return $this->callWebServer($xml,$arr,$verifyCode); // 调用webserver
	}
	
	/**
	 * 调用webserver
	 * @param $xml XML字符串
	 * @param $verifyCode 加密后的字符串
	 * 返回xml格式
	 */
	 
	public function callWebServer($xml,$arr,$verifyCode){
	
		$client = new SoapClient($arr['server'],array('trace' => true, 'exceptions' => true ));

		$authToken = $arr['authtoken'];
		
		$headers = new SoapHeader($arr['headerNamespace'],"authtoken",$authToken, false, SOAP_ACTOR_NEXT);
		
		$client->__setSoapHeaders(array($headers));
				
		$result = $client->__soapCall($arr['Servicefun'], array("data" => $xml, "verifyCode"=> $verifyCode));
		
		//var_dump($client->__getLastRequest());
		$rr=$result;
		return $rr; //返回xml格式
	}
}


/**
 * 数组转为xML
 * @param $var 数组
 * @param $type xml的根节点
 * @param $tag
 * 返回xml格式
 */

function array2xml($var, $type = 'root', $tag = '') {
   $ret = '';
   if (!is_int($type)) {
       if ($tag)
           return array2xml(array($tag => $var), 0, $type); else {
           $tag .= $type;
           $type = 0;
       }
   }
   $level = $type;
   $indent = str_repeat("\t", $level);
   if (!is_array($var)) {
       $ret .= $indent . '<' . $tag;
       $var = strval($var);
       if ($var == '') {
           $ret .= ' />';
       } else{
		    $ret .= '>' . $var . '</' . $tag . '>';
		   
	   }
	   
	   /*else if (!preg_match('/[^0-9a-zA-Z@\._:\/-]/', $var)) {
           $ret .= '>' . $var . '</' . $tag . '>';
       } else {
           $ret .= "><![CDATA[{$var}]]></{$tag}>";
       }
	   */
       $ret .= "\n";
   } else if (!(is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) && !empty($var)) {  
       foreach ($var as $tmp)
           $ret .= array2xml($tmp, $level, $tag);
   } else {
       $ret .= $indent . '<' . $tag;
       if ($level == 0)
           $ret .= '';
       if (isset($var['@attributes'])) {
           foreach ($var['@attributes'] as $k => $v) {
               if (!is_array($v)) {
                   $ret .= sprintf(' %s="%s"', $k, $v);
               }
           }
           unset($var['@attributes']);
       }
       $ret .= ">\n";
       foreach ($var as $key => $val) {
           $ret .= array2xml($val, $level + 1, $key);
       }
       $ret .= "{$indent}</{$tag}>\n";
   }
   return $ret;
 }

?>

