<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>彩铃管理</title>
<link rel="stylesheet" type="text/css" href="../../../../weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../../mshop/admin/Common/css/Product/product.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/js/percent/jquery.percentageloader.0.2.css">
<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script>
<script type="text/javascript" src="/weixinpl/js/ajaxfileupload.js"></script>
<link rel="stylesheet" type="text/css" href="/mshop/web/static/css/webuploader.css" />
<script type="text/javascript" src="/mshop/web/static/js/TouchSlide.1.1.js"></script>
<script type="text/javascript" src="/mshop/web/static/js/global.js"></script>
<meta http-equiv="content-phone" content="text/html;charset=UTF-8">
<script>
    layer.config({
        extend: '/extend/layer.ext.js'
    });
</script>
<style>

		/*<!-- 导出字段 -->*/
		.floatbox{position: absolute;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
		.floatbox .tishitext{margin-bottom: 4px;}
		.floatbox .checkboxsdiv{border: 1px solid #888888;padding: 8px;width: 200px;background-color: #ffffff;}
		.checkboxsdiv input,.quanbuxuan input{display: inline-block;}
		.checkboxsdiv p,.quanbuxuan p{display: inline-block;white-space: nowrap;overflow: hidden;max-width: 181px;margin-left: 5px;}
		.floatbox .floatinputs{width: 60px;height: 27px;border-radius: 6px;background-color: #2eade8;cursor: pointer;color: #ffffff;display: inline-block;margin-top: 15px;margin-left: 16px;margin-right: 10px;}
		.floatbox .floatinputc{width: 60px;height: 27px;color: #ffffff;background-color: #aaaaaa;cursor: pointer;border-radius: 6px;display: inline-block;margin-top: 15px;}
		.quanbuxuan{display: inline-block;padding: 5px 0 0 10px;vertical-align: middle;margin-top: 15px;}
		.subdivb{display: inline-block;vertical-align: middle;}
		/*<!-- 导出字段 End -->*/
		#WSY_q1 .td{text-align:center;}	
</style>
</head> 
<body>
<div class="WSY_content" id="WSY_content_height">

       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
		<div class="WSY_column_header"> 
                <?php 
					$head = 1;
					include("cailing_head.php");
				?>
			</div>
		<div class="WSY_data">
	    	<div class="WSY_agentsbox"> 	
				<form class="search" style="display:block" method="get" action="/mshop/admin/index.php?m=cailing&a=color_bell_management" >
					<input type="hidden" name="m" value="cailing">
					<input type="hidden" name="a" value="color_bell_management">
					<!-- <input type="hidden" name="customer_id" value="<?php echo $result['id']; ?>"> -->
					<input type="hidden" name="id" value="<?php echo $p_id; ?>">
					<div class="WSY_search_q">
						<div class="WSY_search_div">
			                <li>彩铃名称：<input type="text" id="name" name="name" value="<?php echo $data['name']; ?>"/></li>
			                <li>状态筛选：
			                	<select id="issale" name="issale">
		                			<option value="2" <?php if($_GET['issale'] == '2') echo 'selected="selected"'; ?>>全部</option>
		                			<option value="1" <?php if($_GET['issale'] == '1') echo 'selected="selected"'; ?>>上架</option>
		                			<option value="0" <?php if($_GET['issale'] == '0') echo 'selected="selected"'; ?>>下架</option>
			                	</select>
			                </li>

			                <ul>
								<li class="WSY_bottonliss"><input type="button" onclick="button_cl()" value="搜索"></li>
								<li class="WSY_bottonliss left" ><input type="button" style="width:100px" id="btn_check_store" value="批量删除" onclick="delete_ids();"></li>
								<li class="WSY_bottonliss left" ><a class="WSY-skin-bg form-btn form-add-btn" onclick="show_div_export();"><input type="button" style="width:100px" id="btn_export" value="导出"></a></li>
								<a href="/mshop/admin/index.php?m=cailing&a=color_bell_static"><li class="WSY_bottonliss left" ><input type="button" style="width:100px" class="mul_property" data-action="add" value="添加彩铃"></li></a>  
							</ul>
						</div>
		          </div>
		        </form>

		        <table width="97%" class="WSY_table" id="WSY_t1">

				  <thead class="WSY_table_header">
					<th width="3%">
						<!-- <input id="ck_all"  type="checkbox" name="input_checkbox" onclick="change_box()"> -->
						 <input type="checkbox" name="all_checkbox" class="all_checkbox" id="ck_all" >
					</th>
					<th width="5%">ID</th>
					<th width="15%">彩铃名称</th>
					<th width="13%">彩铃图片</th>
					<th width="12%">彩铃标签</th>
					<th width="7%">彩铃价格</th>
					<th width="4%">状态</th>
					<th width="8%">操作</th>
				  </thead>
			<tbody>


				<?php
			foreach($res_select as $v){
				$p_id              = $v['id'];
				$p_name 		   = $v['name'];
				$p_price           = $v['price'];
				$p_tip             = $v['tip'];
				$p_imgurl          = $v['img_url'];
				$p_issale          = $v['issale'];
				$p_sort            = $v['sort'];
				$p_createtime      = $v['createtime'];
				$p_isvalid         = $v['isvalid'];

				$v         		   = array();
				$c_data_json 	   = json_encode($v); //将数组json格式化，方便传参
				// print_r($p_id);
		       ?>

				<tr id="WSY_q1">
					<td class="td">
						<!-- <input type="checkbox" name="input_checkbox" value="<?php echo $p_id; ?>"> td .WSY_fixed -->
						<input type="checkbox" name="input_checkbox[]" id="input_checkbox" class="checkbox" value="<?php echo $p_id;?>"  />
					</td>
					<td id="id" class="td">
						<?php echo $p_id;?>
					</td>
					<td class="td">
						<?php echo $p_name;?>
					</td>
					<td class="td">
						 <div style="width: 45%;margin: 0 auto;"><img src="<?php echo $p_imgurl; ?>" class="WSY_fixed" /> </div>  <!-- 输出照片 -->
					</td>	
					<td class="td">
						<?php  echo $p_tip; ?>
					</td>
					<td class="td">	
						<?php  echo $p_price; ?>	
					</td>
					<td class="td">
						<?php if ($p_issale==0){echo "下架";}else{echo "上架";} ?>
					</td>
					<td class="WSY_t4 td" id="WSY_t4">
						
						<a href="/mshop/admin/index.php?m=cailing&a=color_ring_editor&customer_id=<?php echo $customer_id; ?>&id=<?php echo $p_id; ?>&name=<?php echo $name; ?>" title="修改">编辑</a>
						<a href="javascript:;" class="del-btn" data-pid="<?php echo $p_id;?>" title="删除"  onclick="del_color_bell( ' <?php echo $p_id;?>');">删除 </a>
						<!-- <button onclick="del_color_bell( ' <?php echo $p_id;?>');">删除</button> -->
						<?php
						if( $p_issale==1){ ?>
							<a  href="#" onclick="change_isout('<?php echo $p_id; ?>',1)">下架</a>
						<?php }?>
						<?php if ($p_issale==0) { ?>
							<a href="#" onclick="change_isout('<?php echo $p_id; ?>',2)">上架</a>
						<?php }?>
						
					</td>
				</tr>

				<?php } ?> <!-- 循环 -->




				</tbody>
           		</table>
		    </div>   <!-- 第四个div -->

		    	<!-- 导出字段选择 -->
			<div class="floatbox">
				<p class="tishitext">导出字段选择</p>
				<div class="checkboxsdiv">
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="id"><p>彩铃ID</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="name"><p>彩铃名称</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="img_url"><p>彩铃图片</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="tip"><p>彩铃标签</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="price"><p>彩铃价格</p></div>				
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="issale"><p>是否上架</p></div>
				</div>
				<div class="quanbuxuan">
					<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
				</div>
				<div class="subdivb">
					<input type="submit" class="floatinputs" value="确定">
					<input type="submit" class="floatinputc" value="取消">
				</div>
			</div>
			<!-- 导出字段选择 End -->

			<!--翻页开始-->
		    <div class="WSY_page">
		    	<ul class="WSY_pageleft" style="width: 70%;">
		    		<?php
		    			$html = '';
		    			$total_number = $res_count['count(id)'];//总条数
		    			$page_num = ceil($total_number / 20);//总页数
		    			$page = $_GET['page']?$_GET['page']:1;//页数传过的值
		    		
		    			if ($page > $page_num) {
		    				$page = $page_num;
		    			}

		    			if ($page < 1) {
		    				$page = 1;
		    			}

		    			$right = 3; //右边显示的页数
		    			$left  = 2; //左边显示的页数

		    			if ($page >= 5) {
			    			$html .= '<li class="tcdNumber" onclick="button_cl(1)">1</li>';
			    			$html .= '<span>...</span>';
			    			$right = $right - 1;
			    		}

		    			$val = $page - $right;
						if ($val < 1) $val = 1;
						for ($i = $val; $i < $page; $i++) {
							if ($i == $page) {
								$html .= '<li class="one" onclick="button_cl('.$i.')">'.$i.'</li>';
							}else{
								$html .= '<li class="tcdNumber" onclick="button_cl('.$i.')">'.$i.'</li>';
							}
						}
						
						if ($page > $page_num-4) {
			    			$left = $left + 1;
			    		}

		    			$val = $page + $left;
						if ($val > $page_num) $val = $page_num;

			    		for($i = $page; $i <= $val; $i++){ 
			    			if ($i == $page) {
			    				$html .= '<li class="one" onclick="button_cl('.$i.')">'.$i.'</li>';
			    			}else{
			    				$html .= '<li class="tcdNumber" onclick="button_cl('.$i.')">'.$i.'</li>';
			    			} 
			    		}

			    		if ($page <= $page_num-4) {
			    			$html .= '<span>...</span>';
			    			$html .= '<li class="tcdNumber" onclick="button_cl('.$page_num.')">'.$page_num.'</li>';
			    		}

			    		echo $html;
			    		?> 

		    		<div class="WSY_searchbox">
		    			<input class="WSY_page_search" name="WSY_jump_page" id="WSY_jump_page" value=""><input class="WSY_jump" type="button" value="跳转" onclick="button_cl('tz')">
		    		</div>
		    	</ul>
		    </div>
		    <!--翻页结束-->
		</div>
	</div>
</div>
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

<script type="text/javascript">
	//分页加搜索开始
	function button_cl(page=''){
		var url = "/mshop/admin/index.php?m=cailing&a=color_bell_management&customer_id=<?php echo $customer_id_en; ?>&total=<?php echo $page_num; ?>";
		var name = $("#name").val();
		var issale = $("#issale").val();
		if (name != '') {
			url += '&name='+name;
		}
		if (issale != '' && issale != 2) {
			url += '&issale='+issale;
		}
		if (page != '' && page != 'tz') {
			url += '&page='+page;
		}
		if (page == 'tz') {
			page = $('#WSY_jump_page').val();
			url += '&page='+page;
		}
		window.location.href = url;
	}

</script>

<script type="text/javascript">
	//导出处理START
    function show_div_export(){
      $(".floatbox").toggle();
    }


    $(".floatinputc").click(function(){
            $(".floatbox").hide();
        });
    $(".floatinputs").click(function(){
    var str='';
    var excludes = '';
    $("input[name='excel_field[]']").each(function(){
        if($(this).attr("checked")){
            str += $(this).val()+',';
        }else{
            excludes += ','+$(this).val();
        }
    })
    
    
    if(str == '')
    {
        alert('至少选中一项');
        return;
    }
    console.log(str);
    $(".floatbox").hide();
    exportRecord(str,excludes);

});
    //点击复选框，判定是否全选
    $('.checkboxsdiv input').click(function(){
    	var num = 0;
    	$("input[name='excel_field[]']").each(function(){
	        if($(this).attr("checked")){
	        	num++;
	        }
    	});
    	if( num >= 10 )
    	{
    		$('#allselects').attr('checked', true);
    	}
    	else
    	{
    		$('#allselects').attr('checked',false);
    	}
    })
    // 全选
    $("#allselects").click(function(){
        if(this.checked){
            $(".checkboxsdiv :checkbox").attr("checked", true);
        }else{
            $(".checkboxsdiv :checkbox").attr("checked",false);
        }
    }); 


    //导出方法
    function exportRecord(str,excludes){
        var title = $("title").text();  // 设置表头
        var filename = $(".title_log").text(); //设置默认文件名
        if (excludes == '') {
            excludes = -1;
        }
        $(".floatbox").css('z-index','999');
        $(".floatbox").show();

        var name = 'color_bell_list';
        var op = 'iscount';
        var customer_id = '<?php echo $customer_id; ?>';
        var condition = {
        	'cailing_issale'   : "<?php echo $cailing_issale; ?>",
        	'cailing_name' 	   : "<?php echo $cailing_name; ?>",
        };

        $.ajax({type:'post', async:false, url:'/weixinpl/common/explore/color_bell_list.php',data:{fields:str,function_name:name,param_json:excludes,customer_id:customer_id,op:op,condition:condition},
            success:function(data)
            {
                console.log(data);
                var res = JSON.parse(data);

                if(res.status == 2)
                {
                    layer.msg(res.msg);
                    return;
                }
                
                var eamil_arr     = res.emails.split('#*#');
                var eamil_address = "";
                var type          = 2;
                var op            = 'add_email';
                var tips          = "导出数据已打包发送到您的邮箱，请注意查收";
                
                if(eamil_arr.length>0)
                {
                    // eamil_address = eamil_arr[0];//不显示默认邮箱2018/3/27
                }

                if(res.errcode == 10003)
                {
                    layer.msg(res.errmsg);
                    return;
                }
                else
                {
                    type = 2;
                    tips = "请留意您的邮箱，导出完成后会发到你的邮箱上！";
                    layer.prompt({title: '请输入您邮箱地址',value:eamil_address, formType: 0}, function(email, prompt){
                        layer.close(prompt);
                        if (checkEmail(email)){
                            emails    = email;
                            $.ajax({type:'post', url:'/weixinpl/common/explore/color_bell_list.php', data:{fields:str,function_name:name,param_json:excludes,customer_id:customer_id,email:emails,op:op,type:type,condition:condition},
                                success:function(data){
                                    console.log(data);
                                    var res           = JSON.parse(data);
                                    if(res.status == 2)
                                    {
                                        layer.msg(res.msg);
                                        return;
                                    }
                                    $.post('/weixinpl/common/explore/color_bell_list.php',{'debug':1,condition:condition},function(da){},'json');
                                    layer.msg(tips);
                                }
                            });

                        }
                        else
                        {
                            layer.msg("邮箱地址填写有误，请填写正确的邮箱地址");
                            return;
                        }
                    })
                }

            }

        });
        $(".floatbox").hide();
    } 
     /*校验邮箱地址*/
    function checkEmail(str){
        var re= /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        return re.test(str);
    }    
</script>

<script type="text/javascript">

	
////批量删除
function delete_ids(){
	var del_arr = '';
	var arr = [];
	var remark = "";
	$("input[name='input_checkbox[]']:checkbox").each(function(i){
		if($(this).is(':checked')){
			arr.push($(this).val());
			del_arr += $(this).val()+",";
		}
	});
	remark = "删除后不可恢复，继续吗";
	if(del_arr.length == 0){
		 alert("请选择需要删除的产品！");
		return false;
	}else{  
		layer.confirm(remark, {
			title:'警告',
			btn: ['确认','取消']
		}, function(confirm){
			layer.close(confirm);	
			$.ajax({
				url: '/mshop/admin/index.php?m=cailing&a=del_cailing',
				type:"get",
				dateType:"json",
				data:{del_cailing:"del_cailing",delete_ids:del_arr},
				success:function(result){
						var res = eval('(' + result + ')');
						console.log(res);

					if(res.errcode=="0"){
						// alert(res.errmsg);
						layer.confirm(res.errmsg, {
							title:'提示',
							btn: ['确认','取消']},function(confirm){history.go(0); return false;},function(){});
						// history.go(0);
						// return false;
					}else{
						alert("删除失败");
						return false;
					}
				}
			});
		}, function(){
		});
    }
}
// 点击复选框，判定是否全选
    $('.td input').click(function(){
    	var num = 0;
    	$("input[name='input_checkbox[]']").each(function(){
	        if($(this).attr("checked")){
	        	num++;
	        }
    	});
    	if( num >= 10 )
    	{
    		$('#ck_all').attr('checked', true);
    	}
    	else
    	{
    		$('#ck_all').attr('checked',false);
    	}
    })
    // 全选
    $("#ck_all").click(function(){
        if(this.checked){
            $(".td :checkbox").attr("checked", true);
        }else{
            $(".td :checkbox").attr("checked",false);
        }
    }); 
	 

var id = $("#id").val();
//單獨删除
function  del_color_bell(id,type){
	   // alert(id);
			//type：1发布 2删除 3终止
		var url = "";
		var remark = "";
		url = '/mshop/admin/index.php?m=cailing&a=del_cailing_shopkeepers';
		remark = "删除后不可恢复，继续吗";

		layer.confirm(remark, {
			title:'警告',
			btn: ['确认','取消']
		}, function(confirm){
			layer.close(confirm);	
			$.ajax({
				url: url,
				dataType: 'json',
				type: 'get',
				data: {
					id:id,
				},
				success: function(res){		
					console.log(res);
					if( res.errcode == 1 ){
						alert(res.errmsg);
						document.location.reload();
					}else{
						alert(res.errmsg);
					}
				}
			});
		}, function(){

		});
	}
//單獨删除结束

//上下架处理
 function change_isout(id,type){
        //type类型 1.下架商品 2.上架商品
        var remark = "";
        if(type==1){

            remark="您确定要下架商品吗？";
            layer.confirm(remark, {
                title:'警告',
                btn: ['确认','取消']
            }, function(confirm){
                layer.close(confirm);

                    $.ajax({
                    url: '/mshop/admin/index.php?m=cailing&a=change_isout_get',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        id:id,
                        is_ajax:1,
                        type_out:1,
                    },
                    success: function(res){
                        if( res.errcode == '1' ){
                            layer.alert(res.errmsg);
                            // console.log(res.sql);
                            // console.log(res.type_out);
                            setTimeout("location.reload()",2000);
                            // location.reload();
                        }else{
                            layer.alert(res.errmsg);
                        }
                    }
                });
            });

        }else if(type==2){
 
	 	 remark="您确定要上架商品吗？";
	            layer.confirm(remark, {
	                title:'警告',
	                btn: ['确认','取消']
	            }, function(confirm){
	                layer.close(confirm);

	                    $.ajax({
	                    url: '/mshop/admin/index.php?m=cailing&a=change_isout_get',
	                    dataType: 'json',
	                    type: 'post',
	                    data: {
	                        id:id,
	                        is_ajax:1,
	                        type_out:2,
	                    },
	                    success: function(res){
	                        if( res.errcode == '1' ){
	                            layer.alert(res.errmsg);
	                            // console.log(res.sql);
	                            // console.log(res.type_out);
	                            setTimeout("location.reload()",2000);
	                            // location.reload();
	                        }else{
	                            layer.alert(res.errmsg);
	                        }
	                    }
	                });
	            });
	          

        }
    }


</script>




</body>
</html>