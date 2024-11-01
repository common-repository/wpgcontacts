<?php

/**
 * Plugin Name: WPGContacts
 * Plugin URI: https://www.gsheetconnector.com/
 * Description: Create Google Contacts From Wordpress plugins(Contact Form, WPForm, Gravity Forms, Ninja Forms).
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Author: GSheetConnector
 * Author URI: https://www.gsheetconnector.com/
 * Version: 1.0.0
 * Text Domain: WPGContacts
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
   exit;
}

define('WP_GOOGLE_CONTACT_FREE_VERSION', '1.0.0');
define('WP_GOOGLE_CONTACT_FREE_DB_VERSION', '1.0.0');
define('WP_GOOGLE_CONTACT_FREE_ROOT', dirname(__FILE__));
define('WP_GOOGLE_CONTACT_FREE_URL', plugins_url('/', __FILE__));
define('WP_GOOGLE_CONTACT_FREE_BASE_FILE', basename(dirname(__FILE__)) . '/WPGContacts.php');
define('WP_GOOGLE_CONTACT_FREE_BASE_NAME', plugin_basename(__FILE__));
define('WP_GOOGLE_CONTACT_FREE_PATH', plugin_dir_path(__FILE__)); //use for include files to other files
define('WP_GOOGLE_CONTACT_FREE_CURRENT_THEME', get_stylesheet_directory());
define('WP_GOOGLE_CONTACT_FREE_SHORT_NAME', 'WPG Connector');
define('WP_GOOGLE_TXTDOMAIN_FREE', 'WPGContacts');
define('WP_GOOGLE_CONTACT_FREE_STORE_URL', 'https://gsheetconnector.com');

load_plugin_textdomain('WPGContacts', false, basename(dirname(__FILE__)) . '/languages');
if (!class_exists('wpgooglecontact_Utility_free')) {
   include(WP_GOOGLE_CONTACT_FREE_ROOT . '/includes/class-wpgooglecontact-utility.php');
}
if (!class_exists('wpgooglecontact_Connector_Service_Free')) {
   include(WP_GOOGLE_CONTACT_FREE_ROOT . '/includes/class-wpgooglecontact-service.php');
}

class wp_google_contacts
{

   public function __construct()
   {
      //run on activation of plugin
      register_activation_hook(__FILE__, array($this, 'wp_to_google_contacts_activate'));

      //run on deactivation of plugin
      register_deactivation_hook(__FILE__, array($this, 'wp_to_google_contacts_deactivate'));

      //run on uninstall
      register_uninstall_hook(__FILE__, array('wp_google_contacts', 'wp_to_google_contacts_uninstall'));

      // validate is wpgooglecontact plugin exist
      add_action('admin_init', array($this, 'wptogocontvalidate_parent_plugin_exists'));

      add_action('admin_menu', array($this, 'wptogocontregister_gs_menu_pages'), 70);

      // load the js and css files
      add_action('init', array($this, 'wptogocontload_css_and_js_files'));
   }


   /**
    * Create/Register menu items for the plugin.
    * @since 1.0
    */
   public function wptogocontregister_gs_menu_pages()
   {
      $current_role = wpgooglecontact_Utility_free::instance()->get_current_user_role();
      add_menu_page( __('WordPress Google Contacts', 'WPGContacts'), __('WordPress Google Contacts', 'WPGContacts'), $current_role, 'wordpress-google-contacts-config', array($this, 'wptogocontgoogle_sheet_config'));
   }

   /**
    * Google Sheets page action.
    * This method is called when the menu item "Google Sheets" is clicked.
    * @since 1.0
    */
   public function wptogocontgoogle_sheet_config()
   {
      include(WP_GOOGLE_CONTACT_FREE_PATH . "includes/pages/google-sheet-settings.php");
   }


   /**
    * Do things on plugin activation
    * @since 1.0
    */
   public function wp_to_google_contacts_activate($network_wide)
   {
      global $wpdb;
      $this->run_on_activation();
      if (function_exists('is_multisite') && is_multisite()) {
         // check if it is a network activation - if so, run the activation function for each blog id
         if ($network_wide) {
            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->base_prefix}blogs");
            foreach ($blogids as $blog_id) {
               switch_to_blog($blog_id);
               $this->run_for_site();
               restore_current_blog();
            }
            return;
         }
      }

      // for non-network sites only
      $this->run_for_site();
   }

   /**
    * Do things on plugin activation
    * @since 1.0
    */
   public function wp_to_google_contacts_deactivate($network_wide)
   {
   }

   /**
    *  Runs on plugin uninstall.
    *  a static class method or function can be used in an uninstall hook
    *
    *  @since 1.0
    */
   public static function wp_to_google_contacts_uninstall()
   {
      global $wpdb;
      wp_google_contacts::run_on_uninstall();
      if (function_exists('is_multisite') && is_multisite()) {
         //Get all blog ids; foreach of them call the uninstall procedure
         $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->base_prefix}blogs");

         //Get all blog ids; foreach them and call the install procedure on each of them if the plugin table is found
         foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            wp_google_contacts::delete_for_site();
            restore_current_blog();
         }
         return;
      }
      wp_google_contacts::delete_for_site();
   }

   /**
    * Called on uninstall - deletes site_options
    *
    * @since 1.0
    */
   private static function run_on_uninstall()
   {
      if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN'))
         exit();

      delete_site_option('wpgoco_info');
   }

   /**
    * Called on uninstall - deletes site specific options
    *
    * @since 1.5
    */
   private static function delete_for_site()
   {
      if (!is_plugin_active('gsheetconnector-wpgooglecontact/gsheetconnector-wpgooglecontact.php') || (!file_exists(plugin_dir_path(__DIR__) . 'gsheetconnector-wpgooglecontact/gsheetconnector-wpgooglecontact.php'))) {

         // deactivate the license
         $license = trim(get_option('wpgoco_license_key'));
         // data to send in our API request
         $api_params = array(
            'edd_action' => 'deactivate_license',
            'license' => $license,
            'item_name' => urlencode(WP_GOOGLE_CONTACT_FREE_PRODUCT_NAME), // the name of our product in EDD
            'url' => home_url()
         );
         // Call the custom API.
         $response = wp_remote_post(WP_GOOGLE_CONTACT_FREE_STORE_URL, array('timeout' => 15, 'body' => $api_params));


         delete_option('wpgoco_access_code');
         delete_option('wpgoco_verify');
         delete_option('wpgoco_token');
         delete_post_meta_by_key('wpgoco_fields');
         delete_post_meta_by_key('wpgoco_settings');
      }
   }


   /**
    * Validate parent Plugin wpgooglecontact exist and activated
    * @access public
    * @since 1.0
    */
   public function wptogocontvalidate_parent_plugin_exists()
   {
      //========= Theme Check
      $theme = wp_get_theme();
      
      $theme_name = $theme->name;
      $parent_theme = $theme->parent_theme;
      
   }

   /**
    * Create/Register menu items for the plugin.
    * @since 1.0
    */

   public function wptogocontload_css_and_js_files()
   {
      add_action('admin_print_styles', array($this, 'add_css_files'));
      add_action('admin_print_scripts', array($this, 'add_js_files'));
   }

   /**
    * enqueue CSS files
    * @since 1.0
    */
   public function add_css_files()
   {
      if (is_admin() && (isset($_GET['page']) && (($_GET['page'] == 'wordpress-google-contacts-config') || ($_GET['page'] == 'wordpress-google-contacts-config')))) {
         wp_enqueue_style('wpgooglecontact-connector-css', WP_GOOGLE_CONTACT_FREE_URL . 'assets/css/wpgooglecontact-style.css', WP_GOOGLE_CONTACT_FREE_VERSION, true);
      }
   }

   public function add_js_files()
   {
      if (is_admin() && (isset($_GET['page']) && (($_GET['page'] == 'wordpress-google-contacts-config') || ($_GET['page'] == 'wordpress-google-contacts-config')))) {
         wp_enqueue_script('wpgooglecontact-connector-js', WP_GOOGLE_CONTACT_FREE_URL . 'assets/js/wpgooglecontact-connector.js', WP_GOOGLE_CONTACT_FREE_VERSION, true);
      }
   }


   /**
    * Add custom link for the plugin beside activate/deactivate links
    * @param array $links Array of links to display below our plugin listing.
    * @return array Amended array of links.    * 
    * @since 1.5
    */
   public function wptogocontconnector_pro_plugin_action_links($links)
   {

      // We shouldn't encourage editing our plugin directly.
      unset($links['edit']);

      // Add our custom links to the returned array value.
      return array_merge(array(
         '<a href="' . admin_url('admin.php?page=wordpress-google-contacts-config&tab=integration') . '">' . __('Settings', 'WPGContacts') . '</a>'
      ), $links);
   }

   public function add_googlesheet_menu()
   {
      require_once plugin_dir_path(__FILE__) . 'includes/pages/google-sheet-settings.php';
   }


   /*****************************/

   /**
    * Called on activation.
    * Creates the site_options (required for all the sites in a multi-site setup)
    * If the current version doesn't match the new version, runs the upgrade
    * @since 1.0
    */
   private function run_on_activation()
   {
      $plugin_options = get_site_option('wpgoco_info');
      if (false === $plugin_options) {
         $google_sheet_info = array(
            'version' => WP_GOOGLE_CONTACT_FREE_VERSION,
            'db_version' => WP_GOOGLE_CONTACT_FREE_DB_VERSION
         );
         update_site_option('google_sheet_info', $google_sheet_info);
      } else if (WP_GOOGLE_CONTACT_FREE_DB_VERSION != $plugin_options['version']) {
         $this->run_on_upgrade();
      }
   }

   /**
    * called on upgrade. 
    * checks the current version and applies the necessary upgrades from that version onwards
    * @since 1.0
    */
   public function run_on_upgrade()
   {
      $plugin_options = get_site_option('wpgoco_info');

      // update the version value
      $google_sheet_info = array(
         'version' => WP_GOOGLE_CONTACT_FREE_ROOT,
         'db_version' => WP_GOOGLE_CONTACT_FREE_DB_VERSION
      );
      update_site_option('wpgoco_info', $google_sheet_info);
   }

   private function run_for_site()
   {
      if (!get_option('wpgoco_access_code')) {
         update_option('wpgoco_access_code', '');
      }
      if (!get_option('wpgoco_verify')) {
         update_option('wpgoco_verify', 'invalid');
      }
      if (!get_option('wpgoco_token')) {
         update_option('wpgoco_token', '');
      }
      if (!get_option('wpgoco_cf7_field_map')) {
         update_option('wpgoco_cf7_field_map', '');
      }
      if (!get_option('wpgoco_wpform_field_map')) {
         update_option('wpgoco_wpform_field_map', '');
      }
      if (!get_option('wpgoco_gravityform_field_map')) {
         update_option('wpgoco_gravityform_field_map', '');
      }
      if (!get_option('wpgoco_ninjaform_field_map')) {
         update_option('wpgoco_ninjaform_field_map', '');
      }
   }
}

$wp_google_contacts = new wp_google_contacts();

// Add custom link for our plugin
add_filter('plugin_action_links_' . WP_GOOGLE_CONTACT_FREE_BASE_NAME, array($wp_google_contacts, 'wptogocontconnector_pro_plugin_action_links'));