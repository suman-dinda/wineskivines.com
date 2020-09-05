<?php
class AjaxsettingsController extends CController
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
	
	public function actionGeneral()
	{		
		
		Yii::app()->functions->updateOption("inventory_email_notify",
	    	isset($this->data['inventory_email_notify'])? InventoryWrapper::purify($this->data['inventory_email_notify']) :''
	    ,$this->merchant_id);
	    
	    Yii::app()->functions->updateOption("inventory_hide_out_stock",
	    	isset($this->data['inventory_hide_out_stock'])?(integer)$this->data['inventory_hide_out_stock']:''
	    ,$this->merchant_id);
	    
	    
	    Yii::app()->functions->updateOption("inventory_allow_negative_order",
	    	isset($this->data['inventory_allow_negative_order'])?(integer)$this->data['inventory_allow_negative_order']:''
	    ,$this->merchant_id);
	    
	    Yii::app()->functions->updateOption("inventory_low_stock_notify",
	    	isset($this->data['inventory_low_stock_notify'])?(integer)$this->data['inventory_low_stock_notify']:''
	    ,$this->merchant_id);
	    
	    $params = array(
	      'inventory_low_stock_notify'=> isset($this->data['inventory_low_stock_notify'])? (integer) $this->data['inventory_low_stock_notify'] :0,
	      'inventory_email_notify'=> isset($this->data['inventory_email_notify'])? (string) $this->data['inventory_email_notify'] : '',
	      'date_modified'=>FunctionsV3::dateNow(),
	      'ip_address'=>$_SERVER['REMOTE_ADDR']
	    );
	    
	    $params = InventoryWrapper::purifyData($params);
	    Yii::app()->db->createCommand()->update("{{merchant}}",$params,
	  	    'merchant_id=:merchant_id',
	  	    array(
	  	      ':merchant_id'=>$this->merchant_id
	  	    )
	  	  );
	    	    
		$this->OKresponse();
		$this->msg = translate("Settings saved");
		$this->details = array();
		
		$this->jsonResponse();
	}
	
	public function actionSettings_reports()
	{
		Yii::app()->functions->updateOption("inventory_reports_default_status",
	    	isset($this->data['inventory_reports_default_status'])? json_encode($this->data['inventory_reports_default_status']) :''
	    ,$this->merchant_id);
	    
	    Yii::app()->functions->updateOption("inventory_accepted_order_status",
	    	isset($this->data['inventory_accepted_order_status'])? json_encode($this->data['inventory_accepted_order_status']) :''
	    ,$this->merchant_id);
	    
	    Yii::app()->functions->updateOption("inventory_cancel_order_status",
	    	isset($this->data['inventory_cancel_order_status'])? json_encode($this->data['inventory_cancel_order_status']) :''
	    ,$this->merchant_id);
	    
		$this->OKresponse();
		$this->msg = translate("Settings saved");
		$this->details = array();
		
		$this->jsonResponse();
	}
	
}/* end class*/