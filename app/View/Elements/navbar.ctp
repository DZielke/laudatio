<div id="navbar">
<?php
echo '<ul>';
if(isset($navbarArray)){
	echo '<li>'.$this->Html->link('Home', array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'index')).'</li>';
    foreach ($navbarArray as $i => $value){
        echo '<li class="nav_separator">&raquo;</li>';
		if ($i == count($navbarArray)-1) {
			echo '<li class="selected">'.$value[0].'</li>';
		}
		else {
            if(count($value) == 3){
                $controller = $value[2];
            }else{
                $controller = "XMLObjects";
            }
			echo '<li>'.$this->Html->link($value[0], array('plugin' => null, 'admin' => false,'controller' => $controller, 'action' => $value[1])).'</li>';
		}
	}
}else{
	echo '<li class="selected">Home</li>';
}
#echo '<li>test</li>';
echo '</ul>';

#echo $this->Html->link('Home', array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'index'));



?>
</div>
	