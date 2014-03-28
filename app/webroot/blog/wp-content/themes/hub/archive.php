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

					<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
					<?php /* If this is a category archive */ if (is_category()) { ?>
						<h2 class="search">Archiv f&uuml;r Kategorie: <?php single_cat_title(); ?></h2>
					<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
						<h2 class="search">Posts Tagged: <?php single_tag_title(); ?></h2>
					<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
						<h2 class="search">Archiv f&uuml;r: <?php the_time('d.m.Y'); ?>:</h2>
					<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
						<h2 class="search">Archiv f&uuml;r: <?php the_time('F, Y'); ?></h2>
					<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
						<h2 class="search">Archiv f&uuml;r: <?php the_time('Y'); ?>:</h2>
					<?php /* If this is an author archive */ } elseif (is_author()) { ?>
						<h2 class="search">Author Archiv</h2>
					<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
						<h2 class="search">Blog Archive</h2>
					<?php } ?>
      
					<?php while (have_posts()) : the_post(); ?>

					<div class="post" id="post-<?php the_ID(); ?>">
					
					<div class="post_header">

						<div class="post_title">
						<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<div class="posted"><?php the_time('d.m.Y'); ?>, Erstellt von: <?php the_author_posts_link(); ?> </div>
						</div>
						<br clear="all" />

						<div class="tags"><?php the_tags('Tags: ', ', ', '<br />'); ?></div>
					</div>
						
					<div class="post_body">	
					<?php the_content('Read more &raquo;'); ?>
					</div>
					
					<div class="meta">
					Abgelegt unter: <?php the_category(', ') ?> | <?php edit_post_link('Bearbeiten', '', ' | '); ?>  <?php comments_popup_link('Kein Kommentar &#187;', '1 Kommentar &#187;', '% Kommentare &#187;'); ?>
					</div><!--end of meta -->

					</div><!--end of post -->

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
