#!/bin/php
<?php
/*
 **�ļ���: sync_radius2ros 
 **����:   ͬ���Ʒ��û���Ϣ��ROS
 **��ע:   inc/db_config.php             MySQL������Ϣ(�û��������룬IP��ַ�����)
 **        inc/routeros_api.class.php    ROS��API�ļ�
 **        inc/routeros_api_function.php ROS��API�����������ļ�
 **        project_ros����Ϣ
			CREATE TABLE `project_ros` (
				`ID` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
				`nasip` VARCHAR(16) NULL DEFAULT NULL,
				`username` VARCHAR(16) NULL DEFAULT NULL,
				`password` VARCHAR(256) NULL DEFAULT NULL,
				`projectID` INT(4) NULL DEFAULT NULL,
				PRIMARY KEY (`ID`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT
			AUTO_INCREMENT=27 
*/

include("inc/routeros_api.class.php");
include("inc/routeros_api_function.php");

$font_start="<font color='red'>";
$font_end="</font>";
/* ����MySQL */
$conn   =mysql_connect($mysqlhost, $mysqluser, $mysqlpwd);
if ( !$conn ) {
	echo "{$font_start}MySQL���Ӵ���(MySQL�����ַ��{$mysqlhost},�û�����{$mysqluser})!!!{$font_end}<hr>\n";
	exit(-1);
}

/* ѡ���("radius") */
mysql_select_db($mysqldb);

/* ��������ROS�ľ�� */
$ROS = new routeros_api();

/* ������Ҫͬ����ROS��������Ŀ����ȡ��project_ros */
$sql="select * from project_ros";
$result=mysql_query($sql,$conn);
while( $row=mysql_fetch_array($result) ) {
	 /* ���ݲ�ѯ����ROS��ַ���û������������ӵ�ROS */
	 echo "{$font_start}��������{$font_end}<hr>\n";
	 
	 /* ����ʧ�ܣ�������һ����Ŀ������ */
	 if ( !connect_ros( $ROS, $row['nasip'], $row['username'], $row['password'] ) ) {
	   echo "{$font_start}ROS(IP:{$row['nasip']} �û�:{$row['username']})����ʧ��!!!<$font_end><hr>\n";
	   continue;
 	 }
 	 
 	 /* ���ӳɹ� */
	 echo "{$font_start}ROS(IP:{$row['nasip']} �û�:{$row['username']})���ӳɹ�����ʼͬ��!!!{$font_end}<hr>\n";
	 if ( 0 != strcmp("{$row['nasip']}-yes", $status) ) {
		/* ɾ�����е�ROS�ϵ��û� */
		echo "{$font_start}��һ��:ɾ��ROS({$row['nasip']})�����û�{$font_end}<hr>\n";
		/* ���������Щ���� */
		delallpppuser($ROS);	 	
	 }

   /* ȷ��һ����ַֻɾ��һ�������û��ı�־λ */
	 $status = "{$row['nasip']}-yes";
	
	/* ���Ҹ���Ŀ��Ӧ�Ĳ�Ʒ */
	echo "{$font_start}�ڶ���:ͬ���û�{$font_end}<hr>\n";
	
	/* ������Ŀid���Ҷ�Ӧ�Ĳ�Ʒid(һ����Ŀ�ɶ�Ӧ�����Ʒ),��ȡ��Ŀ�Ͳ�Ʒ�Ĺ�ϵ��productandproject��productID�ֶ� */
	$sql="select productID from productandproject where projectID='{$row['projectID']}'";	
	$get_product_id = mysql_query($sql,$conn);
	while( $product_id = mysql_fetch_array($get_product_id) ) {
		/* ����ͳ��һ����Ŀͬ���˶��ٺϷ��û��ı�־λ */
		$sum = 0;
		
		/* ���ݻ�õĲ�ƷID��ѯ��Ʒ�������д���ֵ����ȡ��product��upbandwidth��downbandwidth�ֶ� */
		$sql = "select upbandwidth, downbandwidth from product where ID='{$product_id['productID']}'";
		$get_bandwidth = mysql_query($sql,$conn);
		
		/* ���ɴ���������ļ� start while */
		while( $bandwidth_info = mysql_fetch_array($get_bandwidth) ) {
			/* ��������ϳ�ros���ϵø�ʽ */
			$bandwidth = "{$bandwidth_info['upbandwidth']}k/{$bandwidth_info['downbandwidth']}k";
			/* ���ݴ�����ROS�豸�����ɴ���������ļ� */
			addpppprofile($ROS, $product_id['productID'], $bandwidth);		
		} /* ���ɴ���������ļ� end while */
		
		/* �����û������ƣ����룬��ƷID */
		/** 
		 ** ����: 
		 **     1 userinfo.projectID= $row['projectID']  $row['projectID']->project_ros��������ѯ������Ŀid 
		 **     2 userattribute.userID = uesrinfo.ID
		 **     3 userattribute.orderID = orderinfo.ID
		 **     4 orderinfo.productID = product.ID
		 **     5 orderinfo.productID = $product_id['productID'] $product_id['productID']->��ѯ���Ĳ�ƷID 
		*/
   $sql = "select u.UserName, u.password, o.productID from userinfo as u,`userattribute` as at, 
   orderinfo as o, product as p where u.projectID='{$row['projectID']}' 
   and at.userID=u.ID and at.orderID=o.ID and o.productID=p.ID and  o.productID='{$product_id['productID']}'";
   $get_userinfo = mysql_query($sql,$conn);
   

   /* �����û������ƣ����룬��ƷID��IP��ַ��� start while */
	 while( $userinfo = mysql_fetch_array($get_userinfo) ) {   
     /* �����û����û��������û���IP��ַ */
     $sql = "select radreply.Value from userinfo, radreply 
     where userinfo.ID= radreply.UserID and radreply.Attribute='Framed-IP-Address' and userinfo.UserName = '{$userinfo['UserName']}'";
     $get_users_ipaddress = mysql_query($sql,$conn);
     $usersipaddress = mysql_fetch_array($get_users_ipaddress);
     
     $sql = "select UserName from userattribute where stop='1' and UserName='{$userinfo['UserName']}'";
     $get_bad_user = mysql_query($sql,$conn);
     $bad_user = mysql_fetch_array($get_bad_user);
     if ( !$bad_user ) {
       /* ��ROS��ͬ���û���Ϣ */
       echo "ͬ���û�(<font color='blue'><B>{$userinfo['UserName']}</B></font>)<hr>\n";
       addpppuser($ROS,$userinfo['UserName'],$userinfo['password'],$usersipaddress['Value'], $userinfo['productID']);  
       $sum++;  
     } else {    	   
//			 if(findpppuser($ROS,$userinfo['UserName'])<>-1){
//			    //������û����ڣ���ɾ��
//			    delpppuser($ROS,$userinfo['UserName']);  
//			 }     	
     }
   } /* �����û������ƣ����룬��ƷID��IP��ַ��� end while */  
  } /* ���Ҹ���Ŀ��Ӧ�Ĳ�Ʒ��� end while */ 
	echo "{$font_start}������:ͬ����ɣ���ͬ���û���{$sum}<hr><br>\n�Ͽ�ROS(IP:{$row['nasip']})����{$font_end}<hr><br>\n\n";
	/* ͬ����ɣ��Ͽ�ROS���� */
	disconnect_ros( $ROS );  
} /* ����ROS��������Ŀ��� */



/* ͬ�������û���Ϣ */
/*
echo "{$font_start}������:��⵽���û�����ROS��ɾ�������û�{$font_end}<hr>\n";
$sql = "select UserName from userattribute where stop='1'";
$result=mysql_query($sql,$conn);
while( $row=mysql_fetch_array($result) ) {
	if ( $row['UserName'] ) {
		 echo "�û�(<font color='blue'><B>{$row['UserName']}</B></font>)�Ѿ�����<hr>\n";
		 delpppuser($ROS,$row['UserName']);
	}
}
*/
//echo "{$font_start}������:ͬ����ɣ��Ͽ�ROS����{$font_end}<hr>\n";
/* ͬ����ɣ��Ͽ�ROS���� */
//disconnect_ros( $ROS );
?> 

