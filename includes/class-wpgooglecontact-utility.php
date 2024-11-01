<?php

/*
 * Utilities class for wptogocont forms google sheet connector
 * @since       1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
   exit;
}

/**
 * Utilities class - singleton class
 * @since 1.0
 */
class wpgooglecontact_Utility_free {

   private function __construct() {
      // Do Nothing
   }

   /**
    * Get the singleton instance of the wpgooglecontact_Utility_free class
    *
    * @return singleton instance of wpgooglecontact_Utility_free
    */
   public static function instance() {

      static $instance = NULL;
      if (is_null($instance)) {
         $instance = new wpgooglecontact_Utility_free();
      }
      return $instance;
   }

   /**
    * Prints message (string or array) in the debug.log file
    *
    * @param mixed $message
    */
   public function logger($message) {
      if (WP_DEBUG === true) {
         if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
         } else {
            error_log($message);
         }
      }
   }

   /**
    * Display error or success message in the admin section
    *
    * @param array $data containing type and message
    * @return string with html containing the error message
    * 
    * @since 1.0 initial version
    */
   public function admin_notice($data = array()) {
      // extract message and type from the $data array
      $message = isset($data['message']) ? $data['message'] : "";
      $message_type = isset($data['type']) ? $data['type'] : "";
      switch ($message_type) {
         case 'error':
            $admin_notice = '<div id="message" class="error notice is-dismissible">';
            break;
         case 'update':
            $admin_notice = '<div id="message" class="updated notice is-dismissible">';
            break;
         case 'update-nag':
            $admin_notice = '<div id="message" class="update-nag">';
            break;
          case 'renew' :
            $admin_notice = '<div id="message" class="error notice gsgf-renew is-dismissible">';
            break;
         default:
            $message = __('There\'s something wrong with your code...', 'WPGContacts');
            $admin_notice = "<div id=\"message\" class=\"error\">\n";
            break;
      }

      $admin_notice .= "    <p>" . __($message, 'WPGContacts') . "</p>\n";
      $admin_notice .= "</div>\n";
      return $admin_notice;
   }

   /**
    * Utility function to get the current user's role
    *
    * @since 1.0
    */
   public function get_current_user_role() {
      global $wp_roles;
      foreach ( $wp_roles->role_names as $role => $name ) :
         if ( current_user_can( $role ) ) {
            return $role;
         }
      endforeach;
   }

   public function get_system_roles() {
      $participating_roles = array();
      $editable_roles = get_editable_roles();
      foreach ($editable_roles as $role => $details) {
         $participating_roles[$role] = $details['name'];
      }
      return $participating_roles;
   }

   public static function wptogocontdebug_log($error) {
      try {
         if (!is_dir(WP_GOOGLE_CONTACT_FREE_PATH . 'logs')) {
            mkdir(WP_GOOGLE_CONTACT_FREE_PATH . 'logs', 0755, true);
         }
      } catch (Exception $e) {
         
      }
      try {
         $log = fopen(WP_GOOGLE_CONTACT_FREE_PATH . "logs/log.txt", 'a');
         if (is_array($error)) {
            fwrite($log, print_r(date_i18n('j F Y H:i:s', current_time('timestamp')) . " \t PHP " . phpversion(), TRUE));
            fwrite($log, print_r($error, TRUE));
         } else {
            $result = fwrite($log, print_r(date_i18n('j F Y H:i:s', current_time('timestamp')) . " \t PHP " . phpversion() . " \t $error \r\n", TRUE));
         }
         fclose($log);
      } catch (Exception $e) {
         
      }
   }

}