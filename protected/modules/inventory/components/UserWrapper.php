<?php
class UserWrapper{
	
	public static function login($username='', $password='', $user_type='')
	{		
		
		if(empty($username)){
			throw new Exception( translate("Username is required") );
		}
		if(empty($password)){
			throw new Exception( translate("Password is required") );
		}
		if(empty($user_type)){
			throw new Exception( translate("User type is required") );
		}
		
		$new_user_type = array();
		switch ($user_type) {
			case "admin":		
			    $new_user_type = array('user_type','admin');
				break;
				
			case "merchant":		
			    $new_user_type = array('merchant_user','merchant');
				break;	
		
			default:
				break;
		}
		
		
		$resp = Yii::app()->db->createCommand()
          ->select('id,user_type,session_token,status,username,email_address,inventory_enabled')
          ->from('{{user_master_list}}')          
          ->where('username=:username and password=:password', array(
            ':username'=>$username,
            ':password'=>md5($password)
           ))
           ->andWhere(array(
             'IN','user_type',$new_user_type
           ))
          ->limit(1)
          ->queryRow();			
                                     
         if($resp){         	
         	
         	if($resp['inventory_enabled']<=0){
         		throw new Exception( translate("Your account is not allowed to access this platform") );
         	}
         	
         	$table_name = ''; $where_id='';
         	         	
         	$table = self::getTableByUseType($resp['user_type']);
         	         	
         	$table_name = $table['table_name'];
         	$where_id = $table['where_id'];
         	
         	if(!empty($table_name)){
         	    $token = self::generateToken();
         	    Yii::app()->db->createCommand()->update($table_name, array(
				    'session_token'=>$token,
				), $where_id.'=:id', array(':id'=>$resp['id']));
				
				Yii::app()->request->cookies['inventory_token'] = new CHttpCookie('inventory_token', $token);
				Yii::app()->request->cookies['inventory_token_type'] = new CHttpCookie('inventory_token_type', $resp['user_type']);
				Yii::app()->request->cookies['inventory_token_user'] = new CHttpCookie('inventory_token_user', $resp['username']);
				Yii::app()->request->cookies['inventory_token_email'] = new CHttpCookie('inventory_token_email', $resp['email_address']);
				
				return true;				
         	} else throw new Exception( translate("Login failed.") );
         }
         throw new Exception( translate("Either username or password are invalid") );
	}
	
	public static function getTableByUseType($user_type='')
	{
		$table_name=''; $where_id=''; 
				
		switch ($user_type) {
     		case "user_type":// admin
     			$table_name  ='{{admin_user}}';
     			$where_id = 'admin_id';
     			$user_type = 'admin';
     			break;
     	
     		case "merchant_user":
     			$table_name  ='{{merchant_user}}';
     			$where_id = 'merchant_user_id';
     			$user_type = $user_type;
     			break;
     			
     		case "merchant":
     			$table_name ='{{merchant}}';
     			$where_id = 'merchant_id';
     			$user_type = $user_type;
     			break;
     				
     		default:
     			break;
     	}         	
     	return array(
     	  'table_name'=>$table_name,
     	  'where_id'=>$where_id,
     	  'user_type'=>$user_type
     	);
	}
	
	public static function getToken()
	{		
		$token = '';
		if(!Yii::app()->user->isGuest){
		   $token = Yii::app()->user->token;
		}
		return $token;
	}
	
	public static function getUserType()
	{		
		$type='';
		if(!Yii::app()->user->isGuest){
			$type = Yii::app()->user->user_type;
		}
		if($type=="user_type"){
			$type = 'admin';
		}
		return $type;
	}
	
	public static function getUserName()
	{		
		$user_name='';
		if(!Yii::app()->user->isGuest){
			$user_name = Yii::app()->user->username;
		}
		return $user_name;
	}
	
	public static function getUserEmail()
	{		
		$user_email='';
		if(!Yii::app()->user->isGuest){
			$user_email = Yii::app()->user->user_email;
		}
		return $user_email;
	}
	
	public static function generateToken()
	{
		$agent = md5($_SERVER['HTTP_USER_AGENT']);		
		return sha1(uniqid(mt_rand(), true)).$agent;
	}
	
	public static function validToken($status='active')
	{
		if(!Yii::app()->user->isGuest){
			$token = self::getToken();			
			if(!empty($token)){
				$resp = Yii::app()->db->createCommand()
		          ->select('id,user_type,session_token,status')
		          ->from('{{user_master_list}}')          
		          ->where('session_token=:session_token AND status=:status', array(
		            ':session_token'=>$token,	            
		            ':status'=>$status
		           ))
		          ->limit(1)
		          ->queryRow();
		        if($resp){
		        	return true;
		        }
			}
		}
		return false;
	}
	
	public static function getAcessRules($status='active')
	{
		$access  = array('none');
		$token = self::getToken();
		if(!empty($token)){
			$stmt="
			SELECT 
			a.user_type,
			a.role_id,			
			a.session_token,
			a.status,
			b.access
			FROM 
			{{user_master_list}} a
			LEFT JOIN {{inventory_access_role}} b
			ON 
			a.role_id = b.role_id
						
			WHERE
			a.session_token = ".FunctionsV3::q($token)."
			LIMIT 0,1
			";
			if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			   if(!empty($res['access'])){
			     $access = json_decode($res['access'],true);			     
			   }			   
			}
		}
		return $access;
	}
	
	public static function AllowAccess()
	{
		return UserWrapper::validToken();
	}
	
	public static function AllowAccessAdmin()
	{
		$validtoken =  UserWrapper::validToken();
		$user_type = self::getUserType();
		if($user_type=="admin" && $validtoken==true){
			return true;
		}
		return false;
	}
	
	public static function logout()
	{		
		$token = self::getToken();		
		if(!empty($token)){
									
			$resp = Yii::app()->db->createCommand()
	          ->select('id,user_type,session_token,status')
	          ->from('{{user_master_list}}')          
	          ->where('session_token=:session_token', array(
	            ':session_token'=>$token,	            
	           ))
	          ->limit(1)
	          ->queryRow();
	        if($resp){
	           $table = self::getTableByUseType($resp['user_type']);
	           $new_token = self::generateToken();
	           if(!empty($table['table_name'])){	           	  
	           	  Yii::app()->db->createCommand()->update( $table['table_name'], array(
				     'session_token'=>$new_token,				     
				  ), 'session_token=:id', array(':id'=>$token));
	           }
	        }	        	        
		}
		
		Yii::app()->user->logout();
	}	
	
	public static function getMerchantIDByAccesToken()
	{
		$token = self::getToken();
		if(!empty($token)){
			$resp = Yii::app()->db->createCommand()
	          ->select('id,user_type,session_token,status,merchant_id')
	          ->from('{{user_master_list}}')          
	          ->where('session_token=:session_token', array(
	            ':session_token'=>$token,	            
	           ))
	          ->limit(1)
	          ->queryRow();
	        if($resp){	        	
	        	$user = self::getTableByUseType($resp['user_type']);	        	
	        	switch ($user['user_type']) {
	        		case 'merchant':
	        			return $resp['id'];
	        			break;
	        	
	        		case "merchant_user":
	        			return $resp['merchant_id'];
	        			break;
	        				
	        		default:
	        			break;
	        	}
	        }
		}
		return false;
	}
	
	public static function getAllUserByMerchantID($merchant_id='')
	{
		$merchant_id = (integer)$merchant_id;
		$db = new DbExt();
		$stmt = "
		SELECT id,merchant_id,user_type,username,status
        FROM {{user_master_list}}
		WHERE
		user_type IN ('merchant','merchant_user')
		AND status = 'active'
		AND ( id=".FunctionsV3::q($merchant_id)." or merchant_id=".FunctionsV3::q($merchant_id)." )
		";
		if($merchant_id>0){
			if($res = $db->rst($stmt)){
				return $res;
			}
		}
		return false;
	}
	
	public static function insertAccessRole($params=array(), $id='')
	{					
		if($id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select(' role_name')
	          ->from('{{inventory_access_role}}')   
	          ->where("role_name=:role_name AND role_id<>:role_id",array(	            
	            ':role_name'=>$params['role_name'],
	            ':role_id'=>$id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){	          	  
	          	  $up =Yii::app()->db->createCommand()->update("{{inventory_access_role}}",$params,
	          	    'role_id=:role_id',
	          	    array(
	          	      ':role_id'=>$id
	          	    )
	          	  );
	          	  if($up){
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Role name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('role_name')
	          ->from('{{inventory_access_role}}')   
	          ->where("role_name=:role_name",array(	            
	            ':role_name'=>$params['role_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){				
				if(Yii::app()->db->createCommand()->insert("{{inventory_access_role}}",$params)){
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Role name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}	
	
	public static function deleteRole($ids=array())
	{					
		$stmt="
		SELECT is_protected 
		FROM {{inventory_access_role}}
		WHERE 
		is_protected='1'
		AND role_id IN (".implode(",", (array) $ids).")
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			throw new Exception( "Failed protected access cannot be deleted" );
		}
		
		$criteria = new CDbCriteria();		
		$criteria->addInCondition('role_id', $ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{inventory_access_role}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function getAccessRole()
	{
		$resp = Yii::app()->db->createCommand()
	      ->select('')
	      ->from("{{inventory_access_role}}")   	      
	      ->order('role_name asc')    
          ->queryAll();		
	      if($resp){
	      	return $resp;
	      } else throw new Exception( "Record not found" );	 
	}
	
	public static function insertUserAccess($user_type='', $merchant_id='', $params=array(), $id='')
	{
			
		$and="";
		if($id>0){
			$and.=" AND id<>".FunctionsV3::q($id)." ";
		}
		
		$stmt="
		SELECT username FROM 
		{{user_master_list}}
		WHERE 1		
		AND 
		user_type IN ('merchant_user','merchant')
		AND (
		  username=".FunctionsV3::q($params['username'])."
		  OR
		  email_address=".FunctionsV3::q($params['contact_email'])."
		)
		$and
		";		
		//dump($stmt);
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			throw new Exception( "Username or email address already exist" );	 
		}
		
		switch ($user_type) {
			case "merchant":
				if($id>0){
					$resp = Yii::app()->db->createCommand()
			          ->select('merchant_id')
			          ->from('{{merchant}}')   
			          ->where("merchant_id=:merchant_id",array(	            
			            ':merchant_id'=>$merchant_id,   
			          ))	          
			          ->limit(1)
			          ->queryRow();		
			          if($resp){
			          	  $up =Yii::app()->db->createCommand()->update("{{merchant}}",$params,
			          	    'merchant_id=:merchant_id',
			          	    array(
			          	      ':merchant_id'=>$id
			          	    )
			          	  );
			          	  if($up){
			          	  	 return true;
			          	  } else throw new Exception( "Failed cannot update records" );
			          } else throw new Exception( "Record not found" );	 
				} else {
					//
				}
				break;
				
			case "merchant_user":				
			    if($id>0){
					$resp = Yii::app()->db->createCommand()
			          ->select('merchant_id')
			          ->from('{{merchant_user}}')   
			          ->where("merchant_id=:merchant_id AND merchant_user_id=:merchant_user_id",array(	            
			            ':merchant_id'=>$merchant_id,   
			            ':merchant_user_id'=>$id,   
			          ))	          
			          ->limit(1)
			          ->queryRow();		
			          if($resp){			          	
			          	  $up =Yii::app()->db->createCommand()->update("{{merchant_user}}",$params,
			          	    'merchant_user_id=:merchant_user_id',
			          	    array(
			          	      ':merchant_user_id'=>$id
			          	    )
			          	  );
			          	  if($up){
			          	  	 return true;
			          	  } else throw new Exception( "Failed cannot update records" );
			          } else throw new Exception( "Record not found" );	 
				} else {
					if(Yii::app()->db->createCommand()->insert("{{merchant_user}}",$params)){
					   return true;
				    } else throw new Exception( "Failed cannot insert records" );
				}
			    break;
		
			default:
				throw new Exception( "invalid user type" );	 
				break;
		}
	}	
	
	public static function deleteUser($merchant_id='',$ids=array())
	{		
		$ok = true;
		foreach ($ids as $id) {
			$stmt="
			SELECT user_type
			FROM {{user_master_list}}
			WHERE
			id=".FunctionsV3::q($id)."
			AND
			merchant_id=".FunctionsV3::q($merchant_id)."
			";						
			if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
				if($res['user_type']=="merchant"){
					$ok = false;
				}
			}
		}
		if($ok){
			$criteria = new CDbCriteria();
			$criteria->compare('merchant_id', $merchant_id);
			$criteria->addInCondition('merchant_user_id', $ids );
			$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{merchant_user}}', $criteria);		
			$resp = $command->execute();		
			if($resp){
				return true;
			} else throw new Exception( "Failed cannot delete records" );
		} else throw new Exception( "Failed you cannot delete main user account" );
	}
	
	public static function merchantUserValidate($user_type='',$email_address='', $contact_phone='', $token='')
	{
		  if($user_type=="admin"){
		  	 $user_type='user_type';
		  }
		  $stmt="SELECT email_address,contact_number FROM 
		   {{user_master_list}} 
		   WHERE 
		   user_type=".FunctionsV3::q($user_type)."
		   AND (email_address =".FunctionsV3::q($email_address)." OR contact_number=".FunctionsV3::q($contact_phone)." )
		   AND session_token != ".FunctionsV3::q($token)."
		  ";		  
		  if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
		  	 if($res['email_address']==$email_address){
		  	    throw new Exception( "Email address already exist" );
		  	 }
		  	 if($res['contact_number']==$contact_phone){
		  	    throw new Exception( "Contact number already exist" );
		  	 }
		  	 throw new Exception( "Either email addres or contact number already exist" );
		  }
		 return true;
	}
	
}
/*end class*/