<?php
class StocksController extends CController
{
	public $layout='layout';
	public $ajax_controller='Ajaxstocks';	
	public $access_actions;	
	
	public function init()
	{
		InventoryWrapper::setLanguage();
		
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
       
       return array(           
            array('allow',                
                'actions'=> $this->access_actions ,
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
		$action_name = $action->id;		
		$this->pageTitle=InventoryWrapper::getPageTitle("stocks_$action_name");
		
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
	
	public function actionIndex()
	{
		$this->actionadjustment_list();
	}
	
	public function actionAdjustment_list()
	{
		$data_columns[] = array('data'=>"transaction_id");		
		$data_columns[] = array('data'=>"created_at");
		$data_columns[] = array('data'=>"transaction_type");
		$data_columns[] = array('data'=>"quantity");		
		$data_columns = json_encode($data_columns);		
				
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var render_checkbox=false;"
		));
			
		$reason = StocksWrapper::adjustmentTypeList();		
		
		$this->render('adjustment_list',array(		 
		 'add_label'=>translate("ADD STOCKS ADJUSTMENT"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/adjustment_new'),
		 'reason'=>$reason
		));
	}
	
	public function actionAdjustment_new()
	{				
       InventoryWrapper::registerJS(array(
		   Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/typeahead/jquery.typeahead.min.js',		   
		));
		
		InventoryWrapper::registerCSS(array(		  
		  Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/typeahead/jquery.typeahead.min.css',
		));
				
		$table_properties = StocksWrapper::tableProperties();
				
		InventoryWrapper::registerScript(array(
		 "var table_properties='".CJavaScript::quote(json_encode($table_properties))."';",	
		 'var track_stock_item=1;'
		));		
		
		$adjustment_type = StocksWrapper::adjustmentType();
		unset($adjustment_type['item_edit']);
		unset($adjustment_type['sale']);		
		
		$this->render('adjustment_new',array(
		 'adjustment_type'=>$adjustment_type,
		 'cancel_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/adjustment_list'),
		));
	}
	
	public function actionAdjustment_details()
	{
		try {						
			$merchant_id = UserWrapper::getMerchantIDByAccesToken();
			$transaction_id = isset($_GET['id'])?(integer)$_GET['id']:0;			
			$data = StocksWrapper::getTransactionDetails($transaction_id, $merchant_id);
			
			$this->render('adjustment_details',array(
			  'cancel_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/adjustment_list'),
			  'data'=>Yii::app()->request->stripSlashes($data),
			  'table_prop'=>StocksWrapper::tablePropertiesView()
			));		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/stocks/adjustment_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}	
	
	public function actionSupplier_list()
	{
		$data_columns[] = array('data'=>"supplier_id");		
		$data_columns[] = array('data'=>"supplier_name");		
		$data_columns[] = array('data'=>"contact_name");
		$data_columns[] = array('data'=>"phone_number");
		$data_columns[] = array('data'=>"email");		
		$data_columns = json_encode($data_columns);		
				
		$ajax_delete = $this->ajax_controller."/delete_supplier";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		$this->render('supplier_list',array(
		 'add_label'=>translate("ADD SUPPLIER"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/supplier_new')
		));
	}
	
	public function actionSupplier_new()
	{
		$data = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("inventory_supplier","merchant_id=:merchant_id AND supplier_id=:supplier_id",array(
				 ':merchant_id'=>$merchant_id,
				 ':supplier_id'=>$row_id
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_supplier";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));
			}
			
			$this->render('supplier_new',array(			 
			  'data'=>Yii::app()->request->stripSlashes($data),		
			  'cancel_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/supplier_list'),	 
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/stocks/supplier_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
		
	public function actionOrder_list()
	{
		$data_columns[] = array('data'=>"po_id");		
		$data_columns[] = array('data'=>"purchase_date");
		$data_columns[] = array('data'=>"supplier_name");
		$data_columns[] = array('data'=>"status");		
		$data_columns[] = array('data'=>"received");		
		$data_columns[] = array('data'=>"expected_on");
		$data_columns[] = array('data'=>"total");
		$data_columns = json_encode($data_columns);		
				
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var render_checkbox=false;"
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		$supplier = (array)InventoryWrapper::dropdownFormat(
		   StocksWrapper::getSupplier((integer)$merchant_id),'supplier_id','supplier_name',
		   array(
		    'all'=>translate("All supplier")
		   )
		);
		
		$this->render('order_list',array(
		  'x'=>0,
		 'supplier'=>$supplier,
		 'purchase_status'=>(array)StocksWrapper::purchaseStatus(),
		 'add_label'=>translate("ADD PURCHASE ORDER"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/purchase_new')
		));
	}
	
	public function actionPurchase_new()
	{
		InventoryWrapper::registerJS(array(
		   Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/typeahead/jquery.typeahead.min.js',
		   Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/datetimepicker/jquery.datetimepicker.full.min.js',
		   Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/datetimepicker/moment.js',
		   Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/datetimepicker/js-date-format.min.js'
		));
		
		InventoryWrapper::registerCSS(array(
		  Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/datetimepicker/jquery.datetimepicker.min.css',
		  Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/typeahead/jquery.typeahead.min.css',
		));
				
		InventoryWrapper::registerScript(array(		  
		  "var datetime_lang='".CJavaScript::quote(InventoryWrapper::datetimeDefaultLanguage())."';"
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		$data=array(); $row_id='';
		
		$table_properties = StocksWrapper::tableProperties();
		$supplier_list = (array) InventoryWrapper::dropdownFormat(StocksWrapper::getSupplier($merchant_id),'supplier_id','supplier_name');		
		
		$cancel_link = Yii::app()->createUrl(APP_FOLDER.'/stocks/order_list');
		
		if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				
				try {
				
				    $data = StocksWrapper::getPurchaseDetails($row_id, $merchant_id);				
				    
				} catch (Exception $e) {
					$back_url = Yii::app()->createUrl(APP_FOLDER.'/stocks/order_list');
				    $error = translate($e->getMessage());
				    $this->render("error",array(
				      'message'=>$error,
				      'back_url'=>$back_url
				    ));
				    return ;
				}
				
				$ajax_delete = $this->ajax_controller."/delete_purchase";
				$cancel_link = Yii::app()->createUrl(APP_FOLDER.'/stocks/purchase_view',array(
				  'id'=>$row_id
				));
										
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';",
				  "var purchase_data='".CJavaScript::quote(json_encode($data))."';",
				));
			}
				
		$this->render('purchase_new',array(		 
		 'data'=>$data,
		 'save_label'=>$row_id>0?translate("UPDATE"):translate("SAVE"),
		 'cancel_link'=>$cancel_link,
		 'supplier_list'=>$supplier_list
		));
	}
	
	public function actionPurchase_view()
	{
		$data = array(); $row_id=''; $data_receive = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		$is_editable = true;
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				
				$data = StocksWrapper::getPurchaseDetails($row_id, $merchant_id);						
				$data_receive = StocksWrapper::getPurchaseOrderView($row_id,$merchant_id);				
				
				$ajax_delete = $this->ajax_controller."/delete_purchase";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));
								
				if($data[0]['status']=="closed"){
					$is_editable=false;
				}
			}
			
			$this->render('purchase_view',array(			 
			  'data'=>Yii::app()->request->stripSlashes($data),		
			  'data_receive'=>$data_receive,
			  'is_editable'=>$is_editable,
			  'cancel_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/order_list'),	 
			  'receive_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/purchase_receive',array(
			    'id'=>$row_id
			  )),
			  'edit_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/purchase_new',array(
			    'id'=>$row_id
			  )),
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/stocks/order_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
	
	public function actionPurchase_receive()
	{
		$data = array(); 
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		$row_id = isset($_GET['id'])? (integer)$_GET['id'] : '';	
		
		try {
			
			$data = StocksWrapper::getPurchaseDetails($row_id, $merchant_id);
			
			InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				));
			
			$this->render('purchase_receive',array(
			  'data'=>$data,
			  'po_id'=>$row_id,
			  'cancel_link'=>Yii::app()->createUrl(APP_FOLDER.'/stocks/purchase_view',array( 
			    'id'=>$row_id
			  ))
			));
			
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/stocks/order_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
	
	public function actionHistory()
	{		
		
        InventoryWrapper::registerJS(array(
            Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/datetimepicker/moment.js',
            Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/daterangepicker/daterangepicker.js',		   
		));
		
		InventoryWrapper::registerCSS(array(		  
		  Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/vendor/daterangepicker/daterangepicker.css',
		));		
		
		$data_columns[] = array('data'=>"created_at");		
		$data_columns[] = array('data'=>"item_name");
		$data_columns[] = array('data'=>"added_by");
		$data_columns[] = array('data'=>"transaction_type");		
		$data_columns[] = array('data'=>"adjustment");
		$data_columns[] = array('data'=>"stock_after");
		$data_columns = json_encode($data_columns);		
		
		$days = InventoryWrapper::reportsRange();
		$start_date = date("Y-m-d", strtotime("-$days days"));
		$end_end  = date("Y-m-d");		
			
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		$date_range_trans = InventoryWrapper::dateRangeTrans();
				
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var render_checkbox=false;",
		 'var range_day='.$days.';',
		 "var date_range_trans='".CJavaScript::quote(json_encode($date_range_trans))."';"
		));
		
		$this->render('history',array(		 
		  'start_date'=>$start_date,
		  'end_end'=>$end_end,
		  'reason'=>StocksWrapper::adjustmentTypeList(),
		  'user_list'=>  InventoryWrapper::dropdownFormat( UserWrapper::getAllUserByMerchantID($merchant_id) , 'username','username',array(
		    'all'=>translate("All user")
		  ))
		));
	}
	
	public function actionValuation()
	{
		$data_columns[] = array('data'=>"item_name");		
		$data_columns[] = array('data'=>"available_stocks");
		$data_columns[] = array('data'=>"cost_price");		
		$data_columns[] = array('data'=>"inventory_value");
		$data_columns[] = array('data'=>"price");		
		$data_columns[] = array('data'=>"retail_value");
		$data_columns[] = array('data'=>"potential_profit");
		$data_columns = json_encode($data_columns);		
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
				
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var render_checkbox=false;",		 
		 "var data_sort_by='ASC';",
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		$this->render('valuation',array(
		  'data'=>StocksWrapper::getEvaluation($merchant_id)
		));
	}
		
}
/*end class*/