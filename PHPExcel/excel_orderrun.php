#!/bin/php
<?php     
header("Content-Type: application/vnd.ms-excel;");
header('Content-Disposition: attachment;filename="orderinfo.csv"'); 
echo "�˺�,��Ʒ,������Ա,��ʼʱ��,����ʱ��,����ʱ��,��ǰ״̬\n";  
require_once("../inc/conn.php");mysql_query("set names gb2312"); //utf8 
//********************************************���ñ��������
@$sql="o.userID=u.ID and o.ID=r.orderID and r.status in(1,5) and u.projectID in (". $_SESSION["auth_project"].") and u.gradeID in (". $_SESSION["auth_gradeID"].")";
@$account         =$_REQUEST["account"];
@$startDateTime   =$_REQUEST["startDateTime"];
@$startDateTime1  =$_REQUEST["startDateTime1"];
@$endDateTime     =$_REQUEST["endDateTime"];
@$endDateTime1    =$_REQUEST["endDateTime1"];
@$operator		    =$_REQUEST["operator"];
if($account) $sql .=" and u.account like '%".$account."%'"; 
if($startDateTime) $sql .=" and r.begindatetime>='".$startDateTime."'"; 
if($startDateTime1) $sql .=" and r.begindatetime<'".$startDateTime1."'"; 
if($endDateTime)$sql .=" and r.enddatetime>='".$endDateTime."'";
if($endDateTime1)$sql .=" and r.enddatetime<'".$endDateTime1."'";
if($operator)	$sql .=" and o.operator='".$operator."'";
$result=$db->select_all("o.*,u.*,o.status as orderID_status,r.*,o.adddatetime as o_adddatetime","orderinfo as o,userinfo as u,userrun as r",$sql);
if(is_array($result)){ 
	foreach($result as $key=>$rs){ 
	if($rs["orderID_status"]==0)  $statusStr = "�ȴ�����";
	else if($rs["orderID_status"]==1) $statusStr = "����ʹ��";
	else if($rs["orderID_status"]==2) $statusStr = "����ʹ��";
	else if($rs["orderID_status"]==3) $statusStr = "Ƿ��ͣ��";
	else if($rs["orderID_status"]==4) $statusStr = "���";
	else if($rs["orderID_status"]==5) $statusStr = "��ͣʹ��";
	else $statusStr = "δ֪";
	
	echo "'".$rs["UserName"].","."'".productShow($rs["productID"]).",".$rs["operator"].","."'".$rs["begindatetime"].","."'".$rs["enddatetime"].","."'".$rs["o_adddatetime"].","."'".$statusStr."\n"; 
   } 
}








 
?>

