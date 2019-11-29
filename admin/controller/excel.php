<?php 
class control_excel extends control_base{

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
     * 导出活动统计
     */
    function share_gifts(){

        $customer_id = $this->customer_id;
        $data        = $this->parmdata;

        $data['customer_id'] =  $customer_id;
        $data['b.name']                   = $_GET['name']?$_GET['name']:"";
        $data['id']                       = $_GET['id']?$_GET['id']:"";
        $data['begin_time']               = $_GET['begin_time']?$_GET['begin_time']:"";//开始时间
        $data['begin_time_int']           =strtotime($data['begin_time']);

        $data['end_time']                 = $_GET['end_time']?$_GET['end_time']:"";//结束时间
        $data['end_time_int']             =strtotime($data['end_time']);
        $data['b.status']                 = $_GET['status']=="0"?"0":$_GET['status'];//状态
        $res   = $this->model_share_gifts->select_statistics($data);
        $info      = $res['activity_arr'];

        $output=array();
        $status = array(
                        1 => '待启用',
                        2 => '已启用',
                        3 => '已结束',

                    );

        if($info){
            for($i=0;$i<count($info);$i++){
                $output[$i]['activity_id']                      =$info[$i]['activity_id'];
                $output[$i]['name']                             =$info[$i]['name'];
                $output[$i]['valid time']                       =$info[$i]['begin_time'].'至'.$info[$i]['end_time'];
                $output[$i]['status']                           =$status[$info[$i]['status']];
                $output[$i]['total_share_num']                  =$info[$i]['total_share_num'];
                $output[$i]['total_share_num']                  =$info[$i]['total_share_num'];
                $output[$i]['new_fans_num']                     =$info[$i]['new_fans_num'];
                $output[$i]['distribute_coupon_num']            =$info[$i]['distribute_coupon_num'];
                $output[$i]['distribute_red_envelopes_value']   =$info[$i]['distribute_red_envelopes_value'];
            }
        }   

        $this->exportexcel($output,array('活动编号','活动标题','活动时间','活动状态','分享总数','分享人数','邀请人数','活动所派优惠券数量','活动所派红包总额'),$activity['title']."活动名单");
        
    }

    /**
     * 导出用户统计
     */
    function activity_user_statistic(){
        $customer_id = $this->customer_id;
        $data        = $this->parmdata;

        $data['customer_id']        = $customer_id;
        $data['user_name']          = $_GET['user_name']?$_GET['user_name']:"";
        $data['user_number']        = $_GET['user_number']?$_GET['user_number']:"";

        $res            = $this->model_share_gifts->get_user_statistic($data);
        $info           = $res['statistic_arr'];
        $output=array();

        if($info){
            for($i=0;$i<count($info);$i++){
                $output[$i]['user_id']                              =$info[$i]['user_id'];
                $output[$i]['weixin_name']                          =strip_tags($info[$i]['weixin_name']);
                $output[$i]['relation_activity_num']                =$info[$i]['relation_activity_num'];
                $output[$i]['user_new_fans_total_num']              =$info[$i]['user_new_fans_total_num'];
                $output[$i]['user_total_share_total_num']           =$info[$i]['user_total_share_total_num'];
                $output[$i]['receive_coupon_total_num']             =$info[$i]['receive_coupon_total_num'];
                $output[$i]['receive_red_envelopes_total_value']    =$info[$i]['receive_red_envelopes_total_value'];
            }
        }   

        $this->exportexcel($output,array('用户编号','用户名','关联活动数','邀请人数','分享次数','活动所得优惠券数量','活动所得红包总额'),$activity['title']."参加活动人员名单");
    }

    /**
     * 导出指定活动统计
     */
    function activity_statistictdatail(){
        $customer_id = $this->customer_id;
        $data        = $this->parmdata;
        if ($_GET['activity_id']) {
            $data['activity_id']         = $_GET['activity_id'];
        }else{
            return false;
        }
        $data['customer_id']        = $customer_id;
        $data['weixin_name']        = $_GET['weixin_name']?$_GET['weixin_name']:"";
        $data['user_id']            = $_GET['user_id']?$_GET['user_id']:"";
        $data['begin_time']         = $_GET['begin_time']?$_GET['begin_time']:"";//开始时间
        $data['end_time']           = $_GET['end_time']?$_GET['end_time']:"";//结束时间

        $res            = $this->model_share_gifts->select_statisticsdatail($data);
        $info           = $res['activity_arr'];
        $output=array();

        if($info){
            for($i=0;$i<count($info);$i++){
                $output[$i]['user_id']                              =$info[$i]['user_id'];
                $output[$i]['weixin_name']                          =strip_tags($info[$i]['weixin_name']);
                $output[$i]['receive_coupon_num']                   =$info[$i]['receive_coupon_num'];
                $output[$i]['receive_red_envelopes_value']          =$info[$i]['receive_red_envelopes_value'];
                $output[$i]['user_total_share_num']                 =$info[$i]['user_total_share_num'];
                $output[$i]['user_new_fans_num']                    =$info[$i]['user_new_fans_num'];
            }
        }   

        $this->exportexcel($output,array('用户编号','用户名','优惠券数量','红包总额','分享次数','邀请人数'),$activity['title']."指定活动人员名单");
    }

    /**
     * 导出邀请记录
     */
    function user_infodetail(){
        $customer_id = $this->customer_id;
        $data        = $this->parmdata;
        
        $data['customer_id']        = $customer_id;
        $data['weixin_name']        = $_GET['weixin_name']?$_GET['weixin_name']:"";
        $data['user_id']            = $_GET['user_id']?$_GET['user_id']:"";
        $data['share_user_id']      = $_GET['share_user_id']?$_GET['share_user_id']:"";

        $res            = $this->model_share_gifts->select_infodetail($data);
        $info           = $res['activity_arr'];
        $output=array();

        if($info){
            for($i=0;$i<count($info);$i++){
                $output[$i]['user_id']                              =$info[$i]['user_id'];
                $output[$i]['weixin_name']                          =strip_tags($info[$i]['weixin_name']);
            }
        }   

        $this->exportexcel($output,array('用户编号','用户名'),$activity['title']."邀请明细");
    }

    /**
     * 导出活动明细
     */
    function activity_statistictdetail(){
        $customer_id = $this->customer_id;
        $data        = $this->parmdata;
        $data['activity_id']        = $_GET['activity_id'];
        $data['customer_id']        = $customer_id;
        $data['weixin_name']        = $_REQUEST['weixin_name']?htmlspecialchars($_REQUEST['weixin_name']):"";
        $data['user_id']            = $_REQUEST['user_id']?$_REQUEST['user_id']:"";
        $data['begin_time']         = $_REQUEST['begin_time']?$_REQUEST['begin_time']:"";//开始时间
        $data['end_time']           = $_REQUEST['end_time']?$_REQUEST['end_time']:"";//结束时间

        $res            = $this->model_share_gifts->select_statisticsdetail($data);
        $info           = $res['activity_arr'];
        $output=array();
        if($info){
            for($i=0;$i<count($info);$i++){
                $output[$i]['user_id']                              =$info[$i]['user_id'];
                $output[$i]['weixin_name']                          =strip_tags($info[$i]['weixin_name']);
                $output[$i]['receive_coupon_num']                   =$info[$i]['receive_coupon_num'];
                $output[$i]['receive_red_envelopes_value']          =$info[$i]['receive_red_envelopes_value'];
                $output[$i]['user_total_share_num']                 =$info[$i]['user_total_share_num'];
                $output[$i]['user_new_fans_num']                    =$info[$i]['user_new_fans_num'];
            }
        }   

        $this->exportexcel($output,array('用户编号','用户名','优惠券数量','红包总额','分享次数','邀请人数'),$activity['title']."活动明细");
    }
	
	//获取订单奖励订单明细
	public function order_reward_poll(){
		
		#引入模型类
		require_once($_SERVER['DOCUMENT_ROOT'].'/mshop/admin/model/order_reward.php');
		$this->model = new model_order_reward();
		
		$customer_id = $this->customer_id;
 
        $page			            = $_REQUEST['page'];
        $pageNum			        = $_REQUEST['pageNum'];
        $search_batchcode			= $_REQUEST['search_batchcode'];
        $search_starttime			= $_REQUEST['search_starttime'];
        $search_endtime			    = $_REQUEST['search_endtime'];
		$type			            = $_REQUEST['type'];
		$data_type			        = $_REQUEST['data_type'];
		
		
		
		if(!empty($search_batchcode) || !empty($search_starttime) || !empty($search_endtime)){
			$data_type = null;
			
		}
		
		$condition = [];
        $condition['search_batchcode']  = $search_batchcode;
        $condition['search_starttime']  = $search_starttime;
        $condition['search_endtime']    = $search_endtime;
        $condition['status']               = $type?$type:0;
		
		switch($data_type){
			
			case 'today':	//当天
				$condition['createtime'] = ' and date(poll.createtime) = curdate()';  
			break;			
			case 'yesterday':  //昨日
				$condition['createtime'] = ' and  (to_days(now())-to_days(poll.createtime)) = 1';
			break;
			case 'week':	//近7天	
				$condition['createtime'] = ' and  date_sub(curdate(), INTERVAL 7 DAY) <= date(poll.createtime) ';   //包含今日
			break;
			case 'month':	//近30天
				$condition['createtime'] = ' and  date_sub(curdate(), INTERVAL 30 DAY) <= date(poll.createtime)' ;   //包含今日
			break;
			
			
		}
		
		var_dump($condition);
		
		if( $type == 1 ){
			 $title = '已处理';
		}elseif( $status == 0 ){
			 $title = '待处理';
		}else{
			 $title = '不处理';
		}

		
		$info = $this->model->order_poll_all($customer_id,$condition);
	 
		$output=array();
        if($info){
            for($i=0;$i<count($info);$i++){
                $output[$i]['batchcode']                            = '`'.$info[$i]['batchcode'];
                $output[$i]['user_id']                              = $info[$i]['user_id'];
                $output[$i]['weixin_name']                          = strip_tags($info[$i]['weixin_name']);
				$money = empty($info[$i]['money'])?0:$info[$i]['money'];
                $output[$i]['money']                   				= $money;
				$type_str = '';
				$status  = $info[$i]['status'];
				if( $status == 1 ){
					 $type_str = '已处理';
				}elseif( $status == -1 ){
					 $type_str = '不处理';
				}else{
					 $type_str = '待处理';
				}
                			
				$output[$i]['status']          						= $type_str;
                //$output[$i]['run_num']                 				= ($info[$i]['run_num']+1) ;
				$remark = empty($info[$i]['remark'])?'无':$info[$i]['remark'];
                $output[$i]['remark']                 				= $remark;
                $output[$i]['createtime']                    		= $info[$i]['createtime'];
            }
			
			 $this->exportexcel($output,array('订单号','用户名ID','用户名称','奖励金额','处理的状态',/*'执行次数',*/'备注','创建时间'),$title."订单明细");
        }else{

			echo '<script>alert("暂无数据可导出");history.go(-1);</script>';
		}		
		
			
	 
		
	}
	
	

	public function count_proxy(){
        $proxy_order_count = new OrderingretailRptProxyOrderCount();
        $proxy = new OrderingretailProxy();
        /*导出数据*/
        $page = input('page',0);              //当前页数
        $pageNum = input('pageNum');        //一次输出的数据量
        $pages = "$page,$pageNum";               //分页集合
        $output = input("output");
        $count = input("count");
        $page_count = input("page_count");
        $iscount = $page ? 2 : 1;
        $filename = "订货商统计" . date("YmdHi");
        $title = array("订货商信息","进货总量","进货总额","销售量",'销售额');
        /**/
        $start_time=input('starttime',date('Y-m-d',strtotime("-1 month")));
        $end_time=input('endtime');
        if(empty($end_time)){
            $end_time =   date("Y-m-d",strtotime("-1 day"));
        }
        $condition = array(
            "customer_id"=>$this->customer_id,
            "time"       => array('elt',$end_time)
        );
        if(!empty($start_time)){
            $condition1 = array("exp","'time'>='" . $start_time."'");
            array_push($condition,$condition1);
        }

        $count_info = $proxy_order_count->count_info($condition);
        $stock_counts = array();
        if (!empty($count_info)) {
            foreach ($count_info as $k => $v) {
                if ($v['proxy_id'] != "") {
                    $stock_counts[$v['proxy_id']]['proxy_id'] = $v['proxy_id'];
                    $stock_counts[$v['proxy_id']]['stock_count'] += $v['stock_count'];
                    $stock_counts[$v['proxy_id']]['stock_price'] += $v['stock_price'];
                    $stock_counts[$v['proxy_id']]['sale_price'] += $v['sale_price'];
                    $stock_counts[$v['proxy_id']]['sale_count'] += $v['sale_count'];
                }
            }
        }
        if (!empty($stock_counts)) {
            foreach ($stock_counts as $val) {
                $key_arrays[] = $val['stock_price'];
            }
            array_multisort($key_arrays, SORT_DESC, SORT_NUMERIC, $stock_counts);
        }
        foreach ($stock_counts as $k => $v) {
            //代理商信息
            $condition = array(
                "a.id" => $v['proxy_id'],
            );
            $proxy_info = $proxy->proxy_base_info($condition, 1);
            if(!empty($proxy_info)){
                $stock_counts[$k]['proxy_name'] = $proxy_info['name'];
                $stock_counts[$k]['proxy_level_name'] = $proxy_info['proxy_level_name'];
                $stock_counts[$k]['phone'] = $proxy_info['phone'];
            }else{
                unset($stock_counts[$k]);
            }
        }

        if($iscount==1){
            $result = count($stock_counts);
        }else{
            $stock_counts = array_slice($stock_counts,($page-1)*$pageNum,$pageNum);
            $result["item"] = $stock_counts;
        }

        $page_data = $this->ajax_excel_output($result,$output,$title,$filename,$page,$pageNum,$count,$page_count);  //导出数据处理
        $data=[];
        if ($page_data["code"] == 1){
            foreach ($result["item"] as $k=>$v){
                $data[$k]['paoxy_info']=$v['proxy_name'].'，'.$v['phone'].'，'.$v['proxy_level_name'];
                $data[$k]['stock_count']=$v['stock_count']==0?$v['stock_count']="0.00":$v['stock_count'];
                $data[$k]['stock_price']=$v['stock_price']==0?$v['stock_price']="0.00":$v['stock_price'];
                $data[$k]['sale_count']=$v['sale_count']==0?$v['sale_count']="0.00":$v['sale_count'];
                $data[$k]['sale_price']=$v['sale_price']==0?$v['sale_price']="0.00":$v['sale_price'];
            }
            /**导出数据处理**/
            $array_exlce_cache = Session::get('excel_cache');
            foreach($data as $val){$array_exlce_cache[] = $val; }
            Session::set('excel_cache',$array_exlce_cache);
            if($page_data['page_count'] < $page_data['page']){
            }elseif($pageNum <= 0){
                $this->exportexcel($data,$title,$filename);
            }
        }
        return json_encode($page_data);
    }

    /**
    * 导出数据为excel表格
    *@param $data    一个二维数组,结构如同从数据库查出来的数组
    *@param $title   excel的第一行标题,一个数组,如果为空则没有标题
    *@param $filename 下载的文件名
    *@examlpe
    $stu = M ('User');
    $arr = $stu -> select();
    exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
    */
    function exportexcel($data=array(),$title=array(),$filename='report'){
        //header("Content-type:application/octet-stream");
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$filename.".xls");
        header("Pragma: no-cache");

        header("Expires: 0");
        ob_clean();
        //导出xls 开始
        if (!empty($title)){
            foreach ($title as $k => $v) {
                $title[$k]=iconv("UTF-8", "GBK//IGNORE",$v);//识别繁体字、特殊字符
                //$title[$k]=iconv("UTF-8", "GB2312",$v);
                //$title[$k]=iconv("BIG5", "GB2312",$v);
            }
            $title= implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)){
            foreach($data as $key=>$val){
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck]=iconv("UTF-8", "GBK//IGNORE", $cv);//识别繁体字、特殊字符
                    
                    //echo $data[$key][$ck]= mb_convert_encoding($cv, "GBK", "UTF-8");
                    if($data[$key][$ck] == '')
                    {
                        $data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
                    }   
                    if($data[$key][$ck] == '')
                    {
                        $data[$key][$ck]= mb_convert_encoding($cv, "GBK", "UTF-8");
                    }
                }
                $data[$key]=implode("\t", $data[$key]);
            }
            echo implode("\n",$data);
        }
    }
	
	
	 public function exportexcel2($data=array(), $title=array(), $filename='report'){
        /*//header("Content-type:application/octet-stream");
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$filename.".xls");
//        header("Content-Disposition:attachment;filename=".$filename.".xlsx"); //@lml更换为xlsx
        header("Pragma: no-cache");
        header("Expires: 0");

        //导出xls 开始
        if (!empty($title)){
            foreach ($title as $k => $v) {
                $title[$k]=iconv("UTF-8", "GB2312",$v);
                //$title[$k]=iconv("BIG5", "GB2312",$v);
            }
            $title= implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)){
            foreach($data as $key=>$val){
                foreach ($val as $ck => $cv) {
                    //$data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
                    $data[$key][$ck]= mb_convert_encoding($cv, "GBK", "UTF-8");
                }
                $data[$key]=implode("\t", $data[$key]);

            }
            echo implode("\n",$data);
        }*/

        $final_arr=array();
        $final_arr[0]=$title;
        if(!empty($data)){
            foreach ($data as $dk=>$dv){
                array_push($final_arr,$dv);
            }
        }
        /*echo '<pre>';
        print_r($final_arr);die();*/
        if(count($data) > 0){
            outputToExcel($filename,$final_arr);
        }
    }


}
 ?>
