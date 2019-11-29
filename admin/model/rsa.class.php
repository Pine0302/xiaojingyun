<?php

header('content-type:text/html;charset=utf-8');

class rsa_method {

    const RSA_DEF_BYTE = 1024;//  512 1024 2048 4096 等

    public function __construct(){
        // require_once('../../base.php');
        
    }


    /**
     * 生成一对公私钥
     * 返回公钥和加密后的私钥
     * @return     array     [密钥对]
     * Author Ning
     */
    public function rsa_create(){

        $config = array(
            "private_key_bits" => self::RSA_DEF_BYTE,      //  512 1024 2048 4096 等
            "private_key_type" => OPENSSL_KEYTYPE_RSA,     //加密类型
            // "config" => "D:/myphp_www/PHPTutorial/nginx/conf/openssl.cnf"
            // "config" => "F:/www/wsy_blockchain/public/lib/rsa/openssl.cnf"
        );

        //----- 生成一对公私钥 -----//
        $res = openssl_pkey_new($config);//生成公钥私钥资源

        if($res == false) {
            return array('code' => 30000, 'msg' => '生成密钥对失败');
        }
        openssl_pkey_export($res, $data['priKey'], null, $config);//私钥 $priKey
        $pubKey = openssl_pkey_get_details($res);
        $data['pubKey'] = $pubKey["key"];//公钥 $pubKey
        //----- 生成一对公私钥 end -----//

        //----- 加密新生成的私钥 -----//
        $data['priKey'] = $data['priKey'];
        //----- 加密新生成的私钥 end -----//

        if ($data['pubKey'] && $data['priKey']) {
            return array('code' => 20000, 'msg' => '成功', 'data' => $data);
            
        }else{
            return array('code' => 30001, 'msg' => '生成密钥对失败');
        }

    }


    /**
     * 加密
     * @param     string     $data['data']          [明文]
     * @param     string     $data['key']           [密钥]
     * @param     string     $data['type']          [1、公钥加密 2、私钥加密]
     * @return    array      $res['encrypt_data']   [加密后的数据]
     * Author Ning
     */
    public function rsa_encrypt($data){

        if (empty($data['data'])) {
            return array('code' => 30002, 'msg' => '未传入明文');
        }

        if (empty($data['key'])) {
            return array('code' => 30004, 'msg' => '未传入密钥');
        }

        if (empty($data['type'])) {
            return array('code' => 30005, 'msg' => '未传入加密类型');
        }

        //----- 加密 -----//
        $encrypted = $this->encrypt_key($data['data'], $data['key'], $data['type']); //2为公钥加密

        return $encrypted;

    }


    /**
     * 解密
     * @param     string     $data['data']       [密文]
     * @param     string     $data['key']        [密钥]
     * @param     string     $data['type']       [1、私钥解密 2、公钥解密]
     * @return    array      $res['data']        [解密后的数据]
     * Author Ning
     */
    public function rsa_decrypt($data){

        if (empty($data['data'])) {
            return array('code' => 30003, 'msg' => '未传入密文');
        }

        if (empty($data['key'])) {
            return array('code' => 30004, 'msg' => '未传入密钥');
        }

        if (empty($data['type'])) {
            return array('code' => 30005, 'msg' => '未传入加密类型');
        }
        
        //----- 解密密文 -----//
        $decrypted = $this->decrypt_key($data['data'], $data['key'], $data['type']); //2为私钥解密
        
        return $decrypted;

    }


    /**
     * 非对称加密
     * @param     string     $data    [明文]
     * @param     string     $Key     [传入密钥]
     * @param     string     $type    [1、公钥加密 其他、私钥加密]
     * @return    array      [加密后的数据,并且base64转码]
     * Author Ning
     */
    private function encrypt_key($data,$Key,$type){

        $check_key = $type == '1'?'openssl_pkey_get_public':'openssl_pkey_get_private';//验证钥匙的方法
        $encrypt_key = $type == '1'?'openssl_public_encrypt':'openssl_private_encrypt';//加密的方法

        if (!$check_key($Key)) {
            return array('code' => 30006, 'msg' => '密钥不可用');
        }

        $encryptData = '';//加密后的数据

        //非对称加密明文最大长度为（密钥长度／8-11）例：1028/8-11 = 117 超出会报错，需分段加密
        foreach (str_split($data, (self::RSA_DEF_BYTE/8-11)) as $value) {
            $encrypt_key($value, $eData, $Key);
            $encryptData .= $eData;
        }

        if ($encryptData == '') {
            return array('code' => 30007, 'msg' => '加密失败');
        }

        return array('code' => 20000, 'msg' => '成功', 'data' => base64_encode($encryptData));
    }


    /**
     * 非对称解密
     * @param     string     $data    [传入需解密的数据]
     * @param     string     $Key     [传入密钥]
     * @param     string     $type    [1、私钥解密 其他、公钥解密]
     * @return    array      [解密后的数据]
     * Author Ning
     */
    private function decrypt_key($data,$Key,$type){

        $check_key = $type == '1'?'openssl_pkey_get_private':'openssl_pkey_get_public';//验证钥匙的方法
        $decrypt_key = $type == '1'?'openssl_private_decrypt':'openssl_public_decrypt';//解密的方法

        if (!$check_key($Key)) {
            return array('code' => 30006, 'msg' => '密钥不可用');
        }

        $decryptData = '';//解密后的数据

        //非对称加密后长度为（密钥长度／8）例：1028/8 = 128
        foreach (str_split(base64_decode($data), (self::RSA_DEF_BYTE/8)) as $value) {
            $decrypt_key($value, $dData, $Key);
            $decryptData .= $dData;
        }

        if ($decryptData == '') {
            return array('code' => 30008, 'msg' => '密钥不匹配');
        }

        return array('code' => 20000, 'msg' => '成功', 'data' => $decryptData);
    }
}

 ?>