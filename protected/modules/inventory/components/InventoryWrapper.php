<?php
class InventoryWrapper
{
	public static function uploadPath()
	{
		return Yii::getPathOfAlias('webroot')."/upload/";
	}
	
	public static function setTimeZone($merchant_id='')
	{
		$website_timezone=getOptionA('website_timezone');
	    if (!empty($website_timezone)){
	 	   Yii::app()->timeZone=$website_timezone;
	    }	
	    if(!empty($merchant_id)){
	    	$mt_timezone=getOption("merchant_timezone", (integer) $merchant_id);			
	    	if (!empty($mt_timezone)){
	    		Yii::app()->timeZone=$mt_timezone;
	    	}    	
	    }
	}
	
	
	public static function purify($text='')
	{
		$p = new CHtmlPurifier();
		return $p->purify($text);
	}
	
	public static function purifyData($data=array())
	{
		if(is_array($data) && count($data)>=1){
			$p = new CHtmlPurifier(); $new_data=array();
			foreach ($data as $key=>$val) {
				$new_data[$key]=$p->purify($val);
			}
			return $new_data;
		}
		return false;
	}
	
	public static function userType()
	{
		return array(
		  'merchant'=>translate("Merchant"),
		  'admin'=>translate("Administrator"),		  
		);
	}
	
	public static function getPageTitle($page_id='')
	{						
		$page_title = array(
		  '404'=>translate("Page not found"),
		  'index'=>translate("Login"),
		  'dashboard'=>translate("Dashboard"),
		  'admin_merchant_list'=>translate("Merchant list"),		  
		  'admin_access_rights'=>translate("Access rights"),		  
		  'admin_create_role'=>translate("Create role"),		  
		  'admin_low_stock_logs'=>translate("Low stock"),
		  'admin_general'=>translate("General settings"),		  
		  'admin_settings_reports'=>translate("Settings reports"),
		  'admin_databaseupdate'=>translate("Settings database"),
		  'admin_cronjobs'=>translate("Settings cron jobs"),
		  'settings'=>translate("Settings"),
		  'merchant_list'=>translate("Merchant List"),		  
		  'item_list'=>translate("Item list"),
		  'item_item_new'=>translate("Item new"),
		  'item_category_list'=>translate("Category list"),
		  'item_category_add'=>translate("Category add/update"),
		  'item_addon_category_list'=>translate("Addon list"),
		  'item_addon_category_add'=>translate("Addon add/update"),
		  'item_size_list'=>translate("Size List"),
		  'item_size_list_add'=>translate("Size add/update"),
		  'item_ingredients_list'=>translate("Ingredients"),
		  'item_ingredients_add'=>translate("Ingredients add/update"),
		  'item_cooking_list'=>translate("Cooking Reference"),
		  'item_cooking_add'=>translate("Cooking Reference add/update"),
		  'item_addon_item_list'=>translate("Addon item"),
		  'item_addon_add'=>translate("Addon item add/update"),		  
		  'stocks_adjustment_list'=>translate("Stock adjustments"),
		  'reports_sales_summary'=>translate("Sales summary"),
		  'reports_sales_item'=>translate("Sales by item"),
		  'reports_sales_category'=>translate("Sales by category"),
		  'reports_sales_payment_type'=>translate("Sales by payment type"),
		  'reports_sales_receipt'=>translate("Receipts"),
		  'reports_sales_by_addon'=>translate("Sales by addon"),
		  'stocks_order_list'=>translate("Purchase order"),
		  'stocks_purchase_new'=>translate("Create purchase order"),
		  'stocks_adjustment_new'=>translate("Create stock adjustments"),
		  'stocks_supplier_list'=>translate("Supplier"),
		  'stocks_supplier_new'=>translate("Create supplier"),
		  'stocks_history'=>translate("Inventory history"),
		  'stocks_valuation'=>translate("Inventory valuation"),
		  'reports_userlist'=>translate("User list"),
		  'reports_add_user'=>translate("Create user"),
		  'reports_access_rights'=>translate("Access rights"),
		  'reports_create_role'=>translate("Add role"),
		  'reports_general'=>translate("Settings"),
		  'reports_settings_reports'=>translate("Reports"),
		  'reports_databaseupdate'=>translate("Database update"),
		  'reports_cronjobs'=>translate("Cron jobs"),
		  'profile'=>translate("Profile"),
		  'admin_update_data'=>translate("Update data")
		);
		if(array_key_exists($page_id,$page_title)){
			return $page_title[$page_id];
		}
		return false;
	}
	
	public static function setLanguage()
	{
		FunctionsV3::handleLanguage(); 
		$lang=Yii::app()->language;
		InventoryWrapper::registerScript(array(
		 "var lang='".CJavaScript::quote($lang)."';",		 
		),'lang_script');
	}
	
	public static function includeMerchantJS()
	{
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/js/app.js',
			CClientScript::POS_END
		);
	}
	
	public static function includeAdminJS()
	{
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/js/admin.js',
			CClientScript::POS_END
		);
	}
	
	public static function registerScript($script=array(), $script_name='reg_script')
	{
		$reg_script='';
		if(is_array($script) && count($script)>=1){		
			foreach ($script as $val) {
				$reg_script.="$val\n";
			}
		    $cs = Yii::app()->getClientScript(); 
			$cs->registerScript(
			  $script_name,
			  "$reg_script",
			  CClientScript::POS_HEAD
			);		
		}
	}
	
	public static function prettyQuantity($qty=0)
	{
		if(is_numeric($qty)){
			return number_format($qty,0,0,',');
		}
		return 0;
	}
	
	public static function prettySKU($sku='')
	{
		if(!empty($sku)){
		  return translate("SKU")." ".$sku;
		}
		return false;
	}
	
	public static function prettyItemName($item_name='',$size='',$sku='')
	{
		if(!empty($item_name)){
			if(!empty($size)){
				$item_name.=" ($size)";
			}
			return $item_name."<div class=\"text-muted\">".self::prettySKU($sku)."</div>";
		}
		return false;
	}
	
	public static function dropdownFormat($data=array(),$value='', $label='', $default_value=array())
	{				
		$list = array();
		if(is_array($default_value) && count($default_value)>=1){
			$list = $default_value;
		} else $list['']='';		
		if(is_array($data) && count($data)>=1){
			foreach ($data as $val) {
				if(isset($val[$value]) && isset($val[$label])){
			 	   $list[ $val[$value] ] = translate($val[$label]);
				}
			}
		}
		return $list;
	}
	
	public static function registerJS($data=array())
	{
		$cs = Yii::app()->getClientScript();
		if(is_array($data) && count($data)>=1){
			foreach ($data as $link) {
				Yii::app()->clientScript->registerScriptFile($link,CClientScript::POS_END);
			}
		}		
	}
	
	public static function registerCSS($data=array())
	{		
		$cs = Yii::app()->getClientScript();		
		if(is_array($data) && count($data)>=1){
			foreach ($data as $link) {
				$cs->registerCssFile($link);
			}
		}		
	}
			
	public static function insertInventorySale($order_id='', $status='', $merchant_id='')
	{
		
		$merchant_id = (integer) $merchant_id;
		
		$accepted_status = getOptionA('inventory_accepted_order_status');
		$cancel_status = getOptionA('inventory_cancel_order_status');
		if(!empty($accepted_status)){
			$accepted_status = json_decode($accepted_status,true);
		} else {
			$accepted_status = array('pending','paid');
		}
		if(!empty($cancel_status)){
			$cancel_status = json_decode($cancel_status,true);
		} else {
			$cancel_status = array('cancelled','decline','cancel');
		}
								
		$transaction_type='';
		
		if(in_array($status,(array)$accepted_status)){
			$transaction_type='sale';
		}
		if(in_array($status,(array)$cancel_status)){
			$transaction_type='cancelled';
		}		
				
		
		$merge_status = array_merge( (array)$accepted_status, (array)$cancel_status);
		
		$order_id = (integer) $order_id;
		if($order_id>0){
			
			$resp = Yii::app()->db->createCommand()
	          ->select('')
	          ->from('{{inventory_sales}}')   
	          ->where("order_id=:order_id AND transaction_type=:transaction_type",array(
	            ':order_id'=>$order_id,	            
	            ':transaction_type'=>$transaction_type
	          ))	          
	          ->limit(1)
	          ->queryRow();
			if(!$resp){
				if(in_array($status, (array) $merge_status)){
					$params = array(
					  'order_id'=>$order_id,
					  'transaction_type'=>$transaction_type,
					  'order_status'=>trim($status),
					  'created_at'=>FunctionsV3::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR']
					);			
					if(Yii::app()->db->createCommand()->insert("{{inventory_sales}}",$params)){						
						CronWrapper::processInventorySale();
						return true;
					} else throw new Exception( "Failed cannot insert records" );
				} throw new Exception( "status not accepted" );
			}
		} else throw new Exception( "Invalid order id" );
	}
	
	public static function prettyDate($date='' , $date_format='[M] d,Y')
	{
		if (!empty($date)){			
			if (empty($date_format)){
				$date_format="[M] d,Y";
			}			
			$date = date($date_format,strtotime($date));
			
			$date = translate($date,array(
			 '[Jan]'=>translate("Jan"),
			 '[Feb]'=>translate("Feb"),			
			 '[Mar]'=>translate("Mar"),
			 '[Apr]'=>translate("Apr"),
			 '[May]'=>translate("May"),
			 '[Jun]'=>translate("Jun"),
			 '[Jul]'=>translate("Jul"),
			 '[Aug]'=>translate("Aug"),
			 '[Sep]'=>translate("Sep"),
			 '[Oct]'=>translate("Oct"),
			 '[Nov]'=>translate("Nov"),
			 '[Dec]'=>translate("Dec"),
			));
			
			return $date;			
		}
		return false;
	}
	
	public static function reportsRange()
	{
		return 7;
	}
	
	public static function dateRangeTrans()
	{
		return array(
		  'format'=>"MMM DD, Y",
		  'separator'=>" - ",
		  'applyLabel'=>translate("Apply"),
		  'cancelLabel'=>translate("Cancel"),
		  'fromLabel'=>translate("fromLabel"),
		  'toLabel'=>translate("To"),
		  'customRangeLabel'=>translate("Custom"),
		  'weekLabel'=>"W",
		  'daysOfWeek'=>array( translate("Su"), translate("Mo"), translate("Tu"), translate("We"), translate("Th"), translate("Fr"), translate("Sa") ),
		  'monthNames'=>array(
		    translate("January"),translate("February"),translate("March"),translate("April"),
		    translate("May"),translate("June"),translate("July"),translate("August"),translate("September"),
		    translate("October"),translate("November"),translate("December")
		  )
		);
	}
	
	public static function datetimeDefaultLanguage()
	{
		return 'en';		
	}
	
	public static function validDate($date) {
       $tempDate = explode('-', $date);  
       return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
    }
    
    public static function getOrderList($merchant_id='')
    {    	    		
    	if($merchant_id<=0){
    		$stmt="
	    	SELECT * FROM {{order_status}}
	    	WHERE merchant_id IN ('0')
	    	ORDER BY description ASC
	    	";    	
    	} else {
	    	$stmt="
	    	SELECT * FROM {{order_status}}
	    	WHERE merchant_id IN ('0',".FunctionsV3::q($merchant_id).")
	    	ORDER BY description ASC
	    	";    	
    	}    	    	
	    if($resp = Yii::app()->db->createCommand($stmt)->queryAll()){
	    	return $resp;
	    }
	    return false;
    }
    
    public static function includeLibraryDateRange()
    {
    	InventoryWrapper::registerJS(array(
            Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/datetimepicker/moment.js',
		    Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/daterangepicker/daterangepicker.js',
		));
		
		InventoryWrapper::registerCSS(array(
		  Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/daterangepicker/daterangepicker.css',
		));		
    }
    
    public static function getRemainingStocks($merchant_id=0, $item_id=0)
    {
    	$data = array();
    	$resp = Yii::app()->db->createCommand()
          ->select("item_id,with_size,size_id,size_name,track_stock,available_stocks")
          ->from('{{view_item_stocks}}')   
          ->where("merchant_id=:merchant_id AND item_id=:item_id",array(
             ':merchant_id'=>$merchant_id,
             ':item_id'=>$item_id
          ))           
          ->order('item_size_id ASC')
          ->queryAll();	
          
        if($resp){
        	return $resp;
        }
        return false;         	
    }       
    
    public static function hideItemOutStocks($merchant_id='')
    {
    	$hide = getOptionA('inventory_hide_out_stock');
    	if($hide!=1){    		
    		$hide = getOption($merchant_id,'inventory_hide_out_stock');
    	}
    	if($hide==1){
    		return true;
    	} else return false;    	
    }
    
    public static function allowNegativeStock($merchant_id='')
    {
    	$allow = getOptionA('inventory_allow_negative_order');
    	if($allow!=1){    		
    		$allow = getOption( (integer) $merchant_id,'inventory_allow_negative_order');
    	}
    	if($allow==1){
    		return true;
    	} else return false;    	
    }
    
    public static function getAppLanguage()
	{		
		$translation=array();
		$enabled_lang=FunctionsV3::getEnabledLanguage();		
		if(is_array($enabled_lang) && count($enabled_lang)>=1){			
			$path=Yii::getPathOfAlias('webroot')."/protected/messages";    	
    	    $res=scandir($path);
    	    if(is_array($res) && count($res)>=1){
    	    	foreach ($res as $val) {
    	    		if(in_array($val,$enabled_lang)){
    	    			$lang_path=$path."/$val/inventory.php";       	    			
    	    			if (file_exists($lang_path)){       	    						
    	    				$temp_lang='';
		    				$temp_lang=require $lang_path;		    				
		    				if(is_array($temp_lang) && count($temp_lang)>=1){				
			    				foreach ($temp_lang as $key=>$val_lang) {
			    					$translation[$key][$val]=$val_lang;
			    				}
		    				}
    	    			}
    	    		}
    	    	}
    	    }    	     	    
		}		
		return $translation;
	}			
	    
}
/*END CLASS*/