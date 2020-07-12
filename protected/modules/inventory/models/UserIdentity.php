<?php
class UserIdentity extends CUserIdentity
{
	private $_id;
	public $_user_type;	
	
	public function __construct($username, $password, $usertype)
	{
		$this->username = $username;
		$this->password = $password;
		$this->_user_type = $usertype;
	}
	
	public function authenticate()
	{

		$new_user_type=array();
		switch ($this->_user_type) {
			case "admin":
				$new_user_type = array( q('user_type'), q('admin'));
				break;
		
			case "merchant":
				$new_user_type = array( q('merchant_user'),q('merchant'));
				break;
					
			default:
				$new_user_type = array( q('none') );
				break;
		}
		
		 $stmt = "
		 SELECT *
		 FROM {{user_master_list}}
		 WHERE
		 username=:username
		 AND
		 user_type IN (".implode(",",$new_user_type).")
		 ";		 
		 $record = User::model()->findBySql($stmt, array(
		  ':username'=>$this->username
		 ));
		 		 
		 if($record===null){
		 	$this->errorCode=self::ERROR_USERNAME_INVALID;
		 	$this->errorMessage = translate("Either username or password are invalid");
		 } elseif ( $this->password!= $record->password ) {
		    $this->errorCode=self::ERROR_PASSWORD_INVALID;	
		    $this->errorMessage = translate("Invalid password");
		 } elseif (  $record->inventory_enabled <=0){
		 	$this->errorCode=300;
		 	$this->errorMessage = translate("Your account is not allowed to access this platform");
		 } else {		 	
		 	$this->_id=$record->id;
		 			 	
		 	$table = UserWrapper::getTableByUseType($record->user_type);		 	
		 	$table_name = $table['table_name'];
         	$where_id = $table['where_id'];
         	         	
     		$token = UserWrapper::generateToken();
     	    Yii::app()->db->createCommand()->update($table_name, array(
			    'session_token'=>$token,
			), $where_id.'=:id', array(':id'=>$record->id));
         	
			$this->setState('token', $token);
		 	$this->setState('user_type', $record->user_type);
		 	$this->setState('username', $record->username);
		 	$this->setState('user_email', $record->email_address);
		 	
		 	$this->errorCode=self::ERROR_NONE;	
		 }
		  return !$this->errorCode;
	}
	
	public function getId()
    {
        return $this->_id;
    }
        
}
/*end class*/