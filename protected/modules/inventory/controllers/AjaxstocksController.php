<?php
class AjaxstocksController extends CController
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
		$this->code = 1; $this->msg = "OK";
	}
	
	public function actionAdjustment_new()
	{
		
		$params_item = array(); $params = array();
		
		$transaction_type = isset($this->data['transaction_type'])?trim($this->data['transaction_type']):'';		
		
		if(isset($this->data['sku'])){
			if(is_array($this->data['sku']) && count($this->data['sku'])>=1 ){
				foreach ($this->data['sku'] as $key=>$val) {
					$params_item[] = array(
					  'transaction_id'=>(integer)StocksWrapper::autoGenerateTransactionID(),
					  'sku'=>$val,
					  'qty'=>(float)$this->data['qty'][$key],					  
					  'cost_price'=>isset($this->data['cost'][$key])?(float)$this->data['cost'][$key]:0,
					  'created_at'=>FunctionsV3::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR']
					);					
				}
				
				$params = array(
				  'merchant_id'=>$this->merchant_id,
				  'transaction_type'=>$transaction_type,
				  'transaction_code'=>StocksWrapper::transactionCode('sa'),
				  'notes'=>trim($this->data['notes']),
				  'created_at'=>FunctionsV3::dateNow(),
				  'added_by'=>UserWrapper::getUserName(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
								
				try {

					$params = InventoryWrapper::purifyData($params);
					
					StocksWrapper::insertAdjustment($params,$params_item);
					$this->details = array(
					  'next_action'=>"redirect",
					  'redirect'=>Yii::app()->createUrl('inventory/stocks/adjustment_list')
					);
					$this->OKresponse();
					$this->msg = translate("Successful");
					
				} catch (Exception $e) {
		           $this->msg = translate($e->getMessage());
		        }
				
			} else $this->msg = translate("Invalid item");
		} else $this->msg = translate("Invalid item");
		$this->jsonResponse();
	}
	
	public function actionAdjustment_list()
	{		
		$feed_data = array(); $transaction_list = StocksWrapper::adjustmentType();
		
		$cols = array('transaction_id','created_at','transaction_type','quantity');
    	$resp = DatatablesWrapper::format($cols,$this->data);
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";		
								
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( reference LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$reason = '';		
		if(isset($this->data['reason'])){			
			if(is_array($this->data['reason']) && count($this->data['reason'])>=1){
				if (!in_array('all',$this->data['reason'])){
					foreach ($this->data['reason'] as $reason_val) {
						$reason.= FunctionsV3::q($reason_val).",";
					}					
					$reason = substr($reason,0,-1);
					$and.= " AND transaction_type IN ($reason) ";
				}		
			}
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		transaction_id,created_at,transaction_type,quantity,reference
		FROM {{view_inventory_transaction}} 
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
			
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/stocks/adjustment_details',array('id'=>$val['transaction_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					if($key_cols==0){
						$cols_data[$cols_val]=$val['reference'];					
					} elseif ( $key_cols==1){
						$cols_data[$cols_val]=FunctionsV3::prettyDate($val[$cols_val]);
					} elseif ( $key_cols==2){	
						$cols_data[$cols_val] = $transaction_list[$val[$cols_val]];
					} elseif ( $key_cols==3){
						$cols_data[$cols_val] = InventoryWrapper::prettyQuantity($val[$cols_val]);
					} else $cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}								
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);	
		} else $this->otableNodata();		
	}
	
	public function actionSupplier_new()
	{
		$params = array(
		  'merchant_id'=>$this->merchant_id,
		  'supplier_name'=>isset($this->data['supplier_name'])?$this->data['supplier_name']:'',
		  'contact_name'=>isset($this->data['contact_name'])?$this->data['contact_name']:'',
		  'email'=>isset($this->data['email'])?$this->data['email']:'',
		  'phone_number'=>isset($this->data['phone_number'])?$this->data['phone_number']:'',
		  'address_1'=>isset($this->data['address_1'])?$this->data['address_1']:'',
		  'address_2'=>isset($this->data['address_2'])?$this->data['address_2']:'',
		  'city'=>isset($this->data['city'])?$this->data['city']:'',
		  'postal_code'=>isset($this->data['postal_code'])?$this->data['postal_code']:'',
		  'country_code'=>isset($this->data['country_code'])?$this->data['country_code']:'',
		  'region'=>isset($this->data['region'])?$this->data['region']:'',
		  'notes'=>isset($this->data['notes'])?$this->data['notes']:'',
		  'created_at'=>FunctionsV3::dateNow(),
		  'updated_at'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
		
		try {
			
			$id = 0;
		    $id = isset($this->data['row_id'])?$this->data['row_id']:0;
		    if($id>0){
		    	unset($params['created_at']);
		    } else unset($params['updated_at']);		    
		    			
		    $params = InventoryWrapper::purifyData($params);
		    
			StocksWrapper::insertSupplier($params['merchant_id'],$params,(integer)$id);
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/stocks/supplier_list')
			);
			$this->OKresponse();
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}				
		$this->jsonResponse();
	}
	
	public function actionSupplier_list()
	{
	
		$feed_data = array(); $transaction_list = StocksWrapper::adjustmentType();
		
		$cols = array('supplier_id','supplier_name','contact_name','phone_number','email');
    	$resp = DatatablesWrapper::format($cols,$this->data);
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( supplier_name LIKE ".FunctionsV3::q("%$search_field%")."
			 OR contact_name LIKE ".FunctionsV3::q("%$search_field%")."
			 OR email LIKE ".FunctionsV3::q("%$search_field%")."
			 OR phone_number LIKE ".FunctionsV3::q("%$search_field%")."
			 )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		*
		FROM {{inventory_supplier}} 
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
			
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/stocks/supplier_new',array('id'=>$val['supplier_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					$cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}								
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);	
		} else $this->otableNodata();		
	}
	
	public function actionDelete_supplier()
	{
		if (isset($this->data['row_id'])){
			try {
								
				
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
				
				StocksWrapper::deleteSupplier($this->merchant_id,$ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/stocks/supplier_list')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
		
	public function actionPurchase_new()
	{		
		$params = array(
		  'merchant_id'=>$this->merchant_id,
		  'purchase_date'=>isset($this->data['purchase_date'])?$this->data['purchase_date']:date("Y-m-d"),
		  'supplier_id'=>isset($this->data['supplier_id'])?$this->data['supplier_id']:'',
		  'notes'=>isset($this->data['notes'])?$this->data['notes']:'',
		  'expected_on'=>isset($this->data['expected_on'])?$this->data['expected_on']:'',
		  'created_at'=>FunctionsV3::dateNow(),
		  'updated_at'=>FunctionsV3::dateNow(),
		  'added_by'=>UserWrapper::getUserName(),		  		  
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);		
		if(empty($params['expected_on'])){
			unset($params['expected_on']);
		}
		
		$row_id =  isset($this->data['row_id'])? (integer) $this->data['row_id']:0;
		
		$params_details = array();
		if(isset($this->data['sku'])){
			if(is_array($this->data['sku']) && count($this->data['sku'])>=1){
				foreach ($this->data['sku'] as $key=>$sku) {
					$qty = isset($this->data['qty'][$key])? (float)$this->data['qty'][$key] : 0;
					$cost_price = isset($this->data['cost'][$key])? (float)$this->data['cost'][$key] : 0;
					
					$po_id = (integer)StocksWrapper::autoGenerateID('inventory_purchase_order');
					if($row_id>0){
						$po_id=$row_id;
					}
					
					$po_details_id = 0;
					if(isset($this->data['po_details_id'][$key])){
					   $po_details_id = (integer) $this->data['po_details_id'][$key];
					}
										
					$p_details = array(					  
					  'po_id'=>$po_id,
					  'sku'=>$sku,
					  'qty'=>$qty,
					  'cost_price'=>$cost_price,
					  'amount'=>$cost_price*$qty
					);
					if($po_details_id>0){
						$p_details['po_details_id']=$po_details_id;		
						$receive_qty = StocksWrapper::getReceiveSum($po_id,$po_details_id);
						$p_details['qty']+= $receive_qty;
					}
					$params_details[]=$p_details;
				}
			}
		}
				
		try {
						
			if($row_id>0){
				unset($params['created_at']);
			} else unset($params['updated_at']);
			
			$params = InventoryWrapper::purifyData($params);
			
			StocksWrapper::insertPurchase($params,$params_details,$row_id);
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/stocks/order_list')
			);
			$this->OKresponse();
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
	       $this->msg = translate($e->getMessage());
	    }
		
		$this->jsonResponse();
	}
	
	public function actionOrder_list()
	{
	
		$feed_data = array(); $transaction_list = StocksWrapper::adjustmentType();
		
		$cols = array('po_id','purchase_date','supplier_name','status','received','expected_on','total','total_qty');
    	$resp = DatatablesWrapper::format($cols,$this->data);
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
				
		$status='';		
		if(isset($this->data['status'])){			
			if(is_array($this->data['status']) && count($this->data['status'])>=1){
				if (!in_array('all',$this->data['status'])){
					foreach ($this->data['status'] as $status_val) {
						$status.= FunctionsV3::q($status_val).",";
					}
					$status = substr($status,0,-1);
					$and.= " AND status IN ($status) ";
			    }		
			}
		}
		
		$supplier_id = '';		
		if(isset($this->data['supplier_id'])){			
			if(is_array($this->data['supplier_id']) && count($this->data['supplier_id'])>=1){
				if (!in_array('all',$this->data['supplier_id'])){
					foreach ($this->data['supplier_id'] as $supplier_id_val) {
						$supplier_id.= FunctionsV3::q($supplier_id_val).",";
					}
					$supplier_id = substr($supplier_id,0,-1);
					$and.= " AND supplier_id IN ($supplier_id) ";
			    }		
			}
		}
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( po_id LIKE ".FunctionsV3::q("%$search_field%")."
			 OR supplier_name LIKE  ".FunctionsV3::q("%$search_field%")." 
			 OR notes LIKE  ".FunctionsV3::q("%$search_field%")."  
			  )";
		}

		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		*
		FROM {{view_inventory_purchase_order}} 
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
			
			foreach ($res as $val) {								
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/stocks/purchase_view',array('id'=>$val['po_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					if($key_cols==0){
						$cols_data[$cols_val] =  StocksWrapper::transactionCode('po').$val[$cols_val];
					} elseif ( $key_cols==1){
						$cols_data[$cols_val]=FunctionsV3::prettyDate($val[$cols_val]);
					} elseif ( $key_cols==3){
						$cols_data[$cols_val] = StocksWrapper::prettyPurchaseStatus($val[$cols_val]);
					} elseif ( $key_cols==4){
						
						$received = isset($val['received'])?(float)$val['received']:0;
						$total_qty = (float)$val['total_qty'];
						$percent = ($received/$total_qty)*100;
						
						$html='<div class="progress">
					      <div class="progress-bar" role="progressbar" style="width:'.$percent.'%"></div>
					    </div>';
						
						$html.='<div class="text-muted">'.translate("[receive] of [total]",array(
						 '[receive]'=>InventoryWrapper::prettyQuantity($received),
						 '[total]'=>InventoryWrapper::prettyQuantity($total_qty),
						)).'</div>';
						$cols_data[$cols_val] = $html;
						
					} elseif ( $key_cols==5){	
						$cols_data[$cols_val]=  !empty($val[$cols_val])? FunctionsV3::prettyDate($val[$cols_val]) :'';
					} elseif ( $key_cols==6){
						$cols_data[$cols_val]=FunctionsV3::prettyPrice($val[$cols_val]);	
					} else $cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}								
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);	
		} else $this->otableNodata();		
	}
	
	public function actionPurchase_receive()
	{		
		$po_id = isset($this->data['row_id'])? (integer)$this->data['row_id'] : '';
		if ( $resp = StocksWrapper::getPurchaseOrder($po_id,$this->merchant_id)){					
		$params = array();
			if(isset($this->data['po_details_id'])){
				foreach ($this->data['po_details_id'] as $key=>$val) {
					
					$po_details_id = $val;
					
					$sku = StocksWrapper::getSKUPurchaseDetails2($po_details_id);					
					
					$receive_params = array(
					  'po_id'=>$po_id,
					  'po_details_id'=>$po_details_id,
					  'sku'=>$sku['sku'],
					  'qty'=>isset($this->data['receive_qty'][$key])? (float) $this->data['receive_qty'][$key]:0,
					  'added_by'=>UserWrapper::getUserName(),
					  'created_at'=>FunctionsV3::dateNow(),
				      'ip_address'=>$_SERVER['REMOTE_ADDR'],
				      'cost_price'=>$sku['cost_price'],
				      'track_stock'=>$sku['track_stock']
					);
					
					if($receive_params['qty']>0){					
					   $params[] = $receive_params;
					}
				}
				try {					
					
					
					StocksWrapper::insertPurchaseReceive($params,$this->merchant_id);
					$this->details = array(
					  'next_action'=>"redirect",
					  'redirect'=>Yii::app()->createUrl('inventory/stocks/order_list')
					);
					$this->OKresponse();
					$this->msg = translate("Successful");
					
				} catch (Exception $e) {
		           $this->msg = translate($e->getMessage());
		        }    
			} else $this->msg = translate("Invalid id");
		} else $this->msg = translate("Purchase information not found");
		$this->jsonResponse();
	}
	
	public function actionDelete_purchase()
	{		
		$id = isset($this->data['row_id'])? (integer) $this->data['row_id'][0]:0;
		try {
			
			$params = array(
			  'status'=>"closed",
			  'updated_at'=>FunctionsV3::dateNow(),
			  'added_by'=>UserWrapper::getUserName(),
			  'ip_address'=>$_SERVER['REMOTE_ADDR']
			);
			
			StocksWrapper::setDeletePurchaseOrder( (integer) $id, $this->merchant_id, $params);
			$this->details = array(
				  'next_action'=>"redirect",
				  'redirect'=>Yii::app()->createUrl('inventory/stocks/order_list')
				);
				$this->OKresponse();
				$this->msg = translate("Successful");
			
		} catch (Exception $e) {
           $this->msg = translate($e->getMessage());
        }  
		$this->jsonResponse();
	}
	
	public function actionHistory()
	{
		
		$feed_data = array(); 
		
		$data_columns[] = array('data'=>"created_at");		
		$data_columns[] = array('data'=>"item_name");
		$data_columns[] = array('data'=>"added_by");
		$data_columns[] = array('data'=>"transaction_type");		
		$data_columns[] = array('data'=>"adjustment");
		$data_columns[] = array('data'=>"stock_after");
		
		$cols = array('created_at','item_name','added_by','transaction_type','adjustment','stock_after');
    	$resp = DatatablesWrapper::format($cols,$this->data);
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';
				
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";		
		
		$and.= " AND item_name <>''";
		
		if(!empty($range1) && !empty($range2)){
			$range2 = date('Y-m-d', strtotime($range2 . ' +1 day'));
			$and.= "\nAND created_at BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."  ";
		}		
		
		$in_reason ='';
		if(isset($this->data['reason'])){
			if(!in_array('all',(array)$this->data['reason'])){
				foreach ($this->data['reason'] as $reason) {
					$in_reason.= FunctionsV3::q($reason).",";
				}
				$in_reason = substr($in_reason,0,-1);
				$and.="\nAND transaction_type IN ($in_reason)";
			}
		}
		
		$in_user ='';
		if(isset($this->data['user'])){
			if(!in_array('all',(array)$this->data['user'])){
				foreach ($this->data['user'] as $user) {
					$in_user.= FunctionsV3::q($user).",";
				}
				$in_user = substr($in_user,0,-1);
				$and.="\nAND added_by IN ($in_user)";
			}
		}
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( item_name LIKE ".FunctionsV3::q("%$search_field%")."			 
			 OR sku LIKE ".FunctionsV3::q("%$search_field%")."
			 )";
		}
				
		$order="ORDER BY stock_id DESC";
		//dump($order);
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		*
		FROM {{view_inventory_stocks}} 
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
			
			foreach ($res as $val) {								
				$cols_data = array();								
				foreach ($cols as $key_cols=> $cols_val) {		
					if($key_cols==0){
						$cols_data[$cols_val] = FunctionsV3::prettyDate($val[$cols_val])." ".FunctionsV3::prettyTime($val[$cols_val]) ;
				    } elseif ( $key_cols==1 ){
				    	  //$cols_data[$cols_val] = ItemWrap::prettyName($val['item_name'],$val['size_name']);
				    	  $cols_data[$cols_val] = InventoryWrapper::prettyItemName($val['item_name'],$val['size_name'],$val['sku']);
					} elseif ( $key_cols==3 ){
						$cols_data[$cols_val] = StocksWrapper::prettyTransactionStatusWithRef(
						  $val[$cols_val],$val['reference'], $val['transaction_code'], $val['transaction_id'], $val['remarks']);
					} elseif ( $key_cols==4 || $key_cols==5){
						$cols_data[$cols_val]=InventoryWrapper::prettyQuantity($val[$cols_val]);
					} else $cols_data[$cols_val]=$val[$cols_val];
				}
				$datas[]=$cols_data;
			}								
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);	
		} else $this->otableNodata();		
	}
	
	public function actionValuation()
	{
		
		$feed_data = array();
		
		$cols = array('item_name','available_stocks','cost_price','inventory_value','price','retail_value','potential_profit');
    	$resp = DatatablesWrapper::format($cols,$this->data);
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		$and.= " AND status IN ('publish') AND track_stock='1' ";
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		*
		FROM {{view_item_stocks}} 
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
			
			foreach ($res as $val) {				
				$cols_data = array();					
				$inventory_value = 0; $retail_value = 0; $potential_profit=0;
				
				foreach ($cols as $key_cols=> $cols_val) {												
					
					if($cols_val=="inventory_value"){	
						if($val['available_stocks']>0){
							$inventory_value = (float)$val['available_stocks'] * (float) $val['cost_price'];			
							$cols_data[$cols_val] = FunctionsV3::prettyPrice($inventory_value);
						} else $cols_data[$cols_val]=0;
						
					} elseif ($cols_val=="retail_value"){
						if($val['available_stocks']>0){
						$retail_value = (float)$val['available_stocks'] * (float) $val['price'];
						$cols_data[$cols_val] = FunctionsV3::prettyPrice($retail_value);
						} else  $cols_data[$cols_val]=0;
						
					} elseif ($cols_val=="potential_profit"){	
						$potential_profit = (float)$retail_value - (float)$inventory_value;
						$cols_data[$cols_val] = FunctionsV3::prettyPrice($potential_profit);
						
					} else {
						switch ($key_cols) {
							case 0:
								$cols_data[$cols_val] = InventoryWrapper::prettyItemName($val['item_name'],$val['size_name'],$val['sku']);
								break;
								
							case 1:	
							    $cols_data[$cols_val]= InventoryWrapper::prettyQuantity($val[$cols_val]);
							   break;
							   
							case 2:							
							case 4:								
								 $cols_data[$cols_val]= FunctionsV3::prettyPrice($val[$cols_val]);
								break;
						
							default:
								$cols_data[$cols_val]=$val[$cols_val];								
								break;
						}
					}
				}
				$datas[]=$cols_data;
			}								
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);	
		} else $this->otableNodata();		
	}
		
	
	public function actionAutoFillItem()
	{
		$type = isset($this->data['type']) ? $this->data['type'] :'';
		$supplier_id = isset($this->data['supplier_id']) ? (integer) $this->data['supplier_id'] :'';
		
		try {
		   	
			$data = ItemWrap::autoFillItem($this->merchant_id, $supplier_id , $type);
			$this->OKresponse();
			$this->details = array(
			 'next_action'=>"auto_fill_purchase",
			 'data'=>$data
			);
			
		} catch (Exception $e) {
           $this->msg = translate($e->getMessage());
           $this->details = array(
		     'error_type'=>'silent',		     
		    );
        }
		$this->jsonResponse();
	}
	
}
/*end class*/