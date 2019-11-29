<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/proxy_info.php');
//require('../../../../weixinpl/auth_user.php');

$op = "";
if(!empty($_GET["op"])){
   $op = $_GET["op"];
   $shop_id  =-1;  
   if(!empty($_GET["shop_id"])){
	   $shop_id = $_GET["shop_id"];
	 }
   if($op=="del"){
	  $shop_id = $configutil->splash_new(passport_decrypt($_GET["shop_id"]));
	  $shop_card_id = $configutil->splash_new(passport_decrypt($_GET["card_id"]));
        $query = 'update weixin_card_shops set isvalid=false where card_id='.$shop_card_id.' and id='.$shop_id;
	   _mysql_query($query)or die('Query failed'.mysql_error());
	   
   }
}

$card_id = -1;
  
if(!empty($_GET["card_id"])){
  $card_id = $_GET["card_id"];
}
$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $_GET["pagenum"];
}

$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);

$query="select shop_card_id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$shop_card_id=-1;
while ($row = mysql_fetch_object($result)) {
	$shop_card_id = $row->shop_card_id;
	break;
}

$search_name="";
if(!empty($_GET["search_name"])){
	$search_name = htmlspecialchars($configutil->splash_new($_GET["search_name"]));
    $search_name_old = $_GET["search_name"];
}
$search_phone="";
if(!empty($_GET["search_phone"])){
	$search_phone = htmlspecialchars($configutil->splash_new($_GET["search_phone"]));
    $search_phone_old = $_GET["search_phone"];
}
$search_contactname="";
if(!empty($_GET["search_contactname"])){
	$search_contactname = htmlspecialchars($configutil->splash_new($_GET["search_contactname"]));
    $search_contactname_old = $_GET["search_contactname"];
}
$search_address="";
if(!empty($_GET["search_address"])){
	$search_address = htmlspecialchars($configutil->splash_new($_GET["search_address"]));
    $search_address_old = $_GET["search_address"];
}

?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>门店列表</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<style>
.WSY_page .WSY_page_search{height:26px;}
.WSY_list .WSY_left{padding-left:0px;}
.floatbox{position: absolute;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
.floatbox .tishitext{margin-bottom: 4px;}
.floatbox .checkboxsdiv{border: 1px solid #888888;padding: 8px;width: 200px;background-color: #ffffff;}
.checkboxsdiv input,.quanbuxuan input{display: inline-block;}
.checkboxsdiv p,.quanbuxuan p{display: inline-block;white-space: nowrap;overflow: hidden;max-width: 181px;margin-left: 5px;}
.floatbox .floatinputs{width: 60px;height: 27px;border-radius: 6px;background-color: #2eade8;cursor: pointer;color: #ffffff;display: inline-block;margin-top: 15px;margin-left: 16px;margin-right: 10px;}
.floatbox .floatinputc{width: 60px;height: 27px;color: #ffffff;background-color: #aaaaaa;cursor: pointer;border-radius: 6px;display: inline-block;margin-top: 15px;}
</style>
</head>

<body>
	<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">门店列表</a>
				</div>
			</div>
			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list">
        	<div class="WSY_left" style="background: none;">
				<a>名称
					<span class="WSY_input01 WSY_input_dd"><input type="text" name="search_name" id="search_name" value="<?php echo $search_name_old; ?>" ></span>
				    电话
					<span class="WSY_input01 WSY_input_dd"><input type="text" name="search_phone" id="search_phone" value="<?php echo $search_phone_old; ?>" ></span>
									
				    联系人
					<span class="WSY_input01 WSY_input_dd"><input type="text" name="search_contactname" id="search_contactname" value="<?php echo $search_contactname_old; ?>"></span>
									
				     地址
					<span class="WSY_input01 WSY_input_dd"><input type="text" name="search_address" id="search_address" value="<?php echo $search_address_old; ?>"></span>&nbsp;&nbsp;
                    <span style="margin-left:-35px;"><button class="WSY_search_01" id="searchForm" onclick="searchForm();">搜索</button></span>
					<span style="margin-left:-5px;"><button class="WSY_search_01" onclick="export_excel();">新导出门店列表</button></span>
				</a>
            </div>

			<?php
				if($shop_card_id>0){	
			?>
			<ul class="WSY_righticon">
            <li><a name="add_store" id="add_store"href="addcardshop.php?card_id=<?php echo passport_encrypt((string)$shop_card_id) ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>">添加门店</a></li>
			<!--   <li><a href="">批量删除</a></li> -->
			</ul>
			<?php
				}else{
					echo '';
				}
			?>
            <br class="WSY_clearfloat";>
        </div>
        <!--列表按钮开始-->
        <!--表格开始-->
        <table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			<thead class="WSY_table_header">
				<tr>
					<th width="12%">名称</th>
					<th width="14%">电话</th>
					<th width="10%">联系人</th>
					<th width="32%">地址</th>
					<th width="8%">会员数</th> 
					<th width="8%">未支付订单数</th>
					<th width="8%">已支付订单数</th>
					<th width="9%">操作</th>
				</tr>
			</thead>
			<form name="form1" method="post">
				<tbody>
					<?php 
					$pagenum = 1;
					if(!empty($_GET["pagenum"])){
						$pagenum = $configutil->splash_new($_GET["pagenum"]);
					}
					$start = ($pagenum-1) * 20;
					$end = 20;
					$query="select shop_card_id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
					$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
					$shop_card_id=-1;
					while ($row = mysql_fetch_object($result)) {
						$shop_card_id = $row->shop_card_id;
						break;
					}
					$query_count = "select count(1) rcount  from weixin_card_shops where isvalid=true and card_id=".$shop_card_id;
					$query = 'SELECT id,name,phone,address,contactname FROM weixin_card_shops where isvalid=true and card_id='.$shop_card_id;

					$query3 = "";
					if(!empty($search_name)){
						$query3=" and name like '%".$search_name."%'";
					}
					if(!empty($search_phone)){
						$query3.=" and phone like '%".$search_phone."%'";
					}
					if(!empty($search_contactname)){
						$query3.=" and contactname like '%".$search_contactname."%'";
					}
					if(!empty($search_address)){
						$query3.=" and address like '%".$search_address."%'";
					}
					$query .= $query3;
					$query_count .= $query3;
					
					$query=$query.' limit '.$start.','.$end;
					$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
					$result_count = _mysql_query($query_count) or die('Query failed: ' . mysql_error());
					if($row_count = mysql_fetch_object($result_count)){
						$rcount_q2 = $row_count->rcount;
					}
					
					while ($row = mysql_fetch_object($result)) {
						$shop_name = $row->name;
						$shop_phone = $row->phone;
						$shop_address = $row->address;
						$shop_contactname = $row->contactname;
						$shop_id = $row->id;
						/*$query2="select id,user_id from weixin_card_members where card_shop_id=".$shop_id." and isvalid=true";
						$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());   
						$user_ids="";
						$pcount = 0;
						while ($row2 = mysql_fetch_object($result2)) {
							$t_user_id = $row2->user_id;
							if(empty($user_ids)){
								$user_ids = $t_user_id;
							}else{
								$user_ids = $user_ids.",".$t_user_id;
							}
							$pcount++;
						}
						$no_pay_orders = 0;
						$pay_orders = 0;
						if(!empty($user_ids)){
							$query2="select store_id,count(1) as ncount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and paystatus=0 and user_id in (".$user_ids.")";
							$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());   
							while ($row2 = mysql_fetch_object($result2)) {  
								$no_pay_orders = $row2->ncount;
								break;
							}
							$query2="select store_id,count(1) as ncount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and paystatus=1 and user_id in (".$user_ids.")";
							$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());   
							while ($row2 = mysql_fetch_object($result2)) {  
								$pay_orders = $row2->ncount;
								break;
							} 
						}*/
						
						$query2="select count(1) as ncount from weixin_card_members where card_shop_id=".$shop_id." and isvalid=true";
						$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());  
						$pcount = 0;
						$no_pay_orders = 0;
						$pay_orders = 0;
						while ($row2 = mysql_fetch_object($result2)) {  
							$pcount = $row2->ncount;
							break;
						}
						
						$query2="select count(1) as ncount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and paystatus=0 and store_id = ".$shop_id;
						$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());   
						while ($row2 = mysql_fetch_object($result2)) {  
							$no_pay_orders = $row2->ncount;
							break;
						}
						$query2="select count(1) as ncount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and paystatus=1 and store_id = ".$shop_id;
						$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());   
						while ($row2 = mysql_fetch_object($result2)) {  
							$pay_orders = $row2->ncount;
							break;
						} 
					?>
				<tr>
					<td><?php echo $shop_name; ?></td>  
					<td><?php echo $shop_phone; ?></td>
					<td><?php echo $shop_contactname; ?></td>
					<td><?php echo $shop_address; ?></td>
					
					<td align="center"><a href="../../../card_member.php?card_id=<?php echo $shop_card_id; ?>&card_shop_id=<?php echo $shop_id; ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" ><?php echo $pcount; ?></a></td>
					
					<td align="center"><a href="../order/order.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&search_paystatus=0&search_shop_id=<?php echo $shop_id; ?>"><?php echo $no_pay_orders; ?></a></td>
					
					<td align="center"><a href="../order/order.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&search_paystatus=1&search_shop_id=<?php echo $shop_id; ?>"><?php echo $pay_orders; ?></a></td>
					
					<td><a href="addcardshop.php?keyid=<?php echo passport_encrypt((string)$shop_id) ?>&card_id=<?php echo passport_encrypt((string)$shop_card_id) ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" style="cursor:pointer;" title="编辑">
						<img src="../../../common/images_V6.0/operating_icon/icon05.png" style="height:20px;width:20px"></a>
					<a href="shops.php?shop_id=<?php echo passport_encrypt((string)$shop_id) ?>&op=del&card_id=<?php echo passport_encrypt((string)$shop_card_id) ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};" title="删除">
						<img src="../../../common/images_V6.0/operating_icon/icon04.png" style="height:20px;width:20px"></a></td>
					
				</tr>					
				<?php } ?>
				</tbody>
			</form>
        </table>
        <!--表格结束-->
        
        <!--翻页开始-->
		
      <div class="WSY_page">

      </div>
  <!--翻页结束--></div> <!--门店列表结束-->
	</div>
	<div style="width:100%;height:20px;"></div>
</div>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" background="#ffffff"> 
var pagenum = <?php echo $pagenum ?>;
var rcount_q2 = <?php echo $rcount_q2 ?>;
var end = <?php echo $end ?>;
var count =Math.ceil(rcount_q2/end);//总页数
var page = count;
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
	pageCount:count,
	current:pagenum,
	backFn:function(p){
    	 var search_name = document.getElementById("search_name").value; 
		document.location= "shops.php?pagenum="+p+"&search_name="+search_name;
	}
});
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());  
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		var search_name = document.getElementById("search_name").value; 
	document.location= "shops.php?pagenum="+p+"&search_name="+search_name;
	}
  }

document.onkeyup = function (event) {
    var e = event || window.event;
    var keyCode = e.keyCode || e.which;
    switch (keyCode) {
        case 13:
            $("#searchForm").click();
            break;
        default:
            break;
    }
}

function searchForm(){
    //判断手机号码是否为11为纯数字
    if ($("#search_phone").val() !== '') {
        var reg = /^\d{11}$/;
        if (!reg.test($("#search_phone").val())) {
            alert("手机号码填写有误，请填写正确的手机号码")
            return false;
        }
    }

    var search_name = encodeURIComponent(document.getElementById("search_name").value);
    var search_phone = document.getElementById("search_phone").value;
    var search_contactname = encodeURIComponent(document.getElementById("search_contactname").value);
    var search_address = encodeURIComponent(document.getElementById("search_address").value);
	document.location= "shops.php?pagenum=1&search_name="+search_name+"&search_phone="+search_phone+"&search_contactname="+search_contactname+"&search_address="+search_address;
}

function export_excel(){
    //判断手机号码是否为11为纯数字
    if ($("#search_phone").val() !== '') {
        var reg = /^\d{11}$/;
        if (!reg.test($("#search_phone").val())) {
            alert("手机号码填写有误，请填写正确的手机号码")
            return false;
        }
    }

    var name ="commonshop_excel_shops";
    var excelArray = [
        ["shop_name","名称"],
        ["shop_phone","电话"],
        ["shop_contactname","联系人"],
        ["shop_address","地址"],
        ["pcount","会员数"],
        ["no_pay_orders","未支付订单数"],
        ["pay_orders","已支付订单数"],
    ];

    var search_name = $("#search_name").val();//名称
    var search_phone = $("#search_phone").val();//电话
    var search_contactname = $("#search_contactname").val();//联系人
    var search_address = $("#search_address").val();//地址

    /*导出订单筛选框*/
    exportBox(excelArray);
    $(".floatbox").show();

    $(".floatinputs").click(function() {
        var str = "";
        $("input[name='excel_field[]']:checkbox").each(function () {
            if ($(this).is(':checked')) {
                str += $(this).val() + ","
            }
        })
        str = str.substring(0, str.length - 1);

        if (str == "") {
            str = 0;
        }
        if (search_name == "") {
            search_name = -1;
        }
        if (search_phone == "") {
            search_phone = -1;
        }
        if (search_contactname == "") {
            search_contactname = -1;
        }
        if (search_address == "") {
            search_address = -1;
        }

        var url_base = '/weixin/plat/app/index.php/Excel/'

        var excel_fields = str;
        var parm = '/customer_id/<?php echo passport_decrypt($customer_id); ?>/search_name/' + search_name + '/search_phone/' + search_phone + '/search_contactname/' + search_contactname + '/search_address/' + search_address + '/';
        url_base += name + parm + 'excel_fields/' + str + '/';

        var __s = [];
        parm = parm.substring(1, parm.length - 1);
        console.log(parm);
        __s = parm.split('/');
        var _obj = {};
        console.log(__s);
        for (var i in __s) {
            if (parseInt(i) % 2 == 0) {
                _obj[__s[i]] = '';
            } else {
                _obj[__s[i - 1]] = __s[i];
            }
        }

        var obj = JSON.stringify(_obj);
        console.log(obj);

        var emails = '';
        var op = 'iscount';
        $.ajax({
            type: 'post',
            async: false,
            url: '/weixinpl/common/explore/jiaoben.php',
            data: {
                fields: excel_fields,
                function_name: name,
                param_json: obj,
                customer_id:<?php echo passport_decrypt($customer_id); ?>,
                op: op,
            },
            success: function (data) {
                var res = JSON.parse(data);

                if (res.status == 2) {
                    layer.msg(res.msg);
                    return;
                }


                var eamil_arr = res.emails.split('#*#');
                var eamil_address = "";
                var type = 2;
                var op = 'add_email';
                var tips = "导出数据已打包发送到您的邮箱，请注意查收";
                if (eamil_arr.length > 0) {
                    eamil_address = eamil_arr[0];
                }

                if (res.errcode == 10003) {
                    layer.msg(res.errmsg);
                    return;
                }
                else
                {
                    type = 2;
                    tips = "请留意您的邮箱，导出完成后会发到你的邮箱上！";
                    layer.prompt({title: '请输入您邮箱地址', value: '', formType: 0}, function (email, prompt) {

                        layer.close(prompt);
                        if (checkEmail(email)) {
                            emails = email;
                            $.ajax({
                                type: 'post',
                                async: false,
                                url: '/weixinpl/common/explore/jiaoben.php',
                                data: {
                                    fields: excel_fields,
                                    function_name: name,
                                    param_json: obj,
                                    customer_id:<?php echo passport_decrypt($customer_id); ?>,
                                    email: emails,
                                    op: op,
                                    type: type,
                                },
                                success: function (data) {
                                    var res = JSON.parse(data);
                                    if (res.status == 2) {
                                        layer.msg(res.msg);
                                        return;
                                    }
                                    $.post('/weixinpl/common/explore/jiaoben.php', {'debug': 1}, function (da) {
                                    }, 'json');
                                    layer.msg(tips);
                                }
                            });

                        }
                        else {
                            layer.msg("邮箱地址填写有误，请填写正确的邮箱地址");
                            return;
                        }
                    })
                }
            }
        })

        $(".floatbox").hide();
        $(".floatbox").remove();
    });
}

function ShowDIV(thisObjID) {
    $("#BgDiv").css({ display: "block", height: $(document).height() });
    var yscroll = document.documentElement.scrollTop;
    $("#" + thisObjID).css("top", "100px");
    $("#" + thisObjID).css("display", "block");
    document.documentElement.scrollTop = 0;
}

function closeDiv(thisObjID) {
    $("#BgDiv").css("display", "none");
    $("#" + thisObjID).css("display", "none");
}
 
/*校验邮箱地址*/
function checkEmail(str){
    var re= /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    return re.test(str);
}


</script>
    <script type="text/javascript" src="/weixinpl/common/js/jquery-2.1.0.min.js"></script>
    <script src="/weixinpl/common/js/floatBox.js"></script>
    <script charset="utf-8" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
    <script>
        layer.config({
            extend: '/extend/layer.ext.js'
        });
    </script>
</body>
</html>
