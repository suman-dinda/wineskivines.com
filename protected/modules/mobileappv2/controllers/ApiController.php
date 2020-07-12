<?php
class ApiController extends CController
{
	
	public $data;
	public $code=2;
	public $msg='';
	public $details='';	
	public static $social_strategy='mobileapp2';
	
	public $device_uiid;
	public $merchant_id;
	
	public function __construct()
	{		
		
		$website_timezone=getOptionA('website_timezone');
	    if (!empty($website_timezone)){
	 	   Yii::app()->timeZone=$website_timezone;
	    }	
				
		$this->data=$_GET;
		$this->getGETData();
										
		$lang=Yii::app()->language;		
	}
	
	public function t($message='')
	{
		return Yii::t("mobile2",$message);
	}
	
	public function beforeAction($action)
	{		
		if(isset($_GET['debug'])){ 
	       dump("<h3>Request</h3>");
       	   dump($this->data);
        }                       
        
        /*CHECK API HASH KEY*/
        $api_key = isset($_REQUEST['api_key'])?trim($_REQUEST['api_key']):'';        
        $api_has_key = trim(getOptionA('mobileapp2_api_has_key'));
                
        if($api_has_key!=$api_key){
        	$this->msg = mt("invalid api hash key");
        	$this->output();
        	return false;
        }	
                
        return true;
	}
	
	private function output()
    {
    	
       if (!isset($this->data['debug'])){    		
       	  header('Access-Control-Allow-Origin: *');       	  
          header('Content-type: application/javascript;charset=utf-8');          
       } 
       
	   $resp=array(
	     'code'=>$this->code,
	     'msg'=>$this->msg,
	     'details'=>$this->details,	     	     
	     'get'=>$_GET,
	     'post'=>$_POST
	   );		   
	   if (isset($this->data['debug'])){
	   	   dump($resp);
	   }
	   
	   if (!isset($_GET['callback'])){
  	   	   $_GET['callback']='';
	   }    
	   
	   if (isset($_GET['jsonp']) && $_GET['jsonp']==TRUE){	   		   	   
	   	   echo $_GET['callback'] . '('.CJSON::encode($resp).')';
	   } else echo CJSON::encode($resp);
	   Yii::app()->end();
    }	
	
    public function actionIndex(){
		echo "API IS WORKING";
	}	
	
	private function getGETData()
	{
		$this->device_uiid = isset($this->data['device_uiid'])?$this->data['device_uiid']:'';
        $this->merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
	}
	
	private function getPOSTData()
	{
		$this->device_uiid = isset($_POST['device_uiid'])?$_POST['device_uiid']:'';
        $this->merchant_id = isset($_POST['merchant_id'])?$_POST['merchant_id']:'';
	}
	
	private function checkToken()
	{
		$this->data['user_token'] = isset($this->data['user_token'])?$this->data['user_token']:'';
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			return false;
		}			
		$client_id = $res['client_id'];	
		return $client_id;
		
	}
		
	public function actiongetSettings()
	{			
		$this->code = 1;
		$this->msg = "OK";
		
		$settings=array();		
		
		$mobile_prefix = getOptionA('mobileapp2_prefix');	
		if(empty($mobile_prefix)){
			$mobile_countrycode = require_once 'MobileCountryCode.php';		
			$admin_country_set=getOptionA('admin_country_set');
			if(!empty($admin_country_set)){
			  if(array_key_exists($admin_country_set,$mobile_countrycode)){
			  	 $mobile_prefix = "+". $mobile_countrycode[$admin_country_set]['code'];
			  }	
			}	
		} else $mobile_prefix = "+$mobile_prefix";
						
		$startup_options = getOptionA('mobileapp2_startup');		
		if(method_exists('mobileWrapper','getStartupBanner')){
			$settings['startup']=array(
			  'options'=>empty($startup_options)?1:$startup_options,			  
			  'select_language'=>getOptionA('mobile2_enabled_select_language'),
			  'banner'=>mobileWrapper::getStartupBanner(),
			  'startup_auto_scroll'=>getOptionA('mobileapp2_startup_auto_scroll'),
			  'startup_interval'=>getOptionA('mobileapp2_startup_interval'),
			);
		} else {
			$settings['startup']=array(
			  'options'=>1,			  
			  'select_language'=>0,
			  'banner'=>array(),
			);
		}	
		
		$location_rep = mobileWrapper::searchMode();
		$settings['search_mode']=$location_rep['search_mode'];
		$settings['location_mode'] = $location_rep['location_mode'];
		$settings['mobile_prefix']=$mobile_prefix;
		$settings['cart_theme']=getOptionA('mobileapp2_cart_theme');
		$settings['future_order_confirm']=getOptionA('mobileapp2_future_order_confirm');
		$settings['mobile_turnoff_prefix']=getOptionA('mobileapp2_turnoff_prefix');
		$settings['menu_type']= mobileWrapper::getMenuType();		
		$settings['enabled_dish']=getOptionA('mobile2_enabled_dish');
		$settings['disabled_image_menu1']=getOptionA('mobile2_disabled_image_menu1');
		$settings['mobileapp2_select_map']=getOptionA('mobileapp2_select_map');
		$settings['mobileapp2_language']=getOptionA('mobileapp2_language');
		$settings['mobile2_enabled_fblogin']=getOptionA('mobile2_enabled_fblogin');
		$settings['mobile2_enabled_googlogin']=getOptionA('mobile2_enabled_googlogin');
		$settings['mobile2_analytics_enabled']=getOptionA('mobile2_analytics_enabled');
		$settings['mobile2_analytics_id']=getOptionA('mobile2_analytics_id');
		$settings['mobile2_location_accuracy']=getOptionA('mobileapp2_location_accuracy');
		if(empty($settings['mobile2_location_accuracy'])){
			$settings['mobile2_location_accuracy']='REQUEST_PRIORITY_BALANCED_POWER_ACCURACY';
		}	
		
		$settings['age_restriction'] = getOptionA('age_restriction');
		$settings['age_restriction_content'] = getOptionA('age_restriction_content');
		
		$mobileapp2_reg_email = getOptionA('mobileapp2_reg_email');
		$mobileapp2_reg_phone = getOptionA('mobileapp2_reg_phone');
		
		if(empty($mobileapp2_reg_email) && empty($mobileapp2_reg_phone)){
			$mobileapp2_reg_email=1;$mobileapp2_reg_phone=1;
		}	
		
		$settings['registration'] = array(
		  'email'=>$mobileapp2_reg_email,
		  'phone'=>$mobileapp2_reg_phone
		);
		
		$settings['tracking_theme'] = getOptionA('mobileapp2_tracking_theme');
		if(empty($settings['tracking_theme'])){
			$settings['tracking_theme']=1;
		}	
		$settings['tracking_interval'] = getOptionA('mobileapp2_tracking_interval');
		if($settings['tracking_interval']<=0 && empty($settings['tracking_interval'])){
			$settings['tracking_interval']=7000;
		}	
		
		$settings['home']=array(
		  'mobile2_home_offer'=>getOptionA('mobile2_home_offer'),
		  'mobile2_home_featured'=>getOptionA('mobile2_home_featured'),
		  'mobile2_home_cuisine'=>getOptionA('mobile2_home_cuisine'),
		  'mobile2_home_all_restaurant'=>getOptionA('mobile2_home_all_restaurant'),
		  'mobile2_home_favorite_restaurant'=>getOptionA('mobile2_home_favorite_restaurant'),
		  'mobile2_home_banner'=>getOptionA('mobile2_home_banner'),
		  'mobile2_home_banner_full'=>getOptionA('mobile2_home_banner_full'),
		  'mobile2_home_food_discount'=>getOptionA('mobile2_home_food_discount'),
		  'mobile2_home_banner_auto_scroll'=>getOptionA('mobile2_home_banner_auto_scroll')
		);
		
		$code_version = isset($this->data['code_version'])?$this->data['code_version']:'';
		if($settings['home']['mobile2_home_banner']==1){
			if($code_version>=1.5){				
				$settings['home_banner'] = mobileWrapper::getHomeBannerNew();
			} else $settings['home_banner'] = mobileWrapper::getHomeBanner();
		} else $settings['home_banner'] = array();
		
		$settings['mobile2_disabled_default_image'] = getOptionA('mobile2_disabled_default_image');
		
		$settings['map_provider'] = FunctionsV3::getMapProvider();
		$settings['map_country']  = FunctionsV3::getCountryCode();		
		
		$settings['map_auto_identity_location']  = false;
		
		$settings['default_map_location']  = array(
		  'lat'=>getOptionA('mobile2_default_lat'),
		  'lng'=>getOptionA('mobile2_default_lng')
		);
						
		$settings['website_hide_foodprice'] = getOptionA('website_hide_foodprice');
		$settings['enabled_map_selection_delivery'] = getOptionA('enabled_map_selection_delivery');
		$settings['website_hide_foodprice'] = getOptionA('website_hide_foodprice');
		$settings['merchant_two_flavor_option'] = getOptionA('merchant_two_flavor_option');
		$settings['images']=array(
		   'image1'=>mobileWrapper::getImage('mobile-default-logo.png'),
		   'image2'=>mobileWrapper::getImage('resto_banner.jpg','resto_banner.jpg'),
		   'image3'=>mobileWrapper::getImage('default_bg.jpg','default_bg.jpg'),
		);			
		$settings['icons']=array(
		  'marker1'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/icon_28.png",
		  'marker2'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/marker_green.png",
		  'marker3'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/marker_orange.png",
		  'bicycle'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/bicycle.png",
		  'bike'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/bike.png",
		  'car'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/car.png",
		  'scooter'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/scooter.png",
		  'truck'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/truck.png",
		  'walk'=>websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/walk.png",
		);	
		$settings['marker_icon']=array(
		   $settings['icons']['marker1'],
		   $settings['icons']['marker2'],
		   $settings['icons']['marker3'],
		   websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/marker1.png",
		   websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/marker2.png",
		   websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/marker3.png",
		   websiteUrl()."/protected/modules/".APP_FOLDER."/assets/images/marker4.png",
		);
		$settings['list_type'] = mobileWrapper::getListType();
		
		$settings['addon']['driver']=false;
		if (mobileWrapper::showDriverSignup()){
			$settings['addon']['driver']=true;
			$settings['addon']['driver_transport']=Driver::transportType();			
		}
				
		$settings['addon']['points']=false;
		if (FunctionsV3::hasModuleAddon("pointsprogram")){
			$settings['addon']['points']=true;
		}
				
		if($settings['mobileapp2_language']=="0"){			
		   $settings['mobileapp2_language']='en';
		}
		
		$settings['cod_change_required']=getOptionA('cod_change_required');
	    $settings['disabled_website_ordering']=getOptionA('disabled_website_ordering');
	    $settings['mapbox_access_token']=getOptionA('mapbox_access_token');
	    $settings['mapbox_default_zoom']=getOptionA('mapbox_default_zoom');
	    $settings['disabled_cc_management']=getOptionA('disabled_cc_management');
	    $settings['merchant_tbl_book_disabled']=getOptionA('merchant_tbl_book_disabled');
		
		$settings['currency_symbol'] = getCurrencyCode();
		$settings['currency_position']=getOptionA('admin_currency_position');
	    $settings['currency_decimal_place']=getOptionA('admin_decimal_place');
	    $settings['currency_space']=getOptionA('admin_add_space_between_price');
	    $settings['currency_use_separators']=getOptionA('admin_use_separators');
	    $settings['currency_decimal_separator']=getOptionA('admin_decimal_separator');
	    $settings['currency_thousand_separator']=getOptionA('admin_thousand_separator');
	    	    
		if(empty($settings['currency_position'])){
			$settings['currency_position']='left';
		}
		if(empty($settings['currency_decimal_place'])){
			$settings['currency_decimal_place']=2;
		}
		if(empty($settings['currency_decimal_separator'])){
			$settings['currency_decimal_separator']=".";
		}
		if($settings['currency_use_separators']=="yes"){
			if($settings['currency_thousand_separator']==""){
				$settings['currency_thousand_separator']=",";
			}		
		}	
		
		$reg_field_1 = getOptionA('client_custom_field_name1');
		$reg_field_2 = getOptionA('client_custom_field_name2');
		
		$settings['reg_custom']=0;
		
		if(!empty($reg_field_1) || !empty($reg_field_2)){
			$fields=array();
			if(!empty($reg_field_1)){
				$fields['custom_field1']=$reg_field_1;
			}
			if(!empty($reg_field_2)){
				$fields['custom_field2']=$reg_field_2;
			}
			$settings['reg_custom_fields']=$fields;
			$settings['reg_custom']=1;
		}
		
		$valid_token = 2; $client_info = array();
		if(isset($this->data['user_token'])){
			if($client_info = mobileWrapper::getCustomerByToken($this->data['user_token'])){						   
			   if (!mobileWrapper::checkBlockAccount($client_info['email_address'],$client_info['contact_phone'])){
			   	   $valid_token = 1;			   	   
			   } 		
			}
		}
						
		$settings['valid_token']=$valid_token;
		
		if ($device_info = mobileWrapper::getDeviceByUIID( $this->device_uiid )){
			$settings['subscribe_topic']= $device_info['subscribe_topic'];
		} else $settings['subscribe_topic'] = 1;
		
		$sortby = array(
		  'restaurant'=>mobileWrapper::sortRestaurantList(),
		  'cusine'=>mobileWrapper::sortCuisineList(),
		  'food_promo'=>mobileWrapper::foodPromoSort(),
		);
		$settings['sort']=$sortby;

		$filters = array(
		  'delivery_fee'=>array(
		    'delivery_fee'=>$this->t("Free Delivery")
		  ),
		  'promos'=>array(
		    'offer'=>mt("Offers"),
		    'voucher'=>mt("Vouchers"),
		  ),
		  'services'=>mobileWrapper::servicesList(),		  
		  'dishes_list'=>itemWrapper::dishesList(),		
		  'cuisine'=>Yii::app()->functions->Cuisine(false),		  
		  'minimum_order'=>FunctionsV3::minimumDeliveryFee()
		);
		$settings['filters']=$filters;
		
		
		$settings['custom_pages'] = mobileWrapper::getTitlePages();
		$settings['custom_pages_location'] = getOptionA('mobileapp2_custom_pages_position');
		
		$settings['order_tabs'] = array(
		  'all'=>mt("All"),
		  'processing'=>mt("Processing"),
		  'completed'=>mt("Completed"),
		  'cancelled'=>mt("Cancelled"),
		);
		
		$settings['booking_tabs'] = array(
		  'all'=>mt("All"),
		  'pending'=>mt("Pending"),
		  'approved'=>mt("Approved"),
		  'denied'=>mt("Denied"),
		);
				
		$app_dict = Mobileappv2Module::$global_dict;		
		$dict_cuisine = mobileWrapper::cuisineListDict( $filters['cuisine'] );
		$dict_pages = mobileWrapper::customerPageDict( $settings['custom_pages'] );	
		$dict = array_merge((array)$app_dict,(array)$dict_cuisine, (array)$dict_pages );		
		$settings['dict'] = $dict;		
		
		$website_terms_customer = getOptionA('website_terms_customer');
		$settings['signup_settings'] = array(
		  'enabled_terms_condition'=> $website_terms_customer=="yes"?1:0,
		  'terms_url'=>FunctionsV3::prettyUrl(getOptionA('website_terms_customer_url'))
		);
		
		$settings['enabled_addon_desc'] = getOptionA('mobile2_enabled_addon_desc');
		$settings['geocomplete_default_country'] = getOptionA('google_default_country');
		
		$p = new CHtmlPurifier();
		$contact_content_default = mt("We are always happy to hear from our clients and visitors, you may contact us anytime");
		$contact_content = $p->purify(getOptionA('contact_content'));
		
		$contact_field = getOptionA('contact_field');
		if(!empty($contact_field)){
			$contact_field = json_decode($contact_field,true);
		} else {
			$contact_field = array('name','email');
		}	
						
		$settings['contact_us'] = array(
		  'contact_content'=>!empty($contact_content)?nl2br($contact_content):$contact_content_default,
		  'contact_field'=>$contact_field,
		  'enabled_contact'=>getOptionA('mobileapp2_enabled_contact')
		);
		
		$settings['remove_contact'] = getOptionA('mobileapp2_remove_contact');
		
		$lang_rtl = getOptionA('lang_rtl');		
		$settings['lang_rtl'] = !empty($lang_rtl)?json_decode($lang_rtl,true):array();
		
		$settings['customer_forgot_password_sms'] = getOptionA('customer_forgot_password_sms');
		
		$this->details=array(
		  'valid_token'=>$valid_token,
		  'settings'=>$settings
		);
			
					
		$this->output();
	}
	
	public function actiongetMobileCodeList()
	{
		$mobile_countrycode = require_once 'MobileCountryCode.php';
		$data = array();
		
		foreach ($mobile_countrycode as $key=>$val) {						
			$val['name']=ucwords(strtolower($val['name']));
			$val['country_code']=$key;
			$data[]=$val;			
		}
				
		$this->code=1;
		$this->msg="OK";
		$this->details = array(				  
		  'data'=>$data
		);
		$this->output();
	}
	
	public function actioncreateAccount()
	{
		$this->data = $_POST;		
		
		$Validator=new Validator;
		if ($this->data['password']!=$this->data['cpassword']){			
			$Validator->msg[] = $this->t("Confirm password does not match");
		}
		
		$mobileapp2_reg_email = getOptionA('mobileapp2_reg_email');
		$mobileapp2_reg_phone = getOptionA('mobileapp2_reg_phone');
		
		if(empty($mobileapp2_reg_email) && empty($mobileapp2_reg_phone)){
			$mobileapp2_reg_email=1;$mobileapp2_reg_phone=1;
		}			
		
		/*check if email address is blocked*/
		if($mobileapp2_reg_email==1){
	    	if ( FunctionsK::emailBlockedCheck($this->data['email_address'])){
	    		$Validator->msg[] = $this->t("Sorry but your email address is blocked by website admin");    		
	    	}	    
	    	if ( $resp = Yii::app()->functions->isClientExist($this->data['email_address']) ){			
			    $Validator->msg[] = $this->t("Sorry but your email address already exist in our records");
		    }
		}
    	
		if($mobileapp2_reg_phone==1){
	    	if ( FunctionsK::mobileBlockedCheck($this->data['contact_phone'])){
				$Validator->msg[] = $this->t("Sorry but your mobile number is blocked by website admin");			
			}
			$functionk=new FunctionsK();
			if ( $functionk->CheckCustomerMobile($this->data['contact_phone'])){
	        	$Validator->msg[] = $this->t("Sorry but your mobile number is already exist in our records");        	
	        }	
		}		
		
		if($Validator->validate()){
			$p = new CHtmlPurifier();			
			$params=array(
    		  'first_name'=>$p->purify($this->data['first_name']),
    		  'last_name'=>$p->purify($this->data['last_name']),    		  
    		  'password'=>md5($this->data['password']),
    		  'date_created'=>FunctionsV3::dateNow(),
    		  'last_login'=>FunctionsV3::dateNow(),
    		  'ip_address'=>$_SERVER['REMOTE_ADDR'],    		  
    		  'social_strategy'=>self::$social_strategy
    		);    	
    		
    		if($mobileapp2_reg_email==1){
    			$params['email_address'] = $p->purify(trim($this->data['email_address']));
    		}
    		if($mobileapp2_reg_phone==1){
    			$params['contact_phone'] = trim($this->data['contact_phone']);
    		}
    		
    		if (isset($this->data['custom_field1'])){
	    		$params['custom_field1']=!empty($this->data['custom_field1'])?$this->data['custom_field1']:'';
	    	}
	    	if (isset($this->data['custom_field2'])){
	    		$params['custom_field2']=!empty($this->data['custom_field2'])?$this->data['custom_field2']:'';
	    	}
	    	
	    	//$this->data['next_step']='map_select_location';
	    		    	
    		/** send verification code */
            $verification=getOptionA('website_enabled_mobile_verification');            
	    	if ( $verification=="yes" && $mobileapp2_reg_phone==1){
	    		$code=Yii::app()->functions->generateRandomKey(5);		    		
	    		FunctionsV3::sendCustomerSMSVerification($params['contact_phone'],$code);
	    		$params['mobile_verification_code']=$code;
	    		$params['status']='pending';
	    		$this->data['next_step'] = 'verification_mobile';
	    	}	    	
	    	
	    	/*send email verification added on version 3*/
	    	$email_code=Yii::app()->functions->generateRandomKey(5);
	    	$email_verification=getOptionA('theme_enabled_email_verification');
	    	if ($email_verification==2 && $mobileapp2_reg_email==1){
	    		$params['email_verification_code']=$email_code;
	    		$params['status']='pending';
	    		FunctionsV3::sendEmailVerificationCode($params['email_address'],$email_code,$params);
	    		$this->data['next_step'] = 'verification_email';
	    	}
	    	
	    	$token = mobileWrapper::generateUniqueToken(15,$this->data['device_uiid']);
	    	$params['token']=$token;
	    		    	
	    	$DbExt=new DbExt;
	    	
	    	if ( $DbExt->insertData("{{client}}",$params)){	    		
	    		$customer_id =Yii::app()->db->getLastInsertID();	    		
	    		$this->code=1;
	    		$this->msg = $this->t("Registration successful");
	    		
	    		if ( $verification=="yes" && $mobileapp2_reg_phone==1){	    				
    				$this->msg=t("We have sent verification code to your mobile number");
    				
    				$this->data['client_id'] = $customer_id;
				    mobileWrapper::registeredDevice($this->data,'pending');
				    
    			} elseif ( $email_verification ==2 && $mobileapp2_reg_email==1 ){ 
    				$this->msg = mt("We have sent verification code to your email address");
    				
    				$this->data['client_id'] = $customer_id;
				    mobileWrapper::registeredDevice($this->data,'pending');
    			} else {    				
    				/*sent welcome email*/	
    				FunctionsV3::sendCustomerWelcomeEmail($params);
    				
    				$this->data['client_id'] = $customer_id;
				    mobileWrapper::registeredDevice($this->data);
    			}	   
    			
    			$this->details = array(
    			  //'form_next_step'=>isset($this->data['form_next_step'])?$this->data['form_next_step']:'',
    			  'next_step'=>isset($this->data['next_step'])?$this->data['next_step']:'',
    			  'customer_token'=>$token,    			  
    			  'contact_phone'=>isset($params['contact_phone'])?$params['contact_phone']:'',
    			  'email_address'=>isset($params['email_address'])?$params['email_address']:'',
    			);
	    		
    			/*POINTS PROGRAM*/	    			
	    	    if (FunctionsV3::hasModuleAddon("pointsprogram")){
	    		    PointsProgram::signupReward($customer_id);
	    	    }	    	    	    	    
	    	    
	    	} else $this->msg = $this->t("Something went wrong during processing your request. Please try again later");
	    	
		} else $this->msg = mobileWrapper::parseValidatorError($Validator->getError());
		
		$this->output();
	}
	
	public function actionresendCode()
	{	
		$verification_type = isset($this->data['verification_type'])?$this->data['verification_type']:'';
		$customer_token = isset($this->data['customer_token'])?$this->data['customer_token']:'';
				
		if(!empty($customer_token)){
			if ($res = mobileWrapper::getCustomerByToken($customer_token,false)){
				$client_id = $res['client_id'];
				
				switch ($verification_type) {
					case "verification_email":
						
						$email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
						if(!empty($email_address)){					
							
							$ok_to_send = true;
							
							$date_now = date('Y-m-d g:i:s a');
							$verify_code_requested = date("Y-m-d g:i:s a",strtotime($res['verify_code_requested']));
							$time_diff=Yii::app()->functions->dateDifference($verify_code_requested,$date_now);
							//dump($time_diff);
							
							if(is_array($time_diff) && count($time_diff)>=1){
								if($time_diff['days']<=0){
									if($time_diff['hours']<=0){
										if($time_diff['minutes']<=1){
											$ok_to_send=false;
										}
									}
								}
							}
		
							if($ok_to_send){
								FunctionsV3::sendEmailVerificationCode($email_address,$res['email_verification_code'],$res);					
								$this->code = 1;
								$this->msg = $this->t("code was sent to your email");
								
								$db=new DbExt();
								$db->updateData("{{client}}",array(
								  'verify_code_requested'=>FunctionsV3::dateNow()
								),'client_id',$client_id);
								unset($db);
							} else {
								$this->msg = $this->t("you are requesting too soon please wait a minute then try again");
								$this->details = $time_diff;
							}					
							
						} else $this->msg = $this->t("invalid email address");
						
						break;
				
					case "verification_mobile":	
					    $contact_phone = isset($this->data['contact_phone'])?$this->data['contact_phone']:'';					    
					    $code = isset($res['mobile_verification_code'])?$res['mobile_verification_code']:'';					    
					    
					    if(!empty($contact_phone)) {
					    	
					    	$ok_to_send = true;
							
							$date_now = date('Y-m-d g:i:s a');
							$verify_code_requested = date("Y-m-d g:i:s a",strtotime($res['verify_code_requested']));
							$time_diff=Yii::app()->functions->dateDifference($verify_code_requested,$date_now);
							//dump($time_diff);
							
							if(is_array($time_diff) && count($time_diff)>=1){
								if($time_diff['days']<=0){
									if($time_diff['hours']<=0){
										if($time_diff['minutes']<=1){
											$ok_to_send=false;
										}
									}
								}
							}
							
							if($ok_to_send){
							    FunctionsV3::sendCustomerSMSVerification($contact_phone,$code);
							    $this->msg = $this->t("code was sent to your mobile");			
							    $this->code = 1;	
							    
							    $db=new DbExt();
								$db->updateData("{{client}}",array(
								  'verify_code_requested'=>FunctionsV3::dateNow()
								),'client_id',$client_id);
								unset($db);
									    
							} else {
								$this->msg = $this->t("you are requesting too soon please wait a minute then try again");
								$this->details = $time_diff;
							}
						    
					    } else $this->msg = $this->t("invalid mobile number");
					    break;
					  
					default:
						$this->msg = $this->t("invalid verification type");
						break;
				}				
			} else $this->msg = $this->t("customer not found");
		} else $this->msg = $this->t("invalid customer token");
		$this->output();
	}
	
	public function actionverifyCode()
	{
		$db=new DbExt();		
		$verification_type = isset($this->data['verification_type'])?$this->data['verification_type']:'';
		$customer_token = isset($this->data['customer_token'])?$this->data['customer_token']:'';
		$code_input = isset($this->data['code'])?$this->data['code']:'';
		
		$this->details = array();
		
		if(!empty($customer_token)){
			if ($res = mobileWrapper::getCustomerByToken($customer_token,false)){				
				$client_id = $res['client_id'];				
				switch ($verification_type) {
					case "verification_email":
						$code = $res['email_verification_code'];
						if ( trim($code)==trim($code_input) ){
							$this->code = 1;
							$this->msg = $this->t("verification ok");							
						} else $this->msg = $this->t("invalid code");
						break;
						
					case "verification_mobile":
						$code = $res['mobile_verification_code'];
						if ( trim($code)==trim($code_input) ){
							$this->code = 1;
							$this->msg = $this->t("verification ok");
						} else $this->msg = $this->t("invalid code");
						break;	
				}
				
				if($this->code==1){
				   $params = array();
				   $token = $res['token']; 
				   if(empty($token)){
				   	  $token = mobileWrapper::generateUniqueToken(15,$client_id);		
				   	  $params['token'] = $token;
				   }				   
				   $this->details['token'] = $token;

				   $params['status']='active';
				   $params['date_modified'] = FunctionsV3::dateNow();
				   $db->updateData("{{client}}", $params,'client_id', $client_id);
				   
				   /*REGISTERED DEVICE*/
				   $this->data['client_id'] = $client_id;
				   mobileWrapper::registeredDevice($this->data);
				   
				}
								
			} else $this->msg = $this->t("customer not found");
		} else $this->msg = $this->t("invalid customer token");
		
		//$this->details['form_next_step'] = isset($this->data['form_next_step'])?$this->data['form_next_step']:'';
		$this->details['next_step'] = isset($this->data['next_step'])?$this->data['next_step']:'';
		
		$this->output();
	}
	
	public function actionsearchMerchant()
	{		
				
		Yii::app()->db->createCommand("SET SQL_BIG_SELECTS=1")->query();
		
		$search_resp = mobileWrapper::searchMode();
		$search_mode = $search_resp['search_mode'];
		$location_mode = $search_resp['location_mode'];		
		
		
		$provider = array(); $provider_mode='';		
	    $provider = mobileWrapper::getMapProvider(); 			
	    MapsWrapperTemp::init($provider);		   
	    $provider_mode = isset($provider['mode'])?$provider['mode']:'driving';		
		
		$home_search_unit_type='';
		
		$search_type = isset($this->data['search_type'])?$this->data['search_type']:'';
		$page_limit = mobileWrapper::paginateLimit();
		$enabled_distance = 1;		
		
		
		$cuisine_name='';
		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
                
		$stmt = ''; $and=''; $where='';
		
		$lat = isset($this->data['lat'])?$this->data['lat']:'';
		$lng = isset($this->data['lng'])?$this->data['lng']:'';
		
		
		$home_search_unit_type=getOptionA('home_search_unit_type');
		
		$multipleField = Yii::app()->functions->multipleField();	
				
		$distance_exp=3959;
		if ($home_search_unit_type=="km"){
			$distance_exp=6371;
		}	
		
		$lat=!empty($lat)?$lat:0;
		$lng=!empty($lng)?$lng:0;
		
		$a = "
		a.merchant_id,
		a.restaurant_name,				
		a.cuisine,
		a.logo,
		a.latitude,
		a.lontitude,
		a.is_sponsored,
		a.delivery_charges,
		a.service,
		a.status,
		a.is_ready,
		a.minimum_order,
		a.minimum_order as minimum_order_raw,
		a.is_featured,
		a.delivery_distance_covered,
		a.distance_unit,
		a.delivery_estimation,
		concat( a.street,' ', a.city, ' ', a.state, ' ',a.post_code )  as address,		
		";
		
		$query_distance="
		( $distance_exp * acos( cos( radians($lat) ) * cos( radians( latitude ) ) 
				* cos( radians( lontitude ) - radians($lng) ) 
				+ sin( radians($lat) ) * sin( radians( latitude ) ) ) ) 
				AS distance		
		";
				
		$sort_asc_desc = isset($this->data['sort_asc_desc'])?$this->data['sort_asc_desc']:'ASC';		
        $sort_asc_desc = mobileWrapper::validateSort($sort_asc_desc);
				
        
        $opening_hours = true;
        /*if(Yii::app()->db->schema->getTable("{{opening_hours}}")){
          $opening_hours = true;
        }*/
        
        $view_cuisine_merchant = true;
        /*if(Yii::app()->db->schema->getTable("{{view_cuisine_merchant}}")){
          $view_cuisine_merchant = true;
        }*/
        
        $sort_by = isset($this->data['sort_by'])?$this->data['sort_by']:'';		
		$sortby_selected = $this->t("Distance");			
        
        if(!$opening_hours){
        	$sort = "ORDER BY is_sponsored DESC, distance $sort_asc_desc";	
        } else $sort = "ORDER BY merchant_open_status DESC, is_sponsored DESC, distance $sort_asc_desc";				

              	
		if($search_mode=="location"){
			$sort = "ORDER BY merchant_open_status DESC,is_sponsored DESC, restaurant_name $sort_asc_desc";		
		}			
									
		if(!empty($sort_by)){			
			$sort_resp = mobileWrapper::validateSortRestoList($sort_by);			
			$sort_by = $sort_resp['key'];
			$sortby_selected = $sort_resp['name'];
			$sort = "ORDER BY ".stripslashes($sort_by)." $sort_asc_desc";								
		} 		
				
		/*FILTER*/
		$filter = '';
		if(isset($this->data['filter_delivery_fee'])){
			if($this->data['filter_delivery_fee']>=1){
			   $filter.=" AND a.delivery_charges <= 0 ";			   
			   if($search_mode=="address"){  			  
				   $filter.=" AND a.merchant_id NOT IN (
				       select f.merchant_id from {{shipping_rate}} f
				       where f.merchant_id = a.merchant_id      	
				       and distance_from<= distance and distance_to>=distance
				       and  f.merchant_id  = (
				          select merchant_id from {{option}}
				          where merchant_id = f.merchant_id and option_name='shipping_enabled' 
				          and option_value='2'
				       )		       
				   )";			   
			   } else {			   	   
			   	   $location_filter = LocationWrapper::getLocationFilter($location_mode);			   	   
			   	   $and_filter = '';	
			   	   if(is_array($location_filter) && count($location_filter)>=1){
			   	   	  foreach ($location_filter as $location_filter_val) {			   	   	  	 
			   	   	  	 $xf = isset($this->data[$location_filter_val])?$this->data[$location_filter_val]:'';
			   	   	  	 $and_filter.=" AND $location_filter_val =".q($xf)." ";
			   	   	  }			   	   	  
			   	   }			   
			   	   $filter.="
			   	     AND a.merchant_id  IN (
			   	       select f.merchant_id from {{view_location_rate}} f
			   	       where f.merchant_id = a.merchant_id and f.fee<=0
			   	       $and_filter
			   	     )
			   	   ";			   	   
			   }			
			}
		}	
		if(isset($this->data['filter_services'])){
			if(is_array($this->data['filter_services']) && count($this->data['filter_services'])>=1){
				$filter_services_stmt='';
				foreach ($this->data['filter_services'] as $filter_services) {					
					switch ($filter_services) {
						case "delivery":							
						    $filter_services_stmt .= " service='1' OR service='2' OR service='4' OR service='5' OR";
							break;
							
					    case "pickup":
					    	$filter_services_stmt .= " service='1' OR service='3' OR service='4' OR service='6' OR";
					   	   break;
					   	   
						case "dinein":
							$filter_services_stmt .= " service='4' OR service='5' OR service='6' OR service='7' OR";
					   	   break;							
					}
				}
				$filter_services_stmt= substr($filter_services_stmt,0,-3);				
				$filter.=" AND ($filter_services_stmt) ";
			}
		}
		
		if(isset($this->data['filter_cuisine'])){
			if(is_array($this->data['filter_cuisine']) && count($this->data['filter_cuisine'])>=1){
				$filter_cuisine_stmt='';
				foreach ($this->data['filter_cuisine'] as $filter_cuisine) {
					$filter_cuisine_stmt.=" cuisine  LIKE ".FunctionsV3::q("%$filter_cuisine%")." OR";
				}
				$filter_cuisine_stmt= substr($filter_cuisine_stmt,0,-3);				
				$filter.=" AND ($filter_cuisine_stmt)";
			}
		}
		
		if(isset($this->data['filter_minimum'])){
			if($this->data['filter_minimum']>=1){
				$filter.=" AND CAST(minimum_order as SIGNED) <=".FunctionsV3::q($this->data['filter_minimum'])." ";
			}
		}
		
		/*FILTER PROMOS*/
		if(isset($this->data['filter_promos'])){			
			if(!empty($this->data['filter_promos']) && $search_type!="special_Offers" ){
				if($this->data['filter_promos']=="offer"){					
					$filter.=" AND a.merchant_id IN (
					   SELECT merchant_id FROM
						{{offers}}
						WHERE
						status in ('publish','published')
						AND
						now() >= valid_from and now() <= valid_to
						AND merchant_id = a.merchant_id							
					)";
				}			
			}		
					
			if($this->data['filter_promos']=="voucher"){
			   $filter.=" AND a.merchant_id IN (
					   SELECT merchant_id FROM
						{{voucher_new}}
						WHERE
						status in ('publish','published')
						AND
						now() <= expiration
						AND merchant_id = a.merchant_id								
					)";
			}		
		}	
		
		$and.=$filter;
		/*END FILTER*/
				
		
		$and.="  AND a.status='active'  AND a.is_ready ='2' ";
		
		$open_query='';
		if($opening_hours){
			$time_now = date("H:i");
			$open_day = strtolower(date("l"));
			$open_query.="
			,(
				select count(*) from
				{{opening_hours}}
				where
				merchant_id = a.merchant_id
				and
				day=".q($open_day)."
				and
				status = 'open'
				and 
				
				(
				CAST(".q($time_now)." AS TIME)
				BETWEEN CAST(start_time AS TIME) and CAST(end_time AS TIME)
				
				or
				
				CAST(".q($time_now)." AS TIME)
				BETWEEN CAST(start_time_pm AS TIME) and CAST(end_time_pm AS TIME)
				
				)
				
			) as merchant_open_status
			";
		}
				
		$state_id=''; $city_id =''; $area_id=''; 
		$sub_query_filter=''; $and_location=''; $and_location_fee='';
		
		$state_id = isset($this->data['state_id'])?$this->data['state_id']:-1;
		$city_id = isset($this->data['city_id'])?$this->data['city_id']:-1;
	    $area_id = isset($this->data['area_id'])?$this->data['area_id']:-1;
	    $postal_code = isset($this->data['postal_code'])?$this->data['postal_code']:-1;	    
	    $current_page = isset($this->data['current_page'])?$this->data['current_page']:'';	  
	    
	    $where = "HAVING distance < a.delivery_distance_covered";
									    
	    if($search_mode!="location"){
	    	// SEARCH BY ADDRESS
			switch ($search_type) {
				case "byLatLong":				
				    if(isset($this->data['map_page'])){
				       if($this->data['map_page']==1){
				       	  $page_limit=1000;
				       }			    
				    }					    		
					$stmt="
					SELECT SQL_CALC_FOUND_ROWS 		
					$a						
					$query_distance
					$open_query
					FROM {{view_merchant}} a 
					$where		
					$and
				 	$sort
					LIMIT $page,$page_limit
					";													
					break;
					
				case "featuredMerchant":				
					$stmt="
				    SELECT SQL_CALC_FOUND_ROWS 
					$a				
					$query_distance
					$open_query
					FROM {{view_merchant}} a
					$where
					AND is_featured='2'				
					$and
					$sort
					LIMIT $page,$page_limit
				   ";			   			      
				   break;
			
				case "allMerchant":  
							  			  			  
				  $stmt="
				    SELECT SQL_CALC_FOUND_ROWS 
					$a				
					$query_distance
				    $open_query
					FROM {{view_merchant}} a
					$where				
					$and
					$sort
					LIMIT $page,$page_limit
				   ";			  			  			 
				   break;
	
				case "special_Offers":
									
				   $stmt="
				    SELECT SQL_CALC_FOUND_ROWS 
					$a				
	                $query_distance		
	                $open_query			
					FROM {{view_merchant}} a
					$where				
					AND 
					(				
					   merchant_id IN (
						    SELECT merchant_id FROM
							{{offers}}
							WHERE
							status in ('publish','published')
							AND
							now() >= valid_from and now() <= valid_to
							AND merchant_id = a.merchant_id					
						)					
						OR					
						merchant_id IN (
						    SELECT merchant_id FROM
							{{voucher_new}}
							WHERE
							status in ('publish','published')													
							AND merchant_id = a.merchant_id			
							AND now() <= expiration
						)									
					)
					$and			    
					$sort
					LIMIT $page,$page_limit
				   ";
				   
				   break;
				   
				case "byCuisine":   
				    $cuisine_id = isset($this->data['cuisine_id'])?$this->data['cuisine_id']:'-1'; 
				    $stmt="
					    SELECT SQL_CALC_FOUND_ROWS 
						$a					
		                $query_distance			
		                $open_query					
						FROM {{view_merchant}} a
						$where									
						AND a.cuisine LIKE ".FunctionsV3::q('%"'.$cuisine_id.'"%')."
						$and
						$sort
						LIMIT $page,$page_limit
					"; 			    
				break;
				
				case "favorites":
									
					$stmt="
				    SELECT SQL_CALC_FOUND_ROWS 
					$a				
					$query_distance
					$open_query
					FROM {{view_merchant}} a
					$where						
					$and
					$sort
					LIMIT $page,$page_limit
				   ";																						
					break;
					
				case "ByTag":				
				    try {
				    	$banner_id = isset($this->data['banner_id'])?$this->data['banner_id']:0;
				    	$resp_banner = mobileWrapper::getBannerByID($banner_id);
				    	$tag_id = !empty($resp_banner['tag_id'])?json_decode($resp_banner['tag_id'],true):'';
				    	if(is_array($tag_id) && count($tag_id)>=1){
				    		$and.=" AND a.merchant_id IN (
				    		  select merchant_id from {{option}}
				    		  where merchant_id = a.merchant_id
				    		  and option_name='tags' 
				    		  and option_value IN (".implode(",",$tag_id).")
				    		)";
				    	}				    
				    } catch (Exception $e) {
				    	//echo $e->getMessage();				    	
				    }
					$stmt="
				    SELECT SQL_CALC_FOUND_ROWS 
					$a				
					$query_distance
					$open_query
					FROM {{view_merchant}} a
					$where						
					$and					
					$sort
					LIMIT $page,$page_limit
				    ";								
					break;
				
				default:			
					break;
			}	
	    } else {
	    	// SEARCH BY LOCATION QUERY	    	
	    	
            $and_location.= LocationWrapper::queryLocation((integer)$location_mode,array(
			  'state_id'=>$state_id,
			  'city_id'=>$city_id,
			  'area_id'=>$area_id,
			  'postal_code'=>$postal_code,
			));
			
			switch ( (integer) $location_mode) {
				case 1:
					$and_location_fee ="
					  and city_id=".FunctionsV3::q($city_id)."
					  and area_id =".FunctionsV3::q($area_id)."
					";  					
				break;
				
				case 2:
					$and_location_fee ="
					  and state_id=".FunctionsV3::q($state_id)."
					  and city_id=".FunctionsV3::q($city_id)."
					";
					break;
				case 3:					
				    $and_location_fee ="
					  and city_id=".FunctionsV3::q($city_id)."
					  and postal_code=".FunctionsV3::q($postal_code)."
					";
				    break;
				break;
			}
									
			if($search_type == "featuredMerchant"){
				$and.="AND is_featured='2'";
				
			} elseif ( $search_type == "special_Offers"){
				$and.=" 
				AND 
				(				
				   merchant_id IN (
					    SELECT merchant_id FROM
						{{offers}}
						WHERE
						status in ('publish','published')
						AND
						now() >= valid_from and now() <= valid_to
						AND merchant_id = a.merchant_id					
					)					
					OR					
					merchant_id IN (
					    SELECT merchant_id FROM
						{{voucher_new}}
						WHERE
						status in ('publish','published')													
						AND merchant_id = a.merchant_id			
						AND now() <= expiration
					)									
				)
				";
			} elseif ( $search_type == "favorites"){
				
				$client_id ='';
				$user_token = isset($this->data['user_token'])?$this->data['user_token']:'';
				if($res_customer = mobileWrapper::getCustomerByToken($user_token)){
					$client_id = $res_customer['client_id'];
				} else $client_id=-1;			
				$and.="
				AND a.merchant_id IN (
				   select merchant_id from {{favorites}} 
				   where
				   merchant_id = a.merchant_id
				   and
				   client_id=".FunctionsV3::q($client_id)."
				)
				";		
			} elseif ( $search_type == "byCuisine"){
				$cuisine_id = isset($this->data['cuisine_id'])?$this->data['cuisine_id']:'-1'; 
				$and.="
				AND a.cuisine LIKE ".FunctionsV3::q('%"'.$cuisine_id.'"%')."
				";
			}	    
	    		    		   	    
	    	$stmt="
			  SELECT SQL_CALC_FOUND_ROWS a.*,
			  $a	          
	          (
	           select fee
	           from {{view_location_rate}}
	           where merchant_id = a.merchant_id
	           $and_location_fee
	           limit 0,1
	          ) as location_fee
	          $open_query
	          
	          FROM {{view_merchant}} a
			  WHERE 1			   
			  $and_location	          
	          $and
			  $sort
	          LIMIT $page,$page_limit
			";					    	
	    }	
		
		$page_action =  isset($this->data['page_action'])?$this->data['page_action']:'';
		$paginate_total = 0;
		
		$search_options = mobileWrapper::getDataSearchOptions();				
			
		if(!empty($stmt)){
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
					
				//dump($res);die();
				
				$total_records=0;
				$stmtc="SELECT FOUND_ROWS() as total_records";		 		
		 		if($resp = Yii::app()->db->createCommand($stmtc)->queryRow()){		 		   
		 		   $total_records=$resp['total_records'];
		 		}		
		 		
				$this->code = 1;				
							
				switch ($search_type) {
					case "allMerchant":
					case "byCuisine":
						$this->msg = mobileWrapper::t("[total] Restaurant Found",array(
						  '[total]'=>$total_records
						));						
						break;
						
					case "featuredMerchant":	
					   $this->msg = mobileWrapper::t("[total] Featured Restaurant",array(
						  '[total]'=>$total_records
						));						
					break;
					
					case "special_Offers":	
					  $this->msg = mobileWrapper::t("[total] Special Offers",array(
						  '[total]'=>$total_records
						));						
					break;
				
					case "favorites":
						$this->msg = mobileWrapper::t("[total] Favorites",array(
						  '[total]'=>$total_records
						));						
						break;
					
					default:
						$this->msg = mobileWrapper::t("[total] Nearby Restaurant",array(
						  '[total]'=>$total_records
						));						
						break;
				}
								
				$paginate_total = ceil( $total_records / $page_limit );
				
				$data = array();
				
				$res = Yii::app()->request->stripSlashes($res);
								
				foreach ($res as $val) {
					$merchant_id = $val['merchant_id'];					
					$distance = 0;
					$unit = isset($val['distance_unit'])?$val['distance_unit']:'';
					$unit2 = mobileWrapper::standardUnit($unit);

					$pretty_unit = MapsWrapperTemp::prettyUnit($unit);
										
					if(in_array('open_tag',(array)$search_options)){
						$status = mobileWrapper::merchantStatus($merchant_id);
						$val['open_status_raw']=$status;
						$val['open_status']=mt($status);
					}
					$val['background_url'] = mobileWrapper::getMerchantBackground($merchant_id);
					$val['logo']=mobileWrapper::getImage($val['logo']);
										
					if(in_array('cuisine',(array)$search_options) && $view_cuisine_merchant ){						   
					   $val['cuisine'] = mobileWrapper::displayCuisine($merchant_id,$multipleField);
					} else unset($val['cuisine']);
					
					if(in_array('review',(array)$search_options)){
						$ratings = Yii::app()->functions->getRatings($merchant_id);
						$ratings['review_count'] = mobileWrapper::t("[count] reviews",array(
						  '[count]'=>$ratings['votes']
						));
						$val['rating']=$ratings;
					}
					
					if($val['is_sponsored']==2){
						$val['sponsored'] =  $this->t("Sponsored");
					}
					
					if(in_array('delivery_estimation',(array)$search_options)){
						if(!empty($val['delivery_estimation'])){
							$val['delivery_estimation'] =  mobileWrapper::t("Delivery Est: [estimation]",array(
							 '[estimation]'=>mt($val['delivery_estimation'])
						    ));
						}
					} else $val['delivery_estimation']='';
					
					if($search_mode=="address"){
						if(in_array('delivery_distance',(array)$search_options)){
							if($val['delivery_distance_covered']>0){
								$val['delivery_distance'] = mobileWrapper::t("Delivery Distance: [delivery_distance]",array(
								 '[delivery_distance]'=>$val['delivery_distance_covered']." $pretty_unit"
								));
							}						
						}
					}
					
					
					if(in_array('offers',(array)$search_options)){
						$offers=array();
						if(method_exists('FunctionsV3','getOffersByMerchantNew')){
							if ($offer=mobileWrapper::getOffersByMerchantNew($merchant_id)){
								foreach ($offer as $offer_val) {
									$offers[]=$offer_val;
								}			    				
							}			    		
						}		
						$free_delivery_above=getOption($merchant_id,'free_delivery_above_price');
						if($free_delivery_above>0.001){
						   $free_above = mobileWrapper::t("Free Delivery On Orders Over [subtotal]",array(
							 '[subtotal]'=>$free_delivery_above
						   ));
						   $offers[] = array(
							 'raw'=>mt("Free[fee]",array('[fee]'=>FunctionsV3::prettyPrice($free_delivery_above))),
							 'full'=>$free_above
						   );
						}			    	
						$val['offers']=$offers;	    	
					}
					
					if(in_array('voucher',(array)$search_options)){
						$vouchers = array();
						if (method_exists("FunctionsV3","merchantActiveVoucher")){
							if ( $voucher=FunctionsV3::merchantActiveVoucher($merchant_id)){
								foreach ($voucher as $voucher_val) {
									if ( $voucher_val['voucher_type']=="fixed amount"){
										$v_amount=FunctionsV3::prettyPrice($voucher_val['amount']);
									} else $v_amount=number_format( ($voucher_val['amount']/100)*100 )."%";
									
									$vouchers[] = mt("[discount] off | Use coupon [code]",array(
									  '[discount]'=>$v_amount,
									  '[code]'=>$voucher_val['voucher_name']
									));
								}			    				
								$val['vouchers']=$vouchers;	 
							}
						}
					}
					
					if(in_array('services',(array)$search_options)){
						$services_list = array();
						if($services = mobileWrapper::getMerchantServicesList($val['service'])){
							foreach ($services as $services_val) {
								$services_list[]=$services_val;
							}
							$val['services']=$services_list;
						}			    	
					}

					
					if(in_array('payment_option',(array)$search_options)){
						$paymet_method_list=array();
						if($paymet_method = FunctionsV3::getMerchantPaymentListNew($merchant_id)){			    			
							if(array_key_exists('cod',$paymet_method)){
								$paymet_method_list[]=mobileWrapper::getImage("icon-cod.png","icon-cod.png");
							}			
							if(array_key_exists('obd',$paymet_method)){
								$paymet_method_list[]=mobileWrapper::getImage("icon-obd.png","icon-obd.png");
							}			    		
							if(array_key_exists('ocr',$paymet_method)){
								$paymet_method_list[]=mobileWrapper::getImage("icon-ocr.png","icon-ocr.png");
							}						    			
							$val['paymet_method_icon']=$paymet_method_list;
						}			    	
					}					

					if(!in_array('address',(array)$search_options)){
						$val['address']='';
					}
						
					if($current_page!="tabbar"):					
					if($search_mode=="address"){	
						/*GET DISTANCE*/													
						try {							
							$params = array(
							  'merchant_id'=>$merchant_id,
							  'provider'=>$provider,
							  'from_lat'=>$val['latitude'],
							  'from_lng'=>$val['lontitude'],
							  'to_lat'=>$lat,
							  'to_lng'=>$lng,
							  'delivery_charges'=>$val['delivery_charges'],
							  'unit'=>$unit,
							  'delivery_distance_covered'=>$val['delivery_distance_covered'],
							  'order_subtotal'=>0,
							  'minimum_order'=>$val['minimum_order_raw']
							);			
																										
							$resp_distance = CheckoutWrapperTemp::getDeliveryDetails($params);							
															
							$distance = $resp_distance['distance'];			
											
							if(in_array('distace',(array)$search_options)){
								$val['distance_plot'] = mobileWrapper::t("Distance : [distance]",array(
					 			   '[distance]'=>$resp_distance['pretty_distance']
					 			));
							}
							
				 			if(in_array('minimum_order',(array)$search_options)){
					 			$val['minimum_order_raw']=$resp_distance['min_order'];
					 			$val['minimum_order'] = mobileWrapper::t("Minimum Order: [min]", array(
								  '[min]'=>FunctionsV3::prettyPrice($resp_distance['min_order'])
								));		
				 			} else {
				 			    unset($val['minimum_order']);
                                unset($val['minimum_order_raw']);
				 			}							
							
				 			if(in_array('delivery_fee',(array)$search_options)){
								if($resp_distance['delivery_fee']>0){
									$val['delivery_fee'] = mobileWrapper::t("Delivery Fee: [fee]",array(
				                	 '[fee]'=>FunctionsV3::prettyPrice($resp_distance['delivery_fee'])
				                	));
								}			
				 			}				
				 			
						} catch (Exception $e) {			 							
 							$val['distance_plot'] = Yii::t("mobile2","Distance : [error]",array(
 							 '[error]'=>$e->getMessage()
 							));
 						}						
					} else {
						  //dump("SEARCH BY LOCATIONx");		
						  if(in_array('minimum_order',(array)$search_options)){
							  $val['minimum_order'] = mobileWrapper::t("Minimum Order: [min]", array(
							  '[min]'=>FunctionsV3::prettyPrice($val['minimum_order_raw'])
							  ));		 
						  }
						  if(in_array('delivery_fee',(array)$search_options)){
							  $val['delivery_fee'] = mobileWrapper::t("Delivery Fee: [fee]",array(
			                	 '[fee]'=>FunctionsV3::prettyPrice($val['location_fee'])
			                  ));
						  }
					}				
					endif; 
					/* END IF TABBAR*/
										
					$data[]=$val;
				}
				
				//die();
								 			 	
				$this->details = array(				  
				  'search_type'=>$search_type,
				  'total_records'=>$total_records,		
				  'sortby_selected'=>$sortby_selected,
				  'page_action'=>$page_action,
				  'paginate_total'=>$paginate_total,
				  'map_page'=>isset($this->data['map_page'])?$this->data['map_page']:'',
				  'refresh_home'=>isset($this->data['refresh_home'])?$this->data['refresh_home']:'',
				  'list'=>$data,
				);
			} else {
				if($search_type=="byLatLong"){
					$this->msg = $this->t("0 restaurant found");
				} else $this->msg = $this->t("No results");
				
				$this->details = array(
				  'search_type'=>$search_type,
				  'sortby_selected'=>$sortby_selected,
				  'page_action'=>$page_action,
				  'paginate_total'=>$paginate_total,
				  'refresh_home'=>isset($this->data['refresh_home'])?$this->data['refresh_home']:''
				);
			}
		} else {
		     $this->msg = $this->t("invalid query");
		     $this->details = array(
				 'search_type'=>$search_type,
				 'sortby_selected'=>$sortby_selected,
				 'page_action'=>$page_action,
				 'paginate_total'=>$paginate_total,
				 'refresh_home'=>isset($this->data['refresh_home'])?$this->data['refresh_home']:''
			);
		}
		$this->output();
	}
	
	public function actioncustomerLogin()
	{		
		$this->details = array();
		$user_mobile = isset($this->data['user_mobile'])?trim($this->data['user_mobile']):'';
		$password = isset($this->data['password'])?trim($this->data['password']):'';
		if(!empty($user_mobile) && !empty($password)){
							
    	    $res=array();
    	    if ($res = mobileWrapper::loginByEmail($user_mobile,$password)){    	    	    	    	
    	    } else {
    	    	$res = mobileWrapper::loginByMobile($user_mobile,$password);    	    	    	    	
    	    }
    	    
    	    if(is_array($res) && count($res)>=1){
    	    	
    	    	if ( FunctionsK::emailBlockedCheck($res['email_address'])){
	    		   $this->msg = $this->t("sorry but your email address is blocked by website admin");
	    		   $this->output();
	    	    }	  			
	    	    
	    	    if ( FunctionsK::mobileBlockedCheck($res['contact_phone'])){
	    		   $this->msg = $this->t("Sorry but your mobile number is blocked by website admin");
	    		   $this->output();
	    	    }	  			
    	    	
    	    	$client_info = array(
    	    	  'token'=>$res['token'],
    	    	  'first_name'=>$res['first_name'],
    	    	  'last_name'=>$res['last_name'],
    	    	  'email_address'=>$res['email_address'],
    	    	  'status'=>$res['status'],
    	    	  'avatar'=>$res['avatar'],
    	    	);
    	    	
    	    	switch ($res['status']) {
    	    		case "active":
    	    			$this->code = 1;
    	    			$this->msg = "OK";
    	    			$this->details['client_info']=$client_info;
    	    			
    	    			/*REGISTERED DEVICE*/
						if(is_array($res) && count((array)$res)>=1){
							$this->data['client_id'] = $res['client_id'];							
						}							
						mobileWrapper::registeredDevice($this->data);
    	    			break;
    	    	
    	    		case "pending":    	    			
    	    			$this->msg = $this->t("Your account is not active");    
    	    				    			    	    		    	    			
    	    			if (strlen($res['mobile_verification_code'])>=2){
    	    				$this->details['next_step'] = 'verification_mobile';
    	    		    } elseif (strlen($res['email_verification_code'])>=2 ){	 	    	
    	    		    	$this->details['next_step'] = 'verification_email';
    	    		    }
    	    			
				    	if(isset($this->details['next_step'])){
				    		$this->details['contact_phone']=$res['contact_phone'];
				    		$this->details['customer_token']=$res['token'];
				    		$this->details['email_address']=$res['email_address'];
				    	}
				    					    					    					    
    	    		    break;
    	    		    
    	    		default:
    	    			$this->msg = mobileWrapper::t("login failed. your account status is [status]",array(
    	    			  '[status]'=>$this->t($res['status'])
    	    			));
    	    			break;
    	    	}
    	    } else $this->msg = $this->t("login failed. either username or password is incorrect");
    	    
		} else $this->msg = $this->t("either username or password is empty");
		$this->output();
	}		
	
	public function actioncuisineList()
	{		
		$search_resp = mobileWrapper::searchMode();
		$search_mode = $search_resp['search_mode'];
		$location_mode = $search_resp['location_mode'];		
		
		
		$page_limit = mobileWrapper::paginateLimit();
		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
                
        
        $sort_by = isset($this->data['sort_by'])?$this->data['sort_by']:'ASC';		
        $sort_by = mobileWrapper::validateSort($sort_by);
        
        $sort_fields = isset($this->data['sort_fields'])?$this->data['sort_fields']:'';       
        if(empty($sort_fields)){
        	$sort_fields='sequence';
        }        
        $sort_resp = mobileWrapper::prettySortCuisine($sort_fields);
        $sortby_selected = $sort_resp['name'];
        $sort_fields = $sort_resp['key'];    
        
        
        $lat = isset($this->data['lat'])?$this->data['lat']:'';
		$lng = isset($this->data['lng'])?$this->data['lng']:'';	
		
		
		$unit=getOptionA('home_search_unit_type');
		$distance_exp=3959;
		if ($unit=="km"){
			$distance_exp=6371;
		}	
						
        $query_distance="
		( $distance_exp * acos( cos( radians($lat) ) * cos( radians( latitude ) ) 
				* cos( radians( lontitude ) - radians($lng) ) 
				+ sin( radians($lat) ) * sin( radians( latitude ) ) ) ) 
				AS distance		
		";
        
        $and='';		
        $cuisine_name='';
        if (isset($this->data['cuisine_name'])){
        	$cuisine_name = trim($this->data['cuisine_name']);
        	$and.=" AND cuisine_name LIKE ".FunctionsV3::q("%".$cuisine_name."%")."";
        }
        
        $lists = array();
        $lists = array();
		$total_records = 0;
		$page_action = isset($this->data['page_action'])?$this->data['page_action']:'';
                
			
        $stmt="
        select SQL_CALC_FOUND_ROWS 
        a.cuisine_id, a.cuisine_name, a.featured_image, a.cuisine_name_trans
        from {{cuisine}} a                    
        where a.status  ='publish' 
        $and
        ORDER BY $sort_fields $sort_by		
        LIMIT $page , $page_limit
        ";        
        if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
        	
        	$total_records=0;		    
	 		if($resp = Yii::app()->db->createCommand("SELECT FOUND_ROWS() as total_records")->queryAll()){	 			
	 			$total_records=$resp[0]['total_records'];
	 		}	
        	
        	foreach ($res as $val) {
        		
        		if($search_mode=="location"){        			
        			$total= mobileWrapper::getTotalCuisineByLocation($val['cuisine_id'],$location_mode,array(
        			  'state_id'=>isset($this->data['state_id'])?$this->data['state_id']:-1,
        			  'city_id'=>isset($this->data['city_id'])?$this->data['city_id']:-1,
        			  'area_id'=>isset($this->data['area_id'])?$this->data['area_id']:-1,
        			  'postal_code'=>isset($this->data['postal_code'])?$this->data['postal_code']:-1,
        			));        			
        		} else $total = mobileWrapper::getTotalCuisine($val['cuisine_id'],$query_distance);        		
        		
        		$cuisine_json['cuisine_name_trans']=!empty($val['cuisine_name_trans'])?
				json_decode($val['cuisine_name_trans'],true):'';
				$cuisine_name_trans = qTranslate($val['cuisine_name'],'cuisine_name',$cuisine_json);
				$cuisine_name_trans = mobileWrapper::highlight_word($cuisine_name_trans,$cuisine_name);
				
				$lists[]=array(
				 'id'=>$val['cuisine_id'],
				 'name'=>$cuisine_name_trans,
				 'featured_image'=>mobileWrapper::getImage($val['featured_image'],'default_cuisine.png'),
				 'total_merchant'=>mobileWrapper::t("[total] restaurant",array('[total]'=>$total))
				);
        	}
        	
        	$this->msg = mobileWrapper::t("[found] cuisine found",array(
			  '[found]'=>$total_records
			));
			
			$paginate_total = ceil( $total_records / $page_limit );
			
			$this->code = 1; 
			$this->details = array(
			 'total'=>$total_records,
			 'sortby_selected'=>mt($sortby_selected),
			 'page_action'=>$page_action,
			 'paginate_total'=>$paginate_total,
			 'list'=>$lists
			);
			
        } else {
        	$this->code = 1; 
        	$this->msg = $this->t("No results");
			$this->details = array(
			 'total'=>$total_records,
			 'sortby_selected'=>$sortby_selected,
			 'page_action'=>$page_action,
			 'paginate_total'=>0,
			 'list'=>''
			);
        }     
        $this->output();   
	}
	
	public function actionsearchByMerchantName()
	{
		$merchant_name = isset($this->data['merchant_name'])?$this->data['merchant_name']:'';
		if(!empty($merchant_name)){			
			
			
			$lat = isset($this->data['lat'])?$this->data['lat']:'';
			$lng = isset($this->data['lng'])?$this->data['lng']:'';		
			
			$home_search_unit_type=getOptionA('home_search_unit_type');
				$distance_exp=3959;
				if ($home_search_unit_type=="km"){
					$distance_exp=6371;
				}	
				
				$query_distance="
				( $distance_exp * acos( cos( radians($lat) ) * cos( radians( latitude ) ) 
						* cos( radians( lontitude ) - radians($lng) ) 
						+ sin( radians($lat) ) * sin( radians( latitude ) ) ) ) 
						AS distance		
				";			
			
			$stmt="
			SELECT 
			merchant_id,
			restaurant_name,
			cuisine, logo,
			concat(street,' ',city,' ',state ) as address,
			country_code,
			delivery_distance_covered,
			status,is_ready,
			$query_distance
			
			FROM {{merchant}}			
			HAVING distance < delivery_distance_covered
			AND restaurant_name LIKE ".FunctionsV3::q("%".$merchant_name."%")."
			AND status='active'
			AND is_ready = '2'
			LIMIT 0,10
			";		
						
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				$data = array();
				foreach ($res as $val) {	
					$merchant_id = $val['merchant_id'];			
					
					$val['restaurant_name']= mobileWrapper::highlight_word( clearString($val['restaurant_name']) ,$merchant_name);
		 			$val['cuisine']=FunctionsV3::displayCuisine($val['cuisine']);
		 			$val['rating']=$ratings=Yii::app()->functions->getRatings($merchant_id); 				
					$val['logo']=mobileWrapper::getImage($val['logo']);
					$val['address']= $val['address']." ".Yii::app()->functions->countryCodeToFull($val['country_code']);
					$data[] = $val;
				}
				
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				 'list'=>$data
				);
								
			} else $this->msg = $this->t("No results");
		} else $this->msg = $this->t("merchant name is empty");
		$this->output();
	}
	
	public function actionsearchByCuisine()
	{
		$this->actioncuisineList();
	}
	
	public function actiongetRestaurantInfo()
	{
		
		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';		
		$client_id = $this->checkToken();
		
		$this->setMerchantTimezone();
		
		if($merchant_id>0){
			
			$show_delivery_fee = false;
			$options = mobileWrapper::getDataSearchOptions();						
			if(in_array('delivery_fee',$options)){
				$show_delivery_fee = false;
			}		
			
			$lat = isset($this->data['lat'])?$this->data['lat']:'';
			$lng = isset($this->data['lng'])?$this->data['lng']:'';	
			
			if ($res = FunctionsV3::getMerchantInfo($merchant_id)){
										
				$this->code = 1;
				$this->msg="ok";
				
				$data['merchant_id']=$res['merchant_id'];
				$data['restaurant_name']=clearString($res['restaurant_name']);
				$data['complete_address']=clearString($res['complete_address']);
				
				$data['latitude']=$res['latitude'];
				$data['lontitude']=$res['lontitude'];
				
				$data['cuisine']=FunctionsV3::displayCuisine($res['cuisine']);
				$data['logo']=mobileWrapper::getImage($res['logo']);
				$data['background_url'] = mobileWrapper::getMerchantBackground($merchant_id);
				
                $status = mobileWrapper::merchantStatus($merchant_id);
                
                $data['close_message']='';
		 		if($status=="close"){		 			
		 			$date_close= FunctionsV3::prettyDate(date('c'))." ".FunctionsV3::prettyTime(date('c'));
		 			$data['close_message']= Yii::t("mobile2","Sorry but we are closed on [date_close]. Please check merchant opening hours.",array(
    		  '[date_close]'=>$date_close
    		));
		 		}
                
		 		$data['status_raw']=$status;
		 		$data['status']=mt($status);	
		 				 		
		 		
		 		$data['gallery']=2;
		 		$enabled_menu_carousel = getOptionA('mobile2_enabled_menu_carousel');		 		
		 		$banner_enabled = getOption($merchant_id,'banner_enabled');		 		
		 		if($enabled_menu_carousel==1 && $banner_enabled==1){		 			
		 		    $data['gallery']=mobileWrapper::getMerchantBanner($merchant_id);
		 		    
		 		}		 		
		 						
				$ratings=Yii::app()->functions->getRatings($merchant_id); 	
				$data['rating']=$ratings;				
				$ratings['review_count'] = mobileWrapper::t("[count] reviews",array(
	 			  '[count]'=>$ratings['votes']
	 			));
	 			$data['rating']=$ratings;
	 				 			
	 			$data['added_as_favorite'] = mobileWrapper::getFavorites($client_id, $merchant_id);
	 							
				if($offers=mobileWrapper::getOffersByMerchantNew($merchant_id)){
	 				$data['offers']=$offers;
	 			}
	 			
		    	if($res['is_sponsored']==2){
		    		$data['sponsored'] =  $this->t("Sponsored");
		    	}
		    			    	
		    	//dump($ratings);
		    	$data['tab_menu_enabled']=1;
		    	$data['tab_menu']=mobileWrapper::getRestoTabMenu($merchant_id , $ratings);		
		    	
		    	$data['share_options']=array(
		    	  'message'=>mt("Find this restaurant on [website_name] | [merchant_address]",array(
		    	    '[website_name]'=>getOptionA('website_title'),
		    	    '[merchant_address]'=>$res['complete_address']
		    	  )),
		    	  'url'=>websiteUrl()."/menu-".$res['restaurant_slug'],
		    	  'subject'=>$res['restaurant_name'],
		    	  'files'=>''
		    	);
		    	
		    	
		    	$data['delivery_fee'] = '';
				if($show_delivery_fee){
					try {						
						$provider = mobileWrapper::getMapProvider();											
						$params_fee =  array(
						  'merchant_id'=>$res['merchant_id'],
						  'provider'=>$provider,
						  'from_lat'=>isset($res['latitude'])?$res['latitude']:0,
						  'from_lng'=>isset($res['lontitude'])?$res['lontitude']:0,
						  'to_lat'=>$lat,
						  'to_lng'=>$lng,
						  'delivery_charges'=>isset($res['delivery_charges'])?$res['delivery_charges']:0,
						  'unit'=>isset($res['distance_unit'])?$res['distance_unit']:'',
						  'delivery_distance_covered'=>isset($res['delivery_distance_covered'])?$res['delivery_distance_covered']:'',
						  'order_subtotal'=>0,
						  'minimum_order'=>isset($res['minimum_order'])?$res['minimum_order']:0
						);						
						$resp_fee = CheckoutWrapperTemp::getDeliveryDetails($params_fee);
						$data['delivery_fee'] =  mt("Delivery charges: [fee]",array(
						  '[fee]'=>FunctionsV3::prettyPrice($resp_fee['delivery_fee'])
						));
									
					} catch (Exception $e) {							
			        }    							        
				}			
								
		    	$settings = array();		    		    	
				
				$this->details = array(
				 'data'=>$data,				 
				);
			} else $this->msg = $this->t("merchant id not found");
		} else $this->msg = $this->t("invalid merchant id");
		$this->output();
	}
	
	public function actiongetMerchantMenu()
	{		
		$page_limit = mobileWrapper::paginateLimit();
		
		$this->setMerchantTimezone();
		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
        
        $page_action =  isset($this->data['page_action'])?$this->data['page_action']:'';
        
		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
		if($merchant_id>0){
			itemWrapper::setMultiTranslation();			
			if($menu = itemWrapper::getMenu($merchant_id, $page, $page_limit)){								
				$this->code = 1;
				$this->msg = "OK";
				$menu['item_id']=isset($this->data['item_id'])?$this->data['item_id']:'';
				$menu['cat_id']=isset($this->data['cat_id'])?$this->data['cat_id']:'';
				$menu['page_action']=$page_action;
				$this->details = $menu;
			} else $this->msg = $this->t("This restaurant has not published their menu yet");
			
		} else $this->msg = $this->t("invalid merchant id");
		$this->output();
	}
			
	public function actiongetItemByCategory()
	{		
		$enabled_trans=getOptionA('enabled_multiple_translation');
		
		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
		
		$cat_id = isset($this->data['cat_id'])?$this->data['cat_id']:'';
		$page_action = isset($this->data['page_action'])?$this->data['page_action']:'';
		
		itemWrapper::setMultiTranslation();
		
		$category = itemWrapper::getCategoryByID($cat_id);
		if($enabled_trans==2){
		   $category_name['category_name_trans']=!empty($category['category_name_trans'])?json_decode($category['category_name_trans'],true):'';
           $category['category_name'] = qTranslate($category['category_name'],'category_name',$category_name);
		}

		$page_limit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
		
        itemWrapper::$sizes = itemWrapper::getSize($merchant_id);
        
        $filter_dishes=array();
        if(isset($this->data['filter_dishes'])){
        	$filter_dishes = $this->data['filter_dishes'];
        }	
        
        if($merchant_id>0 && $cat_id>0 ){
			if($res = itemWrapper::getItemByCategory($merchant_id,$cat_id,true,$page,$page_limit,$filter_dishes)){			
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				 'page_action'=>$page_action,
				 'paginate_total'=>$res['paginate_total'],
				 'category'=>$category,
				 'category_list'=>itemWrapper::getMerchantCategory($merchant_id),
				 'data'=>$res['data']
				);
			} else {
				$this->msg = $this->t("no item found on this category");
				$this->details = array(
				 'page_action'=>0,
				 'category'=>$category,		
				 'category_list'=>itemWrapper::getMerchantCategory($merchant_id),	 
				);
			}
        } else $this->msg = mt("invalid merchant id or category id");
		$this->output();
	}
	
	public function actionsearchFoodItem()
	{
		$item_name = isset($this->data['item_name'])?$this->data['item_name']:'';
		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
				
		if($res = itemWrapper::searchItemByName($merchant_id,$item_name)){
			$data = array();
			foreach ($res as $val) {
				$category = json_decode($val['category'],true);
				if(is_array($category) && count($category)>=1){
					$val['cat_id'] = $category[0];
				}
				$val['item_name'] = mobileWrapper::highlight_word($val['item_name'],$item_name);
				$val['photo']=mobileWrapper::getImage($val['photo']);
				$val['item_description']=strip_tags($val['item_description']);				
				$data[]=$val;
			}
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			 'data'=>$data
			);
		} else $this->msg = $this->t("No results");
		$this->output();
	}
	
	
	public function actionitemDetails()
	{		
		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';		
		$item_id = isset($this->data['item_id'])?$this->data['item_id']:'';		
		$device_uiid = isset($this->data['device_uiid'])?$this->data['device_uiid']:'';		
		
		if($merchant_id<0 || empty($merchant_id)){
			$this->msg = $this->t("invalid merchant id");
			$this->output();
		}
		if($item_id<0 || empty($item_id)){
			$this->msg = $this->t("invalid item id");
			$this->output();
		}
		
		$ordering_disabled=false; $ordering_msg='';
		$disabled_website_ordering = getOptionA('disabled_website_ordering');		
		if($disabled_website_ordering=="yes"){
			$ordering_msg = $this->t("Ordering is disabled by admin");
			$ordering_disabled=true;			
		}
		$merchant_disabled_ordering = getOption($merchant_id,'merchant_disabled_ordering');
		if($merchant_disabled_ordering=="yes"){
			$ordering_msg = $this->t("Ordering is disabled by merchant");
			$ordering_disabled=true;
		}
		$merchant_close_store = getOption($merchant_id,'merchant_close_store');
		if($merchant_close_store=="yes"){
			$ordering_msg = $this->t("Merchant is now close and not accepting any orders");
			$ordering_disabled=true;
		}
		
		$p = new CHtmlPurifier(); $cart_data=array();		
		$trans=getOptionA('enabled_multiple_translation'); 
				
		
		if ($res=Yii::app()->functions->getItemById($this->data['item_id'])){
			
			itemWrapper::setMultiTranslation();
			
			$res = $res[0];
			unset($res['cooking_ref_trans']);			
			$res['cooking_ref'] = itemWrapper::translateCookingRef($res['cooking_ref']);			
			$res['ingredients'] = itemWrapper::translateIngredients($res['ingredients']);			
			
			/*TRANSLATE ADDON*/
			if($trans==2){
				$new_addon = array();
				if(is_array($res['addon_item']) && count($res['addon_item'])>=1){
					foreach ($res['addon_item'] as $add_val) {							
						$add_val['subcat_name']=qTranslate($add_val['subcat_name'],'subcat_name',$add_val);
																				
						if(is_array($add_val['sub_item']) && count($add_val['sub_item'])>=1){
							$new_sub_item = array();
							foreach ($add_val['sub_item'] as $sub_item_val) {
								$sub_item_val['sub_item_name'] = qTranslate($sub_item_val['sub_item_name'],'sub_item_name',$sub_item_val);
								$sub_item_val['item_description'] = qTranslate($sub_item_val['item_description'],'item_description',$sub_item_val);									
								$new_sub_item[]=$sub_item_val;
							}
							$add_val['sub_item']=$new_sub_item;
						}													
													
						$new_addon[]=$add_val;
					}
					
					$res['addon_item']=$new_addon;
				}				
			}								
			/*END TRANSLATE ADDON*/
			
			if($res['not_available']==2){				
			   $ordering_msg = $this->t("Sorry but this item is not available");
			   $ordering_disabled=true;
			}
			
			$res['item_name']=qTranslate($res['item_name'],'item_name',$res);        	
			$res['item_description']=qTranslate($res['item_description'],'item_description',$res);
			
			
			$res['item_name'] = $p->purify($res['item_name']);
			$res['item_description'] = $p->purify($res['item_description']);
			$res['item_name_trans'] = $p->purify($res['item_name_trans']);
			$res['item_description_trans'] = $p->purify($res['item_description_trans']);
			
			$res['photo'] = mobileWrapper::getImage($res['photo'],'default_cuisine.png');
			
			/*GET DISH*/
			$icon_dish= array();
			if(!empty($res['dish'])){				
				if (method_exists("FunctionsV3","getDishIcon")){	   
			       $icon_dish = FunctionsV3::getDishIcon($res['dish']);
				} else $icon_dish='';
			} else $icon_dish='';
			
			$res['dish_list'] = $icon_dish;
			
			/*GALLERY*/
			$res['gallery']=array();
			if(!empty($res['gallery_photo'])){
				$new_gallery_photo=array();
				$gallery_photo = json_decode($res['gallery_photo'],true);
				if(is_array($gallery_photo) && count((array)$gallery_photo)>=1){
					foreach ($gallery_photo as $gallery_photo_val) {
						$new_gallery_photo[]= mobileWrapper::getImage($gallery_photo_val);
					}
					$res['gallery']=$new_gallery_photo;					
				}			
			}
			
			
			/*CHECK IF MULTIPLE PRICE*/
			$res['multiple_price'] = false;
			if(is_array($res['prices']) && count($res['prices'])>=2){	
				$new_price = array();
				foreach ($res['prices'] as $prices) {
					$prices['size']=qTranslate($prices['size'],'size',$prices); 					
					$new_price[]=$prices;
				}
				$res['prices']=$new_price;					
				$res['multiple_price'] = true;
			} else {
				/*FIXED FOR SINGLE PRICE WITH ONLY 1 SIZE*/	
				if(isset($res['prices'][0])){
					if( $res['prices'][0]['size_id']>0 ){
						$res['multiple_price'] = true;
					}					
				}		
			}		
			
			$row = isset($this->data['row'])?$this->data['row']:'';
			if(is_numeric($row)){				
				if($resp=mobileWrapper::getCart($device_uiid)){
					$cart=json_decode($resp['cart'],true);
					if(array_key_exists($row,(array)$cart)){
						$cart[$row]['row']=$row;
						$cart_data = isset($cart[$row])?$cart[$row]:'';
					}
				}
			} else $cart_data='';
			
			
			/*inventory*/
			$inventory_enabled = FunctionsV3::inventoryEnabled($merchant_id);
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'inventory_enabled'=>$inventory_enabled==true?1:0,
			  'cat_id'=>isset($this->data['cat_id'])?$this->data['cat_id']:'',
			  'data'=>$res,
			  'cart_data'=>$cart_data,		
			  'ordering_disabled'=>$ordering_disabled,
			  'ordering_msg'=>$ordering_msg
			);			
			
		} else $this->msg = $this->t("Invalid item id");
		
		$this->output();
	}
	
	public function actionaddToCart()
	{	
		$code_version = isset($_REQUEST['code_version'])?(float)$_REQUEST['code_version']:1.5;
		
		if($code_version<=1.4){				
			$this->getGETData();
		    $this->data = $_GET;
		} else {
			$this->getPOSTData();
			$this->data = $_POST;
		}
		
		$data = $_POST;			
			
		$qty = isset($data['qty'])?$data['qty']:'';		
		
		if($qty>0){
			// silent			
			if (strpos($qty,'.') !== false) {
			   $this->msg = $this->t("invalid quantity");
			   $this->output();
			}	
		} else {
		  	$this->msg = $this->t("invalid quantity");
			$this->output();
		}
		
		$device_uiid = isset($this->data['device_uiid'])?$this->data['device_uiid']:'';
		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
		$item_id = isset($data['item_id'])?$data['item_id']:'';
				
		if($device_uiid<0 || empty($device_uiid)){
			$this->msg = $this->t("invalid device uiid");
			$this->output();
		}
		
		if($merchant_id<0 || empty($merchant_id)){			
			$this->msg = $this->t("invalid merchant id");
			$this->output();
		}
		if($merchant_id=="undefined"){
			$this->msg = $this->t("invalid merchant id");
			$this->output();
		}		
		
		if(!is_numeric($item_id)){
			$this->msg = $this->t("Invalid item id");
			$this->output();
		}
		if(!$item_details = Yii::app()->functions->getFoodItem($item_id)){
			$this->msg = $this->t("Item details not found");
			$this->output();
		}
		$data['discount'] = isset($item_details['discount'])?$item_details['discount']:0;
		$data['non_taxable'] = isset($item_details['non_taxable'])?$item_details['non_taxable']:0;
		
		//dump($item_details);
		
		if(!isset($data['price'])){
			$this->msg = $this->t("Please select price");
			$this->output();
		}
		
		$DbExt=new DbExt;
		$refresh = 0;
		$debug = false;	
				
		if ( $res = mobileWrapper::getCart($device_uiid)){			
			 $current_cart = json_decode($res['cart'],true);
			 
			 $row = isset($data['row'])?$data['row']:'';
			 if(is_numeric($row)){					 	    
				$current_cart[$row]= $data;	
				$refresh = 1;		
			 } else {
			 	
			 		if($debug){
						dump($data);
						dump("END DATA");
						dump($current_cart);
					}										
										
					/*CHECK IF THE ITEM IS ALREADY IN THE CART */					
					$item_found = true; $found_key = -1;
					
					if(is_array($current_cart) && count($current_cart)>=1){
						foreach ($current_cart as $current_cart_key => $current_cart_val) {
							/*dump($current_cart_key);
							dump($current_cart_val);*/
										
							$item_found = true;
													
							if ($current_cart_val['item_id']!=$data['item_id']){
								$item_found = false;
							}
							if ($current_cart_val['price']!=$data['price']){
								$item_found = false;
							}
							
							/*COOKING REF*/
							if(array_key_exists('cooking_ref',$data) && array_key_exists('cooking_ref',$current_cart_val)){
								if ( $data['cooking_ref']!=$current_cart_val['cooking_ref']){
									$item_found = false;
								}
							} else {								
								if(!array_key_exists('cooking_ref',$data) && !array_key_exists('cooking_ref',$current_cart_val)){
								} else $item_found = false;								
							}
							
							/*INGREDIENTS*/
							if(array_key_exists('ingredients',$data) && array_key_exists('ingredients',$current_cart_val)){
								$ingredients = json_encode($data['ingredients']);
								$ingredients2 = json_encode($current_cart_val['ingredients']);								
								if($ingredients!=$ingredients2){
									$item_found = false;
								} 
							} else {
								if(!array_key_exists('ingredients',$data) && !array_key_exists('ingredients',$current_cart_val)){
								} else $item_found = false;								
							}
							
							/*ADDON*/
							if(array_key_exists('sub_item',$data) && array_key_exists('sub_item',$current_cart_val)){
								$sub_item = json_encode($data['sub_item']);
								$sub_item2 = json_encode($current_cart_val['sub_item']);
								if($sub_item!=$sub_item2){
									$item_found = false;
								} 
							} else {
								if(!array_key_exists('sub_item',$data) && !array_key_exists('sub_item',$current_cart_val)){
								} else $item_found = false;								
							}
							
							if($item_found==TRUE){								
							   $found_key = $current_cart_key;
						    } 
						    
						} /*END LOOP*/
						
						if($found_key>=0){
							if($debug){dump("found key=> $found_key");}								
							$current_cart[$found_key]['qty']  = $current_cart[$found_key]['qty']+$data['qty'];							
						} else {							
							array_push($current_cart,$data);
						}
						
					} else {						
						array_push($current_cart,$data);
					}					
				}
				
				if($debug){
					dump("FINAL CART");
					dump($current_cart);
					die();
				}
				
				/*inventory*/				
				if(FunctionsV3::inventoryEnabled($merchant_id)){
				  $current_item_id = isset($data['item_id'])?(integer)$data['item_id']:'';
				  $current_item_price = isset($data['price'])?$data['price']:'';		
				  $current_item_size = isset($data['with_size'])?(integer)$data['with_size']:0;
				  $inv_qty = 0;				  
				  foreach ($current_cart as $val) {
				  	  if($current_item_id==$val['item_id'] && trim($current_item_price) == trim($val['price']) ){
				  	  	 $inv_qty+=$val['qty'];
				  	  }
				  }				 				  				  
				  try {
				  	 StocksWrapper::verifyStocks($inv_qty,$merchant_id,$current_item_id,$current_item_size,$current_item_price);
				  } catch (Exception $e) {
		            $this->msg = $e->getMessage();
		            $this->output();
		          }					  
				}
				
							
				$cart_count = count($current_cart);				
				$DbExt->updateData("{{mobile2_cart}}",array(
				  'merchant_id'=>$merchant_id,
				  'device_uiid'=>$device_uiid,				  
				  'device_platform'=>isset($this->data['device_platform'])?strtolower($this->data['device_platform']):'android',
				  'cart'=>json_encode($current_cart),
				  'cart_count'=>$cart_count,
				  'date_modified'=>FunctionsV3::dateNow(),
				),'cart_id', $res['cart_id']);
			
		} else {			
								
			/*inventory*/
			if(FunctionsV3::inventoryEnabled($merchant_id)){
				try {
				  	 StocksWrapper::verifyStocks(
				  	  isset($data['qty'])?(integer)$data['qty']:0,
				  	  $merchant_id,
				  	  isset($data['item_id'])?(integer)$data['item_id']:0,
				  	  isset($data['with_size'])?(integer)$data['with_size']:0,
				  	  isset($data['price'])?$data['price']:''
				  	 );
				} catch (Exception $e) {
		            $this->msg = $e->getMessage();
		            $this->output();
		        }			
			}			
			
			$cart_count=1;
			$DbExt->insertData("{{mobile2_cart}}",array(
			 'merchant_id'=>$merchant_id,
		     'device_uiid'=>$device_uiid,		     
		     'device_platform'=>isset($this->data['device_platform'])?strtolower($this->data['device_platform']):'',
		     'cart'=>json_encode(array($data)),
		     'cart_count'=>$cart_count,
		     'date_modified'=>FunctionsV3::dateNow(),
		    ));
		}
		
		$this->code = 1;
		$this->msg=$this->t("Added to cart");
		if($refresh==1){
			$this->msg=$this->t("Cart updated");
		}			
		$this->details=array(
		 'merchant_id'=>$merchant_id,
		 'cart_count'=>$cart_count,
		 'refresh'=>$refresh
		);
			
		$this->output();
	}
	
	public function actiongetCartCount()
	{
		$this->data = $_POST;
		$data = $_POST;
		$device_uiid = isset($this->data['device_uiid'])?$this->data['device_uiid']:'';
		$merchant_id = isset($data['merchant_id'])?$data['merchant_id']:'';		
		$basket_total=0;$item_total=0; 
		$transaction_type = isset($data['transaction_type'])?$data['transaction_type']:'';		
		
		if($res=mobileWrapper::getCart($device_uiid)){			
			if ( $res['merchant_id']!=$merchant_id){
				mobileWrapper::clearCart($device_uiid);				
			} else {
				$cart=json_decode($res['cart'],true);		
				
				if(empty($transaction_type)){
					$services = Yii::app()->functions->DeliveryOptions($merchant_id);		
					if(is_array($services) && count($services)>=1){
						foreach ($services as $services_key=>$services_val) {				
							$transaction_type = $services_key;
							break;				
						}
					}	
				}
				
				$params = array(
				  'delivery_type'=>$transaction_type,
				  'merchant_id'=>$merchant_id,
				  'card_fee'=>0
				);
										
				Yii::app()->functions->displayOrderHTML( $params,$cart );
				$code = Yii::app()->functions->code;		
				if($code==1){
				    $details = Yii::app()->functions->details['raw'];
				    
				    //dump($details);
				    if(is_array($cart) && count($cart)>=1){
						foreach ($cart as $val) {					
							$item_total+=$val['qty'];
						}
					}			    
				    					
					$basket_total = $details['total']['subtotal'];
					if($item_total>1){
						$basket_msg = mt("[item] items",array('[item]'=>$item_total));
					} else {
						$basket_msg = mt("[item] item",array('[item]'=>$item_total));
				    }
				    			    
				    $basket_total = FunctionsV3::prettyPrice($basket_total);			    
					$this->code=1;
					$this->msg = "OK";				
					$this->details = array(
					  'count'=>$item_total,
					  'basket_count'=>$basket_msg,
					  'basket_total'=>$basket_total
					);			
					$this->output();
				}
			}
		} 
		
		$this->msg=mt("0 found");
		$this->details = array(
		  'count'=>0,
		  'basket_count'=>mt("0 items"),		  
		  'basket_total'=>FunctionsV3::prettyPrice(0.00001)
		);				
		$this->output();
	}
	
	public function actionloadCart()
	{				
		
		$this->details = array();
		
		if(!is_numeric($this->merchant_id) || $this->merchant_id<=0){
			$this->msg = $this->t("invalid merchant id");
			$this->code = 5;
						
			mobileWrapper::clearCart($this->device_uiid);
			$this->output();
		}
		
		$is_location = FunctionsV3::isSearchByLocation();
		
		if (!$merchant_info_raw = FunctionsV3::getMerchantById($this->merchant_id)){
			$merchant_info_raw = Yii::app()->request->stripSlashes($merchant_info_raw);
			$this->msg = $this->t("Merchant not found");
			$this->code = 5;
		}
		
		$merchant_lat = isset($merchant_info_raw['latitude'])?$merchant_info_raw['latitude']:0;
		$merchant_lng = isset($merchant_info_raw['lontitude'])?$merchant_info_raw['lontitude']:0;				
		$merchant_unit = isset($merchant_info_raw['distance_unit'])?$merchant_info_raw['distance_unit']:'';	
		
		$customer_lat = isset($this->data['lat'])?$this->data['lat']:0;
		$customer_lng = isset($this->data['lng'])?$this->data['lng']:0;				
		
		/*CHECK IF ORDERING IS DISABLED*/
		$disabled_website_ordering = getOptionA('disabled_website_ordering');		
		if($disabled_website_ordering=="yes"){
			$this->msg = $this->t("Ordering is disabled by admin");
			$this->code = 4;
			
			mobileWrapper::clearCart($this->device_uiid);
			$this->output();
		}
		$merchant_disabled_ordering = getOption($this->merchant_id,'merchant_disabled_ordering');
		if($merchant_disabled_ordering=="yes"){
			$this->msg = $this->t("Ordering is disabled by merchant");
			$this->code = 4;
			
			mobileWrapper::clearCart($this->device_uiid);
			$this->output();
		}
				
		$merchant_close_store = getOption($this->merchant_id,'merchant_close_store');
		if($merchant_close_store=="yes"){
			$this->msg = $this->t("Merchant is now close and not accepting any orders");
			$this->code = 4;
			
			mobileWrapper::clearCart($this->device_uiid);
			$this->output();
		}
		
		$this->setMerchantTimezone();
		
		$transaction_type='';
		$services = Yii::app()->functions->DeliveryOptions($this->merchant_id);
		
		if(is_array($services) && count($services)>=1){
			foreach ($services as $services_key=>$services_val) {				
				$transaction_type = $services_key;
				break;				
			}
		}
		
		if(isset($this->data['transaction_type'])){
			if(!empty($this->data['transaction_type'])){
				$transaction_type=$this->data['transaction_type'];
			}
		} else {
			$this->data['transaction_type']=$transaction_type;
		}	
		
		$token = isset($this->data['user_token'])?$this->data['user_token']:'';		
		$client_info = mobileWrapper::getCustomerByToken($token);
							
		/*GET CART*/
		$res=mobileWrapper::getCart($this->device_uiid);
				
		$search_resp = mobileWrapper::searchMode();
		$search_mode = $search_resp['search_mode'];
		$location_mode = $search_resp['location_mode'];			

		$code_version = isset($_REQUEST['code_version'])?(float)$_REQUEST['code_version']:1.5;
		
		
		/*UPDATE DISTANCE*/				
		if($res):
		if(!$is_location && $res['delivery_lat']!=$customer_lat && $code_version>1.4 ){
			
			$provider = array(); $provider_mode='';			
			
		    $provider = mobileWrapper::getMapProvider(); 			
		    MapsWrapperTemp::init($provider);		   
		    $provider_mode = isset($provider['mode'])?$provider['mode']:'driving';			
		    
			try {			
				
				$new_merchant_unit = CheckoutWrapperTemp::unit($merchant_unit);				
				$resp_distance = MapsWrapperTemp::getDistance(
				 $merchant_lat,$merchant_lng,$customer_lat,$customer_lng,$new_merchant_unit,$provider_mode
				);

							
				$res['distance']=$resp_distance['distance'];
				$res['distance_unit']=$resp_distance['unit'];
				
				$res['street']='';
				$res['city']='';
				$res['state']='';
				$res['zipcode']='';
				
				Yii::app()->db->createCommand()->update("{{mobile2_cart}}",array(
				 'distance'=>$resp_distance['distance'],
				 'distance_unit'=>$resp_distance['unit'],
				 'delivery_lat'=>$customer_lat,
				 'delivery_long'=>$customer_lng,
				 'street'=>'',
				 'city'=>'',
				 'state'=>'',
				 'zipcode'=>''
				),
          	     'cart_id=:cart_id',
          	     array(
          	      ':cart_id'=>$res['cart_id']
          	     )
          	   );
				
			} catch (Exception $e) {
	   	  	    echo $e->getMessage();
	   	  	 }
		} 
		endif;
		/*END UPDATE DISTANCE*/		

		
		/*CHECK TIPS DEFAULT*/
		if($res):
		$merchant_tip_default = getOption($this->merchant_id,'merchant_tip_default');
		if($merchant_tip_default>0 && $res['tips']<=0 && $res['remove_tip']<=0 ){			
			$res['tips'] = $merchant_tip_default;
			
			Yii::app()->db->createCommand()->update("{{mobile2_cart}}",array(
			  'tips'=>$merchant_tip_default
			),
	  	    'cart_id=:cart_id',
		  	    array(
		  	      ':cart_id'=>$res['cart_id']
		  	    )
	  	    );
		}			
		endif;
				
		if($res){
			$cart=json_decode($res['cart'],true);
			
			$data = array(
			  'delivery_type'=>$transaction_type,
			  'merchant_id'=>$this->merchant_id,
			  'card_fee'=>0
			);
			
			if($res['tips']>0.0001){
				$data['cart_tip_percentage']=$res['tips'];
				$data['tip_enabled']=2;
				$data['tip_percent']=$res['tips'];
			}		
			
			$voucher_details = !empty($res['voucher_details'])?json_decode($res['voucher_details'],true):false;	
			if(is_array($voucher_details) && count($voucher_details)>=1){
				$data['voucher_name']=$voucher_details['voucher_name'];
				$data['voucher_amount']=$voucher_details['amount'];
				$data['voucher_type']=$voucher_details['voucher_type'];
			}
			
			if($res['points_apply']>0.0001){
				$data['points_apply']=$res['points_apply'];
			}
			if($res['points_amount']>0.0001){
				$data['points_amount']=$res['points_amount'];
			}
			
			unset($_SESSION['shipping_fee']);
			if($res['delivery_fee']>0.0001){
				$data['delivery_charge']=$res['delivery_fee'];
			} else $data['delivery_charge']=0;
			
			$cart_details = $res;
			unset($cart_details['cart']);		
			unset($cart_details['device_uiid']);
			unset($cart_details['cart_id']);			
			unset($_SESSION['pts_redeem_amt']);
						
			/*inventory*/
			if(FunctionsV3::inventoryEnabled($this->merchant_id)){
				$new_cart = array();
			    if(is_array($cart) && count($cart)>=1){
			    	foreach ($cart as $cartval) {
			    		try {			    		   
			    		   StocksWrapper::verifyStocks(
			    		      isset($cartval['qty'])?(integer)$cartval['qty']:0,
			    		      $this->merchant_id,
			    		      isset($cartval['item_id'])?(integer)$cartval['item_id']:0,
			    		      isset($cartval['with_size'])?(integer)$cartval['with_size']:0,
			    		      isset($cartval['price'])?$cartval['price']:0
			    		   );
			    		   $new_cart[]=$cartval;
			    		} catch (Exception $e) {
				   	  	    //echo $e->getMessage();
				   	  	 }
			    	}
			    	$cart = $new_cart;
			    }			
			}
			
			//dump($data);
			/*dump($cart);
			die();*/
			
			$multiple_translation = getOptionA('enabled_multiple_translation'); 	
			
			Yii::app()->functions->displayOrderHTML( $data,$cart );
			$code = Yii::app()->functions->code;
			$msg  = Yii::app()->functions->msg;
			
			if ($code==1){
				$this->code = 1;
			    $details = Yii::app()->functions->details['raw'];
			    
			    /*dump($details);
			    die();*/
			    			    
			    /*TRANSLATE*/
			   if($multiple_translation==2){
				   if(is_array($details['item']) && count($details['item'])>=1){
				   	  $new_item = array();
				   	  foreach ($details['item'] as $key=> $details_item_val) {				   	  	
				   	  	 $details_item_val['item_name'] = qTranslate($details_item_val['item_name'],'item_name',$details_item_val['item_name_trans']);
				   	  	 
				   	  	 if(isset($details_item_val['new_sub_item'])){						   	  	 	 
					   	  	 if(is_array($details_item_val['new_sub_item']) && count( (array) $details_item_val['new_sub_item'])>=1){					   	  	 	
					   	  	 	$newest_new_sub_item_val=array();
					   	  	 	foreach ($details_item_val['new_sub_item'] as $new_sub_item_key=>$new_sub_item_val) {		
					   	  	 		$new_sub_item_key = qTranslate($new_sub_item_key,'subcategory_name',$new_sub_item_val[0]['subcategory_name_trans']);				   	  	 						   	  	 		
					   	  	 		$newest_new_sub_item_val[$new_sub_item_key]=$new_sub_item_val;
					   	  	 	}							   	  	 	
					   	  	 	$details_item_val['new_sub_item']=$newest_new_sub_item_val;
					   	  	 }				   	  
				   	  	 }
				   	  	 
				   	  	 $new_item[$key]=$details_item_val;
				   	  }		
				   	  $details['item']=$new_item;
				   }			
			   }
			   /*END TRANSLATE*/
			   
			    /*EURO TAX*/
			   $is_apply_tax = 2;
			   if(EuroTax::isApplyTax($this->merchant_id)){
			   	   $new_total = EuroTax::computeWithTax($details, $this->merchant_id);
			   	   $details['total']=$new_total;			
			   	   $is_apply_tax=1;   	   
			   }
			   
			   $has_addressbook = 0;
			   $client_id='';
			   
			   //$token = isset($this->data['user_token'])?$this->data['user_token']:'';
    	       //if($client_info = mobileWrapper::getCustomerByToken($token)){
    	       if($client_info){
    		       $client_id = $client_info['client_id'];
    		       if($search_mode=="location"){
    		       	   if(LocationWrapper::hasAddress($client_id)){
    		       	   	  $has_addressbook = 1;
    		       	   }
    		       } else {
	    		       if (mobileWrapper::getAddressBookByClient($client_id)){
	    		       	  $has_addressbook = 1;
					   }
    		       }
    	       }    
			   
    	       $defaul_delivery_date = date("Y-m-d");
    	       $date_list = FunctionsV3::getDateList($this->merchant_id);
    	       foreach ($date_list as $date_list_key => $date_list_val) {    	       	  
    	       	  $defaul_delivery_date = $date_list_key;
    	       	  break;
    	       }
    	           	       
    	       $subtotal = $details['total']['subtotal'];
    	       $cart_error=array();
    	       
    	       $merchant_minimum_order=0;
    	       
    	        /*CHECKING MAX AND MIN AMOUNT*/    	        
    	       if($transaction_type=="delivery"){    	       	  

    	       	  $merchant_minimum_order = getOption($this->merchant_id,'merchant_minimum_order');     	
    	       	  $min_tables_enabled = getOption($this->merchant_id,'min_tables_enabled');
    	       	      	       	  
    	       	  if($min_tables_enabled==1 && !empty($res['distance'])){    	       	  	  
    	       	  	  $merchant_minimum_order = CheckoutWrapperTemp::getMinimumOrderTable(
    	       	  	  $this->merchant_id,$res['distance'],$res['distance_unit'],$merchant_minimum_order
    	       	  	  );
    	       	  }    	       
    	       	      	       	      	       	  
    	       	  if($merchant_minimum_order>0){
    	       	  	 if($merchant_minimum_order>$subtotal){
    	       	  	 	$cart_error[] = Yii::t("mobile2","Sorry, your order does not meet the minimum [transaction_type] amount of [min_amount]",array(
    	       	  	 	 '[min_amount]'=>FunctionsV3::prettyPrice($merchant_minimum_order),
    	       	  	 	 '[transaction_type]'=>$this->t($transaction_type)
    	       	  	 	));
    	       	  	 }    	       	  
    	       	  }      
    	       	  
    	       	  
    	       	  $merchant_maximum_order = getOption($this->merchant_id,'merchant_maximum_order');
    	       	  if($merchant_maximum_order>0.001){
    	       	  	 if($subtotal>$merchant_maximum_order) {
    	       	  	 	$cart_error[] = Yii::t("mobile2","Sorry, your order has exceeded the maximum [transaction_type] amount of [min_amount]",array(
    	       	  	 	 '[min_amount]'=>FunctionsV3::prettyPrice($merchant_maximum_order),
    	       	  	 	 '[transaction_type]'=>$this->t($transaction_type)
    	       	  	 	));
    	       	  	 }    	       	  
    	       	  }    	       	     
    	       } elseif ( $transaction_type=="pickup"){
    	       	  $minimum_order = getOption($this->merchant_id,'merchant_minimum_order_pickup'); 
    	       	  if($minimum_order>0.001){
    	       	  	 if($minimum_order>$subtotal){
    	       	  	 	$cart_error[] = Yii::t("mobile2","Sorry, your order does not meet the minimum [transaction_type] amount of [min_amount]",array(
    	       	  	 	 '[min_amount]'=>FunctionsV3::prettyPrice($minimum_order),
    	       	  	 	 '[transaction_type]'=>$this->t($transaction_type)
    	       	  	 	));
    	       	  	 }    	       	  
    	       	  }    	         	       	  
    	       	  $maximum_order = getOption($this->merchant_id,'merchant_maximum_order_pickup');
    	       	  if($maximum_order>0.001){
    	       	  	 if($subtotal>$maximum_order) {
    	       	  	 	$cart_error[] = Yii::t("mobile2","Sorry, your order has exceeded the maximum [transaction_type] amount of [min_amount]",array(
    	       	  	 	 '[min_amount]'=>FunctionsV3::prettyPrice($maximum_order),
    	       	  	 	 '[transaction_type]'=>$this->t($transaction_type)
    	       	  	 	));
    	       	  	 }    	       	  
    	       	  }	       	
    	       } elseif ( $transaction_type=="dinein"){
    	       	  $minimum_order = getOption($this->merchant_id,'merchant_minimum_order_dinein'); 
    	       	  if($minimum_order>0.001){
    	       	  	 if($minimum_order>$subtotal){
    	       	  	 	$cart_error[] = Yii::t("mobile2","Sorry, your order does not meet the minimum [transaction_type] amount of [min_amount]",array(
    	       	  	 	 '[min_amount]'=>FunctionsV3::prettyPrice($minimum_order),
    	       	  	 	 '[transaction_type]'=>$this->t($transaction_type)
    	       	  	 	));
    	       	  	 }    	       	  
    	       	  }      	       	  
    	       	  $maximum_order = getOption($this->merchant_id,'merchant_maximum_order_dinein');
    	       	  if($maximum_order>0.001){
    	       	  	 if($subtotal>$maximum_order) {
    	       	  	 	$cart_error[] = Yii::t("mobile2","Sorry, your order has exceeded the maximum [transaction_type] amount of [min_amount]",array(
    	       	  	 	 '[min_amount]'=>FunctionsV3::prettyPrice($maximum_order),
    	       	  	 	 '[transaction_type]'=>$this->t($transaction_type)
    	       	  	 	));
    	       	  	 }    	       	  
    	       	  }	       	
    	       }    	       
    	       /*CHECKING MAX AND MIN AMOUNT*/	
    	       
    	       
    	        /*CHECK IF HAS POINTS ADDON*/
    	       $available_points=0; $available_points_label = '';
    	       $points_enabled = '';   $pts_disabled_redeem=''; 	       
    	       if (FunctionsV3::hasModuleAddon("pointsprogram")){
    	       	    	       	
    	       	  $points_enabled = getOptionA('points_enabled');
    	       	  if($points_enabled=="1"){
    	       	   	  if(!PointsProgram::isMerchantSettingsDisabled()){
    	       	   	  	  $mt_disabled_pts = getOption($this->merchant_id,'mt_disabled_pts');
    	       	   	  	  if($mt_disabled_pts==2){
    	       	   	  	  	 $points_enabled='';
    	       	   	  	  }	    	       	   	  
    	       	   	  }
    	       	  }
    	       	  
    	       	  $pts_disabled_redeem = getOptionA('pts_disabled_redeem');
    	       	  if(!PointsProgram::isMerchantSettingsDisabled()){
    	       	  	  $mt_pts_disabled_redeem=getOption($this->merchant_id,'mt_pts_disabled_redeem');
    	       	  	  if($mt_pts_disabled_redeem>0){
    	       	  	  	  $pts_disabled_redeem=$mt_pts_disabled_redeem;
    	       	  	  }    	       	  
    	       	  }
    	       	   
    	       	  /*GET EARNING POINTS FOR THIS ORDER*/
    	       	  $subtotal = $details['total']['subtotal'];    	       	  
    	       	  if ($earn_pts = mobileWrapper::getCartEarningPoints($cart,$subtotal,$this->merchant_id)){    	       	  	 
    	       	  	 Yii::app()->db->createCommand()->update("{{mobile2_cart}}",array(
						  'points_earn'=>(float)$earn_pts['points_earn'],
    	       	  	      'date_modified'=>FunctionsV3::dateNow()
						),
		          	     'cart_id=:cart_id',
		          	     array(
		          	      ':cart_id'=>$res['cart_id']
		          	     )
		          	   );		
    	       	  	 unset($db);
    	       	  }    	    
    	       	         	       	     	       	    	       
    	       	  if($client_id>0){    	       	  	    	       	      	       	   	  
    	       	   	  if($points_enabled=="1"){
	    	       	   	  $available_points = mobileWrapper::getTotalEarnPoints( $client_id , $this->merchant_id);
	    	       	   	  $available_points_label = Yii::t("mobile2","Your available points [points]",array(
	    	       	   	    '[points]'=>$available_points
	    	       	   	  ));
    	       	   	  }
    	       	   }    	       
    	       }    
    	       
    	       $merchant_info = array();
    	       if($merchant_info_raw){
    	       	   $merchant_info['restaurant_name']=clearString($merchant_info_raw['restaurant_name']);
    	       	   $merchant_info['rating'] = $merchant_info_raw['ratings']>0?$merchant_info_raw['ratings']:0;
    	       	   $merchant_info['background_url'] = mobileWrapper::getMerchantBackground($this->merchant_id);
    	       }
    	       
    	       $subtotal = isset($details['total']['subtotal'])?$details['total']['subtotal']:0;    	       
    	       Yii::app()->db->createCommand()->update("{{mobile2_cart}}",array(
				  'cart_subtotal'=>(float)$subtotal,
       	  	      'date_modified'=>FunctionsV3::dateNow()
				),
          	     'cart_id=:cart_id',
          	     array(
          	      ':cart_id'=>$res['cart_id']
          	     )
          	   );		
    	       
    	        $this->details = array(
    	         'merchant'=>$merchant_info,
			     'is_apply_tax'=>$is_apply_tax,
			     'checkout_stats'=>FunctionsV3::isMerchantcanCheckout($this->merchant_id),
			     'has_addressbook'=>$has_addressbook,
			     'services'=>$services,
			     'transaction_type'=>$transaction_type,
			     'default_delivery_date'=>$defaul_delivery_date,
			     //'default_delivery_date_pretty'=>date("D F d, Y"),
			     'default_delivery_date_pretty'=>FunctionsV3::prettyDate($defaul_delivery_date),
			     'required_delivery_time'=>getOption($this->merchant_id,'merchant_required_delivery_time'),	
			     'opt_contact_delivery'=>getOption($this->merchant_id,'merchant_opt_contact_delivery'),
			     'tip_list'=>mobileWrapper::tipList(),			     
			     'data'=>$details,
			     'cart_details'=>$cart_details,
			     'cart_error'=>$cart_error,
			     'points_enabled'=>$points_enabled,			     
			     'points_earn'=>isset($earn_pts['points_earn'])?$earn_pts['points_earn']:'',
			     'pts_label_earn'=>isset($earn_pts['pts_label_earn'])?$earn_pts['pts_label_earn']:'',
			     'available_points'=>$available_points,
			     'available_points_label'=>$available_points_label,
			     'pts_disabled_redeem'=>$pts_disabled_redeem
			   );
    	       
			} else {
				mobileWrapper::clearCart($this->device_uiid);
				$this->msg = $msg;
			}			
		} else $this->msg = $this->t("Cart is empty");
		
		$this->details['merchant_settings'] = mobileWrapper::merchantAppSettings($this->merchant_id);		
		
		$onetime_payment = getOptionA('mobileapp2_onetime_payment');    	   
	    $payment_list = (array) FunctionsV3::getMerchantPaymentListNew($this->merchant_id);	   
	    if($onetime_payment!=1){
	   	   $payment_list = array();
	    }  
	    $this->details['payment_list_count'] = count($payment_list);
	    $this->details['payment_list'] = $payment_list;		      	
								
		$this->output();
	}
	
	private function setMerchantTimezone(){
		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
		if($merchant_id>0){			
			$mt_timezone=Yii::app()->functions->getOption("merchant_timezone",$merchant_id);			
	    	if (!empty($mt_timezone)){
	    		Yii::app()->timeZone=$mt_timezone;
	    	}    	
		}
	}
	
	public function actiongetFirstCart()
	{		
		
		$token = isset($this->data['user_token'])?$this->data['user_token']:'';
		if($client_info = mobileWrapper::getCustomerByToken($token)){			
			$this->data['client_id'] = $client_info['client_id'];			
            mobileWrapper::registeredDevice($this->data);
		}
		
		if($res = mobileWrapper::getCart($this->device_uiid)){			
			$cart=json_decode($res['cart'],true);
			$count=0;			
			if(is_array($cart) && count($cart)>=1){
				foreach ($cart as $val) {					
					$count+=$val['qty'];
				}
			}						
			if($count>0){
				$this->code=1;
			    $this->msg = "OK";
				$this->details = array(
				  'merchant_id'=>$res['merchant_id'],
				  'count'=>$count
				);
			} else $this->msg = mt("No results");
		} else $this->msg = mt("No results");
		$this->output();
	}
	
	public function actionremoveCartItem()
	{
		
		$row = isset($this->data['row'])?$this->data['row']:0;		
		if($res=mobileWrapper::getCart($this->device_uiid)){
			$cart=json_decode($res['cart'],true);			
			if(array_key_exists($row,(array)$cart)){
				unset($cart[$row]);
				$DbExt=new DbExt;				
				$DbExt->updateData("{{mobile2_cart}}",array(				  
				  'cart'=>json_encode($cart),
				  'cart_count'=>count($cart),
				),'cart_id', $res['cart_id']);
				
				$this->code = 1;
				$this->msg="OK"; 
				$this->details='';
			} else $this->msg = $this->t("Cannot find cart row");
		} else $this->msg = $this->t("Cart is empty");
		$this->output();
	}
	
	public function actionclearCart()
	{		
		mobileWrapper::clearCart($this->device_uiid); 
		$this->code = 1;
		$this->msg = "OK";
		$this->output();
	}
	
	public function actionservicesList()
	{		
		$services = Yii::app()->functions->DeliveryOptions($this->merchant_id);
		if(is_array($services) && count($services)>=1){
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$services
			);
		} else $this->msg = $this->t("Services not available");
		$this->output();
	}
	
	public function actiondeliveryDateList()
	{		
		$this->setMerchantTimezone();			
		$dates = FunctionsV3::getDateList($this->merchant_id);
		
		$this->code = 1;
		$this->msg = "OK";
		$this->details = array(
		 'data'=>$dates
		);
		$this->output();
	}	
	
	public function actiondeliveryTimeList()
	{
		$this->setMerchantTimezone();	
		$delivery_date = isset($this->data['delivery_date'])?$this->data['delivery_date']:'';		
		$times = FunctionsV3::getTimeList($this->merchant_id,$delivery_date);
		$this->code = 1;
		$this->msg = "OK";
		$this->details = array(
		  'data'=>$times
		);
		$this->output();
	}
	
	public function actiongetAddressBookDropDown()
	{
		$this->actiongetAddressBookList();
	}
	
	public function actiongetAddressBookList()
	{		
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];						
		if($client_id>0){			
			if ( $res = mobileWrapper::getAddressBookByClient($client_id)){
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'data'=>$res
				);
			} else $this->msg = $this->t("No results");
		} else $this->msg = $this->t("No results");
		$this->output();
	}
	
	public function actionsetDeliveryAddress()
	{						
				
		$lat = isset($this->data['lat'])?$this->data['lat']:'';
	    $lng = isset($this->data['lng'])?$this->data['lng']:'';
	    $merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
	    
	    
	    if($merchant_id<=0){
	    	$this->msg = $this->t("invalid merchant id");
    		$this->output();
	    }	
	    
	    if(!$cart=mobileWrapper::getCart($this->device_uiid)){
	    	$this->msg = $this->t("Cart is empty");    	
    		$this->output();
	    }
	    
	    $country_name = Yii::app()->functions->countryCodeToFull(isset($this->data['country_code'])?$this->data['country_code']:'');		
		if(!empty($country_name)){
			$this->data['country']=$country_name;
		}
	
		$complete_address = $this->data['street']." ".$this->data['city']." ".$this->data['state']." ".$this->data['zipcode'];
		$complete_address.=" $country_name";
		
		try {
			
			$min_fees=0;			
			$cart_subtotal = isset($cart['cart_subtotal'])?(float)$cart['cart_subtotal']:0;			
			$resp = CheckoutWrapperTemp::verifyLocation($merchant_id,$lat,$lng,$cart_subtotal);	
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'complete_address'=>$complete_address,			  
			  'min_delivery_order'=>$min_fees,
			  'lat'=>$lat,
			  'lng'=>$lng,
			  'formatted_address'=>$complete_address,
			  'street'=>isset($this->data['street'])?$this->data['street']:'',
			  'city'=>isset($this->data['city'])?$this->data['city']:'',
			  'state'=>isset($this->data['state'])?$this->data['state']:'',
			  'zipcode'=>isset($this->data['zipcode'])?$this->data['zipcode']:'',
			  'country'=>$country_name
			);
						
			$params = array(
			  'street'=>isset($this->data['street'])?$this->data['street']:'',
			  'city'=>isset($this->data['city'])?$this->data['city']:'',
			  'state'=>isset($this->data['state'])?$this->data['state']:'',
			  'zipcode'=>isset($this->data['zipcode'])?$this->data['zipcode']:'',
			  'delivery_instruction'=>isset($this->data['delivery_instruction'])?$this->data['delivery_instruction']:'',
			  'location_name'=>isset($this->data['location_name'])?$this->data['location_name']:'',
			  'contact_phone'=>isset($this->data['contact_phone'])?$this->data['contact_phone']:'',
			  'country_code'=>isset($this->data['country_code'])?$this->data['country_code']:'',
			  'delivery_lat'=>$lat,
			  'delivery_long'=>$lng,
			  'save_address'=>isset($this->data['save_address'])?(integer)$this->data['save_address']:0,
			  'delivery_fee'=>(float)$resp['delivery_fee'],
			  'min_delivery_order'=>(float)$resp['min_order'],
			  'distance'=>$resp['distance'],
			  'distance_unit'=>$resp['unit'],
			);			
										
			Yii::app()->db->createCommand()->update("{{mobile2_cart}}",$params,
      	     'cart_id=:cart_id',
      	     array(
      	      ':cart_id'=>$cart['cart_id']
      	     )
      	   );		
      	   
      	   /*SAVE ADDRESS*/
      	   $customer_token = isset($this->data['user_token'])?$this->data['user_token']:'';      	   
      	   if( !empty($customer_token) && $params['save_address']==1 && !empty($params['street']) ){
      	   	   if ($customer_data = mobileWrapper::getCustomerByToken($customer_token,false)){
      	   	   	    $client_id = $customer_data['client_id'];
      	   	   	    if (!$resp_book =  mobileWrapper::getBookAddress($client_id,$params['street'],$params['city'],$params['state'])){
      	   	   	    	
      	   	   	    	$client_id = (integer)$client_id;      	   	   	    	
      	   	   	    	Yii::app()->db->createCommand()->update("{{address_book}}",array(
						  'as_default'=>1,
    	       	  	      'date_modified'=>FunctionsV3::dateNow()
						),
		          	     'client_id=:client_id',
		          	     array(
		          	      ':client_id'=>$client_id
		          	     )
		          	   );		
      	   	   	    	
      	   	   	    	$params_address_book = array(
						  'client_id'=>$client_id,
						  'street'=>isset($this->data['street'])?$this->data['street']:'',
						  'city'=>isset($this->data['city'])?$this->data['city']:'',
						  'state'=>isset($this->data['state'])?$this->data['state']:'',
						  'zipcode'=>isset($this->data['zipcode'])?$this->data['zipcode']:'',
						  'location_name'=>isset($this->data['location_name'])?$this->data['location_name']:'',
						  'country_code'=>isset($this->data['country_code'])?$this->data['country_code']:'',
						  'as_default'=>2,
						  'date_created'=>FunctionsV3::dateNow(),
						  'latitude'=>isset($this->data['lat'])?$this->data['lat']:'',
						  'longitude'=>isset($this->data['lng'])?$this->data['lng']:'',
						  'ip_address'=>$_SERVER['REMOTE_ADDR']
						);								
						Yii::app()->db->createCommand()->insert("{{address_book}}",$params_address_book);
      	   	   	    }      	   	   	   
      	   	   }
      	   }		
			
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();					    
        }		        
        
		$this->output();
	}
	
	public function actionloadPaymentList()
	{
		
		/*CHECK IF ORDERING IS DISABLED*/
		$disabled_website_ordering = getOptionA('disabled_website_ordering');		
		if($disabled_website_ordering=="yes"){
			$this->msg = $this->t("Ordering is disabled by admin");
			$this->output();
		}
		$merchant_disabled_ordering = getOption($this->merchant_id,'merchant_disabled_ordering');
		if($merchant_disabled_ordering=="yes"){
			$this->msg = $this->t("Ordering is disabled by merchant");
			$this->output();
		}
		
		$merchant_opt_contact_delivery= getOption($this->merchant_id,'merchant_opt_contact_delivery');
		$opt_contact_delivery = isset($this->data['opt_contact_delivery'])?$this->data['opt_contact_delivery']:'';
					
		if ( $res = FunctionsV3::getMerchantPaymentListNew($this->merchant_id)){			
			 $transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';
			 $this->code = 1;
			 $this->msg = "OK";		
			 $list = array();
			 
			 if(isset($res['mcd'])){
			    unset($res['mcd']);
			 }
			 if(isset($res['pyp'])){
			    unset($res['pyp']);
			 }
			 			 
			 /*REMOVE OFFLINE PAYMENT OPTION CONTACT DELIVERY*/
			 if($merchant_opt_contact_delivery==1 && $transaction_type=="delivery" && $opt_contact_delivery==1){
			 	if(isset($res['cod'])){unset($res['cod']);}
			 	if(isset($res['pyr'])){unset($res['pyr']);}
			 	if(isset($res['obd'])){unset($res['obd']);}
			 	if(isset($res['ocr'])){unset($res['ocr']);}
			 }				 
			 
			 foreach ($res as $key => $val) {
			 	switch ($key) {
			 		case "cod":
			 			if ( $transaction_type=="pickup"){
			 			   $val= $this->t("Pay On Pickup");
				 		} elseif ( $transaction_type=="dinein"){
				 			$val= $this->t("Pay in person");
				 		} else $val= mt($val);
			 			break;
			 	
			 		case "pyr":
			 			if ($transaction_type=="pickup"){
			 				$val = $this->t("Pay On Pickup Using Cards");
			 			} else $val = mt($val);
			 			break;
			 						 	
			 		case "paypal_v2":	
			 		   if ( !$resp = PaypalWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","Paypal V2 (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;	   
			 		  
			 		case "stp":				 		
			 		   if ( $resp = StripeWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $cardfee = FunctionsV3::prettyPrice($resp['card_fee']);
			 		   	   	  if(isset($resp['card_percentage'])){
			 		   	   	  	 $cardfee = FunctionsV3::prettyPriceNoCurrency($resp['card_percentage'])."%";
			 		   	   	  	 $cardfee.= "+".FunctionsV3::prettyPriceNoCurrency($resp['card_fee']);
			 		   	   	  }			 		   	   
			 		   	   	  $val = Yii::t("mobile2","Stripe (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>$cardfee
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;    
			 		   
			 		case "mercadopago":	
			 		   if ( $resp = mercadopagoWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","Mercadopago (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;    
			 		   
			 		case "mollie":	
			 		   if ( $resp = MollieWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","mollie (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;       
			 		   
			 		case "pagseguro":	
			 		   if ( $resp = pagseguroWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","pagseguro (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;          
			 		   
			 		case "selcompay":	
			 		   if ( $resp = selcompayWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","selcompay (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;             
			 		   
			 		case "kushi":	
			 		   if ( $resp = KushiWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","Kushi (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;         
			 		   
			 		case "ideal":	
			 		   if ( $resp = iDEALWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","Stripe iDEAL (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;        
			 		   
			 		case "orange":	
			 		   if ( $resp = OrangeWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","Orange Money (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;           
			 		   
			 		case "redsys":	
			 		   if ( $resp = RedsysWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","redsys (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;              
			 		   
			 		case "emspay":	
			 		   if ( $resp = EmspayWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","emspay (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;                 
			 		   
			 		case "fac":	
			 		   if ( $resp = FacWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","fac (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;    	   
			 		   
			 		case "rave":	
			 		   if ( $resp = RaveWrapper::getCredentials($this->merchant_id)){
			 		   	   if ($resp['card_fee']>0.0001){
			 		   	   	  $val = Yii::t("mobile2","rave (card fee [card_fee])",array(
			 		   	   	    '[card_fee]'=>FunctionsV3::prettyPrice($resp['card_fee'])
			 		   	   	  ));
			 		   	   } else $val = mt($val);
			 		   } else $val = mt($val);
			 		   break;       
			 			
			 		default:
			 			$val = mt($val);
			 			break;
			 	}			 	
			 	$list[] = array(
		 		  'payment_code'=>$key,
		 		  'payment_name'=>$val
		 		);
			 }
			 $this->details = array(
			   'data'=>$list
			 );
		} else $this->msg = $this->t("No payment option available");
		$this->output();
	}
	
	public function actionpayNow()
	{
				
		$this->setMerchantTimezone();
		
		$lang_code=Yii::app()->language;
		
		$search_resp = mobileWrapper::searchMode();
		$search_mode = $search_resp['search_mode'];
		$location_mode = $search_resp['location_mode'];		
		
		$token = isset($this->data['user_token'])?$this->data['user_token']:'';
		if(!$client_info = mobileWrapper::getCustomerByToken($token)){
			$this->msg = $this->t("Invalid token, please relogin again");
    		$this->output();
		}
				
		if (!$merchant_info = FunctionsV3::getMerchantInfo($this->merchant_id)){
			$this->msg = $this->t("invalid merchant id");
    		$this->output();
		}
		
		$client_id = (integer) $client_info['client_id'];    	
		$customer_first_name = isset($client_info['first_name'])?$client_info['first_name']:'';
		$customer_last_name = isset($client_info['last_name'])?$client_info['last_name']:'';
		$customer_email = isset($client_info['email_address'])?$client_info['email_address']:'';
				
    	$email_address = $client_info['email_address'];
    	
    	if ( FunctionsK::emailBlockedCheck($email_address)){
    		$this->msg = $this->t("Sorry but your email address is blocked by website admin"); 
    		$this->output();
    	}   
    	
    	/*CHECK CUSTOMER CAN ORDER*/
    	try {	    	    		    	
	    	CheckoutWrapperTemp::verifyCanPlaceOrder($client_id);	    	    	
	    } catch (Exception $e) {
			 $this->msg = $e->getMessage();
			 $this->output();
		}
		
    	
    	$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';
    	$delivery_date = isset($this->data['delivery_date'])?$this->data['delivery_date']:'';
    	$delivery_time = isset($this->data['delivery_time'])?$this->data['delivery_time']:'';
    	$payment_provider = isset($this->data['payment_provider'])?$this->data['payment_provider']:'';
    	
    	if(empty($delivery_date)){
    		$this->msg = $this->t("Delivery date is required");
    		$this->output();
    	}
    	
    	if(empty($payment_provider)){
    		$this->msg = $this->t("Payment provider is empty. please go back and try again");
    		$this->output();
    	}
    	
    	$full_delivery = "$delivery_date $delivery_time";    	
    	$delivery_day = strtolower(date("D",strtotime($full_delivery)));
    	
    	$delivery_time_formated = '';
    	if(!empty($delivery_time)){
    		$delivery_time_formated=date('h:i A',strtotime($delivery_time));
    	} else $delivery_time_formated = date('h:i A');
    	
    	if ( !Yii::app()->functions->isMerchantOpenTimes($this->merchant_id,$delivery_day,$delivery_time_formated)){
    		$date_close=date("F,d l Y h:ia",strtotime($full_delivery));
    		$this->msg = Yii::t("mobile2","Sorry but we are closed on [date_close]. Please check merchant opening hours.",array(
    		  '[date_close]'=>$date_close
    		));
    		$this->output();
    	}    	 
    	    	
    	/*CHECK IF DATE IS HOLIDAY*/
    	if ( $res_holiday =  Yii::app()->functions->getMerchantHoliday($this->merchant_id)){
    		if (in_array($delivery_date,$res_holiday)){
    		   $this->msg=Yii::t("mobile2","were close on [date]",array(
			   	  	   '[date]'=>FunctionsV3::prettyDate($delivery_date)
			   	));
			   	
			   	$close_msg=getOption($this->merchant_id,'merchant_close_msg_holiday');
			   	if(!empty($close_msg)){
	   	  	 	  $this->msg = Yii::t("default",$close_msg,array(
	   	  	 	   '[date]'=>FunctionsV3::prettyDate($delivery_date)
	   	  	 	  ));
	   	  	    }	
    			$this->output();	
    		}
    	}
    	    	
    	/*CHECK DELIVERY TIME PAST*/
    	if(!empty($delivery_date) && !empty($delivery_time)){
    		$time_1=date('Y-m-d g:i:s a');
    		$time_2="$delivery_date $delivery_time";
    		$time_2=date("Y-m-d g:i:s a",strtotime($time_2));
    		$time_diff=Yii::app()->functions->dateDifference($time_2,$time_1);    		    		
    		if (is_array($time_diff) && count($time_diff)>=1){
    			if ( $time_diff['hours']>0){	       	  	     	
	       	  	     $this->msg= mobileWrapper::timePastByTransaction($transaction_type);
	       	  	     $this->output(); 	  	     	
       	  	     }	       	  	
       	  	     if ( $time_diff['minutes']>0){	       	  	     	
	       	  	     $this->msg= mobileWrapper::timePastByTransaction($transaction_type);
	       	  	     $this->output();  	  	     	
       	  	     }	       	  	
    		}
    	}        	    	
    	   	
    	if($res=mobileWrapper::getCart($this->device_uiid)){
    		$cart=json_decode($res['cart'],true);
    		$card_fee = 0; $card_percentage=0;

    		/*CARD FEE*/
    		switch ($payment_provider) {
    			case "pyp":
    				if (FunctionsV3::isMerchantPaymentToUseAdmin($this->merchant_id)){
    					$card_fee=getOptionA('admin_paypal_fee');
    				} else {    					
    					$card_fee = getOption($this->merchant_id,'merchant_paypal_fee');
    				}	    	
    				break;
    				
    			case "paypal_v2":	
    			    if ( $credentials = PaypalWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;
    			   
    			case "stp":	
    			    if ( $credentials = StripeWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    		if(isset($credentials['card_percentage'])){
    			    			$card_percentage=$credentials['card_percentage']>0?$credentials['card_percentage']:0;
    			    		}    			    	
    			    	}
    			    }
    			   break;   
    			   
    			case "mercadopago":   
    			   if ( $credentials = mercadopagoWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;   
    			   
    			case "mollie":   
    			   if ( $credentials = MollieWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;      
    			 
    			case "pagseguro":   
    			   if ( $credentials = pagseguroWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;           
    			   
    		    case "selcompay":   
    			   if ( $credentials = selcompayWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;           	   
    			   
    			   
    		    case "kushi":   
    			   if ( $credentials = KushiWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;       
    			   
    			case "ideal":   
    			   if ( $credentials = iDEALWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;          
    			   
    			case "orange":   
    			   if ( $credentials = OrangeWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;        
    			   
    			case "redsys":   
    			   if ( $credentials = RedsysWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;                  
    			   
    			case "emspay":   
    			   if ( $credentials = EmspayWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;                     
    			   
    			case "fac":   
    			   if ( $credentials = FacWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;       

    			case "rave":   
    			   if ( $credentials = RaveWrapper::getCredentials($this->merchant_id)){
    			    	if ($credentials['card_fee']>0.0001){
    			    		$card_fee = $credentials['card_fee'];
    			    	}
    			    }
    			   break;                                 
    		
    			default:
    				break;
    		}
    		
    		$data = array(
			  'delivery_type'=>$transaction_type,
			  'merchant_id'=>$this->merchant_id,
			  'card_fee'=>$card_fee,
			);
			if($card_percentage>0){
			   $data['card_percentage']=$card_percentage;
			}    
			
			$voucher_details = !empty($res['voucher_details'])?json_decode($res['voucher_details'],true):false;	
			if(is_array($voucher_details) && count($voucher_details)>=1){
				$data['voucher_name']=$voucher_details['voucher_name'];
				$data['voucher_amount']=$voucher_details['amount'];
				$data['voucher_type']=$voucher_details['voucher_type'];
			}
			
			if($res['tips']>0.0001){
				$data['cart_tip_percentage']=$res['tips'];
				$data['tip_enabled']=2;
				$data['tip_percent']=$res['tips'];
			}		
			
			/*POINTS*/
			if($res['points_amount']>0.0001){
				$data['points_amount']=$res['points_amount'];
			}								
			//dump($data);die();
			
			/*DELIVERY FEE*/
			unset($_SESSION['shipping_fee']);
			if($res['delivery_fee']>0.0001){
				$data['delivery_charge']=$res['delivery_fee'];
			}
			
			//dump($data);
			Yii::app()->functions->displayOrderHTML( $data,$cart );
			$code = Yii::app()->functions->code;
		    $msg  = Yii::app()->functions->msg;
		    if ($code==1){
		    	$raw = Yii::app()->functions->details['raw'];
		    	
		    	//dump($raw['total']['card_fee']);
		    	
		        /*EURO TAX*/
			    $is_apply_tax = 0;
			    if(EuroTax::isApplyTax($this->merchant_id)){
			   	   $new_total = EuroTax::computeWithTax($raw, $this->merchant_id);
			   	   $raw['total']=$new_total;			
			   	   $is_apply_tax=1;   	   
			    }
			    /*EURO TAX*/		
			    
			    $donot_apply_tax_delivery = getOption($this->merchant_id,'merchant_tax_charges');
				if(empty($donot_apply_tax_delivery)){
					$donot_apply_tax_delivery=1;
				}
				
				if($card_percentage>0){
					$card_fee = (float) $raw['total']['card_fee'];
				}
				
				$params = array(
				  'merchant_id'=>$this->merchant_id,				  
				  'client_id'=>$client_id,
				  'json_details'=>$res['cart'],
				  'trans_type'=>$transaction_type,
				  'payment_type'=>$this->data['payment_provider'],
				  'sub_total'=>$raw['total']['subtotal'],
				  'tax'=>$raw['total']['tax'],
				  'taxable_total'=>$raw['total']['taxable_total'],
				  'total_w_tax'=>isset($raw['total']['total'])?$raw['total']['total']:0,
				  'delivery_charge'=>isset($raw['total']['delivery_charges'])?$raw['total']['delivery_charges']:0,
				  'delivery_date'=>$delivery_date,
				  'delivery_time'=>$delivery_time,
				  'delivery_asap'=>isset($this->data['delivery_asap'])?$this->data['delivery_asap']:'',
				  'date_created'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR'],
				  'delivery_instruction'=>isset($res['delivery_instruction'])?$res['delivery_instruction']:'',
				  'cc_id'=>isset($this->data['cc_id'])?$this->data['cc_id']:'',
				  'order_change'=>isset($this->data['order_change'])?$this->data['order_change']:0,
				  'payment_provider_name'=>'',
				  'card_fee'=>$card_fee,
				  'packaging'=>$raw['total']['merchant_packaging_charge'],
				  'donot_apply_tax_delivery'=>$donot_apply_tax_delivery,
				  'order_id_token'=>FunctionsV3::generateOrderToken(),
				  'request_from'=>"mobileapp2",
				  'apply_food_tax'=>$is_apply_tax,				  
				);

				$order_id_token = $params['order_id_token'];
				
				/*TIPS*/
				if(isset($raw['total']['tips'])){
					if($raw['total']['tips']>0.0001){
						$params['cart_tip_percentage']= $raw['total']['cart_tip_percentage'];
						$params['cart_tip_value']= $raw['total']['tips'];
					}				
				}	
				
				switch ($transaction_type) {
					case "dinein":
						$params['dinein_number_of_guest'] = isset($this->data['dinein_number_of_guest'])?$this->data['dinein_number_of_guest']:'';
						$params['dinein_special_instruction'] = isset($this->data['dinein_special_instruction'])?$this->data['dinein_special_instruction']:'';
						
						$params['dinein_table_number'] = isset($this->data['dinein_table_number'])?$this->data['dinein_table_number']:'';
												
						$contact_phone = isset($this->data['contact_phone'])?$this->data['contact_phone']:'';
						if(!empty($contact_phone)){
						    Yii::app()->db->createCommand()->update("{{client}}",array(
							 'contact_phone'=>$contact_phone
							),
			          	    'client_id=:client_id',
			          	     array(
			          	      ':client_id'=>(integer)$client_id
			          	     )
			          	   );
						}										
						break;
						
					case "pickup":
						$pickup_contact = isset($this->data['pickup_contact'])?$this->data['pickup_contact']:'';
						if(!empty($pickup_contact)){
							Yii::app()->db->createCommand()->update("{{client}}",array(
							 'contact_phone'=>$pickup_contact
							),
			          	    'client_id=:client_id',
			          	     array(
			          	      ':client_id'=>(integer)$client_id
			          	     )
			          	   );
						}						
						break;
						
					case "delivery":
						$delivery_asap = '';
						if(isset($this->data['delivery_asap'])){
							$delivery_asap = $this->data['delivery_asap']=="true"?1:'';
							$params['delivery_asap'] = $delivery_asap;
						}
						break;
				
					default:
						break;
				}
					
				
				/*DEFAULT ORDER STATUS*/				
				$default_order_status=getOption($this->merchant_id,'default_order_status');										
				switch ($payment_provider) {								
					case "cod":
					case "obd":
						$params['status'] =!empty($default_order_status)?$default_order_status:'pending';
						break;
					case "ccr":
					case "ocr":
						 $params['cc_id'] = isset($this->data['cc_id'])?$this->data['cc_id']:'';	
						 $params['status']= !empty($default_order_status)?$default_order_status:'pending';
						 break;
								
					case "pyr":	 		 
					     $params['payment_provider_name'] = isset($this->data['selected_card'])?$this->data['selected_card']:'';	
						 $params['status']= !empty($default_order_status)?$default_order_status:'pending';
						 break;
						 
					default:			
					    $params['status']=initialStatus();
						break;
				}
				
				/*PROMO*/	    				
				//dump($raw);
				if (isset($raw['total']['discounted_amount'])){
    				if ($raw['total']['discounted_amount']>=0.0001){	    					
    				    $params['discounted_amount']=$raw['total']['discounted_amount'];
    				    $params['discount_percentage']=$raw['total']['merchant_discount_amount'];
    				}
				}
				
				/*VOUCHER*/
				if(!empty($res['voucher_details'])){
					$voucher_details = !empty($res['voucher_details'])?json_decode($res['voucher_details'],true):false;	
					if(is_array($voucher_details) && count($voucher_details)>=1){
						$params['voucher_amount']=$voucher_details['amount'];
			         	$params['voucher_code']=$voucher_details['voucher_name'];
			         	$params['voucher_type']=$voucher_details['voucher_type'];
					}
				}
				
				/*POINTS*/
				if($res['points_amount']>0.0001){
					$params['points_discount']=$res['points_amount'];
				}	
				
				/*SET COMMISSION*/
				if ( Yii::app()->functions->isMerchantCommission($this->merchant_id)){
					$admin_commision_ontop=Yii::app()->functions->getOptionAdmin('admin_commision_ontop');
					if ( $com=Yii::app()->functions->getMerchantCommission($this->merchant_id)){
	            		$params['percent_commision']=$com;			            		
	            		$params['total_commission']=($com/100)*$params['total_w_tax'];
	            		$params['merchant_earnings']=$params['total_w_tax']-$params['total_commission'];
	            		if ( $admin_commision_ontop==1){
	            			$params['total_commission']=($com/100)*$params['sub_total'];
	            			$params['commision_ontop']=$admin_commision_ontop;			            		
	            			$params['merchant_earnings']=$params['sub_total']-$params['total_commission'];
	            		}
	            	}	
	            	
	            	/** check if merchant commission is fixed  */
			        $merchant_com_details=Yii::app()->functions->getMerchantCommissionDetails($this->merchant_id);	
			        if ( $merchant_com_details['commision_type']=="fixed"){
	            		$params['percent_commision']=$merchant_com_details['percent_commision'];
	            		$params['total_commission']=$merchant_com_details['percent_commision'];
	            		$params['merchant_earnings']=$params['total_w_tax']-$merchant_com_details['percent_commision'];
	            		$params['commision_type']='fixed';
	            		
	            		if ( $admin_commision_ontop==1){			            		
	            		    $params['merchant_earnings']=$params['sub_total']-$merchant_com_details['percent_commision'];
	            		}
	            	} 
				}
				/*END COMMISSION*/
				
				if(!is_numeric($params['cc_id'])){
					unset($params['cc_id']);
				}
				if(!is_numeric($params['order_change'])){
					unset($params['order_change']);
				}
				
				/*BEGIN INSERT ORDER*/				
				if(!is_numeric($params['sub_total'])){
					$params['sub_total']=0;
				}			
				if(!is_numeric($params['tax'])){
					$params['tax']=0;
				}			
				if(!is_numeric($params['taxable_total'])){
					$params['taxable_total']=0;
				}			
				if(!is_numeric($params['total_w_tax'])){
					$params['total_w_tax']=0;
				}
				
				if(isset($params['order_change'])){
					if(!is_numeric($params['order_change'])){
						$params['order_change']=0;
					}			
				}
				if(!is_numeric($params['card_fee'])){
					$params['card_fee']=0;
				}			
				if(!is_numeric($params['packaging'])){
					$params['packaging']=0;
				}			
				if(!is_numeric($params['donot_apply_tax_delivery'])){
					unset($params['donot_apply_tax_delivery']);
				}			
				if(!is_numeric($params['apply_food_tax'])){
					unset($params['apply_food_tax']);
				}			
				
				if(isset($params['percent_commision'])){
					if(!is_numeric($params['percent_commision'])){
						$params['percent_commision']=0;
					}			
				}
				
				if(isset($params['total_commission'])){
					if(!is_numeric($params['total_commission'])){
						$params['total_commission']=0;
					}			
				}
				
				if(isset($params['merchant_earnings'])){
					if(!is_numeric($params['merchant_earnings'])){
						$params['merchant_earnings']=0;
					}			
				}		
				
							
				if(Yii::app()->db->createCommand()->insert("{{order}}",$params)){	
					$order_id=Yii::app()->db->getLastInsertID();
										
					$params_history=array(
    				  'order_id'=>$order_id,
    				  'status'=>initialStatus(),    	
    				  'remarks'=>'',
    				  'date_created'=>FunctionsV3::dateNow(),
    				  'ip_address'=>$_SERVER['REMOTE_ADDR']
    				);    				
    				Yii::app()->db->createCommand()->insert("{{order_history}}",$params_history);
					
					$next_step = "receipt";
					
					/*SAVE ITEM */					
					foreach ($raw['item'] as $val) {								
						$params_order_details=array(
						  'order_id'=>isset($order_id)?$order_id:'',
						  'client_id'=>$client_id,
						  'item_id'=>isset($val['item_id'])?$val['item_id']:'',
						  'item_name'=>isset($val['item_name'])?$val['item_name']:'',
						  'order_notes'=>isset($val['order_notes'])?$val['order_notes']:'',
						  'normal_price'=>isset($val['normal_price'])?$val['normal_price']:'',
						  'discounted_price'=>isset($val['discounted_price'])?$val['discounted_price']:'',
						  'size'=>isset($val['size_words'])?$val['size_words']:'',
						  'qty'=>isset($val['qty'])?$val['qty']:'',		    					  
						  'addon'=>isset($val['sub_item'])?json_encode($val['sub_item']):'',
						  'cooking_ref'=>isset($val['cooking_ref'])?$val['cooking_ref']:'',
						  'ingredients'=>isset($val['ingredients'])?json_encode($val['ingredients']):'',
						  'non_taxable'=>isset($val['non_taxable'])?$val['non_taxable']:1
						);
						/*inventory*/
						$new_fields=array('size_id'=>"size_id");
                        if ( FunctionsV3::checkTableFields('order_details',$new_fields)){
                        	$params_order_details['size_id'] = isset($val['size_id'])? (integer) $val['size_id']:0;
                        	$params_order_details['cat_id'] = isset($val['category_id'])? (integer) $val['category_id']:0;
                        }												
						Yii::app()->db->createCommand()->insert("{{order_details}}",$params_order_details);
												
						/*inventory*/
    					if (FunctionsV3::checkIfTableExist('order_details_addon')){
	    					if(isset($val['sub_item'])){
		    					if(is_array($val['sub_item']) && count($val['sub_item'])>=1){
		    						foreach ($val['sub_item'] as $sub_item_data) {
		    							Yii::app()->db->createCommand()->insert("{{order_details_addon}}",array(
		    							  'order_id'=>$order_id,
		    							  'subcat_id'=>$sub_item_data['subcat_id'],
		    							  'sub_item_id'=>$sub_item_data['sub_item_id'],
		    							  'addon_price'=>$sub_item_data['addon_price'],
		    							  'addon_qty'=>$sub_item_data['addon_qty'],
		    							));
		    						}
		    					}		    				
	    					}
    					}
    					
					}
					
					$params_address = array();
					
					/*SAVE DELIVERY ADDRESS*/
					if ($transaction_type=="delivery"){						
						$params_address=array(	    				  
	    				  'street'=>isset($res['street'])?$res['street']:'',
	    				  'city'=>isset($res['city'])?$res['city']:'',
	    				  'state'=>isset($res['state'])?$res['state']:'',
	    				  'zipcode'=>isset($res['zipcode'])?$res['zipcode']:'',
	    				  'location_name'=>isset($res['location_name'])?$res['location_name']:'',
	    				  'contact_phone'=>isset($res['contact_phone'])?$res['contact_phone']:'',
	    				  'country'=>isset($res['country_code'])?$res['country_code']:'',
	    				  'google_lat'=>isset($res['delivery_lat'])?$res['delivery_lat']:'',
	    				  'google_lng'=>isset($res['delivery_long'])?$res['delivery_long']:'',
	    				  'opt_contact_delivery'=>isset($this->data['opt_contact_delivery'])?(integer)$this->data['opt_contact_delivery']:0
	    				);		    					    				
					} elseif ( $transaction_type=="pickup"){
						$params_address = array(						  
	    				  'contact_phone'=>isset($this->data['pickup_contact'])?$this->data['pickup_contact']:''	    				  
						);
					} elseif ( $transaction_type=="dinein"){
						$params_address = array(						  
	    				  'contact_phone'=>isset($this->data['contact_phone'])?$this->data['contact_phone']:'',
	    				  'dinein_number_of_guest'=>isset($this->data['dinein_number_of_guest'])?$this->data['dinein_number_of_guest']:'',
	    				  'dinein_special_instruction'=>isset($this->data['dinein_special_instruction'])?$this->data['dinein_special_instruction']:'',
	    				  'dinein_table_number'=>isset($this->data['dinein_table_number'])?$this->data['dinein_table_number']:''
						);
					}
										
	    		    $params_address['order_id'] = (integer)$order_id;
	    		    $params_address['client_id'] = (integer)$client_id;
					$params_address['first_name'] = $customer_first_name;
					$params_address['last_name'] = $customer_last_name;
					$params_address['contact_email'] = $customer_email;
					$params_address['date_created'] = FunctionsV3::dateNow();
					$params_address['ip_address'] = $_SERVER['REMOTE_ADDR'];
					 					
					Yii::app()->db->createCommand()->insert("{{order_delivery_address}}",$params_address);
					
					/*SAVE ADDRESS*/			
					if(isset($res['save_address'])){									
					if($res['save_address']==1){  	
						if($search_mode=="location"){							
							if( !LocationWrapper::isAddressBookExist(
							   $client_id,
							   isset($res['street'])?$res['street']:'',
							   $res['state_id'],
							   $res['city_id'],
							   $res['area_id']
							)){
								$params_address_book=array(
								  'client_id'=>$client_id,
								  'street'=>$res['street'],
								  'location_name'=>$res['location_name'],
								  'state_id'=>$res['state_id'],
								  'city_id'=>$res['city_id'],
								  'area_id'=>$res['area_id'],
								  'date_created'=>FunctionsV3::dateNow(),
							      'latitude'=>$res['delivery_lat'],
								  'longitude'=>$res['delivery_long'],
								  'ip_address'=>$_SERVER['REMOTE_ADDR']
								);																
								Yii::app()->db->createCommand()->insert("{{address_book_location}}",$params_address_book);
							}							
						} else {
							if (!mobileWrapper::getBookAddress($client_id,$res['street'],$res['city'],$res['state'])){
								$params_address_book = array(
								  'client_id'=>$client_id,
								  'street'=>$res['street'],
								  'city'=>$res['city'],
								  'state'=>$res['state'],
								  'zipcode'=>$res['zipcode'],
								  'location_name'=>$res['location_name'],
								  'country_code'=>getOptionA('admin_country_set'),
								  'as_default'=>1,
								  'date_created'=>FunctionsV3::dateNow(),
								  'latitude'=>$res['delivery_lat'],
								  'longitude'=>$res['delivery_long'],
								  'ip_address'=>$_SERVER['REMOTE_ADDR']
								);															
								Yii::app()->db->createCommand()->insert("{{address_book}}",$params_address_book);
							} //else echo 'd1';
						}
					} //else echo 'd2';
					} //else echo 'd3';
										
					$this->code = 1;
				    $this->msg = Yii::t("mobile2","Your order has been placed. Reference # [order_id]",array(
				      '[order_id]'=>$order_id
				    ));
					
					$provider_credentials=array();
					$redirect_url='';
					
					/*SAVE POINTS*/
					switch ($payment_provider) {
						/*case "cod":
						case "ccr":
					    case "ocr":				
					    case "pyr":
					    case "obd":
					    break;*/
					    
					    default:					    	
					    	mobileWrapper::savePoints(
					    	  $this->device_uiid,
					    	  $client_id,
					    	  $this->merchant_id,
					    	  $order_id,
					    	  'initial_order'
					    	);
					    	break;
					}
					
					
					/*PAYMENT DATA*/
					switch ($payment_provider) {
						case "cod":
						case "ccr":
					    case "ocr":				
					    case "pyr":	    					    
					    					          					          					         
					          mobileWrapper::sendNotification($order_id);	
					          mobileWrapper::clearCartByCustomerID($client_id);
					          mobileWrapper::executeAddons($order_id);
					          
							  break;	
							  
					    case "obd":
					    	  FunctionsV3::sendBankInstructionPurchase(
	    					      $this->merchant_id,
	    					      $order_id,
	    					      isset($params['total_w_tax'])?$params['total_w_tax']:0,
	    					      $client_id
	    					  );
	    					  	    					  	    					  
	    					  mobileWrapper::sendNotification($order_id);
	    					  mobileWrapper::clearCart($this->device_uiid);
	    					  mobileWrapper::executeAddons($order_id);
	    					  				    	  				    	 
					    	  break;
					    	  
					    case "rzr":	  
					       $next_step = "init_".$payment_provider;
					       $provider_credentials = FunctionsV3::razorPaymentCredentials($this->merchant_id);
					       if(!$provider_credentials){
					       	  $this->code = 2;
					          $this->msg = $this->t("Merchant payment credentials not properly set");
					       }
					       break;
					       
					    case "btr":
					       $next_step='init_webview';
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/braintree?id=".urlencode($order_id)."&lang=$lang_code";
					       $redirect_url.= "&device_uiid=".urlencode($this->device_uiid);				       
					    	break;
					    	
					    case "paypal_v2":	
					       $next_step='init_webview';
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/paypal?id=".urlencode($order_id)."&lang=$lang_code";
					       $redirect_url.= "&device_uiid=".urlencode($this->device_uiid);
					       break;
					       
					    case "stp":	
					       $next_step='init_webview';
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/stripe?id=".urlencode($order_id)."&lang=$lang_code";
					       $redirect_url.= "&device_uiid=".urlencode($this->device_uiid);
					       break;   
					       
					    case "mercadopago":	
					       $next_step='init_webview';
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/mercadopago?id=".urlencode($order_id)."&lang=$lang_code";
					       $redirect_url.= "&device_uiid=".urlencode($this->device_uiid);		       
					       break;      
					       
					    case "vog":	
					       $next_step='init_webview';
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/voguepay?id=".urlencode($order_id)."&lang=$lang_code";
					       $redirect_url.= "&device_uiid=".urlencode($this->device_uiid);				       
					       break;         
					       
					    case "mollie":	
					       $next_step='init_webview';
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/mollie?id=".urlencode($order_id)."&lang=$lang_code";
					       $redirect_url.= "&device_uiid=".urlencode($this->device_uiid);
					       break;   
					       
					    case "pagseguro":	
					       $next_step='init_webview';
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/pagseguro?id=".urlencode($order_id)."&lang=$lang_code";
					       $redirect_url.= "&device_uiid=".urlencode($this->device_uiid);
					       break;      
							  					    
					       
					    case "ideal":	
					       $next_step='init_webview';					       
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/ideal?id=".urlencode($order_id)."&lang=$lang_code";					       
					       break;      
							  					    
					    case "orange":	
					       $next_step='init_webview';					       
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/orange?id=".urlencode($order_id)."&lang=$lang_code";					       
					       break;         
					       
					    case "payu":   
					       $next_step='init_webview';					       
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/payu?id=".urlencode($order_id)."&lang=$lang_code";					       
					       break;         
					       
					    case "redsys":   
					       $next_step='init_webview';					       
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/redsys?id=".urlencode($order_id)."&lang=$lang_code";					       
					       break;            
					          
					    case "emspay":   
					       $next_step='init_webview';					       
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/emspay?id=".urlencode($order_id)."&lang=$lang_code";					       
					       break;            
					       
					    case "fac":   
					       $next_step='init_webview';					       
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/fac?id=".urlencode($order_id)."&lang=$lang_code";					       
					       break;      
					       
					     case "rave":   
					       $next_step='init_webview';					       
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/rave?id=".urlencode($order_id)."&lang=$lang_code";					       
					       break;              
					       
					     case "paystack":   
					       $next_step='init_webview';					       
					       $redirect_url = websiteUrl()."/".APP_FOLDER."/paystack?id=".urlencode($order_id)."&lang=$lang_code";					       
					       break;                
					       
					     case "sage":  					       
					       $next_step="init_sage";   
					       if ( $cards = SageWrapper::getCreditCards($client_id) ){
					       	    $next_step="sage_cards";   
					       }					       
					       break;                  
					          
						default:						
						    $next_step = "init_".$payment_provider;
							break;
					}
					
				    $client_info = array( 
				      'first_name'=>$client_info['first_name'],
				      'last_name'=>$client_info['last_name'],
				      'email_address'=>$client_info['email_address'],
				      'contact_phone'=>$client_info['contact_phone'],				      
				    );
				    
				    /*$payment_description = Yii::t("mobile2","Payment to merchant [merchant_name]",array(
				      '[merchant_name]'=>clearString($merchant_info['restaurant_name'])
				    ));*/
				    
				    $payment_description = Yii::t("mobile2","Payment to merchant [merchant_name]. Order ID#[order]",array(
					  '[merchant_name]'=>clearString($merchant_info['restaurant_name']),
					  '[order]'=>$order_id
					));
				    
				    $total = number_format($params['total_w_tax'],2,'.','');
				    
				    $this->details=array(
				      'order_id'=>$order_id,
				      'total_amount'=>$params['total_w_tax'],
				      'total_amount_by_100'=>$total*100,
				      'total_amount_formatted'=>$total,
				      'card_fee'=>(float)$params['card_fee'],
				      'sub_less_card_fee'=>(float)$params['total_w_tax']-(float)$params['card_fee'],
				      'payment_provider'=>$payment_provider,
				      'next_step'=>$next_step,
				      'currency_code'=>Yii::app()->functions->adminCurrencyCode(),
				      'payment_description'=>$payment_description,
				      'merchant_name'=>clearString($merchant_info['restaurant_name']),
				      'provider_credentials'=>$provider_credentials,
				      'redirect_url'=>$redirect_url,
				      'client_info'=>$client_info
				    );
				    
				} else $this->msg = $this->t("Something went wrong cannot insert records. please try again later");
		    	
		    } else $this->msg = $msg;
    		   		
    	} else $this->msg = $this->t("Cart is empty");    	
    	
		$this->output();
	}
	
	public function actionverifyCustomerToken()
	{		
		$user_token = isset($this->data['user_token'])?$this->data['user_token']:'';
		$action = isset($this->data['action'])?$this->data['action']:'';
		if($res = mobileWrapper::getCustomerByToken($user_token)){		
										
			$email_address = $res['email_address'];
			$contact_phone = $res['contact_phone'];
					
			if (mobileWrapper::checkBlockAccount($email_address,$contact_phone)){
				$this->msg = mt("account blocked");
				$this->details=array(
				  'action'=>$action,
				  'social_strategy'=>$res['social_strategy']
				);			
				$this->output();
			}
						
			$this->data['client_id'] = $res['client_id'];			
			mobileWrapper::registeredDevice($this->data);	
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details=array(
			  'action'=>$action,
			  'social_strategy'=>$res['social_strategy']
			);			
		} else {
		    $this->msg = $this->t("invalid token");	
		    $this->details=array(
			  'action'=>$action,			  
			);			
		}
		$this->output();
	}
	
	public function actionGetAddressFromCart()
	{	
		$country_list = require_once('CountryCode.php');
		$default_country_code = getOptionA('admin_country_set');
		
		$customer_phone = '';
		if($client = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$customer_phone=$client['contact_phone'];
		}
			
		if($resp=mobileWrapper::getCart($this->device_uiid)){			
			$this->code = 1;
			$this->msg = "OK";
			
			$lat = isset($this->data['lat'])?$this->data['lat']:'';
			$lng = isset($this->data['lng'])?$this->data['lng']:'';
			
			if(empty($resp['street']) && empty($resp['city']) && !empty($lat) && !empty($lng) ){
				if ( $res_location = mobileWrapper::getRecentLocation($this->device_uiid,$lat,$lng)){					
				    $resp['street'] = $res_location['street'];
				    $resp['city'] = $res_location['city'];
				    $resp['state'] = $res_location['state'];
				    $resp['zipcode'] = $res_location['zipcode'];
				    $resp['location_name'] = $res_location['location_name'];
				    $resp['delivery_lat'] = $res_location['latitude'];
				    $resp['delivery_long'] = $res_location['longitude'];
				}
			}	
			
			if(empty($resp['contact_phone'])){
				if(!empty($customer_phone)){
				   	$resp['contact_phone']=$customer_phone;
				}
			}	
						
			$this->details = array(
			  'street'=>$resp['street'],
			  'city'=>$resp['city'],
			  'state'=>$resp['state'],
			  'zipcode'=>$resp['zipcode'],
			  'delivery_instruction'=>$resp['delivery_instruction'],
			  'location_name'=>$resp['location_name'],
			  'contact_phone'=>$resp['contact_phone'],
			  'country_code'=>!empty($resp['country_code'])?$resp['country_code']:$default_country_code,
			  'delivery_lat'=>$resp['delivery_lat'],
			  'delivery_long'=>$resp['delivery_long'],
			  'save_address'=>$resp['save_address'],
			  'customer_phone'=>$customer_phone,
			  'country_list'=>$country_list
			);
			
		} else {
			$this->msg = mt("cart not available");
			$this->details = array(
			  'customer_phone'=>$customer_phone,
			  'country_code'=>!empty($resp['country_code'])?$resp['country_code']:$default_country_code,
			  'country_list'=>$country_list
			);
		}
		$this->output();
	}
	
	public function actionsetAddressBook()
	{
		$addressbook_id = isset($this->data['addressbook_id'])?$this->data['addressbook_id']:'';		
		if($addressbook_id>0){
			if ( $res = Yii::app()->functions->getAddressBookByID($addressbook_id)){								
				
				if(empty($res['latitude']) && empty($res['latitude'])){
					$this->msg = mt("This address book has no latitude and longitude. update your address book under your account.");
					$this->output();
				}
				
				$this->data['country']= $res['country_code'];
				$this->data['country_code']= $res['country_code'];
				$this->data['street']= $res['street'];
				$this->data['city']= $res['city'];
				$this->data['state']= $res['state'];
				$this->data['zipcode']= $res['zipcode'];
				$this->data['save_address']= '';
				$this->data['location_name']= $res['location_name'];				
				$this->data['lat']= $res['latitude'];
				$this->data['lng']= $res['longitude'];

				$this->actionsetDeliveryAddress();
				
			} else $this->msg = $this->t("Address not available. please try again later");
		} else $this->msg = $this->t("Invalid address book id");
		$this->output();
	}
	
	public function actionOrderList()
	{		
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];		
		
		$pagelimit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $pagelimit;
        } else  $page = 0; 

        $paginate_total=0; 
        $limit="LIMIT $page,$pagelimit"; 
                
        $cancel_order_enabled = getOptionA('cancel_order_enabled');		
		$website_review_type = getOptionA('website_review_type');
		$review_baseon_status = getOptionA('review_baseon_status');	
		$merchant_can_edit_reviews = getOptionA('merchant_can_edit_reviews');
		if($website_review_type==1){
			$review_baseon_status = getOptionA('review_merchant_can_add_review_status');
		}	
					
		$date_now=date('Y-m-d g:i:s a');	 
		
		$and='';		
		$tab = isset($this->data['tab'])?$this->data['tab']:'';		
		$and = mobileWrapper::getOrderTabsStatus($tab);
        		        
		$stmt="
		SELECT SQL_CALC_FOUND_ROWS 
		a.order_id,
		a.client_id,
		a.merchant_id,
		a.trans_type,
		a.payment_type,
		a.date_created,
		a.date_created as date_created_raw,
		a.total_w_tax,
		a.status,
		a.status as status_raw,		
		a.request_cancel,
		a.order_locked,
		a.request_cancel_status,
		b.restaurant_name as merchant_name,
		b.logo,
		
		(
		select rating from {{review}}
		where order_id = a.order_id
		and status='publish'		
		limit 0,1
		) as rating
		
		FROM
		{{order}} a
		left join {{merchant}} b
        ON
        a.merchant_id = b.merchant_id
                
		WHERE a.client_id=".FunctionsV3::q($client_id)."
		
		AND a.status NOT IN ('".initialStatus()."')

		$and	
		
		ORDER BY a.order_id DESC
		$limit
		";			
		
						
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$total_records=0;			
			if($resp = Yii::app()->db->createCommand("SELECT FOUND_ROWS() as total_records")->queryRow()){	
				$total_records=$resp['total_records'];
			}					
			$paginate_total = ceil( $total_records / $pagelimit );
			
			$data = array();
			foreach ($res as $val) {		
				$val['merchant_name'] = clearString($val['merchant_name']);
				$val['status'] = mt($val['status']);
				$val['transaction'] = mobileWrapper::t("[trans_type] #[order_id]",array(
				  '[trans_type]'=>t($val['trans_type']),
				  '[order_id]'=>t($val['order_id']),
				));
				$val['date_created'] = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
				$val['total_w_tax'] = FunctionsV3::prettyPrice($val['total_w_tax']);
				$val['payment_type'] = mobileWrapper::t(FunctionsV3::prettyPaymentTypeTrans($val['trans_type'],$val['payment_type']));
				$val['logo']=mobileWrapper::getImage($val['logo']);
				
				$add_review = false;		
				if(mobileWrapper::canReviewOrder($val['status_raw'],$website_review_type,$review_baseon_status)){
				   $add_review=true;
				}				
				
				if($add_review){		
					if ($val['client_id']==$client_id){		    		
		    			$date_diff=Yii::app()->functions->dateDifference(
		    			date('Y-m-d g:i:s a',strtotime($val['date_created_raw']))
		    			,$date_now);
		    			if(is_array($date_diff) && count($date_diff)>=1){
		    				if ($date_diff['days']>=5){
		    				   $add_review=false;
		    				}
		    			}	    	
					} else $add_review=false;
				}
				
				if($website_review_type==1){
					if($val['rating']>0){
						if($merchant_can_edit_reviews=="yes"){
						   	$add_review=false;
						}
					}				
				}
								
				$val['add_review'] = $add_review;
				
				$show_cancel = false; $cancel_status='';
				if(FunctionsV3::canCancelOrderNew($val['request_cancel'],$val['date_created_raw'],$val['status_raw'],$val['order_locked'],$val['request_cancel_status'],$cancel_order_enabled)){
					if($val['request_cancel']==1){
						$cancel_status = mt("Pending for review");
					} else $show_cancel=true;									
				}	
				
				if ($val['request_cancel_status']!='pending'){					
					$cancel_status = Yii::t("mobile2","Request cancel : [status]",array(
					  '[status]'=>t($val['request_cancel_status'])
					));
				}		
				
				$val['add_cancel']=$show_cancel;
				$val['cancel_status']=$cancel_status;

				$val['add_track']=true;
				
				$data[]=$val;
			}
			
			$this->code = 1;
			$this->msg="OK";
			$this->details = array( 
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$paginate_total,
			  'data'=>$data
			);
			
		} else {
			
			$msg1 = $this->t("Your order list is empty");
			$msg2 = $this->t("Make your first order");
			
			switch ($tab) {
				case "processing":		
				    $msg1 = $this->t("There is no processing order");			        
					break;
			
				case "completed":			
				    $msg1 = $this->t("There is no completed order");	
					break;
					
				case "cancelled":				
				    $msg1 = $this->t("There is no cancelled order");	
					break;
							
				default:
					break;
			}
			
			$this->code = 6;
			$this->msg = $msg1;
			$this->details = array(
			   'element'=>".order_loader",
        	   'element_list'=>"#order_list_item",
        	   'message'=>$msg2
			);
		}
        
		$this->output();
	}
	
    public function actionBookingList()
	{				
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];		
		
		$pagelimit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $pagelimit;
        } else  $page = 0; 

        $paginate_total=0; 
        $limit="LIMIT $page,$pagelimit"; 
        
        $and='';
        $tab = isset($this->data['tab'])?$this->data['tab']:'';		        
        switch ($tab) {        	
        	case "all":
        		break;
        	default:
        		$and=" AND a.status=".FunctionsV3::q($tab)." ";
        		break;
        }
        
        $booking_cancel_days = getOptionA('booking_cancel_days');
        $booking_cancel_hours = getOptionA('booking_cancel_hours');
        $booking_cancel_minutes = getOptionA('booking_cancel_minutes');
        		     
		$stmt="
		SELECT SQL_CALC_FOUND_ROWS 
		a.booking_id,
		a.merchant_id,
		a.number_guest,
		a.status,
		a.status as status_raw,
		a.date_created,
		a.request_cancel,
		b.restaurant_name as merchant_name,
		b.logo
		
		FROM
		{{bookingtable}} a
		left join {{merchant}} b
        ON
        a.merchant_id = b.merchant_id
                
		WHERE a.client_id=".FunctionsV3::q($client_id)."			
		$and
		ORDER BY a.booking_id DESC
		$limit
		";					
		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			
			$total_records=0;						
			if($resp = Yii::app()->db->createCommand("SELECT FOUND_ROWS() as total_records")->queryRow()){			
				$total_records=$resp['total_records'];
			}					
			$paginate_total = ceil( $total_records / $pagelimit );
			
			$data = array();
			foreach ($res as $val) {		
				$val['status'] = mt($val['status']);
				
				if($val['status_raw']=="request_cancel_booking"){
					$val['status'] = mt("Request cancel");
				} elseif ( $val['status_raw']=="cancel_booking_approved" ){
				   	$val['status'] = mt("Cancel approved");
				}			
				
				$val['number_guest'] = mobileWrapper::t("No. of guest [count]",array(
				  '[count]'=> $val['number_guest']
				));
				$val['booking_ref'] = mobileWrapper::t("Booking ID#[booking_id]",array(
				  '[booking_id]'=> $val['booking_id']
				));
				$val['date_created'] = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
				$val['logo']=mobileWrapper::getImage($val['logo']);
				
				$ratings = Yii::app()->functions->getRatings($val['merchant_id']);
				
				$ratings['review_count'] = mobileWrapper::t("[count] reviews",array(
	 			  '[count]'=>$ratings['votes']
	 			));
	 			$val['rating']=$ratings;

	 			
	 			$val['can_cancel'] = 0;
	 			$can_cancel = mobileWrapper::canCancel($val['date_created'],$booking_cancel_days,$booking_cancel_hours,$booking_cancel_minutes);
	 			if($can_cancel){
	 			   if($val['request_cancel']<=0){
	 			   	  if($val['status']=='pending'){
	 			   	  	  $val['can_cancel'] = 'cancel_booking';
	 			   	  } else {
	 			   	  	  $val['can_cancel'] = 'cancel_booking_request_sent';
	 			   	  }			   
	 			   }	
	 			} else {
	 				if($val['request_cancel']>0){
	 				   	$val['can_cancel'] = 'cancel_booking_request_sent';
	 				}
	 			}	 			 			
	 				 				 		
				$data[]=$val;
			}
			
			$this->code = 1;
			$this->msg="OK";
			$this->details = array( 
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$paginate_total,
			  'data'=>$data
			);
			
		} else {			
			
			$msg1 = $this->t("Your booking list is empty");
			$msg2 = $this->t("Make your first booking");
			if($tab=="pending"){
				$msg1 = $this->t("You have no pending booking");
			} elseif ( $tab=="approved"){
				$msg1 = $this->t("You have no approved booking");
			} elseif ( $tab=="denied"){
				$msg1 = $this->t("You have no denied booking");
			}
			
			$this->code = 6;
			$this->msg = $msg1;
								
			$this->details = array(
			   'element'=>".booking_loader",
        	   'element_list'=>"#booking_history_item",
        	   'message'=>$msg2
			);
						
		}
        
		$this->output();
	}	
	
    public function actionFavoriteList()
	{						
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];		
		
		$pagelimit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $pagelimit;
        } else  $page = 0; 

        $paginate_total=0; 
        $limit="LIMIT $page,$pagelimit"; 
        
        $db = new DbExt();
		$stmt="
		SELECT SQL_CALC_FOUND_ROWS 
		a.id,
		a.merchant_id,
		a.client_id,
		a.date_created,
		b.restaurant_name as merchant_name,
		b.logo
		
		FROM
		{{favorites}} a
		left join {{merchant}} b
        ON
        a.merchant_id = b.merchant_id
                
		WHERE a.client_id=".FunctionsV3::q($client_id)."
				
		ORDER BY a.id DESC
		$limit
		";					
		if($res = $db->rst($stmt)){
			
			$total_records=0;
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ($resp=$db->rst($stmtc)){			 			
				$total_records=$resp[0]['total_records'];
			}					
			$paginate_total = ceil( $total_records / $pagelimit );
			
			$data = array();
			foreach ($res as $val) {										
				$date_added = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
				$val['date_added']= mobileWrapper::t("Added [date]",array(
				  '[date]'=>$date_added
				));
				$val['logo']=mobileWrapper::getImage($val['logo']);
				
				$ratings = Yii::app()->functions->getRatings($val['merchant_id']);
				
				$ratings['review_count'] = mobileWrapper::t("[count] reviews",array(
	 			  '[count]'=>$ratings['votes']
	 			));
	 			$val['rating']=$ratings;
	 			
	 			$val['background_url'] = mobileWrapper::getMerchantBackground($val['merchant_id'],'resto_banner.jpg');
	 			
	 			$val['merchant_name'] = clearString($val['merchant_name']);
	 			
				$data[]=$val;
			}
			
			$this->code = 1;
			$this->msg="OK";
			$this->details = array( 
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$paginate_total,
			  'data'=>$data
			);
			
		} else {				
			$this->code = 6;
			$this->msg = $this->t("Your favorite list is empty");
								
			$this->details = array(
			   'element'=>".favorite_loader",
        	   'element_list'=>"#favorite_list_item",
        	   'message'=>$this->t("Add your favorite restaurant")
			);
		}
        
		$this->output();
	}	
	
    public function actionCrediCartList()
	{				
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];		
		
		$pagelimit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $pagelimit;
        } else  $page = 0; 

        $paginate_total=0; 
        $limit="LIMIT $page,$pagelimit"; 
        
        $db = new DbExt();
		$stmt="
		SELECT SQL_CALC_FOUND_ROWS 
		a.cc_id as id,
		a.card_name,
		a.credit_card_number,
		a.date_created		
		FROM
		{{client_cc}} a
				       
		WHERE a.client_id=".FunctionsV3::q($client_id)."
				
		ORDER BY a.cc_id DESC
		$limit
		";					
		if($res = $db->rst($stmt)){
			
			$total_records=0;
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ($resp=$db->rst($stmtc)){			 			
				$total_records=$resp[0]['total_records'];
			}					
			$paginate_total = ceil( $total_records / $pagelimit );
			
			$data = array();
			foreach ($res as $val) {	
				$date_added = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
				$val['date_added']= mobileWrapper::t("Added [date]",array(
				  '[date]'=>$date_added
				));													
				$val['date_created'] = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
				$data[]=$val;
			}
			
			$this->code = 1;
			$this->msg="OK";
			$this->details = array( 
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$paginate_total,
			  'data'=>$data
			);
			
		} else {						
			$this->code = 6;
			$this->msg = $this->t("Your credit card list is empty");
								
			$this->details = array(
			   'element'=>".creditcard_loader",
        	   'element_list'=>"#creditcard_list_item",
        	   'message'=>$this->t("Add your first credit card")
			);
		}
        
		$this->output();
	}		
	
    public function actionAddressBookList()
	{				
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];		
		
		$search_resp = mobileWrapper::searchMode();
		$search_mode = $search_resp['search_mode'];
		
		$pagelimit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $pagelimit;
        } else  $page = 0; 

        $paginate_total=0; 
        $limit="LIMIT $page,$pagelimit"; 
                        
        $db = new DbExt();
		$stmt="
		SELECT SQL_CALC_FOUND_ROWS 
		a.id,
		a.as_default,
		concat( a.street,' ', a.city, ' ', a.state, ' ',a.zipcode )  as address,
		a.date_created		
		FROM
		{{address_book}} a
				       
		WHERE a.client_id=".FunctionsV3::q($client_id)."
				
		AND a.street <> ''    	      
		
		ORDER BY a.id DESC
		$limit
		";		

		if($search_mode=="location"){
			$stmt="
			SELECT SQL_CALC_FOUND_ROWS 
			a.id,
			a.as_default,			
			a.date_created,
			concat(a.street,' ',d.name,' ',c.name,' ',b.name) as address			
			FROM
			{{address_book_location}} a

			LEFT JOIN {{location_states}} b
			ON 
			a.state_id = b.state_id
			
			LEFT JOIN {{location_cities}} c
			ON 
			a.city_id = c.city_id
			
			LEFT JOIN {{location_area}} d
			ON 
			a.area_id = d.area_id
			    
			WHERE a.client_id=".FunctionsV3::q($client_id)."
					
			AND a.street <> ''    	      
			
			ORDER BY a.id DESC
			$limit
			";			
		}	
					
		if($res = $db->rst($stmt)){
			
			$total_records=0;
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ($resp=$db->rst($stmtc)){			 			
				$total_records=$resp[0]['total_records'];
			}					
			$paginate_total = ceil( $total_records / $pagelimit );
			
			$data = array();
			foreach ($res as $val) {			
				$date_added = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
				$val['date_added']= mobileWrapper::t("Added [date]",array(
				  '[date]'=>$date_added
				));
				$val['date_created'] = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
				
				if($search_mode=="location"){
					if($val['as_default']==1){
						$val['as_default']=2;
					}			
				}
				
				$data[]=$val;
			}
			
			$this->code = 1;
			$this->msg="OK";
			$this->details = array( 
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$paginate_total,
			  'data'=>$data
			);
			
		} else {						
			$this->code = 6;
			$this->msg = $this->t("Your address book list is empty");
								
			$this->details = array(
			   'element'=>".addressbook_loader",
        	   'element_list'=>"#addressbook_list_item",
        	   'message'=>$this->t("Add your first address")
			);
		}
        
		$this->output();
	}			
	
	
	public function actiongetlanguageList()
	{
		$data = array();
		if ($lang_list=FunctionsV3::getLanguageList(false) ){	
			$enabled_lang=FunctionsV3::getEnabledLanguage();
			foreach ($lang_list as $val) {
				if (in_array($val,(array)$enabled_lang)){
					$data[$val]=mt($val);
				}			
			}
			$this->code=1;
			$this->msg = "OK";
			$this->details = array(
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'lang'=>Yii::app()->language,
			  'data'=>$data
			);
		} else {			
			$this->code = 6;
			$this->msg = $this->t("No available language");
								
			$this->details = array(
			   'element'=>".language_list_loader",
        	   'element_list'=>"#language_list_item",
        	   'message'=>$this->t("language not available")
			);
		}
		$this->output();
	}	
	
	public function actiongetOrderDetails()
	{ 
		$data = array();
		$order_id = isset($this->data['order_id'])?$this->data['order_id']:0;
		if($order_id>0){
		   if ($res = mobileWrapper::orderDetails($order_id)){
		   	  
		   	  $res['review_as']='';
		   	  if($clien_info =  Yii::app()->functions->getClientInfo($res['client_id'])){
		   	  	 $res['review_as'] = mobileWrapper::t("Review as [customer_name]",array(
				   '[customer_name]'=>$clien_info['first_name']
				 ));
		   	  }		   
		   	  $this->code = 1;
		   	  $this->msg = "ok";
		   	  
		   	  $res['logo'] = $res['logo']=mobileWrapper::getImage($res['logo']);
		   	  
		   	  $res['transaction'] = mobileWrapper::t("[trans_type] #[order_id]",array(
		   	    '[trans_type]'=>t($res['trans_type']),
				'[order_id]'=>t($res['order_id']),
		   	  ));
		   	  
		   	  $res['payment_type'] = mobileWrapper::t(FunctionsV3::prettyPaymentTypeTrans($res['trans_type'],$res['payment_type']));
		   	  $res['merchant_name'] = clearString($res['merchant_name']);
		   	  
		   	  $this->details = array(
		   	    'data'=>$res
		   	  );
		   } else $this->msg = $this->t("order not found");		
		} else $this->msg = $this->t("invalid order id");		
		$this->output();
	}
	
	public function actionaddReview()
	{
		$this->data = $_POST;	
		$db = new DbExt();
		$order_id =  isset($this->data['order_id'])?$this->data['order_id']:'';    	
		
		if(!is_numeric($this->data['rating'])){
			$this->msg = $this->t("Please select rating");
			$this->output();
		}
		if(!is_numeric($order_id)){
			$this->msg = $this->t("invalid order id");
			$this->output();
		}
		
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}
		
		$website_review_type = getOptionA('website_review_type');				
		$order_info=Yii::app()->functions->getOrderInfo($order_id);
				
		$client_id = $res['client_id'];
		
		if($order_info){
			if ($website_review_type==2){														
					$order_id = $order_info['order_id'];
					$params = array(
					  'merchant_id'=>$order_info['merchant_id'],
					  'client_id'=>$client_id,
					  'review'=>$this->data['review'],
					  'rating'=>$this->data['rating'],
					  'as_anonymous'=>isset($this->data['as_anonymous'])?$this->data['as_anonymous']:0,
					  'date_created'=>FunctionsV3::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR'],
					  'order_id'=>$order_id,  
					);
					if(method_exists('FunctionsV3','getReviewBasedOnStatus')){
					   $params['status']=FunctionsV3::getReviewBasedOnStatus($order_info['status']);
				    }
				    
				    //dump($params);
				    
				    if(!$res_review = FunctionsV3::getReviewByOrder($client_id,$order_id)){
				    	if ( $db->insertData("{{review}}",$params)){
				    		$review_id=Yii::app()->db->getLastInsertID();
				    		
				    		if (FunctionsV3::hasModuleAddon("pointsprogram")){
								if (method_exists('PointsProgram','addReviewsPerOrder')){
									PointsProgram::addReviewsPerOrder($order_id,
									$client_id,$review_id,$order_info['merchant_id'],$order_info['status']);
								}			
							}	
							
							$this->code = 1;
					        $this->msg = mt("Your review has been published.");
					        $this->details = array();
									
				    	} else $this->msg = mt("ERROR. cannot insert data.");
				    } else {
				    	$id = $res_review['id'];
				    	unset($params['date_created']);
				    	$params['date_modified'] = FunctionsV3::dateNow();
				    	$db->updateData("{{review}}",$params,'id', $id);
				    	$this->code = 1;
					    $this->msg = mt("Your review has been published.");
					    $this->details = array();
				    }
						    			
			} else {
				// review merchant
				$order_id = $order_info['order_id'];
				$params = array(
				  'merchant_id'=>$order_info['merchant_id'],
				  'client_id'=>$client_id,
				  'review'=>$this->data['review'],
				  'rating'=>$this->data['rating'],
				  'as_anonymous'=>isset($this->data['as_anonymous'])?$this->data['as_anonymous']:0,
				  'date_created'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR'],
				  'order_id'=>$order_id,  
				);
				$actual_purchase = getOptionA('website_reviews_actual_purchase');				
				if($actual_purchase=="yes"){
					$functionk=new FunctionsK();
					if (!$functionk->checkIfUserCanRateMerchant($client_id,$order_info['merchant_id'])){
						$this->msg=mt("Reviews are only accepted from actual purchases!");
					}
					if (!$functionk->canReviewBasedOnOrder($client_id,$order_info['merchant_id'])){
		    		   $this->msg=mt("Sorry but you can make one review per order");
		    	       return ;
		    	    }	  		   
				}
				
				if(!$res_review = FunctionsV3::getReviewByOrder($client_id,$order_id)){
			    	if ( $db->insertData("{{review}}",$params)){
			    		$review_id=Yii::app()->db->getLastInsertID();
			    		
			    		if (FunctionsV3::hasModuleAddon("pointsprogram")){
							if (method_exists('PointsProgram','addReviewsPerOrder')){
								PointsProgram::addReviewsPerOrder($order_id,
								$client_id,$review_id,$order_info['merchant_id'],$order_info['status']);
							}			
						}	
						
						$this->code = 1;
				        $this->msg = mt("Your review has been published.");
				        $this->details = array();
								
			    	} else $this->msg = mt("ERROR. cannot insert data.");
			    } else {
			    	$id = $res_review['id'];
			    	unset($params['date_created']);
			    	$params['date_modified'] = FunctionsV3::dateNow();
			    	$db->updateData("{{review}}",$params,'id', $id);
			    	$this->code = 1;
				    $this->msg = mt("Your review has been published.");
				    $this->details = array();
			    }
							
			}
		} else $this->msg = $this->t("order id not found");
		
		$this->output();
	}
	
	public function actionCancelOrder()
	{
		$this->getPOSTData();
		$this->data = $_POST;		
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];		
		$order_id = isset($this->data['order_id'])?$this->data['order_id']:0;
		
		if($order_id>0){
			if ($res = Yii::app()->functions->getOrderInfo($order_id)){
				if($res['client_id']== $client_id){
					
					$p = new CHtmlPurifier();
					
					$params = array(
    				  'request_cancel'=>1,
    				  'cancel_reason'=>$p->purify($this->data['cancel_reason']),
    				  'date_modified'=>FunctionsV3::dateNow(),
    				  'ip_address'=>$_SERVER['REMOTE_ADDR']
    				);
    				$db = new DbExt();
    				    			
    				if ( $db->updateData("{{order}}",$params,'order_id',$order_id)){ 
    					FunctionsV3::notifyCancelOrder($res);
    					$this->code = 1;
		    			$this->msg = mt("Your request has been sent to merchant");
		    			$this->details;
		    			
		    			/*logs*/
		    			$params_logs=array(
		    			  'order_id'=>$order_id,
		    			  'status'=>"cancel order request",
		    			  'date_created'=>FunctionsV3::dateNow(),
		    			  'ip_address'=>$_SERVER['REMOTE_ADDR']
		    			);
		    			$db->insertData("{{order_history}}",$params_logs);
		    			
    				} else $this->msg = mt("ERROR: cannot update records.");
    				
				} else $this->msg = mt("Sorry but this order does not belong to you");
			} else $this->msg = mt("Order id not found");
		} else $this->msg = $this->t("invalid order id");
		
		$this->output();
	}

	public function actiongetOrderHistory()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];	
		$order_id = isset($this->data['order_id'])?$this->data['order_id']:0;
		
		$p = new CHtmlPurifier();	
		$page_action =  isset($this->data['page_action'])?$this->data['page_action']:'';
		
		if($order_id>0){
			if ($res = mobileWrapper::orderHistory($order_id)){
				$data =array();
				foreach ($res as $val) {
		   	   	  
		   	   	  $remarks = $p->purify(clearString($val['remarks']));
		   	   	  if(!empty($val['remarks2'])){
		   	   	  	  $args=json_decode($val['remarks_args'],true);  
		   	   	  	  if(is_array($args) && count( (array) $args)>=1){
						 foreach ($args as $args_key=>$args_val) {
							$args[$args_key]=t($args_val);
						 }						 
						 $new_remarks=$val['remarks2'];
						 $remarks=Yii::t("driver","".$new_remarks,$args);	
					  }
		   	   	  }
		   	   	  
		   	      $data[]=array(
		   	        'date'=>FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']),
		   	        'status_raw'=>$val['status'],
		   	        'status'=>mt($val['status']),
		   	        'remarks'=>$remarks
		   	      );
		   	   }
		   	   
		   	   $order_info = mobileWrapper::orderDetails($order_id);		   	   
		   	   $order_info['merchant_name'] = clearString($order_info['merchant_name']);
		   	   $order_info['logo'] = $order_info['logo']=mobileWrapper::getImage($order_info['logo']);
		   	   $order_info['transaction'] = mobileWrapper::t("[trans_type] #[order_id]",array(
		   	    '[trans_type]'=>t($order_info['trans_type']),
				'[order_id]'=>t($order_info['order_id']),
		   	   ));		   	   
		   	   $order_info['payment_type'] = mobileWrapper::t(FunctionsV3::prettyPaymentTypeTrans($order_info['trans_type'],$order_info['payment_type']));
		   	   
		   	   $this->code = 1;
		   	   $this->msg = "OK";
		   	   $this->details = array(
		   	     'order_id'=>$order_id,
		   	     'show_track'=>mobileWrapper::showTrackOrder($order_id),
		   	     'page_action'=>$page_action,
		   	     'order_info'=>$order_info,
		   	     'data'=>$data,		   	    
		   	   );
			} else {				
				$this->code = 6;
				$this->msg = $this->t("No results");
									
				$this->details = array(
				   'element'=>".track_history_loader",
	        	   'element_list'=>"#track_history_item",
	        	   'message'=>$this->t("Order history is empty")
				);
			}		
		} else {			
			$this->code = 6;
			$this->msg = $this->t("invalid order id");
								
			$this->details = array(
			   'element'=>".track_history_loader",
        	   'element_list'=>"#track_history_item",
        	   'message'=>$this->t("Order history is empty")
			);				
		}
		$this->output();
	}
	
	public function actionsearchOrder()
	{
		if ($client_id = $this->checkToken()){
		$data = array();
		$search_str = isset($this->data['search_str'])?$this->data['search_str']:'';
		if(!empty($search_str)){
			$db=new DbExt();
			$stmt="SELECT 
			a.order_id,
			a.client_id,
			a.trans_type,
			a.trans_type as trans_type_raw,
			a.payment_type,
			a.payment_type as payment_type_raw,
			a.total_w_tax,
			b.restaurant_name,
			b.logo
			FROM {{order}} a			
			left join {{merchant}} b
            ON
            a.merchant_id = b.merchant_id
            WHERE a.client_id=".FunctionsV3::q($client_id)."
            AND ( 
                a.order_id LIKE ".FunctionsV3::q("%$search_str")."
                OR b.restaurant_name LIKE ".FunctionsV3::q("%$search_str%")."
                OR a.trans_type LIKE ".FunctionsV3::q("%$search_str%")."
                OR a.payment_type LIKE ".FunctionsV3::q("%$search_str%")."
             )
            
			LIMIT 0,20
			";						
			
			if ($res = $db->rst($stmt)){				
				$res = Yii::app()->request->stripSlashes($res);				
				foreach ($res as $val) {
					$val['payment_type'] = mobileWrapper::t(FunctionsV3::prettyPaymentTypeTrans($val['trans_type'],$val['payment_type']));
					$val['restaurant_name']= mobileWrapper::highlight_word($val['restaurant_name'],$search_str);
					$val['transaction'] = mobileWrapper::t("[trans_type] #[order_id]",array(
					  '[trans_type]'=>t($val['trans_type']),
					  '[order_id]'=>t($val['order_id']),
					));
					
					$val['payment_type']= mobileWrapper::highlight_word($val['payment_type'],$search_str);
					$val['restaurant_name']= mobileWrapper::highlight_word($val['restaurant_name'],$search_str);
					$val['transaction']= mobileWrapper::highlight_word($val['transaction'],$search_str);
					
					$val['logo']=mobileWrapper::getImage($val['logo']);
					
					$data[] = $val;
				}
				
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				 'list'=>$data
				);
				
			} else $this->msg = $this->t("No results");
		} else $this->msg = $this->t("invalid search string");
		}
		$this->output();
	}
	
	public function actionViewOrder()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];	
		$order_id = isset($this->data['order_id'])?$this->data['order_id']:0;
		
		if($order_id>0){
						
			if ( $data = mobileWrapper::getReceiptByID($order_id)){
				$data = Yii::app()->request->stripSlashes($data);
				
				$json_details=!empty($data['json_details'])?json_decode($data['json_details'],true):false;				
				
				if ( $json_details !=false){
					
					 Yii::app()->functions->displayOrderHTML(array(
				       'merchant_id'=>$data['merchant_id'],
				       'order_id'=>$data['order_id'],
				       'delivery_type'=>$data['trans_type'],
				       'delivery_charge'=>$data['delivery_charge'],
				       'packaging'=>$data['packaging'],
				       'cart_tip_value'=>$data['cart_tip_value'],
					   'cart_tip_percentage'=>$data['cart_tip_percentage']/100,
					   'card_fee'=>$data['card_fee'],
					   'donot_apply_tax_delivery'=>$data['donot_apply_tax_delivery'],
					   'points_discount'=>isset($data['points_discount'])?$data['points_discount']:'' /*POINTS PROGRAM*/,
					   'voucher_amount'=>$data['voucher_amount'],
					   'voucher_type'=>$data['voucher_type']
				     ),$json_details,true,$data['order_id']);
				     
				     $data2=Yii::app()->functions->details;
				      
				     $merchant_info=Yii::app()->functions->getMerchant( $data['merchant_id'] );
			         $full_merchant_address=$merchant_info['street']." ".$merchant_info['city']. " ".$merchant_info['state'].
			         " ".$merchant_info['post_code'];
			
					 if (isset($data['contact_phone1'])){
						if (!empty($data['contact_phone1'])){
							$data['contact_phone']=$data['contact_phone1'];
						}
					 }				
					 if (isset($data['location_name1'])){
						if (!empty($data['location_name1'])){
							$data['location_name']=$data['location_name1'];
						}
					}
					
					$new_data = array();					
					$new_data[] = mobileWrapper::receiptFormater("Customer Name", clearString($data['full_name']) );
					$new_data[] = mobileWrapper::receiptFormater("Merchant Name", clearString($data['merchant_name']) );					
					if (isset($data['abn']) && !empty($data['abn'])){						
						$new_data[] = mobileWrapper::receiptFormater("ABN",$data['abn']);					
					}
					$new_data[] = mobileWrapper::receiptFormater("Telephone",$data['merchant_contact_phone']);
					$new_data[] = mobileWrapper::receiptFormater("Address",$full_merchant_address);
										
					$merchant_tax_number=getOption($this->merchant_id,'merchant_tax_number');
			        if(!empty($merchant_tax_number)){
			           $new_data[] = mobileWrapper::receiptFormater("Tax number",$merchant_tax_number);
			        }
			        
			        $new_data[] = mobileWrapper::receiptFormater("TRN Type", t($data['trans_type']) );
			        $new_data[] = mobileWrapper::receiptFormater("Payment Type",
			          FunctionsV3::prettyPaymentType('payment_order',$data['payment_type'],$data['order_id'],$data['trans_type'])
			        );
			        
			        if ( $data['payment_provider_name']){			       	   
			       	   $new_data[] = mobileWrapper::receiptFormater("Card#",$data['payment_provider_name']);
			        }
			        
			        if ( $data['payment_type'] =="pyp"){
			       	  $paypal_info=Yii::app()->functions->getPaypalOrderPayment($data['order_id']);	
			          			       	  
			          $new_data[] = mobileWrapper::receiptFormater("Paypal Transaction ID",
			            isset($paypal_info['TRANSACTIONID'])?$paypal_info['TRANSACTIONID']:''
			          );
			        }
			        			        
			        $new_data[] = mobileWrapper::receiptFormater("Reference #", Yii::app()->functions->formatOrderNumber($data['order_id']));
			        
			        if ( !empty($data['payment_reference'])){			       	  
			       	   $new_data[] = mobileWrapper::receiptFormater("Payment Ref",$data['payment_reference']);
			        }
			        if ( $data['payment_type']=="ccr" || $data['payment_type']=="ocr"){			           
			           $new_data[] = mobileWrapper::receiptFormater("Card #",
			             Yii::app()->functions->maskCardnumber($data['credit_card_number'])
			           );
			        }
			        
			        $trn_date=date('M d,Y G:i:s',strtotime($data['date_created']));			        
			        $new_data[] = mobileWrapper::receiptFormater("TRN Date",
			          Yii::app()->functions->translateDate($trn_date)
			        );

			        switch ($data['trans_type']) {
        	         	case "delivery":
        	         		
        	         		if (isset($data['delivery_date'])){
				           	   $date = prettyDate($data['delivery_date']);
					           $date=Yii::app()->functions->translateDate($date);				               
				               $new_data[] = mobileWrapper::receiptFormater("Delivery Date",$date);
				            }
				            
				            if (isset($data['delivery_time'])){
				       	  	  if ( !empty($data['delivery_time'])){				       	  	  	  
				       	  	  	  $new_data[] = mobileWrapper::receiptFormater("Delivery Time",
				       	  	  	    Yii::app()->functions->timeFormat($data['delivery_time'],true)
				       	  	  	  );
				       	  	  }
				       	    }
				       	    
				       	    if (isset($data['delivery_asap'])){
				       	   	   if ( !empty($data['delivery_asap'])){				       	   	   	   
				       	   	   	   $new_data[] = mobileWrapper::receiptFormater("Deliver ASAP", $data['delivery_asap']==1?t("Yes"):'' );
				       	   	   }
				       	    } 
				       	    
				       	    if (!empty($data['client_full_address'])){
					         	$delivery_address=$data['client_full_address'];
					        } else $delivery_address=$data['full_address'];				       	    
					        
				       	    $new_data[] = mobileWrapper::receiptFormater("Deliver to",$delivery_address);
				       	    
				       	    if (!empty($data['delivery_instruction'])){					       	   
					       	    $new_data[] = mobileWrapper::receiptFormater("Delivery Instruction",$data['delivery_instruction']);
					       	}
					       	
					       	if (!empty($data['location_name1'])){
					           $data['location_name']=$data['location_name1'];
					        }					       	
					       	$new_data[] = mobileWrapper::receiptFormater("Location Name",$data['location_name']);
					       						       	 
					       	if ( !empty($data['contact_phone1'])){
					          $data['contact_phone']=$data['contact_phone1'];
					        }				       	    
				       	    $new_data[] = mobileWrapper::receiptFormater("Contact Number",$data['contact_phone']);
        	         		
				       	    if ($data['order_change']>=0.0001){	       	   	               
	       	   	               $new_data[] = mobileWrapper::receiptFormater("Change", FunctionsV3::prettyPrice($data['order_change']) );
	       	                }
	       	                
	       	                if($data['opt_contact_delivery']==1){
	       	                	$new_data[] = mobileWrapper::receiptFormater("Delivery options", mt("Leave order at the door or gate") );
	       	                }
				       	    	       	                
        	         		break;
        	         
        	         	case "pickup":
        	         		        	         		
        	         		$new_data[] = mobileWrapper::receiptFormater("Contact Number", $data['contact_phone'] );
        	         		if (isset($data['delivery_date'])){	       	  	                
	       	  	                $new_data[] = mobileWrapper::receiptFormater("Pickup Date", $data['delivery_date'] );
	       	                }
	       	                
	       	                if (isset($data['delivery_time'])){
				       	  	   if ( !empty($data['delivery_time'])){				       	  	  	  
				       	  	  	  $new_data[] = mobileWrapper::receiptFormater("Pickup Time", $data['delivery_time'] );
				       	  	   }
					       	}
					       	
					       	if ($data['order_change']>=0.0001){	       	   	               
	       	   	               $new_data[] = mobileWrapper::receiptFormater("Change", FunctionsV3::prettyPrice($data['order_change']) );
	       	                }
        	         		
        	         	    break;        	         	
        	         	    
        	         	case "dinein":
        	         		
        	         		$new_data[] = mobileWrapper::receiptFormater("Contact Number", $data['contact_phone'] );
        	         		if (isset($data['delivery_date'])){	       	  	                
	       	  	                $new_data[] = mobileWrapper::receiptFormater("Dine in Date", $data['delivery_date'] );
	       	                }
	       	                
	       	                if (isset($data['delivery_time'])){
				       	  	   if ( !empty($data['delivery_time'])){				       	  	  	  
				       	  	  	  $new_data[] = mobileWrapper::receiptFormater("Dine in Time", $data['delivery_time'] );
				       	  	   }
					       	}
					       	
					       	if ($data['order_change']>=0.0001){	       	   	               
	       	   	               $new_data[] = mobileWrapper::receiptFormater("Change", FunctionsV3::prettyPrice($data['order_change']) );
	       	                }
	       	                
	       	                $new_data[] = mobileWrapper::receiptFormater("Number of guest", $data['dinein_number_of_guest'] );
	       	                $new_data[] = mobileWrapper::receiptFormater("Table number", $data['dinein_table_number'] );
	       	                $new_data[] = mobileWrapper::receiptFormater("Special instructions", $data['dinein_special_instruction'] );	       	                
        	         		
        	         	    break;     
        	         }	                 	         
					        	      
        	        
        	        $new_total_html='';
        	        
        	        if($data['apply_food_tax']==1){          	        	
        	        	$file = Yii::getPathOfAlias('webroot')."/protected/modules/".APP_FOLDER."/views/api/cart.php";        	        	
        	        	$new_total_html=$this->renderFile($file,array(
			    		   'data'=>$data
			    		),true);
        	        }        	      
        	                	        
					$this->code = 1;
					$this->msg = "OK";
					$this->details = array(
					  'apply_food_tax'=>$data['apply_food_tax'],
					  'data'=>$new_data,
					  'html'=>$data2['html'],
					  'new_total_html'=>$new_total_html
					);
				     
				} else $this->msg = $this->t("Order not available to view. please try again later");				
			} else $this->msg = $this->t("Order not available to view. please try again later");
		} else $this->msg = $this->t("invalid order id");
		$this->output();
	}
	
	public function actionReOrder()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];	
		$order_id = isset($this->data['order_id'])?$this->data['order_id']:0;
		
		/*inventory*/
		$merchant_id = 0;
		
		if($order_id>0){			
			if ($res = mobileWrapper::ReOrderGetInfo($order_id)){				
				$res = Yii::app()->request->stripSlashes($res);
				
				if($res['merchant_status']!="active"){
					$this->msg = $this->t("Merchant is no longer active");
					$this->output();
				}			
				if($res['is_ready']!=2){
					$this->msg = $this->t("Merchant is not published");
					$this->output();
				}			
				
				/*inventory*/
				$merchant_id = (integer) $res['merchant_id'];
				
				/*VALIDATE IF ITEM IS AVAILABLE*/
				$cart_count=0;
				$json_details = json_decode($res['json_details'],true);
				$re_order_items = array();							
					
				if(is_array($json_details) && count($json_details)>=1){
				   foreach ($json_details as $item) {				   	   				   	   
				   	
				   	   $newest_price = 0; $newest_discount=0;
				   	   $current_discount = 0; $current_item_price = 0;
				   	
				   	   if ($item_res = Yii::app()->functions->getFoodItem($item['item_id'])){
				   	   	   if($item_res['not_available']==2){
				   	   	   	  // do nothing			   	   	   	  
				   	   	   } else {				   	   	   
				   	   	   	  //dump($item_res);				   	   	   	  
				   	   	   	  $current_discount = isset($item['discount'])?$item['discount']:0;
				   	   	   	  $item_price = explode("|",$item['price']);
				   	   	   	  				   	   	   	  
				   	   	   	  if( count($item_price) <=1){				   	   	   	  	
				   	   	   	  			  
				   	   	   	  	$newest_discount = $item_res['discount'];
				   	   	   	  	$current_item_price = $item_price[0];
				   	   	   	  	$current_item = json_decode($item_res['price'],true);
				   	   	   	  	if(is_array($current_item) && count($current_item)>=1){
				   	   	   	  		//$newest_price = $current_item[0];
				   	   	   	  		foreach ($current_item as $new_price) {
				   	   	   	  			$newest_price = $new_price;
				   	   	   	  		}
				   	   	   	  	}								   	   	   	  					   	   	   	  				   	   	   	  
				   	   	   	  	if($current_item_price!=$newest_price){
				   	   	   	  		$item['price'] = $newest_price;
				   	   	   	  	}				   	   	   	  
				   	   	   	  	if($current_discount!=$newest_discount){
				   	   	   	  		$item['discount'] = $newest_discount;
				   	   	   	  	}				   	   	   	  
				   	   	   	  } else {				   	  					   	   	   	  			   	   	   	  
				   	   	   	  	$newest_discount = $item_res['discount'];				   	   	   	  	 	   	  	
				   	   	   	  	$current_size_id = isset($item_price[2])?$item_price[2]:0;
				   	   	   	  	$current_item_price = isset($item_price[0])?$item_price[0]:0;
				   	   	   	  	$newest_price_list = json_decode($item_res['price'],true);				   	   	   	  	
				   	   	   	  	if(array_key_exists($current_size_id,(array)$newest_price_list)){
				   	   	   	  		$newest_price = $newest_price_list[$current_size_id];				   	   	   	  						   	   	   	  	
				   	   	   	  	    if($current_item_price!=$newest_price){
				   	   	   	  	    	$item['price'] = $newest_price."|".$item_price[1]."|".$item_price[2];
				   	   	   	  	    }			
				   	   	   	  	    
				   	   	   	  	    if($current_discount!=$newest_discount){
				   	   	   	  		   $item['discount'] = $newest_discount;
				   	   	   	  	    }				   	   	   	  
				   	   	   	  		   	   	   	  	
				   	   	   	  	} else {
				   	   	   	  		// price does not exist
				   	   	   	  	}	   	   	   	  				   	   	   	  					   	   	   	  	
				   	   	   	  } 	   				   	   	   	  
				   	   	   	  				   	   	   	  
				   	   	   	  $re_order_items[] = $item;
				   	   	   	  $cart_count++;
				   	   	   }
				   	   }				   
				   }
				}	
				
				/*dump($re_order_items);
				die();*/
				
				if($cart_count<=0){
					$this->msg = $this->t("There is no item to re-order");
					$this->output();
				}		

							
				/*inventory*/				
				if(FunctionsV3::inventoryEnabled($merchant_id)){
					try {						
						StocksWrapper::verifyStocksReOrder($order_id,$merchant_id);
					} catch (Exception $e) {
						$this->msg = $e->getMessage();
		                $this->output();
					}
				}				
				
				$params = array(		
				  'merchant_id'=>$res['merchant_id'],
				  'cart'=>json_encode($re_order_items),
				  'device_uiid'=>$this->device_uiid,
				  'cart_count'=>$cart_count,
				  'date_modified'=>FunctionsV3::dateNow()
				);								 
				           
				if($resp=mobileWrapper::getCart($this->device_uiid)){
					$up =Yii::app()->db->createCommand()->update("{{mobile2_cart}}",$params,
		          	    'device_uiid=:device_uiid',
		          	    array(
		          	      ':device_uiid'=>$this->device_uiid
		          	    )
		          	);	          	  
					if($up){
						$this->code = 1;
						$this->msg = "OK";					
						$this->details = array(
						  'merchant_id'=>$res['merchant_id']
						);
					} else $this->msg = $this->t("Order not available to re-order. please try again later");
				} else {					
					if(Yii::app()->db->createCommand()->insert("{{mobile2_cart}}",$params)){	
						$this->code = 1;
						$this->msg = "OK";
						$this->details = array(
						  'merchant_id'=>$res['merchant_id']
						);
					} else $this->msg = $this->t("Order not available to re-order. please try again later");
				}				
				
			} else $this->msg = $this->t("Order not available to re-order. please try again later");
		} else $this->msg = $this->t("invalid order id");	
		
		$this->output();
	}
	
	public function actionRemoveFavorites()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];	
		
		$id= isset($this->data['id'])?$this->data['id']:0;
		if($id>0){
			mobileWrapper::removeFavorite($id, $client_id);
			$this->code = 1;
			$this->msg = $this->t("Successfully remove from your favorites");
		} else $this->msg = $this->t("invalid id");
		
		$this->output();
	}
	
	public function actionsearchFavorites()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];	
		
		$search_str = isset($this->data['search_str'])?$this->data['search_str']:'';
		if(!empty($search_str)){			
			$stmt="
		    SELECT SQL_CALC_FOUND_ROWS 
			a.id,
			a.merchant_id,
			a.client_id,
			a.date_created,
			b.restaurant_name as merchant_name,
			b.logo
			
			FROM
			{{favorites}} a
			left join {{merchant}} b
	        ON
	        a.merchant_id = b.merchant_id
	                
			WHERE a.client_id=".FunctionsV3::q($client_id)."
			AND b.restaurant_name LIKE ".FunctionsV3::q("%$search_str%")."
					
			ORDER BY a.id DESC
			";						
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){	
				$data = array();
				foreach ($res as $val) {
					$date_added = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
					$val['date_added']= mobileWrapper::t("Added [date]",array(
					  '[date]'=>$date_added
					));
					$val['logo']=mobileWrapper::getImage($val['logo']);
					
					$val['merchant_name']= mobileWrapper::highlight_word($val['merchant_name'],$search_str);
					
					$data[]=$val;
				}
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'data'=>$data
				);
			} else $this->msg = $this->t("No results");		
			
		} else $this->msg = $this->t("invalid search string");
		
		$this->output();
	}

	public function actionsaveCreditCard()
	{
		$this->data = $_POST;
		
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];	
		
		if(isset($this->data['credit_card_number'])){
			if(!empty($this->data['credit_card_number'])){				
				$this->data['credit_card_number']  = str_replace(" ","",$this->data['credit_card_number']);
			}	
		}
		
		if(strlen($this->data['credit_card_number'])!=16){
			$this->msg = $this->t("Invalid credit card length");
			$this->output();
		}
		
		$id = isset($this->data['cc_id'])?$this->data['cc_id']:'';
		
		$p = new CHtmlPurifier();			
		$params = array(
		  'client_id'=>$client_id,
		  'card_name'=>isset($this->data['card_name'])?$p->purify($this->data['card_name']):'',
		  'credit_card_number'=>isset($this->data['credit_card_number'])?$this->data['credit_card_number']:'',
		  'billing_address'=>isset($this->data['billing_address'])?$p->purify($this->data['billing_address']):'',
		  'cvv'=>isset($this->data['cvv'])?$this->data['cvv']:'',
		  'expiration_month'=>isset($this->data['expiration_month'])?$this->data['expiration_month']:'',
		  'expiration_yr'=>isset($this->data['expiration_yr'])?$this->data['expiration_yr']:'',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
		
		$params['credit_card_number']=FunctionsV3::maskCardnumber($p->purify($params['credit_card_number']));
		
		try {        	
    	   $params['encrypted_card']=CreditCardWrapper::encryptCard($p->purify($this->data['credit_card_number']));
    	} catch (Exception $e) {
    		$this->msg =  Yii::t("default","Caught exception: [error]",array(
						    '[error]'=>$e->getMessage()
						  ));
		    $this->output();
    		return ;
    	}
    	
    	$db = new DbExt();
    	
		if($id>0){
			unset($params['date_created']);
			unset($params['ip_address']);
			$db->updateData("{{client_cc}}",$params,'cc_id',$id);
			$this->code = 1;
			$this->msg = $this->t("Successfully updated");
		} else {
			if ( !Yii::app()->functions->getCCbyCard($params['credit_card_number'],$client_id) ){
				$db->insertData("{{client_cc}}",$params);
				$this->code = 1;
				$this->msg = $this->t("Successful");
			} else $this->msg = $this->t("Credit card already exits");
		}			
		$this->output();
	}
	
	public function actiongetCedittCardInfo()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];	
		
		$id = isset($this->data['cc_id'])?$this->data['cc_id']:'';
		if($id>0){
			if ($res = Yii::app()->functions->getCreditCardInfo($id)){
				
				unset($res['client_id']);
				unset($res['date_created']);unset($res['date_modified']);
				unset($res['ip_address']);
				$this->code = 1;
				$this->msg = "OK";
				
				$decryp_card = isset($res['credit_card_number'])?$res['credit_card_number']:'';
				if(isset($res['encrypted_card'])){
					try {
						$decryp_card = CreditCardWrapper::decryptCard($res['encrypted_card']);
					} catch (Exception $e) {
						$decryp_card = Yii::t("default","Caught exception: [error]",array(
						  '[error]'=>$e->getMessage()
						));
					}
				}
				
				$res['credit_card_number']=$decryp_card;
						
				unset($res['encrypted_card']);
				$this->details = array(
				   'data'=>$res
				);
				
			} else $this->msg = $this->t("card information not found");	
		} else $this->msg = $this->t("invalid card id");	
		$this->output();
	}
	
	public function actionDeleteCreditCard()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];	
		
		$id = isset($this->data['id'])?$this->data['id']:'';
		if($id>0){
			$stmt="
			DELETE FROM {{client_cc}}
			WHERE 
			cc_id =".FunctionsV3::q($id)."
			AND 
			client_id = ".FunctionsV3::q($client_id)."
			";
			$db = new DbExt();
			$db->qry($stmt);
			$this->code = 1;
			$this->msg = $this->t("Credit card deleted");	
			$this->details = array();
		} else $this->msg = $this->t("invalid card id");	
		$this->output();
	}
	
	public function actionDeleteAddressBook()
	{
		if ($client_id = $this->checkToken()){
			$id = isset($this->data['id'])?$this->data['id']:'';

			$search_resp = mobileWrapper::searchMode();
		    $search_mode = $search_resp['search_mode'];
				
			if($id>0){
				$is_location = false;
				if($search_mode=="location"){
					$is_location = true;
				}
				mobileWrapper::DeleteAddressBook($id,$client_id, $is_location);
				$this->code = 1; $this->msg = $this->t("Address has been deleted");	
			} else $this->msg = $this->t("invalid id");		
		} 
		$this->output();
	}		
	
	public function actiongetCountryList()
	{
		$country_list = require_once('CountryCode.php');
		$this->code = 1;
		$this->msg = "OK";
		$this->details = array(
		  'country_list'=>$country_list,
		  'country_code'=>getOptionA('admin_country_set')
		);
		$this->output();
	}
	
	public function actionsaveAddressBook()
	{
		$this->data = $_POST;		
		if ($client_id = $this->checkToken()){
			$params = array(
			  'client_id'=>$client_id,
			  'latitude'=>isset($this->data['lat'])?$this->data['lat']:'',
			  'longitude'=>isset($this->data['lng'])?$this->data['lng']:'',
			  'street'=>isset($this->data['street'])?$this->data['street']:'',
			  'city'=>isset($this->data['city'])?$this->data['city']:'',
			  'state'=>isset($this->data['state'])?$this->data['state']:'',
			  'zipcode'=>isset($this->data['zipcode'])?$this->data['zipcode']:'',
			  'country_code'=>isset($this->data['country_code'])?$this->data['country_code']:'',
			  'as_default'=>isset($this->data['as_default'])?$this->data['as_default']:'',
			  'location_name'=>isset($this->data['location_name'])?$this->data['location_name']:'',
			  'date_created'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],			  
			);					
			
			if(empty($params['latitude'])){
				$this->msg = $this->t("please select your location on the map");
				$this->output();
			}		
			if(empty($params['longitude'])){
				$this->msg = $this->t("please select your location on the map");
				$this->output();
			}		
			
			if(!is_numeric($params['as_default'])){
			    //unset($params['as_default']);
			    $params['as_default']=1;
		    }			
			
			$db = new DbExt();			
			$id = isset($this->data['id'])?$this->data['id']:'';
			if($id>0){
				 unset($params['date_created']);
				 $params['date_modified']=FunctionsV3::dateNow();
				 
				 if ($params['as_default']==2){
					mobileWrapper::UpdateAllAddressBookDefault($client_id);
				 }				 				 
				 $db->updateData("{{address_book}}", $params ,'id',$id);
				 $this->code = 1; $this->msg = $this->t("Successfully updated");
			} else {				
				
				if ($params['as_default']==2){
					mobileWrapper::UpdateAllAddressBookDefault($client_id);
				}
								
				if ( $db->insertData("{{address_book}}",$params)){
					$this->code = 1; $this->msg = $this->t("Successfully added");
				} else $this->msg = $this->t("failed cannot insert records");
			}		
		}
		$this->output();
	}
	
	public function actiongetAddressBookByID()
	{
		if ($client_id = $this->checkToken()){
			$id = isset($this->data['id'])?$this->data['id']:'';
			if($id>=1){
				if ($res=Yii::app()->functions->getAddressBookByID($id)){
				unset($res['date_created']);
				unset($res['date_modified']);
				unset($res['ip_address']);
				
				$country_list = require_once('CountryCode.php');
				$res['country_list'] = $country_list;
							
				$this->code = 1;
				$this->msg = "ok";
				$this->details = array(
				  'data'=>$res
				);
			} else $this->msg = $this->t("Record not found. please try again later");
			} else $this->msg = $this->t("Invalid id");
		}
		$this->output();
	}
	
	public function actionGetProfile()
	{
		$data=array();
		if ($client_id = $this->checkToken()){
			if($res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
				$data['avatar'] = mobileWrapper::getImage($res['avatar'],'avatar.png');
				$data['first_name']=$res['first_name'];
				$data['last_name']=$res['last_name'];
				$data['full_name']=$res['first_name']." ".$res['last_name'];
				$data['email_address']=$res['email_address'];
				$data['contact_phone']=$res['contact_phone'];
				$this->code = 1;
				$this->msg = "ok";
				$this->details = array(
				  'data'=>$data
				);
			} else $this->msg = $this->t("Profile not found");
		}
		$this->output();
	}
	
	public function actionUpdateProfile()
	{
		$this->data = $_POST;		
		if ($client_id = $this->checkToken()){
			$params = array(
			  'first_name'=> isset($this->data['first_name'])?$this->data['first_name']:'',
			  'last_name'=> isset($this->data['last_name'])?$this->data['last_name']:'',
			  'contact_phone'=> isset($this->data['contact_phone'])?$this->data['contact_phone']:'',
			  'date_modified'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR']
			);
			$db = new DbExt();
			if ($db->updateData("{{client}}",$params,'client_id', $client_id) ){
				$this->code = 1;
				$this->msg = $this->t("profile successfully updated");
			} else $this->msg = $this->t("ERROR: cannot update records.");
		}
		$this->output();
	}
	
	public function actionChangePassword()
	{
		$this->data = $_POST;		
		if ($client_id = $this->checkToken()){
			if($res = mobileWrapper::getCustomerByToken($this->data['user_token'])){				
				$current_password = md5($this->data['current_password']);			
				
				$new_password = isset($this->data['new_password'])?$this->data['new_password']:'';
				$cnew_password = isset($this->data['cnew_password'])?$this->data['cnew_password']:'';
				
				if ($new_password!=$cnew_password){
					$this->msg = $this->t("Confirm password does not match");
					$this->output();
				}						
				if ($current_password!=$res['password']){
					$this->msg = $this->t("current password is invalid");
					$this->output();
				}		
				if(md5($new_password)==$res['password']){
					$this->msg = $this->t("new password cannot be same as your old password");
					$this->output();
				}			
				
				$params = array(
				  'password'=>trim(md5($new_password)),
				  'date_modified'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
				
				$db = new DbExt();
				if ($db->updateData("{{client}}",$params,'client_id', $client_id) ){
				  $this->code = 1;
				  $this->msg = $this->t("password successfully updated");
			   } else $this->msg = $this->t("ERROR: cannot update records.");
				
			} else $this->msg = $this->t("Profile not found");
		}
		$this->output();
	}

	public function actionUploadProfile()
	{		
		                
		$this->data = $_POST;
		$profile_photo = '';
		$path_to_upload= FunctionsV3::uploadPath();
				
		if ($client_id = $this->checkToken()){
			if(isset($_FILES['file'])){
				
			   header('Access-Control-Allow-Origin: *');			   	    	
		       $new_image_name = urldecode($_FILES["file"]["name"]).".jpg";	
		       $new_image_name=str_replace(array('?',':'),'',$new_image_name);
		        
		       $upload_res = @move_uploaded_file($_FILES["file"]["tmp_name"], "$path_to_upload/".$new_image_name);

			   if($upload_res){
			        $DbExt=new DbExt;	  			      	
			        $params = array(
			          'avatar'=>$new_image_name,
			          'date_modified'=>FunctionsV3::dateNow(),
			          'ip_address'=>$_SERVER['REMOTE_ADDR']
			        );			        
			        if($DbExt->updateData("{{client}}",$params,'client_id',$client_id)){
			        	$this->code=1;
						$this->msg=self::t("Upload successful");
						$this->details=$new_image_name;
						$profile_photo = mobileWrapper::getImage($new_image_name,'avatar.png');
			        } else $this->msg = self::t("ERROR: cannot update records.");
			    } else $this->msg = self::t("Cannot upload file");
			    
			} else $this->msg=$this->t("Image is missing");
		}		
		echo "$this->code|$this->msg|$profile_photo";
    	Yii::app()->end();  
	}
	
	public function actionGetMerchantAbout()
	{
		$data = array();
		$merchant_id = $this->merchant_id;
		if ($merchant_id>0){
			if ($res = FunctionsV3::getMerchantInfo($merchant_id)){				
				$data['merchant_id']=$res['merchant_id'];
				$data['restaurant_name']=clearString($res['restaurant_name']);
				$data['complete_address']=clearString($res['complete_address']);				
				$data['restaurant_phone']= $res['restaurant_phone'];
				$data['contact_phone']= $res['contact_phone'];
				$data['latitude']=$res['latitude'];
				$data['lontitude']=$res['lontitude'];
				
				$data['cuisine']=FunctionsV3::displayCuisine($res['cuisine']);		
				$ratings=Yii::app()->functions->getRatings($merchant_id); 	
				$data['rating']=$ratings;	
				$data['review_count'] = mobileWrapper::t("[count] reviews",array(
		 			  '[count]'=>$ratings['votes']
		 			));
								
				$data['opening'] = array();
				if ( $opening=FunctionsV3::getMerchantOpeningHours($merchant_id)){
					foreach ($opening as $val){
						$new_hours[]=array(
						  'day'=> ucwords(t($val['day'])) ,
						  'hours'=>$val['hours'],
						  'open_text'=>t($val['open_text']),
						);
					}
					$data['opening']=$new_hours;
				} 
				
				$data['payment'] = array(); 
				$payment_list_new = array();
				if($payment = FunctionsV3::getMerchantPaymentListNew($merchant_id)){
					 foreach ($payment as $payment_list_key=>$payment_list_val) {
				   		$payment_list_new[] = array(
				   		  'label'=>mt($payment_list_val)
				   		);
				   	}	
				   	$data['payment']=$payment_list_new;
				}
												
				$data['information'] = nl2br(clearString(getOption($merchant_id,'merchant_information')));
				$data['website'] =  getOption($merchant_id,'merchant_extenal');
				if(!empty($data['website'])){
					$data['website'] = FunctionsV3::prettyUrl($data['website']);
				}			
				
				$services_list='';
				$services = Yii::app()->functions->DeliveryOptions($merchant_id);
				foreach ($services as $val) {					
					$services_list.= t($val).",";
				}
				$services_list = substr($services_list,0,-1);
				$data['services']=$services_list;
				
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'data'=>$data
				);
				
			} else $this->msg = $this->t("merchant id not found");
		} else $this->msg = $this->t("invalid merchant id");
		$this->output();
	}
	
	public function actionReviewList()
	{
		$website_title = getOptionA('website_title');
		
		$pagelimit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $pagelimit;
        } else  $page = 0; 
        
        $paginate_total=0; 
        $limit="LIMIT $page,$pagelimit";         
        $db = new DbExt();
        
        $stmt="
        SELECT SQL_CALC_FOUND_ROWS 
        a.id,
        a.merchant_id,
        a.client_id,
        a.review,
        a.rating,
        a.as_anonymous,
        a.date_created,
        concat(b.first_name,' ',b.last_name) as customer_name,
        b.avatar
        
        FROM {{review}} a
        left join {{client}} b
        ON
        a.client_id = b.client_id
        
        WHERE a.status='publish'
        AND a.merchant_id=".FunctionsV3::q($this->merchant_id)."        
        
        ORDER BY a.id DESC
		$limit
        ";        
        if($res = $db->rst($stmt)){
        	$total_records=0;
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ($resp=$db->rst($stmtc)){			 			
				$total_records=$resp[0]['total_records'];
			}					
			$paginate_total = ceil( $total_records / $pagelimit );
			
			$data = array();
			foreach ($res as $val) {						
				if($val['as_anonymous']==1){
					$val['customer_name'] = mobileWrapper::t("By [sitename] Customer",array(
					  '[sitename]'=>$website_title
					));
					$val['avatar'] = mobileWrapper::getImage('x.png','avatar.png');
				} else {
					$val['avatar'] = mobileWrapper::getImage($val['avatar'],'avatar.png');
					$val['customer_name'] = mobileWrapper::t("By [customer_name]",array(
					  '[customer_name]'=>$val['customer_name']
					));
				}		

				$pretyy_date=PrettyDateTime::parse(new DateTime($val['date_created']));
		        $pretyy_date=Yii::app()->functions->translateDate($pretyy_date);
		        $val['date_posted']=$pretyy_date;
		        
		        $val['reply'] = mobileWrapper::getReviewReplied($val['id'],$val['merchant_id']);
		        		    					
				$data[]=$val;
			}
			
			$this->code = 1;
			$this->msg="OK";
			$this->details = array( 			   
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$paginate_total,
			  'element'=>".reviews_loader",
			  'element_list'=>"#reviews_list_item",
			  'data'=>$data
			);
			
        } else {
        	$this->code = 6;
        	$this->msg = $this->t("No available review");
        	$this->details = array(
        	  'element'=>".reviews_loader",
        	  'element_list'=>"#reviews_list_item",
        	  'message'=>$this->t("be the first one to leave review order now!")
        	);
        }	
        
		$this->output();
	}
	
	public function actionGetMerchantDateList()
	{		
		$customer = array();
		if(isset($this->data['user_token'])){
			if($res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
				$customer['name'] = $res['first_name']." ".$res['last_name'];
				$customer['email_address']=$res['email_address'];
				$customer['contact_phone']=$res['contact_phone'];
			}		
		}
		if ($res = FunctionsV3::getDateList($this->merchant_id)){
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			   'customer'=>array($customer),
			  'data'=>$res
			);
		} else $this->msg = $this->t("No results");
		$this->output();
	}
	
	public function actionGetMerchantTimeList()
	{
		$date = isset($this->data['date'])?$this->data['date']:date("Y-m-d");					
		if ($res = FunctionsV3::getTimeList($this->merchant_id,$date)){
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$res
			);
		} else $this->msg = $this->t("No results");
		$this->output();
	}
	
	public function actionSaveBooking()
	{
		$this->data = $_POST;
		$merchant_id = isset($this->data['book_merchant_id'])?$this->data['book_merchant_id']:'';
		
		if($merchant_id<=0){
		   $this->msg = $this->t("invalid merchant id");
		   $this->output();
		}	
			
		if ( isset($this->data['booking_time'])){
       	  if(!empty($this->data['booking_time'])){
       	  	 $time_1=date('Y-m-d g:i:s a');
       	  	 $time_2=$this->data['date_booking']." ".$this->data['booking_time'];       	  	 
       	  	 $time_2=date("Y-m-d g:i:s a",strtotime($time_2));	     	       	  	 
       	  	 $time_diff=Yii::app()->functions->dateDifference($time_2,$time_1);	       	  		       	  	        	  	        	  	 
       	  	 if (is_array($time_diff) && count($time_diff)>=1){
       	  	     if ( $time_diff['hours']>0){	       	  	     	
	       	  	     $this->msg=$this->t("Sorry but you have selected time that already past");
	       	  	     $this->output(); 	  	     	
       	  	     }	       	  	
       	  	     if ( $time_diff['minutes']>0){	       	  	     	
	       	  	     $this->msg=$this->t("Sorry but you have selected time that already past");
	       	  	     $this->output();  	  	     	
       	  	     }	       	  	
       	  	 }	       	  
       	  }	       
       }		   
       
        $full_booking_time=$this->data['date_booking']." ".$this->data['booking_time'];
	    $full_booking_day=strtolower(date("D",strtotime($full_booking_time)));			
	    $booking_time=date('h:i A',strtotime($full_booking_time));	  
	    
	     if ( !Yii::app()->functions->isMerchantOpenTimes($merchant_id,$full_booking_day,$booking_time)){			
			$this->msg = Yii::t("mobile2","Sorry but we are closed on [date]. Please check merchant opening hours",array(
			  '[date]'=>date("F,d Y h:ia",strtotime($full_booking_time))
			));
		    $this->output();  	 
		}		
				
		$now=isset($this->data['date_booking'])?$this->data['date_booking']:'';			
		$merchant_close_msg_holiday='';
	    $is_holiday=false;
	    if ( $m_holiday=Yii::app()->functions->getMerchantHoliday($merchant_id)){
      	    if (in_array($now,(array)$m_holiday)){
      	   	    $is_holiday=true;
      	    }
	    }
	    if ( $is_holiday==true){
	    	$merchant_close_msg_holiday=!empty($merchant_close_msg_holiday)?$merchant_close_msg_holiday:$this->t("Sorry but we are on holiday on")." ".date("F d Y",strtotime($now));
	    	$this->msg=$merchant_close_msg_holiday;
	    	$this->output();  
	    }		  
	    
	    $fully_booked_msg=Yii::app()->functions->getOption("fully_booked_msg",$merchant_id);
		if (!Yii::app()->functions->bookedAvailable($merchant_id)){
		   if (!empty($fully_booked_msg)){
		    		$this->msg=t($fully_booked_msg);
		   } else $this->msg=$this->t("Sorry we are fully booked for that day");			 	
		   $this->output();  
		}  
		
		$params=array(
		  'merchant_id'=>$merchant_id,
		  'number_guest'=>isset($this->data['number_guest'])?$this->data['number_guest']:'',
		  'date_booking'=>isset($this->data['date_booking'])?$this->data['date_booking']:'',
		  'booking_time'=>isset($this->data['booking_time'])?$this->data['booking_time']:'',
		  'booking_name'=>isset($this->data['booking_name'])?$this->data['booking_name']:'',
		  'email'=>isset($this->data['email'])?$this->data['email']:'',
		  'mobile'=>isset($this->data['mobile'])?$this->data['mobile']:'',
		  'booking_notes'=>isset($this->data['booking_notes'])?$this->data['booking_notes']:'',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],		  
		);
		if ($client_id = $this->checkToken()){
			$params['client_id']= $client_id;
		}
		$db=new DbExt;				
		if ( $db->insertData('{{bookingtable}}',$params)){			
			$booking_id=Yii::app()->db->getLastInsertID();
			$this->code=1;			
			
			$this->msg = Yii::t("mobile2","Your booking has been placed. Reference # [booking_id]",array(
				      '[booking_id]'=>$booking_id
				    ));
			
			$this->details = $booking_id;
			
			$merchant_name = '';
			if ($merchant_info = FunctionsV3::getMerchantInfo($merchant_id)){
				$merchant_name = $merchant_info['restaurant_name'];
			}
			
			/*SEND NOTIFICATIONS*/		
			$new_data = $params;	
			$new_data['restaurant_name']=$merchant_name;
		    $new_data['booking_id']=$booking_id;			    
		    if(method_exists("FunctionsV3","notifyBooking")){
		       FunctionsV3::notifyBooking($new_data);
		    }
		    
		    /*POINTS PROGRAM*/		    		
    		if (FunctionsV3::hasModuleAddon("pointsprogram")){
    		   PointsProgram::rewardsBookTable($booking_id , isset($params['client_id'])?$params['client_id']:'' , $merchant_id );
    		}
			    
		} else $this->msg = $this->t("Something went wrong during processing your request. Please try again later");
				
		$this->output();
	}
	
	public function actionGetGallery()
	{		
		$data = array();
		if($this->merchant_id>0){
		    $gallery=mobileWrapper::getMerchantGallery($this->merchant_id);		    
		    if(is_array($gallery) && count($gallery)>=1){
		    	$data['gallery']=$gallery;
			    $this->code = 1;
			    $this->msg = "OK";
			    $this->details = array(
			       'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			       'data'=>$data
			    );
		    } else {
		    	$this->code = 6;		    	
		    	$this->msg = $this->t("No images found");							
				$this->details = array(
				   'element'=>".gallery_loader",
	        	   'element_list'=>"#list_cuisine",
	        	   'message'=>$this->t("gallery not available")
				);
		    }		
		} else {			
			$this->code = 6;		    	
	    	$this->msg = $this->t("invalid merchant id");
			$this->details = array(
			   'element'=>".gallery_loader",
        	   'element_list'=>"#list_cuisine",
        	   'message'=>$this->t("gallery not available")
			);
		}	
		$this->output();
	}
	
	public function actionGetMerchantInformation()
	{		
		$data = array();
		if($this->merchant_id>0){
			$data['information'] = nl2br(clearString(getOption($this->merchant_id,'merchant_information')));
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array( 
			 'data'=>$data
			);
		} else {
			$this->code = 6;		    	
	    	$this->msg = $this->t("invalid merchant id");
			$this->details = array(
			   'element'=>".information_loader",
        	   'element_list'=>"#test",
        	   'message'=>$this->t("information not available")
			);
		}	
		$this->output();
	}
	
	public function actionGetMerchantPromo()
	{
		$data = array();
		if($this->merchant_id>0){
			$merchant_id =  $this->merchant_id;
			$promo = array();
    		$promo['enabled']=1;
    		
    		if (method_exists("FunctionsV3","getOffersByMerchantNew")){	
	    		if($offer=FunctionsV3::getOffersByMerchantNew($merchant_id)){
		    	   $promo['offer']=$offer;
		    	   $promo['enabled']=2;
		    	}		    	
    		}
	    	
    		if (method_exists("FunctionsV3","merchantActiveVoucher")){			
		    	if ( $voucher=FunctionsV3::merchantActiveVoucher($merchant_id)){		    	    		
		    		$promo['enabled']=2;	    		
		    		foreach ($voucher as $val) {
		    			if ( $val['voucher_type']=="fixed amount"){
				      	  $amount=FunctionsV3::prettyPrice($val['amount']);
				        } else $amount=number_format( ($val['amount']/100)*100 )."%";
				        				        
				        $promo['voucher'][] = mt("[discount] off | Use coupon [code]",array(
				          '[discount]'=>$amount,
				          '[code]'=>$val['voucher_name']
				        ));
		    		}	    		 	    		
		    	}
    		}
	    	
	    	$free_delivery_above_price=getOption($merchant_id,'free_delivery_above_price');
	    	if ($free_delivery_above_price>0){
	    	    $promo['free_delivery'][0]=$this->t("Free Delivery On Orders Over")." ". FunctionsV3::prettyPrice($free_delivery_above_price);
	    		$promo['enabled']=2;
	    	}
	    		    
	    	if($promo['enabled']==1){
	    		$this->code = 6;
	    		$this->msg = $this->t("No available promos");
	    		$this->details = array(
				   'element'=>".promos_loader",
	        	   'element_list'=>"#promo_list_item",
	        	   'message'=>$this->t("no promo available for this merchant")
				);
	    	} else {
	    		$this->code = 1;
	    	    $this->msg = "OK";
	    	    $this->details = array(
		    	  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
		    	  'data'=>$promo
		    	);
	    	}    		    		    	    	
	    	
		} else {
			$this->code = 6;		    	
	    	$this->msg = $this->t("invalid merchant id");
			$this->details = array(
			   'element'=>".promos_loader",
        	   'element_list'=>"#promo_list_item",
        	   'message'=>$this->t("no promo available")
			);
		}	
		$this->output();
	}
	
	public function actionGetPointSummary()
	{
		if (!FunctionsV3::hasModuleAddon("pointsprogram")){
			$this->code = 6;
			$this->msg = $this->t("Points not available");
								
			$this->details = array(
			   'element'=>".points_list_loader",
        	   'element_list'=>"#points_list_item",
        	   'message'=>$this->t("points addon for this app is not install properly")
			);
			$this->output();
		}
		
		$data = array();
		if ($client_id = $this->checkToken()){
			
			$total_available_pts = PointsProgram::getTotalEarnPoints($client_id);		
	    	$total_expiring_pts = PointsProgram::getExpiringPoints($client_id);
	    	$total_expenses = mobileWrapper::pointsTotalExpenses($client_id);
	    	$total_earn_by_merchant = mobileWrapper::pointsEarnByMerchant($client_id);
	    	
	    	$data[]=array(
	    	  'label'=>$this->t("Income Points"),
	    	  'value'=>$total_available_pts>0?$total_available_pts:0,
	    	  'point_type'=>'income_points'
	    	);
	    	
	    	$data[]=array(
	    	  'label'=>$this->t("Expenses Points"),
	    	  'value'=>$total_expenses>0?$total_expenses:0,
	    	  'point_type'=>'expenses_points'
	    	);
	    	
	    	$data[]=array(
	    	  'label'=>$this->t("Expired Points"),
	    	  'value'=>$total_expiring_pts>0?$total_expiring_pts:0,
	    	  'point_type'=>'expired_points'
	    	);
	    	
	    	$data[]=array(
	    	  'label'=>$this->t("Points By Merchant"),
	    	  'value'=>$total_earn_by_merchant,
	    	  'point_type'=>'points_merchant'
	    	);
	    	
	    	$this->code = 1;
	    	$this->msg="OK";
	    	$this->details=array(
	    	  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
	    	  'data'=>$data
	    	);
		}
		$this->output();
	}
	
	public function actionGetPointDetails()
	{
		$pagelimit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $pagelimit;
        } else  $page = 0; 
        
        $paginate_total=0; $page_title=''; $stmt='';
        $limit="LIMIT $page,$pagelimit"; 
        
		if ($client_id = $this->checkToken()){
			$point_type = isset($this->data['point_type'])?$this->data['point_type']:'';
			switch ($point_type){
				case "income_points":
					
					$page_title = $this->t("Income Points");
					
					$stmt="
					SELECT SQL_CALC_FOUND_ROWS 
					a.trans_type,
					a.order_id,
					a.date_created,
					a.total_points_earn
					FROM
					{{points_earn}} a
					WHERE
					status='active'
					AND
					client_id=".FunctionsV3::q($client_id)."
					ORDER BY id DESC
					$limit
					";
					break;
					
				case "expenses_points":
					
					$page_title = $this->t("Expenses Points");
					
					$stmt="
					SELECT SQL_CALC_FOUND_ROWS 
					a.points_type,
					a.trans_type,
					a.order_id,
					a.total_points,
					a.date_created
					FROM
					{{points_expenses}} a
					WHERE
					status='active'
					AND
					client_id=".FunctionsV3::q($client_id)."
					ORDER BY id DESC
					$limit
					";					
					break;
					
				case "expired_points":	
				
				    $page_title = $this->t("Expired Points");
					
					$stmt="
					SELECT SQL_CALC_FOUND_ROWS 
					a.points_type,
					a.trans_type,
					a.order_id,
					a.date_created,
					a.total_points_earn
					FROM
					{{points_earn}} a
					WHERE
					status='expired'
					AND
					client_id=".FunctionsV3::q($client_id)."
					ORDER BY id DESC
					$limit					
					";		
				
				   break;
				   
				case "points_merchant":
					
					$page_title = $this->t("Points By Merchant");
					
					$stmt="
					SELECT SQL_CALC_FOUND_ROWS 
					a.merchant_id,
					b.restaurant_name,
					b.restaurant_slug
					FROM {{points_earn}} a		
					LEFT JOIN {{merchant}} b
					ON
					a.merchant_id=b.merchant_id		
					WHERE
					a.merchant_id <> 0
					and
					client_id=".FunctionsV3::q($client_id)."
					GROUP BY a.merchant_id		
					ORDER BY b.restaurant_name ASC
					$limit					
					";		
					
					break;
			}			
			
			$data = array();
			$db=new DbExt();
												
			if($res = $db->rst($stmt)){
				foreach ($res as $val) {
					switch ($point_type) {
						case "income_points":					    
							$label=PointsProgram::PointsDefinition('earn',$val['trans_type'],$val['order_id']);
							$data[]=array(
							  'date'=>FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']),
							  'label'=>$label,
							  'points'=>$val['total_points_earn']
							);
							break;
							
						case "expenses_points":	
							$label=PointsProgram::PointsDefinition($val['points_type'],$val['trans_type'],
							$val['order_id'],$val['total_points']);
							$data[]=array(
							  'date'=>FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']),
							  'label'=>$label,
							  'points'=>$val['total_points']
							);
						  break;
						  
						case "expired_points":
							$label=PointsProgram::PointsDefinition($val['points_type'],$val['trans_type'],
							$val['order_id'],$val['total_points_earn']);
							
							$data[]=array(
							  'date'=>FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']),
							  'label'=>$label,
							  'points'=>$val['total_points_earn']
							);  
							break;  
					
						case "points_merchant":	
						    
						    $points = mobileWrapper::getTotalEarnPoints($client_id,$val['merchant_id'],1);
							$data[]=array(
							  'date'=>clearString($val['restaurant_name']),
							  'label'=>$this->t("Merchant Name"),
							  'points'=>$points>0?$points:0
							);
						    break;
						    
						default:
							break;
					}
					
				}
				$this->code = 1; $this->msg = "ok";
				$this->details = array(
				  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
				  'page_title'=>$page_title,
				  'data'=>$data
				);
			} else {
				$this->code = 6;
				$this->msg = $this->t("No results");
								
				$this->details = array(
				   'element'=>".points_details_loader",
	        	   'element_list'=>"#points_details_item",
	        	   'message'=>mt('points details not found')
				);
			}		
		}
		$this->output();
	}
	
	public function actionsearchFoodCategory()
	{
		$item_name = isset($this->data['item_name'])?$this->data['item_name']:'';
		if($this->merchant_id>0){
			if(!empty($item_name)){
				if($res = itemWrapper::searchByCategoryByName($this->merchant_id,$item_name)){
				   $data = array();
				   foreach ($res as $val) {				   	 
				   	 $val['category_name'] = mobileWrapper::highlight_word($val['category_name'],$item_name);
				     $val['photo']=mobileWrapper::getImage($val['photo']);
				   	 $val['category_description']=strip_tags($val['category_description']);				
				   	 $val['category_description'] = mobileWrapper::highlight_word($val['category_description'],$item_name);
				     $data[]=$val;				   	
				   }
				   
				   $this->code = 1;
				   $this->msg = "OK";
				   $this->details = array(
					 'data'=>$data
					);
				} else $this->msg = $this->t("No results");
			} else $this->msg = $this->t("invalid search string");
		} else $this->msg = $this->t("invalid merchant id");
		$this->output();
	}
	
	public function actionGetRecentLocation()
	{
		if(!empty($this->device_uiid)){
			
			$page_limit = mobileWrapper::paginateLimit();		
			if (isset($this->data['page'])){
	        	$page = $this->data['page'] * $page_limit;
	        } else  $page = 0; 
	
	        $paginate_total=0; 
	        $limit="LIMIT $page,$page_limit"; 
	        
	        $db = new DbExt();
	        $stmt="
	        SELECT SQL_CALC_FOUND_ROWS 
	        a.*
	        FROM {{mobile2_recent_location}} a
	        WHERE 
	        device_uiid = ".FunctionsV3::q($this->device_uiid)."
	        
	        ORDER BY a.id DESC
		    $limit
	        ";	        
	        if($res=$db->rst($stmt)){
	        	$total_records=0;
				$stmtc="SELECT FOUND_ROWS() as total_records";
				if ($resp=$db->rst($stmtc)){			 			
					$total_records=$resp[0]['total_records'];
				}					
				$paginate_total = ceil( $total_records / $page_limit );
				
				foreach ($res as $val) {
					$val['date_created']=FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
					$data[]=$val;
				}
				
				$this->code = 1;
				$this->msg="OK";
				$this->details = array( 
				  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
				  'paginate_total'=>$paginate_total,
				  'data'=>$data
				);
				
	        } else $this->msg = $this->t("No results");
		} else $this->msg = $this->t("invalid device uiid");
				
		$this->output();
	}

	public function actionSetLocation()
	{
		$this->getPOSTData();
		$this->data = $_POST;
		$lat = isset($this->data['lat'])?$this->data['lat']:'';
		$lng = isset($this->data['lng'])?$this->data['lng']:'';
		$recent_search_address = isset($this->data['recent_search_address'])?$this->data['recent_search_address']:'';
		
		if(empty($recent_search_address)){
			$this->msg = $this->t("invalid location");
			$this->output();
		}	
		
		if(!empty($lat) && !empty($lng)){
			
			$params = array(
			  'device_uiid'=>$this->device_uiid,
			  'search_address'=>$recent_search_address,
			  'street'=>isset($this->data['street'])?$this->data['street']:'',
			  'city'=>isset($this->data['city'])?$this->data['city']:'',
			  'state'=>isset($this->data['state'])?$this->data['state']:'',
			  'zipcode'=>isset($this->data['zipcode'])?$this->data['zipcode']:'',
			  'location_name'=>isset($this->data['location_name'])?$this->data['location_name']:'',
			  'country'=>isset($this->data['country'])?$this->data['country']:'',
			  'latitude'=>$lat,
			  'longitude'=>$lng,
			  'date_created'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR']
			);				
			
			if(!$res_recent = mobileWrapper::getRecentLocation($this->device_uiid,$lat,$lng)){					
				Yii::app()->db->createCommand()->insert("{{mobile2_recent_location}}",$params);
			} else {
				$id = (integer)$res_recent['id'];								
				Yii::app()->db->createCommand()->update("{{mobile2_recent_location}}",$params,
          	     'id=:id', 
          	     array(
          	       ':id'=>$id
          	     )
          	   );
			}
			unset($db);
			
			$data = array(
			  'recent_search_address'=>$recent_search_address,
			  'formatted_address'=>$recent_search_address,
			  'lat'=>$lat,
			  'lng'=>$lng,
			  'street'=>$params['street'],
			  'city'=>$params['city'],
			  'state'=>$params['state'],
			  'zipcode'=>$params['zipcode'],
			  'country'=>$params['country'],			  
			);
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$data
			);
			
		} else $this->msg = $this->t("invalid location");
		$this->output();
	}
	
	public function actionAddFavorite()
	{		
		if ($client_id = $this->checkToken()){
			if($this->merchant_id>0){
				$res = FunctionsV3::addToFavorites($client_id,$this->merchant_id);
				$this->code = 1;
				if($res==1){
				   $this->msg= $this->t("successfully added to your favorite list");
				} else $this->msg= $this->t("successfully remove to your favorite list");					
				$this->details = array(
				  'added'=>$res==true?true:false,
				  'id'=>$this->merchant_id
				);
			} else $this->msg = $this->t("invalid merchant id");
		} else $this->msg = $this->t("You need to login to add this restaurant to your favorites");
		$this->output();
	}
	
	public function actionsearchMerchantFood()
	{
		$search_resp = mobileWrapper::searchMode();
		$search_mode = $search_resp['search_mode'];
		$location_mode = $search_resp['location_mode'];	
		
		$search_string = isset($this->data['search_string'])?$this->data['search_string']:'';
		if(!empty($search_string)){				
			$stmt="
			SELECT 
			a.merchant_id,
			a.merchant_id as id,
			a.restaurant_name as title,
			a.cuisine as sub_title,
			a.logo as logo,
			'restaurant',
			a.service as category,
			a.merchant_id as mmtid
			
			FROM  {{merchant}} a
			WHERE restaurant_name LIKE ".FunctionsV3::q("%$search_string%")."
			AND a.status = 'active' AND a.is_ready='2'
			
			UNION ALL
			SELECT 
			b.merchant_id,
			b.item_id as id,
			b.item_name as title,
			b.item_description as sub_title,
			b.photo as logo,					
			'food',
			b.category as category,
			c.merchant_id as mmtid
			
			FROM {{item}} b						
			left join {{merchant}} c
			
			On b.merchant_id = c.merchant_id
			
			WHERE 
			b.item_name LIKE ".FunctionsV3::q("%$search_string%")."
			AND b.status ='publish'
			AND c.status='active'
			AND c.is_ready='2'
			
			
			UNION ALL
			SELECT 
			c.cuisine_id as merchant_id,
			c.cuisine_id,
			c.cuisine_name as title,
			c.cuisine_name as sub_title,
			c.featured_image as logo,
			'cuisine',
			c.cuisine_name as category,
			c.cuisine_id as mmtid
			
			FROM {{cuisine}} c
			WHERE c.cuisine_name LIKE ".FunctionsV3::q("%$search_string%")."
			AND c.status = 'publish'
			LIMIT 0,10
			";	
					
			
			$show_delivery_fee = false;
			$options = mobileWrapper::getDataSearchOptions();						
			if(in_array('delivery_fee',$options)){
				$show_delivery_fee = true;
			}		
			
			$lat = isset($this->data['lat'])?$this->data['lat']:'';
			$lng = isset($this->data['lng'])?$this->data['lng']:'';		
			
			if($search_mode=="address"){
				$home_search_unit_type=getOptionA('home_search_unit_type');
				$distance_exp=3959;
				if ($home_search_unit_type=="km"){
					$distance_exp=6371;
				}	
				
				$query_distance="
				( $distance_exp * acos( cos( radians($lat) ) * cos( radians( latitude ) ) 
						* cos( radians( lontitude ) - radians($lng) ) 
						+ sin( radians($lat) ) * sin( radians( latitude ) ) ) ) 
						AS distance		
				";			
			}
			
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				$res = Yii::app()->request->stripSlashes($res);
				$data = array();
				foreach ($res as $val) {
					$val['title'] = clearString($val['title']);
					$val['title'] = mobileWrapper::highlight_word($val['title'],$search_string);
					$val['logo']=mobileWrapper::getImage($val['logo']);					
					if ($val['restaurant']=="restaurant"){
						$val['sub_title']=FunctionsV3::displayCuisine($val['sub_title']);						
					} elseif ($val['restaurant']=="cuisine" ) {
						
						if($search_mode=="location"){        			
		        			$total= mobileWrapper::getTotalCuisineByLocation($val['id'],$location_mode,array(
		        			  'state_id'=>isset($this->data['state_id'])?$this->data['state_id']:-1,
		        			  'city_id'=>isset($this->data['city_id'])?$this->data['city_id']:-1,
		        			  'area_id'=>isset($this->data['area_id'])?$this->data['area_id']:-1,
		        			  'postal_code'=>isset($this->data['postal_code'])?$this->data['postal_code']:-1,
		        			));        			
		        		} else $total = mobileWrapper::getTotalCuisine($val['id'],$query_distance);    
		        		
		        		$val['sub_title'] = mobileWrapper::t("[total] restaurant",array('[total]'=>$total));
        		
					} else {
						$category = json_decode($val['category'],true);
						if(is_array($category) && count((array)$category)>=1){
						   $val['category']=$category[0];
						} else $val['category']='';
					}				
					
					$val['delivery_fee'] = '';
										
					$data[]=$val;
				}
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'data'=>$data
				);
				
				if(strlen($search_string)>=6){
					$params = array(
					  'device_uiid'=>$this->device_uiid,
					  'search_string'=>$search_string,
					  'date_created'=>FunctionsV3::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR']
					);
					if(!mobileWrapper::getRecentSearchs($this->device_uiid,$search_string)){
						 $db->insertData("{{mobile2_recent_search}}",$params);
					}			
				}
				
			} else $this->msg = $this->t("No results"); 		
		} else $this->msg = $this->t("invalid search string");
		$this->output();
	}
	
	public function actionGetRecentSearch()
	{		
		if(!empty($this->device_uiid)){			
			
			$page_limit = mobileWrapper::paginateLimit();		
			if (isset($this->data['page'])){
	        	$page = $this->data['page'] * $page_limit;
	        } else  $page = 0; 
	
	        $paginate_total=0; 
	        $limit="LIMIT $page,$page_limit"; 
	        
	        $db = new DbExt();
	        $stmt="
	        SELECT SQL_CALC_FOUND_ROWS 
	        a.device_uiid,
	        a.search_string
	        FROM {{mobile2_recent_search}} a
	        WHERE 
	        device_uiid = ".FunctionsV3::q($this->device_uiid)."	        
	        ORDER BY a.id DESC
		    $limit
	        ";	        	              
	        if($res=$db->rst($stmt)){
	        	$total_records=0;
				$stmtc="SELECT FOUND_ROWS() as total_records";
				if ($resp=$db->rst($stmtc)){			 			
					$total_records=$resp[0]['total_records'];
				}					
				$paginate_total = ceil( $total_records / $page_limit );
				
				foreach ($res as $val) {
					$data[]=$val;
				}
				
				$this->code = 1;
				$this->msg="OK";
				$this->details = array( 
				  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
				  'paginate_total'=>$paginate_total,
				  'data'=>$data
				);
				
	        } else $this->msg = $this->t("No results");			
			
		} else $this->msg = $this->t("invalid device uiid");
		$this->output();
	}
	
	public function actionDriverSignup()
	{
		$this->data = $_POST;
		
		if (FunctionsV3::hasModuleAddon('driver')){
			$Validator=new Validator;
			$req=array(
	    	  'first_name'=>t("First name is required"),
	    	  'last_name'=>t("Last name is required"),
	    	  'email'=>t("Email is required"),
	    	  'phone'=>t("Mobile number is required"),
	    	  'username'=>t("Username is required"),
	    	  'password'=>t("Password is required"),	    	  
	    	);
	    	
			if ( Driver::getDriverByUsername($this->data['username'])){			
				$Validator->msg[]=t("Username already exist");
			}			
			if ( Driver::getDriverByEmail($this->data['email'])){			
				$Validator->msg[]=t("Email already exist");
			}			
			if (isset($this->data['phone'])){
				if ( strlen($this->data['phone']<10)){
					$Validator->msg[]=t("Mobile number is required");
				}
			}
			if(isset($this->data['password'])){					
				if($this->data['password']!=$this->data['cpassword']){					
					$Validator->msg[]=t("Confirm password does not match");
				}			
			}	
			
			$Validator->email(array(
			  'email'=>"Invalid email address"
			),$this->data);
			
			$Validator->required($req,$this->data);
			if ( $Validator->validate()){
				
				$admin_id=Driver::getAdminID();
	    		$status=getOptionA('driver_signup_status');
	    		if(empty($status)){
	    			$status='pending';
	    		}
	    		$params=array(
	    		  'first_name'=>$this->data['first_name'],
	    		  'last_name'=>$this->data['last_name'],
	    		  'email'=>$this->data['email'],
	    		  'phone'=>$this->data['phone'],
	    		  'username'=>$this->data['username'],
	    		  'password'=>md5($this->data['password']),
	    		  'transport_type_id'=>$this->data['transport_type_id'],
	    		  'transport_description'=>isset($this->data['transport_description'])?$this->data['transport_description']:'',
	              'licence_plate'=>isset($this->data['licence_plate'])?$this->data['licence_plate']:'',
	              'color'=>isset($this->data['color'])?$this->data['color']:'',
	              'date_created'=>FunctionsV3::dateNow(),
	              'ip_address'=>$_SERVER['REMOTE_ADDR'],
	              'status'=>$status,
	              'user_type'=>"admin",
	              'user_id'=>isset($admin_id['admin_id'])?$admin_id['admin_id']:'',
	              'is_signup'=>1
	    		);
	    		$db=new DbExt;
	    		if ( $db->insertData("{{driver}}",$params)){
	    			$this->code=1;
	    			
	    			if ( $status=="active"){
    			      $this->msg=t("Signup successful");
    			    } else $this->msg=t("Your request has been receive please wait while we validate your application");
    			        			    
	    			 /*send email to admin*/
		    		$driver_enabled_signup=getOptionA('driver_enabled_signup');
		    		if($driver_enabled_signup==1){
		    			$admin_email=getOptionA('driver_send_admin_notification_email');
		    			if(!empty($admin_email)){
		    				$tpl=EmailTemplate::newDriverSignup();
		    				$tpl=Driver::smarty('full_name',$this->data['first_name']." ".
		    				$this->data['last_name']
		    				,$tpl);
		    				$tpl=Driver::smarty('email',$this->data['email'],$tpl);
		    				$tpl=Driver::smarty('phone',$this->data['phone'],$tpl);
		    				$tpl=Driver::smarty('username',$this->data['username'],$tpl);
		    				$tpl=Driver::smarty('transport_type_id',$this->data['transport_type_id'],$tpl);    				
		    				Yii::app()->functions->sendEmail(
		    				  $admin_email,'',t("New driver Signup"),$tpl
		    				);
		    			}
		    		}
		    		
		    		/*send welcome email*/
		    		$DRIVER_NEW_SIGNUP_EMAIL=getOptionA('DRIVER_NEW_SIGNUP_EMAIL');
		    		$DRIVER_NEW_SIGNUP_EMAIL_TPL=getOptionA('DRIVER_NEW_SIGNUP_EMAIL_TPL');    		
		    		if ( $DRIVER_NEW_SIGNUP_EMAIL==1 && !empty($DRIVER_NEW_SIGNUP_EMAIL_TPL) ){
		    			$tpl=$DRIVER_NEW_SIGNUP_EMAIL_TPL;
		    			$company_name=Yii ::app()->functions->getOptionAdmin('website_title');  
		    			$tpl=Driver::smarty('DriverName',$this->data['first_name'],$tpl);
		    			$tpl=Driver::smarty('CompanyName',$company_name,$tpl);
		    			Yii::app()->functions->sendEmail(
						  $this->data['email'],'',t("Thank you for signing up"),$tpl
						);
		    		}
		    		
	    		} else $this->msg = t("Something went wrong please try again later");
			} else $this->msg = mobileWrapper::parseValidatorError($Validator->getError());			
		} else $this->msg = t("Failed. cannot find driver addon");
		$this->output();
	}
	
	public function actionsendOrderSMSCode()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			return false;
		}				
		$client_id = $res['client_id'];	
		$contact_phone = $res['contact_phone'];
		
		if(empty($contact_phone)){
			$this->msg = $this->t("We cannot send sms code to your phone number cause its empty. please fixed by putting mobile number into your profile");
			$this->output();
		}
				
		$sms_balance=Yii::app()->functions->getMerchantSMSCredit($this->merchant_id);
		if ( $sms_balance>=1){
			 $code=FunctionsK::generateSMSOrderCode($contact_phone);
			 $sms_msg= Yii::t("mobile2","Your order sms code is [code]",array(
			  '[code]'=>$code
			));		
			if ( $resp=Yii::app()->functions->sendSMS($contact_phone,$sms_msg)){
				if ($resp['msg']!="process"){
					
					$sms_order_session = Yii::app()->functions->generateCode(50);
					
					$this->code=1;
				    $this->msg= Yii::t("mobile2","Your order sms code has been sent to [mobile]",array(
				     '[mobile]'=>$contact_phone
				    ));
				    
				    $this->details = array(
				      'sms_order_session'=>$sms_order_session
				    );			
				    
				    $contact_phone = str_replace("+","",$contact_phone);
				    $params=array(
			    	  'mobile'=>trim($contact_phone),
			    	  'code'=>$code,
			    	  'session'=>$sms_order_session,
			    	  'date_created'=>FunctionsV3::dateNow(),
			    	  'ip_address'=>$_SERVER['REMOTE_ADDR']
			    	);
			    	$db=new DbExt();
			    	$db->insertData("{{order_sms}}",$params);
			    	
			    	$params=array(
		        	  'merchant_id'=>$this->merchant_id,
		        	  'broadcast_id'=>"999999999",			        	  
		        	  'contact_phone'=>$contact_phone,
		        	  'sms_message'=>$sms_msg,
		        	  'status'=>$resp['msg'],
		        	  'gateway_response'=>$resp['raw'],
		        	  'date_created'=>FunctionsV3::dateNow(),
		        	  'date_executed'=>FunctionsV3::dateNow(),
		        	  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		        	  'gateway'=>$resp['sms_provider']
		        	);	  	
		        	if(!is_numeric($params['merchant_id'])){
		        		unset($params['merchant_id']);
		        	}		        	
		        	$db->insertData("{{sms_broadcast_details}}",$params);	
					
				} else $this->msg=$this->t("Sorry but we cannot send sms code this time");
			} else $this->msg=$this->t("Sorry but we cannot send sms code this time. please try again later");
		} else $this->msg=$this->t("Sorry but this merchant does not have enought sms credit to send sms");		
		
		$this->output();
	}
	
	public function actionverifyOrderSMScode()
	{
		$this->getPOSTData();
		$this->data = $_POST;		
		$sms_order_session = isset($this->data['sms_order_session'])?$this->data['sms_order_session']:'';
		$order_sms_code = isset($this->data['sms_order_session'])?$this->data['order_sms_code']:'';
		
		if($resp = mobileWrapper::validateOrderSMSCode($sms_order_session,$order_sms_code)){
			$this->code = 1;
			$this->msg = "ok";
			$this->details = $resp['id'];
		} else $this->msg = $this->t("Invalid SMS code");
		
		$this->output();
	}
	
	public function actionapplyRedeemPoints()
	{
		$points = isset($this->data['points'])?$this->data['points']:0;
		
		if (!is_numeric($this->merchant_id)){
			$this->msg = $this->t("Invalid merchant id");
			$this->output();
		}
				
		if($points>0.0001){		
		} else {
			$this->msg = $this->t("Invalid redeem points");
			$this->output();
		}
				
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			return false;
		}				
		$client_id = $res['client_id'];	
		
		$pts_disabled_redeem = getOptionA('pts_disabled_redeem');
		if($pts_disabled_redeem==1){
			$this->msg = $this->t("Redeeming points is disabled");
			$this->output();
		}
		
		/*CHECK POINTS BALANCE*/
		$available_points = PointsProgram::getTotalEarnPoints( $client_id , $this->merchant_id);
		if($available_points<=0){
			$this->msg = $this->t("Sorry but your points is not enough");
			$this->output();
		}	
		
		if($points>$available_points){
			$this->msg = $this->t("Sorry but your points is not enough");
			$this->output();
		}
		
		$data = array(
		  'delivery_type'=>isset($this->data['transaction_type'])?$this->data['transaction_type']:'',
		  'merchant_id'=>$this->merchant_id,
		  'card_fee'=>0
		);			
		if ( $cart = mobileWrapper::getCartContent($this->device_uiid,$data)){
			$is_disabled_merchant_settings = PointsProgram::isMerchantSettingsDisabled();
			
			/*CHECK IF HAS ALREADY DISCOUNT*/			
			$pts_enabled_offers_discount = getOptionA('pts_enabled_offers_discount');
			if(!$is_disabled_merchant_settings){
				$mt_pts_enabled_offers_discount = getOption($this->merchant_id,'mt_pts_enabled_offers_discount');
				if($mt_pts_enabled_offers_discount>0){					
					$pts_enabled_offers_discount = $mt_pts_enabled_offers_discount;
				}			
			}
			
			if($pts_enabled_offers_discount!=1){
				$discounted_amount = isset($cart['total']['discounted_amount'])?$cart['total']['discounted_amount']:0;			
				if($discounted_amount>0.0001){
					$this->msg = $this->t("Sorry you cannot apply voucher, exising discount is alread applied in your cart");
					$this->output();
				}					
			}
			/*END CHECK IF HAS ALREADY DISCOUNT*/
			
			
			/*CHECK IF HAS ALREADY VOUCHER*/				
			$pts_enabled_add_voucher = getOptionA('pts_enabled_add_voucher');
			if(!$is_disabled_merchant_settings){
				$mt_pts_enabled_add_voucher= getOption($this->merchant_id,'mt_pts_enabled_add_voucher');
				if($mt_pts_enabled_add_voucher>0){
					$pts_enabled_add_voucher=$mt_pts_enabled_add_voucher;
				}			
			}
			
			if($pts_enabled_add_voucher!=1){
				$less_voucher = $cart['total']['less_voucher'];			
				if($less_voucher>0.0001){
				   $this->msg = $this->t("Sorry but you cannot redeem points if you have already voucher applied on your cart");
				   $this->output();
				}					
			}
			/*END CHECK IF HAS ALREADY VOUCHER*/	
			
			$redeeming_point = getOptionA('pts_redeeming_point');
			$redeeming_point_value = getOptionA('pts_redeeming_point_value');
			
			if(!$is_disabled_merchant_settings){
				$mt_pts_redeeming_point = getOption($this->merchant_id,'mt_pts_redeeming_point');
				$mt_pts_redeeming_point_value = getOption($this->merchant_id,'mt_pts_redeeming_point_value');
				
				if($mt_pts_redeeming_point>0){
					$redeeming_point=$mt_pts_redeeming_point;
				}
				if($mt_pts_redeeming_point_value>0){
					$redeeming_point_value=$mt_pts_redeeming_point_value;
				}
			}	
			
			/*CHECK ABOVE ORDER*/
			$subtotal = isset($cart['total']['subtotal'])?$cart['total']['subtotal']:0;
			
			$points_apply_order_amt = getOptionA('points_apply_order_amt');
			if(!$is_disabled_merchant_settings){
				$mt_points_apply_order_amt = getOption($this->merchant_id,'mt_points_apply_order_amt');
				if($mt_points_apply_order_amt>0){
					$points_apply_order_amt=$mt_points_apply_order_amt;
				}			
			}
			
			if($points_apply_order_amt>0.0001){
				if($points_apply_order_amt>$subtotal){
					$this->msg = Yii::t("mobile2","Sorry but you can only redeem points on orders over [amount]",array(
					  '[amount]'=>FunctionsV3::prettyPrice($points_apply_order_amt)
					));
					$this->output();
				}			
			}								
			/*END CHECK ABOVE ORDER*/
			
			/*CHECK MINIMUM POINTS CAN BE USED*/
			$points_minimum = getOptionA('points_minimum');
			if(!$is_disabled_merchant_settings){
				$mt_points_minimum = getOption($this->merchant_id,'mt_points_minimum');
				if($mt_points_minimum>0){
					$points_minimum=$mt_points_minimum;
				}			
			}						
			if($points_minimum>0.0001){
				if($points_minimum>$points){
					$this->msg = Yii::t("mobile2","Sorry but Minimum redeem points can be used is [points]",array(
					  '[points]'=>$points_minimum
					));
					$this->output();
				}			
			}								
			/*END CHECK MINIMUM POINTS CAN BE USED*/
			
			
			/*CHECK MAXIMUM POINTS CAN BE USED*/
			$points_max = getOptionA('points_max');
			if(!$is_disabled_merchant_settings){
				$mt_points_max = getOption($this->merchant_id,'mt_points_max');
				if($mt_points_max>0.0001){
					$points_max=$mt_points_max;
				}			
			}
			
			if($points_max>0.0001){
				if($points_max<$points){
				   	$this->msg = Yii::t("mobile2","Sorry but Maximum redeem points can be used is [points]",array(
						  '[points]'=>$points_max
						));
					$this->output();
				}		
			}
			/*END CHECK MAXIMUM POINTS CAN BE USED*/
			
			$temp_redeem=intval($this->data['points']/$redeeming_point);
			$points_amount=$temp_redeem*$redeeming_point_value;
			
			/*CHECK IF SUB TOTAL WILL BE IN NEGATIVE*/			
			$new_balance = $subtotal-$points_amount;
			if($new_balance<=0){
				$this->msg = $this->t("Sorry you cannot redeem points which the Sub Total will become negative when after applying the points");
				$this->output();
			}			
			
			$db = new DbExt();
			$params = array(
			  'points_apply'=>$this->data['points'],
			  'points_amount'=>$points_amount
			);
			$db->updateData("{{mobile2_cart}}",$params,'device_uiid',$this->device_uiid);
					
			$this->code = 1;
			$this->msg = mt("Succesful");
			$this->details = array(
			  'points_apply'=>$this->data['points'],
			  'points_amount'=>$points_amount,
			  'pretty_points_amount'=>FunctionsV3::prettyPrice($points_amount)
			);
			
		} else $this->msg = $this->t("Cart is empty");
			
		$this->output();
	}
	
	public function actionremovePoints()
	{
		$DbExt=new DbExt;
    	$params = array(
    	  'date_modified'=>FunctionsV3::dateNow(),
    	  'points_apply'=>0,
    	  'points_amount'=>0
    	);
    	$DbExt->updateData("{{mobile2_cart}}",$params,'device_uiid',$this->device_uiid);	
    	
    	$this->code = 1;
		$this->msg="OK";
		$this->details='';
    	
		$this->output();
	}
	
	public function actionapplyVoucher()
	{		
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){			
			$this->msg = $this->t("You need to login to apply voucher");
			$this->output();
		}			
		$client_id = $res['client_id'];		
		
		if (!is_numeric($this->merchant_id)){
			$this->msg = $this->t("Invalid merchant id");
			$this->output();
		}
		
		$data = array(
		  'delivery_type'=>isset($this->data['transaction_type'])?$this->data['transaction_type']:'',
		  'merchant_id'=>$this->merchant_id,
		  'card_fee'=>0
		);
		
		if ( $cart = mobileWrapper::getCartContent($this->device_uiid,$data)){			
			if ( $cart['total']['discounted_amount']>=0.0001){
				$this->msg = $this->t("Sorry you cannot apply voucher, exising discount is alread applied in your cart");
				$this->output();
			}
		}	
		
		/*CHECK IF HAS POINTS APPLIED*/		
		if (FunctionsV3::hasModuleAddon("pointsprogram")){
			$pts_enabled_add_voucher = getOptionA('pts_enabled_add_voucher');						
			$is_disabled_merchant_settings = PointsProgram::isMerchantSettingsDisabled();
			if(!$is_disabled_merchant_settings){
				$mt_pts_enabled_add_voucher = getOption($this->merchant_id,'mt_pts_enabled_add_voucher');
				if($mt_pts_enabled_add_voucher>0){
					$pts_enabled_add_voucher=$mt_pts_enabled_add_voucher;
				}		
			}						
			if($pts_enabled_add_voucher!=1){
				$pts_redeem_amt_orig = isset($cart['total']['pts_redeem_amt_orig'])?$cart['total']['pts_redeem_amt_orig']:0;
				if($pts_redeem_amt_orig>0.0001){
					$this->msg = $this->t("Sorry but you cannot apply voucher when you have already redeem a points");
					$this->output();
				}			
			}
		}
		/*END CHECK IF HAS POINTS APPLIED*/
		
		$voucher_name = isset($this->data['voucher_name'])?$this->data['voucher_name']:'';
		if(empty($voucher_name)){
			$this->msg = $this->t("Voucher is required");
			$this->output();
		}	
				
		//if ( $res=Yii::app()->functions->getVoucherCodeNew($voucher_name,$this->merchant_id) ){
		if ( $res=mobileWrapper::getVoucherMerchant($client_id,$voucher_name,$this->merchant_id) ){
			$voucher_type='merchant';
		} else {
			$voucher_type='admin';
			//$res=Yii::app()->functions->getVoucherCodeAdmin($voucher_name);
			$res=mobileWrapper::getVoucherAdmin($client_id,$voucher_name);
		}
				
		if($res){
			
			/*CHECK IF ALREADY USE*/
			if ( $res['found']<=0){					
			} else {
				$this->msg = $this->t("Sorry but you have already use this voucher code");
				$this->output();
			}
			
			if ( !empty($res['expiration'])){						
				$expiration=$res['expiration'];
				$now=date('Y-m-d');						
				$date_diff=date_diff(date_create($now),date_create($expiration));						
				if (is_object($date_diff)){
					if ( $date_diff->invert==1){
						if ( $date_diff->d>0){
							$this->msg= $this->t("Voucher code has expired");
							$this->output();
						}
					}
				}
			}
			
			/*check if voucher code can be used only once*/
			if ( $res['used_once']==2){
				if ( $res['number_used']>0){
					$this->msg= $this->t("Sorry this voucher code has already been used");
					$this->output();
				}
			}
			if($voucher_type=="admin"){
				if (!empty($res['joining_merchant'])){							
					$joining_merchant=json_decode($res['joining_merchant']);							
					if (in_array($this->merchant_id,(array)$joining_merchant)){								
					} else {
						$this->msg= $this->t("Sorry this voucher code cannot be used on this merchant");
						$this->output();
					}
				} 				
			}	
			
			
			/*CHECK SUBTOTAL WILL BECOME LESS THAN ZERO*/
			if($resp=mobileWrapper::getCart($this->device_uiid)){
				$cart=json_decode($resp['cart'],true);
				$data = array(
				  'delivery_type'=>isset($this->data['transaction_type'])?$this->data['transaction_type']:'delivery',
				  'merchant_id'=>$this->merchant_id,
				  'card_fee'=>0
				);
				Yii::app()->functions->displayOrderHTML( $data,$cart );
				if(Yii::app()->functions->code==1){
					$raw = Yii::app()->functions->details['raw']['total'];
					$subtotal = isset($raw['subtotal'])?$raw['subtotal']:0;						
											
					if ($res['voucher_type']=="percentage"){
					    $less_voucher = $subtotal*($res['amount']/100);
					    $subtotal_after_voucher = $subtotal  - $less_voucher;
					} else $subtotal_after_voucher = $subtotal- $res['amount'];
					
					if($subtotal_after_voucher<=0){
						$this->msg = $this->t("Sorry you cannot Voucher which the Sub Total will become negative when after applying the voucher");
						$this->output();
					}
				}					
			}	
			
			$params = array(
			  'voucher_id'=>$res['voucher_id'],
			  'voucher_owner'=>$res['voucher_owner'],
			  'voucher_name'=>$res['voucher_name'],
			  'amount'=>$res['amount'],
			  'voucher_type'=>$res['voucher_type'],
			);

			$DbExt=new DbExt;
			$DbExt->updateData("{{mobile2_cart}}",array(
			  'voucher_details'=>json_encode($params)
			),'device_uiid', $this->device_uiid);
			$this->code = 1;
			$this->msg="OK";
			$this->details='';		
	
		} else $this->msg = $this->t("Invalid voucher code");
		
		$this->output();
	}
	
	public function actionremoveVoucher()
	{
		mobileWrapper::removeVoucher($this->device_uiid);
		$this->code = 1;
		$this->msg="OK";
		$this->details='';
		$this->output();
	}
	
	public function actionapplyTips()
	{
		if (!is_numeric($this->merchant_id)){
			$this->msg = $this->t("Invalid merchant id");
			$this->output();
		}
		
		$tips = isset($this->data['tips'])?$this->data['tips']:0;
		if ($tips>0.0001){
			$data = array(
			  'delivery_type'=>isset($this->data['transaction_type'])?$this->data['transaction_type']:'',
			  'merchant_id'=>$this->merchant_id,
			  'card_fee'=>0
			);
			if ( $cart = mobileWrapper::getCartContent($this->device_uiid,$data)){			
				$params = array(
				  'tips'=>(float)$tips,
				  'date_modified'=>FunctionsV3::dateNow()
				);
								
				Yii::app()->db->createCommand()->update("{{mobile2_cart}}",$params,
		  	    'device_uiid=:device_uiid',
			  	    array(
			  	      ':device_uiid'=>$this->device_uiid
			  	    )
		  	    );
						
				$this->code = 1;
				$this->msg = "OK";
			} else $this->msg = $this->t("cart not available");
		} else $this->msg = $this->t("Invalid tip");
		$this->output();
	}
	
	public function actionremoveTip()
	{
		mobileWrapper::removeTip($this->device_uiid);
		$this->code = 1;
		$this->msg="OK";
		$this->details='';		
		$this->output();
	}
	
	public function actionPayOnDeliveryCardList()
	{
		if (!is_numeric($this->merchant_id)){
			$this->msg = $this->t("Invalid merchant id");
			$this->output();
		}
		if ($client_id = $this->checkToken()){
			if($res=Yii::app()->functions->getPaymentProviderMerchant($this->merchant_id)){				
				$data = array();
				foreach ($res as $val) {
					$val['payment_logo'] = mobileWrapper::getImage($val['payment_logo']);
					$data[] = $val;
				}
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
				  'list'=>$data
				);			
			} else {
				
				$this->code = 6;
				$this->msg = $this->t("No results");
									
				$this->details = array(
				   'element'=>".payondelivery_loader",
	        	   'element_list'=>"#payondelivery_list_item",
	        	   'message'=>$this->t("No Available Cards")
				);
				
			}
		}
		$this->output();
	}
	
	public function actionrazorPaymentSuccessfull()
	{
		$db = new DbExt();
		$device_uiid = isset($_GET['device_uiid'])?$_GET['device_uiid']:'';
		
		if ($client_id = $this->checkToken()){
			$order_id = isset($this->data['order_id'])?$this->data['order_id']:'';	
			if($order_id>0){
			   if($order_details = Yii::app()->functions->getOrderInfo($order_id)){
			   	  
			   	 $payment_gateway_ref = isset($this->data['payment_id'])?$this->data['payment_id']:'';
			   				   	 
			   	 $params=array(
					  'payment_type'=>'rzr',
					  'payment_reference'=>$payment_gateway_ref,
					  'order_id'=>$order_id,
					  'raw_response'=>$payment_gateway_ref,
					  'date_created'=>FunctionsV3::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR']
				 );		
				 if ( $db->insertData("{{payment_order}}",$params) ){
				 	
				 	  $this->code = 1;
					  $this->msg = Yii::t("mobile2","Your order has been placed. Reference # [order_id]",array(
					      '[order_id]'=>$order_id
					  ));
					  
					  $total = $order_details['total_w_tax'];
					 
				      $this->details=array(
				       'order_id'=>$order_id,
				       'total_amount'=>$total,		
				       'next_step'=>'receipt'		      
				      );
				      
				      /*SEND EMAIL RECEIPT*/
                      mobileWrapper::sendNotification($order_id);	
                      
                      FunctionsV3::updateOrderPayment($order_id,'rzr',
                      $payment_gateway_ref,$payment_gateway_ref,$order_id);
                      
                      mobileWrapper::executeAddons($order_id);
                      
                      /*CLEAR CART*/
                      mobileWrapper::clearCart($device_uiid); 
                      				 	
				 } else $this->msg  = $this->t("Something went wrong cannot insert records. please try again later");
			   	
			   } else $this->msg = $this->t("invalid order id not found");
			} else $this->msg = $this->t("invalid order id");
		}
		$this->output();
	}
	
	public function actionPayAuthorize()
	{
		$this->getPOSTData();
		$this->data = $_POST;
						
		if ($client_id = $this->checkToken()){
			$order_id = isset($this->data['order_id'])?$this->data['order_id']:'';
	        $_GET['id'] = $order_id;
	        require_once('buy.php');
	        if(empty($error)){
	        	
	        	$mode_autho=Yii::app()->functions->getOption('merchant_mode_autho',$merchant_id);
	            $autho_api_id=Yii::app()->functions->getOption('merchant_autho_api_id',$merchant_id);
	            $autho_key=Yii::app()->functions->getOption('merchant_autho_key',$merchant_id);
	            
	            if (FunctionsV3::isMerchantPaymentToUseAdmin($merchant_id)){
					$mode_autho=Yii::app()->functions->getOptionAdmin('admin_mode_autho');
			        $autho_api_id=Yii::app()->functions->getOptionAdmin('admin_autho_api_id');
			        $autho_key=Yii::app()->functions->getOptionAdmin('admin_autho_key');        
				}
				
				if(empty($mode_autho) || empty($autho_api_id) || empty($autho_key)){
	            	$this->msg=$this->t("Payment settings not properly configured");
				    $this->output();		 	    
	            }
	            
	            AuthorizePayWrapper::$mode = $mode_autho;     
	            AuthorizePayWrapper::$api = $autho_api_id;
	            AuthorizePayWrapper::$key = $autho_key; 
	            
	            $params = array(
	              'total_w_tax'=>$amount_to_pay,
	              'cc_number'=>trim( str_replace(" ","",$this->data['credit_card_number']) ),
	              'expiration_month'=>$this->data['expiration_month'],
	              'expiration_yr'=>$this->data['expiration_yr'],
	              'cvv'=>$this->data['cvv'],
	              'paymet_desc'=>$payment_description,
	              'x_first_name'=>$this->data['first_name'],
	              'x_last_name'=>$this->data['last_name'],
	              'x_address'=>$this->data['address'],
	              'x_city'=>$this->data['city'],
	              'x_state'=>$this->data['state'],
	              'x_zip'=>$this->data['zip_code'],
	              'x_country'=>$this->data['country_code'],
	            );
	            	            
	            if($resp = AuthorizePayWrapper::Paynow($params, $client_id)){
	               $payment_reference = $resp['payment_reference'];
	               
	               FunctionsV3::updateOrderPayment($order_id,"atz",
		    		  	  $payment_reference,$resp,$reference_id);		
		    		  	  
		    	   mobileWrapper::executeAddons($order_id);  	  
		    	   
		    	   /*SEND EMAIL RECEIPT*/
                    mobileWrapper::sendNotification($order_id);	
                    
                    /*CLEAR CART*/
	                mobileWrapper::clearCart($this->device_uiid); 
	                
	                 $this->code = 1;
				    $this->msg = Yii::t("mobile2","Your order has been placed. Reference # [order_id]",array(
				      '[order_id]'=>$order_id
				    ));
				    
				    $this->details=array(
				      'order_id'=>$order_id,
				      'total_amount'=>$amount_to_pay,	
				      'next_step'=>'receipt'
				    );			
	            	
	            } else $this->msg = AuthorizePayWrapper::$error;	        
	        } else $this->msg = $error;   		
		}
		$this->output();
	}
	
	public function actionsavePushSettings()
	{
		if(!empty($this->device_uiid)){
			$enabled_push = isset($this->data['enabled_push'])?$this->data['enabled_push']:'';			
			$params = array(
			  'push_enabled'=>(integer)$enabled_push,
			  'date_modified'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR']
			);			
			Yii::app()->db->createCommand()->update("{{mobile2_device_reg}}",$params,
	  	    'device_uiid=:device_uiid',
		  	    array(
		  	      ':device_uiid'=>$this->device_uiid
		  	    )
	  	    );
			$this->code=1;
			$this->msg = $this->t("Setting saved");
			$this->details = array();
		} else $this->msg = $this->t("invalid device uiid");
		$this->output();
	}
	
	public function actiongetPushSettings()
	{
		if(!empty($this->device_uiid)){
			if ($res = mobileWrapper::getDeviceByUIID( $this->device_uiid )){				
				$this->code = 1;
				$this->msg = "ok";
				$this->details = array(
				  'push_enabled'=>$res['push_enabled'],
				  'subscribe_topic'=>isset($res['subscribe_topic'])?(integer)$res['subscribe_topic']:0
				);
			} else $this->msg = $this->t("device uiid not found");
		} else $this->msg = $this->t("invalid device uiid");
		$this->output();
	}
	
	public function actionreRegisterDevice()
	{
		if ($client_id = $this->checkToken()){
			$this->data['client_id'] = $client_id;
		}		
		$this->data['device_id'] = isset($this->data['new_device_id'])?$this->data['new_device_id']:'';
		mobileWrapper::registeredDevice($this->data);	
		$this->code = 1;	
		$this->msg = "ok";
		$this->output();
	}
	
	public function actionlogout()
	{
		if ($client_id = $this->checkToken()){
			$this->data['client_id'] = $client_id;
		}		
		$device_uiid = isset($this->data['device_uiid'])?$this->data['device_uiid']:'';		
		mobileWrapper::clearCart($device_uiid);
		mobileWrapper::registeredDevice($this->data,'inactive');
		$this->code=1;
		$this->msg = "ok";
		$this->output();
	}
	
	public function actionregisterUsingFb()
	{
		$this->data['social_strategy']='fb_mobile';
		$this->socialLogin();
		$this->output();
	}
	
	public function actiongoogleLogin()
	{
		$this->data['social_strategy']='google_mobile';
		$this->socialLogin();
		$this->output();
	}
	
	private function socialLogin()
	{
		$DbExt=new DbExt; 
		
		$email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
		
		$Validator=new Validator;
		if ( FunctionsK::emailBlockedCheck($email_address)){
    		$Validator->msg[] = $this->t("Sorry but your email address is blocked by website admin");    		
    	}	 
    	
    	if(empty($email_address)){
    	  $Validator->msg[] = $this->t("invalid email address");    		
    	}	
    	
    	$Validator->email(array(
		  'email_address'=>$this->t("Invalid email address")
		),$this->data);
    	
    	if($Validator->validate()){    		
    	   if($res = Yii::app()->functions->isClientExist($email_address)){
    	   	  // UPDATE
    	   	  $client_id = $res['client_id'];
    	   	  $token = $res['token'];
    	   	  
    	   	  if(empty($token)){
    	   	  	 $token = mobileWrapper::generateUniqueToken(15,$this->data['device_uiid']);    	   	  	 
    	   	  }    	       	   	  
    	   	  $params=array(
    	   	    'first_name'=>$res['first_name'],
    	   	    'last_name'=>$res['last_name'],
    	   	    'email_address'=>$res['email_address'],
    	   	  );    	   	
    	   	  if($res['status']=="pending"){
    	   	  	 $verification=getOptionA('website_enabled_mobile_verification'); 
    	   	     $email_verification=getOptionA('theme_enabled_email_verification');    	   	     
    	   	     $email_code = $res['email_verification_code'];
    	   	     if($verification=="yes" || $email_verification==2){
    	   	     	$params['email_verification_code']=$email_code;		    		
		    		FunctionsV3::sendEmailVerificationCode($params['email_address'],$email_code,$params);
		    		$this->data['next_step'] = 'verification_email';
    	   	     }    	   	
    	   	     if($verification=="yes" || $email_verification==2){
    	   	     	$this->msg = mt("We have sent verification code to your email address");    	   	     	
    	   	     }   	   	      	   	         	   	     
    	   	  }   	       	   
    	   	  
    	   	  $this->data['client_id'] = $client_id;
    	   	  mobileWrapper::registeredDevice($this->data);
    	   	  
    	   	  $DbExt->updateData("{{client}}",array(
    	   	  	  'token'=>$token,
    	   	  	  'social_strategy'=>$this->data['social_strategy'],
    	   	  	  'social_id'=>$this->data['social_id'],
				  'last_login'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				),'client_id',$client_id);
    	   	  
    	   	  $this->code=1;
    	   	  $this->msg = $this->t("Registration successful");
    	   	  
    	   	  $this->details = array(    			  
    			  'next_step'=>isset($this->data['next_step'])?$this->data['next_step']:'',
    			  'customer_token'=>$token,    			  
    			  'contact_phone'=>$res['contact_phone'],
    			  'email_address'=>$params['email_address'],
    			);
    	   	       	   	
    	   } else {    	   	
    	   	  /*INSET*/
    	   	  $p = new CHtmlPurifier();
    	   	  $params = array(
    	   	    'first_name'=>$p->purify($this->data['first_name']),
    	   	    'last_name'=>$p->purify($this->data['last_name']),
    	   	    'email_address'=>$p->purify($email_address),
    	   	    'password'=>md5($this->data['social_id']),
    	   	    'last_login'=>FunctionsV3::dateNow(),
    	   	    'ip_address'=>$_SERVER['REMOTE_ADDR'],
    	   	    'social_strategy'=>$this->data['social_strategy'],
    	   	    'social_id'=>$this->data['social_id']
    	   	  );
    	   	  
    	   	  $verification=getOptionA('website_enabled_mobile_verification'); 
    	   	  $email_verification=getOptionA('theme_enabled_email_verification');
    	   	  
    	   	  $email_code=Yii::app()->functions->generateRandomKey(5);
    	   	  if($verification=="yes" || $email_verification==2){
    	   	  	 $params['email_verification_code']=$email_code;
	    		 $params['status']='pending';
	    		 FunctionsV3::sendEmailVerificationCode($params['email_address'],$email_code,$params);
	    		 $this->data['next_step'] = 'verification_email';
    	   	  }    	   
    	   	  
    	   	  $token = mobileWrapper::generateUniqueToken(15,$this->data['device_uiid']);
	    	  $params['token']=$token;
	    	     	 
	    	  if ( $DbExt->insertData("{{client}}",$params)){
	    	  	 $customer_id =Yii::app()->db->getLastInsertID();
	    	  	 $this->code=1;
	    		 $this->msg = $this->t("Registration successful");
	    		
	    		 if($verification=="yes" || $email_verification==2){	    		 	
	    		 	$this->msg = mt("We have sent verification code to your email address");    				
    				$this->data['client_id'] = $customer_id;
				    mobileWrapper::registeredDevice($this->data,'pending');				    
	    		 } else {
	    		 	/*sent welcome email*/	
    				FunctionsV3::sendCustomerWelcomeEmail($params);
    				    				
    				$this->data['client_id'] = $customer_id;
				    mobileWrapper::registeredDevice($this->data);
	    		 }	    	  
	    		 
	    		 $this->details = array(    			  
    			  'next_step'=>isset($this->data['next_step'])?$this->data['next_step']:'',
    			  'customer_token'=>$token,    			  
    			  'contact_phone'=>'',
    			  'email_address'=>$params['email_address'],
    			);
	    		
    			/*POINTS PROGRAM*/	    			
	    	    if (FunctionsV3::hasModuleAddon("pointsprogram")){
	    		    PointsProgram::signupReward($customer_id);
	    	    }	    	
	    	      	    	    	    
	    	    FunctionsV3::fastRequest(FunctionsV3::getHostURL().Yii::app()->createUrl(APP_FOLDER."/cron/getfbavatar"));
	    		 
	    	  } else $this->msg = $this->t("Something went wrong during processing your request. Please try again later");	    	  	    	  
    	   }    	
    	} else $this->msg = mobileWrapper::parseValidatorError($Validator->getError());
		
		$this->output();
	}
	
	public function actionretrievePassword()
	{
		$user_mobile = isset($this->data['user_mobile'])?$this->data['user_mobile']:'';
		
		$res=array();
	    if ($res = mobileWrapper::getAccountByEmail($user_mobile)){    	    	
	    } else {
	    	$res = mobileWrapper::getAccountByPhone($user_mobile);
	    }
	    	    
	    if(is_array($res) && count($res)>=1){
	    	
	    	$token = mobileWrapper::generateUniqueToken(15,$this->data['device_uiid']);
	    	
	    	$res['lost_password_token'] = $token;
	    	
	    	$client_id = $res['client_id'];
	    	$db = new DbExt();
	    	$db->updateData("{{client}}",array(
								  'lost_password_token'=>$token,
								  'date_modified'=>FunctionsV3::dateNow(),
								  'ip_address'=>$_SERVER['REMOTE_ADDR']
								),'client_id',$client_id);
	    	
	    	$email_address = $res['email_address'];
	    	mobileWrapper::SendForgotPassword($email_address,$res);
	    	
	    	unset($db);
	    	
	     	$this->code = 1;
	     	$this->msg = $this->t("We sent your forgot password link, Please follow that link. Thank You.");
	     	$this->details = array();
	    } else $this->msg = $this->t("sorry the email or phone you have entered does not exist in our records");
		
		$this->output();
	}
	
	public function actionmapboxgeocode()
	{
		$this->actiongeocode();
	}
	
	private function actiongeocode()
	{
		$lat = isset($this->data['lat'])?$this->data['lat']:'';
		$lng = isset($this->data['lng'])?$this->data['lng']:'';
		
		if(!empty($lat) && !empty($lng) ){						
			if ($res=FunctionsV3::latToAdress($lat,$lng)){
				$this->code = 1;
				$this->msg = "OK";
				$this->details = $res;
			} else $this->msg=$this->t("location not available");
		} else $this->msg = mt("invalid latitude or longitude");
		
		$this->output();
	}
	
	public function actionclearRecentLocation()
	{		
		if( mobileWrapper::clearRecentLocation($this->device_uiid)){
			$this->code = 1; $this->msg = 'OK';
			$this->details = array();
		} else $this->msg = mt("failed deleting recent location");
		$this->output();
	}
	
	public function actionGetPage()
	{
		$lang=Yii::app()->language;
		$page_id = isset($this->data['page_id'])?$this->data['page_id']:'';
		if ($res = mobileWrapper::getPageByID($page_id)){				
			
			$data['title']=$res['title'];
			$data['content']=$res['content'];			
			
			if(isset($res["title_$lang"])){
			   if(!empty($res["title_$lang"])){			   	  
			   	   $data['title']=$res["title_$lang"];
			   }			
			}
	
			if(isset($res["content_$lang"])){
			   if(!empty($res["content_$lang"])){			   	  
			   	   $data['content']=$res["content_$lang"];
			   }			
			}
			
			if($res['use_html']==0){
			   $data['content']=nl2br(strip_tags($data['content']));
			} else {
			   $data['content']=trim($data['content']);
			}		
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$data
			);
			
		} else $this->msg = mt("page not found");
		$this->output();
	}
	
	public function actionclearRecentSearches()
	{
		if( mobileWrapper::clearRecentSearches($this->device_uiid)){
			$this->code = 1; $this->msg = 'OK';
			$this->details = array();
		} else $this->msg = mt("failed deleting recent searches");
		$this->output();
	}
	
	public function actionTaskInformation()
	{
		$this->code = 6;		
		$this->details = array( 
		 'element'=>'.map_wrapper',
		 'message'=>mt("delivery information not found"),
		);
		
		$order_id = isset($this->data['order_id'])?$this->data['order_id']:'';
		if($order_id>0){
		    if($res = mobileWrapper::getDriverTask($order_id)){
		    	
		    	$driver_icon = 'car.png';
		    	if(!empty($res['transport_type_id'])){
		    		$driver_icon = strtolower($res['transport_type_id']).".png";
		    	} 
		    			    			    	
		    	$res['map_icons'] = array( 
		    	  'delivery'=>mobileWrapper::getImage('marker_orange.png','marker_orange.png'),
		    	  'dropoff'=>mobileWrapper::getImage('marker_green.png','marker_green.png'),
		    	  'driver'=>mobileWrapper::getImage($driver_icon,$driver_icon)
		    	);
		    			    			    	
		    	$completed = mobileWrapper::taskProgress($res['status']);		    
		    	$res['completed']=$completed;
		    			    	
		    	$res['driver_photo'] = mobileWrapper::getImage($res['driver_photo'],'avatar.png',false,'driver');
		    	
		    	$resp_status = '';
		    	switch (strtolower(trim($res['status']))) {
		    		case "failed":
		    		case "cancelled":
		    		case "declined":	
		    			$resp_status = mt("Sorry but this Delivery is already set to [status]",array(
		    			   '[status]'=>mt($res['status'])
		    			));
		    			break;
		    	
		    		case "successful":	
		    		  $resp_status = mt("This Delivery is already [status] <br/> you have rate this delivery [rating] stars",array(
		    			   '[status]'=>mt($res['status']),
		    			   '[rating]'=>$res['rating']
		    			));
		    		break;
		    				    		
		    		default:
		    			$resp_status = mt("Sorry but we cannot find what you are looking for");
		    			break;
		    	}
		    	
		    	$res['resp_status']=$resp_status;
		    	
		    	$this->code = 1;
		    	$this->msg = "OK";
		    	$this->details = array(
		    	 'data'=>$res
		    	);
		    } else $this->msg = mt("Not found");
		} else $this->msg = mt("invalid order id");
		$this->output();
	}
	
	public function actionDriverInformation()
	{
		$driver_id = isset($this->data['driver_id'])?$this->data['driver_id']:'';
		if ($res = mobileWrapper::DriverInformation($driver_id)){
			$res['profile_photo'] = mobileWrapper::getImage($res['profile_photo'],'avatar.png',false,'driver');
			
			$res['rating'] = mobileWrapper::getDriverRatings($driver_id);
			
			$datas=array();
			
			$datas[]=array(
			  'label'=>mt("TEAM"),
			  'value'=>$res['team_name']
			);
			$datas[]=array(
			  'label'=>mt("TRANSPORTATION TYPE"),
			  'value'=>Driver::t($res['transport_type_id'])
			);
			$datas[]=array(
			  'label'=>mt("TRANSPORTATION DESCRIPTION"),
			  'value'=>$res['transport_description']
			);
			$datas[]=array(
			  'label'=>mt("LICENSE PLATE"),
			  'value'=>$res['licence_plate']
			);
			$datas[]=array(
			  'label'=>mt("LAST LOGIN"),
			  'value'=>FunctionsV3::prettyDate($res['last_login'])." ".FunctionsV3::prettyTime($res['last_login'])
			);
			
			$res['sub_data']=$datas;
			
			$this->code = 1;
	    	$this->msg = "OK";
	    	$this->details = array(
	    	 'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
	    	 'data'=>$res
	    	);
		} else {
			$this->code = 6;
			$this->msg = mt("Not found");			
	    	$this->details = array(
	    	  'element'=>".driver_details_loader",
        	  'element_list'=>"#driver_list_details",
        	  'message'=>$this->t("driver information not found"),
	    	  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',	    	 
	    	);
		}
		$this->output();
	}
	
	public function actionTrackDriver()
	{
		$driver_id = isset($this->data['driver_id'])?$this->data['driver_id']:'';
		$order_id = isset($this->data['track_order_id'])?$this->data['track_order_id']:'';
		
		if($res = mobileWrapper::getDriverLocation($driver_id)){
			if(!empty($res['location_lat']) && !empty($res['location_lng']) ) {
				
				$res['task_status']='';
				$res['task_id']='';
				$res['completed']=0;
				
				$res['driver_photo']='';
				$res['driver_name']='';
				
				if($resp = mobileWrapper::getDriverTask($order_id)){					
					$res['task_status']=$resp['status'];
					$res['task_id']=$resp['task_id'];
					$res['completed']=mobileWrapper::taskProgress($resp['status']);
					
					$res['driver_name'] = $resp['driver_name'];					
					$res['driver_photo'] = mobileWrapper::getImage($resp['driver_photo'],'avatar.png',false,'driver');
					
					
					$resp_status = '';
			    	switch (strtolower(trim($resp['status']))) {
			    		case "failed":
			    		case "cancelled":
			    		case "declined":	
			    			$resp_status = mt("Sorry but this Delivery status is [status]",array(
			    			   '[status]'=>mt($resp['status'])
			    			));
			    			break;
			    	
			    		case "successful":	
			    		  $resp_status = mt("This Delivery is already [status] <br/> you have rate this delivery [rating] stars",array(
			    			   '[status]'=>mt($resp['status']),
			    			   '[rating]'=>$resp['rating']
			    			));
			    		break;
			    				    		
			    		default:
			    			$resp_status = mt("Sorry but we cannot find what you are looking for");
			    			break;
			    	}
			    	
			    	$res['resp_status']=$resp_status;
				}
				
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'data'=>$res
				);
			} else $this->msg = mt("location is empty");
		} else $this->msg = mt("Not found");
		$this->output();
	}
	
	public function actionGetTask()
	{
		$task_id = isset($this->data['task_id'])?$this->data['task_id']:'';		
		if ($client_id = $this->checkToken()){
			$this->data['client_id'] = $client_id;
			if($res = mobileWrapper::getTaskFullInformation($task_id)){
				
				$res['profile_photo'] = mobileWrapper::getImage($res['driver_photo'],'avatar.png',false,'driver');
				$res['review_as'] = mobileWrapper::t("Review as [customer_name]",array(
				  '[customer_name]'=>$res['customer_firstname']
				));
				
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'data'=>$res
				);
			} else $this->msg = mt("Task not found");
		}				
		$this->output();
	}
	
	public function actionaddTaskReview()
	{
				  
	   /*$this->code = 1;
	   $this->msg = mt("Your review has submitted. Thank you!");
	   $this->output();*/
		   
		$this->getPOSTData();
		$this->data = $_POST;
		
		$task_id = isset($this->data['task_id'])?$this->data['task_id']:'';
		$rating = isset($this->data['rating'])?$this->data['rating']:'';
		
		if(!is_numeric($this->data['rating'])){
			$this->msg = $this->t("Please select rating");
			$this->output();
		}		
		if($this->data['rating']<=0){
			$this->msg = $this->t("Please select rating");
			$this->output();
		}		
		
		$params = array(
		  'rating'=>$rating,
		  'rating_comment'=>isset($this->data['review'])?$this->data['review']:'',
		  'rating_anonymous'=>isset($this->data['as_anonymous'])?$this->data['as_anonymous']:0,
		  'date_modified'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
		
		if(!is_numeric($params['rating'])){
			$params['rating']=0;
		}	
		if(!is_numeric($params['rating_anonymous'])){
			$params['rating_anonymous']=0;
		}	
		
		if($task_id>0){
		   $db = new DbExt();
		   $db->updateData("{{driver_task}}",$params,'task_id', $task_id);
		   $this->code = 1;
		   $this->msg = mt("your review has submitted");
		} else $this->msg = mt("invalid task id");
				
		$this->output();
	}
	
	public function actiongetOrderDetailsCancel()
	{
		$this->actiongetOrderDetails();
	}
	
	public function actionGetNotifications()
	{
		if(!$res = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		$client_id = $res['client_id'];		
		
		$pagelimit = mobileWrapper::paginateLimit();		
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $pagelimit;
        } else  $page = 0; 

        $paginate_total=0; 
        $limit="LIMIT $page,$pagelimit"; 
        
        //WHERE a.client_id=".FunctionsV3::q($client_id)."
        
        $db = new DbExt();
		$stmt="
		SELECT SQL_CALC_FOUND_ROWS 
		a.id,
		a.push_title,
		a.push_message,
		a.date_created		
		FROM
		{{mobile2_push_logs}} a
				       
		WHERE a.device_uiid=".FunctionsV3::q($this->device_uiid)."
		AND is_read != '1'
				
		ORDER BY a.id DESC
		$limit
		";		

			
		if($res = $db->rst($stmt)){
			
			$total_records=0;
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ($resp=$db->rst($stmtc)){			 			
				$total_records=$resp[0]['total_records'];
			}					
			$paginate_total = ceil( $total_records / $pagelimit );
			
			$data = array();
			foreach ($res as $val) {				
				$val['date_created'] = FunctionsV3::prettyDate($val['date_created'])." ".FunctionsV3::prettyTime($val['date_created']);
				$data[]=$val;
			}
			
			$this->code = 1;
			$this->msg="OK";
			$this->details = array( 
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$paginate_total,
			  'data'=>$data
			);
			
		} else {						
			$this->code = 6;
			$this->msg = $this->t("Your notifications list is empty");
								
			$this->details = array(
			   'element'=>".notifications_loader",
        	   'element_list'=>"#notifications_list_item",
        	   'message'=>$this->t("You don't have any notifications yet")
			);
		}
        
		$this->output();
	}
	
	public function actionReadNotification()
	{
		if ($client_id = $this->checkToken()){
			$id = isset($this->data['id'])?$this->data['id']:'';
			if($id>0){
				$params = array(
				  'is_read'=>1,
				  'date_modified'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
				$db = new  DbExt();
				$db->updateData("{{mobile2_push_logs}}",$params,'id',$id);
				$this->msg = 'ok';
				$this->code = 1;
				$this->details = array();
			} else $this->msg = mt("invalid id");
		}
		$this->output();
	}
	
	public function actionmarkAllNotifications()
	{
		if ($client_id = $this->checkToken()){			
			$stmt="
			UPDATE {{mobile2_push_logs}}
			SET is_read='1'
			WHERE
			client_id = ".FunctionsV3::q($client_id)."
			AND is_read != '1'
			";
			$db = new  DbExt();		
			$db->qry($stmt)	;
			$this->msg = 'ok';
			$this->code = 1;
			$this->details = array();
		}
		$this->output();
	}
	
	public function actionsearchBooking()
	{
		if ($client_id = $this->checkToken()){
			$search_str = isset($this->data['search_str'])?$this->data['search_str']:'';
			if(!empty($search_str)){
				$db=new DbExt();
				$stmt="
				SELECT 				
				a.booking_id,
				a.client_id,
				a.merchant_id,
				a.date_booking,
				a.booking_time,
				a.number_guest,
				b.restaurant_name,
			    b.logo
				FROM {{bookingtable}} a
				left join {{merchant}} b
                ON
                a.merchant_id = b.merchant_id
                WHERE a.client_id=".FunctionsV3::q($client_id)."
                AND (
                   a.booking_id LIKE ".FunctionsV3::q("%$search_str")."
                   OR b.restaurant_name LIKE ".FunctionsV3::q("%$search_str%")."
                )
                LIMIT 0,20
				";
				
			    if ($res = $db->rst($stmt)){
			    	foreach ($res as $val) {
			    		$val['date_booking_format'] = FunctionsV3::prettyDate( $val['date_booking'] )." ".FunctionsV3::prettyTime($val['booking_time']);
			    		$val['restaurant_name']=clearString($val['restaurant_name']);
			    		$val['logo']=mobileWrapper::getImage($val['logo']);
			    		$val['booking_ref'] = mobileWrapper::t("Booking ID#[booking_id]",array(
						  '[booking_id]'=> $val['booking_id']
						));
						$val['number_guest'] = mobileWrapper::t("No. of guest [count]",array(
				           '[count]'=> $val['number_guest']
				        ));
				        
				        $val['restaurant_name']= mobileWrapper::highlight_word($val['restaurant_name'],$search_str);
				        $val['booking_ref']= mobileWrapper::highlight_word($val['booking_ref'],$search_str);
				        
			    		$data[] = $val;
			    	}
			    	$this->code = 1;
					$this->msg = "OK";
					$this->details = array(
					 'list'=>$data
					);			    	
			    } else $this->msg = $this->t("No results");
			} else $this->msg = $this->t("invalid search string");
		}			
		$this->output();
	}
	
	public function actionGetBookingDetails()
	{
		if ($client_id = $this->checkToken()){			
			$booking_id = isset($this->data['booking_id'])?$this->data['booking_id']:'';
			if($res = mobileWrapper::GetBookingDetails($booking_id,$client_id)){
				$this->code = 1;
				$this->msg = "ok";
				$data = array();
				
				$data[]=array(
				  'label'=>mt("Booking ID"),
				  'value'=>$res['booking_id'],
				);
				$data[]=array(
				  'label'=>mt("Number Of Guests"),
				  'value'=>$res['number_guest'],
				);
				$data[]=array(
				  'label'=>mt("Date Of Booking"),
				  'value'=>FunctionsV3::prettyDate($res['date_booking']),
				);
				$data[]=array(
				  'label'=>mt("Time"),
				  'value'=>FunctionsV3::prettyTime($res['booking_time']),
				);
				$data[]=array(
				  'label'=>mt("Name"),
				  'value'=>$res['booking_name']
				);
				$data[]=array(
				  'label'=>mt("Email"),
				  'value'=>$res['email']
				);
				$data[]=array(
				  'label'=>mt("Mobile"),
				  'value'=>$res['mobile']
				);
				$data[]=array(
				  'label'=>mt("Your Instructions"),
				  'value'=>$res['booking_notes']
				);
				
				$this->details = array(
				  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
				  'data'=>$data
				);
			} else {
				$this->code = 6;
				$this->msg = $this->t("Booking not found");
									
				$this->details = array(
				   'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
				   'element'=>".booking_details_loader",
	        	   'element_list'=>"#booking_details_list",
	        	   'message'=>$this->t("Sorry but we cannot find what you are looking for")
				);
			}
		}
		$this->output();
	}
	
	public function actiongetlanguageList2()
	{
		$data = array();
		if ($lang_list=FunctionsV3::getLanguageList(false) ){	
			$enabled_lang=FunctionsV3::getEnabledLanguage();
			foreach ($lang_list as $val) {
				if (in_array($val,(array)$enabled_lang)){
					$data[$val]=mt($val);
				}			
			}
			$this->code=1;
			$this->msg = "OK";
			$this->details = array(
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'lang'=>Yii::app()->language,
			  'data'=>$data
			);
		} else {			
			$this->code = 6;
			$this->msg = $this->t("No available language");
								
			$this->details = array(
			   'element'=>".language2_list_loader",
        	   'element_list'=>"#language2_list_item",
        	   'message'=>$this->t("language not available")
			);
		}
		$this->output();
	}
	
	public function actioncheckRunTrackHistory()
	{
		$run_track = true;
		$order_id = isset($this->data['order_id'])?$this->data['order_id']:'';
		if($order_id>0){
			if($res = mobileWrapper::getDriverTask($order_id)){
				
				switch ($res['status']) {
					case "successful":
					case "failed":
					case "cancelled":
					case "declined":	
					    $run_track = false;
						break;
					default:
						break;
				}
				
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'run_track'=>$run_track
				);
			} else $this->msg = mt("invalid order id not found");
		} else $this->msg = mt("invalid order id");
		$this->output();
	}
	
	public function actiongetOrderHistory2()
	{
		$this->actiongetOrderHistory();
	}

	public function actionPaySelcompay()
	{
		
		$this->getPOSTData();
		$this->data = $_POST;		
		
		if(!$customer = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$this->code = 3;
			$this->msg = $this->t("token not found");
			$this->output();
		}			
		
					
		$_GET['id'] = isset($this->data['order_id'])?$this->data['order_id']:'';		
		require_once('buy.php');
		if(empty($error)){
			if($credentials = selcompayWrapper::getCredentials($merchant_id)){				
				$email_address = $customer['email_address'];					
				$client_id = $customer['client_id'];
							
				if(isset($this->data['contact_phone'])){
					$contact_phone = trim($this->data['contact_phone']);
				} else $contact_phone = trim($customer['contact_phone']);

			   	$new_reference_id = selcompayWrapper::generateOrderToken();
			   	
			   	$params = array(
				  "order_id" => $new_reference_id, 
				  "amount" => $amount_to_pay,
				  "currency" => $currency_code,
				  "payer_remarks" => isset($this->data['payer_remarks'])?$this->data['payer_remarks']:'',
				  "merchant_remarks" => $payment_description,
				  "payer_email" => $email_address,
				  "payer_phone" => str_replace("+","",$contact_phone)
			    );	
			    			    			    				    			    			    			    	
			    try {
			    	
			    	$resp = selcompayWrapper::postRequest( $credentials['environment']['create'] ,$credentials,$params);
			    				    				    				    				    	
			    	$db = new DbExt();
			    	$params_logs = array(
			    	  'order_id'=>$reference_id,
			    	  'reference_id'=>$new_reference_id,
			    	  'date_created_raw'=>date("Y-m-d"),
			    	  'date_created'=>FunctionsV3::dateNow(),
			    	  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			    	  'status'=>isset($resp['payment_status'])?$resp['payment_status']:'pending',
			    	  'payment_token'=>isset($resp['payment_token'])?$resp['payment_token']:'',
			    	  'json_response'=>json_encode($resp)
			    	);
			    	$db->insertData("{{selcompay_trans}}",$params_logs);
			    	
			    	$payment_code = isset($resp['payment_token'])?$resp['payment_token']:'';
			    				    				    	
			    	$this->code = 1;			    	
			    	$this->msg = "OK";
			    	$this->details = array(
			    	  'reference_id'=>$reference_id,
			    	  'new_reference_id'=>$new_reference_id,
			    	  'payment_code'=>mt("Your Payment code is [code]", array(
				    	  '[code]'=>$payment_code
				       ))
			    	);
			    	
			    	selcompayWrapper::sendPaymentCode($email_address, $contact_phone, $payment_code , array( 
			    	  'first_name'=>$customer['first_name'],
			    	  'total_amount'=>normalPrettyPrice($amount_to_pay)
			    	));
			    	
			    } catch (Exception $e) {
					$this->msg = Yii::t("mobile2","Caught exception: [error]",array(
					  '[error]'=>$e->getMessage()
					));
				}    
			    
			} else $this->msg = mt("invalid merchant credentials");		   	   
		} else $this->msg = $error;						
		$this->output();
	}
	
	public function actionSelcomQuery()
	{
		$new_reference_id = isset($this->data['new_reference_id'])?$this->data['new_reference_id']:'';
		$reference_id = isset($this->data['reference_id'])?$this->data['reference_id']:'';
			
		if ( $order_info = FunctionsV3::getOrderInfoByToken($reference_id)){
			
			$merchant_id = $order_info['merchant_id'];
			$order_id = $order_info['order_id'];
			$client_id = $order_info['client_id'];
			
			if($order_info['status']=="paid"){							
			   $payment_reference = $order_id;			   
			   if($payment_info = FunctionsV3::getPaymentOrderByOrderID($order_id)){
			   	  $payment_reference = $payment_info['payment_reference'];
			   }						   
			   $this->code = 1;
			   $this->msg =  Yii::t("mobile2","payment successfull with payment reference id [ref]",array(
	                           '[ref]'=>$payment_reference
	                         ));		      

	           $this->details = array(
			      'payment_status'=>'COMPLETED',
			      'order_id'=>$order_id,
	              'total_amount'=>isset($order_info['total_w_tax'])?$order_info['total_w_tax']:0
			    );   
	                                              				
			   $this->output();
			}
			
			if($credentials = selcompayWrapper::getCredentials($merchant_id)){
				try {
					$params=array();
										
					$resp = selcompayWrapper::postRequest( $credentials['environment']['query']."/$new_reference_id" ,$credentials,$params);				
					
									
					switch (trim($resp['payment_status'])) {
						case "COMPLETED":	
						    $this->code = 1;	

						    $payment_reference = $reference_id;
						    if(isset($resp['transid'])){
						    	if(!empty($resp['transid'])){
						    	    $payment_reference = $resp['transid'];						    	
						    	}
						    }			
						    									    					   
						    $this->msg =  Yii::t("mobile2","payment successfull with payment reference id [ref]",array(
		                           '[ref]'=>$payment_reference
		                         ));		                                    
		                    		                        
		                    $db = new DbExt();
					 		$params = array(
					 		  'status'=>$resp['payment_status'],
					 		  'json_response1'=>json_encode($resp),
					 		  'date_modified'=>FunctionsV3::dateNow(),
					 		  'ip_address'=>$_SERVER['REMOTE_ADDR']
					 		);
					 		$db->updateData("{{selcompay_trans}}",$params,'order_id',$reference_id);
		                    
		                         
		                    FunctionsV3::updateOrderPayment($order_id,selcompayWrapper::paymentCode(),
		        	    		$payment_reference,$resp,$reference_id);        
		        	    		
			        	    /*SEND EMAIL RECEIPT*/
		        	    	mobileWrapper::sendNotification($order_id);	
		        	    	
		        	    	mobileWrapper::executeAddons($order_id);	
		
		        	    	
		                    /*CLEAR CART*/
			        	    mobileWrapper::clearCartByCustomerID($client_id);
						    
							break;
					
						case "CANCELLED":										
						    $this->msg = mt("Payment was cancelled");
							break;
							
						case "PENDING":		
						   $this->msg = mt("Payment is pending");						
							break;
									
						default:
							break;
					}
					
					$this->details = array(
				      'payment_status'=>$resp['payment_status'],
				      'order_id'=>$order_id,
		              'total_amount'=>isset($order_info['total_w_tax'])?$order_info['total_w_tax']:0
				    );
					
				} catch (Exception $e) {
					$this->msg = Yii::t("mobile2","Caught exception: [error]",array(
					  '[error]'=>$e->getMessage()
					));
				}    
			} else $this->msg = mt("invalid merchant credentials");		   	   
		} else $this->msg = mt("invalid order id");
		$this->output();
	}
	
	public function actionCancelPaymentSelcompay()
	{
		$this->data = $_POST;
		$new_reference_id = isset($this->data['new_reference_id'])?$this->data['new_reference_id']:'';
		$reference_id = isset($this->data['reference_id'])?$this->data['reference_id']:'';
		if ( $order_info = FunctionsV3::getOrderInfoByToken($reference_id)){
			 $merchant_id = $order_info['merchant_id'];
			 if($credentials = selcompayWrapper::getCredentials($merchant_id)){
			 	
			 	try {
			 		
			 		$params=array();
					$resp = selcompayWrapper::postRequest( $credentials['environment']['cancel']."/$new_reference_id" ,$credentials,$params);
			 		$db = new DbExt();
			 		$params = array(
			 		  'status'=>$resp['payment_status'],
			 		  'date_modified'=>FunctionsV3::dateNow(),
			 		  'ip_address'=>$_SERVER['REMOTE_ADDR']
			 		);
			 		$db->updateData("{{selcompay_trans}}",$params,'order_id',$reference_id);
			 		
			 		$this->code = 1;
			 		$this->msg = mt("Payment was succesfully cancelled");
			 		
			 		
			 	} catch (Exception $e) {
					$this->msg = Yii::t("mobile2","Caught exception: [error]",array(
					  '[error]'=>$e->getMessage()
					));
				}    
			 	
			 } else $this->msg = mt("invalid merchant credentials");	
		} else $this->msg = mt("invalid order id");
		$this->output();
	}
	
	public function actionCityList()
	{				      
		$page_limit = mobileWrapper::paginateLimit();
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
        
        $search_string = isset($this->data['search_str'])?$this->data['search_str']:'';
        $state_id = isset($this->data['state_id'])?$this->data['state_id']:'';
        
        $search_resp = mobileWrapper::searchMode();		
		$location_mode = $search_resp['location_mode'];
		if($location_mode==2){
			if((integer)$state_id<=0){
				$this->msg = mt("No results");
				$this->output();
			}		
		}	
		
		if($res = LocationWrapper::GetLocationCity($page,$page_limit, $search_string, $state_id) ){
			$this->code =1;
			$this->msg = "OK";
			$this->details = array(
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$res['paginate_total'],
			  'data'=>$res['list'],
			);
		} else $this->msg = mt("No results");
		$this->output();
	}
	
	public function actionAreaList()
	{
		$page_limit = mobileWrapper::paginateLimit();
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
        
        $search_string = isset($this->data['search_str'])?$this->data['search_str']:'';
        $city_id = isset($this->data['city_id'])?$this->data['city_id']:'';
                
        if($res = LocationWrapper::GetAreaList($city_id,$page,$page_limit, $search_string) ){
			$this->code =1;
			$this->msg = "OK";
			$this->details = array(
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$res['paginate_total'],
			  'data'=>$res['list'],
			);
		} else $this->msg = mt("No results");
		$this->output();
	}
	
	public function actionStateList()
	{				      
		$page_limit = mobileWrapper::paginateLimit();
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
        
        $search_string = isset($this->data['search_str'])?$this->data['search_str']:'';
		
		if($res = LocationWrapper::GetStateList($page,$page_limit, $search_string) ){			
			$this->code =1;
			$this->msg = "OK";
			$this->details = array(
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$res['paginate_total'],
			  'data'=>$res['list'],
			);
		} else $this->msg = mt("No results");
		$this->output();
	}
	
	public function actionsaveAddressBookLocation()
	{
		$this->data = $_POST;				
				
		if ($client_id = $this->checkToken()){
			$params = array(
			  'client_id'=>$client_id,
			  'street'=>isset($this->data['street'])?$this->data['street']:'',
			  'latitude'=>isset($this->data['lat'])?$this->data['lat']:'',
			  'longitude'=>isset($this->data['lng'])?$this->data['lng']:'',
			  'state_id'=>isset($this->data['state_id'])?$this->data['state_id']:'',
			  'city_id'=>isset($this->data['city_id'])?$this->data['city_id']:'',
			  'area_id'=>isset($this->data['area_id'])?$this->data['area_id']:'',
			  'as_default'=>isset($this->data['as_default'])?$this->data['as_default']:'',
			  'location_name'=>isset($this->data['location_name'])?$this->data['location_name']:'',
			  'date_created'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],			  
			);					
			
			if($country_id = LocationWrapper::getCountryID($params['state_id'])){
				$params['country_id']=$country_id;
			}		
						
			if(empty($params['latitude'])){
				$this->msg = $this->t("please select your location on the map");
				$this->output();
			}		
			if(empty($params['longitude'])){
				$this->msg = $this->t("please select your location on the map");
				$this->output();
			}		
			
			if(!is_numeric($params['as_default'])){			    
			    $params['as_default']=0;
		    }			
			
			$db = new DbExt();			
			$id = isset($this->data['id'])?$this->data['id']:'';
			if($id>0){
				 unset($params['date_created']);
				 $params['date_modified']=FunctionsV3::dateNow();
				 
				 if(LocationWrapper::isAddressBookExist($client_id,
				 $params['street'],$params['state_id'],
				 $params['city_id'],$params['area_id'],$id
				 )){
				   $this->msg = mt("Address already exist");
				   $this->output();
				 }
				 
				 if ($params['as_default']==1){
					mobileWrapper::UpdateAllAddressBookDefaultLocation($client_id);
				 }							 
				 $db->updateData("{{address_book_location}}", $params ,'id',$id);
				 $this->code = 1; $this->msg = $this->t("Successfully updated");
			} else {				
				
				if(LocationWrapper::isAddressBookExist($client_id,
				$params['street'],$params['state_id'],
				$params['city_id'],$params['area_id']
				)){
				   $this->msg = mt("Address already exist");
				   $this->output();
				}
								
				if ($params['as_default']==1){
					mobileWrapper::UpdateAllAddressBookDefaultLocation($client_id);
				}
								
				if ( $db->insertData("{{address_book_location}}",$params)){
					$this->code = 1; $this->msg = $this->t("Successfully added");
				} else $this->msg = $this->t("failed cannot insert records");
			}		
		}
		$this->output();
	}
	
	public function actiongetAddressBookLocationByID()
	{
		if ($client_id = $this->checkToken()){
			$id = isset($this->data['id'])?$this->data['id']:'';
			if($id>=1){
				if ($res=LocationWrapper::getAddressBookByID($id,$client_id)){
				unset($res['date_created']);
				unset($res['date_modified']);
				unset($res['ip_address']);
											
				
				$this->code = 1;
				$this->msg = "ok";
				$this->details = array(
				  'data'=>$res
				);
			} else $this->msg = $this->t("Record not found. please try again later");
			} else $this->msg = $this->t("Invalid id");
		}
		$this->output();
	}
	
	public function actionGetAddressFromCartLocation()
	{
		$customer_phone = '';
		if($client = mobileWrapper::getCustomerByToken($this->data['user_token'])){
			$customer_phone=$client['contact_phone'];
		}
		if($resp=mobileWrapper::getCart($this->device_uiid)){
			$this->code = 1;
			$this->msg = "OK";
			
			if(empty($resp['contact_phone'])){
				if(!empty($customer_phone)){
				   	$resp['contact_phone']=$customer_phone;
				}
			}	
						
			$this->details = array(
			  'street'=>$resp['street'],
			  'state_name'=>$resp['state'],
			  'city_name'=>$resp['city'],			  
			  'area_name'=>$resp['zipcode'],
			  'delivery_instruction'=>$resp['delivery_instruction'],
			  'location_name'=>$resp['location_name'],
			  'contact_phone'=>$resp['contact_phone'],			  
			  'lat'=>$resp['delivery_lat'],
			  'lng'=>$resp['delivery_long'],
			  'save_address'=>$resp['save_address'],
			  'customer_phone'=>$customer_phone,
			  'state_id'=>$resp['state_id'],
			  'city_id'=>$resp['city_id'],
			  'area_id'=>$resp['area_id'],
			);
			
		} else {
			$this->msg = mt("cart not available");
			$this->details = array(
			  'customer_phone'=>$customer_phone			  
			);
		}	
		$this->output();
	}	
	
	public function actionsetAddressBookLocation()
	{		
		if ($client_id = $this->checkToken()){
			$addressbook_id = isset($this->data['addressbook_id'])?$this->data['addressbook_id']:'';		
			if($addressbook_id>0){
				if($res = LocationWrapper::getAddressBookByID($addressbook_id,$client_id)){					
					$new_data = array(
					  'contact_phone'=>isset($this->data['contact_phone'])?$this->data['contact_phone']:'',
					  'delivery_instruction'=>isset($this->data['delivery_instruction'])?$this->data['delivery_instruction']:'',
					  'street'=>$res['street'],
					  'state_name'=>$res['state_name'],
					  'city_name'=>$res['city_name'],
					  'area_name'=>$res['area_name'],
					  'location_name'=>$res['location_name'],
					  'lat'=>$res['lat'],
					  'lng'=>$res['lng'],
					  'state_id'=>$res['state_id'],
					  'city_id'=>$res['city_id'],
					  'area_id'=>$res['area_id'],
					  'save_address'=>0,
					);
					$this->data = $new_data;
					$this->actionsetDeliveryLocation();
					$this->output();
				} else $this->msg = mt("invalid address book not found");
			} else $this->msg = mt("invalid address book id");
		}		
		$this->output();
	}
	
	public function actionsetDeliveryLocation()
	{		
			
		$db = new DbExt();
		$params=array();
		$params['street'] = isset($this->data['street'])?$this->data['street']:'';
		$params['state'] = isset($this->data['state_name'])?$this->data['state_name']:'';
		$params['city']= isset($this->data['city_name'])?$this->data['city_name']:'';
		$params['zipcode']= isset($this->data['area_name'])?$this->data['area_name']:'';
		$params['location_name']= isset($this->data['location_name'])?$this->data['location_name']:'';
		$params['contact_phone']= isset($this->data['contact_phone'])?$this->data['contact_phone']:'';
		$params['delivery_instruction']= isset($this->data['delivery_instruction'])?$this->data['delivery_instruction']:'';
		$params['save_address']= isset($this->data['save_address'])?$this->data['save_address']:0;
		$params['delivery_lat']= isset($this->data['lat'])?$this->data['lat']:'';
		$params['delivery_long']= isset($this->data['lng'])?$this->data['lng']:'';
		$params['state_id']= isset($this->data['state_id'])?$this->data['state_id']:'';
		$params['city_id']= isset($this->data['city_id'])?$this->data['city_id']:'';
		$params['area_id']= isset($this->data['area_id'])?$this->data['area_id']:'';
		
		$delivery_fee = getOption($this->merchant_id,'merchant_delivery_charges');
		
		$resp_delivery = LocationWrapper::getDeliveryFee(
		 $this->merchant_id,
		 $delivery_fee,
		 $params['state_id'],
		 $params['city_id'],
		 $params['area_id']
		);
				
		if($resp_delivery){
		  $params['delivery_fee']=$resp_delivery;
		} else {
		   $this->msg = mt("Sorry this merchant does not deliver to your location");
		   $this->output();
		}	
				
		$db->updateData("{{mobile2_cart}}",$params,'device_uiid',$this->device_uiid);
		
		if($params['save_address']==1){
			if ($client_id = $this->checkToken()){
				if (!LocationWrapper::isAddressBookExist($client_id,$params['street'],$params['state_id'],$params['city_id'],$params['area_id'])){
					mobileWrapper::UpdateAllAddressBookDefaultLocation($client_id);
					$address = array(
					  'client_id'=>$client_id,
					  'street'=>$params['street'],
					  'location_name'=>$params['location_name'],
					  'country_id'=>LocationWrapper::getCountryID($params['state_id']),
					  'state_id'=>$params['state_id'],
					  'city_id'=>$params['city_id'],
					  'area_id'=>$params['area_id'],
					  'latitude'=>$params['delivery_lat'],
					  'longitude'=>$params['delivery_long'],
					  'as_default'=>1,
					  'date_created'=>FunctionsV3::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR']
					);
					$db->insertData("{{address_book_location}}",$address);
				}		
			}
		}	
		
		$this->code = 1;
		$this->msg = "OK";
		$this->details = array(		  
		);
		$this->output();
	}
	
	public function actiongetAddressLocationBookDropDown()
	{
		if ($client_id = $this->checkToken()){
			if ($res=LocationWrapper::getAddressBook($client_id)){
				foreach ($res as $val) {
					$val['as_default'] = $val['as_default']==1?2:0;
					$data[]=$val;
				}
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				 'data'=>$data
				);
			} else $this->msg = $this->t("No results");
		}
		$this->output();
	}
	
	public function actionPostalCodeList()
	{
		$page_limit = mobileWrapper::paginateLimit();
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
        
        $search_string = isset($this->data['search_str'])?$this->data['search_str']:'';
		
		if($res = LocationWrapper::GetPostalCodeList($page,$page_limit, $search_string) ){			
			$this->code =1;
			$this->msg = "OK";
			$this->details = array(
			  'page_action'=>isset($this->data['page_action'])?$this->data['page_action']:'',
			  'paginate_total'=>$res['paginate_total'],
			  'data'=>$res['list'],
			);
		} else $this->msg = mt("No results");
		$this->output();
	}
	
	public function actionrecheckLocation()
	{
		$new_lat = isset($this->data['new_lat'])?$this->data['new_lat']:'';
		$new_lng = isset($this->data['new_lng'])?$this->data['new_lng']:'';
		
		$old_lat = isset($this->data['old_lat'])?$this->data['old_lat']:'';
		$old_lng = isset($this->data['old_lng'])?$this->data['old_lng']:'';
		
		$this->msg="OK";
		
		$distance_type_raw="M";
		$distance=FunctionsV3::getDistanceBetweenPlot($new_lat,$new_lng,$old_lat,$old_lng,"M");  
		if($distance){			
			if(!empty(FunctionsV3::$distance_type_result)){
             	$distance_type_raw=FunctionsV3::$distance_type_result;
            }
            if($distance_type_raw=="ft" || $distance_type_raw=="meter" || $distance_type_raw=="mt"){
            	// do nothing
            } else {
	            if($distance>5){
	              	$this->code = 1;	              	
	              	$this->msg = mt("Your address is too far from your current location");
	              	$this->details = array(
	              	 'distance'=>$distance
	              	);
	            }		
            }
		} else $this->msg = mt("distance not available");
		$this->output();
	}
	
	public function actiongetActiveMerchantCategory()
	{
		$this->getGETData();
		if($this->merchant_id>0){
			if($res = itemWrapper::getMerchantCategory($this->merchant_id)){
				$this->code =1;
				$this->msg = "ok";
				$this->details = array(
				 'data'=>$res
				);
			} else $this->msg = mt("no results");
		} else $this->msg = mt("invalid merchant id");
		$this->output();
	}
	
	public function actionKushiRequestToken()
	{
		$data = $_POST;					
		$currency_code = FunctionsV3::getCurrencyCode();
		$amount=0;
		
		$order_id = isset($data['order_id'])?$data['order_id']:'';
		
		if ($res = Yii::app()->functions->getOrderInfo($order_id)){
			
			$merchant_id = $res['merchant_id'];
			$reference_id = $res['order_id_token'];
			
			$payment_description = Yii::t("default","Purchase Order ID# [order_id]",array(
	         '[order_id]'=>$order_id
	        ));	        	        
			
			if(!$credentials = KushiWrapper::getCredentials($merchant_id)){
				$this->msg = mt("invalid merchant credentials");
				$this->output();
			}		
			
			$amount = normalPrettyPrice($res['total_w_tax']);
			$params = array(
			  'card'=>array(
			    'name'=>isset($data['card_name'])?$data['card_name']:'',
			    'lastName'=>isset($data['card_last_name'])?$data['card_last_name']:'',
			    'number'=>trim( str_replace(" ","",$data['credit_card_number']) ),
			    'expiryMonth'=>trim($data['expiration_month']),
			    'expiryYear'=>substr($data['expiration_yr'],2,2),
			    'cvv'=>trim($data['cvv']),
			  ),
			  'totalAmount'=>(float)$amount,
			  'currency'=>$currency_code
			);	
			
			//dump($credentials);dump($params);die();
			
			$card_details = mt("[card_name] ([card_number])",array(
			  '[card_name]'=>$params['card']['name']." ".$params['card']['lastName'],
			  '[card_number]'=>FunctionsV3::maskCardnumber($params['card']['number']),
			));
			
			try {
				
				$transaction_token = KushiWrapper::requestToken($params,$credentials['public_key'],$credentials['api_url']);
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'transaction_token'=>$transaction_token,
				  'reference_id'=>$reference_id,
				  'payment_description'=>$payment_description,
				  'amount'=>FunctionsV3::prettyPrice($amount),
				  'amount_raw'=>$amount,
				  'card_details'=>$card_details
				);
				
			} catch (Exception $e) {
			    $this->msg = Yii::t("mobile2","Caught exception: [error]",array(
				  '[error]'=>$e->getMessage()
				));
			}   								
			
		} else $this->msg = mt("Order id not found");		
					
		$this->output();
	}
	
	public function actionKushiChargeToken()
	{		
		$this->data = $_POST;	
		$this->getPOSTData();
			
		$transaction_token = isset($this->data['transaction_token'])?$this->data['transaction_token']:'';
		$reference_id = isset($this->data['reference_id'])?$this->data['reference_id']:'';
				
		if( $res = FunctionsV3::getOrderInfoByToken($reference_id)){
			
			$merchant_id = $res['merchant_id'];			
			$order_id = $res['order_id'];
			$client_id = $res['client_id'];
			
			$payment_description = Yii::t("default","Purchase Order ID# [order_id]",array(
	         '[order_id]'=>$order_id
	        ));	        	        
			
			if(!$credentials = KushiWrapper::getCredentials($merchant_id)){
				$this->msg = mt("invalid merchant credentials");
				$this->output();
			}		
			
			$amount = normalPrettyPrice($res['total_w_tax']);	
			$currency_code = FunctionsV3::getCurrencyCode();
			$payment_code = KushiWrapper::paymentCode();
								
			$params = array(
			  'token'=>$transaction_token,
			  'amount'=>array(
			    'subtotalIva'=>0,
			    'subtotalIva0'=>(float)$amount,
			    'ice'=>0,
			    'iva'=>0,
			    'currency'=>$currency_code
			  ),
			  'months'=>1,				  
			  'metadata'=>array(
			    'reference_id'=>$reference_id,
			    'payment_description'=>$payment_description,
			    'order_id'=>$order_id,
			    'request_from'=>'mobileapp2'
			  ),
			  'fullResponse'=>true
			);						

			try {		
				
				$resp = KushiWrapper::chargeToken($params,$credentials['private_key'],$credentials['api_url']);
			    $payment_gateway_ref = $resp['ticketNumber'];
		    
			    FunctionsV3::updateOrderPayment($order_id,$payment_code,
    		  	$payment_gateway_ref,$resp,$reference_id);		 
    		  	     		  	 		    		  	  		    		  	  
			    /*SEND EMAIL RECEIPT*/
	            mobileWrapper::sendNotification($order_id);	
	              
	            mobileWrapper::executeAddons($order_id);
	              
	            /*CLEAR CART*/
	            mobileWrapper::clearCart($this->device_uiid); 
	            
	            $this->code = 1;
		        $this->msg = Yii::t("mobile2","Your order has been placed. Reference # [order_id]",array(
			  	      '[order_id]'=>$order_id
			    ));
			    
			    $this->details=array(
				       'order_id'=>$order_id,
				       'total_amount'=>$amount,		
				       'next_step'=>'receipt'		      
				      );			    		  	  
			    
		    } catch (Exception $e) {
				$this->msg = Yii::t("mobile2","Caught exception: [error]",array(
				  '[error]'=>$e->getMessage()
				));
			}     		    
			
		} else $this->msg = mt("Order id not found");
		$this->output();
	}	
	
	public function actionpreCheckout()
	{
		$this->setMerchantTimezone();
		
		$date_now = date("Y-m-d");
		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
		$delivery_date = isset($this->data['delivery_date'])?$this->data['delivery_date']:'';
    	$delivery_time = isset($this->data['delivery_time'])?$this->data['delivery_time']:'';
    	    	    	    	
    	if($merchant_id>0){
    	   $resp = mobileWrapper::preCheckout($merchant_id,$date_now,$delivery_date,$delivery_time);    	   
    	   $this->code = $resp['code'];
    	   $this->msg = $resp['message'];
    	   $this->details = array(
    	    'future_order'=>$resp['future_order'],
    	    'future_order_confirm'=>getOptionA('mobileapp2_future_order_confirm'),
    	    'future_order_message'=>mt("This order is for another day. Continue?")
    	   );
    	} else $this->msg = mt("invalid merchant id");
    	
		$this->output();
	}
	
	public function actionContactSubmit()
	{
		$this->data = $_POST;				
		$Validator=new Validator;
		
		$req=array(
    	  'name'=>mt("Name is required"),    	  
    	  'email'=>mt("Email is required"),
    	  'contact_phone'=>mt("Mobile number is required")    	  
    	);
		
    	if($Validator->validate()){
    		$lang=Yii::app()->language;
	    	$to=getOptionA('contact_email_receiver');
	    	$from='';
	    	
	    	$contact_us=getOptionA('contact_us_email');
    		if ( $contact_us==""){	    			
    		    $this->msg=mt("Contact form template is not enabled in template settings");
    			$this->output();
    		}	    
    		
    		$subject=getOptionA('contact_us_tpl_subject_'.$lang);
    		$tpl=getOptionA('contact_us_tpl_content_'.$lang);
    		
    		if(!empty($tpl)){	    				    				    		
    			$tpl=FunctionsV3::smarty('name',
    			isset($this->data['name'])?$this->data['name']:'',$tpl);
    			
    			$tpl=FunctionsV3::smarty('email',
    			isset($this->data['email'])?$this->data['email']:'',$tpl);
    			
    			$tpl=FunctionsV3::smarty('country',
    			isset($this->data['country'])?$this->data['country']:'',$tpl);
    			
    			$tpl=FunctionsV3::smarty('message',
    			isset($this->data['message'])?$this->data['message']:'',$tpl);
    			
    			$tpl=FunctionsV3::smarty('phone',
    			isset($this->data['contact_phone'])?$this->data['contact_phone']:'',$tpl);
    			
    			$tpl=FunctionsV3::smarty('sitename',getOptionA('website_title'),$tpl);
    			$tpl=FunctionsV3::smarty('siteurl',websiteUrl(),$tpl);
    		} 	    	
    		
    		if (empty($to)){
				$this->msg=Yii::t("default","ERROR: no email to send.");
				$this->output();
			}	    	
			if(empty($subject)){
				$this->msg=t("Subject is empty");
				$this->output();
			}	    	
			if(empty($tpl)){
				$this->msg=t("Template is empty");
				$this->output();
			}	    	
												
			if ( Yii::app()->functions->sendEmail($to,$from,$subject,$tpl) ){
				$this->code=1;    		
    		    $this->msg=Yii::t("default","Your message was sent successfully. Thanks.");
			} else $this->msg=Yii::t("default","ERROR: Cannot sent email.");	
    	
    	} else $this->msg = mobileWrapper::parseValidatorError($Validator->getError());
    			
		$this->output();
	}
	
	public function actionfoodPromo()
	{
		$db = new DbExt();
		itemWrapper::setMultiTranslation();
		
		$page_limit = mobileWrapper::paginateLimit();
		
		$search_resp = mobileWrapper::searchMode();
		$search_mode = $search_resp['search_mode'];
		$location_mode = $search_resp['location_mode'];	
				
		if (isset($this->data['page'])){
        	$page = $this->data['page'] * $page_limit;
        } else  $page = 0;  
        
        $sort_by = isset($this->data['sort_by'])?$this->data['sort_by']:'DESC';		
        $sort_fields = isset($this->data['sort_fields'])?$this->data['sort_fields']:'discount';       
        
        $page_action =  isset($this->data['page_action'])?$this->data['page_action']:'';        
        
        $default_image='resto_banner.jpg';
		$disabled_default_image=false;
        
		$item_name='';
		$and ='';
		if (isset($this->data['item_name'])){
			$item_name = trim($this->data['item_name']);
			$and.=" AND a.item_name LIKE ".FunctionsV3::q("%".$item_name."%")."";
		}
		
		if($search_mode=="location"){		
			
			$state_id = isset($this->data['state_id'])?$this->data['state_id']:-1;
			$city_id = isset($this->data['city_id'])?$this->data['city_id']:-1;
		    $area_id = isset($this->data['area_id'])?$this->data['area_id']:-1;
		    $postal_code = isset($this->data['postal_code'])?$this->data['postal_code']:-1;	    
		    $current_page = isset($this->data['current_page'])?$this->data['current_page']:'';	  
		    	
			$and.= LocationWrapper::queryLocation((integer)$location_mode,array(
			 'state_id'=>$state_id,
			 'city_id'=>$city_id,
			 'area_id'=>$area_id,
			 'postal_code'=>$postal_code,
			));
		} else {
			$lat = isset($this->data['lat'])?$this->data['lat']:0;
		    $lng = isset($this->data['lng'])?$this->data['lng']:0;
		    
			$home_search_unit_type=getOptionA('home_search_unit_type');
			$distance_exp=3959;
			if ($home_search_unit_type=="km"){
				$distance_exp=6371;
			}	
					
			$query_distance="
			( $distance_exp * acos( cos( radians($lat) ) * cos( radians( latitude ) ) 
					* cos( radians( lontitude ) - radians($lng) ) 
					+ sin( radians($lat) ) * sin( radians( latitude ) ) ) ) 
					AS distance		
			";		
			$and.="
			AND merchant_id IN (
			   select merchant_id 
			   from {{merchant}}
			   where delivery_distance_covered > (
			      select 
			      $query_distance from {{merchant}}
			      where merchant_id = a.merchant_id
			   )
			)
			";
		}
		
        $stmt="
        SELECT 
        SQL_CALC_FOUND_ROWS 
        a.merchant_id,a.item_id,a.item_name,a.item_name_trans,a.photo,a.price,a.discount,
        a.item_description,a.item_description_trans,a.category
        FROM {{item}} a
        WHERE discount>0
        AND status IN ('publish')
        $and
        ORDER BY $sort_fields $sort_by
        LIMIT $page,$page_limit
        ";                              
        if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
        	
        	$total_records=0;
		    $stmtc="SELECT FOUND_ROWS() as total_records";
	 		if ($resp=$db->rst($stmtc)){			 			
	 			$total_records=$resp[0]['total_records'];
	 		}		
        	
        	$data = array(); $prices2 = array(); 
        	foreach ($res as $val) {
        		
        		itemWrapper::$sizes = itemWrapper::getSize($val['merchant_id']);
        		
        		if ( json_decode($val['price'])){
        			$price = json_decode($val['price'],true);
        			foreach ($price as $size_id=>$priceval) {
        				$original_price = $priceval;
						$discounted_price = 0;
						
						if($val['discount']>=0.001){
							$priceval = $priceval-$val['discount'];
							$discounted_price = $priceval;
						}					
							
						if(array_key_exists($size_id,(array)itemWrapper::$sizes)){
							$prices[]=itemWrapper::$sizes[$size_id]."&nbsp;".FunctionsV3::prettyPrice($priceval);
							$prices2[] = array(							  
							  'original_price'=>itemWrapper::$sizes[$size_id]."&nbsp;".FunctionsV3::prettyPrice($original_price),
							  'discount'=>$val['discount'],
							  'discounted_price_pretty'=>itemWrapper::$sizes[$size_id]."&nbsp;".FunctionsV3::prettyPrice($priceval),
							);
						} else {							
							$prices[]=FunctionsV3::prettyPrice($priceval);		
							$prices2[] = array(							  
							  'original_price'=>FunctionsV3::prettyPrice($original_price),
							  'discount'=>$val['discount'],
							  'discounted_price_pretty'=>FunctionsV3::prettyPrice($priceval),
							);
						}
        			}
        		}
        		
        		$val['photo']=mobileWrapper::getImage($val['photo'],$default_image,$disabled_default_image);
        		
        		if ( json_decode($val['category'])){
        			$category = json_decode($val['category'],true);        			
        			$val['category_id'] = $category[0];
        		} else $val['category_id']='';
        		        		
        		if(itemWrapper::$enabled_trans==TRUE){
					$val['item_name'] = qTranslate($val['item_name'],'item_name',array(
					  'item_name_trans'=>json_decode($val['item_name_trans'],true)
					));
					
					$val['item_description'] = qTranslate($val['item_description'],'item_description',array(
					  'item_description_trans'=>json_decode($val['item_description_trans'],true)
					));
				}
				
				if(!empty($item_name)){
			  	  $item_name = mobileWrapper::highlight_word($val['item_name'],$item_name);				
				  $val['item_name'] = $item_name;								
				}
						
				
        		$val['prices']=$prices;
        		$val['prices2']=$prices2;
        		$data[]=$val;
        		unset($prices2);
        		unset($prices);
        	}
        	
        	$this->code = 1; 
        	$this->msg = "OK";
        	$this->details = array(
        	  'page_action'=>$page_action,
        	  'total'=>mt("[total] promo found",array(
        	    '[total]'=>$total_records
        	  )),
        	  'sortby_default'=>mt("discount"),
        	  'list'=>$data
        	);
        } else {        	
        	$this->msg = $this->t("No results");
        	$this->details = array(
        	  'page_action'=>$page_action,
        	  'total'=>mt("[total] promo found",array(
        	    '[total]'=>0
        	  )),
        	);
        }	
        
		$this->output();
	}
	
	public function actionsearchFoodPromo()
	{
		$this->actionfoodPromo();
	}
	
	public function actiongetStocks()
	{
		Yii::app()->setImport(array(			
	       'application.modules.inventory.components.*',
        ));	
        		        
		/*function translate($words='', $params=array())
		{
			return Yii::t("inventory",$words,$params);
		}*/
     
		$this->data = $_POST;
		   
        $value = isset($this->data['price'])?$this->data['price']:'';
		$item_id = isset($this->data['item_id'])? (integer) $this->data['item_id']:'';
		$with_size = isset($this->data['with_size'])? (integer) $this->data['with_size']:'';
		$merchant_id = isset($this->data['merchant_id'])? (integer) $this->data['merchant_id']:0 ;
		        									
		if($merchant_id>0 && $item_id>0 ){
			try {
				
				$allow_negative_stock = InventoryWrapper::allowNegativeStock($merchant_id);
				
				$size_id = 0;
								
				if($with_size>0){
					$value = explode("|",$value);
					if(is_array($value) && count($value)>=1){
						$size_id = isset($value[2])?(integer)$value[2]:0;
					}
				}		
				
				$resp = StocksWrapper::getAvailableStocks($merchant_id,$item_id,$size_id);
				
				$this->code = 1; $this->msg = "OK";
				$this->details = array(
				  'next_action'=>"display_stocks",
				  'available_stocks'=>$resp['available_stocks'],
				  'message'=>$resp['message'],
				  'allow_negative_stock'=>$allow_negative_stock
				);			
						
			} catch (Exception $e) {
			   $this->details = array('next_action'=>"item_not_available");
		       $this->msg = Yii::t("inventory",$e->getMessage());
		    }
		} else {
			 $this->details = array('next_action'=>"item_info_not_available");
			 $this->msg = Yii::t("inventory","invalid merchant id or size id");
		}
		$this->output();
	}
	
	public function actionSageCreateCardIdentifier()
	{
		$this->getPOSTData();
		$this->data = $_POST;			
		$order_id = isset($this->data['order_id'])?(integer)$this->data['order_id']:0;					
		$_GET['id'] = $order_id;
		
		require_once 'buy.php';
		if(empty($error)){
			if ( $credentials = SageWrapper::getCredentials($merchant_id)){
				try {
					
					SageWrapper::$api=$credentials['api'];
					$merchant_keys = SageWrapper::createMerchantKeys($credentials['vendor_name'],$credentials['key'],$credentials['password']);
										
					$expiry_month = isset($this->data['expiration_month'])?trim($this->data['expiration_month']):'';				
					$expiry_yr= isset($this->data['expiration_yr'])?substr($this->data['expiration_yr'],2,2):'';	
					$expiry_yr_full = isset($this->data['expiration_yr'])?trim($this->data['expiration_yr']):'';
					
					$expiry=$expiry_month.$expiry_yr;
					
					$cvv = 	isset($this->data['cvv'])?trim($this->data['cvv']):'';
					$card_name = isset($this->data['card_name'])?trim($this->data['card_name']):'';
					$card_number  = isset($this->data['credit_card_number'])?trim($this->data['credit_card_number']):'';
					
					$card_number = str_replace(" ","",$card_number);
						
					$params['cardDetails'] = array(
					   'cardholderName'=> $card_name,
					   'cardNumber'=>$card_number ,
					   'expiryDate'=>$expiry ,
					   "securityCode"=>$cvv,
					);		
					
					$resp_card = SageWrapper::createCardIdentifier('card-identifiers',$params,$merchant_keys);	
										
					$mask_card = FunctionsV3::maskCardnumber($card_number);
					
					$billing_address = isset($this->data['billing_address'])?$this->data['billing_address']:'';
					$billing_city = isset($this->data['billing_city'])?$this->data['billing_city']:'';
					$billing_postalcode = isset($this->data['billing_postalcode'])?$this->data['billing_postalcode']:'';
									
					if(isset($this->data['save_card'])){
					   if($this->data['save_card']==1){
					   	
					   	   $encrypted_card=CreditCardWrapper::encryptCard($card_number);
					   	  
					   	   SageWrapper::saveCards($client_id,$mask_card,array(
					   	     'client_id'=>(integer)$client_id,
					   	     'card_name'=>$card_name,
					   	     'encrypted_card'=>$encrypted_card,
					   	     'credit_card_number'=>$mask_card,
					   	     'expiration_month'=>$expiry_month,
					   	     'expiration_yr'=>$expiry_yr_full,
					   	     'cvv'=>$cvv,					   	     
					   	     'card_type'=>$resp_card['cardType'],
					   	     'billing_address'=>$billing_address,
					   	     'billing_city'=>$billing_city,
					   	     'billing_postalcode'=>$billing_postalcode,
					   	     'date_created'=>FunctionsV3::dateNow(),
					   	     'date_modified'=>FunctionsV3::dateNow(),
					   	     'ip_address'=>$_SERVER['REMOTE_ADDR']
					   	   ));
					   }					
					}				
															
					$this->code = 1;
					$this->msg = "OK";
					$this->details = array(
					  'order_id'=>$order_id,
					  'merchant_keys'=>$merchant_keys,
					  'card_name'=>$params['cardDetails']['cardholderName'],
					  'card_number'=>$mask_card,
					  /*'expiry'=>$expiry,
					  'cvv'=>$cvv,*/
					  'card_identifier'=>$resp_card['cardIdentifier'],
					  'card_type'=>$resp_card['cardType'],
					  'billing_address'=>$billing_address,
					  'billing_city'=>$billing_city,
					  'billing_postalcode'=>$billing_postalcode,
					  'pay_now'=>mt("PAY [amount]",array('[amount]'=>FunctionsV3::prettyPrice($amount_to_pay)))
					);										
				} catch (Exception $e) {
		            $this->msg = $e->getMessage();		            
		        }					  
			} else $this->msg = mt("Invalid payment credentials");
		} else $this->msg = $error;			
		$this->output();
	}
	
	public function actionSageCreateTransaction()
	{
		$this->getPOSTData();
		$this->data = $_POST;
		$order_id = isset($this->data['order_id'])?(integer)$this->data['order_id']:0;
		$_GET['id'] = $order_id;
		
		require_once 'buy.php';
		if(empty($error)){
		   if ( $credentials = SageWrapper::getCredentials($merchant_id)){					   			   	   
				try {
					
					$client_info=Yii::app()->functions->getClientInfo($client_id);
							   	    		   	  
					$params = array(
					  'transactionType'=>"Payment",
					  'paymentMethod'=>array(
					    'card'=>array(
					      'merchantSessionKey'=>isset($this->data['merchant_keys'])?$this->data['merchant_keys']:'',
					      'cardIdentifier'=>isset($this->data['card_identifier'])?$this->data['card_identifier']:'',
					      'save'=>false
					    )
					  ),
					  'vendorTxCode'=>$reference_id,
					  'amount'=>$amount_to_pay*100,
					  'currency'=>$currency_code,
					  'description'=>$payment_description,
					  'apply3DSecure'=>'Disable',
					  'customerFirstName'=>isset($client_info['first_name'])?$client_info['first_name']:'',
					  'customerLastName'=>isset($client_info['last_name'])?$client_info['last_name']:'',
					  'billingAddress'=> array(
					    'address1'=>isset($this->data['billing_address'])?$this->data['billing_address']:'',
					    'city'=>isset($this->data['billing_city'])?$this->data['billing_city']:'',
					    'postalCode'=>isset($this->data['billing_postalcode'])?$this->data['billing_postalcode']:'',
					    'country'=> Yii::app()->functions->adminSetCounryCode()					    
					  ),
					  'entryMethod'=>"Ecommerce"
					);					
					
					
					SageWrapper::$api=$credentials['api'];				
					$resp = SageWrapper::createTransaction($params,$credentials['vendor_name'],$credentials['key'],$credentials['password']);
					
					
					$gateway_ref = isset($resp['transactionId'])?$resp['transactionId']:$reference_id;
					
					mobileWrapper::sendNotification($order_id);	
					
					FunctionsV3::updateOrderPayment($order_id,SageWrapper::paymentCode(),
                    $gateway_ref,$resp,$reference_id);
                    
                    mobileWrapper::executeAddons($order_id);
                    
                    /*CLEAR CART*/
                    mobileWrapper::clearCartByCustomerID($client_id);
                    
                    $this->code = 1;
                    $this->msg = Yii::t("mobile2","payment successfull with payment reference id [ref]",array(
						'[ref]'=>$gateway_ref
					));
					
					 $this->details=array(
				       'order_id'=>$order_id,
				       'total_amount'=>$amount_to_pay,		
				       'next_step'=>'receipt'		      
				      );
					
				} catch (Exception $e) {
		            $this->msg = $e->getMessage();		            
		        }					  
		   } else $this->msg = mt("Invalid payment credentials");
		} else $this->msg = $error;	
		$this->output();
	}
	
	public function actionSageLoadCard()
	{
		if ($client_id = $this->checkToken()){
			if ($resp = SageWrapper::getCreditCards($client_id)){
				$data  = array();
				foreach ($resp as $val) {
					//dump($val);
					try {																
						$data[]=array(
						  'id'=>$val['cc_id'],
						  'card_number'=>$val['credit_card_number'],
						  'card_type'=>!empty($val['card_type'])?$val['card_type']:t("Visa"),
						);
					} catch (Exception $e) {
		              //$this->msg = $e->getMessage();		            
		            }	
				}
				$this->code=1; $this->msg = "OK";
				$this->details = array(
				  'data'=>$data
				);
			} else $this->msg = mt("No results");
		}
		
		$this->output();
	}

	public function actionSageLinkCard()
	{
		$this->getPOSTData();
		$this->data = $_POST;
		$order_id = isset($this->data['order_id'])?(integer)$this->data['order_id']:0;
		$_GET['id'] = $order_id;		
		require_once 'buy.php';
		
		if ($client_id = $this->checkToken()){
			//
		}
		
		if(empty($error)){
			if ( $credentials = SageWrapper::getCredentials($merchant_id)){
				
				try {
									
				   $card_id = isset($this->data['card_id'])?(integer)$this->data['card_id']:'';
				   
				   if($card_id<=0){
				   	  $this->msg = mt("Please select card");
				   	  $this->output();
				   }				
				   
				   $card_info = SageWrapper::getCreditCardByID($client_id,$card_id);				   
				   $card_number = CreditCardWrapper::decryptCard($card_info['encrypted_card']);
				   
				   SageWrapper::$api=$credentials['api'];
				   $merchant_keys = SageWrapper::createMerchantKeys($credentials['vendor_name'],$credentials['key'],$credentials['password']);				   
				   
				   $params['cardDetails'] = array(
					   'cardholderName'=>$card_info['card_name'],
					   'cardNumber'=>$card_number ,
					   'expiryDate'=>$card_info['expiration_month'].substr($card_info['expiration_yr'],2,2),
					   "securityCode"=>$card_info['cvv'],
					);		
					
				    $resp_card = SageWrapper::createCardIdentifier('card-identifiers',$params,$merchant_keys);	
				    $card_identifier = $resp_card['cardIdentifier'];
				    				    		   	    
				    $client_info=Yii::app()->functions->getClientInfo($client_id);
				    		   	  
					$params = array(
					  'transactionType'=>"Payment",
					  'paymentMethod'=>array(
					    'card'=>array(
					      'merchantSessionKey'=>$merchant_keys,
					      'cardIdentifier'=>$card_identifier,
					      'save'=>false
					    )
					  ),
					  'vendorTxCode'=>$reference_id,
					  'amount'=>$amount_to_pay*100,
					  'currency'=>$currency_code,
					  'description'=>$payment_description,
					  'apply3DSecure'=>'Disable',
					  'customerFirstName'=>isset($client_info['first_name'])?$client_info['first_name']:'',
					  'customerLastName'=>isset($client_info['last_name'])?$client_info['last_name']:'',
					  'billingAddress'=> array(
					    'address1'=>$card_info['billing_address'],
					    'city'=>$card_info['billing_city'],
					    'postalCode'=>$card_info['billing_postalcode'],
					    'country'=> Yii::app()->functions->adminSetCounryCode()					    
					  ),
					  'entryMethod'=>"Ecommerce"
					);										
								
					$resp = SageWrapper::createTransaction($params,$credentials['vendor_name'],$credentials['key'],$credentials['password']);
									
					$gateway_ref = isset($resp['transactionId'])?$resp['transactionId']:$reference_id;
					
					mobileWrapper::sendNotification($order_id);	
					
					FunctionsV3::updateOrderPayment($order_id,SageWrapper::paymentCode(),
                    $gateway_ref,$resp,$reference_id);
                    
                    mobileWrapper::executeAddons($order_id);
                    
                    /*CLEAR CART*/
                    mobileWrapper::clearCartByCustomerID($client_id);
                    
                    $this->code = 1;
                    $this->msg = Yii::t("mobile2","payment successfull with payment reference id [ref]",array(
						'[ref]'=>$gateway_ref
					));
					
					 $this->details=array(
				       'order_id'=>$order_id,
				       'total_amount'=>$amount_to_pay,		
				       'next_step'=>'receipt'		      
				      );
				
				} catch (Exception $e) {
		            $this->msg = $e->getMessage();		            
		        }	
                
			} else $this->msg = mt("Invalid payment credentials");
		} else $this->msg = $error;	
		$this->output();
	}
	
	public function actionAddTip()
	{
		$this->getGETData();		
		$tip_amount = isset($this->data['tip_amount'])?(float)$this->data['tip_amount']:0;		
		if($resp=mobileWrapper::getCart($this->device_uiid)){
			$subtotal = (float)$resp['cart_subtotal'];			
			$percentage = ($tip_amount/$subtotal)*100;
			$percentage = number_format($percentage/100,4);			
			Yii::app()->db->createCommand()->update("{{mobile2_cart}}",array(
			  'tips'=>$percentage
			),
	  	    'device_uiid=:device_uiid',
		  	    array(
		  	      ':device_uiid'=>$this->device_uiid
		  	    )
	  	    );	  	    
	  	    $this->code = 1;
	  	    $this->msg = "OK";
	  	    $this->details = array();			
		} else $this->msg = mt("cart not available");
		$this->output();
	}
	
	public function actioncancelBooking()
	{
		$this->getGETData();
		$booking_id = isset($this->data['booking_id'])?(integer)$this->data['booking_id']:0;
		if ($client_id = $this->checkToken()){
			if($booking_id>0){
				$pattern = 'booking_id,restaurant_name,number_guest,date_booking,time,booking_name,email,mobile,instruction,status,merchant_remarks,sitename,siteurl';
    	        $pattern = explode(",",$pattern);    	        
    	        $lang = Yii::app()->language;
    	        if ($res = FunctionsV3::getBookingByIDWithDetails($booking_id)){
    	        	if($res['request_cancel']>=1){
		    			$this->msg = mt("You have already request to cancel this booking");
		    		    $this->output();
		    		}
		    		
		    		$res['sitename'] = getOptionA('website_title');
    		        $res['siteurl'] = websiteUrl();
    		        $res = Yii::app()->request->stripSlashes($res);
    		        
    		        $merchant_id = $res['merchant_id'];
    		        $merchant_email = getOption($merchant_id,'merchant_notify_email');
    		        $sender = getOptionA('global_admin_sender_email');
    		        $email_provider  = getOptionA('email_provider');
    		        
    		        /*SEND EMAIL TO MERCHANT*/
    		        if(!empty($merchant_email)){    		        
    		        	$email = getOptionA('booking_request_cancel_email');    		
			    		$subject = getOptionA('booking_request_cancel_tpl_subject_'.$lang);
			    		$content = getOptionA('booking_request_cancel_tpl_content_'.$lang);
			    		foreach ($pattern as $val) {    			
			    			$content = FunctionsV3::smarty($val, isset($res[$val])?$res[$val]:'' ,$content);
			    			$subject = FunctionsV3::smarty($val, isset($res[$val])?$res[$val]:'' ,$subject);
			    		}
			    		$merchant_email = explode(",",$merchant_email);			    		
    		        	
			    		if($email==1 && is_array($merchant_email) && count($merchant_email)>=1){
			    			foreach ($merchant_email as $_mail) {
			    				$params = array(
			    				  'email_address'=>$_mail,
			    				  'sender'=>$sender,
			    				  'subject'=>$subject,
			    				  'content'=>$content,
			    				  'date_created'=>FunctionsV3::dateNow(),
			    				  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			    				  'email_provider'=>$email_provider,	    				  
			    				);
			    				Yii::app()->db->createCommand()->insert("{{email_logs}}",$params);
			    			}
			    		}
    		        }
    		        
    		        /*SMS*/	
    		        $balance=Yii::app()->functions->getMerchantSMSCredit($merchant_id);	
    		        $phone = getOption($merchant_id,'merchant_cancel_order_phone');	    		    		        
    		        $sms_enabled = getOptionA('booking_request_cancel_sms');
    		        
		    		if(!empty($phone) && $balance>0 && $sms_enabled==1){	    		    			
		    		    $sms_content = getOptionA('booking_request_cancel_sms_content_'.$lang);
		    		    foreach ($pattern as $val) {    			
		    			   $sms_content = FunctionsV3::smarty($val, isset($res[$val])?$res[$val]:'' ,$sms_content);	    			   
		    		    }
		    		    $params = array(
		    		      'merchant_id'=>$merchant_id,
		    		      'contact_phone'=>$phone,
		    		      'sms_message'=>$sms_content,
		    		      'date_created'=>FunctionsV3::dateNow(),
		    		      'ip_address'=>$_SERVER['REMOTE_ADDR']
		    		    );
		    		    Yii::app()->db->createCommand()->insert("{{sms_broadcast_details}}",$params);    		    
		    		}
		    		
		    		/*PUSH*/	    		
		    		if(Yii::app()->db->schema->getTable("{{mobile_device_merchant}}")){
		    			
		    			$push_enabled=getOptionA('booking_request_cancel_sms');
		    			$push_title=getOptionA('booking_request_cancel_push_title_'.$lang);
		    			$push_message=getOptionA('booking_request_cancel_push_content_'.$lang);
		    			
		    			$resp = Yii::app()->db->createCommand()
				          ->select()
				          ->from('{{mobile_device_merchant}}')   
				          ->where("merchant_id=:merchant_id AND enabled_push=:enabled_push AND status=:status",array(
				             ':merchant_id'=>$merchant_id,			             
				             ':enabled_push'=>1,
				             ':status'=>'active'
				          )) 
				          ->limit(1)
				          ->queryAll();	
				        if($resp && $push_enabled==1){
				        	
				        	foreach ($pattern as $val) {    			
    			               $push_title = FunctionsV3::smarty($val, isset($res[$val])?$res[$val]:'' ,$push_title);
    			               $push_message = FunctionsV3::smarty($val, isset($res[$val])?$res[$val]:'' ,$push_message);
    		                }
    		                    		        
				        	foreach ($merchant_resp as $merchant_device_id) {
				        		$params_merchant = array(
				        		  'merchant_id'=>(integer)$merchant_id,
				        		  'user_type'=>$merchant_device_id['user_type'],
				        		  'merchant_user_id'=>(integer)$merchant_device_id['merchant_user_id'],
				        		  'device_platform'=>$merchant_device_id['device_platform'],
				        		  'device_id'=>$merchant_device_id['device_id'],
				        		  'push_title'=>$push_title,
				        		  'push_message'=>$push_message,
				        		  'date_created'=>FunctionsV3::dateNow(),
				        		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
				        		  'booking_id'=>(integer)$booking_id
				        		);
				        		Yii::app()->db->createCommand()->insert("{{mobile_merchant_pushlogs}}",$params_merchant);
				        	}
				        }
		    		}
		    		
		    		Yii::app()->db->createCommand()->update("{{bookingtable}}",
		    		 array(
		    		   'request_cancel'=>1,
		    		   'status'=>'request_cancel_booking'
		    		 )
		    		,
		      	     'booking_id=:booking_id',
		      	     array(
		      	       ':booking_id'=>$booking_id
		      	     )
		      	   );
    		        
    		        $this->code = 1;
    		        $this->msg = mt("Your request has been sent to merchant");
		    		
    	        } else $this->msg = t("Booking id not found");    	        
			} else $this->msg = mt("Invalid booking id");
		}		
		$this->output();
	}
	
	public function actionretrievePasswordBySMS()
	{
		$lang = Yii::app()->language;
		
		$phone_number = isset($this->data['user_mobile'])?$this->data['user_mobile']:'';
		if(empty($phone_number)){
			$this->msg = mt("Phone number is required");
			$this->output();
		}
		if(strlen($phone_number)<=4){
			$this->msg = mt("Invalid phone number");
			$this->output();
		}
		
		try {
		
		   $code = Yii::app()->functions->generateRandomKey(5);
		   $res = FunctionsV3::getCustomerByPhone( str_replace("+","",$phone_number) );
		   $token=md5(date('c').$res['client_id']);  													 
		   
		   FunctionsV3::updateCustomerProfile($res['client_id'],array(
			 'mobile_verification_code'=>$code,
			 'mobile_verification_date'=>FunctionsV3::dateNow(),
			 'lost_password_token'=>$token,
			 'ip_address'=>$_SERVER['REMOTE_ADDR']
		   ));
		   
		   $resp = FunctionsV3::getNotificationTemplate('customer_forgot_password',$lang,'sms');
		   $data = array(
			  'firstname'=>$res['first_name'],
			  'lastname'=>$res['last_name'],
			  'code'=>$code,							  
			);		
			$sms_content = $resp['sms_content'];
			$sms_content = FunctionsV3::replaceTags($sms_content,$data);			
			$sms = Yii::app()->functions->sendSMS($phone_number,$sms_content);
			if($sms['msg']!="process"){
				$this->code=1; $this->msg=mt("We have sent verification code in your phone number");
				$this->details = array(							   	  				  
				  'forgot_password_token'=>$token
				);
			} else $this->msg = mt("Failed sending sms [error]",array(
			  '[error]'=>$sms['msg']
			));
			
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();
		}	
		$this->output();
	}
	
	public function actionchangePasswordBySMS()
	{		
		$token = isset($this->data['forgot_password_token'])?trim($this->data['forgot_password_token']):'';
    	$sms_code = isset($this->data['sms_code'])?trim($this->data['sms_code']):'';
    	if($res = Yii::app()->functions->getLostPassToken($token)){    		
    		if($res['mobile_verification_code']==$sms_code){ 
    			$new_password = isset($this->data['new_password'])?$this->data['new_password']:'';
    			$confirm_new_password = isset($this->data['confirm_new_password'])?$this->data['confirm_new_password']:'';
    			if(!empty($new_password) && $new_password==$confirm_new_password){
    				
    				 Yii::app()->db->createCommand()->update("{{client}}",
			    		 array(
			    		   'password'=>md5($new_password),
			    		   'ip_address'=>$_SERVER['REMOTE_ADDR'],
			    		   'date_modified'=>FunctionsV3::dateNow()
			    		 )
			    		,
			      	     'client_id=:client_id',
			      	     array(
			      	       ':client_id'=>$res['client_id']
			      	     )
			      	   );   			
			      	   
	    			   $this->code = 1; 
	    			   $this->msg = t("You have successfully change your password");
	    			   
    				
    			} else $this->msg = t("Password is not valid");
    		} else $this->msg = t("Invalid verification code");	
    	} else $this->msg = t("Invalid token");
    	$this->output();
	}
	
	public function actionsaveSubsribe()
	{
		if(!empty($this->device_uiid)){
			$subscribe_topic = isset($this->data['subscribe_topic'])?$this->data['subscribe_topic']:0;			
			$params = array(
			  'subscribe_topic'=>(integer)$subscribe_topic,
			  'date_modified'=>FunctionsV3::dateNow(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR']
			);			
			
			Yii::app()->db->createCommand()->update("{{mobile2_device_reg}}",$params,
	  	    'device_uiid=:device_uiid',
		  	    array(
		  	      ':device_uiid'=>$this->device_uiid
		  	    )
	  	    );
			
			$this->code=1;
			$this->msg = $this->t("Setting saved");
			$this->details = array(
			  'subscribe_topic'=>$params['subscribe_topic']
			);
		} else $this->msg = $this->t("invalid device uiid");
		$this->output();
	}
	
}
/* END CLASS*/