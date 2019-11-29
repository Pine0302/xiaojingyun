<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=4;//头部文件---限时广告图管理
$pagenum = 1;
if(!empty($_GET["pagenum"])){
    $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * 20;
$end = 20;

//查询是否有上架的广告图，有的话查出其id
$ad_up_id = 0;            //无上架为0， 有上架则为上架id
$ad_up_query = "select id from weixin_commonshop_ads where isvalid=1 and status=1 and customer_id=".$customer_id." limit 0,1" ;
$ad_up_result = _mysql_query($ad_up_query) or die('ad_up_query Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($ad_up_result)) {
    $ad_up_id        = $row->id;				//上架广告的id
}
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title>限时广告图管理</title>
	<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/limit_ad.css">
	<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
	<?php
		include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/basicdesign/basic_head.php");
	?>
	<div class="add-ad-btn">
		<a href="limit_ad_edit.php?" class="add_limit_ad">添加广告图</a>
	</div>
	<table width="97%" class="WSY_table" id="WSY_t1">
		<thead class="WSY_table_header">
			<tr>
				<th width="5%">序号</th>
				<th width="10%">名称</th>
				<th width="8%">模式</th>
				<th width="20%">显示时间</th>
				<th width="10%">状态</th>
				<th width="15%">创建时间</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
            <?php
            $query = "select id,customer_id,status,name,show_type,timelimit_type,start_time,end_time,createtime from weixin_commonshop_ads where isvalid=1 and customer_id=".$customer_id;

            $query_count = "select count(1) as tcount from weixin_commonshop_ads where isvalid=1 and customer_id=".$customer_id;
            // echo $query;
            /* 输出数量开始 */
            $adcount = 0;
            $result1 = _mysql_query($query_count) or die('commonshop_ads_count Query failed: ' . mysql_error());
            while ($row1 = mysql_fetch_object($result1)) {
                $adcount=$row1->tcount;
            }

            $page=ceil($adcount/$end);

            /* 输出数量结束 */
            $query = $query." order by createtime desc"." limit ".$start.",".$end;
            $result = _mysql_query($query) or die('commonshop_ads Query failed: ' . mysql_error());
            while ($row = mysql_fetch_object($result)) {
                $id                        = $row->id;
                $customer_id               = $row->customer_id;
                $status                    = $row->status;
                $name                      = $row->name;
                $show_type                 = $row->show_type;
                $timelimit_type            = $row->timelimit_type;
                $start_time                = $row->start_time;
                $end_time                  = $row->end_time;
                $createtime  			   = $row->createtime;

                //进入该广告列表页面触发检查每个广告是否过期，过期改为下架状态
                if($end_time != '' && $timelimit_type == 1){

                    $nowtime = strtotime(date("Y-m-d H:i:s",time()));
                    $endtime = strtotime($end_time);
                    if(($nowtime - $endtime )>0){
                        $check_time= "update  weixin_commonshop_ads set status=0 where customer_id=".$customer_id." and id=".$id;
                        _mysql_query($check_time) or die('ads check_time Query failed: ' . mysql_error());
                        $status = 0;
                    }
                }
            ?>
			<tr>
				<td><?php echo $id;?></td>
				<td><?php echo $name;?></td>
				<td>
                    <?php
                        if($show_type == 0){
                            echo '半屏';
                        }elseif($show_type == 1){
                            echo '全屏';
                        }
                    ?>
                </td>
				<td>
                    <?php
                        if($timelimit_type == 0){
                            echo ' 永久 ';
                        }else{
                    ?>
					<ol class="limit-time" style="margin-left: -15%;margin-top: 8px;">
						<li><?php echo $start_time;?></li>
						<li>至</li>
						<li><?php echo $end_time;?></li>
					</ol>
                    <?php
                        }
                    ?>
				</td>

				<td><?php
                    if($status == 0){
                        echo '下架';
                    }elseif($status == 1){
                        echo '上架中';
                    }
                    ?></td>
				<td><?php echo $createtime;?></td>
				<td class="limit-btn">
                    <?php
                    if($status == 0){
                    ?>
                        <a href="javascript:void(0);" onclick="change_status('<?php echo $id;?>','<?php echo $status;?>','<?php echo $timelimit_type;?>')" title="上架">
                            <img src="../../../common/images_V6.0/operating_icon/icon40.png" />
                        </a>
                    <?php
                    }else{
                    ?>
                    <a href="javascript:void(0);" onclick="change_status('<?php echo $id;?>','<?php echo $status;?>','<?php echo $timelimit_type;?>')" title="下架">
                        <img src="../../../common/images_V6.0/operating_icon/icon33.png" />
                    </a>
                    <?php
                    }
                    ?>
					<a href="limit_ad_edit.php?id=<?php echo $id;?>" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
					<a href="javascript:void(0);" title="删除" onclick="del_ads('<?php echo $id;?>');"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>
				</td>
			</tr>
        <?php }?>
		</tbody>
	</table>
    <div class="WSY_page"></div>

</div>
<script type="text/javascript" src="../../Common/js/layer/layer.js"></script>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
    var ad_up_id = <?php echo $ad_up_id;?>; //上架广告的id,没有则为0
    //删除广告
	function del_ads(id){
//		var tr = $(obj).parents('tr');
		layer.alert('确定删除！', {
            title: '提示',
            btn: ['确定', '取消'],
            btnAlign: 'c',
            yes: function(index, layero){
                //tr.remove();
                layer.close(index);
                var option="del_ads";
                $.ajax({
                    type : "POST",
                    url : "limit_ad_operation.php",
                    data : {"op" : option,"id" : id},
                    dataType: "json",
                    success : function(result) {
                        if(result.code=="1"){
                            window.location.reload();
                        }
                    }

                });
            },
            btn2: function(index, layero){
                layer.close(index);
            }
        });
	}

	//广告图上架、下架
    function change_status(id,ad_status,timelimit_type){
        var option="change_status";
        if((ad_up_id == 0 && ad_status == 0) || (ad_status == 1) ){
            $.ajax("limit_ad_operation.php",{
                type : "POST",
                async:false,
                data: {customer_id:'<?php echo $customer_id_en;?>',op : option, id : id,ad_status : ad_status,timelimit_type : timelimit_type},
                dataType:"json",
                success:function(result) {
                    if(result.code == 1){
                        window.location.reload();
                    }else if(result.code == -1){
                        layer.alert('该广告图已过期，请先修改显示时间再上架！', {
                            title: '提示',
                            btn: ['知道了'],
                            btnAlign: 'c',
                            yes: function(index, layero){
                                layer.close(index);

                            }
                        });
                    }
                }
            });
        }else if(ad_up_id != 0 &&  ad_status == 0){
            layer.alert('有广告正在上架，请先将其下架！', {
                title: '提示',
                btn: ['知道了'],
                btnAlign: 'c',
                yes: function(index, layero){
                    layer.close(index);

                }
            });
        }
    }
</script>
<script>
    pagenum  = <?php echo $pagenum; ?>;
    end      = <?php echo $end ?>;
    adcount = <?php echo $adcount;?>;
    count    = Math.ceil(adcount/end);//总页数
    page    = Math.ceil(adcount/end);//总页数
    $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
            var url = "limit_ad.php?pagenum="+p+"&pagecount="+end+"&customer_id=<?php echo $customer_id_en;?>";
            document.location = url;
        }
    });

    function jumppage(){
        var a=parseInt($("#WSY_jump_page").val());
        if((a<1) || (a==pagenum) ||  (a>page)|| isNaN(a)){
            return false;
        }else{
            var url = "limit_ad.php?pagenum="+a+"&pagecount="+end+"&customer_id=<?php echo $customer_id_en;?>";
            document.location = url;
        }
    }
</script>
<!--选择链接的JS结束-->
</body>
</html>