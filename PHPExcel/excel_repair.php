#!/bin/php
<?php     
header("Content-Type: application/vnd.ms-excel;");
header('Content-Disposition: attachment;filename="repair.csv"'); 
echo "�ʺ�,�������,˵��,��ʼʱ��,����ʱ��,�Ǽ���Ա,��ǰ״̬\n";  
require_once("../inc/conn.php");mysql_query("set names gb2312"); //utf8 
//********************************************���ñ�������� 
@$UserName     =$_REQUEST["UserName"];
@$starDateTime =$_REQUEST["startDateTime"];
@$endDateTime  =$_REQUEST["endDateTime"];
@$status 	     =$_REQUEST["status"];
@$type         =$_REQUEST["type"];
@$operator     =$_REQUEST["operator"];
$sql="r.UserName=u.UserName and  u.projectID in (". $_SESSION["auth_project"].") and u.gradeID in (". $_SESSION["auth_gradeID"].") ";
if($UserName)$sql .=" and r.UserName like '%".$UserName."%'";
if($startDateTime)$sql .=" and r.startdatetime>='".$startDateTime."'";
if($endDateTime)$sql .=" and r.startdatetime<'".$endDateTime."'";
if($status)$sql .=" and r.status='".$status."'";
if($type) $sql .=" and r.type ='".$type."'";
if($operator)$sql .=" and r.operator='".$operator."'";
$sql .=" order by r.ID desc";
$result=$db->select_all("r.*","repair as r,userinfo as u",$sql); 
	
if(is_array($result)){
	 foreach($result as $key=>$rs){
			if($rs["status"]=="1")$status ="����������";
			else if($rs["status"]=="2")$status="����������";
	    else if($rs["status"]=="3")$status="�����������";
			if($rs["type"]=="1")$type=_("��װ");
			else if($rs["type"]=="2") $type=_("����");
			else if($rs["type"]=="3")$type=_("����");
	    echo "'".$rs["UserName"].","."'". $type.",".trim($rs["reason"]).","."'".$rs["startdatetime"].","."'".$rs["enddatetime"].",".$rs["operator"].",".$status."\n"; 
   }
}			








  
?>

