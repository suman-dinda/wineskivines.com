<?php
class IndexController extends CController
{
	public $layout='layout';		
	public $access_actions;	
	public $ajax_controller='Ajaxuser';		
	
	public function init()
	{
		$inventory_install_steps = getOptionA('inventory_install_steps');			
		if($inventory_install_steps<=3){
			$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/install'));
			Yii::app()->end();			
			return false;
		}				
		
		$user_type = UserWrapper::getUserType();
		if($user_type=="admin"){
		   InventoryWrapper::includeAdminJS();
		} else {
		   InventoryWrapper::includeMerchantJS();
		   
		   InventoryWrapper::registerScript(array(		 
			"var check_heart_beat=1;",	
		   ),'heart_beat');		
		}
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
                'actions'=> array('dashboard','deny','logout') ,                
            )
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
		
		if(!UserWrapper::validToken()){
			$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/login'));
			Yii::app()->end();			
		}
				
		$action_name = $action->id ;
		$this->pageTitle=InventoryWrapper::getPageTitle($action_name);
		
		$cs = Yii::app()->getClientScript();  
		$cs->registerScript(
		  'controller',
		  "var controller='index';",
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
	
	public function actionIndex(){
		$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/index/dashboard'));
	}		
	
	public function actionDashboard()
	{				
		$user_type = UserWrapper::getUserType();
				
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
	        
        if($user_type=="admin"):
           InventoryWrapper::registerScript(array(		 
			 "var ajax_charts='/Ajaxadmin/sales_summary_chart';",	
			 "var chart_type_series='area';",	
			 "var chart_type_options='".CJavaScript::quote($chart_options)."';"
			));		
        else :
	        InventoryWrapper::registerScript(array(		 
			 "var ajax_charts='/Ajaxreports/sales_summary_chart';",	
			 "var chart_type_series='area';",	
			 "var chart_type_options='".CJavaScript::quote($chart_options)."';"
			));		
		endif;			
		
		$days = InventoryWrapper::reportsRange();		
		$start_date = date("Y-m-d", strtotime("-$days days"));
		$end_end  = date("Y-m-d");		
				
		$default_status = ReportsWrapper::getDefaultStatus();	  
        			
		if($user_type=="admin"){
			$this->render('admin_dashboard',array(
			  'start_date'=>$start_date,
		      'end_end'=>$end_end,
		      'default_status'=>$default_status
			));
		} else {
			$this->render('dashboard',array(
			  'start_date'=>$start_date,
		      'end_end'=>$end_end,
		      'default_status'=>$default_status
			));	
		}
	}
	
	public function actionDeny()
	{
		$this->render('deny');
	}
	
	public function actionLogout()
	{
		$lang  = Yii::app()->language;		
		UserWrapper::logout();
		$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/login',array(
		 'lang'=>$lang
		)));
	}
	
	public function actionPagenotfound()
	{
		$this->render('404');
	}
	
	public function missingAction($action)
	{
		$this->pageTitle='';
		$this->render('404');
	}
	
	public function actionProfile()
	{			
		try {
			
			$user_type = UserWrapper::getUserType();
			$merchant_id = UserWrapper::getMerchantIDByAccesToken();			
			$token = UserWrapper::getToken();			
			$data = array();
			
			if($user_type=="admin"){				
				$data = ItemWrap::getData("user_master_list","session_token=:session_token and user_type=:user_type ",array(				 
				 ':session_token'=>$token,		
				 ':user_type'=>'user_type',				 
				));	
			} else {
			    $data = ItemWrap::getData("user_master_list","merchant_id=:merchant_id AND session_token=:session_token",array(				 
				 ':merchant_id'=>$merchant_id,
				 ':session_token'=>$token,				 
				));					
			}		
			
			unset($data['password']);
			unset($data['session_token']);
			unset($data['status']);
								
	        $this->render('profile',array(
	          'data'=>$data
	        ));
	        
	    } catch (Exception $e) {
			$back_url = 'javascript:window.history.back();';
		    $error = translate($e->getMessage());
		    $this->render(APP_FOLDER.'.views.item.error',array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
		
}
/*END CLASS*/