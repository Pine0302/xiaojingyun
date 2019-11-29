<?php 

require_once ('shoproom.php');
$pc = new Pcshop ();
$http = $pc->http;
$theme = $pc->get_theme();
$customer_id_en = $pc->customer_id_en;

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<title>会员卡次</title>
		<link rel="stylesheet" href="<?php echo $pc->http;?>/weixinpl/common/css_V6.0/content.css"/>
		<link rel="stylesheet" href="<?php echo $pc->http;?>/weixinpl/common/css_V6.0/<?php echo $theme;?>.css"/>
		<link rel="stylesheet" href="static/css/xincommon.css" />
		<style type="text/css">
			.WSY_content{
				width: 100%;
				/*background: #f4f4f4;*/
				padding-bottom: 20px;
			}
			.WSY_content .WSY_button{
				float: none;
				margin-left: 2%;
				margin-bottom: 20px;
			}
			tbody tr img{
				display: inline-block;
				width: 20px;
				cursor:pointer 
			}
			tbody tr td span{
				height: 20px;
				line-height: 20px;
				width: 40px;
				display: inline-block;
				background: #0099FF;
				left: 40px;
				color: #FFFFFF;
				vertical-align: 6px;
				cursor: pointer;
			}
			/*.typeclass_x:active{
				color:rgb(81,31,144);
			}*/
			.typeclass_x{
				cursor:pointer;
                color:#2e6da4;
			}
			.WSY_table{
				margin-left: 2%;
			}
			.WSY_pageleft li{
				float: none;
				display: inline-block;
			}
			.WSY_pageleft{
				text-align: left;
			}
		</style>
	</head>
	<body>
		<div class="WSY_columnbox">
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a href="<{:U('mini/card_ticket_management')}>?customer_id_en=<{$customer_id_en}>" >会员卡</a>
				</div>
				<div class="WSY_columnnav">
					<a href="<{:U('mini/coupon')}>?customer_id_en=<{$customer_id_en}>" >优惠券</a>
				</div>
				<div class="WSY_columnnav">
					<a href="<{:U('mini/number_card')}>?customer_id_en=<{$customer_id_en}>" class="white1">会员次卡</a>
				</div>
			</div>
			<!---->
			<div class="WSY_content">
				<input type="button" value="+添加" class="WSY_button" onclick="jumpto_addNumbercard();">
				<div class="WSY_columnbox_table">
					<!--表格开始-->
					<table width="96%" class="WSY_table" id="WSY_t1">
						<thead class="WSY_table_header">
						<tr>
							<th width="20%">会员次卡名称</th>
							<th width="40%">有效期</th>
							<th width="40%">已售数量</th>
							<th width="40%">剩余数量</th>
							<th width="40%">状态</th>
							<th width="40%">操作</th>
						</tr>
						</thead>
						<tbody>
							<volist name="ncdata" id="ve" key="key" >
								<tr>
									<td><{$ve['name']}></td>
									<td><{$ve['valid_time']}></td>
									<td><{$ve['sended']}></td>
									<td><{$ve['remainder']}></td>
									<td>
										<if condition="$ve['is_enabled'] eq 1">上架
										<else/>下架
										</if>
									</td>
									<td>
										<a class="typeclass_x" title='编辑' onclick="jumpto_editnumbercard(<{$ve['id']}>,<{$key}>)">编辑</a>
									    <if condition="$ve['is_enabled'] eq 1">
									    <a class="typeclass_x" title='下架' onclick="putaway(<{$ve['id']}>,<{$key}>,2)">下架</a>
									    <else />
									    <a class="typeclass_x" title='上架' onclick="putaway(<{$ve['id']}>,<{$key}>,1)">上架</a>
									    </if>
									    <a class="typeclass_x" title='删除' onclick="delete_data(<{$ve['id']}>,<{$key}>)">删除</a>
									    <!--
										<img src="__PUBLIC__/ass/img/shanchu.png" title='删除' onclick="delete_data(<{$ve['id']}>,<{$key}>)"/>
										<img src="__PUBLIC__/ass/img/shang.png" title='上架' onclick="putaway(<{$ve['id']}>,<{$key}>)"/>
										<img src="__PUBLIC__/ass/img/bianji.png" title='编辑' onclick="jumpto_editnumbercard(<{$ve['id']}>,<{$key}>)"/>
										-->
									</td>
								</tr>
							</volist>
						</tbody>
					</table>
					<!--翻页开始-->
				    <div class="WSY_page">
				    	<ul class="WSY_pageleft" style="width:100%;margin-left:16px;"><{$strPage}>
			        		<!--
			        		<div class="WSY_searchbox">
			        			<input class="WSY_page_search" id="WSY_jump_page"/>
			        			<input class="WSY_jump" type="button" value="跳转"/>
			        		</div>
			        		-->
			        	</ul>
				    </div>
				    <!--翻页结束-->
				</div>
			</div>
		</div>	
	</body>
	<script type="text/javascript" src="__PUBLIC__/ass/js/jquery-1.12.1.min.js" ></script>
	<script type="text/javascript" src="<{$http}>/weixinpl/js/WdatePicker.js"></script>
	<script type="text/javascript">
	    //添加次卡
	    function jumpto_addNumbercard(){
	    	location.href = "<{:U('mini/add_number_card')}>?customer_id_en=<{$customer_id_en}>";
	    }
		//编辑次卡
	    function jumpto_editnumbercard(id,key){
	    	if(!id){return;}
	    	location.href="<{:U('mini/edit_number_card')}>?customer_id_en=<{$customer_id_en}>&id="+id;
	    }      
        //删除次卡
	    function delete_data(id,key){
	    	if(!id){return;}
	    	if(confirm("确定要删除吗?")){
	    		delete_action(id,key);
	    	}else{void(0);}
	    }
	    function delete_action(id,key){
	    	if(!id){return;}
	    	$("tbody tr").eq(key-1).css("display","none");
	    	$.ajax({
	    		url:"<{:U('mini/del_minicard')}>?customer_id_en=<{$customer_id_en}>",
	    		data:{id:id},
	    		type:'get',
	    		dataType:'json',
	    		async:false,
	    		success:function(res){
	    			if(res.err_code!=1){
	    				alert(res.err_data);
	    			}else{
	                    location.reload();
	    			}
	    		}
	    	});
	    }
	    //状态切换
	    function putaway(id,key,data_id){
	    	if(!id){return;}
	    	location.href = "<{:U('mini/enable_status_nccard')}>?customer_id_en=<{$customer_id_en}>&id="+id+'&type='+data_id;
	    }
	    function pagehref(obj){
			var attr_page = $(obj).attr('page');
			var attr_condition = $(obj).attr('condition');
			location.href = '?page='+attr_page+attr_condition;
		}
	</script>		
</html>