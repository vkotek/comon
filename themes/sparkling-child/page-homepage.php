<?php
/**
 * Template Name: Home ( Active topics )
 *
 * This is the template that displays full width page without sidebar
 *
 * @package sparkling
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		$post_query = new WP_Query(array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1));

		if ( $post_query->have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>

				<?php

				    if ( comon_expiry() ) : // True if post has not expired yet
				        if ( comon_data_filter() ) : // True if all post and user criteria match
					        get_template_part( 'content', get_post_format() );
					    endif;
					endif;
				?>

			<?php endwhile; ?>

			<?php sparkling_paging_nav(); ?>

		<?php else : ?>

			<?php // get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
