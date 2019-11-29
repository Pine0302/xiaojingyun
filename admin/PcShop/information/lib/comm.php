<?php
require_once $_SERVER["DOCUMENT_ROOT"].'/mp/config.php';
define("PC_DB_HOST",DB_HOST);
define("PC_DB_USER",DB_USER);
define("PC_DB_PWD",DB_PWD);
define("PC_DB_NAME",DB_NAME);
//error_reporting(0);
function print_w($data,$l=0){
	for($i=0;$i<3;$i++){
		if($i==0){echo '<pre>';}
		if($i==1){print_r($data);}
		if($i==2){echo '</pre>';}
	} if($l){echo '<hr/>';}
}
function encode_wsy($txt) {
	srand((double)microtime() * 1000000);
	$encrypt_key = md5(rand(0, 32000));
	$ctr = 0;
	$tmp = '';
	for($i = 0;$i < strlen($txt); $i++) {
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
	}
	$str =  base64_encode(passport_key_yr($tmp, 'wsy20'));
	$pos = strpos($str,"+");
	$pos2 = strpos($str,"/");
	if($pos>0 or $pos2>0){
		return encode_wsy($txt);
	}
	return $str;
}
function decode_wsy($txt) {
	if(is_numeric($txt)){ //先加这测试一下 , 如果传入解密的文本是数字，表示不用再进行解密 。
		return $txt;
	}
	$txt = passport_key_yr(base64_decode($txt), 'wsy20');
	$tmp = '';
	for($i = 0;$i < strlen($txt); $i++) {
		$md5 = $txt[$i];
		$tmp .= $txt[++$i] ^ $md5;
	}
	return $tmp;
}
function passport_key_yr($txt, $encrypt_key) {
	$encrypt_key = md5($encrypt_key);
	$ctr = 0;
	$tmp = '';
	for($i = 0; $i < strlen($txt); $i++) {
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
	}
	return $tmp;
}
class Cc{
	static public function lt($list,$pid=0,$space=0,&$a=[],$catename='catename'){
		$space +=4;
		foreach($list as $v){
			if($v['parent_id']==$pid){
				if($v['parent_id']==0){$space = 0;}$v[$catename] = str_repeat('&nbsp;',$space).'|--'.$v[$catename];
				$a[] = $v;self::lt($list,$v['id'],$space,$a);
			}
		} return $a;
	}
	static public function shl($cate,$pid='parent_id',$catename='catename',$level_id=null,$strCate=''){
		$strCate .= "<select name=$pid id='settltype'><option value='0'>顶级分类</option>";
		foreach($cate as $v){
			if($v['id']==$level_id){$strCate .= "<option value='{$v['id']}' selected>".$v[$catename]."</option>";}
			else{$strCate .= "<option value='{$v['id']}'>".$v[$catename]."</option>";}
		} return $strCate.="</select>";
	}
	static public function sh2($cate,$pid='parent_id',$catename='catename',$level_id=null,$strCate=''){
		$strCate .= "<select name=$pid id='parent_id'><option value='0'>--请选择--</option>";
		foreach($cate as $v){
			if($v['id']==$level_id){$strCate .= "<option value='{$v['id']}' selected>".$v[$catename]."</option>";}
			else{$strCate .= "<option value='{$v['id']}'>".$v[$catename]."</option>";}
		} return $strCate.="</select>";
	}
	static public function pare($id,&$a=[],$catename='catename',$table='pc_catetype'){
		$sql = "select id,parent_id,$catename from $table where id=$id";
		$arr = mysql_fetch_assoc(mysql_query($sql));
		if(!empty($arr)){
			$a[] = $arr;
			self::pare($arr['parent_id'],$a);
		} return $a;
	}
	static public function tck($list,$pid,&$a=array()){
		foreach($list as $v){if($v['parent_id']==$pid){
		$a[] = $v;}}return $a;///
	}
	static public function tch($list,$pid,&$a=array()){
		foreach($list as $v){if($v['cate_id']==$pid){$a[] = $v;}}
		return $a;
	}
	
}


