<?php
class ItemController extends CController
{
	public $layout='layout';
	public $ajax_controller='Ajaxitem';		
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
    	array_push($this->access_actions,'update_data');    
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
		InventoryWrapper::setLanguage();
		
		$action_name = $action->id;		
		$this->pageTitle=InventoryWrapper::getPageTitle("item_$action_name");
		
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
	
	public function actionindex()
	{
		$this->actionlist();
	}
	
	public function missingAction($action)
	{		
		$this->redirect(Yii::app()->createUrl(APP_FOLDER.'/index/pagenotfound'));
		Yii::app()->end();
	}
	
	public function actionList()
	{
		$data_columns[] = array('data'=>"item_id");
		$data_columns[] = array('data'=>"photo");
		$data_columns[] = array('data'=>"item_name");
		$data_columns[] = array('data'=>"categories_name");
		$data_columns[] = array('data'=>"price");
		$data_columns[] = array('data'=>"cost_price");
		$data_columns[] = array('data'=>"available_stocks");	
		$data_columns[] = array('data'=>"stocks_status");	
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_item";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",		 
		 "var ajax_delete='$ajax_delete';"
		));
		
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();		
		
		$category_list =   (array)InventoryWrapper::dropdownFormat(
		   ItemWrap::getCategory((integer)$merchant_id),'cat_id','category_name',
		   array(
		    'all'=>translate("All category")
		   )
		);
		
		$stock_list = array(   
		  'all'=>translate("All items"),
		  'low_stock'=>translate("Low Stock"),
		  'out_stock'=>translate("Out of Stock"),
		);				
		
		$this->render('item_list',array(
		 'add_label'=>translate("ADD ITEM"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/item/item_new'),
		 'category_list'=>$category_list,
		 'stock_list'=>$stock_list
		));
	}
	
	public function actionNew()
	{		
		$this->render('item_new');
	}
	
	public function actionCategory_list()
	{
		$data_columns[] = array('data'=>"cat_id");
		$data_columns[] = array('data'=>"photo");
		$data_columns[] = array('data'=>"category_name");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_category";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		$this->render('category_list');
	}
	
	public function actionCategory_add()
	{			
		$data = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();				
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("category","merchant_id=:merchant_id AND cat_id=:cat_id",array(
				 ':merchant_id'=>$merchant_id,
				 ':cat_id'=>$row_id
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_category";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",
				  "var uploaded_filename='".CJavaScript::quote($data['photo'])."';",
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));
			}				
			$this->render('category_add',array(
			 'dish'=>ItemWrap::getDish(),
			 'data'=>Yii::app()->request->stripSlashes($data),			 
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/item/category_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
	
	public function actionAddon_category_list()
	{
		$data_columns[] = array('data'=>"subcat_id");		
		$data_columns[] = array('data'=>"subcategory_name");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_addon_category";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		$this->render('addon_category_list',array(
		 'add_label'=>translate("ADDON CATEGORY"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/item/addon_category_add')
		));
	}
	
	public function actionAddon_category_add()
	{
		$data = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("subcategory","merchant_id=:merchant_id AND subcat_id=:subcat_id",array(
				 ':merchant_id'=>$merchant_id,
				 ':subcat_id'=>$row_id
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_addon_category";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));
			}
			
			$this->render('addon_category_add',array(			 
			 'data'=>Yii::app()->request->stripSlashes($data),			 
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/item/addon_category_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
	
	public function actionSize_list()
	{
		$data_columns[] = array('data'=>"size_id");		
		$data_columns[] = array('data'=>"size_name");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_size";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		$this->render('size_list',array(
		 'add_label'=>translate("ADD SIZE"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/item/size_add')
		));
	}
	
	public function actionSize_add()
	{
		$data = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("size","merchant_id=:merchant_id AND size_id=:size_id",array(
				 ':merchant_id'=>$merchant_id,
				 ':size_id'=>$row_id
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_size";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));
			}
			
			$this->render('size_add',array(			 
			 'data'=>Yii::app()->request->stripSlashes($data),			 
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/item/size_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
			
	public function actionIngredients_list()
	{
		$data_columns[] = array('data'=>"ingredients_id");		
		$data_columns[] = array('data'=>"ingredients_name");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_ingredients";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		$this->render('ingredients_list',array(
		 'add_label'=>translate("INGREDIENTS"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/item/ingredients_add')
		));
	}
	
	public function actionIngredients_add()
	{
		$data = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("ingredients","merchant_id=:merchant_id AND ingredients_id=:ingredients_id",array(
				 ':merchant_id'=>$merchant_id,
				 ':ingredients_id'=>$row_id
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_ingredients";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));
			}
			
			$this->render('ingredients_add',array(			 
			 'data'=>Yii::app()->request->stripSlashes($data),			 
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/item/ingredients_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
	
	public function actionCooking_list()
	{
		$data_columns[] = array('data'=>"cook_id");		
		$data_columns[] = array('data'=>"cooking_name");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_cooking";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		$this->render('cooking_list',array(
		 'add_label'=>translate("COOKING REF"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/item/cooking_add')
		));
	}
	
	public function actionCooking_add()
	{
		$data = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("cooking_ref","merchant_id=:merchant_id AND cook_id=:cook_id",array(
				 ':merchant_id'=>$merchant_id,
				 ':cook_id'=>$row_id
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_cooking";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';"
				));
			}
			
			$this->render('cooking_add',array(			 
			 'data'=>Yii::app()->request->stripSlashes($data),			 
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/item/cooking_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
	
	public function actionAddon_item_list()
	{
		$data_columns[] = array('data'=>"sub_item_id");		
		$data_columns[] = array('data'=>"photo");	
		$data_columns[] = array('data'=>"sub_item_name");
		$data_columns[] = array('data'=>"category");
		$data_columns = json_encode($data_columns);		

		$ajax_delete = $this->ajax_controller."/delete_addon";
		InventoryWrapper::registerScript(array(
		 "var data_columns=$data_columns;",
		 "var ajax_delete='$ajax_delete';"
		));
		
		$this->render('addon_item_list',array(
		 'add_label'=>translate("ADDON ITEM"),
		 'add_link'=>Yii::app()->createUrl(APP_FOLDER.'/item/addon_add')
		));
	}

	public function actionAddon_add()
	{
		$data = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("subcategory_item","merchant_id=:merchant_id AND sub_item_id=:sub_item_id",array(
				 ':merchant_id'=>$merchant_id,
				 ':sub_item_id'=>$row_id
				));			
				
				$ajax_delete = $this->ajax_controller."/delete_addon";
				
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';",
				  "var uploaded_filename='".CJavaScript::quote($data['photo'])."';",
				));
			}
			
			$this->render('addon_add',array(			 
			 'category'=>ItemWrap::getAddonCategory($merchant_id),
			 'data'=>Yii::app()->request->stripSlashes($data),			 
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/item/addon_item_list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
	
	public function actionItem_new()
	{		
		$data = array();
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		
		try {
								
			if(isset($_GET['id'])){
				$row_id = (integer)$_GET['id'];		
				$data = ItemWrap::getData("item","merchant_id=:merchant_id AND item_id=:item_id",array(
				 ':merchant_id'=>$merchant_id,
				 ':item_id'=>$row_id
				));			
								
				$ajax_delete = $this->ajax_controller."/delete_item";
				
				$gallery_photo = !empty($data['gallery_photo'])?json_decode($data['gallery_photo'],true):array();				
				 
				InventoryWrapper::registerScript(array(
				  "var row_id=".CJavaScript::quote($row_id).";",				  
				  "var ajax_delete='".CJavaScript::quote($ajax_delete)."';",
				  "var uploaded_filename='".CJavaScript::quote($data['photo'])."';",
				  "var gallery_photo='".CJavaScript::quote( json_encode($gallery_photo) )."';",
				));
			}
			
			
			$addon_category = (array)ItemWrap::dropdownFormat(ItemWrap::getAddonCategory($merchant_id),'subcat_id','subcategory_name');			
						
			
			$supplier_list = (array)ItemWrap::dropdownFormat(StocksWrapper::getSupplier($merchant_id),'supplier_id','supplier_name');			
			
			$this->render('item_add',array(			 
			 'data'=>Yii::app()->request->stripSlashes($data),		
			 'category'=>ItemWrap::getCategory($merchant_id),
			 'cooking'=>ItemWrap::getCookingRef($merchant_id),
			 'ingredients'=>ItemWrap::getIngredients($merchant_id),
			 'dish'=>ItemWrap::getDish(),
			 'sizes'=>(array)ItemWrap::dropdownFormat((array)ItemWrap::getSizes($merchant_id),'size_id','size_name'),
			 'addon_category'=>$addon_category,	
			 'supplier_list'=>$supplier_list
			));
		
		} catch (Exception $e) {
			$back_url = Yii::app()->createUrl(APP_FOLDER.'/item/list');
		    $error = translate($e->getMessage());
		    $this->render("error",array(
		      'message'=>$error,
		      'back_url'=>$back_url
		    ));
		}
	}
	
	public function actionUpdate_data()
	{
		$merchant_id = UserWrapper::getMerchantIDByAccesToken();
		$total_item = InstallWrapper::getItemsCountMigrate($merchant_id);		
		
		InventoryWrapper::registerScript(array(
		  "var total_item=$total_item;",	
		  "var inline_loader=1;",	
		));
		$this->render('update_data',array(
		  'total_item'=>$total_item
		));
	}
	
}
/*end class*/