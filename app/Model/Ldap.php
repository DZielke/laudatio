<?php
class Ldap extends AppModel {
	public $useDbConfig = 'ldap';
	public $primaryKey = 'uid';
	public $useTable = 'ou=users';
	public $userModel = 'Ldap';

	#var $hostname 	= 'localhost';
	#var $baseDn 	= 'o=test, c=de';
	#var $username 	= 'cn=admin, o=test, c=de';
	#var $password	= 'secret';
	#var $ldapconn;
	
	public function buildUserdata($user,$uid){
		$ldap_user_data = $user[0][$this->userModel];		
		$user_data = array(
			'id' => '15', #<--- noch anpassen !!!!!!!!! 
			'username' => $uid,//$ldap_user_data['givenname'].' '.$ldap_user_data['sn'],
			'group_id' => '6',
			'Group' =>array(
				'id' => '6',
				'name' => 'LdapGroup',
				'created'=>'2013-01-11 14:36:14',
				'modified'=>'2013-01-11 14:39:16'
			)
		);
		return $user_data;
	}
	
	#function __construct() {
	#	parent::__construct();
		#$this->ldapConn = ldap_connect($this->hostname, 389);
		#ldap_set_option($this->ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
		#ldap_bind($this->ldapConn, $this->username, $this->password);
	#}
	
	#function __destruct() {
		#ldap_close($this->ldapConn);
	#}

	function login($uid, $password){
		#$this->__setDefaults();
		$this->_loggedIn = false;
		$dn = $this->getDn('uid', $uid);
		$loginResult = $this->ldapauth($dn, $password); 
		if( $loginResult == 1){
			$this->_loggedIn = true;
			$user = $this->find('all', array('scope'=>'base', 'targetDn'=>$dn));
			$user_data = $this->buildUserdata($user,$uid);
			#$user_data = $user[0][$this->userModel];
			#$user_data['bindDN'] = $dn;
			#$user_data['bindPasswd'] = $password;
			#$this->Session->write($this->sessionKey, $user_data);
			return $user_data;
			
		}else{
			$this->loginError =  $loginResult;
		}
                return $this->_loggedIn;
	}

	function ldapauth($dn, $password){
		$authResult =  $this->auth( array('dn'=>$dn, 'password'=>$password));
		return $authResult;
	}

	function getDn( $attr, $query){
		$dn = 'uid='.$query.',ou=xxx,ou=xxx,ou=xxx,o=xxxx,c=XX';
		return $dn;
	}

    function isUser($uid){
        App::uses('ConnectionManager', 'Model');
        $dataSource = ConnectionManager::getDataSource('ldap');
        $ldap = ldap_connect($dataSource->config['host'],$dataSource->config['port']);
        $response = ldap_search($ldap,$dataSource->config['basedn'],'uid='.$uid);
        $entries = ldap_get_entries($ldap,$response);

        if($entries['count'] === 1){
            $isUser = true;
        }else{
            $isUser = false;
        }
        ldap_close($ldap);
        return $isUser;
    }
}
?>
