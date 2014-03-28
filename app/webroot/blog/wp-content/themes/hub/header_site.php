<div id="header">
		<div id="header-top">
			<div id="header-top-left">
				<img src="<?php bloginfo('template_directory'); ?>/images/humboldt-uni-berlin.jpg" width="100%" height="100%" border="0" alt="HU" />
			</div>
			<div id="header-top-right">
				<div id="header-top-right-top">
					<div id="header-top-right-top-fr">
						<img src="<?php bloginfo('template_directory'); ?>/images/hukombi_bbw.jpg" align="right"  height="100%" border="0" alt="HU" />
					</div>
				</div>
				<div id="header-top-right-bottom">
				</div>
			</div>
		</div>
		<div id="header-bottom">
			<div id="header-bottom-left">
				<div id="navigation">
					<div id="menu">
						<ul>
						<li><span><a href="<?php echo get_settings('home'); ?>">Home<?php echo $langblog;?></a></span></li>	
						</ul>
						<?php wp_nav_menu(array('theme_location'=>'header_nav','link_before'=>'<span class="border"></span>')); ?>
					</div>
				</div>
			</div>
			<div id="header-bottom-right">
				<div id="header-bottom-right-le">
					Suchen
				</div>
				<div id="header-bottom-right-ri">
					<form action="<?php bloginfo('url'); ?>/" method="get">
						<input id="searchfield" type="text" name="s"/>
						<input id="searchbutton" type="image" src="<?php bloginfo('template_directory'); ?>/images/search_icon.gif"/>
					</form>
				</div>
			</div>
		</div>
</div>