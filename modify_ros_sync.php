#!/bin/php
<?php
//include("inc/db_config.php");
//include("inc/routeros_api.class.php");
//include("inc/routeros_api_function.php"); 
include_once("evn.php"); 

function sync_ros_one_user($UserName) {
	if ( !$UserName ) {
		return false;
	}
	global $mysqlhost, $mysqluser, $mysqlpwd, $mysqldb;
	/* ����MySQL */
	$conn   = mysql_connect($mysqlhost, $mysqluser, $mysqlpwd);
	if ( !$conn ) {
		echo "MySQL���Ӵ���(MySQL�����ַ��{$mysqlhost},�û�����{$mysqluser})!!!\n";
		exit(-1);
	}
	
	/* ѡ���("radius") */
	mysql_select_db($mysqldb);
	
	/* ��������ROS�ľ�� */
	$ROS = new routeros_api();
	
	/* �����û�����ȡ���룬��ƷID����ĿID��IPaddress*/
	$sql = "select userinfo.password, userinfo.projectID, radreply.Value, orderinfo.productID from userinfo, radreply, orderinfo where userinfo.UserName='$UserName' and userinfo.ID= radreply.UserID and radreply.Attribute='Framed-IP-Address' and userinfo.ID=orderinfo.userID and orderinfo.status='1';";
	$result=mysql_query($sql,$conn);
	$row = @mysql_fetch_array($result);
	/* û������IPaddress�������û�����ȡ���룬��ƷID����ĿID */
	if ( !$row ) {
	  $sql = "select userinfo.password, userinfo.projectID, orderinfo.productID from userinfo, orderinfo where userinfo.UserName='$UserName' and userinfo.ID=orderinfo.userID and orderinfo.status='1';;";	
	  $result=mysql_query($sql,$conn);
	  $row = @mysql_fetch_array($result);
	}
	
	/* ������ĿID����ȡ��Ҫͬ����ros��ص���Ϣ */
	$sql = "select nasip, username, password from `project_ros` where projectID='{$row['projectID']}'";
	$sync_ros_info = mysql_query($sql,$conn);
	while( $sync_ros_ = @mysql_fetch_array($sync_ros_info) ) {
	  if ( !connect_ros( $ROS, $sync_ros_['nasip'], $sync_ros_['username'], $sync_ros_['password'] ) ) {
	    echo "ROS(IP:{$row['nasip']} �û�:{$row['username']})����ʧ��!!!\n";
	    continue;
	  }    	
	  
	  /* ���ݲ�ƷID�� ��������д��� */
	  $sql = "select upbandwidth, downbandwidth from product where ID='{$row['productID']}';";	
	  $bandwidth = mysql_query($sql,$conn);
	  while( $row_bandwidth = @mysql_fetch_array($bandwidth) ) {
			$bandwidth = "{$row_bandwidth['upbandwidth']}k/{$row_bandwidth['downbandwidth']}k";
			/* ���ݴ�����ROS�豸�����ɴ���������ļ� */
			addpppprofile($ROS, $row['productID'], $bandwidth);	  	
	  }	
	  /* ͬ���û� */
	  addpppuser($ROS,$UserName,$row['password'],$row['Value'], $row['productID']);  
	  disconnect_ros( $ROS ); 	  	  
	}
}
?> 

