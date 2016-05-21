#!/bin/php
<?php 
include("inc/scan_conn.php");  
include_once("evn.php");
/*
**
 ************************************
 * 文件名:  scan_everyone_hour.php
 * 文件描述: 是针对包小时用户扫描
 * 创建人:  yuan 
 * 创建日期:  2014.03.22
 * 版本号:   3.1
 * 修改记录: RealNatInf 03/25 2014
 ************************************
 */ 
$accResult=$db->select_all("*","radacct","AcctStopTime='0000-00-00 00:00:00'");
if($accResult){
	foreach($accResult as $accKey=>$accRs){//view online user 
		$AcctStartTime   =$accRs["AcctStartTime"];//上线开始时间
		$AcctSessionTime =$accRs["AcctSessionTime"];//在线时间
		$AcctInputOctets =$accRs["AcctInputOctets"];
		$AcctOutputOctets=$accRs["AcctOutputOctets"];		
		$UserName        =$accRs["UserName"];
		$userID          =getUserID($accRs["UserName"]);//得到用户编号
		//但是只查询出是计时，计流量的用户
		  //$pRs=$db->select_one("o.*,o.ID as orderID,p.*,p.ID as productID","orderinfo as o,product as p","o.productID=p.ID and (o.status in (1,2) ) and o.userID='$userID' and (p.type='hour' or p.type='flow')");
		//针对小时用户
		 $pRs=$db->select_one("o.*,o.ID as orderID,p.*,p.ID as productID","orderinfo as o,product as p","o.productID=p.ID and (o.status in (1,2) ) and o.userID='$userID' and  p.type='hour' ");
              
                 if($pRs){//判断此用户是否是符合要求
		 	 $productID   =$pRs["productID"];
		 	 $orderID     =$pRs["orderID"];
		 	 $periodValue =$pRs["period"];
			 $type        =$pRs["type"];
                         $pPrice       =$pRs["price"];
			 $creditline  =$pRs["creditline"];//信誉值
			 $capping     =$pRs["capping"];//封顶
			 $unitprice   =$pRs["unitprice"];
			 if($type=="hour"){
			 	$periodValue=$periodValue*3600;//换成秒,套餐总共的周期
				$unitprice  =$unitprice/3600;//秒为单位的费率
				$onlineData =$AcctSessionTime;//计时
			 }else if($type=="flow"){
			 	$periodValue=$periodValue*1024;//流量的换算 KB，套餐总共的流量
				$unitprice  =$unitprice/1024;//这是以KB为单位的费率
				$onlineData =$AcctInputOctets+$AcctOutputOctets;//讲流量的是又上传流量+下载流量的
				$onlineDate =($onlineDate/8)*1024; //这里是把字节换算成KB
			 }	
			 /**
			  *第一步操作判断用户是否超出了用户限制 
			  *
			  */				  
			 //统计当前用户的总值
			 $tTotal      =$db->select_one("sum(stats) as useValue","runinfo","userID='$userID' and orderID='$orderID'");
			 $tTotalStats =$tTotal["useValue"];			 
			 $tRs         =$db->select_one("*","runinfo","userID='$userID' and orderID='$orderID' and adddatetime='$AcctStartTime'");//查询用户的此订单的当前运行记录    
			 if($tRs){
				if($tTotalStats<$periodValue){//还没有超出限制
					$db->query("update runinfo set stats='$onlineData' where userID='$userID' and orderID='$orderID' and adddatetime='$AcctStartTime' ");//只更新记录当天的	
					$nTotal      =$db->select_one("sum(stats) as useValue","runinfo","userID='$userID' and orderID='$orderID'");
                                       $nTotalStats =(int)$nTotal["useValue"];//消费时长	
                                       $nowperiod= $pPrice/$periodValue ;//当前费率     
                                       $balance =$pPrice-$nowperiod*$nTotalStats;//用户余额 = 产品价格 - 消费时长*费率
                                        $db->query("update userrun set balance='$balance' where userID='$userID' and orderID='$orderID' ");					
				}else{//超出限制后
					$userRs = $db->select_one("money","userinfo","ID='".$userID."'");
					 $userMoney=(float)$userRs["money"];
					if($userMoney<=0) {//用户帐号没有钱了
						$db->update_new("orderinfo","ID='".$orderID."'",array("status"=>4));
						$db->update_new("userrun","userID='".$userID."' and orderID=".$orderID."",array("status"=>4,"balance"=>0,"enddatetime"=>date("Y-m-d H:i:s",time())));//更新余额、状态结束时间
						//更新用户的属性条件,这是方便拨号时验证
						updateUserAttribute($userID,array("status"=>4,"stop"=>1));
						//加写订单日志						
						addOrderLogInfo($userID,$orderID,"4",$_SERVER['REQUEST_URI'],"SYSTEM_systemScanOrder");	 					
						//把用户级踢下线的
						include('inc/scan_down_line.php');
                                                //--------在t.php记录下线记录2014.03.17----------
                                            $file = fopen('t.php','a');
                                            $name="scan_everyone_hour.php*计时用户余额不足踢下线";
                                            $time=date("Y-m-d H:i:s",time())."||";
                                            fwrite($file,$name.$time);
                                            fclose($file);
                                            //-----------------------------------------------
					} else {
                                             $db->update_new("userrun","userID='".$userID."' and orderID=".$orderID."",array("balance"=>0));//更新套餐余额
                                                $nowPrice=(float)($onlineData-$tRs["stats"])*$unitprice;//这是本次应当扣去的费用
                                                $nowPrice= number_format($nowPrice, 2, '.', ''); 
						$price   =(float)$unitprice*$onlineData;//算出当次在线有所有费用
					 	$userMoney=$userMoney-$nowPrice;
                                                if($userMoney<0){
                                                  $userMoney=0;  
                                                }
						$db->query("update userinfo set money=$userMoney where ID='$userID'");
						$db->query("update runinfo set stats='$onlineData',price='$price' where userID='$userID' and orderID='$orderID' and adddatetime='$AcctStartTime'");					
					}

					 
//					 if($capping<=0){//封顶金额为0时间
//					 
//						//设置订单
//						$db->update_new("orderinfo","ID='".$orderID."'",array("status"=>4));
//						$db->update_new("userrun","userID='".$userID."' and orderID=".$orderID."",array("status"=>4));
//						
//						//更新用户的属性条件,这是方便拨号时验证
//						updateUserAttribute($userID,array("status"=>4,"stop"=>1));
//						//加写订单日志						
//						addOrderLogInfo($userID,$orderID,"4",$_SERVER['REQUEST_URI'],"SYSTEM_systemScanOrder");	 					
//						//把用户级踢下线的
//						include('inc/scan_down_line.php');	
//																 	
//					 }else if($capping<$moneyTotal){//表示达到封顶金额，不开始计费
//					 	//$db->query("update runinfo set stats='$onlineData' where userID='$userID' and orderID='$orderID' and adddatetime='$AcctStartTime'");//只更新记录当天的
//						//设置订单
//						$db->update_new("orderinfo","ID='".$orderID."'",array("status"=>4));
//						$db->update_new("userrun","userID='".$userID."' and orderID=".$orderID."",array("status"=>4));
//
//						//更新用户的属性条件,这是方便拨号时验证
//						updateUserAttribute($userID,array("status"=>4,"stop"=>1));
//						//加写订单日志						
//						addOrderLogInfo($userID,$orderID,"4",$_SERVER['REQUEST_URI'],"SYSTEM_systemScanOrder");	 						
//						//把用户级踢下线的
//						include('inc/scan_down_line.php');	
//											
//					 }else{
//						//***********这里是要算出每次扣费操作，要总价格减去，之前扣过的费用得到本次应该扣去的费用
//						$nowPrice=($onlineData-$tRs["stats"])*$unitprice;//这是本次应当扣去的费用
//						$price   =$unitprice*$onlineData;//算出当次在线有所有费用
//						
//						$db->query("update userinfo set money=money-$nowPrice where ID='$userID'");
//						$db->query("update runinfo set stats='$onlineData',price='$price' where userID='$userID' and orderID='$orderID' and adddatetime='$AcctStartTime'");
//					 }
//					 
//					 
				}
			 }else{//当不存在，表示此用户第一次上线的
			 	$sql=array(
					"userID"=>$userID,
					"orderID"=>$orderID,
					"stats"=>$onlineData,
					"price"=>0,
					"adddatetime"=>$AcctStartTime
				);
			 	$db->insert_new("runinfo",$sql);
			 }// end $tRs	 	 
		 }//end $pRs	
		 
		 /**
		  * 第二步是判断用户是否离线了
		  * 
		  */
		 user_is_offline($userID);  
		  
	}//end foreach
}
?>
