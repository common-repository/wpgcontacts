<?php

if ( !defined( 'ABSPATH' ) )
   exit;

include_once ( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

use RapidWeb\GoogleOAuth2Handler\GoogleOAuth2Handler;
use RapidWeb\GooglePeopleAPI\GooglePeople;
use RapidWeb\GooglePeopleAPI\Contact;


class WP_GOOGLE_CONTACT {
   private $token;
   const clientId_web = '144430387520-tp3de5bo1n331c664ppq4ghleqr1ondq.apps.googleusercontent.com';
   const clientSecret_web = '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';
   private static $instance;
   public $google_contacts_fields;
   
   public function __construct() {

      $this->google_contacts_fields = [ 
         "names" => ["givenName","familyName"],
         "emailAddresses"=> ["emailAddresses"],
         "phoneNumbers" => ["phoneNumbers"],
      ];
 
     $this->google_contacts_fields = apply_filters( "wp_google_contacts_fields", $this->google_contacts_fields );
      
   }
   

   public static function setInstance( Google_Client $instance = null ) {
      self::$instance = $instance;
   }

   public static function getInstance() {
      if ( is_null( self::$instance ) ) {
         throw new LogicException( "Invalid Client" );
      }

      return self::$instance;
   }

   //constructed on call
   public static function preauth( $access_code ) {

     $clientId = WP_GOOGLE_CONTACT::clientId_web;
     $clientSecret = WP_GOOGLE_CONTACT::clientSecret_web;

      $client = new Google_Client();
      $client->setClientId($clientId);
      $client->setClientSecret($clientSecret);
      $client->setRedirectUri( 'https://oauth.gsheetconnector.com/formstogooglecontacts.php' );
	   $client->setScopes('https://www.google.com/m8/feeds');
      $client->addScope( 'https://www.googleapis.com/auth/userinfo.email' );
      $client->addScope( 'https://www.googleapis.com/auth/contacts.readonly' );
      $client->addScope( 'https://www.googleapis.com/auth/contacts' );
      $client->setAccessType( 'offline' );
      $client->fetchAccessTokenWithAuthCode( $access_code );
      $tokenData = $client->getAccessToken();
      WP_GOOGLE_CONTACT::updateToken( $tokenData );
   }

   public static function updateToken( $tokenData ) {
      $expires_in = isset( $tokenData['expires_in'] ) ? intval( $tokenData['expires_in'] ) : 0;
      $tokenData['expire'] = time() + $expires_in;
      try {
         $tokenJson = json_encode( $tokenData );
         update_option( 'wpgoco_token', $tokenJson );
         if(isset($tokenData['scope'])){
            $permission = explode(" ", $tokenData['scope']);
			update_option('wpgoco_verify', 'valid');
         }
       
      } catch ( Exception $e ) {
         wpgooglecontact_Utility_free::wptogocontdebug_log( "Token write fail! - " . $e->getMessage() );
      }
   }

   public function auth() {

       $maunal_setting = get_option('wpgoco_manual_setting') ? get_option('wpgoco_manual_setting') : '0';

        if ((isset($maunal_setting)) && ($maunal_setting == '1'))
            $tokenData = json_decode(get_option('wpgoco_token_manual'), true);
        else
            $tokenData = json_decode(get_option('wpgoco_token'), true);

      //$tokenData = json_decode( get_option( 'wpgoco_token' ), true );
      if ( !isset( $tokenData['refresh_token'] ) || empty( $tokenData['refresh_token'] ) ) {
         throw new LogicException( "Auth, Invalid OAuth2 access token" );
         exit();
      }

      try {
         $client = new Google_Client();
         
         if ($maunal_setting == '1') {
             $wpgoco_client_id = get_option('wpgoco_client_id');
             $wpgoco_secret_id = get_option('wpgoco_secret_id');
             $client->setClientId($wpgoco_client_id);
             $client->setClientSecret($wpgoco_secret_id);
         } else {
            $clientId = WP_GOOGLE_CONTACT::clientId_web;
            $clientSecret = WP_GOOGLE_CONTACT::clientSecret_web;
            
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
         }
		   $client->setScopes('https://www.google.com/m8/feeds');
         $client->addScope( 'https://www.googleapis.com/auth/userinfo.email' );
         $client->addScope( 'https://www.googleapis.com/auth/contacts.readonly' );
         $client->addScope( 'https://www.googleapis.com/auth/contacts' );

         $client->refreshToken( $tokenData['refresh_token'] );
         $client->setAccessType( 'offline' );

         // echo "<pre>";
         // print_r($tokenData);
         // exit;
        
         if($maunal_setting == '1')
            WP_GOOGLE_CONTACT::updateToken_manual( $tokenData );
         else
            WP_GOOGLE_CONTACT::updateToken( $tokenData );

         self::setInstance( $client );
      } catch ( Exception $e ) {
         throw new LogicException( "Auth, Error fetching OAuth2 access token, message: " . $e->getMessage() );
         exit();
      }
   }
  
   //constructed on call
    public static function preauth_manual($access_code, $client_id, $secret_id, $redirect_url)
    {
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($secret_id);
        $client->setRedirectUri($redirect_url);
        //$client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        //$client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
		$client -> setScopes('https://www.google.com/m8/feeds');
        $client->setAccessType('offline');
        $client->fetchAccessTokenWithAuthCode($access_code);
        $tokenData = $client->getAccessToken();
        WP_GOOGLE_CONTACT::updateToken_manual($tokenData);
    }

    public static function updateToken_manual($tokenData)
    {
        $tokenData['expire'] = time() + intval($tokenData['expires_in']);
        try {
            $tokenJson = json_encode($tokenData);
            update_option('wpgoco_token_manual', $tokenJson);
        } catch (Exception $e) {
            wpgooglecontact_Utility_free::wptogocontdebug_log("Token write fail! - " . $e->getMessage());
        }
    }


    /**
     * Generate token for the user and refresh the token if it's expired.
     *
     * @return array
     */
    public static function getClient_auth($flag = 0, $wptogocont_clientId = '', $wptogocont_clientSecert = '')
    {
        $wptogocont_client = new Google_Client();
        $wptogocont_client->setApplicationName('Manage wptogocont DB with Google Contacts');
        $wptogocont_client->addScope( 'https://www.googleapis.com/auth/userinfo.email' );
        $wptogocont_client->addScope( 'https://www.googleapis.com/auth/contacts.readonly' );
        $wptogocont_client->addScope( 'https://www.googleapis.com/auth/contacts' );
        $wptogocont_client->setClientId($wptogocont_clientId);
        $wptogocont_client->setClientSecret($wptogocont_clientSecert);
        $wptogocont_client->setRedirectUri(esc_html(admin_url('admin.php?page=wordpress-google-contacts-config')));
        $wptogocont_client->setAccessType('offline');
        $wptogocont_client->setApprovalPrompt('force');
        try {
            if (empty($wptogocont_auth_token)) {
                $wptogocont_auth_url = $wptogocont_client->createAuthUrl();
                return $wptogocont_auth_url;
            }
            if (!empty($wptogocont_gscwptogocont_accessToken)) {
                $wptogocont_accessToken = json_decode($wptogocont_gscwptogocont_accessToken, true);
            } else {
                if (empty($wptogocont_auth_token)) {
                    $wptogocont_auth_url = $wptogocont_client->createAuthUrl();
                    return $wptogocont_auth_url;
                }
            }

            $wptogocont_client->setAccessToken($wptogocont_accessToken);
            // Refresh the token if it's expired.
            if ($wptogocont_client->isAccessTokenExpired()) {
                // save refresh token to some variable
                $wptogocont_refreshTokenSaved = $wptogocont_client->getRefreshToken();
                $wptogocont_client->fetchAccessTokenWithRefreshToken($wptogocont_client->getRefreshToken());
                // pass access token to some variable
                $wptogocont_accessTokenUpdated = $wptogocont_client->getAccessToken();
                // append refresh token
                $wptogocont_accessTokenUpdated['refresh_token'] = $wptogocont_refreshTokenSaved;
                //Set the new acces token
                $wptogocont_accessToken = $wptogocont_refreshTokenSaved;
                gscwptogocont::gscwptogocont_update_option('wptogocontsheets_google_accessToken', json_encode($wptogocont_accessTokenUpdated));
                $wptogocont_accessToken = json_decode(json_encode($wptogocont_accessTokenUpdated), true);
                $wptogocont_client->setAccessToken($wptogocont_accessToken);
            }
        } catch (Exception $e) {
            if ($flag) {
                return $e->getMessage();
            } else {
                return false;
            }
        }
        return $wptogocont_client;
    }

    /** 
       * GFGSC_googlesheet::gsheet_print_google_account_email
       * Get Google Account Email
       * @since 3.1 
       * @retun string $google_account
       **/
       public function gsheet_print_google_account_email_manual() {
          try{
             $google_account = get_option("wpgoco_email_account_manual");
             if( false && $google_account ) {
                return $google_account;
             }
             else {
                
                $google_sheet = new WP_GOOGLE_CONTACT();
                $google_sheet->auth();            
                $email = $google_sheet->gsheet_get_google_account_email();
                update_option("wpgoco_email_account_manual", $email);
                return $email;
             }
          }catch(Exception $e){
             return false;
          }    
               
       }

	   /** 
		* GFGSC_googlesheet::gsheet_get_google_account_email
		* Get Google Account Email
		* @since 3.1 
		* @retun string $email
		**/
		public function gsheet_get_google_account_email() {		
			$google_account = $this->gsheet_get_google_account();	
			
			if( $google_account ) {
				return $google_account->email;
			}
			else {
				return "";
			}
		}

		/** 
	* GFGSC_googlesheet::gsheet_get_google_account
	* Get Google Account
	* @since 3.1 
	* @retun $user
	**/
	public function gsheet_get_google_account() {
		try {
			$client = $this->getInstance();
			if( ! $client ) {
				return false;
			}
			
			$service = new Google_Service_Oauth2($client);
			$user = $service->userinfo->get();			
		}
		catch (Exception $e) {
			wpgooglecontact_Utility_free::wptogocontdebug_log( __METHOD__ . " Error in fetching user info: \n " . $e->getMessage() );
			return false;
		}
		
		return $user;
	}


   public static function getAllContact() {
      try{
            $google_sheet = new WP_GOOGLE_CONTACT();
            $google_sheet->auth();
            $email = $google_sheet->gsheet_get_google_account_email();
            echo sanitize_email($email);
            exit;
      }catch(Exception $e){
         return false;
      }  
   }

   /** 
	* GFGSC_googlesheet::gsheet_print_google_account_email
	* Get Google Account Email
	* @since 3.1 
	* @retun string $google_account
	**/
	public function gsheet_print_google_account_email() {
		try{		
			$google_account = get_option("wpgoco_email_account");
			
			if( $google_account ) {
				return $google_account;
			}
			else {
				
				$google_sheet = new WP_GOOGLE_CONTACT();
				$google_sheet->auth();				 
				$email = $google_sheet->gsheet_get_google_account_email();
				update_option("wpgoco_email_account", $email);
				return $email;
			}
		}catch(Exception $e){
			return false;
		 } 		
	}


   /** 
	* GFGSC_googlesheet::gsheet_print_google_account_email
	* Get Google Account Email
	* @since 3.1 
	* @retun string $google_account
	**/
	public function get_all_google_contact($data = array()) {
		try{
         if(isset($data) && !empty($data)){
             $doc = new WP_GOOGLE_CONTACT();
             $doc->auth();
               $google_account = get_option("wpgoco_email_account");
               $token = get_option( 'wpgoco_token' );
               $token_access = json_decode($token)->refresh_token;
               
               $scopes = array(
                  'https://www.googleapis.com/auth/userinfo.profile',
                  'https://www.googleapis.com/auth/contacts',
                  'https://www.googleapis.com/auth/contacts.readonly'
            );
            $clientId = WP_GOOGLE_CONTACT::clientId_web;
            $clientSecret = WP_GOOGLE_CONTACT::clientSecret_web;

            //reference google contact field list : https://developers.google.com/people/contacts-api-migration
            $googleOAuth2Handler = new GoogleOAuth2Handler($clientId, $clientSecret, $scopes, $token_access);
            $people = new GooglePeople($googleOAuth2Handler);
            
            //$contactsAll = $people->all();//Working for get all
            //$contactInd  = $people->get('people/c9102447711783527895');//Working for individual contact
            
            $contact = new Contact($people);
            $val_array = array("emailAddresses", "phoneNumbers");
            foreach($this->google_contacts_fields as $class => $field){
               $contact->$class[0] = new stdClass;
               foreach($field as $f){
                  if(!in_array($f, $val_array))
                     $temp[$f] = $data[$f];
                  else
                     $temp['value'] = $data[$f];
               }
               $contact->$class[0] = (object) $temp;
               unset($temp);
            }
            // echo "<pre>";
            // print_r($contact);
            // exit;
            // $contact->names[0] = new stdClass;
            // $contact->names[0]->givenName = isset($data['givenName']) ? $data['givenName'] : '';
            // $contact->names[0]->familyName = isset($data['familyName']) ? $data['familyName'] : '';

            // //object
            // $contact->emailAddresses[0] = new stdClass;
            // $contact->phoneNumbers[0] = new stdClass;
            
            // $contact->phoneNumbers[0]->value = isset($data['phoneNumbers']) ? $data['phoneNumbers'] : '';
            // $contact->emailAddresses[0]->value = isset($data['emailAddresses']) ? $data['emailAddresses'] : '';

            $contact->save();
         }
        return;
		}catch(Exception $e){
         echo "<pre>";
         print_r($e);
         exit;
			return false;
		 } 		
	}


}