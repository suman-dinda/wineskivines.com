<?php
class InstallController extends CController
{
	public $layout='layout_install';		
	public $access_actions;	
	public $ajax_controller='ajaxinstall';		
	
	public function init()
	{
		/*
		variable to check during installations
		inventory_install_steps
		*/
	}
	
	public function beforeAction($action)
	{
		
		InventoryWrapper::setLanguage();
		
		$inventory_install_steps = getOptionA('inventory_install_steps');
		if($inventory_install_steps>=4){
			return false;
		}
		
		InventoryWrapper::registerJS(array(
		  Yii::app()->baseUrl . '/protected/modules/'.APP_FOLDER.'/assets/js/install.js'
		));
		
		$cs = Yii::app()->getClientScript();  
		
		$action_name = $action->id ;				
		$ajax_action = $this->ajax_controller."/$action_name";
		$cs->registerScript(
		  'ajax_action',
		  "var ajax_action='$ajax_action';"
		  ,CClientScript::POS_HEAD
		);						
		return true;
	}
	
	public function actionIndex()
	{
		$this->render('step1',array(
		  'step'=>1,
		  'data'=>InstallWrapper::requiredTable()
		));
	}
	
	public function actionStep1()
	{
		$this->actionIndex();
	}
	
	public function actionStep2()
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
		));
		$this->render('step2',array(
		  'step'=>2,		
		  'total_table'=>$total_table
		));
	}
	
	public function actionStep3()
	{
		$current_steps = (integer) getOptionA('inventory_install_steps');		
		if($current_steps<=1){
		   $this->redirect(Yii::app()->createUrl(APP_FOLDER.'/install/step2'));
		   Yii::app()->end();			
		}
		
		$total_table = InstallWrapper::getItemsCountMigrate();		
		
		InventoryWrapper::registerScript(array(
		  "var total_table=$total_table;",	
		));
		$this->render('step3',array(
		  'step'=>3,		
		  'total_table'=>$total_table
		));
	}
	
	public function actionStep4()
	{
		$current_steps = (integer) getOptionA('inventory_install_steps');				
		if($current_steps>=3){			
		   Yii::app()->functions->updateOptionAdmin('inventory_install_steps',4);		
		   $this->render('step4',array(
		    'step'=>4,				
		   ));
		} else {
			$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/install/step3'));
		    Yii::app()->end();			
		}			
	}

} /*end class*/
    