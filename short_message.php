#!/bin/php
<?php //即将到期短信
//include("inc/conn.php");
header("content-type:text/html;charset=utf-8");
include("inc/db_config.php"); 
include("inc/class_mysql.php");
//include_once("evn.php");     
$db   = new Db_class($mysqlhost,$mysqluser,$mysqlpwd,$mysqldb);//程序
mysql_select_db($mysqldb);
mysql_query("set names utf8"); 
if(!empty($_GET['mobile'])){//单独发送信息
   $m=$_GET['mobile'].","; 
}else{
//查询即将到期的用户全部发送
$enddays         =($enddays)?$enddays:15;
$nowTime        = date("Y-m-d H:i:s",time());
$upcomingTime   = date('Y-m-d H:i:s',strtotime("$nowTime +$enddays day")); 
$sql=" u.ID=r.userID and r.enddatetime<'$upcomingTime' and r.enddatetime>='$nowTime' and r.enddatetime > now() ";  
$result=$db->select_all("u.ID,u.UserName,u.account,u.name,u.Mname,u.projectID,u.MAC,u.mobile,u.money,r.enddatetime,r.userID","userinfo as u,userrun as r",$sql); 
foreach($result as $r){
 //echo $r['mobile'].",";
  $m.= $r['mobile'].",";
  
}
}
$mobile=substr($m,0, -1);
 $rs=$db->select_one("*","message","type = 3");
 $content=$rs['client'].$rs['message'].$rs['content'];
$userid=$rs['userid'];
$account=$rs['account'];
$password=$rs['password'];
//提交短信**********************************************************************

$post_data = array();
$post_data['userid'] = $userid;
$post_data['account'] = $account;
$post_data['password'] = $password;
$post_data['content'] = $content; //短信内容需要用urlencode编码下
$post_data['mobile'] = $mobile;
$post_data['sendtime'] = ''; //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
$url='http://113.11.210.120:6666/sms.aspx?action=send';
$o='';
foreach ($post_data as $k=>$v)
{
   $o.="$k=".urlencode($v).'&';
}
$post_data=substr($o,0,-1);
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
$result = curl_exec($ch);
//echo $result;
if(strpos($result, "Success")){
    echo "发送成功";
}else{
    echo "发送失败";			
}
?>