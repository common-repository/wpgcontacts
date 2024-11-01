jQuery(document).ready(function () {

   setTimeout(function () {
      if(localStorage.tabname!="" && localStorage.tabname!=undefined)
         tab_google_contact(localStorage.tabname);
      else
         tab_google_contact('google-integration');

   }, 1000);
     /**
    * verify the api code
    * @since 1.0
    */
    jQuery(document).on('click', '#save-wptogocont-code', function () {
        jQuery(this).parent().children(".loading-sign").addClass( "loading" );
        var data = {
        action: 'verify_wpgooglecontact_integation',
        code: jQuery('#wptogocont-code').val(),
        security: jQuery('#wptogocont-ajax-nonce').val()
      };
      console.log(data);
      jQuery.post(ajaxurl, data, function (response ) {
         if ( response == -1 ) {
            return false; // Invalid nonce
         }
         
         if( ! response.success ) { 
           jQuery( ".loading-sign" ).removeClass( "loading" );
           jQuery( "#wptogocont-validation-message" ).empty();
           jQuery("<span class='error-message'>Invalid Access code entered.</span>").appendTo('#wptogocont-validation-message');
         } else {
           jQuery( ".loading-sign" ).removeClass( "loading" );
           jQuery( "#wptogocont-validation-message" ).empty();
           jQuery("<span class='wptogocont-valid-message'>Your Google Access Code is Authorized and Saved.</span> <br/><br/><span class='wp-valid-notice'> Note: If you are getting any errors or not showing sheet in dropdown, then make sure to check the debug log. To contact us for any issues do send us your debug log.</span>").appendTo('#wptogocont-validation-message');
		   //setTimeout(function () { location.reload(); }, 1000);
            setTimeout(function () {
               window.location.href = jQuery("#redirect_auth_wptogocont").val();
            }, 1000);
         }
      });
      
    }); 
    
	 /**
    * deactivate the api code
    * @since 1.0
    */
    jQuery(document).on('click', '#deactivate-log', function () {
        jQuery(".loading-sign-deactive").addClass( "loading" );
		var txt;
		var r = confirm("Are You sure you want to deactivate Google Integration ?");
		if (r == true) {
			var data = {
				action: 'deactivate_wpgooglecontact_integation',
				security: jQuery('#wptogocont-ajax-nonce').val()
			};
			jQuery.post(ajaxurl, data, function (response ) {
				if ( response == -1 ) {
					return false; // Invalid nonce
				}
			 
				if( ! response.success ) {
					alert('Error while deactivation');
					jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
					jQuery( "#deactivate-message" ).empty();
					
				} else {
					jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
					jQuery( "#deactivate-message" ).empty();
					jQuery("<span class='wptogocont-valid-message'>Your account is removed. Reauthenticate again to integrate Contact Form with Google Sheet.</span>").appendTo('#deactivate-message');
		   		    setTimeout(function () { location.reload(); }, 1000);
				}
			});
		} else {
			jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
		}
        
      
      
    }); 
	
   /**
    * Clear debug
    */
   jQuery(document).on('click', '.debug-clear', function () {
      jQuery(".clear-loading-sign").addClass("loading");
      var data = {
         action: 'wptogocontclear_log',
         security: jQuery('#wptogocont-ajax-nonce').val()
      };
      jQuery.post(ajaxurl, data, function ( response ) {
         if (response == -1) {
            return false; // Invalid nonce
         }
         
         if (response.success) {
            jQuery(".clear-loading-sign").removeClass("loading");
            jQuery("#wptogocont-validation-message").empty();
            jQuery("<span class='wptogocont-valid-message'>Logs are cleared.</span>").appendTo('#wptogocont-validation-message');
         }
      });
   });

});


jQuery(document).ready(function($) {
//================= Code From AVADA ==================

//================ Selectc wptogocont ============ START
jQuery('#wpgooglecontact_select').change(function (e) {
      var FormId = jQuery(this).val();
      select_form_id(FeedId,FormId);
      
});

 function html_decode(input){
	var doc = new DOMParser().parseFromString(input, "text/html");
	return doc.documentElement.textContent;
}



function options_selected_sheet(tabs_arr){
   var tabs_option = '';
   jQuery.each( tabs_arr, function( key, value ) {
      tabs_option+='<option value="'+value+'">'+key+'</option>'; 
   });
   console.log("==== tab option ===="+tabs_option);
   jQuery("#wpgooglecontact-gs-sheet-tab-name").html(tabs_option);
   var sheetTabValue = jQuery("#wpgooglecontact-gs-sheet-tab-name").find('option:first').text();
   jQuery('#sheet-tab-name').val(sheetTabValue); 
}




   // add input field for custom name
  jQuery(document).on("click", "#manual-name", function () {
    var sheetname = jQuery(this).val();
    jQuery(this).parent().children(".loading-sign").addClass( "loading" );
     if(jQuery(this).is(":checked")) {
        jQuery(".wpgooglecontact-gs-fields").addClass('hide');
        jQuery(".manual-fields").removeClass('hide');
     } else {
        jQuery(".wpgooglecontact-gs-fields").removeClass('hide');
        jQuery(".manual-fields").addClass('hide');
     }
   });

//================ Selectc wptogocont ============ END

/* drop down event for Google API */
   if(jQuery("#wptogocont_manual_setting").val() == '1')
   {
      jQuery("#wptogocont_dro_option").val('wptogocont_manual');
      jQuery(".fgc_api_manual_setting").show();
      jQuery(".fgc_api_existing_setting").hide();
   }
   jQuery(document).on('change', '#wptogocont_dro_option', function () {
          var option = jQuery('option:selected', jQuery(this)).val();
          if(option == "wptogocont_manual")
          {
            jQuery(".fgc_api_manual_setting").show();
            jQuery(".fgc_api_existing_setting").hide();
          }else{
            jQuery(".fgc_api_manual_setting").hide();
            jQuery(".fgc_api_existing_setting").show();
          }  
   });
   /* drop down event for Google API */


   /*Save wptogocont integration(Auth) Manual */
   /**
   * verify the api code
   * @since 1.0
   */
   jQuery(document).on('click', '#save-wptogocont-manual', function (event) {
      event.preventDefault();
      jQuery(".loading-sign").addClass("loading");
      var data = {
         action: 'fgc_save_client_id_sec_id_gapi',
         client_id: jQuery('#wptogocont-client-id').val(),
         secret_id: jQuery('#wptogocont-secret-id').val(),
         wptogocont_client_token: jQuery('#wptogocont-client-token').val(),
         security: jQuery('#wptogocont-ajax-nonce').val()
      };
      jQuery.post(ajaxurl, data, function (response) {
         if (!response.success) {
            jQuery(".loading-sign").removeClass("loading");
            jQuery("#wptogocont-validation-message").empty();
            jQuery("<span class='error-message'>Access code Can't be blank.</span>").appendTo('#wptogocont-validation-message');
         } else {
            jQuery(".loading-sign").removeClass("loading");
            jQuery("#wptogocont-validation-message").empty();
            jQuery("<span class='wptogocont-valid-message'>Your Google Access Code is Authorized and Saved.</span> ").appendTo('#wptogocont-validation-message');
            //setTimeout(function () { location.reload(); }, 1000);
            setTimeout(function () {
               window.location.href = jQuery("#redirect_auth_wptogocont").val();
            }, 1000);
         }
      });
   });

   /**
   * verify the api code
   * @since 1.0
   */
   jQuery(document).on('click', '#wptogocont-deactivate-auth', function (event) {
      event.preventDefault();
      jQuery(".loading-sign").addClass("loading");
      var data = {
         action: 'fgc_deactivate_auth_token_gapi',
         security: jQuery('#wptogocont-ajax-nonce').val()
      };
      jQuery.post(ajaxurl, data, function (response) {
         if (!response.success) {
            jQuery(".loading-sign").removeClass("loading");
            jQuery("#wptogocont-validation-message").empty();
            jQuery("<span class='error-message'>Access code Can't be blank.</span>").appendTo('#wptogocont-validation-message');
         } else {
            jQuery(".loading-sign").removeClass("loading");
            jQuery("#wptogocont-validation-message").empty();
            jQuery("<span class='wptogocont-valid-message'>Your Google Access Code is Authorized and Saved.</span> ").appendTo('#wptogocont-validation-message');
            setTimeout(function () { location.reload(); }, 1000);
         }
      });

   });

   /**
   * reset form
    @since 1.0
   */
   jQuery(document).on('click', '#save-wptogocont-reset', function (event) {
      jQuery("#wptogocont-client-id").val('');
      jQuery("#wptogocont-secret-id").val('');
      jQuery("#wptogocont-client-token").val('');
      jQuery("#wptogocont-client-id").removeAttr('disabled');
      jQuery("#wptogocont-secret-id").removeAttr('disabled');
      jQuery("#save-wptogocont-manual").removeAttr('disabled');
      
   });
   /*Save wptogocont integration(Auth) Manual */


    /**
  * verify the api code for manual setup
  * @since 1.0
  */
 jQuery(document).on('click', '#save-method-api-wptogocont', function (event) {
     event.preventDefault();
     jQuery(".loading-sign-method-api").addClass("loading");
     var method_api_wptogocont = jQuery("#wptogocont_dro_option").val();
     console.log(method_api_wptogocont);
     var data = {
         action: 'save_method_api_wptogocont',
         method_api_wptogocont : method_api_wptogocont,
         security: jQuery('#wptogocont-ajax-nonce').val()
     };
     jQuery.post(ajaxurl, data, function (response) {
         setTimeout(function () {
                 location.reload();
             }, 1000);
     });
 });
 
 
});


if(localStorage.tabname == '' || localStorage.tabname == undefined){
   localStorage.tabname = 'google-integration';
}

function tab_google_contact(tabname){
   localStorage.tabname = tabname;
   jQuery(".tab-all-setting").css("display","none");
   jQuery(".all-"+tabname).css("display", "block");
   jQuery(".sec-tab-link").removeClass( "active" );
   jQuery(".sec-tab-link").removeClass( "active" );
   jQuery("."+tabname).addClass("active");
   jQuery(".loader-google-contact").css("display","none");

   if(tabname == 'cf7-settings'){
      jQuery(".cf7_show_field_list").css('display','none');
      jQuery(".cf7_show_form_list").css('display','block'); 
   }else if(tabname == 'wpforms-settings'){
      //jQuery(".wpform_show_field_list").css('display','none');
      //jQuery(".wpform_show_form_list").css('display','block');
   }else if(tabname == 'gravityforms-settings'){
      //jQuery(".gravityforms_show_field_list").css('display','none');
      //jQuery(".gravityforms_show_form_list").css('display','block');
   }else if(tabname == 'ninjaforms-settings'){
      //jQuery(".ninjaforms_show_field_list").css('display','none');
      //jQuery(".ninjaforms_show_form_list").css('display','block');
   }
}

jQuery(document).on('click', '.show-cf7form-field', function(){
   jQuery(".loader-google-contact").css("display","block");
   form_id = jQuery(this).attr('data-id');
   var data = {
      action: 'get_cf7_form_fields_list',
      security: jQuery('#wptogocont-ajax-nonce').val(),
      form_id : form_id
   };
   jQuery.post(ajaxurl, data, function ( response ) {
      if (response == -1) {
         return false; // Invalid nonce
      }
      if (response.success) {
         jQuery(".loader-google-contact").css("display","none");
         //jQuery(".show_form_back").css('display','block');
         jQuery(".cf7_show_form_list").css('display','none');
         jQuery(".cf7_show_field_list").html(response.data);
         jQuery(".cf7_show_field_list").css('display','block');
         //console.log(response.data);
      }
   });

});

jQuery(document).on('click', '.show_form_back', function(){
         jQuery(".cf7_show_field_list").css('display','none');
         jQuery(".cf7_show_form_list").css('display','block');  
});

var txt = [];
var json_arr = {};
var json_arr_combine = {};
var json_arr_new = {};

function getFormfieldVal(field){
   var dataid = jQuery(field).attr('data-id');
   //console.log(dataid);
   if(field.value!="" && field.value!=undefined){
      
      if(dataid == "" || dataid == undefined){
         console.log('select to value'); 
         //json_arr_new[field.name] = field.value;
         var fieldName = jQuery(field).find('option:selected').text();
         //jQuery("#cf7-google-field-map").val(JSON.stringify(json_arr_new));
         jQuery('.google-contact-drop option[value='+field.value+']').attr("disabled","disabled");
         jQuery(field).attr('data-id', field.value);
      }else{
         console.log('value to value'); 

         var disabledfield = jQuery(field).attr('data-id');
         //json_arr_new[field.name] = field.value;
         var fieldName = jQuery(field).find('option:selected').text();
         //jQuery("#cf7-google-field-map").val(JSON.stringify(json_arr_new));
         jQuery('.google-contact-drop option[value='+field.value+']').attr("disabled","disabled");
         jQuery('.google-contact-drop option[value='+disabledfield+']').removeAttr("disabled");
         jQuery(field).attr('data-id', field.value);
      }
   }else{
      console.log('value to select'); 
      var disabledfield = jQuery(field).attr('data-id');
      //console.log(jQuery("#cf7-google-field-map").val());
      jQuery('.google-contact-drop option[value='+disabledfield+']').removeAttr("disabled");
      jQuery(field).attr('data-id', '');

   }
}

jQuery(document).on('click', '.save_googleform_fields', function(e){
   jQuery(".loader-google-contact").css("display","block");
   e.preventDefault();
   var formDataSelect = jQuery('.google-contact-drop');
   console.log('===== serialize formdata  =====');
   console.log(formDataSelect);
   var formData = {};
   jQuery.each(formDataSelect, function(i, field) {
      if(field.value != "")
         formData[field.name] = field.value;
  });
  console.log(formData);
   var data = {
      action: 'save_googleform_fields',
      security: jQuery('#wptogocont-ajax-nonce').val(),
      formData : formData,
      formId : jQuery('#cf7-form-id').val(),
   };
   //console.log(formData);

   jQuery.post(ajaxurl, data, function ( response ) {
      if (response == -1) {
         return false; // Invalid nonce
      }
      if (response.success) {
         console.log(response);
         if(response.data!=undefined){
            alert('Fields are not selected');
         }
      }
       jQuery(".loader-google-contact").css("display","none");

   });
   
});