<?php
/**
* Service class for Google Sheet Connector
* @since 1.0
*/
if (!defined('ABSPATH')) {
exit; // Exit if accessed directly
}

/**
* wpgooglecontact_Connector_Service_Free Class
*
* @since 1.0
*/
class wpgooglecontact_Connector_Service_Free {

 /**
 *  Set things up.
 *  @since 1.0
 */
public function __construct() {
    //============ Ajax ==============
    add_action('wp_ajax_verify_wpgooglecontact_integation', array($this, 'verify_wpgooglecontact_integation'));
    add_action('wp_ajax_deactivate_wpgooglecontact_integation', array($this, 'deactivate_wpgooglecontact_integation'));
    add_action('wp_ajax_wpgooglecontact_clear_log', array($this, 'wpgoco_clear_logs'));
    add_action('admin_notices', array($this, 'display_upgrade_notice'));
    add_action('wp_ajax_set_upgrade_notification_interval', array($this, 'set_upgrade_notification_interval'));
    add_action('wp_ajax_close_upgrade_notification_interval', array($this, 'close_upgrade_notification_interval'));
    add_action('wp_ajax_fgc_save_client_id_sec_id_gapi', array($this, 'fgc_save_client_id_sec_id_gapi'));
    add_action('wp_ajax_fgc_deactivate_auth_token_gapi', array($this, 'fgc_deactivate_wptogocont_integation_manual'));
    add_action('wp_ajax_save_method_api_wptogocont', array($this, 'save_method_api_wptogocont'));
    add_action( 'wp_ajax_get_cf7_form_fields_list', array($this, 'get_cf7_form_fields_list'));
    add_action( 'wp_ajax_save_googleform_fields', array($this, 'save_googleform_fields'));
    add_action('wpcf7_before_send_mail', array($this, 'save_wp_to_google_contacts'));

}

public function save_wp_to_google_contacts($form_tag) {
    $submission = WPCF7_Submission::get_instance();
    $posted_data = $submission->get_posted_data();
    $form_id = $form_tag->id();
    $data = array();
    $googleFields = array();
    $wpgoco_cf7_field_map = get_option( 'wpgoco_cf7_field_map' );
    if(!empty($posted_data) && !empty($wpgoco_cf7_field_map)){
        if(isset($wpgoco_cf7_field_map[$form_id])){
            $googleFields = json_decode($wpgoco_cf7_field_map[$form_id]);

            foreach($googleFields as $key => $value){
                if(array_key_exists($key,$posted_data)){
                    $data[$value] = $posted_data[$key];
                }
            }
            if(!empty($data)){
                include_once( WP_GOOGLE_CONTACT_FREE_ROOT . "/lib/google-contacts.php" );
                $google_sheet = new WP_GOOGLE_CONTACT();
                $google_sheet->get_all_google_contact($data);
            }

        }
    }
}

public function save_googleform_fields(){
    try {
       check_ajax_referer('wptogocont-ajax-nonce', 'security');
       $formData = sanitize_text_field(json_encode($_POST["formData"]));
       if($formData == 'null'){
        wp_send_json_success('Fields are not selected'); 
       }else{
        $formId = sanitize_text_field(($_POST["formId"]));
        $formOption[$formId] = $formData;
        $wpgoco_cf7_field_map = get_option( 'wpgoco_cf7_field_map' );

            if($wpgoco_cf7_field_map == "")
            update_option( 'wpgoco_cf7_field_map', $formOption );
            else{
                if (array_key_exists($formId,$wpgoco_cf7_field_map)){
                    $wpgoco_cf7_field_map[$formId] = $formData;
                    update_option( 'wpgoco_cf7_field_map', $wpgoco_cf7_field_map );
                }else{
                    $add_new_option = $wpgoco_cf7_field_map + $formOption;
                    update_option( 'wpgoco_cf7_field_map', $add_new_option );
                }
            }
        }
        wp_send_json_success();
    
    } catch (Exception $e) {
        wp_send_json_error();
    }
    
}


public function get_cf7_form_fields_list(){
    check_ajax_referer('wptogocont-ajax-nonce', 'security');
    $field_arr = array('First Name'=>'givenName', 'Last Name'=>'familyName', 'Email Address'=>'emailAddresses', 'Phone Number'=>'phoneNumbers');
    $form_fields = array();
    
    $form_id = sanitize_text_field($_POST["form_id"]);
    $ContactForm = WPCF7_ContactForm::get_instance($form_id);
    $form_fields = $ContactForm->scan_form_tags();
    $cf7form_html = "<form id='google_contact_form_field_list'><input type='hidden' name='cf7-form-id' id='cf7-form-id' value='".$form_id."'/><table>";
    $wpgoco_cf7_field_map = get_option( 'wpgoco_cf7_field_map' );
    
    
    

    if(!empty($form_fields)){
        $cf7form_html.='<div class="show_form_back"><span class="dashicons dashicons-controls-back"></span></div>';
        $savedForm =  (array) json_decode($wpgoco_cf7_field_map[$form_id]);
        foreach ($form_fields as $key => $value) {
            $option_html = "<option value=''>---select---</option>";
            foreach ($field_arr as $fieldlabel => $fieldVal) {
                if(!isset($wpgoco_cf7_field_map[$form_id]))
                    $option_html.='<option value='.$fieldVal.'>'.$fieldlabel.'</option>';
                else{
                    if(array_key_exists($value->raw_name, $savedForm)){
                        if($savedForm[$value->raw_name] == $fieldVal){
                            $option_html.='<option value='.$fieldVal.' selected>'.$fieldlabel.'</option>';
                            $select[$value->raw_name] = $fieldVal;
                        }
                        else
                            $option_html.='<option value='.$fieldVal.' disabled="disabled">'.$fieldlabel.'</option>';
                    }
                    else
                        $option_html.='<option value='.$fieldVal.' disabled="disabled">'.$fieldlabel.'</option>';
                }
            }
            if($value->raw_name!="")
                $cf7form_html.= "<tr><td>".$value->raw_name."</td><td><select class='google-contact-drop' onchange='getFormfieldVal(this)' name='".$value->raw_name."' data-id='".(isset($select[$value->raw_name]) ? $select[$value->raw_name] : '')  ."'>".$option_html."</select></td></tr>";
        }
        $cf7form_html.="</table>";
        $cf7form_html.='<div class="cf7_form_save_btn"><button class="show_form_save save_googleform_fields">SUBMIT</button></div></form>';
    }
    wp_send_json_success($cf7form_html);
}
 /**
 * AJAX function - verifies the token
 * @since 1.0
 */
public function verify_wpgooglecontact_integation() {
    check_ajax_referer('wptogocont-ajax-nonce', 'security');
    /* sanitize incoming data */
    $Code = sanitize_text_field($_POST["code"]);
    if (!empty($Code)) {
        update_option('wpgoco_access_code', $Code);
    } else {
        return;
    }
    if (get_option('wpgoco_access_code') != '') {
        include_once(WP_GOOGLE_CONTACT_FREE_ROOT . '/lib/google-contacts.php');
        WP_GOOGLE_CONTACT::preauth(get_option('wpgoco_access_code'));
        update_option('wpgoco_verify', 'valid');
        update_option('wpgoco_manual_setting', '0');
        wp_send_json_success();
    } else {
        update_option('wpgoco_verify', 'invalid');
        wp_send_json_error();
    }
}


 /**
 * AJAX function - verifies the token
 * @since 1.0
 */
public function verify_wpgooglecontact_integation_new($Code="") {
    if (!empty($Code)) {
        update_option('wpgoco_access_code', $Code);
    } else {
        return;
    }
    if (get_option('wpgoco_access_code') != '') {
        include_once(WP_GOOGLE_CONTACT_FREE_ROOT . '/lib/google-contacts.php');
        WP_GOOGLE_CONTACT::preauth(get_option('wpgoco_access_code'));
        update_option('wpgoco_verify', 'valid');
        update_option('wpgoco_manual_setting', '0');
    } else {
        update_option('wpgoco_verify', 'invalid');
    }
}

/**
 * AJAX function - verifies the token
 * @since 1.0
 */
public function verify_wpgooglecontact_integation_new_manual($Code="") {
    if (!empty($Code)) {
        update_option('wpgoco_access_manual_code', $Code);
    }
    if (get_option('wpgoco_access_manual_code') != '') {
        include_once(WP_GOOGLE_CONTACT_FREE_ROOT . '/lib/google-contacts.php');
        $manual_access_code = get_option('wpgoco_access_manual_code');
        $client_id = get_option('wpgoco_client_id');
        $secret_id = get_option('wpgoco_secret_id');

        WP_GOOGLE_CONTACT::preauth_manual($manual_access_code, $client_id,$secret_id, esc_html(admin_url('admin.php?page=wptogocont-db-google-sheet-config')));
        update_option('wpgoco_verify', 'valid');
        update_option('wpgoco_manual_setting', '1');
        
    } else {
        update_option('wpgoco_verify', 'invalid');
    }
}


/**
 * AJAX function - deactivate activation
 * @since 1.4
 */
public function deactivate_wpgooglecontact_integation() {
    // nonce check
    check_ajax_referer('wptogocont-ajax-nonce', 'security');

    if (get_option('wpgoco_token') !== '') {
        delete_option('wpgoco_sheetId');
        delete_option('wpgoco_sheetTabs');
        delete_option('wpgoco_token');
        delete_option('wpgoco_access_code');
        delete_option('wpgoco_verify');
        delete_option('wpgoco_email_account');
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}


 /**
 * AJAX function - clear log file
 * @since 1.0
 */
public function wpgoco_clear_logs() {
    // nonce check
    check_ajax_referer('wptogocont-ajax-nonce', 'security');
    $handle = fopen(WP_GOOGLE_CONTACT_FREE_PATH . 'logs/log.txt', 'w');
    fclose($handle);
    wp_send_json_success();
}


public function display_upgrade_notice() {
        $get_notification_display_interval = get_option('wpgoco_pro_upgrade_notice_interval');
        $close_notification_interval = get_option('wpgoco_pro_close_upgrade_notice');

        $plugin_options = get_site_option('google_sheet_info');

        if ($plugin_options['version'] !== "1.5") {
            return;
        }

        if ($close_notification_interval === "off") {
            return;
        }

        if (!empty($get_notification_display_interval)) {
            $adds_interval_date_object = DateTime::createFromFormat("Y-m-d", $get_notification_display_interval);
            $notice_interval_timestamp = $adds_interval_date_object->getTimestamp();
        }

        if (empty($get_notification_display_interval) || current_time('timestamp') > $notice_interval_timestamp) {
            $ajax_nonce = wp_create_nonce("gs_upgrade_ajax_nonce");
            $upgrade_text = '<div class="gs-adds-notice">';
            $upgrade_text .= '<span><b>wptogocont Forms Google Sheet Connector PRO </b> ';
            $upgrade_text .= 'version 1.5 would required you to <a href="' . admin_url("admin.php?page=wpwptogocont-google-sheet-config") . '">reauthenticate</a> with your Google Account and fetch Sheet Details again due to update of Google API V3 to V4.<br/><br/>';
            $upgrade_text .= 'To avoid any loss of data for <b>manual setup</b> redo the Google Sheet settings of each Contact Forms again with required sheet and tab details. It would required you to add Sheet and Tab Id.</span>';
            $upgrade_text .= '<ul class="review-rating-list">';
            $upgrade_text .= '<li><a href="javascript:void(0);" class="wptogocontgsc_upgrade" title="Done">Yes, I have done.</a></li>';
            $upgrade_text .= '<li><a href="javascript:void(0);" class="wptogocontgsc_upgrade_later" title="Remind me later">Remind me later.</a></li>';
            $upgrade_text .= '</ul>';
            $upgrade_text .= '<input type="hidden" name="gs_upgrade_ajax_nonce" id="gs_upgrade_ajax_nonce" value="' . $ajax_nonce . '" />';
            $upgrade_text .= '</div>';

            $upgrade_block = Gs_Connector_Utility::instance()->admin_notice(array(
                'type' => 'upgrade',
                'message' => $upgrade_text
                    ));
            
            echo esc_html($upgrade_block);

        }
    }

       public function set_upgrade_notification_interval() {
        // check nonce
        check_ajax_referer('wpgoco_upgrade_ajax_nonce', 'security');
        $time_interval = date('Y-m-d', strtotime('+10 day'));
        update_option('wpgoco_pro_upgrade_notice_interval', $time_interval);
        wp_send_json_success();
    }

    public function close_upgrade_notification_interval() {
        // check nonce
        check_ajax_referer('wpgoco_upgrade_ajax_nonce', 'security');
        update_option('wpgoco_pro_close_upgrade_notice', 'off');
        wp_send_json_success();
    }



/**
 * AJAX function - Save Client Id and Secret Id
 *
 * @since 1.0
 */
public function fgc_save_client_id_sec_id_gapi()
{
    // nonce checksave_wptogocont_settings
    check_ajax_referer('wptogocont-ajax-nonce', 'security');
    /* sanitize incoming data */
    $client_id = sanitize_text_field($_POST["client_id"]);
    $secret_id = sanitize_text_field($_POST["secret_id"]);
    //save google setting with manual client id and secret id
    if ((!empty($client_id)) && (!empty($secret_id))) {
        update_option('wpgoco_client_id', $client_id);
        update_option('wpgoco_secret_id', $secret_id);
        $Code = "";
        if (isset($_POST["wptogocont_client_token"]))
            $Code = sanitize_text_field($_POST["wptogocont_client_token"]);
        if (!empty($Code)) {
            update_option('wpgoco_access_manual_code', $Code);
        } else {
            wp_send_json_success();
            return;
        }
        if (get_option('wpgoco_access_manual_code') != '') {
            include_once(WP_GOOGLE_CONTACT_FREE_ROOT . '/lib/google-contacts.php');
            $manual_access_code = get_option('wpgoco_access_manual_code');
            $client_id = get_option('wpgoco_client_id');
            $secret_id = get_option('wpgoco_secret_id');

            WP_GOOGLE_CONTACT::preauth_manual($manual_access_code, $client_id,$secret_id, esc_html(admin_url('admin.php?page=wordpress-google-contacts-config')));
                update_option('wpgoco_verify', 'valid');
                update_option('wpgoco_manual_setting', '1');
            //deactivate auto setting
            //delete_option('wpgoco_token');
            //delete_option('wpgoco_access_code');
            //deactivate auto setting
            wp_send_json_success();
        } else {
            update_option('wpgoco_verify', 'invalid');
            wp_send_json_error();
        }
    } else {
        update_option('wpgoco_client_id', '');
        update_option('wpgoco_secret_id', '');
        wp_send_json_success();
        return;
    }
}


/**
 * AJAX function - deactivate activation - Manual
 * @since 1.2
 */
public function fgc_deactivate_wptogocont_integation_manual()
{
    // nonce check
    check_ajax_referer('wptogocont-ajax-nonce', 'security');
    //$wptogocont_manual_setting = get_option('wpgoco_manual_setting');
    //if(isset($wptogocont_manual_setting) || $wptogocont_manual_setting=="1"){
    //if (get_option('wpgoco_token_manual') !== '') {
        delete_option('wpgoco_token_manual');
        delete_option('wpgoco_verify');
        delete_option('wpgoco_access_manual_code');
        delete_option('wpgoco_email_account_manual');
        update_option('wpgoco_manual_setting', '1');
        wp_send_json_success();
    //} else {
        //wp_send_json_error();
    //}
    //}
}

/*Save integration method using Ajax*/
 public function save_method_api_wptogocont(){

    try {
        $msg = array();
        // nonce check
        check_ajax_referer('wptogocont-ajax-nonce', 'security');
        
        if($_POST['method_api_wptogocont'] == "wptogocont_manual")
            update_option('wpgoco_manual_setting', '1');
        else
            update_option('wpgoco_manual_setting', '0');

        wp_send_json_success();
        
    } catch (Exception $e) {
        $msg['ERROR_MSG'] = $e->getMessage();
        $msg['TRACE_STK'] = $e->getTraceAsString();
        Gs_Connector_Utility::gs_debug_log($msg);
        wp_send_json_error();
    }

}

    public function getAllContact(){
        
        include_once(WP_GOOGLE_CONTACT_FREE_ROOT . '/lib/google-contacts.php');
        $contacts = WP_GOOGLE_CONTACT::getAllContact();
        
    }
}

$wpgooglecontact_Connector_Service_Free = new wpgooglecontact_Connector_Service_Free();