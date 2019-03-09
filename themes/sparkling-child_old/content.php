<?php
/**
 * @package sparkling
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="blog-item-wrap">
		<div class="post-inner-content">


			<header class="entry-header page-header">

				<h2 class="entry-title"><?php commented_checkmark(); ?> <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<div class="countdown">
				<?php /* <span class="countdown text"><?php esc_html_e('Ends in', 'sparkling-child'); ?></span>
				<span class="countdown days"><?php print(comon_expiry('days')); ?></span>
				<span class="countdown text"><?php printf(_n('day','days', comon_expiry('days')), number_format_i18n(comon_expiry('days'))); ?></span> */ ?>
				<?php printf(_n('Ends in <b>%s</b> day','Ends in <b>%s</b> days', comon_expiry('days'), 'sparkling-child'), number_format_i18n(comon_expiry('days')));  ?>
			</div>

				<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-meta">
				<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<span class="comments-link"><i class="fa fa-comment-o"></i><?php comments_popup_link( esc_html__( 'Leave a comment', 'sparkling' ), esc_html__( '1 Comment', 'sparkling' ), esc_html__( '% Comments', 'sparkling' ) ); ?></span>
				<?php endif; ?>
				<?php edit_post_link( esc_html__( 'Edit', 'sparkling' ), '<i class="fa fa-pencil-square-o"></i><span class="edit-link">', '</span>' ); ?>
				</div><!-- .entry-meta -->
				<?php endif; ?>

				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
			 	<?php the_post_thumbnail( 'sparkling-featured', array( 'class' => 'single-featured' )); ?>
			</a>
			</header><!-- .entry-header -->

			<?php if ( is_search() ) : // Only display Excerpts for Search ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
				<p><a class="btn btn-default read-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'sparkling' ); ?></a></p>
			</div><!-- .entry-summary -->
			<?php else : ?>
			<div class="entry-content">

				<?php
				if ( get_theme_mod( 'sparkling_excerpts' ) == 1 ) :
					the_excerpt();?>
					<p><a class="read-more" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php esc_html_e( 'Read More', 'sparkling' ); ?></a></p>
				<?php else :
					the_content();
				endif;
				 ?>

				<?php
					wp_link_pages( array(
						'before'            => '<div class="page-links">'.esc_html__( 'Pages:', 'sparkling' ),
						'after'             => '</div>',
						'link_before'       => '<span>',
						'link_after'        => '</span>',
						'pagelink'          => '%',
						'echo'              => 1
		       		) );
		    	?>
			</div><!-- .entry-content -->
			<?php endif; ?>
		</div>
	</div>
</article><!-- #post-## -->
