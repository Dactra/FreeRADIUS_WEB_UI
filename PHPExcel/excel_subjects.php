#!/bin/php
<?php     
header("Content-Type: application/vnd.ms-excel;");
header('Content-Disposition: attachment;filename="subjects.csv"'); 
echo "��Ŀ����,���,�����Ա,���ʱ��,��ע\n";   
require_once("../inc/conn.php");mysql_query("set names gb2312"); //utf8
//********************************************���ñ�������� 
@$name			      =$_REQUEST["name"]; 
@$sql .="ID!=''";
if($name)	$sql .=" and name like '%".$name."%'";
@$sql .=" order by ID DESC";
$result=$db->select_all("*","finance",$sql,20);
if(is_array($result)){ 
	foreach($result as $rs){
		//��ѯ���û���IP��ַ
	 	echo "'".$rs["name"].","."'".$rs["money"].",".$rs["operator"].","."'".$rs["adddatetime"].","."'".trim($rs["remark"])."\n"; 
	} 
}   
?>

