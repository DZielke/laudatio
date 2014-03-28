<?php
echo '<div id="loginbar" class="login">';
if ($this->Session->check('Auth.User.id')) {
    echo '<p><b>'.$this->Session->read('Auth.User.username').'</b> (';
    echo $this->Html->link('Logout',array('plugin' => null,'admin' => false,'controller' => 'Users', 'action' => 'logout'));
	echo ')</p>';


}
else {

    echo '<p>'.$this->Html->link('Register new account',array('plugin' => null, 'admin' => false,'controller' => 'Users', 'action' => 'requestAccount'));
    echo ' &#448; ';
    echo $this->Html->link('Login',array('plugin' => null,'admin' => false,'controller' => 'Users', 'action' => 'login'),array('id' => 'loginlink'));
    echo '</p>';

    //login form to activate
    echo '<div class="loginform" style="display:none"><form action="'.Router::url(array('plugin'=>null,'controller'=>'Users','action'=>'login')).'"';
    echo 'id="UserLoginForm" method="post" accept-charset="utf-8" class="fields">';
	echo '<div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>';


    echo '<span style="display:inline-block"><label for="UserUsername">Username</label><input name="data[User][username]" tabindex="1" maxlength="50" type="text" id="UserUsername"/></span>';
	echo '<span style="display:inline-block"><label for="UserPassword">Password</label><input name="data[User][password]" tabindex="2" type="password" id="UserPassword"/></span>';
    echo '<input  type="submit" tabindex="3" value="Login"/>';
	echo '</form></div>';
}
echo '</div>';

?>