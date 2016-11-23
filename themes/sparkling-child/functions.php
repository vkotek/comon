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
<link href="http://www.millwardbrown.com/favicon.ico" rel="shortcut icon" type="image/x-icon">
<?php }


/* Change logo to custom one on login page */
function my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/logo.png);
            padding-bottom: 0px;
			height: 50px;
			-webkit-background-size: 200px;
			background-size: 200px;
			width: 200px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

/* User warning at top of page if user has not filled in his profile */
function user_warning() {
	if(is_user_logged_in() AND !bp_get_profile_field_data('field=139&user_id='.bp_loggedin_user_id())):
		$message = "Prosíme abyste si před používáním komunity vyplnili profilové údaje.";
		$btn_msg = "vyplnit";
		$btn_url = bp_loggedin_user_domain() . "profile/edit/group/2/";
		printf('<div class="cfa"><div class="container"><div class="col-sm-8"><span class="cfa-text">%s</span></div><div class="col-sm-4"><a class="btn btn-lg cfa-button" href="%s">%s</a></div></div></div>', $message, $btn_url, $btn_msg);
	endif;
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
	$comments_query = new WP_Comment_Query;
	
	$comment_args = array(
        'post_id' 	=> $post->ID,
        'user_id' 	=> get_current_user_id(),
        'count' 	=> true	
    );

	$user_comments = $comments_query->query( $comment_args );
	
	if( $user_comments > 0 ) { 
		$checkmark_status = "fa fa-check-square-o";
		$checkmark_title = "Na toto téma jste již reagovali";
	} else { 
		$checkmark_status = "fa fa-square-o";
		$checkmark_title = "Na toto téma jste ještě nereagovali";
	}
	
	printf('<i class="%s" title="%s"></i>',$checkmark_status,$checkmark_title);
}

// Hide update nags
function fuck_nags() {
   echo '<style type="text/css">
           div.update-nag{display:none}
         </style>';
}

add_action('admin_head', 'fuck_nags');