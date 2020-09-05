<?php
class AjaxadminController extends CController
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
		if(!UserWrapper::validToken()){
			return false;
		}				
		
		$user_type = UserWrapper::getUserType();
		if($user_type!="admin"){
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
	
	public function actionGeneral()
	{
		
		Yii::app()->functions->updateOptionAdmin('inventory_email_notify',
		isset($this->data['inventory_email_notify'])? InventoryWrapper::purify($this->data['inventory_email_notify']) :''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_hide_out_stock',
		isset($this->data['inventory_hide_out_stock'])?$this->data['inventory_hide_out_stock']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_show_stock',
		isset($this->data['inventory_show_stock'])?$this->data['inventory_show_stock']:1
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_allow_negative_order',
		isset($this->data['inventory_allow_negative_order'])?$this->data['inventory_allow_negative_order']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_low_stock_notify',
		isset($this->data['inventory_low_stock_notify'])?$this->data['inventory_low_stock_notify']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_negative_stock_notify',
		isset($this->data['inventory_negative_stock_notify'])?$this->data['inventory_negative_stock_notify']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_in_stock',
		isset($this->data['inventory_in_stock'])?$this->data['inventory_in_stock']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_low_stock',
		isset($this->data['inventory_low_stock'])?$this->data['inventory_low_stock']:''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_items_left',
		isset($this->data['inventory_items_left'])?$this->data['inventory_items_left']:''
		);
		
		$this->OKresponse(); $this->msg = translate("Settings saved");
		$this->jsonResponse();
	}
	
	public function actionSettings_reports()
	{
		Yii::app()->functions->updateOptionAdmin('inventory_reports_default_status',
		isset($this->data['inventory_reports_default_status'])?json_encode($this->data['inventory_reports_default_status']):''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_accepted_order_status',
		isset($this->data['inventory_accepted_order_status'])?json_encode($this->data['inventory_accepted_order_status']):''
		);
		
		Yii::app()->functions->updateOptionAdmin('inventory_cancel_order_status',
		isset($this->data['inventory_cancel_order_status'])?json_encode($this->data['inventory_cancel_order_status']):''
		);
		
		$this->OKresponse(); $this->msg = translate("Settings saved");
		$this->jsonResponse();
	}
	
	public function actionMerchant_list()
	{
				
    	$feed_data = array();
    	
    	$cols = array('restaurant_name','merchant_id','status','inventory_enabled','inventory_role_id');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = '';
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( restaurant_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.merchant_id,
		a.restaurant_name, 
		a.status, 
		a.inventory_enabled, 
		a.inventory_role_id,
		b.role_name
		
		 FROM
		{{merchant}}  a
		LEFT JOIN {{inventory_access_role}} b
		ON
		a.inventory_role_id = b.role_id
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
				foreach ($cols as $key_cols=> $cols_val) {					
					switch ($key_cols) {
						case 2:
							$cols_data[$cols_val]='<div class="badge badge-light">'.translate($val[$cols_val])."</div>";
							break;
					
						case 3:
							$cols_data[$cols_val]= '<div>'. ItemHtmlWrapper::formSwitch('inventory_enabled','',
							$val['inventory_enabled']==1?true:false
							,array(
							 'class'=>"inventory_enabled",
							 'value'=>$val['merchant_id']
							)) .'</div>';
							break;
								
						case 4:	
						    $role =  (array)InventoryWrapper::dropdownFormat(
							   UserWrapper::getAccessRole(),'role_id','role_name',
							   array(
							    '0'=>translate("Select role")
							   )
							);						
						    $attributes = 'class="inline_edit" ';
							$inline_form = $this->renderPartial(APP_FOLDER.'.views.adm.inline_role',array(
							    'inventory_role_id'=>$val['inventory_role_id'],
							    'merchant_id'=>$val['merchant_id'],
							    'action'=>"inline_access_role",
							    'role'=>$role
							),true);
							if(empty($val['role_name'])){
								$cols_data[$cols_val]= '<span '.$attributes.'>'.translate("none").$inline_form.'</span>';
							} else $cols_data[$cols_val]= '<span '.$attributes.'>'.translate($val['role_name']).$inline_form.'</span>';						    
						    break;
						    
						default:
							$cols_data[$cols_val]=$val[$cols_val];
							break;
					}					
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
	public function actionAllowaccess()
	{
		$merchant_id = isset($this->data['merchant_id']) ? (integer) $this->data['merchant_id'] :0;
		$enabled = isset($this->data['enabled']) ? (integer) $this->data['enabled'] :0;
		if($merchant_id>0){			
			try {
				$params = array(
				  'inventory_enabled'=>$enabled,
				  'date_modified'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
				MerchantWrapper::updateAllowAccess($merchant_id, $params);
				$this->OKresponse();
				$this->details = array(
				  'next_action'=>"silent"
				);
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("Invalid merchant id");
		$this->jsonResponse();
	}
	
	public function actionAccess_rights()
	{
				
    	$feed_data = array();
    	
    	$cols = array('role_id','role_name','access','user_count');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = '';
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( a.role_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.role_id, a.role_name, a.access,
		(
		 select count(*) from {{user_master_list}}
		 where role_id = a.role_id
		) as user_count
		FROM
		{{inventory_access_role}}  a
		WHERE 1		
		$and
		$where
		$order
		$limit
		";		
		//dump($stmt);
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";			
			if($resc = Yii::app()->db->createCommand($stmtc)->queryAll()){
				$total_records=$resc[0]['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/adm/create_role',array('id'=>$val['role_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					if($key_cols==2){
						
						$count = 0;
						if(!empty($val[$cols_val])) {
							$t = json_decode($val[$cols_val]);
							if(is_array($t) && count($t)>=1){
								$count = count($t);
							}
						}  
						
						$cols_data[$cols_val] = $count;
						
					} else $cols_data[$cols_val]=$val[$cols_val];					
				}
				$datas[]=$cols_data;
			}	
						
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}

	public function actionCreate_role()
	{
		$params = array(
		   'role_name'=>isset($this->data['role_name'])?$this->data['role_name']:'',
		   'access'=>isset($this->data['access']) ? json_encode($this->data['access']) : '',
		   'created_at'=>FunctionsV3::dateNow(),
		   'updated_at'=>FunctionsV3::dateNow(),
		   'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
		
		if(empty($params['access'])){
			$this->msg = translate("Invalid access");
			$this->jsonResponse();
		}
		
		$id = isset($this->data['row_id'])? (float) $this->data['row_id']:0;
		
		if($id>0){
			unset($params['created_at']);
		} else unset($params['updated_at']);
		
		try {
						
			
			$params = InventoryWrapper::purifyData($params);
			
			UserWrapper::insertAccessRole($params,(integer)$id);			
			$this->OKresponse();
			$this->msg = translate("Successful");
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/adm/access_rights')
			);
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();
	}

	public function actionDelete_access_role()
	{
		if (isset($this->data['row_id'])){
			try {
				
								
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
												
				UserWrapper::deleteRole($ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/adm/access_rights')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionItemStockAlert()
	{
		try {
			
			$data = array();
			
			$stmt="
			SELECT b.restaurant_name as merchant ,a.item_name,a.size_name,a.sku,a.available_stocks
			FROM {{view_item_stocks_status}} a
			LEFT JOIN {{merchant}} b
			ON 
			a.merchant_id = b.merchant_id
			WHERE 			
			stock_status IN ('Low stock','Out of stocks')
			AND item_size_id > 0
			ORDER BY a.available_stocks ASC
			LIMIT 0,20
			";
						
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				$res = Yii::app()->request->stripSlashes($res);				
				foreach ($res as $key=>$val) {				
					$colors = ReportsWrapper::randomColors($key);
					$data[]=array(
					  'merchant'=>$val['merchant'],
					  'item_name'=>InventoryWrapper::prettyItemName($val['item_name'],$val['size_name'],$val['sku']),
					  'value'=>InventoryWrapper::prettyQuantity($val['available_stocks']),
					  'color'=>$colors
					);
				}				
				$this->OKresponse();
				$this->details = array(
				 'next_action'=>'stock_alert',
				 'data'=>$data
				);						
			} else {
				$this->msg = translate("No results");
				$this->details = array('error_type'=>"silent");
			}
		} catch (Exception $e) {
		   $this->msg = translate($e->getMessage());
		   $this->details = array('error_type'=>"silent");
		}		
		$this->jsonResponse();
	}
	
	
	public function actionSaleslast30days()
	{
		try {
			
			$days = InventoryWrapper::reportsRange();
			$data = array();
			$datenow = date("Y-m-d"); 
			$range2 = date("Y-m-d"); 
			$range1 = date("Y-m-d",strtotime( $datenow . "-$days day"));
			
			$and='';
			
			$status = ReportsWrapper::getDefaultStatus();
			if(is_array($status) && count($status)>=1){
				$in_status = '';		
				foreach ($status as $stats_val) {
					$in_status.= FunctionsV3::q($stats_val).",";
				}
				$in_status = substr($in_status,0,-1);
				$and.=" AND status IN ($in_status)";
			}
			
			$stmt="
			select SQL_CALC_FOUND_ROWS
			a.merchant_id,
			a.sale_date,
			sum(a.total_sale) as gross_sale,
			sum(a.discount) as discount,
			(sum(a.total_sale)-sum(a.discount)) as net_sale,
			sum(a.total_cost) as total_cost,
			
			(sum(a.total_sale)-sum(a.discount)) -  sum(a.total_cost) as gross_profit
			
			from {{view_inventory_order_details}} a
			WHERE 1					
			AND sale_date  BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."
			$and
			group by a.sale_date,a.merchant_id			
			";				
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){				
				foreach ($res as $key=>$val) {				
					$data[]=array(
					  'sale_date'=>InventoryWrapper::prettyDate($val['sale_date'],"[M] d"),
					  'net_sale'=>FunctionsV3::prettyPrice($val['net_sale'])
					);
				}
				
				$this->OKresponse();
				$this->details = array(
				 'next_action'=>'sales_last30',
				 'data'=>$data
				);									
			} else {
				$this->msg = translate("No results");
				$this->details = array('error_type'=>"silent");
			}
			
			//dump($data);
			
		} catch (Exception $e) {
		   $this->msg = translate($e->getMessage());
		   $this->details = array('error_type'=>"silent");
		}		
		$this->jsonResponse();
	}	
	
	
	public function actionSales_summary_chart()
	{		
		
		$chart_type = isset($this->data['chart_type'])?$this->data['chart_type']:'';
		
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';
		
		$date = array(); $data = array(); $series = array(); $summary=array();
						
		try {
		    $date = ReportsWrapper::generateDateRange($range1,$range2);
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		    $this->jsonResponse();
		}		
							
		$and = "";
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';		
		if( InventoryWrapper::validDate($range1) && InventoryWrapper::validDate($range2)){
			$and.="AND sale_date  BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."  ";
		}

		if(isset($this->data['status'])){
			if(!in_array('all',(array)$this->data['status'])){		
				$in_status = '';		
				foreach ($this->data['status'] as $stats_val) {
					$in_status.= FunctionsV3::q($stats_val).",";
				}
				$in_status = substr($in_status,0,-1);
				$and.=" AND status IN ($in_status)";
			}
		}
		
		$series_stmt = ''; $chart_title='';
		switch ($chart_type) {
			case "discount":			
			    $series_stmt="sum(a.discount) as value";	
			    $chart_title= translate("Discount");
				break;
				
		    case "net_sales":		
		        $series_stmt="(sum(a.total_sale)-sum(a.discount)) as value";
		        $chart_title= translate("Net sales");
				break;
				
			case "gross_profit":			
			   $series_stmt="(sum(a.total_sale)-sum(a.discount)) -  sum(a.total_cost) as value";
			   $chart_title= translate("Gross profit");
				break;	
				
			default:
				$series_stmt="sum(a.total_sale) as value";
				$chart_title= translate("Gross sales");
				break;
		}
		
		$stmt="
		select
		DATE_FORMAT(a.sale_date,'%b %d') as date,		
		$series_stmt
		from {{view_inventory_order_details}} a
		WHERE 1		
		$and
		group by a.sale_date
		";		
			
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {
				$data[$val['date']] = $val['value'];
			}
		}		
				
		if(is_array($date) && count($date)>=1){
			foreach ($date as $date_val) {				
				if(array_key_exists($date_val,$data)){
					$series[] = (float) normalPrettyPrice($data[$date_val]) ;
				} else $series[] = 0;
			}				
			
			$stmt="
			select
			sum(a.total_sale) as gross_sale,
			sum(a.discount) as discount,
			(sum(a.total_sale)-sum(a.discount)) as net_sales,		    
		    (sum(a.total_sale)-sum(a.discount)) -  sum(a.total_cost) as gross_profit
			from {{view_inventory_order_details}} a
			WHERE 1		
		    $and
			";			
			if($resp = Yii::app()->db->createCommand($stmt)->queryRow()){
			    foreach ($resp as $resp_key=>$resp_val) {
			    	$summary[$resp_key]=FunctionsV3::prettyPrice($resp_val);
			    }
			}			
						
			$this->code = 1;
			$this->msg = "ok";
			$this->details = array(
			  'next_action'=>'sales_summary_chart',
			  'categories'=>$date,
			  'series'=>$series,
			  'chart_title'=>$chart_title,
			  'summary'=>(array)$summary
			);					
		} else $this->msg = translate("invalid date");		
		$this->jsonResponse();
	}	
	
	public function actionLow_stock_logs()
	{
				
    	$feed_data = array();
    	
    	$cols = array('id','merchant','item_name','available_stocks','date_process');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "";
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( item_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS id,merchant,item_name,available_stocks,date_process
		FROM
		{{view_inventory_lowstock_notification}} 
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
				//$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/item/size_add',array('id'=>$val['size_id']));
				foreach ($cols as $key_cols=> $cols_val) {	
					switch ($key_cols) {
						
						case 3:
							$cols_data[$cols_val]= InventoryWrapper::prettyQuantity($val[$cols_val]);
							break;
							
						case 4:
							$cols_data[$cols_val]= InventoryWrapper::prettyDate($val[$cols_val]);
							break;
					
						default:
							$cols_data[$cols_val]=$val[$cols_val];
							break;
					}									
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}

	public function actionDelete_lowstock_logs()
	{
		if (isset($this->data['row_id'])){
			try {
								
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
								
				ReportsWrapper::deleteLowStockLogs($ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> 'refresh_table',
				 'redirect'=>Yii::app()->createUrl('inventory/adm/low_stock_logs')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionCheckData()
	{
		$links = array();
		
		/*ITEM*/
		$total = InstallWrapper::getItemsCountMigrate();
				
		if($total>0){
			$link = Yii::app()->createUrl('inventory/adm/update_data');	
			$message = translate("There is ([count]) items that need to update",array(
		     '[count]'=>$total
		   ));
			$links[]=array(
			   '<a class="dropdown-item" href="'.$link.'">'.$message.'</a>'
			);
		}		
		
		/*REPORT*/	
		$total_item = ReportsWrapper::totalFixedReport();
		if($total_item>0){
			$link = Yii::app()->createUrl('inventory/adm/fixedreport');	
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
				//Yii::app()->db->createCommand("DELETE FROM {{item_relationship_category}} WHERE item_id =".q($item_id)." ")->query();
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
				$next_url =  Yii::app()->createUrl(APP_FOLDER.'/index/dashboard');
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
			$next_url =  Yii::app()->createUrl(APP_FOLDER.'/index/dashboard');
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
	
	
	public function actionTable_update()
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
						
						if( !InstallWrapper::isTableView($table_name)){
							Yii::app()->db->createCommand()->dropTable($table_name);
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
		} else $stats = $stats = "<span class=\"text-danger\">Index not found</span>";


		$message = $type." ".translate("table [table] ... [stats]",array(
		 '[table]'=>$table_name,
		 '[stats]'=>$stats
		));	
				
		
		if($counter>=$total_table){
			$next_action = 'done';
		}
		
		if($next_action=="done"){			
			$next_url =  Yii::app()->createUrl(APP_FOLDER.'/adm/databaseupdate');
			$message = '<a href="'.$next_url.'" class="btn btn-raised btn-primary" >'.translate("Done").'</a>';			
		}
				
		
		$this->OKresponse();
		$this->details = array(
		  'next_action'=>$next_action,
		  'counter'=>$counter+1,
		  'message'=>$message
		);
		$this->jsonResponse();
	}
	
	public function actionInline_access_role()
	{		
		$role = isset($this->data['inline_value'])?(integer)$this->data['inline_value']:0;
		$merchant_id = isset($this->data['inline_id'])?(integer)$this->data['inline_id']:0;
		
		if($merchant_id>0 && $role>0){
			try {
				
				$params = array(
				  'inventory_role_id'=>(integer)$role,
				  'date_modified'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
								
				Yii::app()->db->createCommand()->update("{{merchant}}",$params,
	          	    'merchant_id=:merchant_id',
	          	    array(
	          	      ':merchant_id'=>$merchant_id
	          	    )
	          	);
	          		          	
	          	$this->OKresponse();
				$this->msg = translate("Record updated");
				$this->details = array(
				 'next_action'=> 'refresh_table',				 
				);	          	
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("Invalid role id or merchant id");
		$this->jsonResponse();
	}
	
}/* end class*/