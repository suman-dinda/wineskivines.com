<?php
class ReportsController extends CController
{
	public $layout='layout';
	public $ajax_controller='Ajaxreports';
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
    	array_push($this->access_actions,'Fixedreport');   
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
	
	public function actionsales_summary()
	{
		InventoryWrapper::includeLibraryDateRange();
		
		InventoryWrapper::registerJS(array(            
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/highcharts.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/exporting.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/export-data.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/accessibility.js',		
		));
				
		$chart_options = $this->renderPartial(APP_FOLDER.'.views.reports.charts_type1',array(
		   'data'=>ReportsWrapper::chartType(),
		   'default_value'=>"Area"
        ),true);        
		
		$data_columns[] = array('data'=>"sale_date");		
		$data_columns[] = array('data'=>"gross_sale");
		$data_columns[] = array('data'=>"discount");		
		$data_columns[] = array('data'=>"net_sale");
		$data_columns[] = array('data'=>"total_cost");		
		$data_columns[] = array('data'=>"gross_profit");		
		$data_columns = json_encode($data_columns);		
		
		$days = InventoryWrapper::reportsRange();
		$start_date = date("Y-m-d", strtotime("-$days days"));
		$end_end  = date("Y-m-d");		
		
		$date_range_trans = InventoryWrapper::dateRangeTrans();		
		
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",		 
		 'var range_day='.$days.';',
		 "var ajax_charts='/Ajaxreports/sales_summary_chart';",	
		 "var chart_type_series='area';",	
		 "var chart_type_options='".CJavaScript::quote($chart_options)."';",
		 "var date_range_trans='".CJavaScript::quote(json_encode($date_range_trans))."';"
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
	    $order_status = InventoryWrapper::dropdownFormat(InventoryWrapper::getOrderList($merchant_id),'description','description',array(
	      'all'=>translate("All status")
	    ));	    

	    $default_status = ReportsWrapper::getDefaultStatus($merchant_id);	    
	    		
		$this->render('sales_summary',array(
		  'start_date'=>$start_date,
		  'end_end'=>$end_end,
		  'order_status'=>$order_status,
		  'default_status'=>$default_status
		));
	}
	
	public function actionSales_item()
	{
		InventoryWrapper::includeLibraryDateRange();
		
		InventoryWrapper::registerJS(array(            
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/highcharts.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/exporting.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/export-data.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/accessibility.js',		
		));
		
		$data_columns[] = array('data'=>"item_name");		
		$data_columns[] = array('data'=>"categories_name");
		$data_columns[] = array('data'=>"item_sold");		
		$data_columns[] = array('data'=>"discount");
		$data_columns[] = array('data'=>"net_sale");
		$data_columns[] = array('data'=>"total_cost");		
		$data_columns[] = array('data'=>"gross_profit");		
		$data_columns = json_encode($data_columns);		
		
		$days = InventoryWrapper::reportsRange();
		$start_date = date("Y-m-d", strtotime("-$days days"));
		$end_end  = date("Y-m-d");		
		
		$chart_options = $this->renderPartial(APP_FOLDER.'.views.reports.charts_type1',array(
		   'data'=>ReportsWrapper::chartType(2),
		   'default_value'=>"Line"
        ),true);        
        
        $date_range_trans = InventoryWrapper::dateRangeTrans();
        
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",		 
		 'var range_day='.$days.';',
		 "var ajax_charts='/Ajaxreports/chart_sales_item';",	
		 "var chart_type_series='line';",		
		 "var chart_type_options='".CJavaScript::quote($chart_options)."';",
		 "var date_range_trans='".CJavaScript::quote(json_encode($date_range_trans))."';",
		 "var top_items='topItems';",		
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
	    $order_status = InventoryWrapper::dropdownFormat(InventoryWrapper::getOrderList($merchant_id),'description','description',array(
	      'all'=>translate("All status")
	    ));
	    
	    	
		$this->render('sales_item',array(
		  'start_date'=>$start_date,
		  'end_end'=>$end_end,
		  'order_status'=>$order_status,
		  'default_status'=>ReportsWrapper::getDefaultStatus($merchant_id)
		));
	}
	
	public function actionSales_category()
	{
		InventoryWrapper::includeLibraryDateRange();
				
		InventoryWrapper::registerJS(array(            
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/highcharts.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/exporting.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/export-data.js',
			Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/highcharts/code/modules/accessibility.js',			
		));
				
		$data_columns[] = array('data'=>"category_name");
		$data_columns[] = array('data'=>"item_sold");		
		$data_columns[] = array('data'=>"discount");
		$data_columns[] = array('data'=>"net_sale");
		$data_columns[] = array('data'=>"total_cost");		
		$data_columns[] = array('data'=>"gross_profit");		
		$data_columns = json_encode($data_columns);		
		
		$days = InventoryWrapper::reportsRange();
		$start_date = date("Y-m-d", strtotime("-$days days"));
		$end_end  = date("Y-m-d");		
		
		$chart_options = $this->renderPartial(APP_FOLDER.'.views.reports.charts_type1',array(
		   'data'=>ReportsWrapper::chartType(2),
		   'default_value'=>"Line"
        ),true);        
        
        $date_range_trans = InventoryWrapper::dateRangeTrans();
        
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",		 
		 'var range_day='.$days.';',
		 "var ajax_charts='/Ajaxreports/chart_sales_category';",	
		 "var chart_type_series='line';",		
		 "var chart_type_options='".CJavaScript::quote($chart_options)."';",
		 "var date_range_trans='".CJavaScript::quote(json_encode($date_range_trans))."';",
		 "var top_items='topCategory';",
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
	    $order_status = InventoryWrapper::dropdownFormat(InventoryWrapper::getOrderList($merchant_id),'description','description',array(
	      'all'=>translate("All status")
	    ));
	    		
		$this->render('sales_category',array(
		  'start_date'=>$start_date,
		  'end_end'=>$end_end,
		  'order_status'=>$order_status,
		  'default_status'=>ReportsWrapper::getDefaultStatus($merchant_id)
		));
	}
		
	public function actionSales_payment_type()
	{
		InventoryWrapper::includeLibraryDateRange();
				
		$data_columns[] = array('data'=>"payment_type");
		$data_columns[] = array('data'=>"payment_transaction");		
		$data_columns[] = array('data'=>"net_amount");		
		$data_columns = json_encode($data_columns);		
		
		$days = InventoryWrapper::reportsRange();
		$start_date = date("Y-m-d", strtotime("-$days days"));
		$end_end  = date("Y-m-d");		
		
		$date_range_trans = InventoryWrapper::dateRangeTrans();
		
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",		 
		 'var range_day='.$days.';',
		 "var data_sort_by='ASC';",
		 "var date_range_trans='".CJavaScript::quote(json_encode($date_range_trans))."';"
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
	    $order_status = InventoryWrapper::dropdownFormat(InventoryWrapper::getOrderList($merchant_id),'description','description',array(
	      'all'=>translate("All status")
	    ));
	    		
		$this->render('sales_payment_type',array(
		  'start_date'=>$start_date,
		  'end_end'=>$end_end,
		  'order_status'=>$order_status,
		  'default_status'=>ReportsWrapper::getDefaultStatus($merchant_id)
		));
	}
	
	public function actionSales_receipt()
	{
		InventoryWrapper::includeLibraryDateRange();
				
		$data_columns[] = array('data'=>"order_id");
		$data_columns[] = array('data'=>"date_created");		
		$data_columns[] = array('data'=>"trans_type");		
		$data_columns[] = array('data'=>"customer_name");
		$data_columns[] = array('data'=>"payment_type");
		$data_columns[] = array('data'=>"request_from");
		$data_columns[] = array('data'=>"status");
		$data_columns[] = array('data'=>"total_w_tax");
		$data_columns = json_encode($data_columns);		
		
		$days = InventoryWrapper::reportsRange();
		$start_date = date("Y-m-d", strtotime("-$days days"));
		$end_end  = date("Y-m-d");		
		
		$order_id = isset($_GET['id'])?$_GET['id']:'';	
		if($order_id>0){			
		   InventoryWrapper::registerScript(array(
			 "var pop_receipt='$order_id';",				
		   ),'pop_receipt');		   
		}
		
		$date_range_trans = InventoryWrapper::dateRangeTrans();
		
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",		 
		 'var range_day='.$days.';',
		 "var table_do='receipt';",
		 "var date_range_trans='".CJavaScript::quote(json_encode($date_range_trans))."';"
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
	    $order_status = InventoryWrapper::dropdownFormat(InventoryWrapper::getOrderList($merchant_id),'description','description',array(
	      'all'=>translate("All status")
	    ));
	    	    	
		$this->render('sales_receipt',array(
		  'start_date'=>$start_date,
		  'end_end'=>$end_end,
		  'order_status'=>$order_status,
		  'default_status'=>$order_status		  
		));
	}
	
	public function actionSales_by_addon()
	{
		InventoryWrapper::includeLibraryDateRange();
				
		$data_columns[] = array('data'=>"sub_item_name");
		$data_columns[] = array('data'=>"qty_sold");		
		$data_columns[] = array('data'=>"gross_sale");				
		$data_columns = json_encode($data_columns);		
		
		$days = InventoryWrapper::reportsRange();
		$start_date = date("Y-m-d", strtotime("-$days days"));
		$end_end  = date("Y-m-d");		
		
		$date_range_trans = InventoryWrapper::dateRangeTrans();
		
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",		 
		 'var range_day='.$days.';',
		 "var data_sort_by='ASC';",
		 "var date_range_trans='".CJavaScript::quote(json_encode($date_range_trans))."';"
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
	    $order_status = InventoryWrapper::dropdownFormat(InventoryWrapper::getOrderList($merchant_id),'description','description',array(
	      'all'=>translate("All status")
	    ));
	    		
		$this->render('sales_by_addon',array(
		  'start_date'=>$start_date,
		  'end_end'=>$end_end,
		  'order_status'=>$order_status,
		  'default_status'=>ReportsWrapper::getDefaultStatus($merchant_id)
		));
	}
	
	public function actionFixedreport()
	{
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		$total_item = ReportsWrapper::totalFixedReport($merchant_id);
		
		InventoryWrapper::registerScript(array(
		  "var total_item=$total_item;",	
		  "var inline_loader=1;",	
		));
		$this->render('fixed_report',array(
		  'total_item'=>$total_item
		));
	}
	
} /*end class*/