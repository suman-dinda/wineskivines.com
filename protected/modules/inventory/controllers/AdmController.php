<?php
class AdmController extends CController
{
	public $layout='layout';
	public $ajax_controller='Ajaxadmin';		
	public $access_actions;	
	
	public function init()
	{
		InventoryWrapper::includeAdminJS();
	}
	
	public function filters()
    {    	
        return array(
            'accessControl',
        );
    }
    
    public function accessRules()
    {
    	$actions = array(
    	  'general','databaseupdate','cronjobs',
    	 'settings_reports','merchant_list','access_rights',
    	 'create_role','low_stock_logs','update_data' ,'table_update','fixedreport'
    	);
    	
        return array(           
            array('allow',                
                'actions'=> $actions,
                'expression' => array('UserWrapper','AllowAccessAdmin'),
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
		$this->pageTitle=InventoryWrapper::getPageTitle("admin_$action_name");
		
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
		$this->render('general_settings');
	}
	
	public function actionDatabaseupdate()
	{		
		InventoryWrapper::registerScript(array(
		 "var controller='settings';",		 
		));
		
		$this->render('db_update',array(
		  'link'=>websiteUrl()."/".APP_FOLDER."/adm/table_update",
		  'notes'=>""
		));
	}
	
	public function actionCronjobs()
	{		
		InventoryWrapper::registerScript(array(
		 "var controller='settings';",		 
		));
				
		$crons[]=array(
		  'link'=>websiteUrl()."/".APP_FOLDER."/cron/low_stock",
		  'notes'=>translate("this cron should be run every 10 minutes")
		);
						
		$this->render('cronjobs',array(
		  'crons'=>$crons
		));
	}
	
	public function actionSettings_reports()
	{
		
		InventoryWrapper::registerScript(array(
		 "var controller='settings';",		 
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
	    $order_status = InventoryWrapper::dropdownFormat(InventoryWrapper::getOrderList(0),'description','description');
	    unset($order_status['']);
	    	    
	    $stats1 = getOptionA('inventory_reports_default_status');
	    if(!empty($stats1)){
	    	$stats1 = json_decode($stats1,true);
	    }	    	   
	    $stats2 = getOptionA('inventory_accepted_order_status');
	    if(!empty($stats1)){
	    	$stats2 = json_decode($stats2,true);
	    }	    	   
	    $stats3 = getOptionA('inventory_cancel_order_status');
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
	
	public function actionMerchant_list()
	{
		$data_columns[] = array('data'=>"restaurant_name");		
		$data_columns[] = array('data'=>"merchant_id");	
		$data_columns[] = array('data'=>"status");
		$data_columns[] = array('data'=>"inventory_enabled");
		$data_columns[] = array('data'=>"inventory_role_id");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_merchant";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		$this->render('merchant_list');
	}
	
	public function actionAccess_rights()
	{
		$data_columns[] = array('data'=>"role_id");		
		$data_columns[] = array('data'=>"role_name");
		$data_columns[] = array('data'=>"access");
		$data_columns[] = array('data'=>"user_count");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_access_role";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		
		$this->render('access_rights',array(	
		  'add_label'=>translate("ADD ROLE"),
		  'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/adm/create_role')	  
		));
	}
	
	public function actionCreate_role()
	{
		$data = array();
		
		InventoryWrapper::registerScript(array(
		 "var controller='access_right';",		 
		));
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("inventory_access_role","role_id=:role_id",array(				 
				 ':role_id'=>$row_id
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_access_role";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));
			}
		
			
			$this->render('create_role',array(			 
			 'data'=>Yii::app()->request->stripSlashes($data),	
			 'menu'=>MenuWrapper::menu()
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/user/access_rights');
		    $error = translate($e->getMessage());
		    $this->render(APP_FOLDER.'.views.item.error',array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}

	public function actionLow_stock_logs()
	{
		$data_columns[] = array('data'=>"id");
		$data_columns[] = array('data'=>"merchant");		
		$data_columns[] = array('data'=>"item_name");
		$data_columns[] = array('data'=>"available_stocks");
		$data_columns[] = array('data'=>"date_process");
		$data_columns = json_encode($data_columns);		
				

		$ajax_delete = $this->ajax_controller."/delete_lowstock_logs";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"		
		));
		
		$this->render('low_stock_logs');
	}
	
	public function actionUpdate_data()
	{
		$total_item = InstallWrapper::getItemsCountMigrate();		
		
		InventoryWrapper::registerScript(array(
		  "var total_item=$total_item;",	
		  "var inline_loader=1;",	
		));
		$this->render('update_data',array(
		  'total_item'=>$total_item
		));
	}
	
	public function actionTable_update()
	{
		require_once 'inv_structure.php';
		
		$current_steps = (integer) getOptionA('inventory_install_steps');
		if($current_steps<=0){
		   $this->redirect(Yii::app()->createUrl(APP_FOLDER.'/install/step1'));
		   Yii::app()->end();			
		}
		
		$total_table = count($tables);
				
		InventoryWrapper::registerScript(array(
		  "var total_table=$total_table;",	
		  "var inline_loader=1;",	
		));
		$this->render('table_update',array(
		  'step'=>2,		
		  'total_table'=>$total_table
		));
	}
	
	public function actionFixedreport()
	{
		$total_item = ReportsWrapper::totalFixedReport();
				
		InventoryWrapper::registerScript(array(
		  "var total_item=$total_item;",	
		  "var inline_loader=1;",	
		));
		$this->render('fixed_report',array(
		  'total_item'=>$total_item
		));
	}
	
} /*end class*/