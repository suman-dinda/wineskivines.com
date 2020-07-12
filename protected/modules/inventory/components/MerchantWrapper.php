<?php
class MerchantWrapper{
	
	public static function updateAllowAccess($merchant_id=0, $params=array())
	{
		try {
			 $up =Yii::app()->db->createCommand()->update("{{merchant}}",$params,
	      	    'merchant_id=:merchant_id',
	      	    array(
	      	      ':merchant_id'=>$merchant_id
	      	    )
	      	 );
	      	 return true;
      	 } catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		 }
	}
	
}/* end class*/