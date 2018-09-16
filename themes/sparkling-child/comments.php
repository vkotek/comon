<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package sparkling
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

?>

		<?php
			/* Checks if paging is turned off via GET in URL */
			if($_GET['page'] == "off") {
				$paging = "1000";
			} else {
				$paging = get_option( 'comments_per_page' );
			}
		?>

<div id="comments" class="comments-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if( comon_expiry() ) { comment_form(); } else { $paging = "1000"; } ?>

	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php
                if( current_user_can('edit_posts') ) {
                    printf( _nx( 'One comment', '%1$s comments', get_comments_number(), 'comments title', 'sparkling-child' ),
                        number_format_i18n( get_comments_number() ));
                    if( current_user_can('edit_posts') ) { // Admin comment tools
                        printf( _nx( ' from %1$s user', ' from %1$s users', comments_unique_users(), 'comments user count', 'sparkling-child' ),
                            number_format_i18n( comments_unique_users() ));
                        printf('  <a href="%s?page=off" title="%s"><i class="fa fa-clone fa-x2"></i></a>', get_permalink(), _x("Show comments on one page", "Alt text for icon above comments", "sparkling-child") );
                        $csv = plugins_url('comon-plugin/csv-comments.php')."?post=".get_the_ID();
                        printf('  <a href="%s" title="%s"><i class="fa fa-file-excel fa-x2"></i></a>', $csv, _x("Download comments in CSV", "Alt text for icon above comments","sparkling-child"));
                        print('  <a href="javascript:window.print()"><i class="fa fa-print fa-x2"></i></a>');
                    }
                } else {
                    esc_html_e( 'Comments:', 'sparkling-child' );
                }

			?>
		</h3>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) && $paging != "1000" ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="comment-navigation" role="navigation">
			<?php paginate_comments_links(); ?>
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'callback'		=> 'comon_comment',
					'style'      	=> 'ol',
					'short_ping' 	=> true,
					'avatar_size' 	=> 60,
					'per_page' 		=> ''. $paging .''
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) && $paging != "1000"  ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="comment-navigation" role="navigation">
			<?php paginate_comments_links(); ?>
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'sparkling' ); ?></p>
	<?php endif; ?>


</div><!-- #comments -->
