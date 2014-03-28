<?php 
$this->set('navbarArray',array(array('Register')));
echo $this->Form->create('User',array('action'=>'register'));
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->end('Register');

#if($dump) echo $dump;

?>