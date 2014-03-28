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
					
						<div id="bloginfo">
							<div id="blogtitle"><h2><?php bloginfo('name'); ?></h2></div>
							<div id="blogslogan"><?php bloginfo('description'); ?></div>
						</div>

						<?php if (have_posts()) : ?>
						<?php while (have_posts()) : the_post(); ?>

						<div class="post" id="post-<?php the_ID(); ?>">

						<div class="post_header">
							<div class="post_title">
								<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
							</div>
							<br clear="all" />
						</div>
						<div class="post_body">
							<?php the_content('Read more &raquo;'); ?>
						</div>

						</div>

						<?php endwhile; ?>
		
						<div class="navigation">
							<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
							<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
						</div>
				
						<?php else : ?>
						<?php endif; ?>
					</div><!--end of entry -->
				</div><!--end of blog -->
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

