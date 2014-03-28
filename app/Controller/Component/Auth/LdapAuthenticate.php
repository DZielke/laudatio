<?php
App::uses('BaseAuthenticate', 'Controller/Component/Auth');

class LdapAuthenticate extends BaseAuthenticate {
	public function authenticate(CakeRequest $request, CakeResponse $response){
		#echo 'Ldap authenticate()';
		$userModel = $this->settings['userModel'];
		list($plugin, $model) = pluginSplit($userModel);

		$fields = $this->settings['fields'];
		if (empty($request->data[$model])) {
			return false;
		}
		if (
			empty($request->data[$model][$fields['username']]) ||
			empty($request->data[$model][$fields['password']])
		) {
			return false;
		}
	
		
		
		#<---------------------hier debuggen 
		$user = ClassRegistry::init('Ldap')->login($request->data[$model][$fields['username']],
			$request->data[$model][$fields['password']]);
		return $user;
		#in users db eintragen, wenn gefunden
		
		#normal einloggen
		#$this->_findUser(
			#$request->data[$model][$fields['username']],
			#$request->data[$model][$fields['password']]
		#);
		
		
		#oder wenn gefunden user array erzeugen und zurückgeben
		/*Array
		(
		    [id] =&gt; 10
		    [username] =&gt; fedoraAdmin
		    [group_id] =&gt; 1
		    [Group] =&gt; Array
		        (
		            [id] =&gt; 1
		            [name] =&gt; admin
		            [created] =&gt; 2013-01-04 10:05:33
		            [modified] =&gt; 2013-01-04 10:05:33
		        )
		
		)*/
		
	}



	
	
}
	/*
App::uses('Component', 'Controller/Component/Auth');

class LdapComponent extends AuthComponent {
	
	public function login($uid, $password){
		echo 'ldaplogin';
		$this->__setDefaults();
		$this->_loggedIn = false;
		$dn = $this->getDn('uid', $uid);
		$loginResult = $this->ldapauth($dn, $password); 
		if( $loginResult == 1){
			$this->_loggedIn = true;
			$user = $this->ldap->find('all', array('scope'=>'base', 'targetDn'=>$dn));
			$user_data = $user[0][$this->userModel];
			$user_data['bindDN'] = $dn;
			$user_data['bindPasswd'] = $password;
			$this->Session->write($this->sessionKey, $user_data);
		}else{
			$this->loginError =  $loginResult;
		}
                return $this->_loggedIn;
	}

	public function ldapauth($dn, $password){
		$authResult =  $this->ldap->auth( array('dn'=>$dn, 'password'=>$password));
		return $authResult;
	}

	public function getDn( $attr, $query){
		$userObj = $this->ldap->find('all', array('conditions'=>"$attr=$query", 'scope'=>'sub'));
		//$this->log("auth lookup found: ".print_r($userObj,true)." with the following conditions: ".print_r(array('conditions'=>"$attr=$query", 'scope'=>'one'),true),'debug');
		return($userObj[0][$this->userModel]['dn']);
	}

	/*
App::uses('Component', 'Controller/Component/Auth');

class LdapComponent extends AuthComponent {
	
	public function login($uid, $password){
		echo 'ldaplogin';
		$this->__setDefaults();
		$this->_loggedIn = false;
		$dn = $this->getDn('uid', $uid);
		$loginResult = $this->ldapauth($dn, $password); 
		if( $loginResult == 1){
			$this->_loggedIn = true;
			$user = $this->ldap->find('all', array('scope'=>'base', 'targetDn'=>$dn));
			$user_data = $user[0][$this->userModel];
			$user_data['bindDN'] = $dn;
			$user_data['bindPasswd'] = $password;
			$this->Session->write($this->sessionKey, $user_data);
		}else{
			$this->loginError =  $loginResult;
		}
                return $this->_loggedIn;
	}

	public function ldapauth($dn, $password){
		$authResult =  $this->ldap->auth( array('dn'=>$dn, 'password'=>$password));
		return $authResult;
	}

	public function getDn( $attr, $query){
		$userObj = $this->ldap->find('all', array('conditions'=>"$attr=$query", 'scope'=>'sub'));
		//$this->log("auth lookup found: ".print_r($userObj,true)." with the following conditions: ".print_r(array('conditions'=>"$attr=$query", 'scope'=>'one'),true),'debug');
		return($userObj[0][$this->userModel]['dn']);
	}

	
	
	
    public function authorize($user = null, CakeRequest $request) {// Do things for ldap here.
        echo 'ldapathorize';
        print_r($user);
		
        if($user['username']== 'bla'){
        	 return true;
        }else{
        	return false;
        }
		return false;
    }
}

/*
	
	
    public function authorize($user = null, CakeRequest $request) {// Do things for ldap here.
        echo 'ldapathorize';
        print_r($user);
		
        if($user['username']== 'bla'){
        	 return true;
        }else{
        	return false;
        }
		return false;
    }
}

*/