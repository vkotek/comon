<?php
/*
 * Blog Comments in Buddypress Activity
 * WARNING: Test thoroughly if it works in your environment before using in production code.
 * LICENSE: Public domain
 */
 
 
/*
 * When a new comment gets added to the database, add this comment to the
 * activity stream
 */
 
 
/*
function bca_record_activity($comment_id, $approval) {
    if($approval == 1) {
        $comment = get_comment($comment_id);
        $userlink = bp_core_get_userlink($comment->user_id);
        $postlink = '<a href="' . get_permalink($comment->comment_post_ID) . '">' 
                        . get_the_title($comment->comment_post_ID) . '</a>';
 
        bp_activity_add(array(
            'action'            => sprintf( __( '%1$s commented on the post, %2$s', 'buddypress' ), 
                                                $userlink, $postlink),
            'content'           => $comment->comment_content,
            'component'         => 'bp_plugin',
            'user_id'           => $comment->user_id,
            'type'              => 'new_blog_comment',
        ));
     
    }
}
//comment_post is triggered "just after a comment is saved in the database".
add_action('comment_post', 'bca_record_activity', 10, 2);
*/


/*
 * We want activity entries of blog comments to be shown as "mini"-entries
 */
function bca_minify_activity($array) {
    $array[] = 'new_blog_comment';
    return $array;
}
add_filter('bp_activity_mini_activity_types', 'bca_minify_activity');


/*
 * Disables comments on this type of activity entry
 */
function bca_remove_commenting($can_comment) {
    if($can_comment == true) {
		$can_comment = ! ('new_blog_comment' == bp_get_activity_action_name());
    }
    return $can_comment;
}
add_filter('bp_activity_can_comment', 'bca_remove_commenting');

// Show all users on autocomplete in messages
define( 'BP_MESSAGES_AUTOCOMPLETE_ALL', true );

?>
