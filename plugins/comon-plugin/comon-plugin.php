<?php
/**
 * Plugin Name: COM.ON plugin
 * Description: Includes customized latest posts widget and IMG zip download widget, as well as export function for Excel API.
 * Version: 1.2
 * Author: Vojtěch Kotek
 * Author URI: http://kotek.co
 * Author Email: kotek.vojtech@gmail.com
 * License: GPL2
 * Text Domain: comon-plugin
 * Domain Path: /languages/
 */
 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// define plugin url
define('zipfile_url', plugins_url()."/".dirname( plugin_basename( __FILE__ ) ) );


function my_load_plugin_textdomain() {
  load_plugin_textdomain( 'comon-plugin', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'my_load_plugin_textdomain' );

/*
 __    __ _     _            _       
/ / /\ \ (_) __| | __ _  ___| |_ ___ 
\ \/  \/ / |/ _` |/ _` |/ _ \ __/ __|
 \  /\  /| | (_| | (_| |  __/ |_\__ \
  \/  \/ |_|\__,_|\__, |\___|\__|___/ 
                  |___/              
*/

/* Show attachment images widget */

// register Imgs_Widget widget
function register_Imgs_Widget() {
    register_widget( 'Imgs_Widget' );
}
add_action( 'widgets_init', 'register_Imgs_Widget' );
class Imgs_Widget extends WP_Widget {
	

	function __construct() {
		parent::__construct(
			'Imgs_Widget', // Base ID
			__( 'COM.ON - IMGs from comments', 'comon-plugin' ), // Name
			array( 'description' => __( 'Displays all image attachements from current post + ZIP download for admins', 'comon-plugin' ), ) // Args
		);
	}
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		if ( is_single() ) {
			
			echo $args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
			
			$queried_object = get_queried_object();
			$post_id = $queried_object->ID;
			
			$post_args = array (
				'post_id'	=> $post_id,
			);
			
			$comment_query = new WP_Comment_Query;
			$comments = $comment_query->query( $post_args );
		
		
			// If comments then...
			if ( $comments ) {
				$imgs = array();
				
				// Comnent loop
				foreach ( $comments as $comment ) {
					$attachmentId =  get_comment_meta($comment->comment_ID, 'attachmentId', TRUE);
					if(is_numeric($attachmentId) && !empty($attachmentId)){

						// atachement info
						$attachmentLink = wp_get_attachment_url($attachmentId);
						$attachmentThumb = wp_get_attachment_image($attachmentId, ATT_TSIZE);
						$real_path = get_attached_file( $attachmentId );
						$imgs[] = array($attachmentLink,$attachmentThumb,$real_path);
					} 
				}
				

			
				// If there are images in array, print title and thumbs
				if($imgs) {
					foreach ($imgs as $img) {
						printf ("<a href=\"%s\">%s</a>",$img[0],$img[1]);
					}
					if ( current_user_can('edit_posts') ) {
						$zip_make = zipfile_url . "/zip-make.php?p=" . $post_id;
						printf ( "<h5><a href=\"%s\"><i class=\"fa fa-download\"></i> %s</a></h5>", $zip_make, __('Download', 'comon-plugin'));
					}
				}
			}
			echo $args['after_widget'];
		}
	}
	
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
	
	
}


/* Custom posts widget */

// register Posts_Widget widget
function register_Posts_Widget() {
    register_widget( 'Posts_Widget' );
}
add_action( 'widgets_init', 'register_Posts_Widget' );
class Posts_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'Posts_Widget', // Base ID
			__( 'COM.ON - Custom Posts', 'text_domain' ), // Name
			array( 'description' => __( 'Displays posts according to user\'s details', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo "<ul>";
		do_shortcode('[active_posts]');
		echo "</ul>";
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Posts_Widget



/*
 __ _                _                _           
/ _\ |__   ___  _ __| |_ ___ ___   __| | ___  ___ 
\ \| '_ \ / _ \| '__| __/ __/ _ \ / _` |/ _ \/ __|
_\ \ | | | (_) | |  | || (_| (_) | (_| |  __/\__ \
\__/_| |_|\___/|_|   \__\___\___/ \__,_|\___||___/
                                                  
*/

// Shortcode for statistics
add_shortcode('stats','comon_stats');
function comon_stats() {
    global $wpdb;
	
	// Gets IDs of all members that are in wp_users (not deleted) and that aren't admins
	$active_users_id = "
		SELECT user_id
		FROM wp_usermeta
		WHERE wp_usermeta.meta_key = 'wp_user_level' 
		AND wp_usermeta.meta_value != 10
		AND user_id IN (
			SELECT id
			FROM wp_users
		)
	";

    // COUNT ALL MEMBERS
    $query_mem_count = "
	    SELECT COUNT(*) AS Amount
	    FROM wp_users
	    WHERE id IN (
	        ".$active_users_id."
	    )";
	
    $mem_count = $wpdb->get_results($query_mem_count);
		
	// COUNT ACTIVE MEMBERS v2
    $query_active_users = "
        SELECT COUNT(DISTINCT(user_id)) AS Amount
        FROM `wp_bp_xprofile_data`
        WHERE field_id IN (
			SELECT id
			FROM wp_bp_xprofile_fields
			WHERE group_id =2
		) AND user_id IN (
	        ".$active_users_id."
		)
		";
    $mem_count_active = $wpdb->get_results($query_active_users);

    printf('<iframe src="%s" class="iframe-stats" style="height: 550px;">Error loading iframe..</iframe>', zipfile_url."/iframe-stats.php");

    printf('<b>Members:</b> %d / %d <small>[ filled in profile / all members ]</small><br>', $mem_count_active[0]->Amount, $mem_count[0]->Amount);
    printf( '<a href="%s">User activity table</a>', zipfile_url."/activity-stats.php" );
	
}

// Shortcode to display active posts list
add_shortcode( 'active_posts', 'ideablog_active_posts' );
function ideablog_active_posts() {
    $the_query = new WP_Query( array( 'posts_per_page' => -1 ) );
    while ($the_query -> have_posts()) : $the_query -> the_post();
        if ( ideablog_data_filter() ) :
            $expiry = ideablog_time_filter();
            if ( $expiry > 0 ) : ?>
                <li><?php 
                if( current_user_can('edit_posts') && get_field('report') ){
			    printf('<a href="%s"><i class="fa fa-file-word-o" title="Stáhnout shrnutí"></i></a> | ',get_field('report'));
			    } ?>
			    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> | <?php ideablog_expires(); ?></li>
            <?php
            endif;
        endif;
    endwhile;
}


// Shortcode to display expired posts list
add_shortcode( 'expired_posts', 'ideablog_expired_posts' );
function ideablog_expired_posts() {
    
    echo "<ul>";
    $the_query = new WP_Query( array( 'posts_per_page' => -1 ) );
    while ($the_query -> have_posts()) : $the_query -> the_post();
        if ( ideablog_data_filter() ) :
            $expiry = ideablog_time_filter();
            if ( $expiry < 0 ) : ?>
                <li> <?php 
                if( current_user_can('edit_posts') && get_field('report') ){
			    printf('<a href="%s"><i class="fa fa-file-word-o" title="Stáhnout shrnutí"></i></a> | ',get_field('report'));
			    } ?>
			    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> | <?php ideablog_expired(); ?></li>
            <?php
            endif;
        endif;
    endwhile;
    echo "</ul>";
}

// Not sure if this is used anywhere??
add_shortcode('reports', 'reports_query');
function reports_query() {
	
	/*
	
	*/
	
	// Get id of all profile fields in group 2
	global $wpdb;
	$questions_query = "
		SELECT id
		FROM wp_bp_xprofile_fields
		WHERE group_id = 2 AND parent_id = 0
	";
	$questions = $wpdb->get_results($questions_query);
	
	
	// Current user's ID
	$user_id = bp_loggedin_user_id();
	
	var_dump($questions);
	echo "<hr>";
	
	
	
	// Go through each question
	foreach($questions as $q) {
		$q_data = bp_get_profile_field_data('field='.$q->id.'&user_id='.$user_id);
		
		echo "<br>".$q->id." -> ".$q_data;
	}
	echo "<hr>";
	
	$user_q1 = get_val(bp_get_profile_field_data('field=2&user_id='.$user_id));
	echo "Q1: ".$user_q1."<br>";
	$user_q2 = bp_get_profile_field_data('field=5&user_id='.$user_id);
	echo "Q2: ".$user_q2."<br>";
	$user_q3 = get_val(bp_get_profile_field_data('field=6&user_id='.$user_id));
	echo "Q3: ".$user_q3."<br>";
	$user_q4 = get_val(bp_get_profile_field_data('field=11&user_id='.$user_id));
	echo "Q4: ".$user_q4."<br>";
	
}

/*
        _                ___                 _   _                 
  /\/\ (_)___  ___      / __\   _ _ __   ___| |_(_) ___  _ __  ___ 
 /    \| / __|/ __|    / _\| | | | '_ \ / __| __| |/ _ \| '_ \/ __|
/ /\/\ \ \__ \ (__ _  / /  | |_| | | | | (__| |_| | (_) | | | \__ \
\/    \/_|___/\___(_) \/    \__,_|_| |_|\___|\__|_|\___/|_| |_|___/
                                                                   
*/

// Counts the items of serialized arrays from SQL statement in iframe stats for Q11
function ideablog_count_array($data) {
    $arr_count = array();
    foreach($data as $row) {
        $tmp = unserialize($row['Item']);   
        foreach($tmp as $item) {
            if(isset($arr_count[$item])) { $arr_count[$item]++; } else { $arr_count[$item] = 1; }
        }
    }
    return $arr_count;
}

// MAIN FILTERING FUNCTION FOR FRONT PAGE
function ideablog_data_filter() {

	$debug = false;
	
	// uncomment to BYPASS filtering system
	return true;
	
	// if User is admin, check if user is set and if yes, turn on debugging, if not set, show all posts. Else set user's id and proceed.
	if ( is_user_logged_in() ) {
		if ( current_user_can('edit_posts') ) {
			if ( is_numeric($_GET['user']) ) { 
				$user_id = $_GET['user'];
				$debug = true;
			} else {
				return true;
			}
		} else {
			$user_id = bp_loggedin_user_id();
		}
	} else {
		return true; // If user not logged in, show all posts
	}
	
	
    // Fast way to see profile extended profile
    //$extended_profile = email_users_extended_get_extended_profile($user_id);
    //echo "<pre>";
    //print_r($extended_profile);
    //echo "</pre>";
	
	
	// Split users into groups of two (odd & even IDs)
	if( $user_id % 2 == 0 ) {
		$user_group = '1'; 
	} else { 
		$user_group = '2'; 
	}

    // Get post info
	$post_gender = get_field('gender');
	$post_age_min = get_field('age_min');
	$post_age_max = get_field('age_max');
	$post_edu = get_field('education');
	$post_region = get_field('region');
	$post_h_income = get_field('h_income');
	$post_children = get_field('children');
	$post_bank = get_field('bank');
	$post_segment = get_field('segment');
	
	// Get user info
	// get_val function extracts the option number so that '13) Male' will return '13'
	$user_gender = bp_get_profile_field_data('field=139&user_id='.$user_id);
	$user_gender = get_val($user_gender);
	$user_age = bp_get_profile_field_data('field=142&user_id='.$user_id);
	$user_edu = bp_get_profile_field_data('field=186&user_id='.$user_id);
	$user_edu = get_val($user_edu);
	$user_region = bp_get_profile_field_data('field=199&user_id='.$user_id);
	$user_region = get_val($user_region);
	$user_h_income = bp_get_profile_field_data('field=216&user_id='.$user_id);
	$user_h_income = get_val($user_h_income);
	$user_children = bp_get_profile_field_data('field=245&user_id='.$user_id);
	$user_children = get_val($user_edu);
	$user_bank = bp_get_profile_field_data('field=254&user_id='.$user_id);
	$user_bank = get_val($user_edu);
	$user_segment = bp_get_profile_field_data('field=258&user_id='.$user_id);
	$user_segment = get_val($user_edu);

	
    // Show post unless any of the criteria below not satisfied
	$show = true;

	// Test post compatibility
    if ( !in_array($user_gender, $post_gender) )                                { $show = false; $break = 'gender'; }
    if ($post_age_min >= $user_age || $post_age_max <= $user_age)               { $show = false; $break = 'age'; }
    if ( !in_array($user_edu, $post_edu) )                                      { $show = false; $break = 'education'; }
    if ( !in_array($user_region, $post_region) )                                { $show = false; $break = 'region'; }
    if ( !in_array($user_h_income, $post_h_income) )                            { $show = false; $break = 'h_income'; }
    if ( !in_array($user_children, $post_children) )                            { $show = false; $break = 'children'; }
    if ( !in_array($user_bank, $post_bank) )                                    { $show = false; $break = 'bank'; }
    if ( !in_array($user_segment, $post_segment) )                              { $show = false; $break = 'segment'; }
	

	// If debug is on and above tests break:
	if ( $debug && !$show ) { 
		printf("<b>[%d] %s</b><br>", get_the_ID(), get_the_title());
		if ( true ) {
			printf("Broke at %s", $break);
			print("<br>USER: ");
			var_dump(${'user_'.$break});
			print("<br>POST: ");
			var_dump(${'post_'.$break});  
		} else {
			print("OK");
		}
		print("<hr>");
		return false;
	}
	
    // Did any of the above fail?
	if ( $show ) { return true; } else { return false; }
}

// Gets the number of the answer, used in the filtering function above
function get_val(&$value) {
	$pos = strpos($value, ")");
	if(!$pos) { 
		$pos = strpos($value, " "); 
	}
	if($pos) {	
		$value =  substr($value,0,$pos); 
	}	
    return $value;
}

// True if post is active, False if expired.
function ideablog_isactive() {
    if ( ideablog_time_filter() > 0 ) { return True; }
    return False;
}

// Returns the days until/since post expires
function ideablog_time_filter() {
    $post_expire = get_field('konec_temata');
	$expire_time = (strtotime($post_expire)-time())/86400;
	return $expire_time;
}

// Fancy expires in.. with days
function ideablog_expires($item) {
    $expiry = round(ideablog_time_filter());
    if ( $expiry == 1 ) { $days = 'den'; }
    elseif ( $expiry == 2 OR $expiry == 3 OR $expiry == 4 ) { $days = 'dny'; }
    else { $days = 'dní'; }
    if ( isset ( $item ) ) {
        if ( $item == 'number' ) { print ( $expiry ); }
        if ( $item == 'text' ) { print ( $days ); }
    } else {
        printf('<span class="posted-on"><i class="fa fa-calendar"></i> <time class="entry">Končí za %s %s</time></span>', $expiry, $days);
    }
    return "Končí za ".$expiry." ".$days;
}
// Expired X days ago
function ideablog_expired() {
    $expiry = round(ideablog_time_filter());
    if ( $expiry == 1 ) { $days = 'dnem'; }
    else { $days = 'dny'; }
    printf('<span class="posted-on"><time class="entry">Skončilo před %s %s</time></span>', abs($expiry) , $days);
    return "Před ".$expiry." ".$days;
}

function user_comment_count() {
    global $wpdb;
    $count = $wpdb->get_var('SELECT COUNT(comment_ID) FROM ' . $wpdb->comments. ' WHERE comment_author_email = "' . get_comment_author_email() . '"');
    return $count;
}


function get_text(&$value) {
	$pos = strpos($value, ")");
	return substr($value, $pos + 2, strlen($value));
}

// Get user meta data for comments
function userMeta($user_id) {
	
	$meta = array();
	
	/* DEFAULT META */

	// Gender
    $user_gender = bp_get_profile_field_data('field=139&user_id='.$user_id);
    $meta[] = get_text($user_gender);
	
	// Age
	$user_age = bp_get_profile_field_data('field=142&user_id='.$user_id);
	$meta[] = $user_age;
	
	// City size
	$user_city = bp_get_profile_field_data('field=143&user_id='.$user_id);
	switch ( get_value($user_city) ) {
		case "1":
			$user_city = "<15k";
			break;
		case "2":
			$user_city = "15k-25k";
			break;
		case "3":
			$user_city = "25k-50k";
			break;
		case "4":
			$user_city = "50k-100k";
			break;
		case "5":
			$user_city = "100k-400k";
			break;
		case "6":
			$user_city = "Praha";
			break;
	}
	$meta[] = $user_city;
	
	// Education level
	$user_edu = bp_get_profile_field_data('field=186&user_id='.$user_id);
	switch ( get_value($user_edu) ) {
		case "1":
			$user_edu = "ZŠ";
			break;
		case "2":
			$user_edu = "SOU";
			break;
		case "3":
			$user_edu = "SŠ";
			break;
		case "4":
			$user_edu = "VŠ";
			break;
	}
	$meta[] = $user_edu;
	
	

	/* CUSTOM META */

	// Add your custom meta here
	// Field ID can be found in the URL when editing the profile filed in Admin. (&field_id=XXX)
	// FORMAT:
	// $user_q{x} = bp_get_profile_field_data('field={y}&user_id='.$user_id);
	// $meta[] = $user_q{x};
	
	
    return(join(', ',$meta));
} 

// Custom comments template
function ideablog_comment($comment, $args, $depth) {
 $GLOBALS['comment'] = $comment; ?>
 <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
 <div id="comment-<?php comment_ID(); ?>" class="comment-body">
 <div>
 <?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>
 <?php if( user_can($comment->user_id,'edit_posts') ) { echo '<span class="moderator">MODERÁTOR</span>'; } ?>
 <?php // printf(__(' <cite><b>%s</b></cite>'), get_comment_author_link()); ?>
 <?php printf(__(' <cite><b>%s</b></cite> '), bp_core_get_userlink($comment->user_id)); ?>
 <?php 
	echo '<a href="'.wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . get_comment_author() ) .' title="Private Message""><i class="fa fa-envelope" aria-hidden="true"></i></a>';
    if( current_user_can('edit_posts') ) {
        printf(__(' <span>[%s]</span>'), userMeta($comment->user_id)); 
    }
    if ( current_user_can( 'edit_posts' ) ) {
    /*
        printf(' <a href="%s/komentare/?id=%d"><i class="fa fa-list" title="Komentáře: %d" style="padding: 0px 5px;"></i></a>', home_url(),$comment->user_id,user_comment_count());
        printf(' <i class="fa fa-list" title="Komentáře: %d" style="padding: 0px 5px;"></i>',user_comment_count());        
    */
    }
 ?>
 </div>
 <?php if ($comment->comment_approved == '0') : ?>
 <em><?php _e('Your comment is awaiting moderation.') ?></em>
 <br />
 <?php endif; ?>
 <div><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>
 <?php comment_text() ?>
 <div class="comment-actions">
	<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	<?php
	if(function_exists('like_counter_c')) { like_counter_c('text for like'); }
	?>
 </div>
 </div>
 <?php
}

// Hide admin bar from subs
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

// Add unread message count to main menu
// add_filter('wp_nav_menu_items','comon_msg', 10, 2);
function comon_msg( $nav, $args ) {
    if( $args->theme_location == 'primary' )
        return $nav.'<li>MSG!</li>';
    return $nav;
}


/* Stop Adding Functions Below this Line */
?>
