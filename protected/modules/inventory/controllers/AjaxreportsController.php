<?php
class AjaxreportsController extends CController
{
	//public $layout = APP_FOLDER.'.views.layouts.empty';
	
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
	
	public function actionSales_summary()
	{
    	$feed_data = array();
    	    			
    	$cols = array('sale_date','gross_sale','discount','net_sale','total_cost','gross_profit');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
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
		$and
		$where				
		group by a.sale_date,a.merchant_id		
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
						case 0:	
						    $cols_data[$cols_val] = InventoryWrapper::prettyDate($val[$cols_val],"M d");
							break;
					
						default:							
							$cols_data[$cols_val]= FunctionsV3::prettyPrice($val[$cols_val]);
							break;
					}					
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
	public function actionSales_item()
	{
		$feed_data = array();
    	    					
    	$cols = array('item_name','categories_name','item_sold','discount','net_sale','total_cost','gross_profit');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
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
		
		$order = "ORDER BY item_name ASC";
		
		$stmt="		
		select SQL_CALC_FOUND_ROWS
		a.sku,
		a.item_name,
		a.size_name,
		a.discount,
		a.category_name as categories_name,		
		sum(a.qty) as item_sold,
		sum(a.total_sale) - sum(a.discount) as net_sale,
		sum(a.total_cost) as total_cost,
		
		(sum(a.total_sale)-sum(a.discount)) - sum(a.total_cost) as gross_profit
		
		from {{view_inventory_order_details}} a						
		
		WHERE 1		
		$and
		$where
		group by a.sku
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
						case 0:
							$cols_data[$cols_val] = InventoryWrapper::prettyItemName($val[$cols_val],$val['size_name'],$val['sku']);
							break;
							
						case 1:	
						    $cols_data[$cols_val] = $val[$cols_val];
							break;
							
						case 2:	
						    $cols_data[$cols_val] = InventoryWrapper::prettyQuantity($val[$cols_val]);
							break;
					
						default:							
							$cols_data[$cols_val]= FunctionsV3::prettyPrice($val[$cols_val]);
							break;
					}					
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
    public function actionSales_category()
	{
		$feed_data = array();
    	    					
    	$cols = array('category_name','item_sold','discount','net_sale','total_cost','gross_profit');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
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
		
		$order = "ORDER BY category_name ASC";
				
		
		$stmt="
		select
		SQL_CALC_FOUND_ROWS
		a.category_name,
		sum(a.qty) as item_sold,
		sum(a.discount) as discount,
		sum(a.total_sale) - sum(a.discount) as net_sale,
		sum(a.total_cost) as total_cost,
		(sum(a.total_sale)-sum(a.discount)) - sum(a.total_cost) as gross_profit
		from {{view_inventory_order_details}} a
		where 1
		$and
		$where
		group by a.cat_id
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
						case 0:
							$cols_data[$cols_val] = $val[$cols_val];
							break;
													
						case 1:	
						case 2:	
						    $cols_data[$cols_val] = InventoryWrapper::prettyQuantity($val[$cols_val]);
							break;
					
						default:							
							$cols_data[$cols_val]= FunctionsV3::prettyPrice($val[$cols_val]);
							break;
					}					
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}	
	
	public function actionSales_payment_type()
	{
		$feed_data = array();
    	    					
    	$cols = array('payment_type','payment_transaction','net_amount');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';		
		if( InventoryWrapper::validDate($range1) && InventoryWrapper::validDate($range2)){
			$and.="AND DATE_FORMAT(a.date_created,'%Y-%m-%d')  BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."  ";
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
				
		$stmt="
		select
		SQL_CALC_FOUND_ROWS
		a.payment_type,
		count(*) as payment_transaction,
		sum(a.total_w_tax) as net_amount
		from {{order}} a
		where 1
		$and
		$where
		group by a.payment_type 
		$order
		$limit
		";
		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
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
						case 0:
							$cols_data[$cols_val] = t($val[$cols_val]);
							break;
													
						case 1:							
						    $cols_data[$cols_val] = InventoryWrapper::prettyQuantity($val[$cols_val]);
							break;
					
						default:							
							$cols_data[$cols_val]= FunctionsV3::prettyPrice($val[$cols_val]);
							break;
					}					
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
    public function actionSales_receipt()
	{
		$feed_data = array();
    	    					
    	$cols = array('order_id','date_created','trans_type','customer_name','payment_type','request_from','status','total_w_tax');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';		
		if( InventoryWrapper::validDate($range1) && InventoryWrapper::validDate($range2)){
			$and.="AND DATE_FORMAT(a.date_created,'%Y-%m-%d')  BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."  ";
		}

		if(isset($this->data['status'])){
			if(!in_array('all',(array)$this->data['status'])){		
				$in_status = '';		
				foreach ($this->data['status'] as $stats_val) {
					$in_status.= FunctionsV3::q($stats_val).",";
				}
				$in_status = substr($in_status,0,-1);
				$and.=" AND a.status IN ($in_status)";
			}
		}				
		
		
        $search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( order_id = ".FunctionsV3::q("$search_field")." )";
		}	
					
		$stmt="
		select 
		SQL_CALC_FOUND_ROWS
		a.order_id,
		a.date_created,
		a.trans_type,
		concat(b.first_name,' ',b.last_name) as customer_name,
		a.payment_type,
		a.request_from,
		a.status,
		a.total_w_tax
		from {{order}} a
		left join {{client}} b
		on
		a.client_id = b.client_id
		where 1
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
				$cols_data["DT_RowId"] = $val['order_id'];
				foreach ($cols as $key_cols=> $cols_val) {					
					switch ($key_cols) {
						case 1:
							$cols_data[$cols_val] = FunctionsV3::prettyDate($val[$cols_val])." ".FunctionsV3::prettyTime($val[$cols_val]);
							break;
						
						case 4:			
						case 5:
						case 6:	
						    $cols_data[$cols_val] = '<div class="badge badge-light">'.t($val[$cols_val]).'</div>';
						  break;
						  
						case 7:							
							$cols_data[$cols_val]= FunctionsV3::prettyPrice($val[$cols_val]);
							break;
															
						default:							
							$cols_data[$cols_val] = t($val[$cols_val]);
							break;
					}					
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}	
	
	public function actionSales_by_addon()
	{
		$feed_data = array();
    	    					
    	$cols = array('sub_item_name','qty_sold','gross_sale');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = "AND merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';		
		if( InventoryWrapper::validDate($range1) && InventoryWrapper::validDate($range2)){
			$and.="AND DATE_FORMAT(date_created,'%Y-%m-%d')  BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."  ";
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
					
		$stmt="		
		select 
		sub_item_name,
		count(*) as qty_sold,
		sum(total) as gross_sale
		from
		{{view_inventory_sales_addon}}
		where 1
		$and
		$where
		group by sub_item_name
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
						case 1:
							$cols_data[$cols_val]= InventoryWrapper::prettyQuantity($val[$cols_val]);
							break;
														
						case 2:
							$cols_data[$cols_val]= FunctionsV3::prettyPrice($val[$cols_val]);
							break;
																			
						default:							
							$cols_data[$cols_val] = t($val[$cols_val]);
							break;
					}					
				}
				$datas[]=$cols_data;
			}			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
	public function actionReceipt()
	{
		
		$order_id = (integer) isset($_POST['order_id'])?$_POST['order_id']:'';
		if($order_id>0){			
			try {				
				$resp = PrintWrapper::prepareReceipt($order_id);
				$resp = Yii::app()->request->stripSlashes($resp);
				$html = $this->renderPartial(APP_FOLDER.'.views.reports.receipt',array(
				  'data'=>$resp
				),true);
				
				$this->OKresponse();
				$this->details = array(
				  'html'=>$html,
				  'next_action'=>'show_receipt'
				);
				
			} catch (Exception $e) {
			    $this->msg = translate($e->getMessage());
			}		
		} else $this->msg = translate("Invalid order id");
		$this->jsonResponse();
	}

	public function actionsales_summary_chart()
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
							
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
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
	
	public function actionChart_sales_item()
	{
		$chart_title = translate("Sales by item");
		
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';
		$chart_type = isset($this->data['chart_type'])?$this->data['chart_type']:'';
		
		$date = array(); $data = array(); $series = array(); $summary=array();
						
		try {
		    $date = ReportsWrapper::generateDateRange($range1,$range2,false);
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		    $this->jsonResponse();
		}		
							
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';		
		if( InventoryWrapper::validDate($range1) && InventoryWrapper::validDate($range2)){
			$and.="AND sale_date  BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."  ";
		}

		$and_status = '';
		if(isset($this->data['status'])){
			if(!in_array('all',(array)$this->data['status'])){		
				$in_status = '';		
				foreach ($this->data['status'] as $stats_val) {
					$in_status.= FunctionsV3::q($stats_val).",";
				}
				$in_status = substr($in_status,0,-1);
				$and.=" AND status IN ($in_status)";
				$and_status.=" AND status IN ($in_status)";
			}
		}
		
		if($chart_type=="pie"){
			$stmt="
			select		
			a.sku,
			a.item_name,
			a.size_name,
			DATE_FORMAT(a.sale_date,'%b %d') as date,		
			sum(a.total_sale) - sum(a.discount) as value
			from {{view_inventory_order_details}} a
			WHERE 1		
			$and
			group by a.sku
			order by sum(a.total_sale) - sum(a.discount) DESC
			";
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				foreach ($res as $key=>$val) {
					$colors = ReportsWrapper::randomColors($key);
					
					$item_name = $val['item_name'];
					if(!empty($val['size_name'])){
						$item_name = $val['item_name']." (".$val['size_name'].")"; 
					}
					
					$series[]=array(
		    		 'name'=>$item_name,
		    		 'y'=>(float) normalPrettyPrice($val['value']),
		    		 'color'=>$colors
		    		);
				}
			}
		} else {
			$stmt="
			select		
			a.sku,
			a.item_name,
			a.size_name			
			from {{view_inventory_order_details}} a
			WHERE 1		
			$and
			group by a.sku
			order by sum(a.total_sale) - sum(a.discount) DESC
			";		
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				foreach ($res as $key=>$val) {
										
					$colors = ReportsWrapper::randomColors($key);
					
					$stmt_data="
					select		
					a.sku,
					a.item_name,
					a.size_name,
					DATE_FORMAT(a.sale_date,'%b %d') as date,		
					sum(a.total_sale) - sum(a.discount) as value
					from {{view_inventory_order_details}} a
					WHERE 1		
					AND a.sku=".FunctionsV3::q($val['sku'])."
					$and
					group by a.sale_date
					order by a.sale_date ASC
					";							
					
					$resp = Yii::app()->db->createCommand($stmt_data)->queryAll();					
										
					foreach ($date as $date_val) {						
						$key = array_search($date_val, array_column( (array) $resp, 'date'));					
						if(is_numeric($key)){
							$data[]=(float) normalPrettyPrice($resp[$key]['value']);
						} else $data[]=0;
					}
					
					$item_name = $val['item_name'];
					if(!empty($val['size_name'])){
						$item_name = $val['item_name']." (".$val['size_name'].")"; 
					}
					$series[$item_name]=array(
					  'color'=>$colors, 
					  'data'=>$data					  
					);		
					$data=array();
				}
			} else {
				// no data
				foreach ($date as $date_val) {
					$data[]=0;
				}					
				$series['']=array(
				  'color'=>'#78909C', 
				  'data'=>$data
				);		
			}		
		}
		
		$this->code = 1;
		$this->msg = "ok";
		$this->details = array(
		  'next_action'=>'chart_sales_item',
		  'categories'=>$date,
		  'series'=>$series,
		  'chart_title'=>$chart_title,					  
		);
		
		$this->jsonResponse();
	}
	
	public function actionChart_sales_category()
	{
		$chart_title = translate("Sales by category");
		
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';
		$chart_type = isset($this->data['chart_type'])?$this->data['chart_type']:'';
		
		$date = array(); $data = array(); $series = array(); $summary=array();
						
		try {
		    $date = ReportsWrapper::generateDateRange($range1,$range2);
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		    $this->jsonResponse();
		}		
							
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
		$range1 = isset($this->data['range1'])?$this->data['range1']:'';
		$range2 = isset($this->data['range2'])?$this->data['range2']:'';		
		if( InventoryWrapper::validDate($range1) && InventoryWrapper::validDate($range2)){
			$and.="AND sale_date  BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."  ";
		}

		$and_status = '';
		if(isset($this->data['status'])){
			if(!in_array('all',(array)$this->data['status'])){		
				$in_status = '';		
				foreach ($this->data['status'] as $stats_val) {
					$in_status.= FunctionsV3::q($stats_val).",";
				}
				$in_status = substr($in_status,0,-1);
				$and.=" AND status IN ($in_status)";
				$and_status.=" AND status IN ($in_status)";
			}
		}
		
		if($chart_type=="pie"){
			$stmt="
			select		
			a.category_name,		
			DATE_FORMAT(a.sale_date,'%b %d') as date,		
			sum(a.total_sale) - sum(a.discount) as value
			from {{view_inventory_order_details}} a
			WHERE 1		
			$and
			group by a.cat_id
			order by sum(a.total_sale) - sum(a.discount) DESC
			";		
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				foreach ($res as $key=>$val) {
					$colors = ReportsWrapper::randomColors($key);					
					$item_name = $val['category_name'];					
					$series[]=array(
		    		 'name'=>$item_name,
		    		 'y'=>(float) normalPrettyPrice($val['value']),
		    		 'color'=>$colors
		    		);
				}
			}
		} else {
			$stmt="
			select		
			a.cat_id,
			a.category_name,		
			DATE_FORMAT(a.sale_date,'%b %d') as date,		
			sum(a.total_sale) - sum(a.discount) as value
			from {{view_inventory_order_details}} a
			WHERE 1		
			$and
			group by a.cat_id
			order by sum(a.total_sale) - sum(a.discount) DESC
			";					
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				
				foreach ($res as $key=>$val) {
										
					$colors = ReportsWrapper::randomColors($key);
					
					$stmt_data="
					select		
					a.category_name,		
					DATE_FORMAT(a.sale_date,'%b %d') as date,		
					sum(a.total_sale) - sum(a.discount) as value
					from {{view_inventory_order_details}} a
					WHERE 1		
					AND a.cat_id=".FunctionsV3::q($val['cat_id'])."
					$and
					group by a.sale_date
					order by a.sale_date ASC
					";							
										
					$resp = Yii::app()->db->createCommand($stmt_data)->queryAll();	
										
					foreach ($date as $date_val) {						
						$key = array_search($date_val, array_column( (array) $resp, 'date'));					
						if(is_numeric($key)){
							$data[]=(float) normalPrettyPrice($resp[$key]['value']);
						} else $data[]=0;
					}
					
					$item_name = $val['category_name'];
					
					$series[$item_name]=array(
					  'color'=>$colors, 
					  'data'=>$data					  
					);		
					$data=array();
				}
			} else {
				// no data
				foreach ($date as $date_val) {
					$data[]=0;
				}					
				$series['']=array(
				  'color'=>'#78909C', 
				  'data'=>$data
				);		
			}		
		}
		
		$this->code = 1;
		$this->msg = "ok";
		$this->details = array(
		  'next_action'=>'chart_sales_item',
		  'categories'=>$date,
		  'series'=>$series,
		  'chart_title'=>$chart_title,					  
		);
		//dump($this->details);	
		
		$this->jsonResponse();
	}
	
	public function actionItemStockAlert()
	{
		try {
			
			$data = array();
			
			$stmt="
			SELECT item_name,size_name,sku,available_stocks
			FROM {{view_item_stocks_status}}
			WHERE 
			merchant_id=".FunctionsV3::q($this->merchant_id)."
			AND
			stock_status IN ('Low stock','Out of stocks')
			ORDER BY available_stocks ASC
			LIMIT 0,10
			";
			
			$res = Yii::app()->db->createCommand($stmt)->queryAll();
			foreach ($res as $key=>$val) {				
				$colors = ReportsWrapper::randomColors($key);
				$data[]=array(
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
			
		} catch (Exception $e) {
		   $this->msg = translate($e->getMessage());
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
			
			$status = ReportsWrapper::getDefaultStatus($this->merchant_id);
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
			AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)."			
			AND sale_date  BETWEEN ".FunctionsV3::q($range1)." AND ".FunctionsV3::q($range2)."
			$and
			group by a.sale_date,a.merchant_id					
			
			";			
			
			$res = Yii::app()->db->createCommand($stmt)->queryAll();
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
			
		} catch (Exception $e) {
		   $this->msg = translate($e->getMessage());
		}		
		$this->jsonResponse();
	}
	
	public function actionTopItems()
	{
		$top_5=array();
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
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
				
		$stmt="		
		select SQL_CALC_FOUND_ROWS
		a.sku,
		a.item_name,
		a.size_name,
		a.discount,
		a.category_name as categories_name,				
		sum(a.total_sale) - sum(a.discount) as net_sale		
		from {{view_inventory_order_details}} a						
		
		WHERE 1		
		$and		
		group by a.sku
		order by sum(a.total_sale) - sum(a.discount) DESC
		LIMIT 0,5		
		";
				
		//dump($stmt);
		$this->OKresponse();
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			foreach ($res as $key=>$val) {
				$colors = ReportsWrapper::randomColors($key);
				$top_5[] = array(
	    		  'color'=>$colors,
	    		  'item_name'=>InventoryWrapper::prettyItemName(
	    		   $val['item_name'],$val['size_name'],$val['sku']
	    		  ),
	    		  'value'=>normalPrettyPrice($val['net_sale'])
	    		);									
			}
			$this->details = array(  
			   'next_action'=>'show_top_items',
			  'data'=>$top_5,			  
			);			
		} else {
			$this->details = array(  
			   'next_action'=>'clear_top_items'			  
			);
		}
		$this->jsonResponse();
	}
	
	public function actionTopCategory()
	{
		    					
    	
		$and = "AND a.merchant_id = ".FunctionsV3::q($this->merchant_id)." ";
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
		
		$order = "ORDER BY category_name ASC";
				
		
		$stmt="
		select
		SQL_CALC_FOUND_ROWS
		a.category_name,		
		sum(a.total_sale) - sum(a.discount) as net_sale
		from {{view_inventory_order_details}} a
		where 1
		$and		
		group by a.cat_id
		ORDER BY sum(a.total_sale) - sum(a.discount) DESC
		LIMIT 0,5		
		";
						
		$this->OKresponse();
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$res = Yii::app()->request->stripSlashes($res);
			foreach ($res as $key=>$val) {
				$colors = ReportsWrapper::randomColors($key);
				$top_5[] = array(
	    		  'color'=>$colors,
	    		  'item_name'=>$val['category_name'],
	    		  'value'=>normalPrettyPrice($val['net_sale'])
	    		);									
			}
			$this->details = array(  
			   'next_action'=>'show_top_items',
			  'data'=>$top_5,			  
			);			
		} else {
			$this->details = array(  
			   'next_action'=>'clear_top_items'			  
			);
		}
				
		$this->jsonResponse();
	}
	
	
} /*end class*/