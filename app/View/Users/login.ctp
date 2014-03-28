<?php $this->set('navbarArray',array(array('Login'))); ?>
Login:
	
<!--<div id='loginForm'>-->
<?php
   echo $this->form->create('User', array('action' => 'login'));
   echo $this->form->input('username');
   echo $this->form->input('password');
   echo $this->form->end('Login');
	###DZ, 21.12.12
	/*
        echo $this->Session->flash('auth');
        echo $this->Form->create('Users', array('action' => 'login'));
        echo $this->Form->input('username');
        echo $this->Form->input('password',array('value'=>''));
        echo $this->Form->input('remember',array('type' => 'checkbox', 'label' => 'Remember me'));
        echo $this->Form->submit('Login');
	*/
?>
<!--</div>-->