<?php 
/**
 *                             _ooOoo_
 *                            o8888888o
 *                            88" . "88
 *                            (| -_- |)
 *                            O\  =  /O
 *                         ____/`---'\____
 *                       .'  \\|     |//  `.
 *                      /  \\|||  :  |||//  \
 *                     /  _||||| -:- |||||-  \
 *                     |   | \\\  -  /// |   |
 *                     | \_|  ''\---/''  |   |
 *                     \  .-\__  `-`  ___/-. /
 *                   ___`. .'  /--.--\  `. . __
 *                ."" '<  `.___\_<|>_/___.'  >'"".
 *               | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *               \  \ `-.   \_ __\ /__ _/   .-` /  /
 *          ======`-.____`-.___\_____/___.-`____.-'======
 *                             `=---='
 *          ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *                     佛祖保佑        永无BUG
*/
class control_brandsubscribe extends control_base 
{
	var $model;
	var $model_common;
	var $customer_id;
	function __construct() 
	{
		parent::__construct();
		require_once('model/brandsubscribe.php');
		$this->model = new model_brandsubscribe();
		require_once('model/common.php');
        $this->model_common = new model_common();
		parent::check_login();
		
    }

    /*
     * 品牌订阅 关联产品列表
     * $Author: zqs
     * $2018-05-07  
     */
     public function related_products_list(){
     	//查询出主题颜色
     	$customer_id = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);

        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页
        $param['activity_id']     = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $add_type                 = $_REQUEST['add_type']?$_REQUEST['add_type']:-1;
        $activity_id              = $param['activity_id'];

        // 数据校验
        if( $param['activity_id'] == ''){
            json_out(['errmsg'=>'活动编码不能为空']);
        }

        $data['id']               = $param['activity_id'];
        $data['customer_id']      = $customer_id;
        $param['customer_id']      = $customer_id;

        //获取活动详情
        $arr = $this->model->get_activity($data);
        //获取产品配置
        $res = $this->model->get_relate_prod($param);

        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];
     	include('view/brandsubscribe/related_products.html');
     }

    /*
     * 品牌订阅 添加关联产品列表
     * $Author: zqs
     * $2018-05-07  
     */
     public function related_products_add(){
     	//查询出主题颜色
     	$customer_id = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);

        $pageNum                   = $_GET['pagenum']?$_GET['pagenum']:1;//当前页
        if ($_POST['pagenum']) {
            $pageNum               = $_POST['pagenum'];
        }
        $param['product_id']       = $_REQUEST['product_id']?$_REQUEST['product_id']:-1;        
        $param['product_name']     = $_REQUEST['product_name']?$_REQUEST['product_name']:"";
        $param['product_type']     = $_REQUEST['product_type']?$_REQUEST['product_type']:-1;
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $add_type                  = $_REQUEST['add_type']?$_REQUEST['add_type']:-1;
        $param['pageNum']          = $pageNum;//当前页
        $param['customer_id']      = $this->customer_id;

        // 数据校验
        if( $param['activity_id'] == ''){
            json_out(['errmsg'=>'活动编码不能为空']);
        }

        $res = $this->model->get_add_relation($param);

        $type      = $res['type'];
        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];
     	include('view/brandsubscribe/related_add.html');
     }

    /*
     * 品牌订阅 添加关联产品
     * $Author: zqs
     * $2018-05-07  
     */
    public function add_related(){
        $param['idsStr']           = $_REQUEST['idsStr']?$_REQUEST['idsStr']:'';        
        $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"";
        $param['customer_id']      = $this->customer_id;
        $str                       = substr($param['idsStr'],strlen($param['idsStr'])-1,strlen($param['idsStr']));
        if ($str == ',') {
            $param['idsStr']       = substr($param['idsStr'],0,strlen($param['idsStr'])-1);
        }

        $res = $this->model->add_related($param);
      
        json_out($res);
    }

    /*
     * 品牌订阅 删除关联产品
     * $Author: zqs
     * $2018-05-07  
     */
    public function del_related(){     
        $param['id']               = $_REQUEST['id']?$_REQUEST['id']:"";
        $param['customer_id']      = $this->customer_id;
        $res = $this->model->del_related($param);
        json_out($res);
    }

    /*
     * 品牌订阅 修改关联产品属性
     * $Author: zqs
     * $2018-05-07  
     */
    public function update_related(){     
        $param['id']               = $_REQUEST['id']?$_REQUEST['id']:""; //品牌订阅活动产品关联表id
        $param['str']              = $_REQUEST['str']?$_REQUEST['str']:"";//修改的字段
        $param['obj']              = $_REQUEST['obj']?$_REQUEST['obj']:"";//修改的数据
        $param['customer_id']      = $this->customer_id;
        $res = $this->model->update_related($param);
        json_out($res);
    }

    // /*
    //  * 品牌订阅 修改关联产品关联礼包 5.28改需求，不需要产品单独关联礼包
    //  * $Author: zqs
    //  * $2018-05-07  
    //  */
    // public function upd_pack(){     
    //     $param['id']               = $_REQUEST['id']?$_REQUEST['id']:""; //品牌订阅活动产品关联表id
    //     $param['package_id']       = $_REQUEST['package_id']?$_REQUEST['package_id']:"-1";//礼包id
    //     $param['activity_id']      = $_REQUEST['activity_id']?$_REQUEST['activity_id']:"-1";//活动id
    //     $param['customer_id']      = $this->customer_id;
    //     $res = $this->model->upd_pack($param);
    //     json_out($res);
    // }

	/**添加/编辑活动
	* @author  HMJ-V384
	* @param  
	* @version  2018-05-07
	* @return  
	* @var  
	*/    
	public function activity_edit(){
		$data['customer_id'] = $customer_id = $this->customer_id;
     	//查询出主题颜色
        $theme  = $this->model_common->find_theme($customer_id);

		$data['id'] = $activity_id = isset($_REQUEST['activity_id'])?$_REQUEST['activity_id']:-1;
		$is_ajax 	= isset($_REQUEST['is_ajax'])?$_REQUEST['is_ajax']:-1;

		if($is_ajax == 1) {
			$res = $this->model->get_activity($data);
            $res1 = $this->model->get_package_act($data); 
            $res2 = $this->model->get_packages($data);
            if($res1['errcode'] != 0) {
                json_out($res1);
            } 

            $res['data']['package'] = $res1['data'];
            $res['data']['packages'] = $res2;

			if($res) {
				$ret = array('errcode' => 0,'errmsg' => '读取活动信息成功！','data' => $res, );
			} else {
				$ret = array('errcode' => 400,'errmsg' => '读取活动信息失败！','data' => $res, );
			}
			json_out($ret);
		} else if($is_ajax == 2){
            $res = $this->model->get_packages($data); 

            $ret = array('errcode' => 0,'errmsg' => '读取礼包成功！','data' => $res, );
            json_out($ret);
        } else {
			include('view/brandsubscribe/activity_edit.html');		
		}

	}

	/**添加/编辑活动保存
	* @author  HMJ-V384
	* @param  
	* @version  2018-05-07
	* @return  
	* @var  
	*/    
	public function activity_save(){
		$customer_id = $this->customer_id;
		$data = $_POST;
        foreach ($data as $key => $value) {
            $data[$key] = mysql_escape_string($value);
        }
		$data['customer_id'] = $customer_id;
		$data['isvalid'] = 1;
		if($data['publish'] == 1) {
			$data['status'] = 2;
		}
		$res = $this->model->activity_save($data);
		json_out($res);
		
	}	

    /*
     * 品牌订阅 活动概况列表
     * $Author: lpx
     * $2018-05-07  
     */
    public function activity_list(){    
        $customer_id       = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $customer_id;
        $param['activity_id']     = $_REQUEST['activity_id']?$_REQUEST['activity_id']:-1;        
        $param['activity_name']   = $_REQUEST['activity_name']?$_REQUEST['activity_name']:"";
        $param['starttime']       = $_REQUEST['starttime']?$_REQUEST['starttime']:-1;
        $param['endtime']         = $_REQUEST['endtime']?$_REQUEST['endtime']:-1;
        $param['activity_status'] = $_REQUEST['activity_status']?$_REQUEST['activity_status']:-1;       
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res = $this->model->get_activities($param);
        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];        
        include('view/brandsubscribe/activity_general_situation.php');
    }

	/**活动管理
	* @author  HMJ-V384
	* @param  
	* @version  2018-05-07
	* @return  
	* @var  
	*/    
    function activity_management(){             
        $customer_id = $this->customer_id;
        $theme       = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $customer_id;
        $param['id']         	  = $_REQUEST['id']?$_REQUEST['id']:-1;        
        $param['status']          = $_REQUEST['status']?$_REQUEST['status']:-1;
        $param['start_time']      = $_REQUEST['start_time']?$_REQUEST['start_time']:-1;
        $param['end_time']        = $_REQUEST['end_time']?$_REQUEST['end_time']:-1;
        $param['name']            = $_REQUEST['name']?$_REQUEST['name']:'';       
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res       = $this->model->activity_management($param);//获取店主列表
        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];        
        include("view/brandsubscribe/activity_management.php");
    }

	/**活动发布/终止/删除
	* @author  HMJ-V384
	* @param  
	* @version  2018-05-08
	* @return  
	* @var  type: 0发布5终止2删除  | id
	*/    
    function activity_deal(){             
        $customer_id =  $data['customer_id'] = $this->customer_id;
        $id  		 =  $data['id']     	 = $_POST['id'];
        $type  		 =  $data['type']     	 = $_POST['type'];
        if(empty($id) || $type == ''){
        	$ret = array('errcode' => 400,'errmsg' => '活动编码异常！','data' => '', );
        	json_out($ret);
        }
        $ret = $this->model->activity_deal($data);//活动状态处理
        json_out($ret);

    }      

    /*
     * 品牌订阅 活动明细列表
     * $Author: lpx
     * $2018-05-08  
     */
    public function activity_details_list(){
        $customer_id       = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $customer_id;
        $param['activity_id']     = $_REQUEST['activity_id']?$_REQUEST['activity_id']:-1;        
        $param['user_name']       = $_REQUEST['user_name']?$_REQUEST['user_name']:"";
        $param['user_id']         = $_REQUEST['user_id']?$_REQUEST['user_id']:-1;
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        // 数据校验
        if( $param['activity_id'] == -1){
            json_out(['errmsg'=>'活动编码不能为空']);
        }
        
        $res = $this->model->get_activity_details($param);
        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];  
        include('view/brandsubscribe/activity_details.php');
    }

    /*
     * 品牌订阅 用户订阅产品明细列表
     * $Author: lpx
     * $2018-05-11  
     */
    public function user_product_list(){
        //查询出主题颜色
        $customer_id = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);

        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页
        $param['activity_id']     = $_REQUEST['activity_id']?$_REQUEST['activity_id']:-1;
        $param['user_id']         = $_REQUEST['user_id']?$_REQUEST['user_id']:"";
        $user_id                  = $param['user_id'];
        $param['product_name']    = $_REQUEST['product_name']?$_REQUEST['product_name']:"";
        $param['product_id']      = $_REQUEST['product_id']?$_REQUEST['product_id']:-1;
        $param['product_status']  = $_REQUEST['product_status']?$_REQUEST['product_status']:-1;

        // 数据校验
        if( $param['user_id'] == ''){
            json_out(['errmsg'=>'用户ID不能为空']);
        }

        $param['customer_id']      = $customer_id;

        $res = $this->model->get_user_product($param);

        $data      = $res['activity_arr'];
        $pageCount = $res['pageCount'];
        include('view/brandsubscribe/subscription_products.php');
    }

    /*
     * 品牌订阅 确认保存关联产品 5.28新加需求
     * $Author: zqs
     * $2018-05-28  
     */
    public function sava_relate_prod(){
    	$customer_id = $this->customer_id;
    	$activity_id = $_POST['activity_id']?$_POST['activity_id']:-1;
    	if ($activity_id==-1) {
    		$return['errcode'] = 4001;
    		$return['errmsg'] = '参数异常';
    	}
    	$data['customer_id'] = $customer_id;
    	$data['activity_id'] = $activity_id;
    	$res = $this->model->sava_relate_prod($data);
    	json_out($res);
    }
}
?>