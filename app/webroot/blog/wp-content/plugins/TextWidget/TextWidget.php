<?php
/*
Plugin Name: HU Deskriptor-Widget
Description: Dient der Anzeige des Fakult&auml;ts- und Institutsnamen
Author: Andreas Gr&uuml;ner
Version: 0.1
*/

add_action( 'widgets_init', 'TextWidget_load' );

function TextWidget_load() {
	register_widget( 'TextWidget' );
	}
                                                                                                                                                        
class TextWidget extends WP_Widget {
	function TextWidget() {
		//Konstruktor
		$widget_ops = array('classname' => 'TextWidget', 'description' => 'Deskriptor Widget f&uuml;r Fakult&auml;ts- und Institutsnamen' );
		$control_ops = array('width' => 300, 'height' => 350);
		$this->WP_Widget('TextWidget','TextWidget', $widget_ops, $control_ops);

	}
 
	function widget($args, $instance) {
		// Ausgabefunktion
		extract($args);
 
		$fakultaet = empty($instance['fakultaet']) ? '&nbsp;' : apply_filters('widget_fakultaet', $instance['fakultaet']);
		$institut = empty($instance['institut']) ? '&nbsp;' : apply_filters('widget_institut', $instance['institut']);
 
		echo $before_widget;
		echo '<div id="sitedesk-space">';
		echo '<div class="textwidget-fakultaet"><h1>'.$fakultaet.'</h1></div>';
		echo '<div class="textwidget-institut"><h1>'.$institut.'</h1></div>';
		echo '</div>';
		echo '<div id="sitedesk-real">';
		echo '<div class="textwidget-fakultaet"><h1>'.$fakultaet.'</h1></div>';
		echo '<div class="textwidget-institut"><h1>'.$institut.'</h1></div>';
		echo '</div>';
		echo $after_widget;

		
	
	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['fakultaet'] = strip_tags($new_instance['fakultaet']);
		$instance['institut'] = strip_tags($new_instance['institut']);
 
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'fakultaet' => '', 'institut' => '') );
		
		?>
		
		
		<p>
		<label for="<?php echo $this->get_field_id('fakultaet'); ?>">Name der Fakult&auml;t:</label> 
		<input id="<?php echo $this->get_field_id('fakultaet'); ?>" name="<?php echo $this->get_field_name('fakultaet'); ?>" type="text" value="<?php echo $instance[fakultaet]; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('institut'); ?>">Name des Instituts: </label>
		<input id="<?php echo $this->get_field_id('institut'); ?>" name="<?php echo $this->get_field_name('institut'); ?>" type="text" value="<?php echo $instance[institut]; ?>" />
		</p>
			
		

	<?php
	}
}

?>