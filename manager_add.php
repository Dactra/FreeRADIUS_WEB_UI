#!/bin/php
<?php include("inc/conn.php"); 
include_once("evn.php");  ?>
<html>
<head>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo _("系统管理组")?></title>
<link href="style/bule/bule.css" rel="stylesheet" type="text/css">
<script src="js/ajax.js" type="text/javascript"></script>
</head>
<body>
<?php 
if($_POST){
	$manager_account  =$_POST["manager_account"];
	$manager_passwd   =$_POST["manager_passwd"];
	$manager_name 	  =$_POST["manager_name"];
	$manager_phone    =$_POST["manager_phone"];
	$manager_mobile   =$_POST["manager_mobile"];
	$manager_groupID  =$_POST["manager_groupID"];	
	$manager_project  =$_POST["manager_project"];
	$addusertotalnum  =$_POST["addusertotalnum"]; 
	$manager_totalmoney =$_POST["manager_totalmoney"];
	$gRs=$db->select_one("group_permision,group_project,group_areaID,group_gradeID","managergroup","ID='".$manager_groupID."'");
	$manager_areaID  = $gRs["group_areaID"];
	$group_gradeID   = $gRs["group_gradeID"];
	$manager_project = $gRs["group_project"];
	$permsion= $gRs["group_permision"];
	$sql=array(
		"manager_account"=>$manager_account,
		"manager_passwd"=>$manager_passwd,
		"manager_name"=>$manager_name,
		"manager_phone"=>$manager_phone,
		"manager_mobile"=>$manager_mobile,
		"manager_groupID"=>$manager_groupID,
		"manager_permision"=>$permsion, 
		"addusertotalnum"=>$addusertotalnum,
		"manager_totalmoney"=>$manager_totalmoney,
		"manager_area"=>$manager_areaID,
		"manager_gradeID"=>$group_gradeID,		
		"manager_project"=>$manager_project
	);
	$db->insert_new("manager",$sql); 
	echo "<script>alert('"._("操作成功")."');window.location.href='manager.php';</script>";
}

//查询项目集合
?>
<table width="100%" height="500" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="14" height="43" valign="top" background="images/li_r6_c4.jpg"><img name="li_r4_c4" src="images/li_r4_c4.jpg" width="14" height="43" border="0" id="li_r4_c4" alt="" /></td>
    <td height="43" align="left" valign="top" background="images/li_r4_c5.jpg">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="4%" height="35"><img src="images/li1.jpg" width="39" height="22" /></td>
			<td width="96%" height="35" valign="middle"><font color="#FFFFFF" size="2"><? echo _("系统设置")?></font></td>
		  </tr>
   		</table>	</td>
    <td width="14" height="43" valign="top" background="images/li_r6_c14.jpg"><img name="li_r4_c14" src="images/li_r4_c14.jpg" width="14" height="43" border="0" id="li_r4_c14" alt="" /></td>
  </tr>
  
  <tr>
    <td width="14" background="images/li_r6_c4.jpg">&nbsp;</td>
    <td height="500" valign="top">
	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" class="title_bg2 bd">
      <tr>
        <td width="89%" class="f-bulue1"><? echo _("系统用户添加")?></td>
		<td width="11%" align="right">&nbsp;</td>
      </tr>
	  </table>
<form action="?" method="post" name="myform" onSubmit="return checkManagerAdd()" >
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="bg1" id="userinfolist">
    <tbody>
		<tr>
		<td align="right" class="bg"><? echo _("系统帐号:")?></td>
		<td align="left" class="bg"><input type="text" name="manager_account" value="<?=$rs["manager_account"]?>"></td>
		</tr> 
		<tr>
		<td width="13%" align="right" class="bg"><? echo _("所属管理组:")?></td>
		<td width="83%" align="left" class="bg">
		<?php 
				$group=$db->select_all("*","managergroup","group_name!=''");
				echo "<select name='manager_groupID'>";
				if(is_array($group)){
					foreach($group as $gKey=>$gRs){
						echo "<option value='".$gRs["ID"]."'";
						if($gRs["ID"]==$rs["manager_groupID"]) echo "selected";
						echo ">"._($gRs["group_name"])."</option>";
					}
				} 
				echo "</select>";
				 
		?>		</td>
		</tr>
		<tr>
		<td align="right" class="bg"><? echo _("帐号密码:")?></td>
		<td colspan="2" align="left" class="bg">
		<input type="text" name="manager_passwd" value="<?=$rs["manager_passwd"]?>">		</td>
		</tr> 
		<tr>
        <td align="right" class="bg"><? echo _("允许收费金额:")?></td>
        <td colspan="2" align="left" class="bg"> 
			<input type="text" name="manager_totalmoney" value="<?=$rs["manager_totalmoney"]?>"> <? echo _("元");?>	</td>     
       </tr>
		<tr>
        <td align="right" class="bg"><? echo _("允许开户人数:")?></td>
        <td colspan="2" align="left" class="bg"> 
			<input type="text" name="addusertotalnum" value="<?=$rs["addusertotalnum"]?>"> <? echo _("人");?>	</td>     
       </tr>
		<tr>
		<td align="right" class="bg"><? echo _("真实姓名:")?> </td>
		<td colspan="2" align="left" class="bg">
		<input type="text" name="manager_name" value="<?=$rs["manager_name"]?>">
		</td>
		</tr>
		<tr>
		<td align="right" class="bg"><? echo _("电话号码:")?></td>
		<td colspan="2" align="left" class="bg">  
		<input type="text" name="manager_phone" value="<?=$rs["manager_phone"]?>">		</td>    
		</tr>
		<tr>
		<td align="right" class="bg"><? echo _("手机号码:")?></td>
		<td colspan="2" align="left" class="bg"> 
		<input type="text" name="manager_mobile" value="<?=$rs["manager_mobile"]?>">		</td>     
		</tr>
		<tr>
		<td align="right" class="bg">&nbsp;</td>
		<td align="left" class="bg"><input name="submit" type="submit" onClick="javascript:return window.confirm( '<? echo _("确认提交")?>？ ');"value="<? echo _("提交")?>">        </td>
		</tr>
    </tbody>
  </table>
</form>	</td>
    <td width="14" background="images/li_r6_c14.jpg">&nbsp;</td>
  </tr>
  <tr>
    <td width="14" height="14"><img name="li_r16_c4" src="images/li_r16_c4.jpg" width="14" height="14" border="0" id="li_r16_c4" alt="" /></td>
    <td width="1327" height="14" background="images/li_r16_c5.jpg"><img name="li_r16_c5" src="images/li_r16_c5.jpg" width="100%" height="14" border="0" id="li_r16_c5" alt="" /></td>
    <td width="14" height="14"><img name="li_r16_c14" src="images/li_r16_c14.jpg" width="14" height="14" border="0" id="li_r16_c14" alt="" /></td>
  </tr>
</table>

<script language="javascript">
<!--
function permision_change(id){
	v   =document.getElementById(id).checked;
	subv=document.getElementById("sub"+id).getElementsByTagName("input");
	for(i=0;i<subv.length;i++){
		subv[i].checked=v;
	}

}
--> 
</script>
</body>
</html>
