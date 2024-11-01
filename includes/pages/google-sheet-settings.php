<?php 
   $cf7 = (is_plugin_active('contact-form-7/wp-contact-form-7.php')) ? 1 :  0;
   $wpform = ((is_plugin_active('wpforms/wpforms.php')) || (is_plugin_active('wpforms-lite/wpforms.php'))) ? 1 :  0;
   $gravity = (is_plugin_active('gravityforms/gravityforms.php')) ? 1 :  0;
   $ninja = (is_plugin_active('ninja-forms/ninja-forms.php')) ? 1 :  0;
?>
<div class="section-main">
    <div class="section-banner">
        <div class="banner-left-right">
            <div class="banner-left">
                <h3>WORDPRESS TO GOOGLE CONTACTS</h3>
            </div>
            <div class="banner-right">
                <div class="banner-btn">
                    <button class="banner-btn-1">
                        <?php echo __( "Documentation", 'WPGContacts'); ?>
                    </button>
                    <button class="banner-btn-2">
                        <?php echo __( "Support", 'WPGContacts'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="loader-google-contact"></div>
    <div class="section-content">
        <div class="section-sidebar">
            <div class="section-box">
                <div class="sec-tab-link google-integration active" onclick="tab_google_contact('google-integration')">
                    <!-- <div class="sec-tab-icon">
                        <span class="dashicons dashicons-admin-generic"></span>
                    </div> -->
                    <div class="sec-tab-title">
                        <span class="dashicons dashicons-admin-generic"></span><span
                            class="title"><?php echo __( "Google API Settings", 'WPGContacts'); ?>
                            </sapn>
                    </div>
                </div>

                <div class="sec-tab-link cf7-settings <?php echo ($cf7 == 0) ? 'wptogoo_disable' : '' ?>"
                    onclick="tab_google_contact('cf7-settings')">
                    <!-- <div class="sec-tab-icon">
                        <span class="dashicons dashicons-format-aside"></span>
                    </div> -->
                    <div class="sec-tab-title">
                        <span class="dashicons dashicons-format-aside"></span><span
                            class="title"><?php echo __( "Contact Form 7", 'WPGContacts'); ?></span>
                    </div>
                </div>

                <div class="sec-tab-link wpforms-settings <?php echo ($wpform == 0) ? 'wptogoo_disable' : '' ?>"
                    onclick="tab_google_contact('wpforms-settings')">
                    <!-- <div class="sec-tab-icon">
                        <span class="dashicons dashicons-forms"></span>
                    </div> -->
                    <div class="sec-tab-title">
                        <span class="dashicons dashicons-forms"></span><span
                            class="title"><?php echo __( "WPForms", 'WPGContacts'); ?> </span>
                    </div>
                </div>

                <div class="sec-tab-link gravityforms-settings <?php echo ($gravity == 0) ? 'wptogoo_disable' : '' ?>"
                    onclick="tab_google_contact('gravityforms-settings')">
                    <!-- <div class="sec-tab-icon">
                        <span class="dashicons dashicons-welcome-widgets-menus"></span>
                    </div> -->
                    <div class="sec-tab-title">
                        <span class="dashicons dashicons-welcome-widgets-menus"></span><span
                            class="title"><?php echo __( "Gravity Forms", 'WPGContacts'); ?></span>
                    </div>
                </div>

                <div class="sec-tab-link ninjaforms-settings <?php echo ($ninja == 0) ? 'wptogoo_disable' : '' ?>"
                    onclick="tab_google_contact('ninjaforms-settings')">
                    <!-- <div class="sec-tab-icon">
                        <span class="dashicons dashicons-media-text"></span>
                    </div> -->
                    <div class="sec-tab-title">
                        <span class="dashicons dashicons-media-text"></span><span
                            class="title"><?php echo __( "Ninja Forms", 'WPGContacts'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-main-body">
            <!-- Google API Setiing -->
            <div class="tab-all-setting all-google-integration" style="display:none">
                <?php include_once('views/wpgooglecontact-integration.php') ?>
            </div>

            <!-- CF7 Setiing -->
            <div class="tab-all-setting all-cf7-settings" style="display:none">
                <?php 
                if($cf7 == 0){
                    echo __( "CF7 Plugins not installed in your site", 'WPGContacts');
                }else{
                    include_once('views/wpgooglecontact-cf7.php');
                }
                
                ?>
            </div>

            <!-- WPForms Setiing -->
            <div class="tab-all-setting all-wpforms-settings" style="display:none">
                <?php include_once('views/wpgooglecontact-wpform.php') ?>
            </div>

            <!-- Gravity Forms Setiing -->
            <div class="tab-all-setting all-gravityforms-settings" style="display:none">
                <?php include_once('views/wpgooglecontact-gravity.php') ?>
            </div>

            <!-- NinjaForms Setiing -->
            <div class="tab-all-setting all-ninjaforms-settings" style="display:none">
                <?php include_once('views/wpgooglecontact-ninjaform.php') ?>
            </div>

        </div>
    </div>
</div>