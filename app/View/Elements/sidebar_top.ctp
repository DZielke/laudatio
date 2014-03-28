<span id="sidebar_top">
    <?php
    #echo '<ul>';
    if (!$this->Session->check('Auth.User.id')) {
        #echo '<span>'.$this->Html->link('Blog',array('plugin' => null, 'admin' => false,'controller' => 'blog', 'action' => '')).'</span>';
        #echo ' &#448; ';

        echo '<span>'.$this->Html->link('Login',array('plugin' => null, 'admin' => false,'controller' => 'Users', 'action' => 'login')).'</span>';
        #echo ' &#448; ';
        #echo '<span>'.$this->Html->link('Register new Account',array('controller' => 'Users', 'action' => 'requestAccount')).'</span>';

        #echo ' &#448; ';
        #echo '<span>'.$this->Html->link('Impressum',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'impressum')).'</span>';
    }
    else {
        #echo '<span>'.$this->Html->link('Blog',array('plugin' => null, 'admin' => false,'controller' => 'blog', 'action' => '')).'</span>';

        #echo ' &#448; ';
        #echo '<span>'.$this->Html->link('Impressum',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'impressum')).'</span>';
    }
    #echo '</ul>';
    ?>
</span>