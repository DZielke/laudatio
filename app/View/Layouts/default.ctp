<?php
$cakeDescription = 'LAUDATIO – Long-term Access and Usage of Deeply Annotated Information';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->html->script('jquery-1.10.2.min');
        echo $this->Html->script('loginbar');
		echo $this->Html->meta('icon');
		echo $this->Html->css('cake.my');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
        /* <script type="text/javascript" src="../../js/jquery.treeTable.js"></script> 
            <script type="text/javascript">
                $(document).ready(function()  {
                $("#tree").treeTable();
                });
            </script>   
          <link href="../../js/css/jquery.treeTable.css" rel="stylesheet" type="text/css" />
         * <link href="/repository/js/css/jquery.treeTable.css" rel="stylesheet" type="text/css" />
        */
	?>
	


	
</head>
<body>
	<div id="container">
		<div id="header">
			<?php 
				echo '<h1>LAUDATIO-Repository</h1>';

				echo $this->element('login');
			 ?>
		</div>
		<?php
            echo $this->element('navbar');
            echo $this->element('sidebar');
        ?>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
    		<?php echo $this->Session->flash('auth'); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
            <div id="footer_links">
                <?php
                echo $this->Html->link(
                    $this->Html->image('dfg_logo_2.png', array('width'=>'80px', 'alt' => 'DFG', 'border' => '0')),
                    'http://www.dfg.de/',
                    array('target' => '_blank', 'escape' => false)
                );
                ?>
            </div>

            <div id="footer_no_logos">
                <?php
                    echo '<span>'.$this->Html->link(
                        $this->Html->image('image_290713.png', array('width'=>'97%', 'alt' => 'Institut für deutsche Sprache und Linguistik', 'border' => '0')),
                        'http://www.linguistik.hu-berlin.de/',
                        array('target' => '_blank', 'escape' => false)
                    ).'</span>';
                ?>
            </div>
            <?php echo '<br><br>'; ?>

            <div id="footer_no_logos">
                <?php
                    echo $this->Html->link(
                        $this->Html->image('cms.png', array('width'=>'73%', 'alt' => 'Computer- und Medienservice', 'border' => '0')),
                        'http://www.cms.hu-berlin.de/',
                        array('target' => '_blank', 'escape' => false)
                    );

                    /*
                    echo $this->Html->link("Institut für deutsche Sprache und Linguistik",
                        "http://www.linguistik.hu-berlin.de/",
                        array('title' => 'Institut für deutsche Sprache und Linguistik')
                    );
                    echo ' &#448; ';
                    echo '<br>';
                    */
                    /*
                    echo $this->Html->link("Computer- und Medienservice",
                        "http://www.cms.hu-berlin.de/",
                        array('title' => 'Computer- und Medienservice')
                    );
                    #echo ' ';
                    */

                    echo '<br><br>';

                    echo $this->Html->link(
                        $this->Html->image('INRIA_ok.png', array('alt' => 'Inventeurs du monde numérique', 'border' => '0')),
                        'http://www.inria.fr/',
                        array('target' => '_blank', 'escape' => false)
                    );
                ?>
            </div>



            <div id="footer_right">
                <?php
                echo $this->Html->link(
                    $this->Html->image('Husiegel_bw_klein.png', array('width'=>'80px', 'alt' => 'HU', 'border' => '0')),
                    'http://www.hu-berlin.de/',
                    array('target' => '_blank', 'escape' => false)
                );
                ?>
            </div>
            <?php
            echo $this->element('sidebar_bottom');
            ?>

            <?php echo '<br>'; ?>

            <div id="footer_middle">
                <?php
/*
                echo $this->Html->link(
                    $this->Html->image('INRIA_ok.png', array('alt' => 'Inventeurs du monde numérique', 'border' => '0')),
                    'http://www.inria.fr/',
                    array('target' => '_blank', 'escape' => false)
                );
*/
                echo $this->Html->link(
                    $this->Html->image('logo_fedora_transparent_200_39.png', array('height'=>'20px', 'alt' => 'Fedora-Commons Repository', 'border' => '0')),
                    'http://www.duraspace.org/about_fedora',
                    array('target' => '_blank', 'escape' => false)
                );

                echo ' &#448; ';

                echo $this->Html->link(
                    $this->Html->image('elasticsearch_logo-600x123.png', array('height'=>'20px', 'alt' => 'Fedora-Commons Repository', 'border' => '0')),
                    'http://www.elasticsearch.org',
                    array('target' => '_blank', 'escape' => false)
                );

                echo ' &#448; ';

                echo $this->Html->link(
                    $this->Html->image('cake.power.gif', array('alt' => 'CakePHP Framework', 'border' => '0')),
                    'http://cakephp.org/',
                    array('target' => '_blank', 'escape' => false)
                );
                ?>
                <?php echo '<br><br>'; ?>
            </div>
            <div id="sidebar_piwik_bottom">
                <iframe frameborder="no" width="100%" height="180px" src="http://www.laudatio-repository.org/piwik/index.php?module=CoreAdminHome&action=optOut&language=en"></iframe>
            </div>
		</div>
	</div>
	<?php	#echo $this->element('sql_dump'); ?>
</body>
</html>
