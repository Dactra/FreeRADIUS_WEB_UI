#!/bin/php
<?php     
header("Content-Type: application/vnd.ms-excel;");
header('Content-Disposition: attachment;filename="pause.csv"'); 
echo "�ʺ�,����,����,֤������,�ֻ�����,��ϵ��ַ,�ʻ����,��ʼʱ��,����ʱ��,��Ŀ���,��Ʒ���,������Ա,�û���ע\n";  
require_once("../inc/conn.php");mysql_query("set names gb2312"); //utf8
//********************************************���ñ��������
@$UserName        =$_REQUEST["UserName"];
@$startDateTime   =$_REQUEST["startDateTime"];
@$endDateTime     =$_REQUEST["endDateTime"];
@$projectID		    =$_REQUEST["projectID"]; 
@$name			      =$_REQUEST["name"];
@$address		      =$_REQUEST["address"];   
@$sql="o.userID=u.ID and u.ID = o.userID and at.orderID =o.ID and at.status=5 and u.projectID in (".$_SESSION["auth_project"].")";
if($UserName)  $sql .=" and u.UserName like '%".$UserName."%'";
if($name)  $sql .=" and u.name like '%".$name."%'"; 
if($address) $sql .=" and u.address like '%".$address."%'";
if($startDateTime) $sql .=" and u.adddatetime>='".$startDateTime."'";
if($endDateTime) $sql .=" and u.adddatetime<'".$endDateTime."'";
if($projectID) $sql .=" and u.projectID='".$projectID."'";  
$sql .=" order by u.ID DESC";  
$result=$db->select_all("u.*,at.*,o.*,u.remark as uremark,u.ID as uID","userinfo as u,userattribute as at,orderinfo as o",$sql);
if(is_array($result)){ 
	foreach($result as $rs){
		//��ѯ���û���IP��ַ
		$rs1=$db->select_one("Value","radreply","UserName='".$rs["UserName"]."' and Attribute='Framed-IP-Address'");
		$rs2=$db->select_one("*","userrun","orderID='".$rs["orderID"]."'");
		$rs3=$db->select_one("p.name,p.ID","orderinfo as o,product as p","o.ID='".$rs["orderID"]."' and p.ID=o.productID");
	  $rs4=$db->select_one("operator","orderinfo","userID='".$rs["uID"]."' order by ID ASC ");
		echo "'".$rs["UserName"].","."'".$rs["password"].",".$rs["name"].","."'".$rs["cardid"].","."'".$rs["mobile"].",".$rs["address"].",".$rs["money"].","."'".$rs2["begindatetime"].","."'".$rs2["enddatetime"].",".$rs["projectID"].",".$rs3["ID"].",".$rs4["operator"].",".trim($rs["uremark"])."\n";
	} 
}   
?>

