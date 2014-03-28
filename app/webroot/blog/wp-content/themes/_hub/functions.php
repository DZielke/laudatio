<?php
if ( function_exists('register_sidebars') )
    register_sidebar(array(
		'name'=>'Left Menu',
		'description'=>'Menü auf der linken Seite'));
	register_sidebar(array('name'=>'Right Menu','description'=>'Menü auf der rechten Seite'));
	register_sidebar(array('name'=>'FakInstDeskriptor','description'=>'Deskriptor f&uuml; Fakult&auml;ts- und Institutsnamen'));
	
	add_theme_support('menus');
	register_nav_menus( array('header_nav' => __( 'Header Navigation', 'Header Nav' ),));
?>