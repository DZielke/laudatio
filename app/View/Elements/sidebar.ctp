<div id="sidebar">
    <?php
    App::import('Model', 'XMLObject');
    $this->XMLObject = new XMLObject();
    $user_id = $this->Session->read('Auth.User.id');
    $isCreator = $this->XMLObject->isCreator($user_id);
    $group_id = $this->Session->read('Auth.User.group_id');
    echo '<ul id="sidebarlist">';
    if (!$this->Session->check('Auth.User.id')) {
        echo '<span><li>'.$this->Html->link('Home',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'index')).'</li></span>';
        echo '<li>'.$this->Html->link('Documentation',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'documentation')).'</li>';
        echo '<li>'.$this->Html->link('View',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'view')).'</li>';
        echo '<li>'.$this->Html->link('Search',array('plugin' => 'search', 'admin' => false,'controller' => 'search')).'</li>';
    }
    else {
        echo '<span><li>'.$this->Html->link('Home',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'index')).'</li></span>';
        echo '<li>'.$this->Html->link('Documentation',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'documentation')).'</li>';
        echo '<li>'.$this->Html->link('View',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'view')).'</li>';
        echo '<span><li>'.$this->Html->link('Search',array('plugin' => 'search', 'admin' => false,'controller' => 'search')).'</li></span>';
        #echo '<li>'.$this->Html->link('Import',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'import')).'</li>';
        #echo '<li>'.$this->Html->link('Delete',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'delete')).'</li>';

        if ($group_id != '3') {
            echo '<li>'.$this->Html->link('Import',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'import')).'</li>';
            echo '<li>'.$this->Html->link('Modify',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'modify')).'</li>';
        }

        if($isCreator ||$group_id == '1'){
            #echo '<li>'.$this->Html->link('Modify',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'modify')).'</li>';
            echo '<li>'.$this->Html->link('Delete',array('plugin' => null, 'admin' => false,'controller' => 'XMLObjects', 'action' => 'delete')).'</li>';
        }

        if ($group_id == '1') {
            echo '<li>'.$this->Html->link('User Management',array('plugin' => null, 'admin' => false,'controller' => 'users', 'action' => 'index')).'</li>';
            echo '<li>'.$this->Html->link('Configuration',array('plugin' => null, 'admin' => false,'controller' => 'configs', 'action' => 'index')).'</li>';
            echo '<span><li>'.$this->Html->link('Rights Management',array('plugin'=> 'acl', 'admin' => true , 'controller' => 'acl')).'</li></span>'; #'<a href="../admin/acl/">Rechteverwaltung</a>';
        }
    }
    echo '</ul>';
    echo '<script type="text/javascript">
        $(function(){
            $("#sidebarlist li").click(function(event){
                if(!$(event.target).is("a"))
                window.location = $(this).children("a").attr("href");
            })
        })
     </script>'
    ?>
</div>