<?php
/*
 ** ��������: ���ű������ROS�ľ�̬IP��ӣ��޸ĺ�ɾ��IP/MAC��¼�ĺ�����, 
              ʵ�ִӼƷ����IP/MAC��ROS����ROS�޸�IP/MAC, ��ROSɾ��IP/MAC�����Թ��ܶ���ROS PHP APIʵ��
 ** ��������: addarp2ros ���ros��IP/MAC��
              delarp_from_ros ����MAC��ַɾ���Ѱ󶨵�arp��Ϣ
              modifyarp �޸��Ѱ󶨵�arp��¼
              linktest  ����ROS����ͨ��
 ** ����ʵ��: 
             1. ROS�汾��3.x����(��Ϊ3.x���ϵĲ�֧��API)�� ��ROS API����->ip service enable api  
             2. ROS�Ľӿ�arp�������ó�proxy-arpģʽ�� ��ʾ�û��Լ�������Ӧ�Ľӿ� ,  �����ǵ�һ���ӿ� interface ethernet set arp=reply-only ether1  
             3. ��ӱ�ip2ros, �ֶ�id, rosipaddress, rosusername, rospassword, inf, projectid
             4. ����ĿΪ "����Linux" ʱ�� ���־�̬IP����ѡ�����û����øĹ��ܣ� 
                ��������������û�����ROS��IP��ַ���û������������Ҫ�󶨵Ľӿ������ӿ����û����룩����ʾ�û��޸�ROS�ӿڵ����ƣ�
                ����Ӧ����Ϣ��ӽ���ip2ros             
             5. ROS API���Ӳ��ԣ����ú���linktest
             6. ���û�����Ŀ�Ǿ�̬IP����ʱ�� ������Ҫ���ú���addarp2ros��delarp_from_ros�� modifyarp     
  */


include("inc/routeros_api.class.php");
include("inc/routeros_api_function.php");
//include("inc/db_config.php");

//linktest("192.168.100.111", "admin", $rospwd);
/*
 ** ������: addarp2ros
 ** ����:   ���ros��IP/MAC��
 ** ����:   $rosipaddress ROS��IP��ַ
            $rosusername ��½ROS���û���
            $rospwd ��½ROS������
            $ipaddress ��Ҫ�󶨵�IP��ַ
            $mac ��Ҫ�󶨵�MAC��ַ
            $inf ��Ҫ�󶨵Ľӿ� 
 ** ����ֵ: �ɹ�->true ʧ��->false
  */
function addarp2ros($rosipaddress, $rosusername, $rospwd, $ipaddress, $mac, $inf) {
  /* ����ROS��API */
  $ROS = new routeros_api();
  if ( !$rosipaddress || !connect_ros( $ROS, $rosipaddress, $rosusername, $rospwd ) ) { /* ����ʧ�ܷ���false */
    disconnect_ros( $ROS );
    return false;
  }    
  
  /* ���arp��ros */
  addarp($ROS, $ipaddress, $mac, $inf);
     
  /* �Ͽ�ROS��API���� */
  disconnect_ros( $ROS );  
  
  return true; 
}

/*
 ** ������: delarp_from_ros
 ** ����:   ����MAC��ַɾ���Ѱ󶨵�arp��Ϣ
 ** ����:   $rosipaddress ROS��IP��ַ
            $rosusername ��½ROS���û���
            $mac ��Ҫƥ���mac��ַ
 ** ����ֵ: �ɹ�->true ʧ��->false
  */
function delarp_from_ros($rosipaddress, $rosusername, $rospwd, $mac) {
  /* ����ROS��API */
  $ROS = new routeros_api();
  if ( !$rosipaddress || !connect_ros( $ROS, $rosipaddress, $rosusername, $rospwd ) ) { /* ����ʧ�ܷ���false */
    disconnect_ros( $ROS );
    return false;
  }  
  /* ����mac��ַ��ѯid */
  $id = find_arpid($ROS, $mac);
  
  /* ɾ��id��¼ */
  delarp($ROS, $id);
     
  /* �Ͽ�ROS��API���� */
  disconnect_ros( $ROS );  
  
  return true; 
}

/*
 ** ������: modifyarp
 ** ����:   �޸��Ѱ󶨵�arp��¼
 ** ����:   $rosipaddress ROS��IP��ַ
            $rosusername ��½ROS���û���
            $rospwd ��½ROS������
            $overduemac ��ƥ���MAC��ַ�� ���ڲ�ѯ�Ƿ���ڸ�MAC��ַ����Ϣ 
            $ipaddress ��Ҫ�󶨵�IP��ַ
            $mac ��Ҫ�󶨵�MAC��ַ
            $inf ��Ҫ�󶨵Ľӿ� 
 ** ����ֵ: �ɹ�->true ʧ��->false
  */
function modifyarp($rosipaddress, $rosusername, $rospwd, $overduemac, $ipaddress, $mac, $inf) {
  /* ����ROS��API */
  $ROS = new routeros_api();
  if ( !$rosipaddress || !connect_ros( $ROS, $rosipaddress, $rosusername, $rospwd ) ) { /* ����ʧ�ܷ���false */
    disconnect_ros( $ROS );
    return false;
  }    
  
  /* ����mac��ַ��ѯid */
  $id = find_arpid($ROS, $overduemac);
  
  /* ɾ��id��¼ */
  delarp($ROS, $id);
  
  /* ���arp��ros */
  addarp($ROS, $ipaddress, $mac, $inf);
  
  /* �Ͽ�ROS��API���� */
  disconnect_ros( $ROS );   
  
  return true;   
}

/*
 ** ������: linktest
 ** ����:   ����ROS������
 ** ����:   $rosipaddress ROS��IP��ַ
            $rosusername ��½ROS���û���
            $rospwd ��½ROS������
 ** ����ֵ: �ɹ�->true ʧ��->false
  */
function linktest($rosipaddress, $rosusername, $rospwd) {
  /* ����ROS��API */
  $ROS = new routeros_api();
  if ( !$rosipaddress || !connect_ros( $ROS, $rosipaddress, $rosusername, $rospwd ) ) { /* ����ʧ�ܷ���false */
    disconnect_ros( $ROS );
    return false;
  }
  disconnect_ros( $ROS );
  //addarp2ros("192.168.100.111", "admin", "", "192.168.0.2", "00:03:47:9A:BA:56", "lan");
  //delarp_from_ros("192.168.100.111", "admin", "", "00:03:47:9A:BA:56");
  //modifyarp("192.168.100.111", "admin", "", "00:03:47:9A:BA:56", "192.168.0.3", "00:03:47:9A:BA:56", "lan");
  
  return true;  
}

//$rosipaddress = "192.168.100.11";
//$rosusername = "admin";
//$rospwd = "";
//$ROS = new routeros_api();
/* ����ROSʧ�ܣ� ����false */

//if ( !$rosipaddress || !connect_ros( $ROS, $rosipaddress, $rosusername, $rospwd ) ) {
//  disconnect_ros( $ROS );
//  return false;
//}

//find_arpid($ROS,$mac);
//addarp($ROS, "192.168.0.100", "00:0C:29:86:FE:2F", "LAN");

/* ͬ����̬IP��Ϣ��ROS 
   ����Ŀ��(project)������ֶ�nasip�� nasusername�� naspwd
   ����Ŀѡ��Otherʱ�� �����û�����nasip��nasusername��naspwd
*/

/*
 ** ������: staticip2ros
 ** ���ܣ�  ͨ��APIͬ����̬IP�û���Ϣ��ROS�� ������Ϊ����ͬ�����ھ�̬IP�û���ROS
 ** ����:   $username �û���
 **         $action ������addΪ��ӣ� delΪɾ��
 ** ����ֵ: �ɹ�����true�� ʧ�ܷ���false, ͬ�����ھ�̬IP�û��޷���ֵ
 ** ע:     �޸��û���Ҫ���޸����ݿ�֮ǰstaticip2ros($username, "del"); Ȼ���޸����ݿ⣬��staticip2ros($username, "add");
 **         ɾ���û���Ҫ���޸����ݿ�֮ǰstaticip2ros($username, "del")
 **         ����û���Ҫ���޸����ݿ�֮��staticip2ros($username, "add")
  */
function staticip2ros($username = NULL, $action = NULL) {
  global $mysqlhost, $mysqluser, $mysqlpwd, $mysqldb;
  
  /* ����MySQL */ 
  $conn   = mysql_connect($mysqlhost, $mysqluser, $mysqlpwd);
  
  /* ����MySQLʧ�ܣ�����false */
  if ( !$conn ) {
   	return mysql_error;
  } 
  
  /* ѡ���("radius") */
  mysql_select_db($mysqldb);  
  
  if ( $username ) { /* ���, �޸�, ɾ���û�����ز��� */
    /* �����û�����ȡ�û���ID�� ���� IP��ַ */
    $sql="select radreply.Attribute, radreply.value, radreply.UserID from radreply where radreply.UserName = '{$username}'";
    $result = mysql_query($sql);
    while( $row=mysql_fetch_array($result, MYSQL_ASSOC) ) {
      if ( $row ) {
        foreach( $row as $key=>$value) {      
          if ( is_ipaddr($value) ) {
            $ipaddress = $value;
          } else if ( count(explode("k/", $value)) > 1 ) {
            $bandwidth = $value;  
          } else {
            $userid = $value; 
          }      
        }
      }
    }
    /* û�л�ȡ����ȷ��IP��ַ��Ϣ�� �������ͬ�������� ����false */
    if ( !$ipaddress ) {
      return false;
    }
     /* �����û���ID��ȡ��Ŀ��Ӧ��ROS��ַ���û��������� */
    $nasinfo = getnasinfo($username);  
    if ( 0 == strcmp("mikrotik", $nasinfo['device']) ) { 
      /* ͬ����ROS */
      /* ��ȡROS��IP��ַ���û��������� */
      $rosipaddress = $nasinfo['nasip'];
      $rosusername = $nasinfo['nasusername'];
      $rospwd = $nasinfo['naspwd'];
      
      /* ����ROS��API */
      $ROS = new routeros_api();
      /* ����ROSʧ�ܣ� ����false */
      
      if ( !$rosipaddress || !connect_ros( $ROS, $rosipaddress, $rosusername, $rospwd ) ) {
        disconnect_ros( $ROS );
        return false;
      }
  
      /* ͬ�� */
      if ( 0 == strcmp("add", trim($action))) {
        addnatrule($ROS, $ipaddress);
        addqueuerule($ROS, $ipaddress, $bandwidth);    
      } else if( 0 == strcmp("del", trim($action)) ) {
        delnatrule($ROS,$ipaddress);
        delqueuerule($ROS,$ipaddress);        
      }
      
      /* �Ͽ�ROS��API���� */
      disconnect_ros( $ROS );
    }
    return true;
  } else { /* �����û����� */
    /* ��ȡ������Ŀ��Ӧ��ROS��Ϣ */
    $nasinfo = getnasinfo();
    if ( $nasinfo ) {
      /* ��ǰ���� */
      $currentdate = strtotime(date("Y-m-d h:i:s"));
      $ROS = new routeros_api();
      
      foreach( $nasinfo as $key=>$value ) {
        if ( 0 == strcmp("mikrotik", $value['device']) ) {
          /* ��ȡ��ĿID, ROS��ַ���û��������� */
          $projectid = $value['ID'];
          $rosipaddress = $value['nasip'];
          $rosusername = $value['nasusername'];
          $rospwd = $value['naspwd'];
          
          /* ����ROS��API */
          if ( !$rosipaddress || !connect_ros( $ROS, $rosipaddress, $rosusername, $rospwd ) ) { /* ROS��API����ʧ�ܣ� ������һ��API */
            continue;
          }          
          /* ������ĿID���Ҹ���Ŀ���û�id */
          $sql = "select userinfo.id from userinfo where userinfo.projectID = '{$projectid}'";
          $result_userinfo = mysql_query($sql);
          while( $row_userinfo = mysql_fetch_array($result_userinfo, MYSQL_ASSOC) ) { 
            $userid = $row_userinfo['id']; 
            /* �����û�ID�����û�����ʱ�� */
            $sql = "select userrun.enddatetime from userrun where userrun.UserID='{$userid}'";
            $result_userrun = mysql_query($sql);
            $row_userrun = mysql_fetch_array($result_userrun, MYSQL_ASSOC);
            $enddate = strtotime($row_userrun['enddatetime']);     
            if ( $currentdate >= $enddate ) { /* �����û� */
              /* ��ȡ�����û���IP��ַ */ 
              $sql = "select  radreply.value from radreply where radreply.UserID = '{$userid}' and radreply.Attribute = 'Framed-IP-Address'"; 
              $result_radreply = mysql_query($sql);
              $row_radreply = mysql_fetch_array($result_radreply, MYSQL_ASSOC);
              $ipaddress = $row_radreply['value'];
              
              /* ͬ��ROS���� */
              delnatrule($ROS,$ipaddress);
              delqueuerule($ROS,$ipaddress);
            }       
          } /* end while �û����� */          
        }
      } /* end foreach */
      /* �Ͽ�ROS��API���� */
      disconnect_ros( $ROS );
    }    
  } 
}

/* ��ȡ��Ŀ��Ϣ�� ������û��������ȡ�û�����Ӧ����Ŀ�������ȡ���е���Ŀ */
function getnasinfo($username = NULL ) {
  if ( $username ) { /* �����û�������Ŀ */
    $sql = "select userinfo.projectID from userinfo where userinfo.UserName='{$username}'";
    $result = mysql_query($sql);
    $row=mysql_fetch_array($result, MYSQL_ASSOC);
    $projectID = $row['projectID'];
    $sql = "select * from project where ID = '{$projectID}'";
    $result = mysql_query($sql);
    $row=mysql_fetch_array($result, MYSQL_ASSOC);
    $result = $row;
  } else { /* ����������Ŀ */
    $sql = "select * from project";
    $result = mysql_query($sql);
    $tmp_array = array();
    while( $row=mysql_fetch_array($result, MYSQL_ASSOC) ) { 
      $tmp_array[] = $row; 
    }    
    $result = $tmp_array;
    unset($tmp_array);
  }
  return $result;
}


function is_ipaddr($ipaddr) {
  if ( filter_var($ipaddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
     $result = true ;
  } else if ( filter_var($ipaddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
    $result = true ;
  } else {
    $result = false ;
  }

  return $result;
}
  
  
function disstr( $str ) {
  if ( is_array($str) ) {
    print_r($str);
    echo "<hr>";
  } else {
    echo "{$str}<hr>"; 
  }
}   
?>
