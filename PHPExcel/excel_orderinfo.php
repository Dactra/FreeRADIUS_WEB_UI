#!/bin/php
<?php     
header("Content-Type: application/vnd.ms-excel;");
header('Content-Disposition: attachment;filename="orderinfo.csv"'); 
echo "�˺�,��Ʒ,������Ա,��ʼʱ��,����ʱ��,����ʱ��,��ǰ״̬\n";  
require_once("../inc/conn.php");mysql_query("set names gb2312"); //utf8 
//********************************************���ñ��������
@$UserName        =$_REQUEST["UserName"];
@$startDateTime   =$_REQUEST["startDateTime"];
@$endDateTime     =$_REQUEST["endDateTime"];
@$operator		    =$_REQUEST["operator"];
@$productID		    =$_REQUEST["productID"];
@$findOrderStatus =$_REQUEST["findOrderStatus"];
@$querystring="UserName=".$UserName."&name=".$name."&startDateTime=".$startDateTime."&endDateTime=".$endDateTime."&startDateTime1=".$startDateTime1."&endDateTime1=".$endDateTime1."&operator=".$operator."&productID=".$productID."&findOrderStatus='".$findOrderStatus."'"; 

$sql="o.userID=u.ID and o.ID=r.orderID and p.ID=o.productID and u.projectID in (". $_SESSION["auth_project"].") and u.gradeID in (". $_SESSION["auth_gradeID"].")";
if($UserName){
	$sql .=" and u.UserName like '%".$UserName."%'";
}
if($startDateTime){
	$sql .=" and o.adddatetime>='".$startDateTime."'";
}
if($endDateTime){
	$sql .=" and o.adddatetime<'".$endDateTime."'";
}
if($operator){
	$sql .=" and o.operator='".$operator."'";
}
if($productID){
	$sql .=" and p.ID='".$productID."'";
}
if($findOrderStatus){
 if($findOrderStatus=="wait") 
    $findOrderStatus = 0;
    $sql .= " and o.status = '".$findOrderStatus."'";
} 

$result=$db->select_all("o.*,u.*,o.ID as orderID,r.*,o.adddatetime as o_adddatetime,o.status as order_status,p.name as pname","orderinfo as o,userinfo as u,userrun as r,product as p",$sql); 
 if(is_array($result)){ 
	foreach($result as $key=>$rs){ 
	if($rs["order_status"]==0)  $statusStr = "�ȴ�����";
	else if($rs["order_status"]==1) $statusStr = "����ʹ��";
	else if($rs["order_status"]==2) $statusStr = "����ʹ��";
	else if($rs["order_status"]==3) $statusStr = "Ƿ��ͣ��";
	else if($rs["order_status"]==4) $statusStr = "���";
	else if($rs["order_status"]==5) $statusStr = "��ͣʹ��";
	else $statusStr = "δ֪";
	echo "'".$rs["UserName"].","."'".$rs["pname"].",".$rs["operator"].","."'".$rs["begindatetime"].","."'".$rs["enddatetime"].","."'".$rs["o_adddatetime"].","."'".$statusStr."\n"; 
   } 
}








 
?>

