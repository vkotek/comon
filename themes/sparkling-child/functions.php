<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'color-style', get_stylesheet_directory_uri() . '/color.css' );
}

add_action( 'after_setup_theme', function () {
    /* load translation file for the child theme */
    load_child_theme_textdomain( 'sparkling-child', get_stylesheet_directory() . '/languages' );
} );

/* Add favicon from MB severs */
add_action ('wp_head', 'add_favicon');
function add_favicon() { ?>
<link href="http://www.kantar.com/favicon.ico" rel="shortcut icon" type="image/x-icon">
<?php }

/* Change logo to custom one on login page */
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/logo.png);
            padding-bottom: 0px;
			height: 25px;
			-webkit-background-size: 133px;
			background-size: 133px;
			width: 133px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

/**
 * Filter Force Login to allow exceptions for specific URLs.
 *
 * @return array An array of URLs. Must be absolute.
 */
function my_forcelogin_whitelist( $whitelist ) {
  $whitelist[] = site_url( '/register/' );
  $whitelist[] = site_url( '/activate/' );
  $whitelist[] = site_url( '/activate/*' );
  return $whitelist;
}
add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist', 10, 1);

function my_forcelogin_bypass( $bypass ) {
  if ( function_exists('bp_is_register_page') ) {
    if ( bp_is_register_page() || bp_is_activation_page() ) {
      $bypass = true;
    }
  }
  return $bypass;
}
add_filter('v_forcelogin_bypass', 'my_forcelogin_bypass', 10, 1);


/* User warning at top of page if user has not filled in his profile */
function sparkling_call_for_action() {
  if ( is_front_page() &&
        of_get_option( 'w2f_cfa_text' )!='' &&
        is_user_logged_in() &&
        !bp_get_profile_field_data('field=139&user_id='.bp_loggedin_user_id())
    ){
    $btn_url = bp_loggedin_user_domain() . "profile/edit/group/1/";
    echo '<div class="cfa">';
      echo '<div class="container">';
        echo '<div class="col-sm-8">';
          echo '<span class="cfa-text">'. of_get_option( 'w2f_cfa_text' ).'</span>';
          echo '</div>';
          echo '<div class="col-sm-4">';
          echo '<a class="btn btn-lg cfa-button" href="'. $btn_url .'">'. of_get_option( 'w2f_cfa_button' ). '</a>';
          echo '</div>';
      echo '</div>';
    echo '</div>';
  }
}

/* Returns # of unique commentators in discussion */
function comments_unique_users() {
	global $wpdb;
	$unique = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT( DISTINCT comment_author_email )
		FROM wp_comments
		WHERE comment_post_ID = %s;", get_the_ID() ));
	return $unique;
}

/* Checks if user has already commented in post */
function commented_checkmark() {

	global $wpdb, $post;
	// $comments_query = new WP_Comment_Query;

	$comment_args = array(
        'post_id' 	=> $post->ID,
        'user_id' 	=> get_current_user_id(),
        'count' 	=> true
    );

  $user_comments = get_comments($comment_args);

	if( $user_comments > 0 ) {
		$checkmark_status = "fas fa-check-square";
		$checkmark_title = _x("You have commented in this topic.",'Checkmark alt text for topics.','sparkling-child');
	} else {
		$checkmark_status = "fas fa-square";
		$checkmark_title = _x("You have not commented on this topic.",'Checkmark alt text for topics.','sparkling-child');
	}

	printf('<i class="%s" title="%s"></i>',$checkmark_status,$checkmark_title);
}

// Hide update nags in backend
function no_nags() {
   echo '<style type="text/css">
           div.update-nag{display:none}
         </style>';
}
add_action('admin_head', 'no_nags');

// Hide admin bar from subscribers
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
  if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
  }
}

// Custom comment template
function comon_comment($comment, $args, $depth) {
  include('comment-format.php');
}
