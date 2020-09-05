<?php
class AjaxitemController extends CController
{
	public $code=2;
	public $msg;
	public $details;
	public $data;	
	public $merchant_id;
	
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
		if(!UserWrapper::validToken()){
			return false;
		}		
		$this->merchant_id = UserWrapper::getMerchantIDByAccesToken();			
		return true;
	}
	
	private function jsonResponse()
	{		      
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
	}
	
	private function otableNodata()
	{
		if (isset($_POST['draw'])){
			$feed_data['draw']=$_POST['draw'];
		} else $feed_data['draw']=1;	   
		     
        $feed_data['recordsTotal']=0;
        $feed_data['recordsFiltered']=0;
        $feed_data['data']=array();		
        echo json_encode($feed_data);
    	die();
	}

	private function otableOutput($feed_data='')
	{
	  echo json_encode($feed_data);
	  die();
    }
    
	
	public function OKresponse()
	{
		$this->code = 1; $this->msg = "OK"; $this->details = array();
	}
	
	public function actionCategory_add()
	{				
		$params = array(
		  'merchant_id'=>UserWrapper::getMerchantIDByAccesToken(),
		  'category_name'=>isset($this->data['category_name'])?$this->data['category_name']:'',
		  'category_description'=>isset($this->data['category_description'])?$this->data['category_description']:'',
		  'sequence'=>isset($this->data['sequence'])?$this->data['sequence']:'0',
		  'status'=>isset($this->data['status'])?$this->data['status']:'pending',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		);
		
		if(isset($this->data['file_name'])){
			$params['photo'] = trim($this->data['file_name']);
		} else $params['photo']='';
		
		if(isset($this->data['dish'])){
			$params['dish'] = json_encode($this->data['dish']);
		} else $params['dish'] = '';
		
		$id = 0;
		$id = isset($this->data['row_id'])?$this->data['row_id']:0;
		
		if($params['sequence']<=0 || !is_numeric($params['sequence'])){
			$params['sequence']=ItemWrap::getMaxSequence('category', $this->merchant_id);
		}
		
		if(isset($this->data['category_name_trans'])){
			if (ItemWrap::okToDecode()){
				$params['category_name_trans']=json_encode($this->data['category_name_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['category_name_trans']=json_encode($this->data['category_name_trans']);				
		}
		
		if (isset($this->data['category_description_trans'])){
			if (ItemWrap::okToDecode()){
				$params['category_description_trans']=json_encode($this->data['category_description_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['category_description_trans']=json_encode($this->data['category_description_trans']);
		}
				
		try {

			$params = InventoryWrapper::purifyData($params);			
					
			ItemWrap::insertCategory($params['merchant_id'],$params,(integer)$id);
			$this->OKresponse();
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/item/category_list')
			);			
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
				
		$this->jsonResponse();
	}
	
	public function actionCategory_list()
	{
				
    	$feed_data = array();
    	
    	$cols = array('cat_id','photo','category_name');
    	$resp = DatatablesWrapper::format($cols,$this->data);
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
				
        $search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( a.category_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.cat_id, a.category_name, a.photo,
		(
		 select count(*) from {{item_relationship_category}}
		 where cat_id = a.cat_id
		) as items
		FROM {{category}} a
		WHERE 1		
		$and
		$where
		$order
		$limit
		";		
		//dump($stmt);		
	    if($res = Yii::app()->db->createCommand($stmt)->queryAll()){		
	    	$res = Yii::app()->request->stripSlashes($res);
	    				
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";			
			if($resc = Yii::app()->db->createCommand($stmtc)->queryRow()){						
				$total_records=$resc['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/item/category_add',array('id'=>$val['cat_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					if($key_cols==1){
						if(!empty($val[$cols_val])){			
						   $cols_data[$cols_val] = "<img class=\"img-thumbnail\" src=\"".FunctionsV3::getImage($val[$cols_val])."\"/>";
						} else $cols_data[$cols_val]=$val[$cols_val];
					} elseif ($key_cols==2){
						$cols_data[$cols_val] = translate("[item_name]<br/>[items] items",array(
						  '[item_name]'=>$val[$cols_val],
						  '[items]'=>$val['items']
						));
					} else $cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
	public function actionDelete_category()
	{
		if (isset($this->data['row_id'])){
			try {
				
				$merchant_id = UserWrapper::getMerchantIDByAccesToken();
				$categories = array();
				foreach ($this->data['row_id'] as $cat_id) {					
					$categories[]= (integer) $cat_id;
				}			
				
				ItemWrap::deleteCategory($merchant_id,$categories);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/item/category_list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionAddon_category_list()
	{
				
    	$feed_data = array();
    	
    	$cols = array('subcat_id','subcategory_name');
    	$resp = DatatablesWrapper::format($cols,$this->data);
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( a.subcategory_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.subcat_id, a.subcategory_name,
		(
		 select count(*) from {{subcategory_item_relationships}}
		 where subcat_id = a.subcat_id
		) as items
		FROM {{subcategory}} a 
		WHERE 1		
		$and
		$where
		$order
		$limit
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			
			$total_records=0;									
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if($resc = Yii::app()->db->createCommand($stmtc)->queryRow()){						
				$total_records=$resc['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/item/addon_category_add',array('id'=>$val['subcat_id']));
				foreach ($cols as $key_cols=> $cols_val) {		
					if($key_cols==1){
						$cols_data[$cols_val] = translate("[item_name]<br/>[items] items",array(
						  '[item_name]'=>$val[$cols_val],
						  '[items]'=>$val['items']
						));
					} else	$cols_data[$cols_val]=$val[$cols_val];					
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
	public function actionAddon_category_add()
	{		
		$params = array(
		  'merchant_id'=>UserWrapper::getMerchantIDByAccesToken(),
		  'subcategory_name'=>isset($this->data['subcategory_name'])?$this->data['subcategory_name']:'',
		  'subcategory_description'=>isset($this->data['subcategory_description'])?$this->data['subcategory_description']:'',
		  'status'=>isset($this->data['status'])?$this->data['status']:'pending',
		  'sequence'=>isset($this->data['sequence'])?$this->data['sequence']:'0',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		);
		
		$id = 0;
		$id = isset($this->data['row_id'])?$this->data['row_id']:0;
		
		if($params['sequence']<=0 || !is_numeric($params['sequence'])){
		   $params['sequence']=ItemWrap::getMaxSequence('subcategory',$this->merchant_id);
		}
		
		if(isset($this->data['subcategory_name_trans'])){
			if (ItemWrap::okToDecode()){
				$params['subcategory_name_trans']=json_encode($this->data['subcategory_name_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['subcategory_name_trans']=json_encode($this->data['subcategory_name_trans']);				
		}
		
		if (isset($this->data['subcategory_description_trans'])){
			if (ItemWrap::okToDecode()){
				$params['subcategory_description_trans']=json_encode($this->data['subcategory_description_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['subcategory_description_trans']=json_encode($this->data['subcategory_description_trans']);
		}
		
		try {
			
			$params = InventoryWrapper::purifyData($params);
			
			ItemWrap::insertAddonCategory($params['merchant_id'],$params,(integer)$id);
			$this->OKresponse();
			$this->msg = translate("Successful");
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/item/addon_category_list')
			);			
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();
	}
	
	public function actionDelete_addon_category()
	{
		if (isset($this->data['row_id'])){
			try {
				
				$merchant_id = UserWrapper::getMerchantIDByAccesToken();
				
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
				
				ItemWrap::deleteAddonCategory($merchant_id,$ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/item/addon_category_list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionSize_list()
	{			
    	$feed_data = array();
    	
    	$cols = array('size_id','size_name');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( size_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS size_id, size_name FROM
		{{size}} 
		WHERE 1		
		$and
		$where
		$order
		$limit
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if($resc = Yii::app()->db->createCommand($stmtc)->queryRow()){				
				$total_records=$resc['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/item/size_add',array('id'=>$val['size_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					$cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
	public function actionDelete_size()
	{
		if (isset($this->data['row_id'])){
			try {
				
				$merchant_id = UserWrapper::getMerchantIDByAccesToken();
				
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
				
				ItemWrap::deleteSize($merchant_id,$ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/item/size_list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionSize_add()
	{		
		$params = array(
		  'merchant_id'=>$this->merchant_id,
		  'size_name'=>isset($this->data['size_name'])?$this->data['size_name']:'',		  
		  'sequence'=>isset($this->data['sequence'])?$this->data['sequence']:'0',
		  'status'=>isset($this->data['status'])?$this->data['status']:'pending',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],		  
		);
		
		$id = 0;
		$id = isset($this->data['row_id'])?$this->data['row_id']:0;
		
		if($params['sequence']<=0 || !is_numeric($params['sequence'])){
		   $params['sequence']=ItemWrap::getMaxSequence('size', $this->merchant_id);
		}
		
		if(isset($this->data['size_name_trans'])){
			if (ItemWrap::okToDecode()){
				$params['size_name_trans']=json_encode($this->data['size_name_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['size_name_trans']=json_encode($this->data['size_name_trans']);				
		}
				
		try {
						
			$params = InventoryWrapper::purifyData($params);
			
			ItemWrap::insertSize($params['merchant_id'],$params,(integer)$id);
			$this->OKresponse();
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/item/size_list')
			);			
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();
	}
	
	public function actionIngredients_list()
	{
				
    	$feed_data = array();
    	
    	$cols = array('ingredients_id','ingredients_name');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( ingredients_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS ingredients_id, ingredients_name FROM
		{{ingredients}} 
		WHERE 1		
		$and
		$where
		$order
		$limit
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if($resc = Yii::app()->db->createCommand($stmtc)->queryRow()){					
				$total_records=$resc['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/item/ingredients_add',array('id'=>$val['ingredients_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					$cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
		
	public function actionIngredients_add()
	{
		$params = array(
		  'merchant_id'=>$this->merchant_id,
		  'ingredients_name'=>isset($this->data['ingredients_name'])?$this->data['ingredients_name']:'',		  
		  'sequence'=>isset($this->data['sequence'])?$this->data['sequence']:'0',
		  'status'=>isset($this->data['status'])?$this->data['status']:'pending',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],		  
		);
		
		$id = 0;
		$id = isset($this->data['row_id'])?$this->data['row_id']:0;
		
		if($params['sequence']<=0 || !is_numeric($params['sequence'])){
		   $params['sequence']=ItemWrap::getMaxSequence('ingredients', $this->merchant_id);
		}
		
		if(isset($this->data['ingredients_name_trans'])){
			if (ItemWrap::okToDecode()){
				$params['ingredients_name_trans']=json_encode($this->data['ingredients_name_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['ingredients_name_trans']=json_encode($this->data['ingredients_name_trans']);				
		}
				
		try {
			
			$params = InventoryWrapper::purifyData($params);
			
			ItemWrap::insertIngredients($params['merchant_id'],$params,(integer)$id);
			$this->OKresponse();
			
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/item/ingredients_list')
			);			
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();
	}
	
	public function actionDelete_ingredients()
	{
		if (isset($this->data['row_id'])){
			try {
				
				$merchant_id = UserWrapper::getMerchantIDByAccesToken();
				
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
				
				ItemWrap::deleteIngredients($merchant_id,$ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/item/ingredients_list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionCooking_list()
	{
						
    	$feed_data = array();
    	
    	$cols = array('cook_id','cooking_name');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( cooking_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS cook_id, cooking_name FROM
		{{cooking_ref}} 
		WHERE 1		
		$and
		$where
		$order
		$limit
		";		
		//dump($stmt);
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if($resc = Yii::app()->db->createCommand($stmtc)->queryRow()){						
				$total_records=$resc['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/item/cooking_add',array('id'=>$val['cook_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					$cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}	
	
	public function actionCooking_add()
	{
		
	    $params = array(
		  'merchant_id'=>$this->merchant_id,
		  'cooking_name'=>isset($this->data['cooking_name'])?$this->data['cooking_name']:'',		  
		  'sequence'=>isset($this->data['sequence'])?$this->data['sequence']:'0',
		  'status'=>isset($this->data['status'])?$this->data['status']:'pending',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],		  
		);
		
		$id = 0;
		$id = isset($this->data['row_id'])?$this->data['row_id']:0;
		
		if($params['sequence']<=0 || !is_numeric($params['sequence'])){
		   $params['sequence']=ItemWrap::getMaxSequence('cooking_ref', $this->merchant_id);
		}
		
		if(isset($this->data['ingredients_name_trans'])){
			if (ItemWrap::okToDecode()){
				$params['cooking_name_trans']=json_encode($this->data['cooking_name_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['cooking_name_trans']=json_encode($this->data['cooking_name_trans']);				
		}
				
		try {
			
			$params = InventoryWrapper::purifyData($params);
			
			ItemWrap::insertCookingRef($params['merchant_id'],$params,(integer)$id);
			$this->OKresponse();
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/item/cooking_list')
			);			
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();		
	}
	
	public function actionDelete_cooking()
	{
		if (isset($this->data['row_id'])){
			try {
				
				$merchant_id = UserWrapper::getMerchantIDByAccesToken();
				
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
				
				ItemWrap::deleteCookingRef($merchant_id,$ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/item/cooking_list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionAddon_item_list()
	{
						
    	$feed_data = array(); $is_updated = false;
    	
    	$cols = array('sub_item_id','photo','sub_item_name','category');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( sub_item_name LIKE ".FunctionsV3::q("%$search_field%")." )";			
		}

		$stmt="SELECT SQL_CALC_FOUND_ROWS sub_item_id, photo, sub_item_name,category FROM
		{{subcategory_item}} 
		WHERE 1		 
		$and
		$where
		$order
		$limit
		";		
		
		if( FunctionsV3::checkIfTableExist('subcategory_item_relationships')){
			$is_updated = true;
			$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
			
			if(!empty($search_field)){
			   $and.=" AND ( a.sub_item_name LIKE ".FunctionsV3::q("%$search_field%")." )";			
		    }
		
			$stmt="
			select 
			a.sub_item_id,
			a.photo,
			a.sub_item_name,
			
			(
			select 			
			GROUP_CONCAT(c.subcategory_name)					
			from {{subcategory_item_relationships}} b 
			left join {{subcategory}} c
			On
			b.subcat_id = c.subcat_id			
			where 
			b.sub_item_id = a.sub_item_id
			) as category
						
			FROM
			{{subcategory_item}} a
			WHERE 1		
			$and
		    $where
		    $order
		    $limit
		    ";		
		}
				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if($resc = Yii::app()->db->createCommand($stmtc)->queryRow()){								
				$total_records=$resc['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/item/addon_add',array('id'=>$val['sub_item_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					if($key_cols==1){
						if(!empty($val[$cols_val])){			
						   $cols_data[$cols_val] = "<img class=\"img-thumbnail\" src=\"".FunctionsV3::getImage($val[$cols_val])."\"/>";
						} else $cols_data[$cols_val]=$val[$cols_val];					
					} else $cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}			
			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}		
	
	public function actionAddon_add()
	{
		$params = array(
		  'merchant_id'=>$this->merchant_id,
		  'sub_item_name'=>isset($this->data['sub_item_name'])?$this->data['sub_item_name']:'',
		  'item_description'=>isset($this->data['item_description'])?$this->data['item_description']:'',
		  'price'=>(float)isset($this->data['price'])?$this->data['price']:0,
		  'category'=>isset($this->data['category'])?json_encode($this->data['category']):'',
		  'sequence'=>isset($this->data['sequence'])?$this->data['sequence']:'0',
		  'status'=>isset($this->data['status'])?$this->data['status']:'pending',
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],		  
		);
		
		$id = 0;
		$id = isset($this->data['row_id'])?$this->data['row_id']:0;
		
		if($params['sequence']<=0 || !is_numeric($params['sequence'])){
		   $params['sequence']=ItemWrap::getMaxSequence('cooking_ref', $this->merchant_id);
		}
		
		if(isset($this->data['sub_item_name_trans'])){
			if (ItemWrap::okToDecode()){
				$params['sub_item_name_trans']=json_encode($this->data['sub_item_name_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['sub_item_name_trans']=json_encode($this->data['sub_item_name_trans']);				
		}
		if(isset($this->data['item_description_trans'])){
			if (ItemWrap::okToDecode()){
				$params['item_description_trans']=json_encode($this->data['item_description_trans'],
				JSON_UNESCAPED_UNICODE);
			} else $params['item_description_trans']=json_encode($this->data['item_description_trans']);				
		}
		
		if(isset($this->data['file_name'])){
			$params['photo'] = trim($this->data['file_name']);
		} else $params['photo']='';		
				
		try {
								
			$params = InventoryWrapper::purifyData($params);
			
			ItemWrap::insertAddonItem($params['merchant_id'],$params,$this->data['category'],(integer)$id);
			$this->OKresponse();
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/item/addon_item_list')
			);			
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();		
		$this->jsonResponse();
	}
	
	public function actionDelete_addon()
	{
		if (isset($this->data['row_id'])){
			try {
				
				$merchant_id = UserWrapper::getMerchantIDByAccesToken();
				
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
				
				ItemWrap::deleteSubItem($merchant_id,$ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/item/addon_item_list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionList()
	{			
    	$feed_data = array();
    	
    	$cols = array('item_id','photo','item_name','categories_name','price','cost_price','available_stocks','stocks_status');
    	$resp = DatatablesWrapper::format($cols,$this->data);
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( a.item_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$cat_ids='';		
		if(isset($this->data['cat_id'])){			
			if(is_array($this->data['cat_id']) && count($this->data['cat_id'])>=1){
				if (!in_array('all',$this->data['cat_id'])){
					foreach ($this->data['cat_id'] as $cat_id_val) {
						$cat_ids.= FunctionsV3::q($cat_id_val).",";
					}
					$cat_ids = substr($cat_ids,0,-1);
					$and.= " AND a.item_id  IN (
					  select item_id from {{item_relationship_category}}
					  where cat_id IN ($cat_ids)
					) ";
			    }		
			}
		}
		
		$stocks_filter='';		
		if(isset($this->data['items'])){
			switch ($this->data['items']) {
				case "out_stock":
					$and.=" AND a.item_id IN (
					    select 
						item_id as total from {{view_item_stocks_status}}
						where stock_status = 'Out of stocks'
						and item_id = a.item_id
						and track_stock='1'									
					)
					";
					break;
			
				 case "low_stock":
				 	$and.=" AND a.item_id IN (
					    select 
						item_id from {{view_item_stocks_status}}
						where stock_status = 'Low stock'
						and item_id = a.item_id
						and track_stock='1'
					)
					";
				 	break;
					
				default:
					break;
			}
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.*,
						
		(
		select 
		count(*) from {{view_item_stocks_status}}
		where stock_status = 'Out of stocks'
		and item_id = a.item_id
		and track_stock='1'
		) as count_out_of_stock,
		
		
		(
		select 
		count(*) from {{view_item_stocks_status}}
		where stock_status = 'Low stock'
		and item_id = a.item_id
		and track_stock='1'
		) as count_low_stock
		
		FROM {{view_item_list}} a
		WHERE 1		
		$and
		$where
		$order
		$limit
		";		
		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if($resc = Yii::app()->db->createCommand($stmtc)->queryRow()){		
				$total_records=$resc['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			
			//$cols_data[$cols_val] = "<img class=\"img-thumbnail\" src=\"".FunctionsV3::getImage($val[$cols_val])."\"/>";
			
			$datas=array(); 
			foreach ($res as $val) {
				//dump($val);
				$available_stocks = (float) $val['available_stocks'];
				$low_stock = (float) $val['low_stock'];
				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/item/item_new',array('id'=>$val['item_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					if($cols_val=="stocks_status"){						  
												
						$count_out_of_stock =''; $count_low_stock='';
						$low_stock_message = "Low stock ([count])";
						$out_stock_message = "Out of stocks ([count])";
						if($val['with_size']<=0){
							$low_stock_message = "Low stock";
						    $out_stock_message = "Out of stocks";
						}
												
						if($val['count_low_stock']>=1){						
							$count_low_stock = '<span class="text-warning font-weight-bold">'.translate($low_stock_message,array(
							 '[count]'=>$val['count_low_stock']
							)).'<span>';
						}
						if($val['count_out_of_stock']>=1){							
							$count_out_of_stock = '<span class="text-danger font-weight-bold">'.translate($out_stock_message,array(
							  '[count]'=>$val['count_out_of_stock']
							)).'</span>';
						}
						
						$cols_data[$cols_val]="$count_out_of_stock $count_low_stock";
						
					} else {
						if($key_cols==1){						
							if($val['with_size']==1){						   	
						       $cols_data[$cols_val]='<a class="item_show_sizes" data-id="'.$val['item_id'].'" href="javascript:;"><i class="fas fa-chevron-down"></i></a>';
						   } else $cols_data[$cols_val] = '';
						} elseif ( $key_cols==4){
							
							  if($val['with_size']<=0){
								  $attributes = 'class="inline_edit" ';
								  $inline_form = $this->renderPartial(APP_FOLDER.'.views.item.inline_edit',array(
								    'price'=>normalPrettyPrice($val['price']),
								    'item_id'=>$val['item_id'],
								    'action'=>"inline_price"
								  ),true);
								  $cols_data[$cols_val]= '<span '.$attributes.'>'.FunctionsV3::prettyPrice($val[$cols_val]).$inline_form.'</span>';
							  } else $cols_data[$cols_val] = FunctionsV3::prettyPrice(0);
							  
						} else if ( $key_cols==5) {
							  $cols_data[$cols_val]= FunctionsV3::prettyPrice($val[$cols_val]);
						} else if ($key_cols==6) {
							  if($val['track_stock']==1){
							  	
							     if($val['with_size']<=0){
								     $inline_form = $this->renderPartial(APP_FOLDER.'.views.item.inline_edit',array(
									    'price'=>InventoryWrapper::prettyQuantity($val[$cols_val]),
									    'item_id'=>$val['sku'],
									    'action'=>"inline_stock"
									  ),true);
									  $cols_data[$cols_val]= '<span class="inline_edit">'.InventoryWrapper::prettyQuantity($val[$cols_val]).$inline_form.'</span>';
							     } else $cols_data[$cols_val] = InventoryWrapper::prettyQuantity($val[$cols_val]);	  							     
							     
							  } else $cols_data[$cols_val] = '-';
						} else $cols_data[$cols_val]=$val[$cols_val];
					}
				}
				$datas[]=$cols_data;
			}	
					
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}	
	
	public function actionAddAddonRow()
	{
		$subcat_id = (integer) isset($this->data['subcat_id'])?$this->data['subcat_id']:'';
		if($subcat_id>0){
			if( $res = ItemWrap::getAddonCategoryWithItem($this->merchant_id,$subcat_id)){
				$html = ItemHtmlWrapper::ListAddon($res);
				$this->OKresponse();
				$this->details = array(
				 'next_action'=>'fill_addon_list',
				 'subcat_id'=>$subcat_id,
				 'html'=>$html
				);				
			} else $this->msg = translate("No items found");
		} else $this->msg = translate("Invalid id");
		$this->jsonResponse();
	}
	
	public function actionLoadAddonItem()
	{
		try {
			$html='';
			
			$row_id =  isset($this->data['row_id'])? (integer) $this->data['row_id']:0;
			$resp = ItemWrap::getData("item","merchant_id=:merchant_id AND item_id=:item_id",array(
					 ':merchant_id'=>$this->merchant_id,
					 ':item_id'=>$row_id
					));						
			$addon_item = !empty($resp['addon_item'])?json_decode($resp['addon_item'],true):false;
			$multi_option = !empty($resp['multi_option'])?json_decode($resp['multi_option'],true):false;
			$multi_option_value = !empty($resp['multi_option_value'])?json_decode($resp['multi_option_value'],true):false;
			$require_addon = !empty($resp['require_addon'])?json_decode($resp['require_addon'],true):false;			
			$two_flavors_position = !empty($resp['two_flavors_position'])?json_decode($resp['two_flavors_position'],true):false;			
						
			if(is_array($addon_item) && count($addon_item)>=1){
				foreach ($addon_item as $subcat_id=>$sub_item_id) {		
					$options = array(
					  'sub_item_id'=>$sub_item_id,
					  'multi_option'=>$multi_option[$subcat_id][0],
					  'multi_option_value'=>isset($multi_option_value[$subcat_id][0])?$multi_option_value[$subcat_id][0]:'',
					  'require_addon'=>isset($require_addon[$subcat_id][0])?$require_addon[$subcat_id][0]:'',
					  'two_flavors_position'=>isset($two_flavors_position[$subcat_id][0])?$two_flavors_position[$subcat_id][0]:'',
					);					
					if( $res = ItemWrap::getAddonCategoryWithItem($this->merchant_id,$subcat_id)){
						$html.= ItemHtmlWrapper::ListAddon($res, $options);
					}
				}				
				$this->OKresponse();
				$this->details = array(
				 'next_action'=>'fill_addon_list',
				 'html'=>$html
				);				
			} else {
				$this->OKresponse();
				$this->msg = translate("Invalid addon item");
				$this->details = array(
				 'next_action'=>'silent',				 
				);				
			}
					
	   } catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		$this->jsonResponse();
	}
	
	public function actionLoadSizeForm()
	{
		$html=''; $data=array(); $stocks = 0;
		$with_size=  isset($this->data['with_size'])? (integer) $this->data['with_size']:0;
		$row_id =  isset($this->data['row_id'])? (integer) $this->data['row_id']:0;	
		if($with_size>0){
			if($row_id>0){
				$data = ItemWrap::getItemSizePricesWithStocks($this->merchant_id,$row_id);			
				$data = Yii::app()->request->stripSlashes($data);					
			}
			//dump($data);
			$html=ItemHtmlWrapper::withSizeForm($this->merchant_id,$data);
			$data=array();
		} else {						
			if($row_id>0){
				$data = ItemWrap::getItemSizePrice($this->merchant_id,$row_id);			
				$data = Yii::app()->request->stripSlashes($data);					
				$stocks = StocksWrapper::getStocksSKU( isset($data['sku'])?$data['sku']:'' );				
			}			
			$html=ItemHtmlWrapper::noSizeForm($data);
		}		
		$this->OKresponse();
		$this->details = array(
		 'next_action'=>isset($this->data['next_action'])?$this->data['next_action']:'load_size_form',
		 'row_id'=>$row_id,
		 'with_size'=>$with_size,
		 'in_stock'=>$stocks,
		 'data'=>$data,
		 'html'=>$html
		);							
		$this->jsonResponse();
	}
	
	public function actionLoadWithSizeForm()
	{
		
		$i = Yii::app()->db->createCommand()->insert("{{item_sku}}",array(
		  'item_id'=>1
	    ));	 
		
		$html=ItemHtmlWrapper::withSizeFormTR($this->merchant_id);
		$this->OKresponse();
		$this->details = array(
		 'next_action'=>'load_size_form_append',
		 'html'=>$html
		);				
		$this->jsonResponse();
	}
	
	public function actionItem_new()
	{				
		$with_size =  isset($this->data['with_size'])?(integer)$this->data['with_size']:0;
		$price=array();
		
		if($with_size==1){
	    	if (isset($this->data['price']) && count($this->data['price'])>=1){
	    		foreach ($this->data['price'] as $key=>$val) {
	    			if (!empty($val)){
	    			   $price[$this->data['size'][$key]]=$val;
	    			}
	    		}	    		
	    	}	      	    	
		} else {
			$price[] = (float)isset($this->data['single_price'])?$this->data['single_price']:0;
		}
    	
		$params = array(
		  'merchant_id'=>(integer)$this->merchant_id,
		  'item_name'=>isset($this->data['item_name'])?trim($this->data['item_name']):'',
		  'item_description'=>isset($this->data['item_description'])?trim($this->data['item_description']):'',
		  'status'=>isset($this->data['status'])?trim($this->data['status']):'',
		  'category'=>isset($this->data['category'])?json_encode($this->data['category']):'',
		  'price'=>isset($price)?json_encode($price):'',		  
		  'addon_item'=>isset($this->data['sub_item_id'])?json_encode($this->data['sub_item_id']):"",
		  'cooking_ref'=>isset($this->data['cooking_ref'])?json_encode($this->data['cooking_ref']):"",
		  'discount'=>!empty($this->data['discount'])?(float)$this->data['discount']:'',
		  'multi_option'=>isset($this->data['multi_option'])?json_encode($this->data['multi_option']):"",
		  'multi_option_value'=>isset($this->data['multi_option_value'])?json_encode($this->data['multi_option_value']):"",
		  'photo'=>isset($this->data['file_name'])?$this->data['file_name']:"",
		  'gallery_photo'=>isset($this->data['file_name_multiple'])?json_encode($this->data['file_name_multiple']):'',
		  'ingredients'=>isset($this->data['ingredients'])?json_encode($this->data['ingredients']):"",
		  'spicydish'=>isset($this->data['spicydish'])?(integer)$this->data['spicydish']:1,
		  'two_flavors'=>isset($this->data['two_flavors'])?(integer)$this->data['two_flavors']:'0',
		  'two_flavors_position'=>isset($this->data['two_flavors_position'])?json_encode($this->data['two_flavors_position']):"",
		  'require_addon'=>isset($this->data['require_addon'])?json_encode($this->data['require_addon']):"",
		  'dish'=>isset($this->data['dish'])?json_encode($this->data['dish']):'',
		  'non_taxable'=> isset($this->data['non_taxable'])?(integer)$this->data['non_taxable']:1,		  
		  'packaging_fee'=>isset($this->data['packaging_fee'])?(float)$this->data['packaging_fee']:0,
		  'packaging_incremental'=>isset($this->data['packaging_incremental'])?(integer)$this->data['packaging_incremental']:0,
		  'sequence'=>isset($this->data['sequence'])?(integer)$this->data['sequence']:0,
		  'points_earned'=>isset($this->data['points_earned'])?(integer)$this->data['points_earned']:0,
		  'points_disabled'=>isset($this->data['points_disabled'])?(integer)$this->data['points_disabled']:1,
		  'date_created'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		  'with_size'=>$with_size,		  
		  'item_token'=>ItemWrap::generateFoodToken(),
		  'not_available'=>isset($this->data['not_available'])?(integer)$this->data['not_available']:1,
		  'track_stock'=>isset($this->data['track_stock'])?(integer)$this->data['track_stock']:0,
		  'supplier_id'=>isset($this->data['supplier_id'])?(integer)$this->data['supplier_id']:0,
		);
		
		$id = 0;
		$id = isset($this->data['row_id'])?$this->data['row_id']:0;
		
		if($params['sequence']<=0 || !is_numeric($params['sequence'])){
			$params['sequence']=ItemWrap::getMaxSequence('item', $this->merchant_id);
		}
		if (isset($this->data['item_name_trans'])){
			if (okToDecode()){
			    $params['item_name_trans']=json_encode($this->data['item_name_trans'],
			    JSON_UNESCAPED_UNICODE);
			} else $params['item_name_trans']=json_encode($this->data['item_name_trans']);
		}	    
		if (isset($this->data['item_description_trans'])){
			if (okToDecode()){
			   $params['item_description_trans']=json_encode($this->data['item_description_trans'],
			   JSON_UNESCAPED_UNICODE);
			} else $params['item_description_trans']=json_encode($this->data['item_description_trans']);
		}			
		
				
		try {
						
			$params = InventoryWrapper::purifyData($params);			
			
			ItemWrap::insertFood($this->merchant_id,$params,$id,$this->data);			
			$this->OKresponse();
			$this->msg = translate("Successful");
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/item/list')
			);
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		    $this->details = array(
		     'error_type'=>'alert',
		     'error_title'=>$id>0?translate("Error editing item"):translate("Error adding item")
		    );
		}
		$this->jsonResponse();
	}
	
	public function actionDelete_item()
	{
		if (isset($this->data['row_id'])){
			try {
								
				$row_id = array();
				foreach ($this->data['row_id'] as $id) {					
					$row_id[]= (integer) $id;
				}							
				
				ItemWrap::deleteItem($this->merchant_id,$row_id);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/item/list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionDeleteItemSize()
	{
		$item_token = isset($this->data['item_token'])?$this->data['item_token']:'';
		if(!empty($item_token)){			
			try {
				
				ItemWrap::deleteItemSizeBySku($this->merchant_id,$item_token);
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> 're_load_size_form',
				 'redirect'=>Yii::app()->createUrl('inventory/item/list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid size id");
		$this->jsonResponse();
	}
	
	public function actionGet_item()
	{
		$status = true;
		$query = (!empty($this->data['q'])) ? strtolower($this->data['q']) : null;
		$track_stock = !empty($this->data['track_stock']) ? $this->data['track_stock'] : '';
		$data = array();
					
		if( !$data = ItemWrap::searchItem($this->merchant_id, $query, $track_stock)){
			$status = false;
		}
		
		$data = Yii::app()->request->stripSlashes($data);		
										
	    header('Content-Type: application/json');
		echo json_encode(array(
		    "status" => $status,
		    "error"  => null,
		    "data"   => array(
		        "item" => $data,	        
		    )
		));

	}
	
	public function actionLoadItemSizeList()
	{
		$item_id = isset($this->data['item_id'])? (integer) $this->data['item_id'] : 0;
		if($item_id>0){
			if ( $res = ItemWrap::getItemSizePricesWithStocks($this->merchant_id,$item_id)){
				$res = Yii::app()->request->stripSlashes($res);		
				
				$html='';
				foreach ($res as $val) {
					   $stock_status = '';
     			   	   $available_stocks = $val['available_stocks'];
     			   	   $low_stock = $val['low_stock'];
     			   	   if($val['track_stock']==1){
	     			   	   if($available_stocks<=0){
	     			   	   	  $stock_status = '<span class="text-danger font-weight-bold">'.translate("Out of stocks").'</span>';
	     			   	   } else if ($available_stocks<=$low_stock){
	     			   	   	  $stock_status = '<span class="text-warning font-weight-bold">'.translate("Low stock").'</span>';
	     			   	   }
     			   	   }     			   	   
     			   	   
     			   	  $inline_form = $this->renderPartial(APP_FOLDER.'.views.item.inline_edit',array(
					    'price'=>normalPrettyPrice($val['price']),
					    'item_id'=>$val['sku'],
					    'action'=>"inline_price_by_sku"
					  ),true);
					  
					  $inline_stocks = $this->renderPartial(APP_FOLDER.'.views.item.inline_edit',array(
					    'price'=>InventoryWrapper::prettyQuantity($val['available_stocks']),
					    'item_id'=>$val['sku'],
					    'action'=>"inline_stock"
					  ),true);
     			   	   
     			   	   $html.='<tr class="tr_item_size_list">';
     			   	    $html.='<td></td>';
     			   	    $html.='<td></td>';
     			   	    $html.='<td>'. $val['size_name'] .'</td>';
     			   	    $html.='<td></td>';
     			   	    $html.='<td><span class="inline_edit">'. FunctionsV3::prettyPrice($val['price']) . $inline_form .'</span></td>';
     			   	    $html.='<td>'. FunctionsV3::prettyPrice($val['cost_price']) .'</td>';
     			   	    $html.='<td><span class="inline_edit">'. InventoryWrapper::prettyQuantity($val['available_stocks']) . $inline_stocks .'</span></td>';
     			   	    $html.='<td>'. $stock_status .'</td>';
     			   	   $html.='</tr>';		
				}
				
				$this->OKresponse();
			    $this->msg = translate("Successful");
			    $this->details = array(
			      'next_action'=>"load_item_ist",
			      'data'=>$html
			    );
			    
			} else $this->msg = translate("No results");
		} else $this->msg = translate("invalid item id");
		$this->jsonResponse();
	}
	
	public function actionInline_price()
	{
		$value = isset($this->data['inline_value'])? (float) $this->data['inline_value'] : 0;
		$id = isset($this->data['inline_id']) ? (integer) $this->data['inline_id'] : 0;
		$params1 = array(
		  'price'=>json_encode(array("$value")),
		  'date_modified'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
		$params2 = array(
		  'price'=>$value,
		  'updated_at'=>FunctionsV3::dateNow(),		  
		);
		
		try {
			
			ItemWrap::inlineUpdatePrice($this->merchant_id,$id,$params1,$params2);
			$this->OKresponse();
			$this->details = array(
			 'next_action'=>"refresh_table",
			);
			
		} catch (Exception $e) {
		   $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();
	}
	
	public function actionInline_price_by_sku()
	{				
		$value = isset($this->data['inline_value'])? (float) $this->data['inline_value'] : 0;
		$sku = isset($this->data['inline_id']) ? (integer) $this->data['inline_id'] : 0;
		
		try {
			
			ItemWrap::inlineUpdatePriceBySku($this->merchant_id, (integer) $sku, $value  );
			
			$this->OKresponse();
			$this->details = array(
			 'next_action'=>"refresh_table",
			);
			
		} catch (Exception $e) {
		   $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();
	}
	
	public function actionInline_stock()
	{
		$in_stock = isset($this->data['inline_value'])? (float) $this->data['inline_value'] : 0;
		$sku = isset($this->data['inline_id']) ? (integer) $this->data['inline_id'] : 0;
		if ($item = ItemWrap::getItemBySku($this->merchant_id,$sku)){			
			StocksWrapper::updateStocksEditItem($sku,$item['cost_price'],$in_stock,$this->merchant_id,UserWrapper::getUserName());
			$this->OKresponse();
			$this->details = array(
			 'next_action'=>"refresh_table",
			);
		} else $this->msg = translate("Item not found");
		$this->jsonResponse();
	}
	
	public function actionCheckNotification()
	{
		$inventory_live = getOption($this->merchant_id,'inventory_live');				
		
		$links = array();
		
		/*ITEM*/
		$total = InstallWrapper::getItemsCountMigrate($this->merchant_id);		
		if($total>0){
			$link = Yii::app()->createUrl('inventory/item/update_data');	
			$message = translate("There is ([count]) items that need to update",array(
		     '[count]'=>$total
		   ));
			$links[]=array(
			   '<a class="dropdown-item" href="'.$link.'">'.$message.'</a>'
			);
		}		

		/*REPORT*/	
		$total_item = ReportsWrapper::totalFixedReport($this->merchant_id);
		if($total_item>0){
			$link = Yii::app()->createUrl('inventory/reports/fixedreport');	
			$message = translate("There is ([count]) order that need to update",array(
		     '[count]'=>$total_item
		   ));
			$links[]=array(
			   '<a class="dropdown-item" href="'.$link.'">'.$message.'</a>'
			);
		}		
		
	
		$this->OKresponse();	
		
		$this->details = array(
		   'next_action'=> 'update_notification',
		   'inventory_live'=>$inventory_live,
		   'count'=>count($links),
		   'link'=>$links		   
		);				
		$this->jsonResponse();
	}
	
	
	public function actionUpdate_data()
	{
		
		$next_action = 'next_item'; 
		
		$counter = (integer) $this->data['counter'];		
		$total_table = (integer) $this->data['total_item'] + 0;
		
		$stmt=""; $stmt_item ="";  $created_at = FunctionsV3::dateNow();
		$message=''; $stats=''; $item_name=''; $merchant_id=0; $item_id=0;
				
		$stmt_cat =''; $stmt_cat_item='';
		$stmt_sku =''; $stmt_sku_item='';
		$stmt_subcat =''; $stmt_subcat_item='';
		$with_size=0;
		
		$sku = ItemWrap::autoGenerateSKU();
		
		if ( $res = InstallWrapper::getItemstoMigrate($counter,$this->merchant_id)){			
			foreach ($res as $val) {
				//dump($val);
				
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
			
			
			$stmt_sku_item = substr($stmt_sku_item,0,-2).";";
			$stmt_sku="
			INSERT INTO {{item_sku}} (
			  sku_id, item_id
			)
			VALUES \n$stmt_sku_item
			";
			
			
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
				
				Yii::app()->db->createCommand($stmt)->query();
				
				if(!empty($stmt_cat_item)){
				   Yii::app()->db->createCommand($stmt_cat)->query();
				}
				
				if(!empty($stmt_subcat_item)){
				   Yii::app()->db->createCommand($stmt_subcat)->query();
				}
				
				Yii::app()->db->createCommand($stmt_sku)->query();
				
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
				$next_url =  Yii::app()->createUrl(APP_FOLDER.'/item/list');
				$message = '<a href="'.$next_url.'" class="btn btn-raised btn-primary" >'.translate("Done").'</a>';				
			}
			
			$this->OKresponse();
			$this->details = array(
			  'next_action'=>$next_action,
			  'counter'=>$counter+1,
			  'message'=>$message
			);
			$this->jsonResponse();
			
		} else {			
			$next_url =  Yii::app()->createUrl(APP_FOLDER.'/item/list');
			$message = '<a href="'.$next_url.'" class="btn btn-raised btn-primary" >'.translate("Done").'</a>';			
			
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
	
}
/*end class*/