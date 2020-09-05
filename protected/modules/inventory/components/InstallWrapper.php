<?php
class InstallWrapper
{
	public static function requiredTable()
	{
		return array(
		 'admin_user',
		 'merchant_user',
		 'merchant',
		 'item',
		 'category',
		 'subcategory',
		 'subcategory_item',		 
		);
	}
	
	public static function isTableView($table_name='')
	{
		$stmt="SELECT TABLE_TYPE FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ".FunctionsV3::q($table_name)." ";
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			if($res['TABLE_TYPE']=="VIEW"){
				return true;
			}
		} 
		return false;
	}
	
	public static function getItemsCountMigrate($merchant_id='')
	{
		$and='';
		$merchant_id = (integer) $merchant_id;
		if($merchant_id>0){
			$and.=" AND merchant_id = ".FunctionsV3::q($merchant_id)." ";
		}
		
		$stmt="
		select count(*) as total
		from
		{{item}} a
		where 1
		AND length(a.price) > 2
		$and
		AND
		a.item_id NOT IN (
		 select item_id 
		 from {{item_relationship_size}}
		 where
		 item_id = a.item_id 
		)
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			return $res['total'];
		}
		return 0;
	}
	
	public static function getItemstoMigrate($counter=0,$merchant_id=0)
	{
		$and='';
		$merchant_id = (integer) $merchant_id;
		if($merchant_id>0){
			$and.=" AND merchant_id = ".FunctionsV3::q($merchant_id)." ";
		}
				
		$stmt="
		select a.item_id,a.item_name, a.merchant_id, a.price,a.category,a.addon_item
		from
		{{item}} a
		where 1
		AND length(a.price) > 2
		$and
		AND a.item_id NOT IN (
		 select item_id 
		 from {{item_relationship_size}}
		 where
		 item_id = a.item_id 
		)
		LIMIT 0,1
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			return $res;
		}
		return 0;
	}
	
	public static function updateMerchantRole($merchant_id=0)
	{
		$res = Yii::app()->db->createCommand()
	          ->select('role_id')
	          ->from('{{inventory_access_role}}')   
	          ->where("is_protected=:is_protected",array(
	            ':is_protected'=>1
	          ))	          
	          ->limit(1)
	          ->queryRow();
	    if($res){
	    	$role_id = $res['role_id'];
	    	$stmt="
	    	UPDATE {{merchant}} SET inventory_role_id =".q($role_id)."
	    	WHERE merchant_id=".q($merchant_id)."
	    	";
	    	Yii::app()->db->createCommand($stmt)->query();
	    }
	}
	
	public static function getSubcategorytoMigrate($merchant_id=0)
	{
		
		$stmt="
		SELECT sub_item_id,category
		FROM {{subcategory_item}}
		WHERE
		merchant_id=".q($merchant_id)."
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			return $res;
		}
		return 0;
	}
	
}
/*end class*/