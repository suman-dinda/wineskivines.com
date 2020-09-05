<?php
class AjaxheartbeatController extends CController
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
	
	public function OKresponse()
	{
		$this->code = 1; $this->msg = "OK"; $this->details = array();
	}
	
	public function actionCheck()
	{
		$this->OKresponse();
		$this->details = array(
		  'next_action'=>"silent"
		);
		if(!UserWrapper::validToken()){
			$this->details = array(
		      'next_action'=>"sesssion_expired",
		      'redirect'=>websiteUrl()."/".APP_FOLDER."/login?message=".translate("Your session has expired")
		    );
		}				
		$this->jsonResponse();
	}
	
}/* end class*/