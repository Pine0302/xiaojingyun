<?php

	$write = new PHPExcel_Writer_Excel5($excel);

	header("Pragma: public");

	header("Expires: 0");

	header("Cache-Control:must-revalidate, post-check=0, pre-check=0");

	header("Content-Type:application/force-download");

	header("Content-Type:application/vnd.ms-execl");

	header("Content-Type:application/octet-stream");

	header("Content-Type:application/download");;

	header('Content-Disposition:attachment;filename="testdata.xls"');

	header("Content-Transfer-Encoding:binary");

	$write->save('php://output');

?>