<?php
class AjaxfixedreportController extends CController
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
	
	public function actionIndex()
	{				
		$continue = 1; $stats=''; $message='';
		$and = '';
		
		$user_type = UserWrapper::getUserType();
		
		if($user_type=="merchant"){
			$and = " AND b.merchant_id= ".q($this->merchant_id)." ";
		}
					
		$stmt="
		SELECT a.id,a.order_id, a.item_id, a.item_name,
		b.order_id as orderid, b.json_details
		
		FROM {{order_details}} a		 
		
		LEFT JOIN {{order}} b
		ON
		a.order_id = b.order_id
		
		WHERE a.cat_id = '0'
		AND b.order_id <> 0
		$and
		ORDER BY a.order_id ASC		
		limit 0,30
		";				
		//dump($stmt);		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {				
				$stats='';													
				if( $json=json_decode($val['json_details'],true)){							
					foreach ($json as $json_val) {						
						if( is_numeric($json_val['price']) ){											
							$size_id=0;			
						} else {							
							$price = explode("|",$json_val['price']);
							$size_id = $price[2];
						}			

						if($json_val['category_id']<=0){
							$json_val['category_id']=-1;
						}
									
						$stmtup = "
						UPDATE {{order_details}} SET
						size_id = ". q($size_id).",
						cat_id = ".  q($json_val['category_id'])."
						WHERE order_id = ".q($val['order_id'])."
						AND item_id = ".q($json_val['item_id'])."
						";							
						try {
						  Yii::app()->db->createCommand($stmtup)->query();
						  $stats.= "<span class=\"text-success\">[OK]</span>";						  
						} catch (Exception $e) {
							$stats.= $e->getMessage();							
						}
						
						/*ADDON*/						
						if (is_array($json_val['sub_item']) && count($json_val['sub_item'])>=1){
							foreach ($json_val['sub_item'] as $subcat_id=> $addonval) {
								foreach ($addonval as $key=> $_addonprice) {
									$addonprice = explode("|",$_addonprice);									
									$params_addon = array(
									  'order_id'=>(integer)$val['order_id'],
									  'subcat_id'=>(integer)$subcat_id,
									  'sub_item_id'=>(integer)$addonprice[0],
									  'addon_price'=>(float)$addonprice[1],									 
									);
									if(isset($json_val['addon_qty'])){
										$params_addon['addon_qty']= (float) $json_val['addon_qty'][$subcat_id][$key];
									} else $params_addon['addon_qty'] = (float) $json_val['qty'];
									
									$stmtsub="
									SELECT id FROM {{order_details_addon}}
									WHERE
									order_id=".q($params_addon['order_id'])."
									AND
									subcat_id=".q($params_addon['subcat_id'])."
									AND
									sub_item_id=".q($params_addon['sub_item_id'])."
									LIMIT 0,1
									";									
									try {
										if(!Yii::app()->db->createCommand($stmtsub)->queryRow()){										   
										   Yii::app()->db->createCommand()->insert("{{order_details_addon}}",$params_addon);
										   $stats.= "<span class=\"text-success\">[OK]</span>";										   
										}										
									} catch (Exception $e) {
										$stats.= $e->getMessage();																		
									}
								}
							}
						}					
				
					} /*end foreach*/					
				} /*end if*/

				$message.='<li class="list-group-item">';
				$message.= translate("Processing order id : [order_id] ... [stats]",array(
				  '[order_id]'=>$val['order_id'],
				  '[stats]'=>$stats
				));					
				$message.='</li>';
				
			}/* end foreach*/
		} else {
			$continue = 2;			
			$next_url =  Yii::app()->createUrl(APP_FOLDER.'/index/dashboard');
			$message = '<a href="'.$next_url.'" class="btn btn-raised btn-primary" >'.translate("Done").'</a>';			
		}				
		
		$this->code = 1;
		$this->msg = "OK";
		$this->details = array(
		  'next_action'=>"fixed_report",
		  'is_continue'=>$continue,
		  'message'=>$message
		);
		$this->jsonResponse();			
	}		
	
	
}
/*END CLASS*/