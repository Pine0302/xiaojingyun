<?php
/**
 * @desc pcshop admin
 * @author Administrator
 */
require_once('pcbase.php');
class Pcinterfaceroom extends Pcbase{
	
	var $referer_web;
	public function __construct(){
		parent::__construct();
		$this->referer_web = $_SERVER['HTTP_REFERER'];
	}
	
	//添加分类
	public function settltype(){
		$result = [];
		$result['errcode'] = 0;
		$refererweb = $_SERVER['HTTP_REFERER'];
		if(empty($_POST)||$_POST['parent_id']=='')
		{header("location:".$refererweb);return;}
		$data = $_POST;
		$level = 0;
		$listorder = 100000;
		$db = Ycc::getInstance()->connect();
		$catename = htmlentities(trim($data['name'])); 
		$parent_id = htmlentities(trim($data['parent_id']));
		$createtime = $changetime = $this->nowtime;
		$_depth = intval(count(\Cc::pare($parent_id)));
		if($_depth>=3){
		echo "<script>alert('暂支持添加最多三级分类');location.href='".$refererweb."'</script>";return;}
		if($parent_id==0){
		$listorder = htmlentities(trim($data['listorder']));
		if($listorder=='first'){
		$listorder = 1;}else{
		$listorder = (int)$listorder;
		++$listorder;}}////
		$insertsql = "insert into pc_catetype(
				catename,
				parent_id,
				level,
				isvalid,
				createtime,
				changetime,
				listorder,
				customer_id) values(
				'".$catename."',
				".$parent_id.",
				".$level.",1,
				'".$createtime."',
				'".$changetime."',
				".$listorder.",
				".$this->customer_id.")";
		$insert = $db->query($insertsql);
		if(!$insert){
			echo "<script>alert('保存失败');location.href='".$refererweb."'</script>";return;
		} $insertInt = mysql_insert_id();
		$depth = count(Cc::pare($insertInt));
		$update = $db->query("update pc_catetype set level=\"{$depth}\" where id='".$insertInt."'");
		$save_msg = $update? '保存成功':'保存失败';
		$result = "<script>alert('".$save_msg."');location.href='".$refererweb."'</script>";
		exit($result);///
	}
	
	//upload 
	public function uploadfile(){
		$file = '';
		$result = [];
		$result['errcode'] = 0;
		$result['pic_id'] = '';
		if(!isset($_FILES['cover_pic'])){
			$result['errcode'] = 1;
			$result['data'] = '请选择上传图片';
			echo json_encode($result);return;
		}
		$file = $_FILES['cover_pic'];
		$file_up_result = $this->upload_file($file,$this->customer_id,true);
		$result['file_path'] = $file_path = $file_up_result['file_path'];
		$result['show_pic'] = $file_up_result['show_pic'];
		$result['data'] = $file_up_result['data'];
		if($file_up_result['errcode']){
			$result['errcode'] = 2;
		}else{///insert
			$nav_name = '';
			$link = '';
			$isvalid = 1;
			$logo = $file_path;
			$listorder = 100000;
			$createtime = $this->nowtime;
			$query_insert = "insert into pc_navigation(nav_name,logo,link,isvalid,createtime,listorder,customer_id) values('".$nav_name."','".$logo."','".$link."',0,'".$createtime."',".$listorder.",".$this->customer_id.")";  
			$db = Ycc::getInstance()->connect();
			$insert_result = $db->query($query_insert);
			if($insert_result)$insertInt = mysql_insert_id();
			if(isset($insertInt))$result['pic_id'] = $insertInt;
		} echo json_encode($result);
	}
	
	/**
	 * 文件上传
	 * @param $file : 上传的文件
	 * @param $customer_id :商家id
	 * @param $check_format :是否图片上传，是true，否false
	 * upload_max_filesize:文件上传允许最大值
	 * @return mixed
	 */
	public function upload_file($file,$customer_id,$check_format){
		$result = [];
		$result['errcode'] = 0;
		$result['file_path'] = '';
		$result['show_pic'] = '';
		$result['data'] = '上传成功';
		require_once $_SERVER['DOCUMENT_ROOT'].'/mp/lib/image.php';
		$image = new \image();
		$file_path = $image->upload_image($file,$customer_id,$dir_name='',$img_name='',$local_storage='',$check_format,$upload_max_filesize=4*1024*1024);
		if(!$file_path){
			$result['errcode'] = 1;
			$result['data'] = $image->error_msg;//上传失败;
		}else{
			$result['file_path'] = '/resources/'.$file_path;
			$result['show_pic'] = $this->http.'/resources/'.$file_path;
		} return $result;///
	}
	
	//添加产品
	public function save_goods(){
		$result = [];
		$pic_file = '';
		$result['errcode'] = 0;$data = $_POST;
		$refererweb = $_SERVER['HTTP_REFERER'];
		//echo $refererweb;die; ///https://admin.weisanyun.cn/weixinpl/back_newshops/PcShop/information/goods_add.php?customer_id=V2JUZFMxAjg=
		if(empty($_POST)){header("location:".$refererweb);return;}
		if(empty($_FILES['upfile_goods']['tmp_name'])){
			echo "<script>alert('请选择上传图片');location.href = '{$refererweb}';</script>";return;
		}
		$pic_file = $_FILES['upfile_goods'];
		$file_up_result = $this->upload_file($pic_file,$this->customer_id,true);
		if($file_up_result['errcode']){
			echo "<script>alert('{$file_up_result['data']}');location.href = '{$refererweb}';</script>";return;
		} $isvalid = 1;
		$pic = $file_up_result['file_path'];
		$title = htmlentities(trim($data['goods_title']));
		$cate_id = htmlentities(trim($data['parent_id']));
		$goods_desc = htmlentities(trim($data['goods_descc']));
		$text_content = trim($data['text_content']); ///富文本内容直接存表(不能转义存库)
		$db = Ycc::getInstance()->connect();
		$insert_query = "insert into pc_goods(title,pic,cate_id,goods_desc,details,isvalid,createtime,customer_id) values('".$title."','".$pic."','".$cate_id."','".$goods_desc."','{$text_content}',1,'".$this->nowtime."',".$this->customer_id.")";   
		$insertRes = $db->query($insert_query);
		if(!$insertRes){
			echo "<script>alert('保存失败');location.href = '{$refererweb}';</script>";
		}else{
			$refererweb .= '&newpic=yes';
			echo "<script>alert('保存成功');location.href = '{$refererweb}';</script>";
		}
	}
	
	//导航编辑 
	public function banner_edt_do(){
		//
		$result = [];
		$result['errcode'] = 0;
		$refererweb = $_SERVER['HTTP_REFERER'];
		if(empty($_POST)||$_POST['navigation_link']=='')
		{header("location:".$refererweb);return;}
		$data = $_POST;$listorder = 100000;
		if(!isset($data['title'])){
			echo "<script>alert('请填写导航名称');location.href = '{$refererweb}';</script>";return;
		} $db = Ycc::getInstance()->connect();
		if($data['listorder']!=-1){
		if($data['listorder']=='first'){
				$listorder = 1;
		}else{
		$listorder = htmlentities(trim($data['listorder']));
				$int_RegExp = '/^[0-9]*[1-9][0-9]*$/';
				if(!preg_match($int_RegExp,$listorder)){
						echo "<script>alert('排序参数不对');location.href = '{$refererweb}';</script>";return;
		} ++$listorder;
		}
		}
		
		$edtid = trim($_GET['edtid']);
		$upload_pic = trim($data['upload_pic']);
		if(!$upload_pic){
			$_pic = $db->get_one("select logo from pc_navigation where id='{$edtid}'");
			$upload_pic = $_pic['logo'];
		}
		
		/* if(!isset($data['upload_pic_id'])){
		echo "<script>alert('请上传LOGO');location.href = '{$refererweb}';</script>";return;
		} */
		
		
		if(!isset($data['navigation_link'])){
		echo "<script>alert('请填写链接地址');location.href = '{$refererweb}';</script>";return;
		}
		if(preg_match("/[\x{4e00}-\x{9fa5}]+/u",$data['navigation_link'])){
			echo "<script>alert('请填写正确的链接地址');location.href = '{$refererweb}';</script>";return;
		}
		$title = htmlentities(trim($data['title']));
		$upload_pic_id = $edtid;
		$navigation_link = htmlentities(trim($data['navigation_link']));
		$update_query = "update pc_navigation set logo='".$upload_pic."',nav_name='".$title."',isvalid=1,link='".$navigation_link."',changetime='".$this->nowtime."',listorder='{$listorder}' where id='{$upload_pic_id}'";
		
		$update_query_result = $db->query($update_query);
		if(!$update_query_result){
		echo "<script>alert('保存失败');location.href = '{$refererweb}';</script>";
		}else{
		$banner_url = $this->http.'/weixinpl/back_newshops/PcShop/information/banner.php?customer_id='.$this->customer_id_en;
			echo "<script>alert('保存成功');location.href = '{$banner_url}';</script>";
		}
	}
	
	/**
	 * 导航添加
	 */
	public function banner_add_do(){
		$result = [];
		$result['errcode'] = 0;
		$refererweb = $_SERVER['HTTP_REFERER'];
		if(empty($_POST)||$_POST['navigation_link']=='')
		{header("location:".$refererweb);return;}
		$data = $_POST;$listorder = 100000;
		if(!isset($data['title'])){
			echo "<script>alert('请填写导航名称');location.href = '{$refererweb}';</script>";return;
		}
		if($data['listorder']!=-1){
			if($data['listorder']=='first'){
				$listorder = 1;
			}else{
				$listorder = htmlentities(trim($data['listorder']));
				$int_RegExp = '/^[0-9]*[1-9][0-9]*$/';
				if(!preg_match($int_RegExp,$listorder)){
					echo "<script>alert('排序参数不对');location.href = '{$refererweb}';</script>";return;
				} ++$listorder;
			}
		}
		if(!isset($data['upload_pic_id'])){
			echo "<script>alert('请上传LOGO');location.href = '{$refererweb}';</script>";return;
		}
		if(!isset($data['navigation_link'])){
			echo "<script>alert('请填写链接地址');location.href = '{$refererweb}';</script>";return;
		}
		if(preg_match("/[\x{4e00}-\x{9fa5}]+/u",$data['navigation_link'])){
			echo "<script>alert('请填写正确的链接地址');location.href = '{$refererweb}';</script>";return;
		}
		$title = htmlentities(trim($data['title']));
		$upload_pic_id = (int)htmlentities(trim($data['upload_pic_id']));
		$navigation_link = htmlentities(trim($data['navigation_link']));
		$update_query = "update pc_navigation set nav_name='".$title."',isvalid=1,link='".$navigation_link."',changetime='".$this->nowtime."',listorder='{$listorder}' where id='{$upload_pic_id}'";  
		$db = Ycc::getInstance()->connect();
		$update_query_result = $db->query($update_query);
		if(!$update_query_result){
			echo "<script>alert('添加失败');location.href = '{$refererweb}';</script>";
		}else{
			$banner_url = $this->http.'/weixinpl/back_newshops/PcShop/information/banner.php?customer_id='.$this->customer_id_en;
			echo "<script>alert('添加成功');location.href = '{$banner_url}';</script>";
		}
	}
	
	//删除导航
	public function delnav(){
		$result = [];
		$result['errcode'] = 0;
		$result['data'] = '删除成功';
		$del_id = isset($_GET['del_id'])? trim($_GET['del_id']):'';
		if(!$del_id){
			$result['errcode'] = 1;
			$result['data'] = '缺少参数id';
			echo json_encode($result);return;
		} $db = Ycc::getInstance()->connect();
		$del_query = "update pc_navigation set isvalid=0 where id='".$del_id."'";
		$resdel = $db->query($del_query);
		if(!$resdel){
			$result['errcode'] = 2;
			$result['data'] = '删除失败';
		} echo json_encode($result);///
	}
	
	//编辑分类
	public function edtcate(){
		$result = [];
		$result['errcode'] = 0;
		$data = $_REQUEST;
		if(empty($data)||!$data['id']||!$data['content']){
			$result['errcode'] = 1;
			$result['data'] = '缺少参数';
			echo json_encode($result);return;
		} $db = Ycc::getInstance()->connect();
		$uptate_query = "update pc_catetype set catename='".$data['content']."' where id='".$data['id']."'";
		$res_update = $db->query($uptate_query);
		if(!$res_update){
			$result['errcode'] = 2;
			$result['data'] = '保存分类失败';
		}else{
			$result['data'] = '保存分类成功';
		} echo json_encode($result);
	}
	
	//删除分类
	public function delcate(){
		$del_id = isset($_GET['id'])? trim($_GET['id']):'';
		$return_url = "<script>location.href='".$this->referer_web."';</script>";
		if(!del_id)exit($return_url);
		$db = Ycc::getInstance()->connect();
		$query_cate = "select id,catename,parent_id from pc_catetype";
		$list = $db->query($query_cate);
		$list = \Cc::tck($list,$del_id);
		if(count($list)>0){
		echo "<script>alert('该分类含有子分类,不可以直接删除');location.href='".$this->referer_web."';</script>";return;}
		$del_query = "delete from pc_catetype where id='".$del_id."'";
		$res_del = $db->query($del_query);
		if(!$res_del){///
		echo "<script>alert('删除失败');location.href='{$this->referer_web}';</script>";return;}
		print $return_url;///
	}
	//删除订单
	public function delorder(){
		$result = [];
		$result['errcode'] = 0;
		$result['data'] = '删除成功';
		$order_id = isset($_GET['order_id'])? trim($_GET['order_id']):'';
		if(!$order_id){
			$result['errcode'] = 1;
			$result['data'] = '缺少参数order_id';
			echo json_encode($result);return;
		} $db = Ycc::getInstance()->connect();
		$update_query = "update `pc_order` set isvalid=0 where id='{$order_id}'";
		$res_update = $db->query($update_query);
		if(!$res_update){
			$result['errcode'] = 2;
			$result['data'] = '删除失败';
		} print json_encode($result);///
	}
	
	
	
}

$ifsroom = new Pcinterfaceroom();
$ifsroom->$_GET['action']();



