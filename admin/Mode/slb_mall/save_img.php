<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');

if ($_files["img"]["error"] > 0)
{
echo "Error: " . $_files["test_file"]["error"] . "<br />";
}
else
{
//这里的判断图片属性的方法就不写了。自己扩展一下。
$filetype=strrchr($_files["img"]["name"],".");
$filetype=substr($filetype,1,strlen($filetype)); 
$filename="img/".time("YmdHis").".".$filetype;
_move_uploaded_file($_files["img"]["tmp_name"],$filename);
echo '<script >alert(1)</script>';
$return="parent.document.getElementByIdx_x('mpic').innerhtml=";
echo "<script >alert('上传成功')</script>";
echo "<script>{$return}</script>";
}

?>