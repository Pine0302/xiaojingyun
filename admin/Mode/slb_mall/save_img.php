<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');

if ($_files["img"]["error"] > 0)
{
echo "Error: " . $_files["test_file"]["error"] . "<br />";
}
else
{
//������ж�ͼƬ���Եķ����Ͳ�д�ˡ��Լ���չһ�¡�
$filetype=strrchr($_files["img"]["name"],".");
$filetype=substr($filetype,1,strlen($filetype)); 
$filename="img/".time("YmdHis").".".$filetype;
_move_uploaded_file($_files["img"]["tmp_name"],$filename);
echo '<script >alert(1)</script>';
$return="parent.document.getElementByIdx_x('mpic').innerhtml=";
echo "<script >alert('�ϴ��ɹ�')</script>";
echo "<script>{$return}</script>";
}

?>