<?php
class CronWrapper
{
	
	public static function processInventorySale()
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
		        	/*dump($resp);
		        	die();*/
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
		        //dump($params_update);
		        $up =Yii::app()->db->createCommand()->update("{{inventory_sales}}",$params_update,
	          	    'id=:id',
	          	    array(
	          	      ':id'=>$id
	          	    )
	          	  );
	    	} /*end for*/
	    } 
	}
	
}
/*end class*/