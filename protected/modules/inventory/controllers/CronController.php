<?php
class CronController extends CController
{
	public $code=2;
	public $msg;
	public $details;
	public $data;	
	public $merchant_id;
	
	public function __construct()
	{
		InventoryWrapper::setTimeZone();
	}
	
	public function actionIndex()
	{		
	}
	
	public function actionProcess()
	{		
		$update_at = FunctionsV3::dateNow();
		$process_status = 'process';
		
		
		$res = Yii::app()->db->createCommand()
	          ->select('')
	          ->from('{{inventory_sales}}')   
	          ->where("process_status=:process_status",array(
	            ':process_status'=>'pending',	            
	          ))	          	          
	          ->order('id ASC')    
	          ->limit(10)
	          ->queryAll();		
	    if($res){
	    	foreach ($res as $val) {
	    		
	    		$id = $val['id']; 
	    		$order_id = $val['order_id']; 
	    		$order_status = trim($val['order_status']);	
	    		$transaction_type = $val['transaction_type'];
	    			    		
	    		$stmt = "
	    		SELECT a.*, sum(a.qty) as qty ,
	    		
	    		IFNULL((
				 select stock_after from
				 {{inventory_stocks}} 
				 where
				 sku = a.sku
				  order by stock_id desc
				limit 0,1
				),0) as available_stocks
				
	    		FROM {{view_inventory_order_details}} a
	    		WHERE order_id=".FunctionsV3::q($order_id)."
	    		GROUP BY sku
	    		";	   
	    		 		
		        if($resp = Yii::app()->db->createCommand($stmt)->queryAll()){		
		        	
		        	foreach ($resp as $data) {		
		        		
		        		$stock_after = 0;
		        		$available_stocks = $data['available_stocks']; 
		        		$user = StocksWrapper::getOwner($data['merchant_id']);
		        		
		        		$params_stock = array(
	        			  'created_at'=>FunctionsV3::dateNow(),
	        			  'sku'=>$data['sku'],
	        			  'merchant_id'=>$data['merchant_id'],
	        			  'transaction_type'=>'sale',
	        			  'transaction_id'=>$data['order_id'],
	        			  'transaction_code'=>StocksWrapper::transactionCode('or'),	        			  
	        			  'cost_price'=>$data['cost_price'],
	        			  'added_by'=> isset($user['username'])?$user['username']:'',		        			  
	        			  'ip_address'=>$_SERVER['REMOTE_ADDR']
	        			);		   
		        				
		        		/*SALE */
		        		if($transaction_type=="sale"){
		        			$stock_after = $data['available_stocks']-$data['qty'];
		        			$params_stock['adjustment'] = -$data['qty'];
		        		}

		        		/*CANCEL*/
		        		if($transaction_type=="cancelled"){
		        			$stock_after = $data['available_stocks']+$data['qty'];
		        			$params_stock['adjustment'] = $data['qty'];	
		        			$params_stock['remarks'] = $order_status;
		        		}
		        		
		        		$params_stock['stock_after'] = $stock_after;		       
		        		
		        		if($data['track_stock']>=1){
			        		if(!Yii::app()->db->createCommand()->insert("{{inventory_stocks}}",$params_stock)){
						       $process_status = "failed. cannot insert";
						    } 
		        		}
		        			        		
		        	} /*end for*/		       
		        }  else $process_status = 'failed. no records to process';
		        
		        $params_update = array(
		         'process_status'=>$process_status,
		         'updated_at'=>FunctionsV3::dateNow(),
		         'ip_address'=>$_SERVER['REMOTE_ADDR']
		        );
		        
		        $up =Yii::app()->db->createCommand()->update("{{inventory_sales}}",$params_update,
	          	    'id=:id',
	          	    array(
	          	      ':id'=>$id
	          	    )
	          	  );
	    	} /*end for*/
	    } 
	}
	
	public function actionLow_stock()
	{
		$todays_date = FunctionsV3::dateNow();
		$sitename = getOptionA('website_title');
		$date_process = date("Y-m-d");
		$merchant_id=''; $email_notify=''; $sql_data='';
		
		$admin_email = getOptionA('inventory_email_notify');
		$inventory_low = getOptionA('inventory_low_stock_notify');
		
		$subject = translate("Low stock notification, [date]",array(
		  '[date]'=>InventoryWrapper::prettyDate($date_process,"l, d/m/Y")
		));
		
		$data = array();		
		$stmt="
		SELECT b.restaurant_name as merchant,
		b.inventory_email_notify as email_notify,
		a.merchant_id,a.item_name,a.size_name,a.sku,a.available_stocks
		FROM {{view_item_stocks_status}} a
		LEFT JOIN {{merchant}} b
		ON 
		a.merchant_id = b.merchant_id
		WHERE 			
		stock_status IN ('Low stock','Out of stocks')		
		AND b.inventory_low_stock_notify='1'
		AND inventory_email_notify !=''		
		AND a.sku NOT IN (
		  select sku from {{inventory_lowstock_notification}}
		  where 
		  sku = a.sku and date_process=".FunctionsV3::q($date_process)."
		  and 
		  available_stocks = a.available_stocks
		)
		ORDER BY a.available_stocks ASC		
		LIMIT 0,100
		";
						
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			$res = Yii::app()->request->stripSlashes($res);
			foreach ($res as $val) {		
								
				$template = $this->renderPartial(APP_FOLDER.'.views.reports.low_stock',array(	
				     'data'=>array($val),
				     'merchant'=>$val['merchant'],
				     'todays_date'=>InventoryWrapper::prettyDate($todays_date,"l , d/m/Y"),
				     'sitename'=>$sitename
				),true);
								
				$sql_data .="(NULL,".FunctionsV3::q($val['merchant_id']).",";
				$sql_data .=FunctionsV3::q($val['sku']).",";
				$sql_data .=FunctionsV3::q($val['available_stocks']).",";
				$sql_data .=FunctionsV3::q($date_process)."),\n";
				
				sendEmail($val['email_notify'],'', $subject , $template );
				if(!empty($admin_email) && $inventory_low==1){
					sendEmail($admin_email,'', $subject , $template );
				}
			}
			
			$sql_data = substr($sql_data,0,-2);
			
			$sql_insert = "INSERT INTO {{inventory_lowstock_notification}} VALUES \n$sql_data;";
			
			Yii::app()->db->createCommand($sql_insert)->query();
			echo "done(s)";
			
		} else echo "no result(s)";
	}
	
	
}
/*end class */