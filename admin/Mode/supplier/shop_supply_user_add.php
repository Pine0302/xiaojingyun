<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');

	$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	_mysql_query("SET NAMES UTF8");
	require('../../../../weixinpl/proxy_info.php');

	$keyid = -1;
	$sex = 1;
  
	if(!empty($_GET["keyid"])){
		$keyid = $configutil->splash_new($_GET["keyid"]);
		// 用户名(自填 weixin_users表)   
		// 推广员表 密码 password
		// apply表 正式姓名、手机号码、性别、 地址 、详细地址、身份证号、公司名称、身份证营业执照相片
		// apply.    wu.  p.
		$query='select wu.name,wu.pwd,p.pwd as ppwd,apply.user_name,apply.user_phone,apply.sex,apply.location_p,apply.location_c,apply.location_a,apply.business_address,apply.id_cards_num,apply.company_name,apply.id_cards_pic,apply.business_licence_pic from '.WSY_SHOP.'.weixin_commonshop_applysupplys apply left join '.WSY_USER.'.weixin_users wu on apply.user_id=wu.id left join '.WSY_PUB.'.promoters p on apply.user_id=p.user_id   where apply.isvalid=true and wu.isvalid=true and p.isvalid=true and apply.user_id='.$keyid;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
		while ($row = mysql_fetch_object($result)) {
			$name=  $row->name;
			$pwd=   $row->pwd;
			$ppwd= $row->ppwd;
			$user_name= $row->user_name;
			$user_phone= $row->user_phone;
			$sex= $row->sex;
			$location_p= $row->location_p;
			$location_c= $row->location_c;
			$location_a= $row->location_a;
			$business_address= $row->business_address;
			$id_cards_num= $row->id_cards_num;
			$company_name= $row->company_name;

		    $id_cards_pic = $row->id_cards_pic;
		    if($id_cards_pic!=''){
		    	$id_cards_pic=explode('|', $id_cards_pic);
		    }

		    $business_licence_pic = $row->business_licence_pic;
		    if($business_licence_pic!=''){
		    	$business_licence_pic=explode('|', $business_licence_pic);
		    }
		}

	}
	// var_dump($name);die;
	$query="select supply_must from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
    $supply_must = explode('_',$row->supply_must);
	}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<!--上传图片样式start-->
<link rel="stylesheet" type="text/css" href="css/font-awesome.css"/>
<link rel="stylesheet" type="text/css" href="css/UploadImg.css"/>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
<!--上传图片样式end-->

<script>
    pro = '{$area}';
</script>
<link rel="stylesheet" href="./css/area_select.css">
<!--<script type="text/javascript" src="js/region_select2.js"></script>-->

<style type="text/css">
	.del-upload{margin-right: 0px !important;}
	.selectArea {
        display: inline-block !important;
    }
</style>
<title>合作商设置</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<script>
 function check(num){
	var check_num=/^[0-9]*$/.test(num);
	return check_num;
}	

 function submitV(a){
	if($(a).hasClass("disable")){
        return;
    }

	var name = document.getElementById("username").value;
	if(name==""){
	    alert('请输入用户名!');
	   return;
	}
	<?php   if($keyid<0){?>
	var password = document.getElementById("password").value;
	if(password==""){
	    alert('请输入密码!');
	   return;
	}
	if(password.length<4){
	    alert('密码至少4个字符!');
	   return;
	}

	// 手动添加时$keyid值为-1   修改时值为其ID
	if(<?php echo $supply_must[0] ?>==1&&<?php echo $keyid == -1 ?>){
		var trur_name = $('#name').val();
		var phone = $('#phone').val();
		if(trur_name==''){
			alert('请填写真实姓名');
			return;
		}
		if(phone==''){
			alert('请填写手机号码');
			return;
		}
	}


	if(<?php echo $supply_must[1] ?>==1&&<?php echo  $keyid == -1 ?>){
		var business_address = $('#business_address').val();
		if(business_address==''){
			alert('请填写详细地址');
			return;
		}
	}
    
	if(<?php echo $supply_must[2] ?>==1&&<?php echo $keyid == -1 ?>){
		var company_name = $('#company_name').val();
		if(company_name==''){
			alert('请填写公司名称');
			return;
		}
	}

	if(<?php echo $supply_must[3] ?>==1&&<?php echo $keyid == -1 ?>){
		var idcard_num = $('#idcard_num').val();
		if(idcard_num==''){
			alert('请填写身份证号码');
			return;
		}
	}

	//判断有没上传身份证图片，至少上传一张
	if(<?php echo $supply_must[4] ?>==1&&<?php echo $keyid == -1 ?>){
		var is_none = $('#uploadidcard').find('.img-show').attr('src');
		console.log(is_none);
		if(is_none==''||is_none == undefined){
			alert('请上传身份证图片');
			return;
		}
	}

	//判断有没上传身份证图片，至少上传一张
	if(<?php echo $supply_must[5] ?>==1&&<?php echo $keyid == -1 ?>){
		var is_none = $('#uploadbusiness').find('.img-show').attr('src');
		if(is_none==''||is_none == undefined){
			alert('请上传营业执照图片');
			return;
		}
	}


	<?php } ?>

	var check_uid=$('#check_uid').val();
	var userName=$('#userName').val();
	var keyid=<?php echo $keyid ?>;
	// console.log(keyid);

	if((userName==''&&check_uid=='')||(keyid!=-1)){
		document.getElementById("upform").submit();
	}else{
		$.ajax({
            type: "post",
            url: "./shop_supply_user_save.php",
            dataType: "json",
            data: {'check_uid': check_uid,'is_check':true},
            success: function (result){
                if(result.errcode=="0"){
                	document.getElementById("upform").submit();	   
                }else{
                	alert(result.msg);
                }
            }
        });
	}
 } 

</script>
<style type="text/css">
.WSY_member textarea {
width: 350px;
height: 150px;
}
.WSY_member dt{text-align:right}
dt{
	margin-top:6px;
}
.WSY_member dd{margin: 5px 0 0 10px !important;}
.username_search{margin-top: 0px !important;height: 22px !important;font-size:15px !important;cursor:pointer;}
</style>
<body>
<div class="div_new_content">
<form action="shop_supply_user_save.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
	<input type="hidden" name="keyid" value="<?php echo $keyid; ?>" />
    <div class="WSY_content">
		<div class="WSY_columnbox">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1"><?php if($keyid>0){echo "修改";}else{echo "添加";} ?>合作商</a>
				</div>
			</div>

			<div class="WSY_data">

			<?php if($keyid==-1) { ?>
				<dl class="WSY_member">
					<dt>用户名</dt>
					<dd>
						<select name="userName" id="userName" onchange="addValue(this)">
							<!-- 遍历出查询出来的值 -->
							<option value="" >请选择一个上线</option>
						</select>
					</dd>
					<dd><input type=text value="" name="" id="promoters_name" placeholder="输入名称进行搜索" style="width:180px;" /></dd>
					<dd><input type=text value="" name="" id="promoters_id" placeholder="输入用户编号进行搜索" style="width:180px;" /></dd>
					<dd><input type="button" class="WSY_button username_search" value="搜索" onclick="searchForm()" /></dd>
				</dl>
			<?php } ?>

				<dl class="WSY_member">
					<dt>用户名</dt>
					<dd><input type=text  name="username" id="username" style="width:250px;" maxlength="16"  onkeyup="count_size(this,20);"  onblur="count_size(this,20)" value="<?php echo $name; ?>" /></dd>长度为不得超过20位字符</dd>
					<input type="hidden" name="check_uid" id="check_uid"  value="">
				</dl>

				<dl class="WSY_member">
					<dt>后台登录密码</dt>
					<dd><input type=text name="password" id="password" value="<?php echo $pwd; ?>" style="width:250px;" maxlength="16"/></dd>长度为4~16位字符</dd>
				</dl>

				<!-- 记录该合作商是PC端申请的 -->
				<input type="hidden" name="apply_way" value="0">

			<?php if($supply_must[0]==1){ ?>
				<dl class="WSY_member">
					<dt>真实姓名</dt>
					<dd><input type=text tips_name="真实姓名" name="name" id="name" value="<?php echo $user_name ?>" style="width:250px;" onkeyup="count_size(this,20);" onblur="count_size(this,20)"/></dd>
					<dd>长度为1-20位字符</dd>
				</dl>

				<dl class="WSY_member">
					<dt>手机号码</dt>
					<dd><input type=text tips_name="手机号码" name="phone" id="phone" value="<?php echo $user_phone ?>" style="width:250px;" onkeyup="count_size(this,20);" onblur="count_size(this,20)" oninput="value=value.replace(/[^\d]/g,'')"/></dd>
					<dd>长度为1-20位数字</dd>
				</dl>	
			<?php } ?>
				<dl class="WSY_member">
					<dt>性别</dt>
					<dd>
					<select name="sex" id="sex" style="width:250px;">
						<option value="1" <?php if($sex==1){echo 'selected="selected"';} ?> >男</option>
						<option value="2" <?php if($sex==2){echo 'selected="selected"';} ?> >女</option>
					</select>
				</dl>

				

			<?php if($supply_must[1]==1){ ?>
				<!--<dl class="WSY_member">
					<dt>省市区</dt>
					<dd><input type=text value="" name="province" id="" style="width:250px;"/></dd>
				</dl>-->
				<dl class="WSY_member">
                    <dt>省市区</dt>
                    <dd>
                         <select class="selectArea" name="location_p" id="location_p" style="width: 120px;"></select>
                         <select class="selectArea" name="location_c" id="location_c" style="width: 120px;"></select>
                         <select class="selectArea" name="location_a" id="location_a" style="width: 120px;"></select>
                    </dd>
                </dl>

				<dl class="WSY_member">
					<dt>详细地址</dt>
					<dd><input type=text tips_name="详细地址" name="business_address" id="business_address" style="width:250px;" onkeyup="count_size(this,50);" onblur="count_size(this,50)"  value="<?php echo $business_address; ?>"/></dd>
					<dd>长度为1-50位字符</dd>
				</dl>
				<script src="js/region_select.js" type="text/javascript" charset="utf-8">
				    
				</script>
				<script type="text/javascript" >
						if(<?php echo $keyid; ?>==-1){
						}

						if(<?php echo $keyid; ?>!=1){
							new PCAS('location_p', 'location_c', 'location_a', '<?php echo $location_p; ?>', '<?php echo $location_c;?>', '<?php echo $location_a;?>');
						}				
				</script>
			<?php } ?>


			<?php if($supply_must[2]==1){ ?>
				<dl class="WSY_member">
					<dt>身份证号</dt>
					<dd><input type=text tips_name="身份证号" name="idcard_num" id="idcard_num" style="width:250px;" onkeyup="count_size(this,20);" onblur="count_size(this,20)" value="<?php echo $id_cards_num ?>" oninput="value=value.replace(/[^\d]/g,'')"/></dd>
					<dd>长度为1-20位数字</dd>
				</dl>
			<?php } ?>

			<?php if($supply_must[3]==1){ ?>
				<dl class="WSY_member">
					<dt>公司名称</dt>
					<dd><input type=text tips_name="公司名称" name="company_name" id="company_name" style="width:250px;" onkeyup="count_size(this,20);" onblur="count_size(this,20)" value="<?php echo $company_name ?>"/></dd>
					<dd>长度为1-20位字符</dd>
				</dl>
			<?php } ?>


			<!-- 加入图片是否为空的判断 -->
			<?php if($supply_must[4]==1){ ?> 
				<dl class="WSY_member">
					<dt>身份证正反两面</dt>
				</dl>
				<dl class="WSY_member" style="margin: 0 60px;display: inline-block;">
					<div>
					    <div class="uploadimg-main2" id="uploadidcard" style="display: inline-block;" data-width="100" data-height="100">
						<?php  if($keyid==-1) { ?>
							<div class="uploadimg-box" data-src="">
                                <input class="file-main" type="file" name="idcard_pic[]" value="">
                                <div class="del-upload">删除</div>
                                <div class="img-default"></div>
                                <div class="img-box" style="width: 100px; height: 100px;"><img class="img-show" src=""></div>
                            </div>
						<?php }else{
							if($id_cards_pic[count($id_cards_pic)-1]==''){
								$length = count($id_cards_pic)-1;
							}else{
								$length = count($id_cards_pic);
							}
							foreach ($id_cards_pic as $value){
								if($value!=''){
								?>
								<div class="uploadimg-box isload" data-src="" style="width: 100px; height: 100px;">
	                            	<input class="file-main" type="file" name="idcard_pic[]">
	                            	<input type="hidden" name="idcard_pic_ex[]" value="<?php echo $value; ?>">
	                            	<div class="del-upload" style="display: block;">删除</div>
	                            	<div class="img-default" style="display: none;"></div>
	                                <div class="img-box" style="width: 100px; height: 100px;">
	                                    <img class="img-show" src="<?php echo $value; ?>" style="opacity: 1;">
	                                </div>
	                            </div>
							<?php } }
							if ($length<2) { ?>
							<div class="uploadimg-box" data-src="">
                                <input class="file-main" type="file" name="idcard_pic[]" value="">
                                <div class="del-upload">删除</div>
                                <div class="img-default"></div>
                                <div class="img-box" style="width: 100px; height: 100px;"><img class="img-show" src=""></div>
                            </div>
						<?php  
							 }
				 			} ?>
                        </div>
					</div>
				</dl>

				<dl class="WSY_member">
                    <dt></dt>
                    <p style="color:red;margin-left: 40px;">图片大小不得超过500K，上传格式：JPG、GIF、BMP、PNG</p>
            	</dl>
			<?php } ?>


			<?php if($supply_must[5]==1){ ?>
				<dl class="WSY_member">
					<dt>公司营业执照</dt>
				</dl>
				<dl class="WSY_member" style="margin: 0 60px;">
					<div>
					    <div class="uploadimg-main" id="uploadbusiness" style="display: inline-block;" data-width="100" data-height="100">
					    <?php if($keyid==-1) { ?>
	                            <div class="uploadimg-box" data-src="">
	                                <input class="file-main" type="file" name="business_licence_pic[]" value="">
	                                <div class="del-upload">删除</div>
	                                <div class="img-default"></div>
	                                <div class="img-box" style="width: 100px; height: 100px;"><img class="img-show" src=""></div>
	                            </div>
						<?php }else{
							if($business_licence_pic[count($business_licence_pic)-1]==''){
								$length = count($business_licence_pic)-1;
							}else{
								$length = count($business_licence_pic);
							}
							foreach ($business_licence_pic as $value){
								if($value!=''){
								?>
                            <div class="uploadimg-box isload" data-src="" style="width: 100px; height: 100px;">
                            	<input class="file-main" type="file" name="business_licence_pic[]">
                            	<input type="hidden" name="business_licence_pic_ex[]" value="<?php echo $value; ?>">
                            	<div class="del-upload" style="display: block;">删除</div>
                            	<div class="img-default" style="display: none;"></div>
                                <div class="img-box" style="width: 100px; height: 100px;">
                                    <img class="img-show" src="<?php echo $value; ?>" style="opacity: 1;">
                                </div>
                            </div>
							<?php } }
							if ($length<3) { ?>
	                            <div class="uploadimg-box" data-src="">
	                                <input class="file-main" type="file" name="business_licence_pic[]" value="">
	                                <div class="del-upload">删除</div>
	                                <div class="img-default"></div>
	                                <div class="img-box" style="width: 100px; height: 100px;"><img class="img-show" src=""></div>
	                            </div>
							<?php  
							 }
					 } ?>                        
						</div>
					</div>
				</dl>



				<dl class="WSY_member">
                    <dt></dt>
                    <p style="color:red;margin-left: 40px;">图片大小不得超过500K，上传格式：JPG、GIF、BMP、PNG</p>
                </dl>
			<?php } ?>
				<div class="button_box">
					<input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;float:left;margin: 30px 52px;"/>
					<input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;float:left"/>
				</div>
			</div>
	
		</div>
		
	</div>
 </form>
</div>	
<!--内容框架结束-->
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>

<!--地址-->
<script src="js/region_select.js" type="text/javascript" charset="utf-8"></script>
<!--地址-->
<!--上传图片js-->
<!-- <script src="js/jquery-uploadImg.js" type="text/javascript" charset="utf-8"></script> -->
<script src="js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="js/bootbox.min.js" type="text/javascript" charset="utf-8"></script>
<!--上传图片js-->
<script type="text/javascript">



	(function($){
        $.fn.UpLoadImg = function(opt){
            var that = $(this);
            var defaults = {
                type:/image\/\w+/,
                max:1,
                maxsize:500*1024,
                file_name : opt.name, //增加文件上传默认name属性
                width:$(that).data('width') ? $(that).data('width'):120,
                height:$(that).data('height') ? $(that).data('height'):120,
                success:function(file){},
                delete:function(obj){}
            }
            var opt  = $.extend(defaults,opt);

            var str = '<div class="uploadimg-box" style="width: '+opt.width+'px; height: '+opt.height+'px;">';
            str += '<input class="file-main" type="file" multiple="multiple" name="'+opt.file_name+'"><div class="del-upload">删除</div>';
            str += '<div class="img-default"></div>';
            str += '<div class="img-box" style="width: '+opt.width+'px; height: '+opt.height+'px;"><img class="img-show"></div>';
            str += '</div>';

            var html = '<div class="del-upload">删除</div>';
            html += '<div class="img-default"></div>';
            html += '<div class="img-box" style="width: '+opt.width+'px; height: '+opt.height+'px;"><img class="img-show"></div>';

            that.find('.uploadimg-box').css({width:opt.width,height:opt.height});
            // that.find('.uploadimg-box').append(html);

            that.find('.uploadimg-box').each(function(){
                var data_src = $(this).data('src');

                if(data_src){

                    $("#is_load_logo").val('1')
                    $(this).find('.del-upload').show();
                    $(this).find('.file-main').hide();
                    $(this).find('.img-default').hide();
                    $(this).find('.img-show').attr('src',data_src).css('opacity',1);
                    $(this).addClass('isload');
                }
            })

            this.each(function(){
            	var int = 0;
                that.on('change',':file',function(e){
                    var file = this.files[0];
                    if(!file){
                        return false;
                    }
                    var file_box = $(this).parent();
                    if(!opt.type.test(file.type)){
                        alert('上传文件格式错误，请重新上传！')
                        this.value = '';
                        return false;
                    }
                    if(file.size > opt.maxsize){
                        bootbox.alert({
                            title:'提示',
                            message:"上传文件过大，请重新上传！",
                            buttons: {
                                ok: {  
                                    label: '确认',
                                }  
                            }
                        });
                        this.value = '';
                        return false;
                    }
                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function(e) {
                        result = e.target.result;
                        file_box.find('.img-show').attr('src',result).css('opacity',1);
                        file_box.find('.del-upload').show();
                        file_box.find('.file-main').hide();
                        file_box.find('.img-default').hide();
                        file_box.addClass('isload');
                        opt.success(file);
                        if(that.find('.uploadimg-box').length < opt.max && file_box.next().length < 1){
                            that.append(str);
                        }else{
                            return false;
                        }
                    };
                });

                that.on('click','.del-upload',function(){
                    var del = $(this).parent();
                    if(that.attr('division') == 'logo'){
                        $("#del_logo").val('yes');
                        $("#is_load_logo").val('2');
                    }

                    if(that.attr('division') == 'start_logo'){
                        $("#del_index"+del.index()).val('yes');
                    }
                    var as = del.attr('del_id');

                    if(that.find('.uploadimg-box').length > 1){
                        del.remove();
                        if(that.find('.uploadimg-box').last().hasClass('isload')){
                            that.append(str);
                        }
                    }else{

                        del.find('.img-show').attr('src','').css('opacity','0');
                        del.find('.del-upload').hide();
                        del.find('.img-default').show();
                        del.find('.file-main').val('');
                        del.find('.file-main').change();
                    }
                    opt.delete(del);
                })
            })
        }
    })(jQuery)
		$(function(){
		    
		    $('.uploadimg-main').UpLoadImg({
	            type: /image\/\w+/, //图片格式
	            max: 3, //图片张数，最少1
	            name:'business_licence_pic[]'
	        });
	        $('.uploadimg-main2').UpLoadImg({
	            type: /image\/\w+/, //图片格式
	            max: 2, //图片张数，最少1
	            name:'idcard_pic[]'
	        });
		})


	function count_size(obj,max) {
	    obj.value = $.trim(obj.value);
	    obj.value = obj.value.replace(/\'/g, "");
	    obj.value = obj.value.replace(/</g, "");
	    obj.value = obj.value.replace(/>/g, "");
	    obj.value = obj.value.replace(/\//g, "");
	    let val = obj.value;
	    let len = val.length;
	    if(len >= max){
	        len = max;
	        obj.value = obj.value.substr(0,max);
	    }
	}	

	//搜索用户
	function searchForm(){
		var promoters_id=$('#promoters_id').val();
		var promoters_name=$('#promoters_name').val();
        $.ajax({
            type: "post",
            url: "./shop_supply_user_save.php",
            dataType: "json",
            data: {'promoters_id': promoters_id,'promoters_name':promoters_name,'is_search':true},
            success: function (result){
                if(result.errcode=="0"){
                	$('#userName').empty();
                	var info = result.info;
                	for (index in info) {
                		$('#userName').append('<option  value="'+info[index].user_id+'">'+info[index].name+'</option>');

                		if(index==0){
                			var name=info[index].name.split(":");
                		  $('#username').val(name[0]);
                		  $('#username').attr('readonly','readonly');
                		  $('#check_uid').val(info[index].user_id);
                		}
					}
                }else{
                	alert(result.msg);
                	$('#userName').empty();
                	$('#userName').append('<option value="-1">请选择一个上线</option>');
                	$('#username').val('');
                	$('#username').removeAttr('readonly');
                }
            }
        });
	}

	//自动填充用户名
	function addValue(obj){
	  var name=$(obj).find("option:selected").text();
	  name=name.split(":");
	  var id=$(obj).find("option:selected").val();
	  $('#username').attr('readonly','readonly');
	  $('#username').val(name[0]);
      $('#check_uid').val(id);
	}


</script>
</body>

<?php mysql_close($link);?>	
</html>