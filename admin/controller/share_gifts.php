<?php
/*
版权信息:  秘密信息
功能描述：分享有礼
开 发 者：甄子祥
开发日期： 2018-01-16
重要说明：无
 */
class control_share_gifts extends control_base
{
    var $model;

    function __construct()
    {
        parent::__construct();
        //登录校验
        parent::check_login();      
        //登录校验 End
        
        require_once('model/common.php');
        $this->model_common = new model_common();
        $data['data']=file_get_contents('php://input', true);
        //$data = $_REQUEST['data'];
        $this->parmdata  = json_decode($data['data'],true);
        $customer_id = $this->customer_id;
        $this->theme = $this->model_common->find_theme($customer_id);

        require_once('model/share_gifts.php');
        $this->model_share_gifts = new share_gifts();
    }

    /**
     * 活动管理
     * 作者：山
     */
    function activity()
    {
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $theme       = $this->theme;
        
        $param = array(
            'customer_id' => $customer_id,
            'fields'      => 'ac.id,ac.name,ac.status,ac.createtime,ac.begin_time,ac.begin_time,ac.end_time'
        );

        $param['act_id']      = $_REQUEST['act_id']?$_REQUEST['act_id']:-1;        
        $param['name']        = $_REQUEST['name']?htmlspecialchars($_REQUEST['name']):"";
        
        $param['begin_time']  = $_REQUEST['begin_time']?$_REQUEST['begin_time']:"";
        $param['end_time']    = $_REQUEST['end_time']?$_REQUEST['end_time']:"";
        
        $param['status']      = $_REQUEST['status']?$_REQUEST['status']:-1;       
        $pageNum              = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']     = $pageNum;//当前页
   
        $param['user_id']    = $_REQUEST['user_id']?$_REQUEST['user_id']:"";

        //查找用户参与过的活动
        $param['user_activity'] = "";
        if(!empty($param['user_id'])){
            $user_param = array(
                   "user_id" => (int)$param['user_id'],
                   "customer_id"=> $customer_id
                );
            $param['user_activity'] = $this->model_share_gifts->select_user_join_activity($user_param);
        }

        $result = $this->model_share_gifts->get_share_activity($param);
        
        $act_infos = "";
        $pageCount = 0;

        if(!empty($result)){
           $act_infos = $result['act_infos']; 
           $pageCount = $result['pageCount'];
           $allCount  = $result['allCount'];
        }
        include ('view/share_gifts/activity.html');
    }
    
    /**
     * 活动操作
     * 作者：山
     */
    function activity_operation()
    {
        $customer_id     = $this->customer_id;

        $op     = $_REQUEST['op']?$_REQUEST['op']:"";
        $act_id = $_REQUEST['act_id']?$_REQUEST['act_id']:-1;

        $param = array(
                "op"          =>$op,
                "act_id"      => $act_id,
                "customer_id" => $customer_id
            );

        $result = $this->model_share_gifts->operation_act($param);

        echo json_encode($result);
    }
    
      
    /**
     * 判断活动是否重叠
     * 作者：山
     */
    function check_activity_overlap()
    {
        $customer_id     = $this->customer_id;

        $begin_time = $_REQUEST['begin_time']?$_REQUEST['begin_time']:"";
        $end_time   = $_REQUEST['end_time']?$_REQUEST['end_time']:"";

        $param = array(
                "begin_time"  =>$begin_time,
                "end_time"    => $end_time,
                "customer_id" => $customer_id
            );

        $result = $this->model_share_gifts->check_activity_is_overlap($param);

        echo json_encode($result);
    }
    /**
     * 活动添加
     * 作者：山
     */
    function activity_add()
    {
        $customer_id = $this->customer_id;
        $theme       = $this->theme;
        
        $all_coupons_id = "";
        $result_coupons = "";

        $ac_id      = $_REQUEST['act_id']?$_REQUEST['act_id']:-1;

        /*获取当前活动详细信息 start*/
        $activity_info = array();
        
        //随机红包信息
        $share_with_prize_info = array();

        if($ac_id > 0){       
            $param = array(
                'customer_id' => $customer_id,
                'fields'      => 'ac.id,ac.name,ac.status,ac.begin_time,ac.end_time,ac.share_background_img,ac.receive_background_img,ac.have_receive_background_img,ac.leaderboards_background_img,ac.is_subscription,ac.is_bind_phone,ac.is_share_instruction,ac.share_instruction,ac.is_receive_instruction,ac.receive_instruction,ac.is_leaderboards_instruction,ac.leaderboards_instruction,pr.share_is_coupon,pr.share_is_red_envelopes,pr.share_coupon_ids,pr.share_coupon_guide_url,pr.share_red_envelopes_type,pr.share_red_envelopes_time_limit,pr.share_red_envelopes_money_limit,pr.share_red_envelopes_people_limit,pr.share_red_envelopes_fixed_value,pr.share_invitations,pr.new_coupon_ids,pr.new_coupon_guide_url,pr.coupon_begin_time_int,pr.coupon_end_time_int,pr.red_envelopes_begin_time_int,pr.red_envelopes_end_time_int,pr.time_type,ac.color,ac.receive_color,ac.have_receive_color,ac.leaderboards_color'
            ); 
            $param['activity_id'] = $ac_id;

            $result = $this->model_share_gifts->get_share_activity($param);

            $activity_info = $result['act_infos'][0];

            //开启了红包奖励并且红包方式为随机红包
            $act_id = (int)$activity_info['id'];

            if((int)$activity_info['share_red_envelopes_type'] == 2 && (int)$activity_info['share_is_red_envelopes'] == 1){
               $param_random = array(
                   'activity_id' => $act_id,
                   'fields'      => 'id as bag_id,min_money,max_money,probability',
                   'customer_id' => $customer_id
                );
               $share_with_prize_info = $this->model_share_gifts->get_random_red_envelopes($param_random);
            }
        }
        /*获取当前活动详细信息 end*/

        //查询优惠券列表
        $result = $this->model_share_gifts->select_coupon_list(array("customer_id"=>$customer_id));

        //优惠券列表
        $all_coupons_id = $result['all_coupons_id'];
        $result_coupons = $result['result_coupons'];

        //查找分享人和新人的优惠券选择
        $share_coupon_ids = -1;
        $new_coupon_ids   = -1;
        
        if(!empty($activity_info)){
            $share_coupon_ids = $activity_info['share_coupon_ids'];
            $new_coupon_ids   = $activity_info['new_coupon_ids'];
        }
        if (substr($share_coupon_ids,0,1) == ',') {
           $share_coupon_ids=substr($share_coupon_ids,1);
        }
        if (substr($new_coupon_ids,0,1) == ',') {
           $new_coupon_ids=substr($new_coupon_ids,1);
        }

        //获取可以领取总的优惠券
        $all_coupons_id = $this->model_share_gifts->select_coupon_get_all(array("customer_id"=>$customer_id));

        //分享人奖励选取优惠券
        $share_coupon_array  = array();

        if($share_coupon_ids == -1){
            $is_open_share_coupon=0;
        }else{
            $is_open_share_coupon=1;
            $new_coupon_array1 = explode(',',$share_coupon_ids);

            //查询新人奖励选择的优惠券列表
            $share_coupon_array = $this->model_share_gifts->select_coupon_one($new_coupon_array1);

            $share_coupon_ids = implode(",",$share_coupon_array);
         
        }

        //新人奖励选取优惠券
        $new_coupon_array  = array();

        if($new_coupon_ids == -1){
            $is_open_new_coupon=0;
        }else{
            $is_open_new_coupon=1;
            $new_coupon_array2=explode(',',$new_coupon_ids);

            //查询新人奖励选择的优惠券列表
            $new_coupon_array = $this->model_share_gifts->select_coupon_one($new_coupon_array2);

            $new_coupon_ids = implode(",",$new_coupon_array);
         
        }

        //链接
        if(!empty($activity_info)){
            $activity_info['share_url'] = end(explode('-',$activity_info['share_coupon_guide_url']));
            $activity_info['new_url']   = end(explode('-',$activity_info['new_coupon_guide_url']));
        }
       
        include ('view/share_gifts/activity_add.html');
    }
     /**
     * 活动详情信息
     * 作者：山
     */
    function activity_detail(){
        $customer_id = $this->customer_id;
        $theme       = $this->theme;

        $ac_id      = $_REQUEST['act_id']?$_REQUEST['act_id']:-1;

         /*获取当前活动详细信息 start*/
        $activity_info = array();
        
        //随机红包信息
        $share_with_prize_info = array();

        if($ac_id > 0){       
            $param = array(
                'customer_id' => $customer_id,
                'fields'      => 'ac.id,ac.name,ac.status,ac.begin_time,ac.end_time,ac.share_background_img,ac.receive_background_img,ac.have_receive_background_img,ac.leaderboards_background_img,ac.is_subscription,ac.is_bind_phone,ac.is_share_instruction,ac.share_instruction,ac.is_receive_instruction,ac.receive_instruction,ac.is_leaderboards_instruction,ac.leaderboards_instruction,pr.share_is_coupon,pr.share_is_red_envelopes,pr.share_coupon_ids,pr.share_coupon_guide_url,pr.share_red_envelopes_type,pr.share_red_envelopes_time_limit,pr.share_red_envelopes_money_limit,pr.share_red_envelopes_people_limit,pr.share_red_envelopes_fixed_value,pr.share_invitations,pr.new_coupon_ids,pr.new_coupon_guide_url,pr.coupon_begin_time_int,pr.coupon_end_time_int,pr.red_envelopes_begin_time_int,pr.red_envelopes_end_time_int,pr.time_type'
            ); 
            $param['activity_id'] = $ac_id;

            $result = $this->model_share_gifts->get_share_activity($param);

            $activity_info = $result['act_infos'][0];
        }
        /*获取当前活动详细信息 end*/
        include ('view/share_gifts/activity_detail.html');
    }
    
    /**
     * 保存活动
     * 作者：山
     */
    function activity_save()
    {
        require_once(ROOT_DIR.'mp/lib/image.php');
        
        $customer_id    = $this->customer_id;
        $customer_id_en = $this->customer_id_en;

        $param = array(
            'customer_id' => $customer_id,
        );

        $act_id      = $_REQUEST['act_id']?$_REQUEST['act_id']:-1;

        $param['act_id']                   = $act_id;
        $param['name']                     = $_REQUEST['name']?htmlspecialchars($_REQUEST['name']):"";
        $param['begin_time']               = $_REQUEST['begin_time']?$_REQUEST['begin_time']:"";
        $param['end_time']                 = $_REQUEST['end_time']?$_REQUEST['end_time']:"";
        $param['is_subscription']          = $_REQUEST['is_subscription']?$_REQUEST['is_subscription']:0;
        $param['is_bind_phone']            = $_REQUEST['is_bind_phone']?$_REQUEST['is_bind_phone']:0;
        $param['share_is_coupon']          = $_REQUEST['share_is_coupon']?$_REQUEST['share_is_coupon']:0;
        $param['share_is_red_envelopes']   = $_REQUEST['share_is_red_envelopes']?$_REQUEST['share_is_red_envelopes']:0;
        $param['share_activity_time_type'] = $_REQUEST['share_activity_time_type']?$_REQUEST['share_activity_time_type']:1;

        $coupon_begin_time        = $_REQUEST['coupon_begin_time']?$_REQUEST['coupon_begin_time']:0;
        $coupon_end_time          = $_REQUEST['coupon_end_time']?$_REQUEST['coupon_end_time']:0;
        $coupon_begin_time_text   = $_REQUEST['coupon_begin_time_text']?$_REQUEST['coupon_begin_time_text']:0;
        $coupon_end_time_text     = $_REQUEST['coupon_end_time_text']?$_REQUEST['coupon_end_time_text']:0;

        $red_envelopes_begin_time = $_REQUEST['red_envelopes_begin_time_text']?$_REQUEST['red_envelopes_begin_time_text']:0;
        $red_envelopes_end_time   = $_REQUEST['red_envelopes_end_time_text']?$_REQUEST['red_envelopes_end_time_text']:0;

        if($coupon_begin_time == 0 || $coupon_begin_time == ""){
            if(!empty($coupon_begin_time_text)){
                $param['coupon_begin_time'] = strtotime($coupon_begin_time_text);
            }else{
                $param['coupon_begin_time'] = 0;
            }           
        }else{
            $param['coupon_begin_time'] = strtotime($coupon_begin_time);
        }
        
        if($coupon_end_time == 0 || $coupon_end_time == ""){
            if(!empty($coupon_end_time_text)){
                 $param['coupon_end_time'] = strtotime($coupon_end_time_text);
             }else{
                 $param['coupon_end_time'] = 0;
             }           
        }else{
            $param['coupon_end_time'] = strtotime($coupon_end_time);
        }

        if($red_envelopes_begin_time == 0 || $red_envelopes_begin_time == ""){
            $param['red_envelopes_begin_time'] = 0;
        }else{
            $param['red_envelopes_begin_time'] = strtotime($red_envelopes_begin_time);
        }

        if($red_envelopes_end_time == 0 || $red_envelopes_end_time == ""){
            $param['red_envelopes_end_time'] = 0;
        }else{
            $param['red_envelopes_end_time'] = strtotime($red_envelopes_end_time);
      }

        $param['share_red_envelopes_type'] = $_REQUEST['share_red_envelopes_type']?$_REQUEST['share_red_envelopes_type']:1;
        $param['share_link_coupons_save']  = $_REQUEST['share_link_coupons_save']?$_REQUEST['share_link_coupons_save']:-1;
        $param['share_coupon_guide_url']   = $_REQUEST['share_coupon_guide_url']?$_REQUEST['share_coupon_guide_url']:"";

        $param['share_red_envelopes_time_limit']   = $_REQUEST['share_red_envelopes_time_limit']?$_REQUEST['share_red_envelopes_time_limit']:-1;
        $param['share_red_envelopes_money_limit']  = $_REQUEST['share_red_envelopes_money_limit']?$_REQUEST['share_red_envelopes_money_limit']:-1;
        $param['share_red_envelopes_people_limit'] = $_REQUEST['share_red_envelopes_people_limit']?$_REQUEST['share_red_envelopes_people_limit']:-1;
        $param['share_red_envelopes_fixed_value']  = $_REQUEST['share_red_envelopes_fixed_value']?$_REQUEST['share_red_envelopes_fixed_value']:-1;
        
        //随机红包
        $param['bag_id']       = $_REQUEST['bag_id']?$_REQUEST['bag_id']:"";
        $param['min_money']    = $_REQUEST['min_money']?$_REQUEST['min_money']:"";
        $param['max_money']    = $_REQUEST['max_money']?$_REQUEST['max_money']:"";
        $param['probability']  = $_REQUEST['probability']?$_REQUEST['probability']:"";
        
        //邀请语
        $param['share_invitations']  = $_REQUEST['share_invitations']?$_REQUEST['share_invitations']:"邀请好友赚大礼";
        
        //新人奖励
        $param['new_link_coupons_save']  = $_REQUEST['new_link_coupons_save']?$_REQUEST['new_link_coupons_save']:-1;
        $param['new_coupon_guide_url']  = $_REQUEST['new_coupon_guide_url']?$_REQUEST['new_coupon_guide_url']:"";

        //背景图片
        $share_background_img         = $_REQUEST['share_background_img']?$_REQUEST['share_background_img']:"";
        $receive_background_img       = $_REQUEST['receive_background_img']?$_REQUEST['receive_background_img']:"";
        $have_receive_background_img  = $_REQUEST['have_receive_background_img']?$_REQUEST['have_receive_background_img']:"";
        $leaderboards_background_img  = $_REQUEST['leaderboards_background_img']?$_REQUEST['leaderboards_background_img']:"";
        //图片上传   
         
        $image = new image();
        //分享页背景图
        $param['share_background_img_path'] = $share_background_img;
        if($_FILES['share_background_img']['name']){                       
            $share_background_img_path = $image->upload_image ($_FILES['share_background_img'],$customer_id,'share_activity');
            

            if($share_background_img_path){
                $param['share_background_img_path']='/resources/'.$share_background_img_path;
            }else{
                $param['share_background_img_path']=$share_background_img;
            }
        }   

        //领取页背景
        $param['receive_background_img_path'] = $receive_background_img;  
        if($_FILES['receive_background_img']['name']){                     
            $receive_background_img_path = $image->upload_image ($_FILES['receive_background_img'],$customer_id,'share_activity');
            
            if($receive_background_img_path){
                $param['receive_background_img_path']='/resources/'.$receive_background_img_path;
            }else{
                $param['receive_background_img_path']=$receive_background_img;
            }
        }
        //已领取页背景
        $param['have_receive_background_img_path'] = $have_receive_background_img;
        if($_FILES['have_receive_background_img']['name']){                       
            $have_receive_background_img_path = $image->upload_image ($_FILES['have_receive_background_img'],$customer_id,'share_activity');
            
            if($have_receive_background_img_path){
                $param['have_receive_background_img_path']='/resources/'.$have_receive_background_img_path;
            }else{
                $param['have_receive_background_img_path']=$have_receive_background_img;
            }
        }
        //排行榜背景
        $param['leaderboards_background_img_path'] = $leaderboards_background_img;
        if($_FILES['leaderboards_background_img']['name']){                       
            $leaderboards_background_img_path = $image->upload_image ($_FILES['leaderboards_background_img'],$customer_id,'share_activity');
            
            if($leaderboards_background_img_path){
                $param['leaderboards_background_img_path']='/resources/'.$leaderboards_background_img_path;
            }else{
                $param['leaderboards_background_img_path']=$leaderboards_background_img;
            }
        }

        //分享说明信息
        $param['is_share_instruction']        = $_REQUEST['is_share_instruction']?(int)$_REQUEST['is_share_instruction']:0;
        $param['share_instruction']           = $_REQUEST['share_instruction']?$_REQUEST['share_instruction']:"";
        
        //领取说明信息
        $param['is_receive_instruction']      = $_REQUEST['is_receive_instruction']?(int)$_REQUEST['is_receive_instruction']:0;
        $param['receive_instruction']         = $_REQUEST['receive_instruction']?$_REQUEST['receive_instruction']:"";
        
        //排行榜说明信息
        $param['is_leaderboards_instruction'] = $_REQUEST['is_leaderboards_instruction']?(int)$_REQUEST['is_leaderboards_instruction']:0;
        $param['leaderboards_instruction']    = $_REQUEST['leaderboards_instruction']?$_REQUEST['leaderboards_instruction']:"";
        
        $param['color']                       = $_REQUEST['color']?$_REQUEST['color']:"F76313";
        $param['receive_color']               = $_REQUEST['receive_color']?$_REQUEST['receive_color']:"F76313";
        $param['have_receive_color']          = $_REQUEST['have_receive_color']?$_REQUEST['have_receive_color']:"F76313";
        $param['leaderboards_color']          = $_REQUEST['leaderboards_color']?$_REQUEST['leaderboards_color']:"F76313";
        //保存，修改活动详情
        $result = $this->model_share_gifts->save_share_activity($param);

        header("Location:/mshop/admin/index.php?m=share_gifts&a=activity&customer_id=".$customer_id_en);                     
      
    }
    /**
     * 活动统计
     * 作者：刘伟涛
     */
    function activity_statistic()
    {
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $theme       = $this->theme;
        $data        = $this->parmdata;

        $pageNum                         = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $data['customer_id'] =  $customer_id;
        $data['pageNum']                 = $pageNum;//当前页



        $data['b.name']                   = $_REQUEST['name']?htmlspecialchars($_REQUEST['name']):"";
        $data['id']                         = $_REQUEST['id']?$_REQUEST['id']:"";
        $data['begin_time']              = $_REQUEST['begin_time']?$_REQUEST['begin_time']:"";//开始时间
        $data['begin_time_int']              =strtotime($data['begin_time']);

        $data['end_time']                = $_REQUEST['end_time']?$_REQUEST['end_time']:"";//结束时间
        $data['end_time_int']              =strtotime($data['end_time']);
        $data['b.status']                  = $_REQUEST['status']=="0"?"0":$_REQUEST['status'];//状态
        //查询活动列表
        $res   = $this->model_share_gifts->select_statistics($data);
        $info      = $res['activity_arr'];
        $pageCount = $res['pageCount'];
        $res['activity_count']?$res['activity_count']:0;
        $status = array(
            1 => '待启用',
            2 => '已启用',
            3 => '已结束',

        );
        include ('view/share_gifts/activity_statistic.html');
    }

    /**
     * 活动明细
     * 作者：刘伟涛
     */

    function activity_statistictdetail()
    {
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $theme       = $this->theme;
        $data        = $this->parmdata;

        $pageNum                         = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $data['customer_id']             =  $customer_id;
        $data['pageNum']                 = $pageNum;//当前页

        $data['activity_id']             = $_GET['activity_id'];
        $data['weixin_name']             = $_REQUEST['weixin_name']?htmlspecialchars($_REQUEST['weixin_name']):"";
        $data['user_id']                 = $_REQUEST['user_id']?$_REQUEST['user_id']:"";
        $data['begin_time']              = $_REQUEST['begin_time']?$_REQUEST['begin_time']:"";//开始时间

        $data['end_time']                = $_REQUEST['end_time']?$_REQUEST['end_time']:"";//结束时间
        
        $res   = $this->model_share_gifts->select_statisticsdetail($data);
        //查询活动详情列表
        $info      = $res['activity_arr'];
        $pageCount = $res['pageCount'];

        include ('view/share_gifts/activity_statistictdetail.html');
    }
    /**
     * 粉丝记录
     * 作者：刘伟涛
     */
     function user_infodetail()
    {
        $customer_id_en = $this->customer_id_en;
        $customer_id = $this->customer_id;
        $theme       = $this->theme;
        $data        = $this->parmdata;

        $pageNum                         = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $data['customer_id'] =  $customer_id;
        $data['pageNum']                 = $pageNum;//当前页

        $data['share_user_id']         = $_GET['share_user_id'];

        if ( $data['share_user_id'] == -1)  $data['share_user_id'] = '';

        $data['weixin_name']                   = $_REQUEST['weixin_name']?htmlspecialchars($_REQUEST['weixin_name']):"";
        $data['user_id']                       = $_REQUEST['user_id']?$_REQUEST['user_id']:"";
        $data['activity_id']                   = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";

        $res   = $this->model_share_gifts->select_infodetail($data);
        //查询活动详情列表
        $info      = $res['activity_arr'];
        $pageCount = $res['pageCount'];

        include ('view/share_gifts/user_infodetail.html');
    }

    /**
     * 用户统计
     * 作者:yezhantu
     */
    function activity_user_statistic()
    {
        $customer_id    = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $theme          = $this->theme;
        $data           = $this->parmdata;

        $pageNum                         = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;          //当前页
        $data['customer_id']             = $customer_id;
        $data['pageNum']                 = $pageNum;//当前页

        $data['user_name']               = $_REQUEST['user_name']?htmlspecialchars($_REQUEST['user_name']):"";     //用户名
        $data['user_number']             = $_REQUEST['user_number']?$_REQUEST['user_number']:""; //用户编号
        
        
        $res            = $this->model_share_gifts->get_user_statistic($data);
        $data2          = $res['statistic_arr'];
        // echo "<pre>";
        // print_r($data2);
        // echo "</pre>";
        $pageCount      = $res['pageCount'];
        $activity_count = $res['activity_count'];
        include ('view/share_gifts/activity_user_statistic.html');
    }

    
}
