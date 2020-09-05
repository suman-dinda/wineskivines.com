<?php
class AjaxfrontController extends CController
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
		return true;
	}
	
	private function jsonResponse()
	{		      
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
	}
	
	public function actiongetStocks()
	{			
		$value = isset($this->data['inv_value'])?$this->data['inv_value']:'';
		$item_id = isset($this->data['inv_item_id'])? (integer) $this->data['inv_item_id']:'';
		$with_size = isset($this->data['inv_with_size'])? (integer) $this->data['inv_with_size']:'';
		$merchant_id = isset($this->data['inv_merchant_id'])? (integer) $this->data['inv_merchant_id']:0 ;
		
		$allow_negative_stock = InventoryWrapper::allowNegativeStock($merchant_id);
		
		if($merchant_id>0 && $item_id>0 ){							
			try {
				
				$size_id = 0;				
				if($with_size>0){
					$value = explode("|",$value);
					if(is_array($value) && count($value)>=1){
						$size_id = isset($value[2])?(integer)$value[2]:0;
					}
				}								
								
				$resp = StocksWrapper::getAvailableStocks($merchant_id,$item_id,$size_id);
				$this->code = 1; $this->msg = "OK";
				$this->details = array(
				  'next_action'=>"display_stocks",
				  'available_stocks'=>$resp['available_stocks'],
				  'message'=>$resp['message'],
				  'allow_negative_stock'=>$allow_negative_stock
				);
				
			} catch (Exception $e) {
			   $this->details = array('next_action'=>"item_not_available");
		       $this->msg = translate($e->getMessage());
		    }
			
		} else {
			 $this->details = array('next_action'=>"item_info_not_available");
			 $this->msg = translate("invalid merchant id or size id");
		}
		$this->jsonResponse();
	}
	
}
/*end class*/