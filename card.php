#!/bin/php
﻿<?php 
include("inc/conn.php"); 
require_once("evn.php");
date_default_timezone_set('Asia/Shanghai');
?>
<html>
<head>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo _("卡片管理")?></title>
<link href="style/bule/bule.css" rel="stylesheet" type="text/css">
<script  language="javascript" src="./js/ajax.js"></script>
<script src="js/jsdate.js" type="text/javascript"></script>
<!--这是点击帮助的脚本-2014.06.07-->
    <link href="js/jiaoben/css/chinaz.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="js/jiaoben/js/jquery-1.4.4.js"></script>   
    <script type="text/javascript" src="js/jiaoben/js/jquery-ui-1.8.1.custom.min.js"></script> 
    <script type="text/javascript" src="js/jiaoben/js/jquery.easing.1.3.js"></script>        
    <script type="text/javascript" src="js/jiaoben/js/jquery-chinaz.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {  		
        $('#Firefoxicon').click(function() {
          $('#Window1').chinaz({
            WindowTitle:          '<b>卡片管理</b>',
            WindowPositionTop:    'center',
            WindowPositionLeft:   'center',
            WindowWidth:          500,
            WindowHeight:         300,
            WindowAnimation:      'easeOutCubic'
          });
        });		
      });
    </script>
   <!--这是点击帮助的脚本-结束-->
<?php 
MysqlBegin();//开始事务定义
$sql_a = true;  $sql_b = true;	  
$ID=$_REQUEST["ID"];
unset($_SESSION['cardID']);
$_SESSION['cardID'] = $_REQUEST['ID']; 
if($_GET["action"]=="sold" && isset($ID)){ 
	if(@ strpos($ID,",")==true){ 
	$IDstr = substr($ID,0,-1);
	$IDarr = explode(",",$ID); 
		foreach($IDarr as $vaID){
			$sql=array(
				"sold"=>1,
				"solder"=>$_POST["solder"],
				"UserName"=>$_POST["UserName"],
				"soldTime"=>date("Y-m-d H:i:s",time())
			);
			$sql_rs_err = $db->update_new("card","ID='$vaID'",$sql);
			if(!$sql_rs_err) $sql_a = false ; else $sql_a = true;
			$sql_rs_err = addCardLog(1,"Sold Card:cardID-{$vaID}");	
			if(!$sql_rs_err) $sal_b = false ; else $sql_b  = true;
		}
		$ID_str=@implode("-",$IDarr);
		if($sql_a && $sql_b){
		   MysqlCommit(); 
          echo "<script language='javascript'>alert('"._("售出成功")."');</script>";
          echo "<script language='javascript'>window.location.href='card.php';window.open('card_sold_print.php?ID=".$ID_str."','newname','height=400,width=700,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no,status=no,top=100,left=300')</script>"; 
		}else{ 
		  MysqRoolback();
          echo "<script>alert('"._("操作失败")."');window.location.href='card.php';</script>"; 
		}     
	}else if($_POST['once']=='once'){
		$sql=array(
			"sold"=>1,
			"solder"=>$_POST["solder"],
			"UserName"=>$_POST["UserName"],
			"soldTime"=>date("Y-m-d H:i:s",time())
		);
		$sql_rs_err = $db->update_new("card","ID='$ID'",$sql);
		if(!$sql_rs_err) $sql_a = false ; else $sql_a = true;
		$sql_rs_err = addCardLog(1,"Sold Card:cardID-{$ID}");
		if(!$sql_rs_err) $sal_b = false ; else $sql_b  = true;	
		$ID_str=$ID;
		if($sql_a && $sql_b){
		   MysqlCommit(); 
          echo "<script language='javascript'>alert('"._("售出成功")."');</script>";
          echo "<script language='javascript'>window.location.href='card.php';window.open('card_sold_print.php?ID=".$ID_str."','newname','height=400,width=700,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no,status=no,top=100,left=300')</script>"; 
		}else{ 
		  MysqRoolback();
          echo "<script>alert('"._("操作失败")."');window.location.href='card.php';</script>"; 
		} 
      } 
}
 
MysqlEnd();//关闭连接
?>
</head>
<body>
<table width="100%" height="500" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="14" height="43" valign="top" background="images/li_r6_c4.jpg"><img name="li_r4_c4" src="images/li_r4_c4.jpg" width="14" height="43" border="0" id="li_r4_c4" alt="" /></td>
    <td height="43" align="left" valign="top" background="images/li_r4_c5.jpg">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="4%" height="35"><img src="images/li1.jpg" width="39" height="22" /></td>
			<td width="93%" height="35" valign="middle"><font color="#FFFFFF" size="2"><? echo _("卡片管理")?></font></td>
                        <td width="3%" height="35">
                           <div id="Firefoxicon" class="bz" style="text-align:right; cursor: pointer; color:#FFF; line-height: 35px; ">帮助<img src="/js/jiaoben/images/bz.jpg" width="20" height="20"  title="帮助" style="vertical-align:middle;"/></div>
                       </td> <!------帮助--2014.06.07----->
                       
		  </tr>
   		</table>
	</td>
    <td width="14" height="43" valign="top" background="images/li_r6_c14.jpg"><img name="li_r4_c14" src="images/li_r4_c14.jpg" width="14" height="43" border="0" id="li_r4_c14" alt="" /></td>
  </tr>
  
  <tr>
    <td width="14" background="images/li_r6_c4.jpg">&nbsp;</td>
    <td height="500" valign="top">
	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" class="title_bg2 bd">
	<tr>
	<td width="89%" class="f-bulue1"><? echo _("卡片销售")?></td>
	<td width="11%" align="right">&nbsp;</td>
	</tr>
	</table>
		<table width="100%" border="0"  cellpadding="3" cellspacing="1" class="bg1" > 
	<?php 
	 if($_GET['action']=='sold'){//卡片销售 
	     $ID    = $_REQUEST["ID"];
		 if(is_array($ID)){ 
		 	  $IDstr = implode(",",$ID);   
			 foreach($ID as $val){ 
				$rs= $db->select_one("*","card","ID='".$val."'");
				$totalMoney +=$rs["money"];
			 }//end foreach   
		  }else{ 
		    $rs= $db->select_one("*","card","ID='".$ID."'"); 
			$IDstr = $ID ;
			$totalMoney = $rs["money"]; 
		 }
		 ?> 
		  <form action="?action=sold" method="post" name="myform"><!-- onSubmit="return checkCardSold();"-->
		    <?php
			  if(!is_array($_REQUEST['ID'])){
			    echo "<input type='hidden' value='once' name='once' >";
			  }
			?>
		    <input  type="hidden" value="<?=$IDstr?>" id="ID" name="ID" >
	      <tr> 
		   <td width="13%" align="right" class="bg"><? echo _("购买用户")?>: </td> 
		   <td width="87%" align="left" class="bg"><input type="text" id="UserName" name="UserName" onBlur="this.className='input_out';" ><!--ajaxInput('ajax_check.php','card_account','UserName','accountTXT');<span id="accountTXT"></span>--> </td> 
	      </tr>
		  <tr> 
		    <td width="13%" align="right" class="bg"> <? echo _("卡片金额")?>:</td> 
		    <td width="87%" align="left" class="bg"><input type="text"  disabled="disabled" readonly="readonly"  onFocus="this.className='input_on'" onBlur="this.className='input_out';" value="<?=$totalMoney?>"> </td> 
	      </tr> 
		  <tr> 
		    <td width="13%" align="right" class="bg"> <? echo _("销售人员")?>:</td> 
		    <td width="87%" align="left" class="bg">
			<input type="hidden" value="<?=$_SESSION["manager"];?>"	 id="solder" name="solder">
			<input type="text"   disabled="disabled" readonly="readonly" class="input_out" onFocus="this.className='input_on'" onBlur="this.className='input_out';" value="<?=$_SESSION['manager']?>"> </td> 
	      </tr> 
		   <tr> 
		    <td width="13%" align="right" class="bg" colspan="3">&nbsp;  </td> 
	      </tr>  
		  <tr> 
		    <td width="13%" align="right" class="bg">&nbsp;</td> 
		    <td width="87%" align="left" class="bg"><input type="submit" value="<? echo _("提交");?>" onClick="javascript:return window.confirm('<? echo _("确认提交");?>？');"> </td> 
			
	      </tr> 
		 </form>
		 </table>
		 <?php  
	 }else{ //卡片查询
	 
	 $cardNumber    = $_REQUEST["cardNumber"];
     $startDateTime = $_REQUEST["startDateTime"];
	 $startDateTime1= $_REQUEST["startDateTime1"];
	 $endDateTime   = $_REQUEST['endDateTime'];
	 $endDateTime1  = $_REQUEST['endDateTime1'];
	 $operator      = $_REQUEST["operator"];
	 $querystring     ="cardNumber=".$cardNumber."&startDateTime=".$startDateTime."&startDateTime1=".$startDateTime1."&endDateTime=".$endDateTime."&endDateTime1=".$endDateTime1."&operator=".$operator;
	?>
	<form action="?action=search" name="iform" method="post">
	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" class="bd"> 
	  <tr>
	  	<td align="right"><? echo _("充值卡号")?>:</td>
		<td><input type="text" name="cardNumber" value="<?=$cardNumber?>"></td>
	  </tr>
	    <tr>
	    <td align="right"><? echo _("生成时间")?>:</td>
	    <td>
		<input type="text" name="startDateTime" onFocus="HS_setDate(this)"value="<?=$startDateTime?>">
	    <? echo _("至")?>
		<input type="text" name="startDateTime1" onFocus="HS_setDate(this)" value="<?=$startDateTime1?>">		 </td>
	    </tr>
	  <tr>
	    <td align="right"><? echo _("失效时间")?>:</td>
	    <td>
		<input type="text" name="endDateTime" onFocus="HS_setDate(this)" value="<?=$endDateTime?>">
		<? echo _("至")?>
		<input type="text" name="endDateTime1" onFocus="HS_setDate(this)" value="<?=$endDateTime1?>">		</td>
	    </tr>
		 <tr>
	    <td align="right"><? echo _("制 卡 员:")?></td>
	    <td><?php managerSelect($operator)?></td>
	    </tr>
	  <tr>
	    <tr>
	    <td align="right">&nbsp;</td>
	    <td><input type="submit" value="<? echo _("提交")?>">
		</td>
		</tr>
	  </table>
	  </form>
	
	<table width="100%" border="0"  cellpadding="3" cellspacing="1" class="bg1" id="myTable">
	<form action="?action=sold"  method="post" name="myform"> 
	<thead>
		  <tr>
			<th width="4%" align="center" class="bg f-12"><input type="checkbox" name="allID" title="<? echo _("全选")?>" onClick="change_allID();"></th>
			<th width="17%" align="center" class="bg f-12"><? echo _("卡号")?></th>
			<th width="23%" align="center" class="bg f-12"><? echo _("密码")?></th>
			<th width="8%" align="center" class="bg f-12"><? echo _("金额(元)")?></th>
			<th width="13%" align="center" class="bg f-12"><? echo _("操作时间")?></th>
			<th width="12%" align="center" class="bg f-12"><? echo _("失效时间")?></th>
			<th width="18%" align="center" class="bg f-12"><? echo _("备注")?></th>
			<th width="5%" align="center" class="bg f-12"><? echo _("操作")?></th>
		  </tr>
	</thead>	     
	<tbody id="card_all">  
	<?php 

	
	if(isset($_GET["showNum"])){
		if($_GET["showNum"]=="0"){
			$showNum="";
		}else{
			$showNum=$_GET["showNum"];
		}
	}else{
		$showNum=20;
	}
	$sql=" sold=0  "; 
	if($cardNumber){
		$sql .=" and cardNumber like '%".mysql_real_escape_string($cardNumber)."%'";
	}
	if($startDateTime){
		$sql .=" and cardAddTime>='".$startDateTime."'";
	}
	if($startDateTime1){
		$sql .=" and cardAddTime<'".$startDateTime1."'";
	}
	if($endDateTime){
		$sql .=" and ivalidTime>='".$endDateTime."'";
	}
	if($endDateTime1){
		$sql .=" and ivalidTime<'".$endDateTime1."'";
	}
	if($operator){
		$sql .=" and operator='".$operator."'";
	}
	    $sql .=" order by ID desc" ;
	$result=$db->select_all("*","card",$sql,$showNum);
	if(is_array($result)){
	foreach($result as $key=>$rs){
	?>   
	  <tr>
		<td align="center" class="bg"><input type="checkbox" name="ID[]" value="<?=$rs['ID'];?>"></td>
		<td align="center" class="bg"><?=$rs['cardNumber'];?></td>
		<td align="center" class="bg"><?=$rs["actviation"]?></td>
		<td align="center" class="bg"><?=$rs["money"]?></td>
		<td align="center" class="bg"><?=$rs["cardAddTime"]?></td>
		<td align="center" class="bg"><?=$rs['ivalidTime'];?></td>
		<td align="center" class="bg"><?=$rs["remark"]?></td>
		<td align="center" class="bg"><a href="?action=sold&ID=<?=$rs["ID"]?>"><? echo _("销售")?></a> </td>
	  </tr>
	<?php  }} ?>
	</tbody>   
	<tbody>
		  <tr>
			<th colspan="8" align="left" class="bg f-12">
				<input type="submit" value="<? echo _("批量销售")?>">
				<? echo _("显示条数:")?> 
				<a href="?showNum=50">50</a> <a href="?showNum=100">100</a> <a href="?showNum=200">200</a> <a href="?showNum=0"><? echo _("全部")?></a> 
			</th>
		  </tr>
	</tbody>   
	</table>
	<table width="100%" border="0" cellpadding="5" cellspacing="0"  class="bg1">
	<tr>
		<td align="center" class="bg">
			<?php $db->page($querystring); ?>			
		</td>
	</tr>
	
	</form>
	</table>
 <?php 
	}
?> 
	</td>
    <td width="14" background="images/li_r6_c14.jpg">&nbsp;</td>
  </tr>
 
  <tr>
    <td width="14" height="14"><img name="li_r16_c4" src="images/li_r16_c4.jpg" width="14" height="14" border="0" id="li_r16_c4" alt="" /></td>
    <td width="1327" height="14" background="images/li_r16_c5.jpg"><img name="li_r16_c5" src="images/li_r16_c5.jpg" width="100%" height="14" border="0" id="li_r16_c5" alt="" /></td>
    <td width="14" height="14"><img name="li_r16_c14" src="images/li_r16_c14.jpg" width="14" height="14" border="0" id="li_r16_c14" alt="" /></td>
  </tr>
  
</table>
    <!-----------这里是点击帮助时显示的脚本--2014.06.07----------->
 <div id="Window1" style="display:none;">
      <p>
        卡片管理-> <strong>卡片销售</strong>
      </p>
      <ul>
          <li>对已经生成的充值卡进行销售。</li>
          <li>提供充值卡批量销售并打印的功能，并生成记录。</li>
      </ul>

    </div>
<!---------------------------------------------->
<script language="javascript">
<!--
function change_allID(){
	ide=document.myform.allID.checked;
	div=document.getElementById("myTable").getElementsByTagName("input");
	for(i=0;i<div.length;i++){
		div[i].checked=ide;
	}
}
-->
</script>
</body>
</html>

