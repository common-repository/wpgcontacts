<?php
$wptogocont_client_id = get_option('wpgoco_client_id');
$wptogocont_secret_id = get_option('wpgoco_secret_id');
$wptogocont_code_db = get_option('wpgoco_access_code');
$wptogocont_manual_code_db = get_option('wpgoco_access_manual_code');
//$wptogocont_manual_setting = get_option('wpgoco_manual_setting');//Remove Auto method
$wptogocont_manual_setting = 1;
$header = admin_url('admin.php?page=wordpress-google-contacts-config');

?>
<div class="wptogoo_title">
    <img src="<?php echo WP_GOOGLE_CONTACT_FREE_URL; ?>assets/img/arrow.png">
    <h2 class="title">
        <?php echo esc_html(__('Google API Setting', 'WPGContacts')); ?>
    </h2>
    <input type="hidden" name="redirect_auth_wptogocont" id="redirect_auth_wptogocont"
        value="<?php echo (isset($header)) ? esc_attr($header) :''; ?>">
</div>

<div class="gApi-div">
    <div class="gIntegration-div">
        <div class="wrap gs-form">
            <div class="card-modified" id="googlesheet">
                <br class="clear">

                <!-- <div class="card-wp dropdownoption">
                    <div class="wpgdrop-left">
                        <label for="wptogocont_dro_option">Choose Google API Setting :</label>
                    </div>
                    <div class="wpgdrop-right">
                        <select id="wptogocont_dro_option" class="wptogocont_dro_option" name="wptogocont_dro_option">
                            <option value="wptogocont_existing" selected>Use Existing Client/Secret Key (Auto Google API
                                Configuration) </option>
                            <option value="wptogocont_manual">Use Manual Client/Secret Key (Use Your Google API
                                Configuration)
                            </option>
                        </select>
                        <p class="int-meth-btn-wptogocont"><input type="button" name="save-method-api-wptogocont"
                                id="save-method-api-wptogocont" value="<?php _e('Save', 'WPGContacts'); ?>"
                                class="button button-primary" />
                            <span class="loading-sign-method-api">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </p>
                    </div>

                </div> -->
                <input type="hidden" name="get_code" id="get_code"
                    value="<?php echo (isset($_GET['code']) && esc_attr($_GET['code']) != "") ? '1' : '0'; ?>">
                <input type="hidden" name="wptogocont_manual_setting" id="wptogocont_manual_setting"
                    value="<?php echo esc_attr($wptogocont_manual_setting); ?>">
                <input type="hidden" name="wptogocont-ajax-nonce" id="wptogocont-ajax-nonce"
                    value="<?php echo wp_create_nonce('wptogocont-ajax-nonce'); ?>" />

                <input type="hidden" name="wptogocont-redirect-url" id="wptogocont-redirect-url"
                    value="<?php echo admin_url('admin.php?page=wordpress-google-contacts-config'); ?>" />


                <?php //if($wptogocont_manual_setting == 0){ ?>
                <!-- <div class="card-wp fgc_api_existing_setting">
                    <div class="inside">
                        <hr>
                        <div class="wptogocont-integration-box">
                            <div class="wptogocont-btn-input">
                                <div class="wpgdrop-left-access">
                                    <label><span
                                            class="title1"><?php //echo esc_html(__('Google Access Code :', 'WPGContacts')); ?></span></label>
                                </div>

                                <div class="wpgdrop-right-access">
                                    <?php 
            //$token = get_option('wpgoco_token');
            //if ( ! empty( $token ) && $token !== "") { ?>
                                    <input type="text" name="wptogocont-code" id="wptogocont-code"
                                        class="wptogocont-code" value="" disabled
                                        placeholder="<?php //echo esc_html(__('Currently Active', 'WPGContacts')); ?>" />
                                    <input type="button" name="deactivate-log" id="deactivate-log"
                                        value="<?php //_e('Deactivate', 'WPGContacts'); ?>"
                                        class="button button-primary deactivat-wpgc" />
                                    <span class="tooltip"> <img
                                            src="<?php //echo WP_GOOGLE_CONTACT_FREE_URL; ?>assets/img/help.png"
                                            class="help-icon"> <span class="tooltiptext tooltip-right">On deactivation,
                                            all
                                            your
                                            data saved with authentication will be removed and you need to
                                            reauthenticate
                                            with your
                                            google account.</span></span>
                                    <span class="loading-sign-deactive">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <?php //} else {
            //$redirct_uri = admin_url( 'admin.php?page=wordpress-google-contacts-config' );
            ?>
                                    <input type="text" name="wptogocont-code" id="wptogocont-code"
                                        class="wptogocont-code"
                                        value="<?php //echo isset($_GET['code']) ? esc_attr($_GET['code']) : '' ?>"
                                        placeholder="<?php //echo esc_html(__('Enter Code', 'WPGContacts')); ?>" />
                                    <a href="https://oauth.gsheetconnector.com/formstogooglecontacts.php?client_admin_url=<?php //echo $redirct_uri;  ?>&plugin=formstogooglecontacts"
                                        class="button_wptogocontgsc"><img
                                            src="<?php //echo WP_GOOGLE_CONTACT_FREE_URL ?>/assets/img/btn_google_signin_dark_pressed_web.png"></a>

                                    <?php// } ?>

                                    <?php 
        //resolved - google sheet permission issues - START
    //if(!empty(get_option('wpgoco_verify')) && (get_option('wpgoco_verify') !="valid") && (get_option('wpgoco_verify') !="invalid")){
      ?>
                                    <p style="color:#c80d0d; font-size: 14px; border: 1px solid;padding: 8px;">
                                        <?php //echo get_option('wpgoco_verify'); ?></p>
                                    <p style="color:#c80d0d;border: 1px solid;padding: 8px;"><img width="350px"
                                            src="<?php //echo WP_GOOGLE_CONTACT_FREE_URL; ?>assets/img/permission_screen.png">
                                    </p>
                                    <?php
  //}
        //resolved - google sheet permission issues - END
  ?>


                                    <?php //if (empty(get_option('wpgoco_token'))) { ?>
                                    <p><input type="button" name="save-wptogocont-code" id="save-wptogocont-code"
                                            class="save-wptogocont-code" value="<?php //_e('Save', 'WPGContacts'); ?>"
                                            class="button button-primary" />
                                    </p>
                                    <?php //} ?>
                                    <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </div>
                            </div>

                            <?php 

// if ( ! empty( $token ) && $token !== "") {
// include_once( WP_GOOGLE_CONTACT_FREE_ROOT . "/lib/google-contacts.php" );
// $google_sheet = new WP_GOOGLE_CONTACT();      
// $email_account = $google_sheet->gsheet_print_google_account_email(); 

//$google_sheet->get_all_google_contact($data);
//if( $email_account ) { ?>
                            <hr>
                            <p class="connected-account">
                                <span class="right">
                                    <?php //printf( __( 'Connected Email Account:', 'WPGContacts' )); ?>
                                </span>
                                <span class="left">
                                    <?php //printf( __( '%s', 'WPGContacts' ), $email_account ); ?>
                                    <span>

                                        <p>
                                            <?php //}else{?>
                                        <p style="color:red">
                                            <?php //echo esc_html(__('Something wrong ! Your Auth Code may be wrong or expired. Please deactivate and do Re-Authentication again. ', 'WPGContacts')); ?>
                                        </p>
                                        <?php 
 //} 
//}?>

                                        <p class="debug-log">
                                            <span class="right">
                                                <label><?php //echo esc_html(__('Debug Log :', 'WPGContacts')); ?></label>
                                            </span>
                                            <span class="left">
                                                <label><a
                                                        href="<?php echo WP_GOOGLE_CONTACT_FREE_URL . 'logs/log.txt'; ?>"
                                                        target="_blank" class="debug-view">View</a></label>
                                                <label><a
                                                        class="debug-clear"><?php echo esc_html(__('Clear', 'WPGContacts')); ?></a></label><span
                                                    class="clear-loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                            </span>
                                        </p>
                                        <p id="wptogocont-validation-message"></p>
                                        <span id="deactivate-message"></span>


                        </div>

                    </div>
                </div>
            </div>
        </div> -->
                <!--integration end -->
                <?php //} ?>

                <!-- Manual Settings START -->
                <?php if($wptogocont_manual_setting == 1){ ?>
                <div class="card-wp fgc_api_manual_setting" style="display: none;">
                    <div class="wptogocont-in-fields inside">
                        <h2><span class="title1"><?php echo __(' Google - ', 'WPGContacts'); ?></span><span
                                class="title"><?php echo __('  API Settings', 'WPGContacts'); ?></span></h2>
                        <hr>
                        <p class="wptogocont-gs-alert-kk wptogocont-alert">
                            <?php echo __('Create new google APIs with Client ID and Client Secret keys to get an access for the google drive and google sheets. ', 'WPGContacts'); ?>
                        </p>
                        <p>
                        <div class="fgc_api_set">
                            <div class="fgc_api_option">
                                <div class="fgc_api_label">
                                    <label><?php echo __('Client Id', 'WPGContacts'); ?></label>
                                </div>
                                <div class="fgc_api_input">
                                    <input type="text" name="wptogocont-client-id" id="wptogocont-client-id"
                                        value="<?php echo esc_attr($wptogocont_client_id); ?>" placeholder="" /
                                        <?php echo (!empty(get_option('wpgoco_token_manual')) && get_option('wpgoco_token_manual') !== "") ? "disabled": "" ?>><br>
                                </div>
                            </div>
                            <div class="fgc_api_option">
                                <div class="fgc_api_label">
                                    <label><?php echo __('Client Secret', 'WPGContacts'); ?></label>
                                </div>
                                <div class="fgc_api_input">
                                    <input type="text" name="wptogocont-secret-id" id="wptogocont-secret-id"
                                        value="<?php echo esc_attr($wptogocont_secret_id); ?>" placeholder=""
                                        <?php echo (!empty(get_option('wpgoco_token_manual')) && get_option('wpgoco_token_manual') !== "") ? "disabled": "" ?> />
                                </div>
                            </div>


                            <?php 
                if (!empty(get_option('wpgoco_token_manual')) && get_option('wpgoco_token_manual') !== "") {
                include_once( WP_GOOGLE_CONTACT_FREE_ROOT . "/lib/google-contacts.php" );
                $google_sheet = new WP_GOOGLE_CONTACT();
                $email_account = $google_sheet->gsheet_print_google_account_email_manual(); 
                if( $email_account ) { ?>

                            <div class="wg_api_option-wptogocont fgc_api_option">
                                <div class="wg_api_label-wptogocont fgc_api_label">
                                    <label><?php echo __('Connected Email Account:', 'WPGContacts'); ?></label>
                                </div>
                                <div class="wg_api_input-wptogocont fgc_api_input">
                                    <p class="connected-account-manual-wptogocont">
                                        <?php printf( __( '%s', 'WPGContacts' ), $email_account ); ?>
                                    <p>
                                </div>
                            </div>
                            <?php }else{?>
                            <p style="color:red">
                                <?php echo esc_html(__('Something wrong ! Your Auth code may be wrong or expired Please Deactivate and Do Re-Auth Code ', 'WPGContacts')); ?>
                            </p>
                            <?php 
                  }
                }         
               ?>

                            <?php
            if (isset($_GET['code']))
                $wptogocont_code = sanitize_text_field($_GET['code']);
            else
                $wptogocont_code = "";
            ?>
                            <?php
            if ($wptogocont_client_id != "" || $wptogocont_secret_id != "") {
                if (!(empty($wptogocont_manual_code_db))) {
                    $auth_butt_display = "none";
                    $auth_input_display = "block";
                } elseif (!empty($wptogocont_code)) {
                    $auth_butt_display = "none";
                    $auth_input_display = "block";
                } else {
                    $auth_butt_display = "block";
                    $auth_input_display = "none";
                }
                ?>
                            <div class="fgc_api_option">
                                <div class="fgc_api_label">
                                    <label><?php echo __('Client Token', 'WPGContacts'); ?></label>
                                </div>
                                <div class="fgc_api_input">
                                    <?php if (get_option('wpgoco_token_manual') != '') { ?>
                                    <input type="text" value="" name="wptogocont-client-token"
                                        id="wptogocont-client-token"
                                        placeholder="<?php echo esc_html(__('Currently Active', 'WPGContacts')); ?>"
                                        disabled />
                                    <?php }else{ ?>
                                    <input type="text"
                                        value="<?php echo (!isset($wptogocont_code) || $wptogocont_code == "") && (isset($wptogocont_manual_code_db) || $wptogocont_manual_code_db != "") ? esc_attr($wptogocont_manual_code_db) : esc_attr($wptogocont_code) ?>"
                                        name="wptogocont-client-token" id="wptogocont-client-token" placeholder=""
                                        style="display: <?php echo esc_attr($auth_input_display); ?>" />
                                    <?php } ?>
                                    <?php
                        if (get_option('wpgoco_token_manual') !== '') {
                            include_once( WP_GOOGLE_CONTACT_FREE_ROOT . "/lib/google-contacts.php" );
                            $wptogocont_auth_url = WP_GOOGLE_CONTACT::getClient_auth(0, $wptogocont_client_id, $wptogocont_secret_id);
                            ?>
                                    <div class="fgc_api_option_auth_url"
                                        style="display: <?php echo esc_attr($auth_butt_display); ?>">
                                        <a href="<?php echo esc_url($wptogocont_auth_url); ?>" id="authlink_wptogocont"
                                            class="authlink_wptogocont" target="_blank">
                                            <div class="wptogocont-button-auth wptogocont-button-secondary">
                                                <?php echo esc_html__("Click here to generate an Authentication Token", 'WPGContacts'); ?>
                                            </div>
                                        </a>
                                    </div>
                                    <?php } ?>
                                    <input type="button" class="wptogocont-deactivate-auth"
                                        name="wptogocont-deactivate-auth" id="wptogocont-deactivate-auth"
                                        value="Deactivate"
                                        style="display:<?php echo ($wptogocont_manual_code_db != "") ? "block" : "none"; ?>">
                                </div>

                            </div>

                            <?php } ?>
                            <div class="fgc_api_option wpg-manual-design">
                                <input type="button" class="wptogocont-save" name="save-wptogocont-manual"
                                    id="save-wptogocont-manual" value="Save"
                                    <?php echo (!empty(get_option('wpgoco_token_manual')) && get_option('wpgoco_token_manual') !== "") ? "disabled": "" ?>>
                                <input type="reset" class="wptogocont-reset" name="save-wptogocont-reset"
                                    id="save-wptogocont-reset" value="Reset">
                            </div>
                            <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Manual Settings END -->
        <?php } ?>

        <!-- <div>
    <?php  
        // $array = array('Contact Form 7'=>'contact-form-7/wp-contact-form-7.php', 'WPForm' => 'wpforms/wpforms.php', 'Gravity Form'=>'gravityforms/gravityforms.php','Ninja Form'=>'ninja-forms/ninja-forms.php');
        
        // foreach($array as $key => $val){
            ?>
    <b><?php //echo esc_html( $key )  ?></b>
    <?php
            //if((is_plugin_active($val))){
            ?>
    <span class="dashicons dashicons-yes"></span>
    <?php
            //}else{
                ?>
    <span class="dashicons dashicons-no"></span>
    <?php
            //}
        //}
    ?>
</div> -->
    </div>

    <!-- <div class="gPlugins-div">
    <h2>Here Plugins List</h2>

</div> -->

</div>