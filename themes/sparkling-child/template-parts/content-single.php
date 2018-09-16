<?php
/**
 * @package sparkling
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php // the_post_thumbnail( 'sparkling-featured', array( 'class' => 'single-featured' )); ?>
	<div class="post-inner-content">
				<?php /* sparkling_posted_on(); */ ?>
		<header class="entry-header page-header">

			<h1 class="entry-title" style="margin-bottom: 10px;"><?php the_title(); ?><small class="title-edit"><?php edit_post_link('<i class="fas fa-edit" aria-hidden="true"></i>',"  ",""); ?></small></h1>
			<p class="page-subtitle"><?php printf(_n('Ends in <b>%s</b> day','Ends in <b>%s</b> days', comon_expiry('days'), 'sparkling-child'), number_format_i18n(comon_expiry('days')));
			if ( get_field('private_comments') ) {  print(esc_html_x(' | Private comments enabled', 'Message to admin below post title.','sparkling-child')); } ?> </p>

			<div class="entry-meta">

				<?php
					/* translators: used between list items, there is a space after the comma */
					$categories_list = get_the_category_list( esc_html__( ', ', 'sparkling' ) );
					if ( $categories_list && sparkling_categorized_blog() ) :
				?>
				<span class="cat-links"><i class="fa fa-folder-open-o"></i>
					<?php printf(' %1$s' , $categories_list ); ?>
				</span>
				<?php endif; // End if categories ?>
				<?php edit_post_link( esc_html_x( 'Edit', 'Edit post link', 'sparkling-child'), '<i class="fa fa-pencil-square-o"></i><span class="edit-link">', '</span>' ); ?>

			</div><!-- .entry-meta -->
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content(); ?>
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

		<footer class="entry-meta">

	    	<?php if(has_tag() && False ) : ?>
	      <!-- tags -->
	      <div class="tagcloud">

	          <?php
	              $tags = get_the_tags(get_the_ID());
	              foreach($tags as $tag){
	                  echo '<a href="'.get_tag_link($tag->term_id).'">'.$tag->name.'</a> ';
	              } ?>

	      </div>
	      <!-- end tags -->
	      <?php endif; ?>

		</footer><!-- .entry-meta -->
	</div>

	<?php if (get_the_author_meta('description') && False ) :  ?>
		<div class="post-inner-content secondary-content-box">
      <!-- author bio -->
      <div class="author-bio content-box-inner">

        <!-- avatar -->
        <div class="avatar">
            <?php echo get_avatar(get_the_author_meta('ID') , '60'); ?>
        </div>
        <!-- end avatar -->

        <!-- user bio -->
        <div class="author-bio-content">

          <h4 class="author-name"><a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php echo get_the_author_meta('display_name'); ?></a></h4>
          <p class="author-description">
              <?php echo get_the_author_meta('description'); ?>
          </p>

        </div><!-- end .author-bio-content -->

      </div><!-- end .author-bio  -->

		</div>
		<?php endif; ?>

</article><!-- #post-## -->
