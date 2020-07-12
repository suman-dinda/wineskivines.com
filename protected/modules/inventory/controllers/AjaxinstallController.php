<?php
class AjaxinstallController extends CController
{
	public $code=2;
	public $msg;
	public $details;
	public $data;		
	
	public function __construct()
	{
		$this->data=$_POST;	
		
		FunctionsV3::handleLanguage();
	    $lang=Yii::app()->language;	    	   
	    if(isset($_GET['debug'])){
	       dump($lang);
	    }
	}
	
	public function beforeAction($action)
	{		
		$inventory_install_steps = getOptionA('inventory_install_steps');
		if($inventory_install_steps>=4){
			return false;
		}
		
		return true;
	}
	
	private function jsonResponse()
	{		      
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
	}
			
	public function OKresponse()
	{
		$this->code = 1; $this->msg = "OK"; $this->details = array();
	}
	
	public function actionIndex()
	{
		
	}
	
	public function actionStep2()
	{
		
		require_once 'inv_structure.php';
		
		$next_action = 'next_table'; 
		$message = ''; $stats=''; $table_name=''; $type = "";
		
		$counter = (integer) $this->data['counter'];
		$total_table = (integer) $this->data['total_table'] + 0;
		
		
		if( array_key_exists($counter,$tables)){			
			try {
				
				$stats='';				
				$type =  $tables[$counter]['type'];					
				$table_name =  "{{".$tables[$counter]['name']."}}";				
				$fields =  isset($tables[$counter]['fields'])?$tables[$counter]['fields']:'';
				
				
				switch ($type) {
					
					case "Insert_data":
						
						$data_insert = $tables[$counter]['data'];						
						if($tables[$counter]['name']=="inventory_access_role"){
							Yii::app()->db->createCommand("TRUNCATE TABLE $table_name")->query();							
							foreach ($data_insert as $data_insert_val) {								
								Yii::app()->db->createCommand()->insert($table_name,$data_insert_val);
							}
							$stats.= "<span class=\"text-success\">[OK]</span>";
						} elseif ( $tables[$counter]['name']=="option"){						
							foreach ($data_insert as $data_insert_val) {
								Yii::app()->functions->updateOptionAdmin($data_insert_val['option_name'], $data_insert_val['option_value']);
							}
							$stats.= "<span class=\"text-success\">[OK]</span>";
						} else {
							//
						}										
						break;
						
					case "View":
					case "view":
						
						if(Yii::app()->db->schema->getTable($table_name)){	
							if( !InstallWrapper::isTableView($table_name)){
								Yii::app()->db->createCommand()->dropTable($table_name);
							}
						}
												
						$stmt = isset($tables[$counter]['stmt'])?$tables[$counter]['stmt']:'';
						if (Yii::app()->db->createCommand($stmt)->query()){
							$stats.= "<span class=\"text-success\">[OK]</span>";
						} else $stats.= "<span class=\"text-info\">failed creating view table</span>";
						break;
						
					case "addColumn":																	
						$table_cols = Yii::app()->db->schema->getTable($table_name);
						if(is_array($fields) && count($fields)>=1){
							foreach ($fields as $key=>$val) {
								if(!isset($table_cols->columns[$key])) {							
								   Yii::app()->db->createCommand()->addColumn($table_name,$key,$val);
								   $stats.= "<span class=\"text-success\">[OK]</span>";
								   sleep(1);
								} else {
									$stats.= "<span class=\"text-info\">field $key already exist</span>";
								}							
							}
						}																						
						break;
						
					case "Query":	
					    $stmt = isset($tables[$counter]['stmt'])?$tables[$counter]['stmt']:'';
						if (Yii::app()->db->createCommand($stmt)->query()){
							$stats.= "<span class=\"text-success\">[OK]</span>";
						} else $stats.= "<span class=\"text-info\">failed Query</span>";
						break;
				
					default:						
						/*DROP TABLE*/
						if(Yii::app()->db->schema->getTable($table_name)){														
							$stats = "<span class=\"text-info\">table already exist</span>";
						} else {					
							/*CREATE TABLE*/
							Yii::app()->db->createCommand()->createTable(
							  $table_name,
							  $fields,
							'ENGINE=InnoDB DEFAULT CHARSET=utf8');
							
							/*ADD INDEX*/
							if(isset($tables[$counter]['index'])){
								foreach ($tables[$counter]['index'] as $index) {							
									Yii::app()->db->createCommand()->createIndex($index,$table_name,$index);
								}
							}		
							$stats = "<span class=\"text-success\">[OK]</span>";								
						 }									
						 						
						break;
				}/* end swicth*/
													
			
			} catch (Exception $e) {			    
			    $stats = "<span class=\"text-danger\">".$e->getMessage()."</span>";
			}										
		} else $stats = $stats = "<span class=\"text-danger\">".translate("Index not found")."</span>";


		$message = $type." ".translate("table [table] ... [stats]",array(
		 '[table]'=>$table_name,
		 '[stats]'=>$stats
		));	
		
		
		if($counter>=$total_table){
			$next_action = 'done';
		}
		
		if($next_action=="done"){			
			$next_url =  Yii::app()->createUrl(APP_FOLDER.'/install/step3');
			$message = '<a href="'.$next_url.'" class="btn btn-raised btn-primary" >'.translate("Next").'</a>';
			Yii::app()->functions->updateOptionAdmin('inventory_install_steps',2);
		}
				
		
		$this->OKresponse();
		$this->details = array(
		  'next_action'=>$next_action,
		  'counter'=>$counter+1,
		  'message'=>$message
		);
		$this->jsonResponse();
	}
	
	public function actionStep3()
	{
		$next_action = 'next_item'; 
		
		$counter = (integer) $this->data['counter'];		
		$total_table = (integer) $this->data['total_table'] + 0;
		
		$stmt=""; $stmt_item ="";  $created_at = FunctionsV3::dateNow();
		$message=''; $stats=''; $item_name=''; $merchant_id=0; $item_id=0;
				
		$stmt_cat =''; $stmt_cat_item='';
		$stmt_sku =''; $stmt_sku_item='';
		$stmt_subcat =''; $stmt_subcat_item='';
		$with_size=0;
		
		$sku = ItemWrap::autoGenerateSKU();
		
		if ( $res = InstallWrapper::getItemstoMigrate($counter)){			
			foreach ($res as $val) {
				
				/*ITEM */
				$merchant_id = $val['merchant_id'];
				$item_name = $val['item_name'];
				$item_id = $val['item_id'];
				
				$price = json_decode($val['price'],true);				
				if(is_array($price) && count($price)>=1){	
										
					foreach ($price as $price_key => $price_val) {											
						$item_token = ItemWrap::generateFoodSizeToken();
					    $stmt_item.= "(NULL,".q($val['merchant_id']).",".q($item_token).",";
					    $stmt_item.= q($val['item_id']).",". q($price_key).",".q($price_val).",".q(0).",";
					    $stmt_item.= q($sku).",".q(1).",".q(0).",".q($created_at).",".q($created_at);
					    $stmt_item.="),\n";
					    $sku++;				
					    $stmt_sku_item.="(NULL,".q($val['item_id'])."),\n";	    
					    
					    if($price_key>0){
					    	$with_size=1;
					    }
					}					
				} 
				
				
				/*ITEM CATEGORY*/				
				ItemWrap::deleteCategoryRelationship($val['merchant_id'],$val['item_id']);				
				$category  = json_decode($val['category'],true);
				if(is_array($category) && count($category)>=1){
					foreach ($category as $cat_id) {						
						$stmt_cat_item.="(NULL,".q($val['merchant_id']).",".q($val['item_id']).",".q($cat_id)."),\n";
					}
				}
				
				/*item_relationship_subcategory*/
				ItemWrap::deleteItemRelationshipSubcategory($merchant_id,$item_id);
				$addon_item = json_decode($val['addon_item'],true);
				if(is_array($addon_item) && count($addon_item)>=1){					
					foreach ($addon_item as $subcat_id=>$subcat_val) {						
						$stmt_subcat_item.="(NULL,".q($merchant_id).",".q($item_id).",".q($subcat_id)."),\n";
					}					
				}
				
				
			}/* END foreach*/

			if(!empty($stmt_item)){
				$stmt_item = substr($stmt_item,0,-2).";";
				$stmt = "INSERT INTO {{item_relationship_size}} (
				  item_size_id,
				  merchant_id,
				  item_token,
				  item_id,
				  size_id,
				  price,
				  cost_price,
				  sku,
				  available,
				  low_stock,
				  created_at,
				  updated_at			  
				)
				VALUES \n$stmt_item
				";
			}
			
			if(!empty($stmt_cat_item)){
				$stmt_cat_item = substr($stmt_cat_item,0,-2).";";
				$stmt_cat = "
				INSERT INTO {{item_relationship_category}} (
				  id,merchant_id, item_id, cat_id
				)
				VALUES \n$stmt_cat_item
				";
			}
			
			if(!empty($stmt_subcat_item)){
				$stmt_subcat_item = substr($stmt_subcat_item,0,-2).";";
				$stmt_subcat="
				INSERT INTO {{item_relationship_subcategory}} (
				  id, merchant_id, item_id, subcat_id
				)
				VALUES \n$stmt_subcat_item
				";							
			}
			
			
			if(!empty($stmt_sku_item)){
				$stmt_sku_item = substr($stmt_sku_item,0,-2).";";
				$stmt_sku="
				INSERT INTO {{item_sku}} (
				  sku_id, item_id
				)
				VALUES \n$stmt_sku_item
				";
			}
			
			
			/*SUBCATEGORY */
			if ( $resp = InstallWrapper::getSubcategorytoMigrate($merchant_id)){
				foreach ($resp as $respval) {					
					$subcategory = json_decode($respval['category'],true);					
					ItemWrap::subitemRelationship($respval['sub_item_id'],$subcategory);
				}				
			}
			
			try {
				
				
				$params = array( 
				  'item_token'=>ItemWrap::generateFoodToken(),
				  'with_size'=>$with_size,
				  'date_modified'=>FunctionsV3::dateNow()
				 );
			    Yii::app()->db->createCommand()->update("{{item}}",$params,
		      	    'item_id=:item_id AND merchant_id=:merchant_id ',
		      	    array(
		      	      ':item_id'=>$item_id,
		      	      ':merchant_id'=>$merchant_id
		      	    )
		      	);
				
		      
		      	if(!empty($stmt_item)){
				   Yii::app()->db->createCommand($stmt)->query();
		      	}
				
				if(!empty($stmt_cat_item)){
				   Yii::app()->db->createCommand($stmt_cat)->query();
				}
				
				if(!empty($stmt_subcat_item)){
				   Yii::app()->db->createCommand($stmt_subcat)->query();
				}
				
				if(!empty($stmt_sku_item)){
				  Yii::app()->db->createCommand($stmt_sku)->query();
				}
				
				InstallWrapper::updateMerchantRole($merchant_id);
				
				$stats = "<span class=\"text-success\">[OK]</span>";								
				
			} catch (Exception $e) {
	           $stats = "<span class=\"text-danger\">".$e->getMessage()."</span>";	           
	        }
	        
	        $message = translate("Updating item [[item_name]] ... [stats]",array(
	          '[item_name]'=>$item_name,
	          '[stats]'=>$stats
			));	
			
			if($counter>=$total_table){
				$next_action = 'done';
			}
			
			if($next_action=="done"){			
				$next_url =  Yii::app()->createUrl(APP_FOLDER.'/install/step4');
				$message = '<a href="'.$next_url.'" class="btn btn-raised btn-primary" >'.translate("Next").'</a>';
				Yii::app()->functions->updateOptionAdmin('inventory_install_steps',3);
			}
			
			$this->OKresponse();
			$this->details = array(
			  'next_action'=>$next_action,
			  'counter'=>$counter+1,
			  'message'=>$message
			);
			$this->jsonResponse();
			
		} else {			
			$next_url =  Yii::app()->createUrl(APP_FOLDER.'/install/step4');
			$message = '<a href="'.$next_url.'" class="btn btn-raised btn-primary" >'.translate("Next").'</a>';
			Yii::app()->functions->updateOptionAdmin('inventory_install_steps',3);
			
			$next_action = 'done';
			
			$this->OKresponse();
			$this->details = array(
			  'next_action'=>$next_action,
			  'counter'=>$counter+1,
			  'message'=>$message
			);
		}
		$this->jsonResponse();
	}
		
} /*end class*/