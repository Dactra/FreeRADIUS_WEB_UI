#!/bin/php
<?php     
header("Content-Type: application/vnd.ms-excel;");
header('Content-Disposition: attachment;filename="cards.csv"'); 
echo "����,����,���(Ԫ),�Ƿ�����,�Ƿ��ֵ,�ƿ�ʱ��,ʧЧʱ��,�ƿ�Ա,����Ա,��ֵ��Ա\n";  
require_once("../inc/conn.php");mysql_query("set names utf8"); 
//********************************************���ñ��������
@$cardNumber      =$_REQUEST["cardNumber"];
@$startDateTime   =$_REQUEST["startDateTime"];
@$startDateTime1  =$_REQUEST["startDateTime1"];
@$endDateTime     =$_REQUEST["endDateTime"];
@$endDateTime1    =$_REQUEST["endDateTime1"];
@$operator		    =$_REQUEST["operator"];
@$sold			      =$_REQUEST["sold"];
@$recharge		    =$_REQUEST["recharge"]; 
$sql=" 0=0 "; 
if($cardNumber) $sql .=" and cardNumber like '%".$cardNumber."%'"; 
if($startDateTime) $sql .=" and cardAddTime>='".$startDateTime."'"; 
if($startDateTime1) $sql .=" and cardAddTime<'".$startDateTime."'"; 
if($endDateTime) $sql .=" and ivalidTime>='".$endDateTime."'"; 
if($endDateTime1) $sql .=" and ivalidTime<'".$endDateTime1."'"; 
if($operator) $sql .=" and operator='".$operator."'"; 
if($sold!="type" && isset($sold) && !empty($sold)) $sql .=" and sold='".$sold."'"; 
if($recharge!="type" && isset($recharge) && !empty($recharge) )	$sql .=" and recharge='".$recharge."'"; 
$sql .="order by ID desc";
$result=$db->select_all("*","card",$sql); 
	if(is_array($result)){
	    $row=2;
		foreach($result as $key=>$rs){
		$cardLogRs=$db->select_one("UserName","cardlog","cardNumber='".$rs["cardNumber"]."'"); 
		$sold=($rs["sold"]==1)?"���۳�":"δ�۳�";
		$recharge=($rs["recharge"]==1)?"����":"δ��";
		$solder=($rs["solder"]=="NULL")?"��":$rs["solder"];
		$cardUserName=$cardLogRs["UserName"];  
		echo $rs["cardNumber"].",".$rs["actviation"].",".$rs["money"].",".$sold.",".$recharge.",'".$rs["cardAddTime"].",'".$rs["ivalidTime"].",".$rs["operator"].",".$solder.",".$cardUserName."\n";   
		} 	
  }    
		
?>

