#!/bin/php
<?php 
include("inc/excel_conn.php");
require_once("ros_static_ip.php"); 
MysqlBegin();//��ʼ������
$sql_a= true;    $sql_b = true;

  //���Ĳ��������û�����ɨ������
    
$result5 = $db->select_all("UserName","radacct",'AcctStopTime="0000-00-00 00:00:00"');
 if(is_array($result5)){
    foreach($result5 as $rs5){
	   $UserName = $rs5['UserName'];
	   $att = $db->select_one("status,stop,userID,orderID","userattribute","UserName='".$rs5['UserName']."'"); 
	    if($att){
		   $userID = $att['userID']; 
		   $orderID= $att['orderID'];  
		   
		   if($att['status']==4 || $att['stop']==1){
		   $run = $db->select_one("enddatetime","userrun","orderID='".$orderID."' and userID= '".$userID."'");
		   
		   if(strtotime($run['enddatetime'])<time()){//�����û�
		    //����״̬
			$sql_rs_eer = $db->query("update userrun set status = 4 where userID= '".$userID."' and orderID= '".$orderID."'");
			if(!$sql_rs_eer) $sql_a =false; 
			$sql_rs_eer = $db->query("update userattribute set status = 4 ,stop =1 where userID= '".$userID."' and orderID= '".$orderID."'");
			if(!$sql_rs_eer) $sql_b =false; 
		   //���û��������ߵ� 
			include('inc/scan_down_line.php');
			 //--------��t.php��¼���߼�¼2014.03.17----------
     $file = fopen('t.php','a');
     $name="scan_hour_flow.php*�����û�����ɨ������";
     $time=date("Y-m-d H:i:s",time())."||";
     fwrite($file,$name.$time);
     fclose($file);
 //-----------------------------------------------
			
		   }  
		   } 
		} 
	}
 
 }
 
  if( $sql_a  &&  $sql_b  ){
   MysqlCommit(); 
   echo "success";
 }else{
   MysqRoolback();
    echo "failure";//.$sql_a ."a&&". $sql_b.";
} 
 MysqlEnd();
?>
