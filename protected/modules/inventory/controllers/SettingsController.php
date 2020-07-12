<?php
class SettingsController extends CController
{
	public $layout='layout';
	public $ajax_controller='Ajaxsettings';		
	public $access_actions;	
	
	public function init()
	{
		InventoryWrapper::includeMerchantJS();
		
		InventoryWrapper::registerScript(array(		 
			"var check_heart_beat=1;",	
		),'heart_beat');
	}
	
	public function filters()
    {    	
        return array(
            'accessControl',
        );
    }
    
    public function accessRules()
    {
    	$this->access_actions = UserWrapper::getAcessRules();    	    	
    	array_push($this->access_actions, "databaseupdate", "cronjobs","settings_reports"); 
        return array(           
            array('allow',                
                'actions'=> $this->access_actions,
                'expression' => array('UserWrapper','AllowAccess'),
            ),            
            array('deny', 
                'users' => array('*'),
                'deniedCallback' =>  array($this, 'redirectlogin')
            ),
        );
    }
    
    public function redirectlogin()
    {
    	$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/index/deny'));
		Yii::app()->end();			
    }
        	
    
	public function beforeAction($action)
	{						
		InventoryWrapper::setLanguage();
			
		$action_name = $action->id;		
		$this->pageTitle=InventoryWrapper::getPageTitle("reports_$action_name");
		
		$cs = Yii::app()->getClientScript();  
		$cs->registerScript(
		  'controller',
		  "var controller='".CJavaScript::quote(Yii::app()->controller->id)."';",
		  CClientScript::POS_HEAD
		);				
				
		$ajax_action = $this->ajax_controller."/$action_name";
		$cs->registerScript(
		  'ajax_action',
		  "var ajax_action='$ajax_action';"
		  ,CClientScript::POS_HEAD
		);				
		return true;
	}
	
	public function missingAction($action)
	{
		$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/index/pagenotfound'));
		Yii::app()->end();
	}
	
	public function actionGeneral()
	{
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();	
		$this->render('general_settings',array(
		 'merchant_id'=>$merchant_id,
		 'hide_out_stock'=>getOptionA('inventory_hide_out_stock'),
		 'allow_negative_order'=>getOptionA('inventory_allow_negative_order'),
		));
	}
	
	public function actionDatabaseupdate()
	{
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();	
		$this->render('db_update',array(
		 'merchant_id'=>$merchant_id
		));
	}
	
	public function actionCronjobs()
	{
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();	
		$this->render('cronjobs',array(
		 'merchant_id'=>$merchant_id
		));
	}
	
	public function actionSettings_reports()
	{
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
	    $order_status = InventoryWrapper::dropdownFormat(InventoryWrapper::getOrderList($merchant_id),'description','description');
	    unset($order_status['']);
	    	    
	    $stats1 = getOption($merchant_id,'inventory_reports_default_status');
	    if(!empty($stats1)){
	    	$stats1 = json_decode($stats1,true);
	    }	    	   
	    $stats2 = getOption($merchant_id,'inventory_accepted_order_status');
	    if(!empty($stats1)){
	    	$stats2 = json_decode($stats2,true);
	    }	    	   
	    $stats3 = getOption($merchant_id,'inventory_cancel_order_status');
	    if(!empty($stats1)){
	    	$stats3 = json_decode($stats3,true);
	    }	    	   
	    
		$this->render('settings_reports',array(
		 'merchant_id'=>$merchant_id,
		 'order_status'=>$order_status,
		 'stats1'=>$stats1,
		 'stats2'=>$stats2,
		 'stats3'=>$stats3,
		));
	}
	
} /*end class*/