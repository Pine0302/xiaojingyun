<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/back_init.php');
$link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


$imgtypes=array('jpg', //上传文件类型列表
'jpeg',
'png',
'pjpeg',
'gif',
'bmp',
'x-png');

$query="select id,width,height from media_library_mapsizes where isvalid=true";
$par_mapflag_id = -1;

if(!empty($_GET["mapflag_id"])){
   $par_mapflag_id = $_GET["mapflag_id"];
}

$par_mapsize_id = -1;

if(!empty($_GET["mapsize_id"])){
   $par_mapsize_id = $configutil->splash_new($_GET["mapsize_id"]);
}

$par_category_id = -1;
if(!empty($_GET["category_id"])){
   $par_category_id = $_GET["category_id"];
}

$par_customer_id = -1;
if(!empty($_GET["par_customer_id"])){
   $par_customer_id = $_GET["par_customer_id"];
}

$pagenum = 1;
if(!empty($_GET["pagenum"])){
    $pagenum = $_GET["pagenum"];
}
$pagesize =15;
$start = ($pagenum-1) * $pagesize;
$end = $pagesize;
?>
<html>
<head>
<script type="text/javascript" src="../../../common/js/jquery.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>


<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
.map_one{
   width:90%;
   margin:0 auto;
   height:40px;
   line-height:40px;
   font-size:12px;
}
.map_two{
  width:90%;
  margin:0 auto;
  height:auto;
  margin-top:10px;
}
.map_two_line{
  width:100%;
  height:150px;
  text-align:center;
}

.map_two_line_item{
  width:30%;
  height:120px;
  float:left;
}
.map_two_line_item_t{
  width:90%;
  height:120px;
  margin:0 auto;
}
.map_two_line_item_b{
  width:90%;
  height:30px;
  margin:0 auto;
}

.map_three{
   width:90%;
   margin:0 auto;
   height:40px;
   line-height:40px;
   margin-top:10px;
}
.map_three_item{
   width:50%;
   margin:0 auto;
   height:100%;
   float:left;
   text-align:center;
}

.sub_btn_con{

   width:100px;
   height:30px;
   background:#63b551;
   background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#63b551), color-stop(100%,#59b144)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #63b551 0%,#59b144 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #63b551 0%,#59b144 100%); /* Opera 11.10+ */
	-webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;	
	
	text-align:center;
	line-height:30px;
	color:#fff;
	font-size:18px;
	cursor:hand;
	float:left;
	margin:0 auto;
}

.map_page{
   width:90%;
   margin: 0 auto;
   height:30px;
   line-height:30px;
}
.map_page_item{
   width:50%;
   height:100%;
   margin:0 auto;
   float:left;
   text-align:center;
}

.getmore{
  width:200px;
  margin:0 auto;
  height:30px;
  line-height:30px;
  text-align:center;
  font-size:13px;
  
}
.getmore_l{
   float:left;
   width:80px;
   height:100%;
   background:#c6c5c5;
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#c6c5c5), color-stop(100%,#a29f9f)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #c6c5c5 0%,#a29f9f 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #c6c5c5 0%,#a29f9f 100%); /* Opera 11.10+ */
	-moz-border-radius:5px;
    -webkit-border-radius:5px;
    border-radius:5px;


    -moz-box-shadow:0 0 1px #fff inset;
    -webkit-box-shadow:0 0 1px #fff inset;
    box-shadow:0 0 1px #fff inset;
	color:#fff;
}

.getmore_r{
   float:right;
   width:80px;
   height:100%;
   background:#c6c5c5;
   
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#c6c5c5), color-stop(100%,#a29f9f)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #c6c5c5 0%,#a29f9f 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #c6c5c5 0%,#a29f9f 100%); /* Opera 11.10+ */
	-moz-border-radius:5px;
    -webkit-border-radius:5px;
    border-radius:5px;


    -moz-box-shadow:0 0 1px #fff inset;
    -webkit-box-shadow:0 0 1px #fff inset;
    box-shadow:0 0 1px #fff inset;
	color:#fff;
   
}

.map_noe select{
  height:30px; 
  border:1px solid #ddd; 
  padding:5px; 
  width:auto; 
  vertical-align:middle; 
  border-radius:5px;
 }

</style>
</head>

<body>
<div class="map_noe">
  
  <select id="mapsize_id" name="mapsize_id" onchange="selMap_sel();">
   <option value=-1>选择尺寸</option>
   <?php 
      $query="select id,width,height from media_library_mapsizes where isvalid=true";
	  $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	  while ($row = mysql_fetch_object($result)) {
	     $mapsize_id = $row->id;
		 $width = $row->width;
		 $height = $row->height;
   ?>
        <option value="<?php echo $mapsize_id; ?>"  <?php if($par_mapsize_id==$mapsize_id){ ?> selected <?php } ?>><?php echo $width; ?>*<?php echo $height; ?></option>
  <?php } ?>
  </select>
   <select id="mapflag_id" name="mapflag_id" onchange="selMap_sel();">
   <option value=-1>选择标签</option>
   <?php 
      $query="select id,name from media_library_map_flags where isvalid=true";
	  $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	  while ($row = mysql_fetch_object($result)) {
	     $mapflag_id = $row->id;
		 
		 $name = $row->name;
   ?>
        <option value="<?php echo $mapflag_id; ?>" <?php if($par_mapflag_id==$mapflag_id){ ?> selected <?php } ?>><?php echo $name; ?></option>
  <?php } ?>
  </select>
   <select id="category_id" name="category_id"  onchange="selMap_sel();">
   <option value=-1>选择行业</option>
   <?php 
      $query="select id,name from categorys where isvalid=true";
	  $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	  while ($row = mysql_fetch_object($result)) {
	     $category_id = $row->id;
		 
		 $name = $row->name;
   ?>
        <option value="<?php echo $category_id; ?>" <?php if($par_category_id==$category_id){ ?> selected <?php } ?>><?php echo $name; ?></option>
  <?php } ?>
  </select>
  <select id="customer_id" name="customer_id"  onchange="selMap_sel();">
   <option value=-1>所有发布者</option>
     <option value="<?php echo $customer_id; ?>" <?php if($par_customer_id==$customer_id){ ?> selected <?php } ?>>我发布的</option>
  </select>
</div>

<div class="map_two">

   <div class="map_two_line">
     <?php 
	  $query="select id,imgurl from media_library_maps where isvalid=true and ((owner_type=2 and owner_id=".$customer_id.") or owner_type!=2)";
	  
	  $q_str = "";
	  if($par_customer_id>0){
	     $q_str = " and owner_id=".$par_customer_id;
	  }else{
	     if($par_mapsize_id>0){
	        $q_str = " and mapsize_id=".$par_mapsize_id;
		  }
		  if($par_mapflag_id>0){
			 $q_str = " and flag_id=".$par_mapflag_id;
		  }
		  if($par_category_id>0){
			 $q_str = " and category_id=".$par_category_id;
		  }
	  }
	  $query = $query.$q_str." order by id desc limit ".$start.",".$end;
	  
	  
	  $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	  $rcount_q = mysql_num_rows($result);
	  while ($row = mysql_fetch_object($result)) {
	     $map_id = $row->id;
	     $imgurl = $row->imgurl;
		 $bigimgurl = $imgurl;
		 $pos = strrpos($bigimgurl,'.');
		 $tail= substr($bigimgurl,$pos+1);
		 
		 if(empty($pos)){
		    continue;
		 }
		 if(!in_array($tail, $imgtypes)){
		     continue;
		 }
		 //$bigimgurl = str_replace("medialibrary","medialibrarybig",$imgurl);
	 ?>
	 <label>
      <div class="map_two_line_item" ondblclick="confPop('<?php echo $bigimgurl; ?>');">
	     <div class="map_two_line_item_t">
		   <img src="<?php echo $imgurl; ?>" style="width:80px;height:80px;" />
		 </div>
		 <div class="map_two_line_item_b">
		    <input type=hidden name="mapid" value="<?php echo $bigimgurl; ?>" id="map_<?php echo $map_id; ?>" />
		 </div>
	  </div>
	  </label>
	  <?php 
	  } ?>
   </div>
</div>
<div style="clear:both;"></div>
<hr/>
<div class="map_page">
    <div class="getmore">
     <?php if($pagenum>1){ ?>
     <div class="getmore_l" onclick="prePage();">
	    上一页
	 </div>
	 <?php } ?>
	 
	 <?php if($rcount_q==15){?>
	 <div class="getmore_r"  onclick="nextPage();">
	    下一页
	 </div>
	 <?php } ?>
  </div>
</div>

<div style="clear:both;"></div>
<!--<div class="map_three">
   <div class="map_three_item">
	   <div class="sub_btn_con" onclick="confPop();">
		 确定
	   </div>
   </div>
   
</div>-->
<script>

var pagenum = <?php echo $pagenum ?>;


function selMap_sel(){
   pagenum = 1;
   selMap();
}
function selMap(){
    var mapsize_id = document.getElementById("mapsize_id").value;
	var mapflag_id = document.getElementById("mapflag_id").value;
	var category_id = document.getElementById("category_id").value;
	var customer_id = document.getElementById("customer_id").value;
	document.location = "mediamap.php?customer_id=<?php echo $customer_id; ?>&mapflag_id="+mapflag_id+"&mapsize_id="+mapsize_id+"&category_id="+category_id+"&pagenum="+pagenum+"&par_customer_id="+customer_id;
}
function confPop(imgurl){
   parent.setMapValue(imgurl);
}


function prePage(){
 pagenum--;
 selMap();
}

function nextPage(){
 pagenum++;
 selMap();
}
</script>
</body>
</html>


<?php 

mysql_close($link);

?>