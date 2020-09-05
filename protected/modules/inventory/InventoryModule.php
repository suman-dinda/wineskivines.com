<?php
/* ********************************************************
 *   Karenderia Inventory Addon
 *
 *   Last Update : April 6, 2020 - 1.0 initial release
 *
 *
***********************************************************/

define("APP_FOLDER",'inventory');
define("APP_BTN",'btn-info');
define("APP_VERSION","1.0");

class InventoryModule extends CWebModule
{
	public $defaultController='home';	
	static $global_dict;
	 
	public function init()
	{		
		
		$session = Yii::app()->session;
				
		$this->setImport(array(			
			'inventory.components.*',
			'inventory.models.*',
			'application.components.*',
		));			
		require_once 'Functions.php';
		
		$website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		
	    if (!empty($website_timezone)){
	 	   Yii::app()->timeZone=$website_timezone;
	    }		 
	    		
		$ajaxurl=Yii::app()->baseUrl.'/'.APP_FOLDER;
				
		$cs = Yii::app()->getClientScript();  
		
		$lang=Yii::app()->language;				
		$file_limit = FunctionsV3::imageLimitSize();
		$cs = Yii::app()->getClientScript();
		
		$notify_delay=5;
		
		$dict = InventoryWrapper::getAppLanguage();		
		self::$global_dict = $dict;				
		$dict=json_encode($dict);
		
		$csrfTokenName = Yii::app()->request->csrfTokenName;
        $csrfToken = Yii::app()->request->csrfToken;  
        
        $error_ajax_message = translate("an error has occured");
				
        /*DEFINE DEFAULT SETTINGS*/
        InventoryWrapper::registerScript(array(
		 "var notify_delay='".CJavaScript::quote($notify_delay)."';",
		 "var controller='".CJavaScript::quote('')."';",
		 "var file_limit='".CJavaScript::quote($file_limit)."';",
		 "var page_length='".CJavaScript::quote(10)."';",
		 "var error_ajax_message='".CJavaScript::quote($error_ajax_message)."';",
		 "var dict='".CJavaScript::quote($dict)."';",
		 "var ajaxurl='".CJavaScript::quote($ajaxurl)."';",
		 "var $csrfTokenName='".CJavaScript::quote($csrfToken)."';",
		),'module_variable');
		
						
		/*JS FILE*/				
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/js/jquery-v3.4.1.js',
		CClientScript::POS_END
		);						
						
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/popper.min.js',
		CClientScript::POS_END
		);
										
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/jquery.webui-popover.min.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/datatables/datatables.min.js',
		CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/js/jquery.translate.js',
			CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/loader/jquery.loading.min.js',
			CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/notify/bootstrap-notify.min.js',
			CClientScript::POS_END
		);
			
								
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/jquery.validate.min.js',
			CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
	       Yii::app()->baseUrl."/".APP_FOLDER.'/Datablelocalize/validation',
			CClientScript::POS_END
		);
								
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/bootstrap-material-design/js/bootstrap-material-design.min.js',
			CClientScript::POS_END
		);
				
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/jquery-confirm/jquery-confirm.min.js',
			CClientScript::POS_END
		);
		
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/dropzone.js',
			CClientScript::POS_END
		);
				
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/jquery.sidebar.min.js',
			CClientScript::POS_END
		);
				
		Yii::app()->clientScript->registerScriptFile(
	        Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/select2/select2.min.js',
			CClientScript::POS_END
		);													
		/*END JS FILE*/
				
		/*CSS FILE*/
		$baseUrl = Yii::app()->baseUrl."/protected/modules/".APP_FOLDER;
		$cs = Yii::app()->getClientScript();		
						
		$cs->registerCssFile($baseUrl."/assets/vendor/jquery.webui-popover.min.css");		
		$cs->registerCssFile($baseUrl."/assets/vendor/fontawesome/css/all.min.css");
			
		$cs->registerCssFile($baseUrl."/assets/vendor/datatables/datatables.min.css");
		
		$cs->registerCssFile($baseUrl."/assets/css/animate.min.css");
		$cs->registerCssFile($baseUrl."/assets/vendor/loader/jquery.loading.min.css");		
		
		$cs->registerCssFile('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons|Oswald:200,300,400,500,600,700|Open+Sans:300,300i,400,400i,600,600i,700');			
		
		$cs->registerCssFile($baseUrl."/assets/vendor/bootstrap-material-design/css/bootstrap-material-design.min.css");
						
		$cs->registerCssFile($baseUrl."/assets/vendor/dropzone.css");		
		$cs->registerCssFile($baseUrl."/assets/vendor/jquery-confirm/jquery-confirm.min.css");
						
		$cs->registerCssFile($baseUrl."/assets/vendor/select2/select2.min.css");				
		
		$cs->registerCssFile($baseUrl."/assets/css/app.css?ver=1.0");
		$cs->registerCssFile($baseUrl."/assets/css/responsive.css?ver=1.0");
	}

	public function beforeControllerAction($controller, $action)
	{				
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here									
			return true;
		}
		else
			return false;
	}
}

function translate($words='', $params=array())
{
	return Yii::t("inventory",$words,$params);
}