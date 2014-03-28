<?php get_header(); ?>

<div id="main">
	<div id="main-left">
		<div id="main-left-top">
			<div id="main-left-top-inner">
				<?php include (TEMPLATEPATH . '/sitedeskriptor.php'); ?>
			</div>
		</div>
		
		<div id="main-left-bottom">
			<?php include (TEMPLATEPATH . '/sidebar1.php'); ?>
		</div>
	</div>
	
	<div id="main-right">
		
		<?php include (TEMPLATEPATH . '/header_site.php'); ?>
		
	
		<div id="main-right-bottom">
	
			<div id="main-right-bottom-left">
	
				<div id="blog">
					<div class="entry">

					<?php if (have_posts()) : ?>
					<?php while (have_posts()) : the_post(); ?>

					<div class="post" id="post-<?php the_ID(); ?>">
					
					<div class="post_header">
						<div class="post_title">
							<h2><?php the_title(); ?></h2>
						</div>
						<br clear="all" />	
					</div>
						
					<div class="post_body">	
					<?php the_content('Read more &raquo;'); ?>
					</div>
						
					<div class="meta">
						<?php the_time('d.m.Y'); ?> | Erstellt von: <?php the_author_posts_link(); ?> | <?php edit_post_link('Bearbeiten', '', ''); ?>
					</div>

					</div>

					<?php endwhile; ?>
					<?php else : ?>
					<?php endif; ?>

				</div>
				</div>
				<br clear="left" />
			</div>
			<div id="main-right-bottom-right">
				<?php include (TEMPLATEPATH . '/sidebar2.php'); ?>
			</div>
		</div>
	</div>	
</div>	
		<br clear="all" />
		
<?php get_footer(); ?>
</div>
</div>
