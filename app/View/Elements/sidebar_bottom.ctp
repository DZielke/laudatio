<div id="sidebar_bottom">
    <?php
    if (!$this->Session->check('Auth.User.id')) {
        echo '<span>'.$this->Html->link('Contact & Imprint',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'contact')).'</span>';
        echo ' &#448; ';
        echo '<span>'.$this->Html->link('Blog',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'blog'), array('target'=>'_blank','escape'=>false)).'</span>';
        echo ' &#448; ';
        echo '<span>'.$this->Html->link('Technical Documentation',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'technical-documentation'), array('target'=>'_blank','escape'=>false)).'</span>';
    }
    else {
        echo '<span>'.$this->Html->link('Contact & Imprint',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'contact')).'</span>';
        echo ' &#448; ';
        echo '<span>'.$this->Html->link('Blog',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'blog'), array('target'=>'_blank','escape'=>false)).'</span>';
        echo ' &#448; ';
        echo '<span>'.$this->Html->link('Technical Documentation',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'technical-documentation'), array('target'=>'_blank','escape'=>false)).'</span>';
    }
    ?>
</div>