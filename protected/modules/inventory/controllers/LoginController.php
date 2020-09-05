<?php
class LoginController extends CController
{
	public $layout='login_layout';	
		
	public function init()
	{
		InventoryWrapper::setLanguage();
		InventoryWrapper::includeMerchantJS();		
	}
	
	public function beforeAction($action)
	{						
		$inventory_install_steps = getOptionA('inventory_install_steps');			
		if($inventory_install_steps<=3){
			$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/install'));
			Yii::app()->end();			
			return false;
		}
		
		$action_name = $action->id ;
		$this->pageTitle=InventoryWrapper::getPageTitle($action_name);
		
		if(UserWrapper::validToken()){
			$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/index/dashboard'));
			Yii::app()->end();			
		}		
		
		$action_name = $action->id;
		$ajax_action = "Ajaxlogin/verify";
		$cs = Yii::app()->getClientScript();  
		$cs->registerScript(
		  'ajax_action',
		  "var ajax_action='$ajax_action';"
		  ,CClientScript::POS_HEAD
		);				
		
		return true;
	}		
	
	public function actionIndex(){
		
		$id = 1;						
		$this->render("login");
	}		
	
	
}
/*END CLASS*/