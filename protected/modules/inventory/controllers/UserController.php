<?php
class UserController extends CController
{
	public $layout='layout';
	public $ajax_controller='Ajaxuser';		
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
    	array_push($this->access_actions, "profile");    	
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
		
	public function actionAccess_rights()
	{
		$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/index/deny'));
		Yii::app()->end();			
		
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
		  'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/user/create_role')	  
		));
	}
	
	public function actionCreate_role()
	{
		$data = array();
		
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
	
	public function actionUserlist()
	{
		$data_columns[] = array('data'=>"id");		
		$data_columns[] = array('data'=>"username");		
		$data_columns[] = array('data'=>"email_address");
		$data_columns[] = array('data'=>"contact_number");
		$data_columns[] = array('data'=>"role_id");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_user";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		
		$this->render('user_list',array(	
		  'add_label'=>translate("ADD USER"),
		  'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/user/add_user')	  
		));
	}
	
	public function actionAdd_user()
	{
		$data = array(); $row_id=0;
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		try {
								
			if(isset($_GET['id'])){
				
				
				$row_id = (integer)$_GET['id'];
				$user_type = isset($_GET['user_type'])?$_GET['user_type']:'';
				$data = ItemWrap::getData("user_master_list","merchant_id=:merchant_id AND id=:id AND user_type=:user_type",array(				 
				 ':merchant_id'=>$merchant_id,
				 ':id'=>$row_id,
				 ':user_type'=>$user_type,
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_user";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",
				  "var user_type='".CJavaScript::quote($data['user_type'])."';",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));								
			} else {
				InventoryWrapper::registerScript(array(				  
				  "var user_type='".CJavaScript::quote('merchant_user')."';",				  
				));								
			}
			
			$role =   (array)InventoryWrapper::dropdownFormat(
			   UserWrapper::getAccessRole(),'role_id','role_name',
			   array(
			    '0'=>translate("Select role")
			   )
			);		
			
			$this->render('add_user',array(			 
			 'data'=>Yii::app()->request->stripSlashes($data),	
			 'role'=>$role
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
	
}/* end class*/