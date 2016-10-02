<?php

/*
Plugin Name: Email Users Extended
Version: 0.6
Plugin URI: 
Description: Extension to the Email Users plugin with additional functionalities.
Author: Srini
Author URI: 
*/



function email_users_extended_admin_menus() {
	add_submenu_page( 'email-users/email-users.php', "Send to User(s) +", "Send to User(s) +", MAILUSERS_EMAIL_SINGLE_USER_CAP, 'mail-users-ext-email-select-users', 'email_users_extended_email_select_users_admin' );
}
add_action('admin_menu', 'email_users_extended_admin_menus');

function email_users_extended_email_select_users_admin() {
	require_once('email_users_extended_send_user_mail.php');
}

function email_users_extended_submenu_order($menu_order) {
	global $submenu;
	$email_users_menu = array();
	$email_users_menu[] = $submenu['email-users/email-users.php'][0];
	$email_users_menu[] = $submenu['email-users/email-users.php'][1];
	$email_users_menu[] = $submenu['email-users/email-users.php'][4];
	$email_users_menu[] = $submenu['email-users/email-users.php'][2];
	$email_users_menu[] = $submenu['email-users/email-users.php'][3];
	$submenu['email-users/email-users.php'] = $email_users_menu;
	return $menu_order;
}
add_filter('custom_menu_order', 'email_users_extended_submenu_order');


function email_users_extended_get_send_users() {
	$users = get_users();
	
	//echo "<pre>";
	//print_r($_REQUEST);
	// print_r($users);
	//echo "</pre>";

	$send_users = array();
	$omit_users = array();

	foreach($users as $user) {

		// We don't want to send email to the user who is sending the email
		if($user->ID == get_current_user_id())
			continue;

		// Administrators won't get emails
		if(isset($user->caps['administrator']) && $user->caps['administrator'] == 1)
			continue;

		// Get the extended profile details
		$extended_profile = email_users_extended_get_extended_profile($user->ID);
		//echo "<pre>";
		//print_r($extended_profile);
		//echo "</pre>";

		// Initializing the flag
		$select_user = true;
		
		
		foreach($extended_profile as $field) {
	
		
			// Gender
			if( $field['id'] == 'field_139' && !in_array( get_value($field['value']) , $_REQUEST['email_users_ext_gender'] ) ) {
				$select_user = false;
				break;
			}
			// Age
			if( $field['id'] == 'field_142' ) {
				if($field['value'] < $_REQUEST['email_users_ext_age_min'] || $field['value'] > $_REQUEST['email_users_ext_age_max']) {
					$select_user = false;
					break;
				}
			}
			// City
			if( $field['id'] == 'field_143' && !in_array( get_value($field['value']) , $_REQUEST['email_users_ext_city'] ) ) {
				$select_user = false;
				break;
			}
			// Education
			if( $field['id'] == 'field_186' && !in_array( get_value($field['value']) , $_REQUEST['email_users_ext_edu'] ) ) {
				$select_user = false;
				break;
			}
			
			
		}
		// If the above loop hasn't 'broken', the user is good to go
		if($select_user === true) {
			$send_users[] = $user->ID;
		} else {
			$omit_users[] = $user->ID;
		}
	}
	
	printf("Sent: %d ",sizeof($send_users));
	printf("Omitted: %d ",sizeof($omit_users));

	return $send_users;
}


function email_users_extended_get_extended_profile( $user_id ) {
		
	$fields = array();
	
	// Get User Extended Data
	$r = bp_parse_args( $args['args'], array(
		'profile_group_id' => 0,
		'user_id'          =>  $user_id
	), 'bp_xprofile_user_admin_profile_loop_args' );

	$i = 0;

	if ( bp_has_profile( $r ) ) {

		while ( bp_profile_groups() ) {

			bp_the_profile_group(); 

			while ( bp_profile_fields() ) {

				bp_the_profile_field();
				$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );

				$fields[ $i ]['name'] = bp_get_the_profile_field_name();
				$fields[ $i ]['id'] = bp_get_the_profile_field_input_name();
				$fields[ $i ]['value'] = bp_get_the_profile_field_edit_value();

				$i++;
			}
		}
	}
	return $fields;
}

function get_value($value) {
	$pos = strpos($value, ")");
	if(!$pos) { 
		$pos = strpos($value, " ");
	}
	if($pos) {
		$value =  substr($value,0,$pos); 
	}
    return $value;
}

?>