<?php
require_once 'comm.php';
class Ycc{
	private $Config = [
    'db_host' => PC_DB_HOST, 
    'db_user' => PC_DB_USER,      
    'db_pwd'  => PC_DB_PWD,       
	'resLink' => null,
	'db_name' => PC_DB_NAME];
	public function connect($db_name=null){
	if(!empty($db_name)){$this->db_name = $db_name;}
	if(empty($this->resLink)){$this->resLink = @mysql_connect($this->db_host,$this->db_user,$this->db_pwd) or die('connect failed:'.mysql_error());}
	mysql_select_db($this->db_name,$this->resLink) or die('db select failed:'.mysql_error());
	mysql_query('set names utf8',$this->resLink) or die('character set error:'.mysql_error());
	return self::$instance;}
	private function __clone(){trigger_error('OMG,U broke the rules',E_USER_ERROR);}
	public function get_one($sql){
	if(empty($sql)){
	exit('sql 不能为空');}
	else{$sql = trim($sql);
	if(strpos($sql,'select')===false){
	return false;}else{
	$result = @mysql_query($sql,$this->resLink);	
	if(is_resource($result)){
	return mysql_fetch_assoc($result);}
	else{return false;}}}}
	static public function getInstance(){
	if(!(self::$instance instanceof self)){self::$instance = new self();}
	return self::$instance;}
	public function query($sql){
	if(empty($sql)){exit('sql 不能为空');}else{
	$sql = trim($sql);
	$result = @mysql_query($sql,$this->resLink);
	if(is_resource($result)){ 
	return self::$instance->fetchAll($result);}
	else{return $result;}}}
	public function __get($name){return $this->Config[$name];}
	public function fetchAll($res,$array=array()){
	while($arr=mysql_fetch_assoc($res)){
	$array[] = $arr;}
	return $array;}
	private function __construct(){}
	public function __destruct(){
	unset($res);$this->resLink = null;}
	static private $instance;
	public function __call($name,$array){
	exit($name.': no this function');}///
}



