<?php 
/**
 * @author 尹键锋-Key
 * @date   2017-01-03
 */
class DB  
{
    private $db = '';
    private $link = '';
    private $table = '';
    private $isCommit = true;

    public function linkDB($dbHost,$dbUser,$dbPwd,$dbName) 
    {   
        if(!empty($dbHost) || !empty($dbUser) || !empty($dbPwd)){
            $this->db = mysql_connect($dbHost,$dbUser,$dbPwd) or die(mysql_error().'：mysql_connect');
            if($dbName){
                $result = mysql_select_db($dbName) or die(mysql_error().'：mysql_select_db');
            }
        }
        return $result;
    }

    /**
     * 开启事务
     * 2016-10-24
     * @param  array  $sql [执行的sql语句组]
     * @return [type]      [description]
     */
    public function transaction($sql=array()){
        $res = array();
        $res['tra'] = _mysql_query('START TRANSACTION',$this->db);
        $this->isCommit = true;
        if($sql){
            if(!empty($sql)){
                foreach ($sql as $key => $value) {
                    $res[] = _mysql_query($value,$this->db) or die(mysql_error().$value);
                }
            }
            if(in_array(0,$res)){
                $res = _mysql_query('ROLLBACK',$this->db) or die(mysql_error().'：ROLLBACK');
            }
            $res = _mysql_query('COMMIT',$this->db) or die(mysql_error().'：COMMIT');
        }
        return $res;
    }

    /**
     * 提交事务
     * 2016-10-21
     * @return [type] [description]
     */
    public function commit(){
        if(!$this->isCommit){
            $res = _mysql_query('ROLLBACK',$this->db) or die(mysql_error().'：ROLLBACK');
            $res = false;
        }else{
            $res = _mysql_query('COMMIT',$this->db) or die(mysql_error().'：COMMIT');
        }
        return $res;
    }

    /**
     * SQL语句执行
     * 2016-09-10
     * @param  [type] $sql  [description]
     * @param  [type] $type [0-二维数组,1-一维数组,2-字符串]
     * @return [type]       [description]
     */
    public function query($sql,$type){
        $result = _mysql_query($sql,$this->db) or die(mysql_error().$sql);
        $this->isCommit = $this->isCommit==true?$result:0;
        if($result){
            $result = mysql_affected_rows($this->db);
            if(!$result){
                $result = true;
            }
        }
        return $result;
    }

    /**
     * [执行sql获取数据-二维数组]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function getData($sql){
        $res = array();
        $result = _mysql_query($sql,$this->db) or die(mysql_error().$sql);
        $this->isCommit = $this->isCommit==true?$result:0;

        if($result){
            while($row = mysql_fetch_assoc($result)){
                $res[]=$row;
            }
        }
        return $res;
    }

    /**
     * [执行sql获取数据-一维数组(单字段多条数据)]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function getArray($sql){
        $res = array();
        $result = _mysql_query($sql,$this->db) or die(mysql_error().$sql);
        $this->isCommit = $this->isCommit==true?$result:0;

        if($result){
            while ( $row = mysql_fetch_row($result) ) {
                $res[] = $row[0];
            }
        }
        return $res;
    }

    /**
     * [执行sql获取数据-一维数组]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function getFields($sql){
        $res = array();
        $result = _mysql_query($sql,$this->db) or die(mysql_error().$sql);
        $this->isCommit = $this->isCommit==true?$result:0;

        if($result){
            $res = mysql_fetch_assoc($result);
        }
        return $res;
    }

    /**
     * [执行sql获取数据-字符串]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function getField($sql){
        $result = _mysql_query($sql,$this->db) or die(mysql_error().$sql);
        $this->isCommit = $this->isCommit==true?$result:0;
        if($result){
            $arr = mysql_fetch_row($result);
            $res = $arr[0];
        }
        return $res;
    }


    /**
     * 初始化变量
     * @param  [type] $data     [数据]
     * @param  [type] $initData [默认值]
     * @param  [type] $trim     [是否去空格]
     * @return [type]           [description]
     */
    public function init($data,$initData='',$trim=false){
        $value = $data==NULL?$initData:$data;
        $value = $trim==true?trim($value):$value;
        return $value;
    }

    /**
     * [状态替换函数]
     * @param  [type] $type        [状态值]
     * @param  array  $swichOption [替换选项]
     * @param  [type] $default     [默认值]
     * @return [type]              [description]
     */
    public function switchReplace($type,$swichOption=array(),$default){
        $result = $default?$default:$swichOption[0];
        $num = count($swichOption);
        for ($i=0; $i < $num; $i++) { 
            if($type==$i){
                $result = $swichOption[$i];
            }
        }
        return $result;
    }
}
 ?>