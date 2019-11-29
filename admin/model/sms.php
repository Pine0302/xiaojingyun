<?php

/**
 * 短信发送
 * developer:xurui
 * date:2015/07/29
 */
class SmsModel extends Model {

    var $tableName = "cld_send_log";
    var $http;

    /* 初始父类的构造函数 */
    public function __construct() {
        parent::__construct();
        import('http');
        $this->http = new http ();
    }

    /* 发送手机短信 */
    function send($mobile, $content) {
        $uid = UID > 0 ? UID : $_SESSION ['user_id'];
        if ($uid > 0) {
            $result = $this->check_user_nums();
            //1、限制同一个帐号1天只能发送5条
            //2、限制同一个IP,1天只能发送20条
            if ($result['user_counts'] > 5 || $result['ip_counts'] > 20) {
                return false;
            }
        }
        $sendurl = "http://sms.xxx.com:81/SDK/Sms_Send.asp?";
        $params = array('CorpID' => '251444', 'LoginName' => 'store', 'passwd' => '5454545', 'LongSms' => '1', 'send_no' => $mobile, 'msg' => iconv('UTF-8', 'GBK', $content));
        $query = http_build_query($params);
        $sendurl .= str_replace('&amp;', '&', $query);

        $ip = GetIP();
        $back_result = $this->http->send($sendurl);
        //		$back_result = file_get_contents ( $sendurl );
        $data = array('type' => 1, 'to' => $mobile, 'content' => addslashes_deep($content), 'status' => $back_result > 0 ? 1 : 2, 'back_status' => $back_result, 'addtime' => NOW_TIME, 'uid' => $uid>0?$uid:$_SESSION['admin_id'], 'uname' => $_SESSION['user_name']?$_SESSION['user_name']:$_SESSION['admin_name'], 'ip_address' => $ip);
        $this->add($data);
        if ($back_result > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断发送短信数量
     * @return type
     */
    function check_user_nums() {
        $uid = UID > 0 ? UID : $_SESSION ['user_id'];
        $ip = GetIP();
        $today_start = date('Y-m-d H:i:s', strtotime('00:00:00'));
        $today_end = date('Y-m-d H:i:s', TIME_STAMP);
        //同一个账号一天发送短信的次数
        $sql = "SELECT COUNT(*) as counts FROM " . $this->tableName . " WHERE uid={$uid}  and status=1 and addtime >= '{$today_start}' AND addtime <= '{$today_end}'";
        $result = $this->query($sql);
        //同一个IP一天发送短信的次数
        $sql = "SELECT COUNT(*) as counts FROM " . $this->tableName . " WHERE ip_address='{$ip}' and status=1 and addtime >= '{$today_start}' AND addtime <= '{$today_end}'";
        $ip_counts = $this->query($sql);
        $arr = array();
        $arr['user_counts'] = (int)$result['counts'];
        $arr['ip_counts'] = (int)$ip_counts['counts'];
        return $arr;
    }

    /* 告急时,给管理员发送短信、邮件 */
    function send_admin_message($title = '', $content = '') {
        if (ADMIN_MOBILE) {
            //发短信
            $mobile_arr = array_unique(json_decode(ADMIN_MOBILE, true));
            foreach ($mobile_arr as $key => $value) {
                if (is_mobile($value)) {//短信
                    try {
                        //限制告急信息发送次数
                        $sql_sms_check = "SELECT COUNT(sms_id) FROM `cld_send_log` WHERE  `type` = 1 AND `to` ='" . $value . "' AND `content` = '" . $content . "' AND `status` = 1 AND `addtime` > '" . date('Y-m-d 00:00:00') . "' AND `addtime` < '" . date('Y-m-d 23:59:59') . "'";
                        $sms_num = $this->one($sql_sms_check);
                        if ($sms_num <= 3)
                            $this->send($value, $content); //一天最多多发三次
                    } catch (Exception $e) {
                        if (DEBUG_MODE)
                            echo 'Message: ' . $e->getMessage();
                    }
                }
            }
        }
        //发邮件
        if (ADMIN_EMAIL) {
            $email_arr = array_unique(json_decode(ADMIN_EMAIL, true));
            foreach ($email_arr as $k => $v) {
                if (is_email($v)) {
                    try {
                        //限制告急信息发送次数
                        $sql_email_check = "SELECT COUNT(sms_id) FROM `cld_send_log` WHERE  `type` = 2 AND `to` ='" . $v . "' AND `title` = '" . $title . "' AND `content` = '" . $content . "' AND  `status` = 1 AND `addtime` > '" . date('Y-m-d 00:00:00') . "' AND `addtime` < '" . date('Y-m-d 23:59:59') . "'";
                        $email_num = $this->one($sql_email_check);
                        if ($email_num <= 3) { //一天最多多发三次
                            $email = array(
                                'address' => $v,
                                'subject' => $title,
                                'body' => $content
                            );
                            M("Admin://Email")->Send($email); //发邮件
                        }
                    } catch (Exception $e) {
                        if (DEBUG_MODE)
                            echo 'Message: ' . $e->getMessage();
                    }
                }
            }
        }
    }

}
