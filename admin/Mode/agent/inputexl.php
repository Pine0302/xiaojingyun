<?php 
$filename = $_POST['filename'];
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");			
header("Accept-Ranges:bytes");
header("Content-type:application/vnd.ms-excel");  
header("Content-Disposition:attachment;filename=".$filename.".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="//www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Worksheet</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>
<?php echo $_POST['excel'];?>
</body></html>