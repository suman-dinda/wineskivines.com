<?php
class AjaxuserController extends CController
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
	
	public function actionCreate_role()
	{
		$params = array(
		   'role_name'=>isset($this->data['role_name'])?$this->data['role_name']:'',
		   'access'=>isset($this->data['access']) ? json_encode($this->data['access']) : '',
		   'created_at'=>FunctionsV3::dateNow(),
		   'updated_at'=>FunctionsV3::dateNow(),
		   'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
		
		if(empty($params['access'])){
			$this->msg = translate("Invalid access");
			$this->jsonResponse();
		}
		
		$id = isset($this->data['row_id'])? (float) $this->data['row_id']:0;
		
		if($id>0){
			unset($params['created_at']);
		} else unset($params['updated_at']);
		
		try {
						
			UserWrapper::insertAccessRole($params,(integer)$id);
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/user/access_rights')
			);
			$this->OKresponse();
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		
		$this->jsonResponse();
	}
	
	public function actionAccess_rights()
	{
				
    	$feed_data = array();
    	
    	$cols = array('role_id','role_name','access','user_count');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = '';
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( a.role_name LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.role_id, a.role_name, a.access,
		(
		 select count(*) from {{user_master_list}}
		 where role_id = a.role_id
		) as user_count
		FROM
		{{inventory_access_role}}  a
		WHERE 1		
		$and
		$where
		$order
		$limit
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";			
			if($resc = Yii::app()->db->createCommand($stmtc)->queryAll()){
				$total_records=$resc[0]['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"]= Yii::app()->createUrl('inventory/user/create_role',array('id'=>$val['role_id']));
				foreach ($cols as $key_cols=> $cols_val) {					
					if($key_cols==2){
						
						$count = 0;
						if(!empty($val[$cols_val])) {
							$t = json_decode($val[$cols_val]);
							if(is_array($t) && count($t)>=1){
								$count = count($t);
							}
						}  
						
						$cols_data[$cols_val] = $count;
						
					} else $cols_data[$cols_val]=$val[$cols_val];					
				}
				$datas[]=$cols_data;
			}			
			//dump($datas);
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
	public function actionDelete_access_role()
	{
		if (isset($this->data['row_id'])){
			try {
				
								
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
				
				UserWrapper::deleteRole($ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/user/access_rights')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionUserlist()
	{
				
    	$feed_data = array();
    	
    	$cols = array('id','username','email_address','contact_number','role_id');
    	$resp = DatatablesWrapper::format($cols,$this->data);    	
    	$where = $resp['where'];
		$order = $resp['order'];
		$limit = $resp['limit'];
		
		$and = " AND merchant_id =".FunctionsV3::q($this->merchant_id)."  ";				
		$and.= " AND user_type='merchant_user' ";
		
		$search_field = isset($this->data['search_field'])?$this->data['search_field']:'';
		if(!empty($search_field)){
			$and.=" AND ( username LIKE ".FunctionsV3::q("%$search_field%")." )";
		}
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.*,
		(
		 select role_name from {{inventory_access_role}}
		 where 
		 role_id = a.role_id
		) as role_id
		FROM
		{{user_master_list}}  a
		WHERE 1		
		$and
		$where
		$order
		$limit
		";		
		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			$total_records=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";			
			if($resc = Yii::app()->db->createCommand($stmtc)->queryAll()){					
				$total_records=$resc[0]['total_records'];
			}			
			$feed_data['draw']=$this->data['draw'];
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
			
			$datas=array(); 
			foreach ($res as $val) {				
				$cols_data = array();				
				$cols_data["DT_RowId"] = Yii::app()->createUrl('inventory/user/add_user',
				  array(
				   'id'=>$val['id'],
				   'user_type'=>$val['user_type']
				 ));
				foreach ($cols as $key_cols=> $cols_val) {					
					$cols_data[$cols_val]=$val[$cols_val];				
				}
				$datas[]=$cols_data;
			}			
			
			$feed_data['data']=$datas;			
			$this->otableOutput($feed_data);			
		} else $this->otableNodata();
	}
	
	public function actionAdd_user()
	{
		$params=array();$where_id = '';		
		$user_type = isset($this->data['user_type'])?$this->data['user_type']:'';
		$id = isset($this->data['row_id'])? (integer) $this->data['row_id']:0;
		
		switch ($user_type) {
			case "merchant":
				$where_id='merchant_id';
				$params=array(
				  'username'=>isset($this->data['username'])?$this->data['username']:'',
				  'contact_email'=>isset($this->data['email'])?$this->data['email']:'',
				  'contact_phone'=>isset($this->data['phone'])?$this->data['phone']:'',				  
				  'date_created'=>FunctionsV3::dateNow(),
				  'date_modified'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);
				if($id>0){
					unset($params['date_created']);
				} else unset($params['date_modified']);
				break;
				
			case "merchant_user":	
			    $params=array(
			      'merchant_id'=>$this->merchant_id,
			      'username'=>isset($this->data['username'])?$this->data['username']:'',
				  'contact_email'=>isset($this->data['email'])?$this->data['email']:'',
				  'contact_number'=>isset($this->data['phone'])?$this->data['phone']:'',
				  'inventory_role_id'=>isset($this->data['role_id'])? (integer) $this->data['role_id']:0,
				  'date_created'=>FunctionsV3::dateNow(),
				  'date_modified'=>FunctionsV3::dateNow(),
				  'ip_address'=>$_SERVER['REMOTE_ADDR']
				);												
				if($id>0){
					unset($params['date_created']);
				} else unset($params['date_modified']);
			    break;
		
			default:
				$this->msg = translate("Invalid user type");
				$this->jsonResponse();
				break;
		}
				
		if(isset($this->data['password'])){
		   if(!empty($this->data['password'])){
			   $password = isset($this->data['password'])? $this->data['password'] :'';
			   $cpassword = isset($this->data['cpassword'])? $this->data['cpassword'] :'';
			   if($password!=$cpassword){
			   	  $this->msg = translate("Invalid confirm password");
				  $this->jsonResponse();
			   }
			   $params['password'] = md5(trim($password));
		   }
		}
		
		if(isset($params['inventory_role_id'])){
		if($params['inventory_role_id']<=0){
			$this->msg = translate("Invalid role");
			$this->jsonResponse();
		}
		}

		
		try {
						
			$params = InventoryWrapper::purifyData($params);
			
			UserWrapper::insertUserAccess($user_type, $this->merchant_id , $params ,(integer) $id);
			$this->details = array(
			  'next_action'=>"redirect",
			  'redirect'=>Yii::app()->createUrl('inventory/user/userlist')
			);
			$this->OKresponse();
			$this->msg = translate("Successful");
			
		} catch (Exception $e) {
		    $this->msg = translate($e->getMessage());
		}
		$this->jsonResponse();
	}
	
	public function actionDelete_user()
	{
		if (isset($this->data['row_id'])){
			try {
				
								
				$ids = array();
				foreach ($this->data['row_id'] as $id) {					
					$ids[]= (integer) $id;
				}		
				
				UserWrapper::deleteUser( (integer) $this->merchant_id,$ids);				
				$this->OKresponse();
				$this->msg = translate("Record deleted");
				$this->details = array(
				 'next_action'=> isset($this->data['next_action'])?$this->data['next_action']:"refresh_table",
				 'redirect'=>Yii::app()->createUrl('inventory/user/userlist')
				);
				
			} catch (Exception $e) {
		       $this->msg = translate($e->getMessage());
		    }
		} else $this->msg = translate("invalid row id");
		$this->jsonResponse();
	}
	
	public function actionProfile()
	{
		$token = UserWrapper::getToken();
		$user_type = UserWrapper::getUserType();
		$params = array(
		  'contact_email'=> isset($this->data['email'])?trim($this->data['email']):'',
		  'contact_phone'=> isset($this->data['phone'])?trim($this->data['phone']):'',
		  'username'=> isset($this->data['username'])?trim($this->data['username']):'',
		  'date_modified'=>FunctionsV3::dateNow(),
		  'ip_address'=>$_SERVER['REMOTE_ADDR']
		);
				
		if(isset($this->data['password'])){
			if(!empty($this->data['password'])){
				if($this->data['password']!=$this->data['cpassword']){
					$this->msg = translate("Confirm password does not match");
					$this->jsonResponse();
				}
				$params['password']=md5( trim($this->data['password']) );
			}
		}

		try {
			
			UserWrapper::merchantUserValidate($user_type,$params['contact_email'],$params['contact_phone'],$token);
			
			if($user_type=="admin"){
			   $params['email_address'] = $params['contact_email'];
			   unset($params['contact_email']);
			   $params['contact_number'] = $params['contact_phone'];
			   unset($params['contact_phone']);			   
			   
			   $params = InventoryWrapper::purifyData($params);
			   
			   $up = Yii::app()->db->createCommand()->update("{{admin_user}}",$params,
	      	    'session_token=:session_token',
	      	    array(
	      	      ':session_token'=>$token
	      	    )
	      	   );
			} elseif ( $user_type=="merchant_user"){						
				$params['contact_number']= $params['contact_phone'];
				unset($params['contact_phone']);				
				
				$params = InventoryWrapper::purifyData($params);
				
				$up = Yii::app()->db->createCommand()->update("{{merchant_user}}",$params,
	      	    'session_token=:session_token',
	      	    array(
	      	      ':session_token'=>$token
	      	    )
	      	   );
			} else {				
				
				$params = InventoryWrapper::purifyData($params);
				
			   $up = Yii::app()->db->createCommand()->update("{{merchant}}",$params,
	      	    'session_token=:session_token',
	      	    array(
	      	      ':session_token'=>$token
	      	    )
	      	   );
			}
			
			$this->OKresponse();
      		$this->msg = translate("Profile saved");
      		$this->details = array();
      		
		} catch (Exception $e) {
			$this->msg = translate($e->getMessage());
		}
		     	          	 
		$this->jsonResponse();
	}
	
	public function actionUpdatestatus()
	{
		$enabled = isset($this->data['enabled'])?$this->data['enabled']:0;
		$enabled = $enabled=="true"?1:0;		
		Yii::app()->functions->updateOption("inventory_live",(integer)$enabled,$this->merchant_id);
		$this->OKresponse();
        $this->msg = translate("Settings saved");
        $this->details = array(
         'next_action'=>"silent"
        );		
		$this->jsonResponse();
	}
	
}/* end class*/