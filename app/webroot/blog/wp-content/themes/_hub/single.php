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
							<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
							<div class="posted"><?php the_time('d.m.Y'); ?>, Erstellt von: <?php the_author_posts_link(); ?> </div>
						</div>

						<br clear="all" />

						<div class="tags"><?php the_tags('Tags: ', ', ', '<br />'); ?></div>
					</div>
					
					<div class="post_body">	
					<?php the_content('Read more &raquo;'); ?>
					</DIV>
					
					<div class="meta">
					Abgelegt unter: <?php the_category(', ') ?> | <?php edit_post_link('Bearbeiten', '', ' | '); ?>  <?php comments_popup_link('Keine Kommentare &#187;', '1 Kommentar &#187;', '% Kommentare &#187;'); ?>
					</div>
					<?php comments_template(); ?>
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
