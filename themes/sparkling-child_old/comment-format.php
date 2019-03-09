<?php $GLOBALS['comment'] = $comment; ?>
<?php
// If comments are private, hide all comment threads started by other users.
$private_comments_enabled = get_field('private_comments');

if( $private_comments_enabled && !current_user_can('edit_posts') ) {
    if( $comment->comment_parent == 0 && $comment->user_id != get_current_user_id() ) {
        $hide_comments = True;
    } elseif ( $comment->comment_parent == 0 ) {
        $hide_comments = False;
    }
} else {
  $hide_comments = False;
}
?>
<li <?php comment_class(($hide_comments ? 'hide' : 'show' )); ?> id="li-comment-<?php comment_ID() ?>">
<div id="comment-<?php comment_ID(); ?>" class="comment-body">

<?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>
<?php if( user_can($comment->user_id,'edit_posts') ) {
  printf('<span class="moderator">%s</span>', _x("MODERATOR",'Label for admins in comments.','sparkling-child'));
 } ?>
<?php printf(' <cite><b>%s</b></cite> ', bp_core_get_userlink($comment->user_id)); ?>
<?php
 $msg_link = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . get_comment_author() );
 printf('<a href="%s" title="Private Message"><i class="fa fa-envelope" aria-hidden="true"></i></a>', $msg_link);
   if( current_user_can('edit_posts') && !user_can($comment->user_id,'edit_posts') ) {
       printf('<span>  [%s]</span>', userMeta($comment->user_id));
   }
?>
<?php if ($comment->comment_approved == '0') : ?>
<em><?php _e('Your comment is awaiting moderation.') ?></em>
<?php endif; ?>
<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(_x('  [%1$s at %2$s]','Timestamp on comment','sparkling-child'), get_comment_date(), get_comment_time()) ?></a>
<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<i class="fa fa-reply"></i>'))) ?>

<?php comment_text() ?>
</div>
