<?php
class ReportsWrapper{
	
	public static function generateDateRange($range1='', $range2='')
	{
		if(empty($range1)){
		   throw new Exception( "Range 1 is invalid" );
		}
		if(empty($range2)){
		   throw new Exception( "Range 2 is invalid" );
		}
		
		$date = array();
		$_range1 = date_create($range1);
		$_range2 = date_create($range2);
		$interval = date_diff($_range1, $_range2);
		$day_diff = $interval->format("%a");			
		for ($x = 0; $x <= $day_diff; $x++) {
			$date[] = date('M d', strtotime($range1 . " +$x day"));
		}
		return $date;
	}
	
	public static function randomColors($index=0)
	{
		$path = Yii::getPathOfAlias('webroot')."/protected/modules/".APP_FOLDER."/vendor/color_list.php";
		$colors = require $path;						
		if(array_key_exists($index,(array)$colors)){			
			return $colors[$index];
		} else return sprintf("#%06x",rand(0,16777215));		
	}
	
	public static function chartType($type=1)
	{
		switch ($type) {
			case 2:
				return array(
				  'line'=>"Line",
				  'column'=>"Bar",
				  'pie'=>"Pie",
				);
				break;
		
			default:
				return array(
				  'area'=>"Area",
				  'line'=>"Line",
				  'column'=>"Bar",
				);
				break;
		}		
	}
	
	public static function getDefaultStatus($merchant_id='')
	{
		//$status = getOption($merchant_id,'inventory_reports_default_status');
		$status = getOptionA('inventory_reports_default_status');
		if(!empty($status)){
			$status = json_decode($status,true);
		}
		return (array) $status;
	}
	
	public static function deleteLowStockLogs($ids=array())
	{
		$criteria = new CDbCriteria();		
		$criteria->addInCondition('id', $ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{inventory_lowstock_notification}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function totalFixedReport($merchant_id='')
	{
		$and='';
		if($merchant_id>0){
			$and.=" AND b.merchant_id= ".q($merchant_id)." ";
		}
		$stmt="
		SELECT count(*) as total,
		a.id,a.order_id, a.item_id, a.item_name,
		b.order_id as orderid, b.json_details
		
		FROM {{order_details}} a		 
		
		LEFT JOIN {{order}} b
		ON
		a.order_id = b.order_id
		
		WHERE a.cat_id = '0'
		AND b.order_id <> 0
		$and
		";		
		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
		   return $res['total'];
		}
		return 0;
	}
	
}/* end class*/