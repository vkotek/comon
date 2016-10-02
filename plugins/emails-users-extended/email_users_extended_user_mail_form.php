<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/*  Copyright 2006 Vincent Prat  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>

<?php
	global $user_identity, $user_email, $user_ID;

	if (!current_user_can(MAILUSERS_EMAIL_SINGLE_USER_CAP)
		&& 	!current_user_can(MAILUSERS_EMAIL_MULTIPLE_USERS_CAP)) {
        wp_die(printf('<div class="error fade"><p>%s</p></div>',
            __('You are not allowed to send emails to users.', MAILUSERS_I18N_DOMAIN)));
	}

	if (!isset($send_users)) {
		$send_users = array();
	}

	if (!isset($mail_format)) {
		$mail_format = mailusers_get_default_mail_format();
	}

	if (!isset($subject)) {
		$subject = '';
	}

	if (!isset($mail_content)) {
		$mail_content = '';
	}

	get_currentuserinfo();

	$from_name = $user_identity;
	$from_address = $user_email;
    $override_name = mailusers_get_from_sender_name_override() ;
    $override_address = mailusers_get_from_sender_address_override() ;

	/*
    if(isset($_REQUEST['email_users_ext_pohlavi'])) {
        switch( $_REQUEST['email_users_ext_pohlavi'] ) {
            case 'Muž':
                $pohlavi_muz_checked = ' checked="checked"';
                break;
            case 'Žena':
                $pohlavi_zena_checked = ' checked="checked"';            
                break;
            default:
                $pohlavi_all_checked = ' checked="checked"';            
        }
    } else {
        $pohlavi_all_checked = ' checked="checked"';
    }

    if(isset($_REQUEST['email_users_ext_operator'])) {
        switch( $_REQUEST['email_users_ext_operator'] ) {
            case 'Vodafone':
                $operator_vodafone_checked = ' checked="checked"';
                break;
            case 'Ostatní':
                $operator_ostatni_checked = ' checked="checked"';            
                break;
            default:
                $operator_all_checked = ' checked="checked"';            
        }
    } else {
        $operator_all_checked = ' checked="checked"';            
    }

    if(isset($_REQUEST['email_users_ext_smlouva'])) {
        switch( $_REQUEST['email_users_ext_smlouva'] ) {
            case 'Prepaid':
                $smlouva_prepaid_checked = ' checked="checked"';
                break;
            case 'Postpaid':
                $smlouva_postpaid_checked = ' checked="checked"';            
                break;
            default:
                $smlouva_all_checked = ' checked="checked"';            
        }
    } else {
        $smlouva_all_checked = ' checked="checked"';
    }

    if(isset($_REQUEST['email_users_ext_smartphone'])) {
        switch( $_REQUEST['email_users_ext_smartphone'] ) {
            case 'Ano':
                $smartphone_ano_checked = ' checked="checked"';
                break;
            case 'ne':
                $smartphone_ne_checked = ' checked="checked"';            
                break;
            default:
                $smartphone_all_checked = ' checked="checked"';            
        }
    } else {
        $smartphone_all_checked = ' checked="checked"';
    }


    if(isset($_REQUEST['email_users_ext_mobilu'])) {
        switch( $_REQUEST['email_users_ext_mobilu'] ) {
            case 'Ano':
                $mobilu_ano_checked = ' checked="checked"';
                break;
            case 'ne':
                $mobilu_ne_checked = ' checked="checked"';            
                break;
            default:
                $mobilu_all_checked = ' checked="checked"';            
        }
    } else {
        $mobilu_all_checked = ' checked="checked"';
    }

    if(
        isset($_REQUEST['email_users_ext_vek_od']) 
        && is_numeric($_REQUEST['email_users_ext_vek_od'])
        && $_REQUEST['email_users_ext_vek_od'] >= 0
        && $_REQUEST['email_users_ext_vek_od'] < 100
    ) {
        $vek_od = $_REQUEST['email_users_ext_vek_od'];
    } else {
        $vek_od = 0;
    }

    if(
        isset($_REQUEST['email_users_ext_vek_do']) 
        && is_numeric($_REQUEST['email_users_ext_vek_do'])
        && $_REQUEST['email_users_ext_vek_do'] >= 0
        && $_REQUEST['email_users_ext_vek_do'] < 100
    ) {
        $vek_do = $_REQUEST['email_users_ext_vek_do'];
    } else {
        $vek_do = 99;
    }
	*/

?>
<div class="wrap">
	<div id="icon-users" class="icon32"><br/></div>
	<h2><?php _e('Send an Email to Select Users', MAILUSERS_I18N_DOMAIN); ?></h2>

	<?php 	if (isset($err_msg) && $err_msg!='') { ?>
			<div class="error fade"><p><?php echo $err_msg; ?></p></div>
			<p><?php _e('Please correct the errors displayed above and try again.', MAILUSERS_I18N_DOMAIN); ?></p>
	<?php	} ?>

	<form name="SendEmail" action="" method="post">
		<input type="hidden" name="send" value="true" />
		<input type="hidden" name="fromName" value="<?php echo $from_name;?>" />
		<input type="hidden" name="fromAddress" value="<?php echo $from_address;?>" />

		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
		<tr>
			<th scope="row" valign="top"><?php _e('Mail format', MAILUSERS_I18N_DOMAIN); ?></th>
			<td><select name="mail_format" style="width: 158px;">
				<option value="html" <?php if ($mail_format=='html') echo 'selected="selected"'; ?>><?php _e('HTML', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="plaintext" <?php if ($mail_format=='plaintext') echo 'selected="selected"'; ?>><?php _e('Plain text', MAILUSERS_I18N_DOMAIN); ?></option>
			</select></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="fromName"><?php _e('Sender', MAILUSERS_I18N_DOMAIN); ?></label></th>
            <?php if (empty($override_address)) { ?>
			<td><?php echo $from_name;?> &lt;<?php echo $from_address;?>&gt;</td>
            <?php } else { ?>
            <td><input name="from_sender" type="radio" value="0" checked/><?php echo $from_name;?> &lt;<?php echo $from_address;?>&gt;<br/><input name="from_sender" type="radio" value="1"/><?php echo $override_name;?> &lt;<?php echo $override_address;?>&gt;</td>
            <?php }?>
        </tr>
        <tr>
            <th scope="row" valign="top"><label><?php _e('Recipient options', MAILUSERS_I18N_DOMAIN); ?></label></th>
            <td>
                <table>
                    <tr>
                        <th scope="row" valign="top"><label for="">Group</label></th>
                        <td>
                            <input type="checkbox" name="email_users_ext_group[]" value="1" checked="checked">1<br/>
                            <input type="checkbox" name="email_users_ext_group[]" value="2" checked="checked">2<br/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top"><label for="">Gender</label></th>
                        <td>
                            <input type="checkbox" name="email_users_ext_gender[]" value="1" checked="checked">Muž<br/>
                            <input type="checkbox" name="email_users_ext_gender[]" value="2" checked="checked">Žena<br/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top"><label for="email_users_ext_age_min">Age min</label></th>
                        <td>
                            <input type="number" id="email_users_ext_age_min" name="email_users_ext_age_min" min="0" max="99" step="1" value="0" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top"><label for="email_users_ext_age_max">Age max</label></th>
                        <td>
                            <input type="number" id="email_users_ext_age_max" name="email_users_ext_age_max" min="0" max="99" step="1" value="99" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top"><label for="">City size<label></th>
                        <td>
                            <input type="checkbox" name="email_users_ext_city[]" value="1" checked="checked"> less than 15k<br/>
                            <input type="checkbox" name="email_users_ext_city[]" value="2" checked="checked">15 to 25k<br/>
                            <input type="checkbox" name="email_users_ext_city[]" value="3" checked="checked">25 to 50k<br/>
                            <input type="checkbox" name="email_users_ext_city[]" value="4" checked="checked">50 to 100k<br/>
                            <input type="checkbox" name="email_users_ext_city[]" value="5" checked="checked">100 to 400k<br/>
                            <input type="checkbox" name="email_users_ext_city[]" value="6" checked="checked">Praha<br/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top"><label for="">Education level<label></th>
                        <td>
                            <input type="checkbox" name="email_users_ext_edu[]" value="1" checked="checked">ZŠ<br/>
                            <input type="checkbox" name="email_users_ext_edu[]" value="2" checked="checked">SOU<br/>
                            <input type="checkbox" name="email_users_ext_edu[]" value="3" checked="checked">SŠ<br/>
                            <input type="checkbox" name="email_users_ext_edu[]" value="4" checked="checked">VŠ<br/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
		<tr>
			<th scope="row" valign="top"><label for="subject"><?php _e('Subject', MAILUSERS_I18N_DOMAIN); ?></label></th>
			<td><input type="text" id="subject" name="subject" value="<?php echo format_to_edit($subject);?>" style="width: 647px;" /></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="mailcontent"><?php _e('Message', MAILUSERS_I18N_DOMAIN); ?></label></th>
			<td>
				<div id="mail-content-editor" style="width: 647px;">
				<?php
					if ($mail_format=='html') {
						wp_editor(stripslashes($mail_content), "mailcontent");
					} else {
				?>
					<textarea rows="10" cols="80" name="mailcontent" id="mailcontent" style="width: 647px;"><?php echo stripslashes($mail_content);?></textarea>
				<?php 
					}
				?>
				</div>
			</td>
		</tr>
		</table>

		<p class="submit">
			<input class="button-primary" type="submit" name="Submit" value="<?php _e('Send Email', MAILUSERS_I18N_DOMAIN); ?> &raquo;" />
		</p>
	</form>
</div>
<?php
    //  Check to see if number of users in select list will exceed the
    //  PHP INI max_input_vars setting.  If it does and the user selects
    //  more users than the max_input_vars value (minus some overhead for
    //  other form fields) the form will be redisplayed without the subject
    //  and email content.  This is an unusual situation which results in
    //  user confusion as it isn't clear what is wrong.
    //
    //  If the scenario is detected, a warning will be displayed on the page.
    
    //  Account for the other form fields of which there are about 10 including hidden fields ...
    if (count($users) > (ini_get('max_input_vars') - 10))
    {
        printf('<div style="border-left: 4px solid #ffba00;" class="error nag"><p>%s</p></div>', sprintf(__('Warning:  The number of users (%d) plus overhead exceeds the PHP <a href="http://php.net/manual/en/info.configuration.php#ini.max-input-vars">max_input_vars</a> setting (%d).  You will not be able to send email to more than %d users in one batch.  This can be changed by increasing the value of <a href="http://php.net/manual/en/info.configuration.php#ini.max-input-vars">max_input_vars</a> setting in the PHP.ini configuration file.', MAILUSERS_I18N_DOMAIN), count($users), ini_get('max_input_vars'), ini_get('max_input_vars') - 10)) ;
    }
?>
