<?php
class StocksWrapper
{
	public static function adjustmentType()
	{
		return array(
		  'receive_items'=>translate("Receive Items"),
		  'inventory_count'=>translate("Inventory count"),
		  'loss'=>translate("Loss"),
		  'damage'=>translate("Damage"),
		  'item_edit'=>translate("Item edit"),
		  'sale'=>translate("Sale")
		);
	}
	
	public static function adjustmentTypeList()
	{
		$arr1 =  array(
		  'all'=>translate("All reason")		  
		);
		$arr2 = self::adjustmentType();
		return $arr1+$arr2;
	}
	
	public static function purchaseStatus()
	{
		return array(
		  'all'=>translate("All"),
		  'pending'=>translate("Pending"),
		  'partially_received'=>translate("Partially received"),
		  'closed'=>translate("Closed"),
		);
	}
	
	public static function tableProperties()
	{
		return array(
		  'receive_items'=> array(
		     'label'=>array( translate('Item') ,translate('In Stock'),translate('Add Stock'),translate('Cost'),
		     translate('Stock after'),""),
		     'sizes'=>array('30%','10%','12%','12%','12%','10%')
		  ),
		  'inventory_count'=>array(
		     'label'=>array(translate('Item'),translate('Expected stock'),translate('Counted stock'), ''),
		     'sizes'=>array('30%','10%','12%','12%')
		  ),
		  'loss'=>array(
		     'label'=>array(translate('Item'),translate('In stock'),translate('Remove stock'),
		     translate('Stock after'),''),
		     'sizes'=>array('30%','10%','12%','12%','10%')
		  ),
		  'damage'=>array(
		     'label'=>array(translate('Item'),translate('In stock'),translate('Remove stock'),
		     translate('Stock after'),''),
		     'sizes'=>array('30%','10%','12%','12%','10%')
		  )
		);
	}
	
	public static function tablePropertiesView()
	{
		return array(
		  'receive_items'=> array(
		     'label'=>array( translate('Item') ,translate('Add Stock'),translate('Cost') ),
		     'fields'=>array('item_name','qty','cost_price'),
		     'class'=>array('','col-qty','col-qty')
		  ),
		  'inventory_count'=>array(
		     'label'=>array(translate('Item'),translate("Counted stock")),
		      'fields'=>array('item_name','qty'),
		      'class'=>array('','col-qty')
		  ),
		  'loss'=>array(
		     'label'=>array(translate('Item'),translate('Remove stock')),
		     'fields'=>array('item_name','qty'),
		     'class'=>array('','col-qty')
		  ),
		  'damage'=>array(
		     'label'=>array(translate('Item'),translate('Remove stock')),
		     'fields'=>array('item_name','qty'),
		     'class'=>array('','col-qty')
		  )
		);
	}
	
	public static function autoGenerateTransactionID()
	{
		$db = new DbExt();
		$stmt="SHOW TABLE STATUS LIKE '{{inventory_transaction}}'";
		if ($res = $db->rst($stmt)){			
			$res = $res[0];
			return $res['Auto_increment'];
		}
		unset($db);
		return false;
	}
	
	public static function autoGenerateID($table='')
	{
		$db = new DbExt();
		$stmt="SHOW TABLE STATUS LIKE '{{{$table}}}'";		
		if ($res = $db->rst($stmt)){			
			$res = $res[0];
			return $res['Auto_increment'];
		}
		unset($db);
		return false;
	}
	
	public static function transactionCode($type='')
	{
		$options = array(
		 'sa'=>"SA",
		 'po'=>"PO",
		 'or'=>"OR"
		);
		if(array_key_exists($type,$options)){
			return $options[$type];
		}
		return 'NA';
	}
	
	public static function insertAdjustment($params=array(), $params2=array() )
	{		
		$transaction_type = isset($params['transaction_type'])?$params['transaction_type']:'';
		if(empty($transaction_type)){
			throw new Exception( "Invalid transaction type" );
		}
		
		if(Yii::app()->db->createCommand()->insert("{{inventory_transaction}}",$params)){
			foreach ($params2 as $val) {				
				$i = Yii::app()->db->createCommand()->insert("{{inventory_transaction_details}}",$val);
				
				$stock_after = 0;
				$stock_balance = (float) self::getStocksSKU($val['sku']);		
				$qty  = (float) $val['qty'];		
				
				/*dump("stock_balance=>$stock_balance");
				dump("qty=>$stock_balance");*/
				
				switch ($transaction_type) {
					case "inventory_count":		
					      if($qty>=$stock_balance){
					      	 $difference = $qty-$stock_balance;
					      	 $qty = $difference;
					      	 $stock_after = $stock_balance+$difference;
					      } else {
					      	 $stock_after = $qty;
					      	 $qty = -($stock_balance-$qty);
					      }
						break;
						
					case "loss":   
					case "damage":				
					    $stock_after = $stock_balance - $val['qty'];
					    break;
					        
					default:
						$stock_after = $stock_balance + $val['qty'];
						break;
				}				
				
				$params_trans = array(
				  'created_at'=>FunctionsV3::dateNow(),
				  'sku'=>$val['sku'],
				  'merchant_id'=>$params['merchant_id'],
				  'transaction_type'=>$params['transaction_type'],
				  'transaction_id'=>$val['transaction_id'],
				  'transaction_code'=>$params['transaction_code'],
				  'adjustment'=>(float)$qty,
				  'stock_after'=>(float)$stock_after,				  
				  'cost_price'=>(float)$val['cost_price'],
				  'added_by'=>UserWrapper::getUserName(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);												
				self::insertInventoryStocks($params_trans);
			}
		 } else throw new Exception( "Failed cannot insert records" );
	}
	
	public static function insertInventoryStocks($data=array()){
		if(Yii::app()->db->createCommand()->insert("{{inventory_stocks}}",$data)){
	    	return true;
	    } else throw new Exception( "Failed cannot insert records" );
	}
	
	public static function getStocksSKU($sku='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select('stock_after')
          ->from('{{inventory_stocks}}')   
          ->where("sku=:sku",array(
             ':sku'=>$sku
          )) 
          ->order('stock_id DESC')    
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp['stock_after'];
        }
        return 0;     
	}
	
	public static function getTransactionDetails($transaction_id='', $merchant_id='')
	{
		$db=new DbExt();
		$stmt="
		select 
		a.*,
		b.sku,
		b.cost_price,
		b.qty,
		c.item_name,
		c.size_name
				
		FROM {{inventory_transaction}} a
		LEFT JOIN {{inventory_transaction_details}} b
		on
		a.transaction_id = b.transaction_id
		
		LEFT JOIN {{view_item}} c
		on
		b.sku = c.sku
		
		WHERE a.transaction_id = ".FunctionsV3::q($transaction_id)."
		AND a.merchant_id = ".FunctionsV3::q($merchant_id)."		
		";
		if($res=$db->rst($stmt)){
			return $res;
		}
		else throw new Exception( "Record not found" );	
	}
	
	public static function insertSupplier($merchant_id='',$params=array(), $id='')
	{					
		if($id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select('supplier_name')
	          ->from('{{inventory_supplier}}')   
	          ->where("merchant_id=:merchant_id AND supplier_name=:supplier_name AND supplier_id<>:supplier_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':supplier_name'=>$params['supplier_name'],
	            ':supplier_id'=>$id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){
	          	  $up =Yii::app()->db->createCommand()->update("{{inventory_supplier}}",$params,
	          	    'supplier_id=:supplier_id',
	          	    array(
	          	      ':supplier_id'=>$id
	          	    )
	          	  );
	          	  if($up){
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Size name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('supplier_name')
	          ->from('{{inventory_supplier}}')   
	          ->where("merchant_id=:merchant_id AND supplier_name=:supplier_name",array(
	            ':merchant_id'=>$merchant_id,
	            ':supplier_name'=>$params['supplier_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){
				if(Yii::app()->db->createCommand()->insert("{{inventory_supplier}}",$params)){
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Supplier name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}	

	public static function deleteSupplier($merchant_id='', $ids=array())
	{
		$criteria = new CDbCriteria();
		$criteria->compare('merchant_id', $merchant_id);
		$criteria->addInCondition('supplier_id', $ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{inventory_supplier}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}	
	
	public static function getSupplier($merchant_id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select("supplier_id,supplier_name")
          ->from('{{inventory_supplier}}')          
          ->where("merchant_id=:merchant_id",array(
	            ':merchant_id'=>$merchant_id,	            
	          ))	       
	      ->order('supplier_name asc')    
          ->queryAll();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function insertPurchase($params=array(), $params2=array(), $row_id=0)
	{
		if(count($params)<=0){			
			throw new Exception( "invalid parameters" );
		}
		if(count($params2)<=0){			
			throw new Exception( "invalid items" );
		}
				
		
		/*dump("row=>$row_id");
		dump($params);
		dump($params2);
		die();*/		

		if($row_id>0){
			$up =Yii::app()->db->createCommand()->update("{{inventory_purchase_order}}",$params,
          	    'po_id=:po_id',
          	    array(
          	      ':po_id'=>$row_id
          	    )
          	);
          	if($up){
          		foreach ($params2 as $val) {
          			$po_details_id = isset($val['po_details_id'])? (integer) $val['po_details_id'] :'';
          			unset($val['po_details_id']);
          			if($po_details_id>0){          			
          				Yii::app()->db->createCommand()->update("{{inventory_purchase_order_details}}",$val,
			          	    'po_details_id=:po_details_id',
			          	    array(
			          	      ':po_details_id'=>$po_details_id
			          	    )
			          	);
          			} else {
          				$i = Yii::app()->db->createCommand()->insert("{{inventory_purchase_order_details}}",$val);
          			}
          		}          		
          		
          		self::updatePurchaseOrderStatus($row_id);
          		
          	} else throw new Exception( "Failed cannot update records" );
		} else {		
			if(Yii::app()->db->createCommand()->insert("{{inventory_purchase_order}}",$params)){
				foreach ($params2 as $val) {
					$i = Yii::app()->db->createCommand()->insert("{{inventory_purchase_order_details}}",$val);
				}
			} else throw new Exception( "Failed cannot insert records" );
		}
	}
	
	public static function getPurchaseDetails($po_id='', $merchant_id='')
	{
		if($po_id<=0){			
			throw new Exception( "invalid purchase nunber" );	
		}
		if($merchant_id<=0){
			throw new Exception( "invalid merchant id" );	
		}
		
		$db=new DbExt();
		$stmt="
		SELECT a.po_id,a.merchant_id,a.purchase_date,a.supplier_id,a.supplier_name,a.notes,a.total_qty,a.added_by,
		a.status,a.expected_on,
		b.sku,
		c.item_name,
		c.size_name,
		b.po_details_id,
		b.qty,
		b.cost_price,
		
		IFNULL((
		 select available_stocks from {{view_item_stocks}}
		 where sku = b.sku
		),0)  as available_stocks,
		
		IFNULL((
		 select sum(qty)
		 from {{inventory_purchase_receive}}
		 where 
		 po_details_id IN (b.po_details_id)
		),0) as total_receive,
		
		IFNULL((
		 select incoming_balance from {{view_inventory_purchase_stocks}}
		 where sku = b.sku
		),0) as incoming_balance
		
		FROM {{view_inventory_purchase_order}} a
		
		LEFT JOIN {{inventory_purchase_order_details}} b
		ON
		a.po_id = b.po_id 
		
		LEFT JOIN {{view_item}} c
		on
		b.sku = c.sku
		
		WHERE 
		a.merchant_id = ".FunctionsV3::q($merchant_id)."
		AND
		a.po_id = ".FunctionsV3::q($po_id)."	
				
		ORDER BY b.po_details_id ASC		
		";						
		if($res=$db->rst($stmt)){			
			return $res;
		}
		else throw new Exception( "Record not found" );	
	}	
	
	public static function insertPurchaseReceive( $data = array() , $merchant_id='')
	{
		$po_id='';
		if(is_array($data) && count($data)>=1){
			foreach ($data as $params) {				
				
				$po_id = $params['po_id'];
				$stock_balance = (float) self::getStocksSKU($params['sku']);
				$stock_after = (float)$stock_balance + (float)$params['qty'] ;
				$track_stock = $params['track_stock'];
				
				$params_stocks = array(
				  'created_at'=>FunctionsV3::dateNow(),
				  'sku'=>$params['sku'],
				  'merchant_id'=>$merchant_id,
				  'transaction_type'=>"receive_items",
				  'transaction_id'=>$params['po_id'],
				  'transaction_code'=>self::transactionCode('po'),
				  'adjustment'=>$params['qty'],
				  'stock_after'=>$stock_after,
				  'cost_price'=>$params['cost_price'],
				  'added_by'=>UserWrapper::getUserName(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);								
				unset($params['cost_price']);
				unset($params['track_stock']);
				if(Yii::app()->db->createCommand()->insert("{{inventory_purchase_receive}}",$params)){
					if($track_stock==1){
					   self::insertInventoryStocks($params_stocks);					
					}
				} else throw new Exception( "Failed cannot insert records" );
			}
			
			self::updatePurchaseOrderStatus($po_id);
			
		} else throw new Exception( "Receive data is invalid" );	
	}
	
	public static function getPurchaseBalance($po_id='')
	{
		$db = new DbExt(); $balance = 0;
		$stmt="				
		SELECT		
		IFNULL((select sum(qty) from  {{inventory_purchase_order_details}}
		where po_id=".FunctionsV3::q($po_id)." ),0) -		
		IFNULL((select sum(qty) from  {{inventory_purchase_receive}}
		where po_id=".FunctionsV3::q($po_id)." ),0) as balance
		";		
		if($res = $db->rst($stmt)){			
			$balance = $res[0]['balance'];
		}
		return $balance;
	}
	
	public static function updatePurchaseOrderStatus($po_id='')
	{	
		$purchase_balance = self::getPurchaseBalance( (integer) $po_id);
		
		$status = '';
		
		if($purchase_balance<=0){
			$status='closed';
		} else if ($purchase_balance>0) {
			$status='partially_received';
		}
		
		if (!empty($status)){
			$params = array('status'=>$status,'updated_at'=>FunctionsV3::dateNow());
			Yii::app()->db->createCommand()->update("{{inventory_purchase_order}}",$params,
      	       'po_id=:po_id',
	      	    array(
	      	      ':po_id'=>$po_id
	      	    )
      	    );
		}
	}
	
	public static function getPurchaseOrder($po_id='', $merchant_id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select()
          ->from('{{inventory_purchase_order}}')          
          ->where("merchant_id=:merchant_id AND po_id=:po_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':po_id'=>$po_id,
	          ))	       	      
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function getPurchaseOrderView($po_id='', $merchant_id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select()
          ->from('{{view_inventory_purchase_order}}')          
          ->where("merchant_id=:merchant_id AND po_id=:po_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':po_id'=>$po_id,
	          ))	       	      
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function getReceiveSum($po_id='',$po_details_id='')
	{
		$db = new DbExt(); $total = 0;
		$stmt="
		SELECT sum(qty) as total
		FROM {{inventory_purchase_receive}}
		WHERE
		po_id = ".FunctionsV3::q($po_id)."
		AND
		po_details_id  = ".FunctionsV3::q($po_details_id) ."
		";		
		if ($res=$db->rst($stmt)){
			return $res[0]['total'];
		}
		return $total;
	}
	
	public static function getReceiveByPO($po_id='')
	{
		$db = new DbExt(); $total = 0;
		$stmt="
		SELECT sum(qty) as total
		FROM {{inventory_purchase_receive}}
		WHERE
		po_id = ".FunctionsV3::q($po_id)."		
		";				
		if ($res=$db->rst($stmt)){			
			$total =  $res[0]['total'];
		}
		return (float) $total;
	}
	
	public static function getSKUByPurchaseDetails($po_details_id='')
	{
		$sku = '';
		$resp = Yii::app()->db->createCommand()
          ->select('sku')
          ->from('{{inventory_purchase_order_details}}')          
          ->where("po_details_id=:po_details_id",array(
	            ':po_details_id'=>$po_details_id,	            
	          ))	       	      
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	$sku =  $resp['sku'];
        }
        return $sku;
	}
	
	public static function getSKUPurchaseDetails($po_details_id='')
	{	
		$resp = Yii::app()->db->createCommand()
          ->select('sku,cost_price')
          ->from('{{inventory_purchase_order_details}}')          
          ->where("po_details_id=:po_details_id",array(
	            ':po_details_id'=>$po_details_id,	            
	          ))	       	      
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp;
        }
        return false;
	}
	
	public static function getSKUPurchaseDetails2($po_details_id='')
	{	
		$db = new DbExt();
		$stmt="
		SELECT a.sku, a.cost_price, b.track_stock
		FROM {{inventory_purchase_order_details}} a
		LEFT JOIN {{view_item}} b
		ON
		a.sku = b.sku
		WHERE po_details_id =".FunctionsV3::q($po_details_id)."
		LIMIT 0,1
		";		
		if($res=$db->rst($stmt)){
			return $res[0];
		}		
		return false;
	}
	
	public static function setDeletePurchaseOrder($po_id='', $merchant_id='',$params=array() )
	{
		if($po_id>0){
			$total = StocksWrapper::getReceiveByPO($po_id);
			if($total<=0){						
				$up =Yii::app()->db->createCommand()->update("{{inventory_purchase_order}}",$params,
	          	    'merchant_id=:merchant_id AND po_id=:po_id',
	          	    array(
	          	      ':merchant_id'=>$merchant_id,
	          	      ':po_id'=>$po_id,
	          	    )
	          	  );
	          	  if($up){
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          	  
			} else throw new Exception( translate("Sorry we cannot cancel this purchase order it has already receive an item") );	
		} else throw new Exception( translate("Invalid purchase number") );	
	}
	
	public static function prettyPurchaseStatus($status='')
	{
		$pretty_status='';
		$status_list = self::purchaseStatus();
		
		if(array_key_exists($status,(array)$status_list)){
		   $pretty_status = $status_list[$status];
	    } else $pretty_status = translate($status);				
		
		switch ($status) {
			case "closed":
			case "cancel":
				return "<span class=\"badge badge-light\">".$pretty_status."</span>";
				break;
		
			default:
				return "<div class=\"badge badge-light\">$pretty_status</div>";;
				break;
		}
	}
	
	public static function prettyTransactionStatus($status='')
	{
		$pretty_status='';
		$status_list = self::adjustmentType();
		
		if(array_key_exists($status,(array)$status_list)){
		   $pretty_status = $status_list[$status];
	    } else $pretty_status = translate($status);				
		
		return $pretty_status;
	}
	
	public static function prettyTransactionStatusWithRef($status='', $reference='',$transaction_code='', $transaction_id='',$remarks='')
	{
		$pretty_status='';
		$status_list = self::adjustmentType();
		
		if(array_key_exists($status,(array)$status_list)){
		   $pretty_status = $status_list[$status];
	    } else $pretty_status = translate($status);		
	    
	    if(!empty($remarks)){
	    	$pretty_status.= " (".translate($remarks).") ";
	    }
		
	    if(!empty($reference)){
	    	$link = self::generateLink($transaction_code,$transaction_id);
	    	$pretty_status.=" <a href=\"$link\" class=\"text-primary\">#".$reference."</a>";
	    }
	    
		return $pretty_status;
	}
	
	public static function generateLink($transaction_code='',$transaction_id='')
	{		
		$link = 'javascript:;';
		switch ( strtolower($transaction_code) ) {
			case "sa":
				$link = Yii::app()->createUrl('inventory/stocks/adjustment_details',array(
				  'id'=>$transaction_id
				));
				break;
				
			case "po":	
			    $link = Yii::app()->createUrl('inventory/stocks/purchase_view',array(
				  'id'=>$transaction_id
				));
			    break;
			    
			case "or" :
				$link = Yii::app()->createUrl('inventory/reports/sales_receipt',array(
				  'id'=>$transaction_id
				));
			    break;
		
			default:
				break;
		}
		return $link;
	}
	
	public static function updateStocksEditItem($sku='', $cost_price=0, $in_stock=0,$merchant_id='', $added_by='')
	{
		$params = array(); $after_stock = 0;
		$stocks = StocksWrapper::getStocksSKU($sku);		
		
		if(!$sku_details = ItemWrap::getItemBySku($merchant_id,$sku)){
			return ;
		}
		
		if($sku_details['track_stock']<=0){			
			return;
		}
				
		if($stocks!=$in_stock){
			
			$params = array(
			  'created_at'=>FunctionsV3::dateNow(),
			  'sku'=>$sku,
			  'merchant_id'=> (integer) $merchant_id,
			  'transaction_type'=>"item_edit",
			  'added_by'=>$added_by,
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'cost_price'=>(float) $cost_price
			);
			
			if($in_stock>$stocks){								
				$after_stock = $in_stock;		
				$params['adjustment']= (float) $in_stock-$stocks;
				$params['stock_after']= (float) $after_stock;
			} else {
				$adjustment = $stocks-$in_stock;
				$after_stock = $stocks-$adjustment;
				$params['adjustment']= (float) -$adjustment;
				$params['stock_after']= (float) $after_stock;
			}			
			$i = Yii::app()->db->createCommand()->insert("{{inventory_stocks}}",$params);
		}		
	}
	
	public static function getEvaluation($merchant_id='')
	{
		$stmt = "
		SELECT SUM(inventory_value) as inventory_value,
		SUM(retail_value) as retail_value,
		SUM(potential_profit) as potential_profit
		FROM {{view_inventory_evaluation}}
		WHERE merchant_id =".FunctionsV3::q($merchant_id)."
		";
		if($resp = Yii::app()->db->createCommand($stmt)->queryRow()){
		   return $resp;
		}
		return false;
	}
	
	public static function getOwner($merchant_id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select('id,username')
          ->from('{{user_master_list}}')          
          ->where("id=:id AND user_type=:user_type",array(
	            ':id'=>$merchant_id,	  
	            ':user_type'=>'merchant'          
	          ))	       	      
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp;
        }
        return false;
	}
	
	public static function getStocksItem($merchant_id=0, $item_id=0,$size_id=0)
	{
		
		$where = 'merchant_id=:merchant_id AND item_id=:item_id';
		$where_val = array(
		  ':merchant_id'=> (integer) $merchant_id,
		  ':item_id'=> (integer) $item_id          
		);
		
		if($size_id>0){
			$where.=" AND size_id=:size_id";
			$where_val[':size_id']= (integer) $size_id;
		}		
		
		/*dump($where);
		dump($where_val);*/
		
		$resp = Yii::app()->db->createCommand()
          ->select('low_stock,available,available_stocks,track_stock')
          ->from('{{view_item_stocks}}')          
          ->where($where,$where_val)	       	      
          ->limit(1)
          ->queryRow();                  
        if($resp){        	
        	return $resp;
        }
        throw new Exception( Yii::t("inventory","Stocks not found") );	
	}
	
	public static function getAvailableStocks($merchant_id=0, $item_id=0, $size_id=0 )
	{
		try {
			$resp = self::getStocksItem($merchant_id,$item_id,$size_id);
			
			if($resp['track_stock']<=0){
				return true;
			}
			
			if($resp['available']<=0){
				throw new Exception( Yii::t("inventory","This item is not available for sale"));
			}
			
			$low_stock = (float) $resp['low_stock'];
			$available_stocks = (float) $resp['available_stocks'];
			
			$stocks_message = '';
			$inventory_in_stock = (integer) getOptionA('inventory_in_stock');
			$inventory_low_stock = (integer) getOptionA('inventory_low_stock');
			$inventory_items_left = (integer) getOptionA('inventory_items_left');
			
			$inventory_in_stock = $inventory_in_stock>0?$inventory_in_stock:11;
			$inventory_low_stock = $inventory_low_stock>0?$inventory_low_stock:10;
			$inventory_items_left = $inventory_items_left>0?$inventory_items_left:8;
									
			//dump($available_stocks); dump($inventory_in_stock);
			if($available_stocks<=0){
				$stocks_message = "Out of stock";
			} elseif ($available_stocks>=$inventory_in_stock){
				$stocks_message= "In stock";
			} elseif ( $available_stocks<=$inventory_low_stock  && $available_stocks>=$inventory_low_stock){
				$stocks_message= "Low stock";
			} elseif ( $available_stocks<=$inventory_items_left){
				$stocks_message = "only [qty] items left";
			} else {
				$stocks_message= "In stock";
			}
			
			$stocks_message = Yii::t("inventory",$stocks_message,array(
			  '[qty]'=>$available_stocks
			));
			
			return array(
			  'available_stocks'=>$available_stocks,
			  'message'=>$stocks_message
			);
			
		} catch (Exception $e) {	       
	        throw new Exception($e->getMessage());
	    } 
	}
	
	public static function verifyStocks($totalqty=0,$merchant_id=0, $item_id=0, $with_size=0, $value='')
	{				
		if(InventoryWrapper::allowNegativeStock($merchant_id)){
			return true;
		}
		
		try {
			
			$size_id=0;			
			if($with_size==1){
			   $value = explode("|",$value);
			   if(is_array($value) && count($value)>=1){
			     $size_id = isset($value[2])?(integer)$value[2]:0;
		       }
			}
			
			$resp = self::getStocksItem($merchant_id,$item_id,$size_id);
			if($resp['track_stock']<=0){
				return true;
			}
						
			if($resp['available']<=0){
				throw new Exception( Yii::t("inventory","This item is not available for sale"));
			}
						
			if($resp['available_stocks']<=0){
				throw new Exception( Yii::t("inventory","Out of stock"));
			} else {
				if ($totalqty>$resp['available_stocks']){
					throw new Exception( Yii::t("inventory","The maximum quantity available for this item is [qty].",array(
					  '[qty]'=>InventoryWrapper::prettyQuantity($resp['available_stocks'])
					)) );
				}
			}
			
		} catch (Exception $e) {	       
	        throw new Exception($e->getMessage());
	    } 
	}
	
	public static function verifyStocksReOrder($order_id=0, $merchant_id=0)
	{
		if(InventoryWrapper::allowNegativeStock((integer)$merchant_id)){
			return true;
		}
		
		$stmt="
		 SELECT 
		 a.order_id, 
		 a.item_id,
		 b.item_name,
		 b.track_stock,
		 a.size, 
		 sum(a.qty) as qty, 
		 a.size_id
		 FROM {{order_details}} a
		 
		 LEFT JOIN {{item}} b
		 ON 
		 a.item_id = b.item_id
		 
		 WHERE
		 a.order_id=".FunctionsV3::q((integer)$order_id)."
		 GROUP BY item_id,size_id
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {
				$val = Yii::app()->request->stripSlashes($val);
				
				if($val['track_stock']>=1):
				$totalqty = $val['qty'];
				$resp = self::getStocksItem((integer)$merchant_id,$val['item_id'],(integer)$val['size_id']);				
				if($resp['available']<=0){
					throw new Exception( Yii::t("inventory","Stock alert: [item_name] is not available for sale",array(
					  '[item_name]'=>$val['item_name']
					)));
				}
				
				if($resp['available_stocks']<=0){
					throw new Exception( Yii::t("inventory","Stock alert: [item_name] is Out of stock",array(
					  '[item_name]'=>$val['item_name']
					)));
				} else {
					if ($totalqty>$resp['available_stocks']){
						throw new Exception( Yii::t("inventory","Stock alert: The maximum quantity available for [item_name] is [qty].",array(
						  '[qty]'=>InventoryWrapper::prettyQuantity($resp['available_stocks']),
						  '[item_name]'=>$val['item_name']
						)) );
					}
				}
				endif;
			}			
			return true;
		} else throw new Exception( Yii::t("inventory","Order not found") );
	}
	
}
/*end class*/