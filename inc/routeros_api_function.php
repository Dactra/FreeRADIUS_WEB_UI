<?php
//require('routeros_api.class.php');


//$ROS = new routeros_api();

@$ROS->debug = false;

/*
 **������: connect_ros 
 **����:   $ROS->����ROS�ľ��; 
 **        $ipaddress->ROS��IP��ַ;
 **        $username->ROS���û���
 **        $password-��ROS���û�������
 **����:   ����ROS����ͬ������
 **��ע:
*/
function connect_ros( $ROS, $ipaddress, $username, $password ) {
	if ( $ROS->connect(trim($ipaddress), trim($username), trim($password)) ) {
    //echo "���ӳɹ�, ���ݿ�����...";
    return true;	
	}else{
	  //echo "���Ӵ���";
	  return false;
	  //echo "connected error!!!<br>";
	}	
}

/*
 **������: delallpppuser 
 **����:   $ROS->����ROS�ľ��; 
 **����:   ɾ�����е��û�
 **��ע:
*/
function delallpppuser($ROS){
   $ARRAY =$ROS->comm("/ppp/secret/getall"); 
   
   foreach($ARRAY as $key=>$users){
   	
        $ROS->comm("/ppp/secret/remove", array(
          ".id"     => "{$users['.id']}"));
     }    
     return true;
}

/*
 **������: disconnect_ros 
 **����:   $ROS->����ROS�ľ��; 
 **����:   �Ͽ�ROS���ر�ͬ������
 **��ע:
*/
function disconnect_ros( $ROS ) {
	$ROS->disconnect();
}

//ROS��IP ��ַ
$ip="192.168.1.1";
//ROS�ĵ�¼�û��������û�Ҫ�й���PPP�û��Ͷ��е�Ȩ��
$username="admin";
//����
$passwd='';


function findpppuser($ROS,$pppusername){
   $ARRAY =$ROS->comm("/ppp/secret/getall"); 
   foreach($ARRAY as $key=>$users){
       if($users['name']==$pppusername){
        return $users['.id'];
        
       }    
     }    
     return -1;
}

function delpppuser($ROS,$pppusername){
    $id=trim(findpppuser($ROS,$pppusername));
     
    if($id==-1){
       
      return -1;
    }
        $ROS->comm("/ppp/secret/remove", array(
          ".id"     => "$id"));
       
      return 0;
}



function addpppuser($ROS,$pppusername,$ppppassword,$remote_address, $profile){
/*
  //�����Ƿ���ڸ��û�
 if(findpppuser($ROS,$pppusername)<>-1){
    //������û����ڣ���ɾ��
    delpppuser($ROS,$pppusername);  
  }
*/
$comment=     date("Y-m-d H:i:s(D)",time()) ;
if ( trim($remote_address) ) {
	 $ROS->comm("/ppp/secret/add", array(
          "name"     => $pppusername,
          "password" => $ppppassword,
          "remote-address" => $remote_address,
          "profile"        => $profile,
          "comment"  => $comment,
          "service"  => "pppoe",
  )); 
} else {
	 $ROS->comm("/ppp/secret/add", array(
          "name"     => $pppusername,
          "password" => $ppppassword,
          "profile"        => $profile,
          "comment"  => $comment,
          "service"  => "pppoe",
  )); 	
}
 
 
}


function findqueuerule($ROS,$ipaddress){
   $ARRAY =$ROS->comm("/queue/simple/getall");

   foreach($ARRAY as $key=>$rules){
       if($rules['name']==$ipaddress){
      
        return $rules['.id'];
        
       }                 
     }    
     return -1;
}

function delqueuerule($ROS,$ipaddress){
    $id=trim(findqueuerule($ROS,$ipaddress));
     
    if($id==-1){
       
      return -1;
    }
        $ROS->comm("/queue/simple/remove", array(
          ".id"     => "$id"));
       
      return 0;
}

function addqueuerule($ROS,$ipaddress,$max_limit){
    //�����Ƿ���ڸ�IP�Ĺ���
 if(findqueuerule($ROS,$ipaddress)<>-1){
    //������û����ڣ���ɾ��
    delqueuerule($ROS,$ipaddress);  
  }

$comment=     date("Y-m-d H:i:s(D)",time()) ;

   $ROS->comm("/queue/simple/add", array(
          "name"                => $ipaddress,
          "target-addresses"     => $ipaddress,
          //"dst-address" => "0.0.0.0/0",
          "comment"  => $comment,
          "max-limit" => $max_limit,

));  
}

/*
 **������: addpppprofile 
 **����:   $ROS->����ROS�ľ��; 
 **        $name->PPPoE�û����ص������ļ���;
 **        $rate_limit->�����д���
 **����:   ����name�ʹ���ΪPPP���һ���µ������ļ�
 **��ע:
*/
function addpppprofile($ROS, $name, $rate_limit) {
  //�����Ƿ���ڸ��û�
 if( ($id = findpppprofileid($ROS,$pppusername))<>-1 ) {
    //������û����ڣ���ɾ��
    delpppprofile($ROS, $id);  
  }	
	 $comment=     date("Y-m-d H:i:s(D)",time());
   $ROS->comm("/ppp/profile/add", array(
          "name"                => $name,
          "rate-limit="     => $rate_limit,
          "comment"  => $comment,

));  
}

/*
 **������: findpppuser 
 **����:   $ROS->����ROS�ľ��; 
 **        $profilename->PPP�����ļ���;
 **����:   ����profilename���ҳ�ID
 **����ֵ: �ɹ�:profilename���ڼ�¼��id, ʧ��:-1
 **��ע:
*/
function findpppprofileid($ROS,$profilename){
   $ARRAY =$ROS->comm("/ppp/profile/getall");
   foreach($ARRAY as $key=>$profile){
       if($profile['name']==$profilename){     
        return $profile['.id'];       
       }    
     }    
     return -1;
}

/*
 **������: delpppprofile 
 **����:   $ROS->����ROS�ľ��; 
 **        $id->PPPoE�û����ص������ļ�����id;
 **����:   ����idɾ����Ӧ�������ļ���
 **��ע:
*/
function delpppprofile($ROS, $id) {
    if($id==-1){      
      return -1;
    }	
	$ROS->comm("/ppp/profile/remove", array(
	  ".id"     => trim($id)));
}

/*
if ($ROS->connect('192.168.1.1', 'admin', '')) {
    addpppuser($ROS,"chenhong", "chenhong", "3.3.3.3", "24");
    addpppuser($ROS,"kkk", "chenhong", "", "24");
    echo "���ӳɹ�, ���ݿ�����...";
   $ROS->disconnect();

}else{
  echo "���Ӵ���";
  //echo "connected error!!!<br>";
}
*/

/* IP/MAC�� */
function find_arpid($ROS, $mac){
   $ARRAY =$ROS->comm("/ip/arp/getall"); 
/*   
   print_r($ARRAY);
   return 0;
    [0] => Array
        (
            [.id] => *2
            [address] => 192.168.100.199
            [mac-address] => BC:AE:C5:61:C9:52
            [interface] => ether2
            [invalid] => false
            [DHCP] => false
            [dynamic] => true
            [disabled] => false
        )

    [1] => Array
        (
            [.id] => *5
            [address] => 192.168.100.1
            [mac-address] => 1A:7B:3C:5D:60:10
            [interface] => ether2
            [invalid] => false
            [DHCP] => false
            [dynamic] => true
            [disabled] => false
        )

    [2] => Array
        (
            [.id] => *6
            [address] => 192.168.0.100
            [mac-address] => 00:0C:29:86:FE:2F
            [interface] => ether1
            [invalid] => false
            [DHCP] => false
            [dynamic] => false
            [disabled] => false
        )

    [3] => Array
        (
            [.id] => *7
            [address] => 192.168.100.188
            [mac-address] => 00:0C:29:CF:B4:D4
            [interface] => ether2
            [invalid] => false
            [DHCP] => false
            [dynamic] => true
            [disabled] => false
        )
   
*/   
   foreach($ARRAY as $key=>$arps){   
       if($arps['mac-address']==trim($mac)) {
        return $arps['.id']; 
       }    
     }    
     return -1;
}


function addarp($ROS, $ipaddress, $mac, $inf) {
  //�����Ƿ���ڸð�
  //ip arp  add address=192.168.0.100 mac-address=00:0C:29:86:FE:2F interface=ether1
// if( ($id = find_arpid($ROS,$mac))<>-1 ) {
    //����ð󶨣���ɾ��
//    delarp($ROS, $id);  
//  }	
	 $comment=     date("Y-m-d H:i:s(D)",time());
   $ROS->comm("/ip/arp/add", array(
          "address"                => $ipaddress,
          "mac-address"     => $mac,
          "interface"       => $inf,
          "comment"  => $comment,

));  
}

function delarp($ROS, $id) {
    if($id==-1){      
      return -1;
    }	
	$ROS->comm("/ip/arp/remove", array(
	  ".id"     => trim($id)));	
}
?>
