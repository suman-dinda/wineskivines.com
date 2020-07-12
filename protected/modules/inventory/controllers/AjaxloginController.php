<?php
class AjaxloginController extends CController
{
	public $code=2;
	public $msg;
	public $details;
	public $data;
	
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
		
	public function actionVerify()
	{
		try {
		   			
			$username = isset($this->data['username'])? trim($this->data['username']) :'';
			$password = isset($this->data['password'])? trim($this->data['password']) :'';
			$user_type = isset($this->data['usertype'])? trim($this->data['usertype']) :'';
			
			$identity=new UserIdentity($username,md5($password),$user_type);			
			if($identity->authenticate()){
				
				Yii::app()->user->login($identity);
				
				$this->code = 1;
			    $this->msg = translate("Login Okay");
			    $this->details = array(
			     'next_action'=>"redirect",
			     'redirect'=>Yii::app()->createUrl('inventory/index')
			    );
			} else $this->msg =  $identity->errorMessage;					
		} catch (Exception $e) {
			$this->msg = $e->getMessage();
		}
		$this->jsonResponse();
	}
	
}
/*end class*/