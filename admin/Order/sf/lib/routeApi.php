<?php
class routeApi {
    
    /**
     * 调用webserver
     * 返回xml格式
     */
    public function getRoute($arr){
		
        $client = new SoapClient($arr['server']);
		
        $authToken = (object) array("authToken"=>$arr['authToken']);
		
        $headers = new SoapHeader($arr['headerNamespace'],"AuthHeader",$authToken);
        $client->__setSoapHeaders($headers);
		try {
			
            $res = $client->__soapCall( 'getRouteInfo', array('customerCode' => $arr['customerCode'], 'mailorderNo' => $arr['mailorderNo']));
			
		
			
             $CryptDes  = new CryptDes($arr['secretKey'],$arr['mix']); //（秘钥向量，混淆向量）
			 
            $res = $CryptDes->decrypt(str_replace(array("\r","\n"," "), "", $res));
			
            return $res;
			
        } catch (SoapFault $e) {
			
            echo "Error: {$e}";
			
        }
    }
}
?>

