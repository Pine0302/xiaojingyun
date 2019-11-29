<?php
header("Content-type: text/html; charset=utf-8");

class control_block_chain_api
{
    var $model;

    function __construct()
    {   
        require_once($_SERVER['DOCUMENT_ROOT'].'/mshop/common/mshop_function.php');
        require_once($_SERVER['DOCUMENT_ROOT'].'/mshop/admin/model/block_chain_api.php');
        $this->model = new model_block_chain_api();
        
    }

        /*
    * 区块链APP登陆注册接口
    * $Author: hjw$
    * $2018-10-8  $
    * 参数：
    */
    public function app_login_api($customer_id){
        $data['customer_id']    = $customer_id; //商家ID
        $http_url = $this->model->http_url($customer_id);//获取前缀地址
        //需要参数
        $access_token = _block_chain_token($customer_id); //获取access_token
        if ($access_token['errcode'] != 0) 
        {
            $msg = '获取access_token失败';
            $url = Protocol."$_SERVER[HTTP_HOST]/weixinpl/common/error2.php?msg=".$msg;
            header("Location: $url");
            exit();
        }
        $access_token = $access_token['access_token'];
        $return_url = Protocol . $_SERVER["HTTP_HOST"] . "/mshop/admin/index.php?m=block_chain_api&a=app_login_return&customer_id=".$customer_id;
         //请求接口地址
        $url = $http_url."wsy_blockchain/api/index.php?m=openapi_user&a=authorization&access_token=".urlencode($access_token)."&return_url=".urlencode($return_url).'&version=1.2';
        header("location:".$url);

    }
    /*
    * 区块链APP登陆注册接口
    * $Author: hjw$
    * $2018-10-8  $
    * 参数：
    */
    public function app_login_return(){
        $code = addslashes($_GET['code']);
        $customer_id = addslashes($_GET['customer_id']);

        if(isset($_GET['code']) && $code){

           /* if($_SESSION["user_id_".$customer_id]){
                $_SESSION["user_id_".$customer_id] = '';
                $this->app_login_api($customer_id);
                return;
            }*/
            //需要参数
            $access_token = _block_chain_token($customer_id); //获取access_token
            $access_token = $access_token['access_token'];
            $http_url = $this->model->http_url($customer_id);//获取前缀地址
            $url = $http_url."/wsy_blockchain/api/index.php?m=openapi_user&a=getOpenid";
            //请求参数
            $type = array(
                'access_token'=> $access_token,
                'code'     => $code
            );
            $type = json_encode($type);
            //请求接口
            $res = get_curl($url,$type);
            //注册失败
            if ($res['data']['errcode'] != 0 || $res['data'] == NULL) 
            {
                $msg = '登陆失败,请重新进入';
                $url = Protocol."$_SERVER[HTTP_HOST]/weixinpl/common/error2.php?msg=".$msg;
                header("Location: $url");
                exit();
            }
            $data['customer_id'] = $customer_id; //商家ID
            $data['openid']      = addslashes($res['data']['data']['openid']);
            $data['mobile']      = addslashes($res['data']['data']['mobile']);
            $data['nickname']    = addslashes($res['data']['data']['nickname']);
            $data['head_img']    = $res['data']['data']['head_img'] == -1 ? '' : addslashes($res['data']['data']['head_img']);
            //$res_bind = $this->model->is_bind_shop($data);//是否绑定商城

            $user_id = $this->model->create_account($data);//是否绑定商城
            //app消息互通：登陆成功吧需要退出登陆的用户进行软删除
            $this->model->H5_loginout($user_id);
            $data['user_id']      = $user_id;
            $res_user = $this->model->get_user_msg($data);
            $weixin_fromuser = $res_user['weixin_fromuser'];
            $weixin_headimgurl = $res_user['weixin_headimgurl'];
            $_SESSION["customer_id"] = $customer_id;   
            $_SESSION["user_id_".$customer_id]      =$user_id;
            $_SESSION["myfromuser_".$customer_id]   =$weixin_fromuser;
            $_SESSION["fromuser_".$customer_id]     =$weixin_fromuser;
            $_SESSION["is_bind_".$customer_id]      =1;//已经注册
            setcookie("login_headimgurl",$weixin_headimgurl, time()+604800,'/');//设置用户头像COOKIE
            $url = $_SESSION["nurl_".$customer_id];
            header("location:".$url);

        }

    }
}