
#!/usr/local/bin/php
<?php

require("guiconfig.inc");

$pgtitle = array(gettext("Services"), gettext("UPS"));
exec("rm -f  /usr/local/www/backup");
if($_GET){
	if($_GET['action'] == "del"){
		exec("rm -rf /mnt/mysql/backup/".$_GET['file']);
		Header("Location: services_mysql.php");
		exit;
	}
	if($_GET['action'] == "download"){
		exec("/bin/ln -s /mnt/mysql/backup backup");
		Header("Location: /backup/".$_GET['file']);
		exit;
	}	
	
	
}


$mysql = &$config['radius'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if ($_POST['backup']) {
		if(file_exists("/mnt/mysql/backup")){
			exec("/usr/local/bin/mysqldump radius > /mnt/mysql/backup/".date("YmdHis").".sql");
		}else{
			exec("mkdir /mnt/mysql/backup");
			exec("/usr/local/bin/mysqldump radius > /mnt/mysql/backup/".date("YmdHis").".sql");
		}
	}

	if (!$input_errors) {
		echo "";
		}

		$savemsg = get_std_save_message($retval);
		
		
	if($_POST['save']){
		$mysql["ip"]       = $_POST["ip"];
		$mysql["database"] = $_POST["database"];
		$mysql["user"]     = $_POST["user"];
		$mysql["password"]     = $_POST["password"];
		write_config();
		
		$str="<?php
			global $mysqlhost, $mysqluser, $mysqlpwd, $mysqldb;
			$mysqlhost= '".$_POST["ip"]."';//  localhost;
			$mysqluser='".$_POST["user"]."';//  login name
			$mysqlpwd='".$_POST["password"]."';//  password
			$mysqldb='".$_POST["database"]."';//name of database
		?>";	
		echo $str;
		echo "OK";
		$fp=fopen("/usr/local/user-gui/inc/db_config.php","w+");
		if($fp){
			fwrite($fp,$str);
		}
		fclose($fp);		

	}
}

?>
<?php include("fbegin.inc");?>

<form action="services_mysql.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  				  	<tr>
						<td colspan="2" valign="top" class="listtopic">���ݿ�����</td>
					</tr>
			  	
					<? //php html_passwordbox("root_password", "root�û�����", "", "MySQL����root�û�������.��������Ҫ�߶ȱ���", true);?>
					<? //php html_passwordbox("radius_password", "radius�û�����", "", "MySQL����radius�û�������.��������Ҫ�߶ȱ���",  true);?>
					
					
					<tr><td class="data_td"><b>����˵�ַ</b> </td>
				<td class="data_td"><input type="text" value="<?=$mysql["ip"]?>" name="ip"  id="ip" />
				
				</td></tr>
				
				<tr><td class="data_td"><b>���ݿ���</b> </td>
				<td class="data_td"><input type="text" value="<?=$mysql["database"]?>" name="database"  id="database"/>
				
				</td></tr>
				<?  //�������ݿ��û���  ?>
				<tr><td class="data_td"><b>�û�����</b> </td>
				<td class="data_td"><input type="text" value="<?=$mysql["user"]?>" name="user" id="user" />
				
				</td></tr>
				<?  //�������ݿ�����  ?>
				<tr><td class="data_td"><b>�û�����</b> </td>
				<td class="data_td"><input  type="password" value="<?=$mysql["password"]?>"  name="password"  id="password"/>
				
				</td></tr>
				
					
					<tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="save" type="submit" class="formbtn" value="����" onClick="chang(),enable_change(true)">
			      </td>
			    </tr>
			    <tr>
							<td colspan="2" class="listtopic">����/�ָ�</td>
					
					</tr>
					<tr>
							<td width="22%" class="listr">�ָ�����</td>
							<td width="78%" class="listr"><input name="ulfile" type="file" class="formfld" size="40">&nbsp;&nbsp;&nbsp;
								<input name="restore" type="submit"  value="�ָ�" >
								
								
								</td>
					</tr>
					<tr>
							<td width="22%" class="listr">�������ݿ�</td>
							<td width="78%" class="listr"><input name="backup" type="submit"  value="����" ></td>
					</tr>					
<?php
		if(file_exists("/mnt/mysql/backup")){
			$handle=opendir('/mnt/mysql/backup');
		}else{
			exec("mkdir /mnt/mysql/backup");
			$handle=opendir('/mnt/mysql/backup');
		}


while ($file = readdir($handle)) {
	if($file <> "." and $file <> ".."){
?>
					<tr>
							<td width="22%" class="listr"><?=$file;?></td>
							<td width="78%" class="listr">
								�ļ���С��<?=filesize("/mnt/mysql/backup/".$file);?>&nbsp;&nbsp;&nbsp;���ڣ�<?=date("Y-m-d H:i:s",filectime("/mnt/mysql/backup/".$file));?>
								&nbsp;&nbsp;&nbsp;&nbsp;<a href="?action=del&file=<?=$file; ?>">ɾ��</a>&nbsp;&nbsp;&nbsp;<a href="?action=download&file=<?=$file; ?>">����</a></td>
					</tr>		
	
<?	
	
  }
}
closedir($handle); 
?>
					
			  </table>
			</td>
		</tr>
	</table>
</form>

<?php include("fend.inc");?>

<script>

function chang(){
  var localhost =document.getElementById('localhost').value;//����˵�ַ
 
var database=document.getElementById('database').value;//���ݿ���

var database_name=document.getElementById('database_name').value;//���ݿ��û���
 
var database_password=document.getElementById('database_password').value;//���ݿ��û�����
 
if(localhost==""){
		alert("����˵�ַ����Ϊ��");
		
	}
else if(database==""){
		alert("���ݿ�������Ϊ��");
		
	}
else if(database_name==""){
		alert("���ݿ��û�������Ϊ��");
		
	}
/*else if(database_password==""){
		alert("���ݿ����벻��Ϊ��");
		
	}*/
	else{
		<?
		
		//ʵ�������ݿ����
	$db_config="<?php 
global \$mysqlhost, \$mysqluser, \$mysqlpwd, \$mysqldb;
\$mysqlhost= \"".$_POST["localhost"]."\";//  localhost;
\$mysqluser=\"".$_POST["database_name"]."\";//  login name
\$mysqlpwd=\"".$_POST["database_password"]."\";//  password
\$mysqldb=\"".$_POST["database"]."\";//name of database

 ?>";


		$fp = fopen("../user-gui/inc/db_config.php","w");
		
		fwrite($fp,$db_config);  //���ļ�����Դ����   �ڶ�����������Ҫд�������

		fclose($fp);  //�ر���
		
		
		
		 ?>
	
	
	}
	
}


</script>
