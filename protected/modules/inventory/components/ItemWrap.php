<?php
class ItemWrap
{

	public static function isMultiLanguage()
    {
    	if ( Yii::app()->functions->getOptionAdmin('enabled_multiple_translation')==2){
    		return true;
    	}
    	return false;
    }
    
    public static function okToDecode()
	{
		$version=phpversion();		
		if ( $version>5.3){
			return true;
		}
		return false;
	}
    
	public static function getMaxSequence($table='', $merchant_id='', $field='sequence')
	{
		$where='';
		if($merchant_id>0){
			$where= " WHERE merchant_id=".FunctionsV3::q($merchant_id)."";
		}
		$stmt = "SELECT 
		 max($field) as max FROM
		 {{{$table}}}
		 $where		 
		";				
		$cmd = Yii::app()->db->createCommand($stmt);
		if($resp = $cmd->queryRow()){						
			return $resp['max']>0?$resp['max']+1:1;
		} else return 1;
	}
	
	public static function getDish()
	{
		$resp = Yii::app()->db->createCommand()
          ->select()
          ->from('{{dishes}}')           
          ->queryAll();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function getItem($merchant_id='', $id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select()
          ->from('{{item}}')   
          ->where("merchant_id=:merchant_id AND item_id=:item_id",array(
             ':merchant_id'=>$merchant_id,
             ':item_id'=>$id,
          )) 
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}	
	
	
	public static function getDishByID($id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select()
          ->from('{{dishes}}')   
          ->where("id=:id",array(
             ':id'=>$id
          )) 
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}	
	
	public static function insertCategory($merchant_id='',$params=array(), $cat_id='')
	{					
		if($cat_id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select('category_name')
	          ->from('{{category}}')   
	          ->where("merchant_id=:merchant_id AND category_name=:category_name AND cat_id<>:cat_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':category_name'=>$params['category_name'],
	            ':cat_id'=>$cat_id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){
	          	  $up =Yii::app()->db->createCommand()->update("{{category}}",$params,
	          	    'cat_id=:cat_id',
	          	    array(
	          	      ':cat_id'=>$cat_id
	          	    )
	          	  );
	          	  if($up){
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Category name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('category_name')
	          ->from('{{category}}')   
	          ->where("merchant_id=:merchant_id AND category_name=:category_name",array(
	            ':merchant_id'=>$merchant_id,
	            ':category_name'=>$params['category_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){
				if(Yii::app()->db->createCommand()->insert("{{category}}",$params)){
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Category name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}
	
	public static function deleteCategory($merchant_id='', $cat_id=array())
	{
		$stmt="SELECT id FROM {{item_relationship_category}} WHERE cat_id IN (".implode(",", (array) $cat_id).")";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			throw new Exception( "Cannot delete records it has reference in another table" );
		}				
		
		$criteria = new CDbCriteria();
		$criteria->compare('merchant_id', $merchant_id);
		$criteria->addInCondition('cat_id', $cat_id );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{category}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
			
	public static function getData($table='',$where='',$where_val=array())
	{
		$resp = Yii::app()->db->createCommand()
	      ->select('')
	      ->from("{{{$table}}}")   
	      ->where($where,$where_val)	          
	      ->limit(1)
	      ->queryRow();	
	      if($resp){
	      	return $resp;
	      } else throw new Exception( "Record not found" );	      
	}
	
	public static function insertAddonCategory($merchant_id='',$params=array(), $id='')
	{					
		if($id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select('subcategory_name')
	          ->from('{{subcategory}}')   
	          ->where("merchant_id=:merchant_id AND subcategory_name=:subcategory_name AND subcat_id<>:subcat_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':subcategory_name'=>$params['subcategory_name'],
	            ':subcat_id'=>$id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){
	          	  $up =Yii::app()->db->createCommand()->update("{{subcategory}}",$params,
	          	    'subcat_id=:subcat_id',
	          	    array(
	          	      ':subcat_id'=>$id
	          	    )
	          	  );
	          	  if($up){
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Category name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('subcategory_name')
	          ->from('{{subcategory}}')   
	          ->where("merchant_id=:merchant_id AND subcategory_name=:subcategory_name",array(
	            ':merchant_id'=>$merchant_id,
	            ':subcategory_name'=>$params['subcategory_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){
				if(Yii::app()->db->createCommand()->insert("{{subcategory}}",$params)){
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Category name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}
	
	public static function deleteAddonCategory($merchant_id='', $ids=array())
	{		
				
		$stmt="SELECT id FROM {{subcategory_item_relationships}} WHERE subcat_id IN (".implode(",", (array) $ids).")";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			throw new Exception( "Cannot delete records it has reference in another table" );
		}		
		
		$stmt="SELECT id FROM {{order_details_addon}} WHERE subcat_id IN (".implode(",", (array) $ids).")";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			throw new Exception( "Cannot delete records it has reference in another table" );
		}		
		
		$criteria = new CDbCriteria();
		$criteria->compare('merchant_id', $merchant_id);
		$criteria->addInCondition('subcat_id', $ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{subcategory}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	
	public static function getAddonCategory($merchant_id='', $status='publish')
	{
		$resp = Yii::app()->db->createCommand()
          ->select("subcat_id,subcategory_name,subcategory_name_trans,status")
          ->from('{{subcategory}}')          
          ->where("merchant_id=:merchant_id AND status=:status",array(
	            ':merchant_id'=>$merchant_id,
	            ':status'=>$status
	          ))	       
	      ->order('sequence asc')    
          ->queryAll();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function deleteSize($merchant_id='', $ids=array())
	{
		$stmt="SELECT item_size_id FROM {{item_relationship_size}} WHERE size_id IN (".implode(",", (array) $ids).")";			
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			throw new Exception( "Cannot delete records it has reference in another table" );
		}			
		
		$criteria = new CDbCriteria();
		$criteria->compare('merchant_id', $merchant_id);
		$criteria->addInCondition('size_id', $ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{size}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function insertSize($merchant_id='',$params=array(), $id='')
	{					
		if($id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select('size_name')
	          ->from('{{size}}')   
	          ->where("merchant_id=:merchant_id AND size_name=:size_name AND size_id<>:size_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':size_name'=>$params['size_name'],
	            ':size_id'=>$id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){
	          	  $up =Yii::app()->db->createCommand()->update("{{size}}",$params,
	          	    'size_id=:size_id',
	          	    array(
	          	      ':size_id'=>$id
	          	    )
	          	  );
	          	  if($up){
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Size name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('size_name')
	          ->from('{{size}}')   
	          ->where("merchant_id=:merchant_id AND size_name=:size_name",array(
	            ':merchant_id'=>$merchant_id,
	            ':size_name'=>$params['size_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){
				if(Yii::app()->db->createCommand()->insert("{{size}}",$params)){
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Size name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}	
	
	public static function insertIngredients($merchant_id='',$params=array(), $id='')
	{					
		if($id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select('ingredients_name')
	          ->from('{{ingredients}}')   
	          ->where("merchant_id=:merchant_id AND ingredients_name=:ingredients_name AND ingredients_id<>:ingredients_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':ingredients_name'=>$params['ingredients_name'],
	            ':ingredients_id'=>$id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){
	          	  $up =Yii::app()->db->createCommand()->update("{{ingredients}}",$params,
	          	    'ingredients_id=:ingredients_id',
	          	    array(
	          	      ':ingredients_id'=>$id
	          	    )
	          	  );
	          	  if($up){
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('ingredients_name')
	          ->from('{{ingredients}}')   
	          ->where("merchant_id=:merchant_id AND ingredients_name=:ingredients_name",array(
	            ':merchant_id'=>$merchant_id,
	            ':ingredients_name'=>$params['ingredients_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){
				if(Yii::app()->db->createCommand()->insert("{{ingredients}}",$params)){
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}		
	
	
	public static function deleteIngredients($merchant_id='', $ids=array())
	{
		$criteria = new CDbCriteria();
		$criteria->compare('merchant_id', $merchant_id);
		$criteria->addInCondition('ingredients_id', $ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{ingredients}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function insertCookingRef($merchant_id='',$params=array(), $id='')
	{					
		if($id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select('cooking_name')
	          ->from('{{cooking_ref}}')   
	          ->where("merchant_id=:merchant_id AND cooking_name=:cooking_name AND cook_id<>:cook_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':cooking_name'=>$params['cooking_name'],
	            ':cook_id'=>$id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){
	          	  $up =Yii::app()->db->createCommand()->update("{{cooking_ref}}",$params,
	          	    'cook_id=:cook_id',
	          	    array(
	          	      ':cook_id'=>$id
	          	    )
	          	  );
	          	  if($up){
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('cooking_name')
	          ->from('{{cooking_ref}}')   
	          ->where("merchant_id=:merchant_id AND cooking_name=:cooking_name",array(
	            ':merchant_id'=>$merchant_id,
	            ':cooking_name'=>$params['cooking_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){
				if(Yii::app()->db->createCommand()->insert("{{cooking_ref}}",$params)){
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}		
		
	public static function deleteCookingRef($merchant_id='', $ids=array())
	{
		$criteria = new CDbCriteria();
		$criteria->compare('merchant_id', $merchant_id);
		$criteria->addInCondition('cook_id', $ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{cooking_ref}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function getCookingRef($merchant_id='', $status='publish')
	{
		$resp = Yii::app()->db->createCommand()
          ->select("cook_id,cooking_name,cooking_name_trans,status")
          ->from('{{cooking_ref}}')          
          ->where("merchant_id=:merchant_id AND status=:status",array(
	            ':merchant_id'=>$merchant_id,
	            ':status'=>$status
	          ))	       
	      ->order('sequence asc')    
          ->queryAll();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function insertAddonItem($merchant_id='',$params=array(), $categories=array(), $id='' )
	{
		if($id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select('sub_item_name')
	          ->from('{{subcategory_item}}')   
	          ->where("merchant_id=:merchant_id AND sub_item_name=:sub_item_name AND sub_item_id<>:sub_item_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':sub_item_name'=>$params['sub_item_name'],
	            ':sub_item_id'=>$id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){
	          	  $up =Yii::app()->db->createCommand()->update("{{subcategory_item}}",$params,
	          	    'sub_item_id=:sub_item_id',
	          	    array(
	          	      ':sub_item_id'=>$id
	          	    )
	          	  );
	          	  if($up){
	          	  	 self::subitemRelationship($id,$categories);
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('sub_item_name')
	          ->from('{{subcategory_item}}')   
	          ->where("merchant_id=:merchant_id AND sub_item_name=:sub_item_name",array(
	            ':merchant_id'=>$merchant_id,
	            ':sub_item_name'=>$params['sub_item_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){								
				if(Yii::app()->db->createCommand()->insert("{{subcategory_item}}",$params)){
					self::subitemRelationship(Yii::app()->db->getLastInsertID(),$categories);
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}
	
	public static function subitemRelationship($sub_item_id='', $categories='')
	{
		$stmt=""; $values='';
		if(is_array($categories) && count($categories)>=1){
			
			Yii::app()->db->createCommand("DELETE FROM {{subcategory_item_relationships}} 
			WHERE sub_item_id =".FunctionsV3::q($sub_item_id)." ")->query();
			
			foreach ($categories as $id) {
				$values.="(".FunctionsV3::q($sub_item_id).",".FunctionsV3::q($id)."),\n";
			}
			$values = substr($values,0,-2);			
			$stmt = "
			INSERT INTO {{subcategory_item_relationships}} ( sub_item_id, subcat_id)
			VALUES $values
			";			
			Yii::app()->db->createCommand($stmt)->query();
		}
	}
	
	public static function deleteSubItem($merchant_id='', $ids=array())
	{
				
		$stmt="SELECT id FROM {{order_details_addon}} WHERE sub_item_id IN (".implode(",", (array) $ids).")";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			throw new Exception( "Cannot delete records it has reference in another table" );
		}		
		
		$criteria = new CDbCriteria();
		$criteria->compare('merchant_id', $merchant_id);
		$criteria->addInCondition('sub_item_id', $ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{subcategory_item}}', $criteria);		
		$resp = $command->execute();		
		if($resp){		
			$criteria = new CDbCriteria();		
		    $criteria->addInCondition('sub_item_id', $ids );
		    $command = Yii::app()->db->commandBuilder->createDeleteCommand('{{subcategory_item_relationships}}', $criteria);		
		    $resp = $command->execute();		
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function getAddonCategoryWithItem($merchant_id='',$subcat_id='')
	{
		$db = new DbExt();
		$stmt="
		SELECT 
		a.subcat_id,a.sub_item_id,
		b.sub_item_name,b.item_description,b.price,b.photo,b.sequence,b.sub_item_name_trans,b.item_description_trans,
		(
		 select subcategory_name from {{subcategory}}
		 where subcat_id = a.subcat_id
		 limit 0,1
		) as subcategory_name
		
		FROM {{subcategory_item_relationships}} a
		
		left join {{subcategory_item}} b
		ON 
		a.sub_item_id = b.sub_item_id
		
		WHERE
		a.subcat_id = ".FunctionsV3::q($subcat_id)."
		AND 
		b.merchant_id = ".FunctionsV3::q($merchant_id)."
		ORDER BY b.sequence ASC
		LIMIT 0,500
		";		
		if($resp = $db->rst($stmt)){
			return $resp;
		}
		unset($db);
		return false;
	}
	
	public static function getCategory($merchant_id='', $status='publish')
	{
		$resp = Yii::app()->db->createCommand()
          ->select("cat_id,category_name,category_description,status")
          ->from('{{category}}')          
          ->where("merchant_id=:merchant_id AND status=:status",array(
	            ':merchant_id'=>$merchant_id,
	            ':status'=>$status
	          ))	       
	      ->order('sequence asc')    
          ->queryAll();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function getIngredients($merchant_id='', $status='publish')
	{
		$resp = Yii::app()->db->createCommand()
          ->select("ingredients_id,ingredients_name,ingredients_name_trans,status")
          ->from('{{ingredients}}')          
          ->where("merchant_id=:merchant_id AND status=:status",array(
	            ':merchant_id'=>$merchant_id,
	            ':status'=>$status
	          ))	       
	      ->order('sequence asc')    
          ->queryAll();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}	
	
	public static function getSizes($merchant_id='', $status='publish')
	{
		$resp = Yii::app()->db->createCommand()
          ->select("size_id,size_name,size_name_trans,status")
          ->from('{{size}}')          
          ->where("merchant_id=:merchant_id AND status=:status",array(
	            ':merchant_id'=>$merchant_id,
	            ':status'=>$status
	          ))	       
	      ->order('sequence asc')    
          ->queryAll();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}	
	
	public static function dropdownFormat($data=array(),$value='', $label='')
	{
		$list = array();
		$list['']='';
		if(is_array($data) && count($data)>=1){
			foreach ($data as $val) {
				if(isset($val[$value]) && isset($val[$label])){
			 	   $list[ $val[$value] ] = $val[$label];
				}
			}
		}
		return $list;
	}
	
	public static function twoFlavorSelection()
	{
		return array(
	         ''=>t("Select options"),
	         'left'=>t("left"),
	         'right'=>t("Right")
	       );
	}
	
	public static function generateFoodToken()
	{		
		$token=FunctionsV3::generateCode(20);
		$resp = Yii::app()->db->createCommand()
          ->select('item_token')
          ->from('{{item}}')   
          ->where("item_token=:item_token",array(
            ':item_token'=>$token            
          ))	          
          ->limit(1)
          ->queryRow();		
	    if($resp){
	    	$token=self::generateFoodToken();
	    }
	    return $token;
	}
	
	public static function generateFoodSizeToken()
	{		
		$token=FunctionsV3::generateCode(20);
		$resp = Yii::app()->db->createCommand()
          ->select('item_token')
          ->from('{{item_relationship_size}}')   
          ->where("item_token=:item_token",array(
            ':item_token'=>$token            
          ))	          
          ->limit(1)
          ->queryRow();		
	    if($resp){
	    	$token=self::generateFoodToken();
	    }
	    return $token;
	}
	
    public static function insertFood($merchant_id='',$params=array(), $item_id='',$raw_data=array())
	{							
		if($item_id>0){
			$resp = Yii::app()->db->createCommand()
	          ->select('item_name')
	          ->from('{{item}}')   
	          ->where("merchant_id=:merchant_id AND item_name=:item_name AND item_id<>:item_id",array(
	            ':merchant_id'=>$merchant_id,
	            ':item_name'=>$params['item_name'],
	            ':item_id'=>$item_id
	          ))	          
	          ->limit(1)
	          ->queryRow();		
	          if(!$resp){
	          		          	  
	          	  self::prepareCheckSKUexist(
	          	    isset($raw_data['sku'])?$raw_data['sku']:''
	          	    ,$item_id, 
	          	    isset($raw_data['size'])?$raw_data['size']:'' 
	          	  );	          	  
	          	
	          	  $up =Yii::app()->db->createCommand()->update("{{item}}",$params,
	          	    'item_id=:item_id',
	          	    array(
	          	      ':item_id'=>$item_id
	          	    )
	          	  );
	          	  if($up){
	          	  	
	          	  	self::InsertItemSize($merchant_id,$item_id,$raw_data);
					self::InsertItemCategoryRelationship($merchant_id,$item_id,$raw_data);
					self::InsertItemRelationshipSubcategory($merchant_id,1,$raw_data);
	          	  	
	          	  	 return true;
	          	  } else throw new Exception( "Failed cannot update records" );
	          } else throw new Exception( "Food name already exist" );
		} else {			
			$resp = Yii::app()->db->createCommand()
	          ->select('item_name')
	          ->from('{{item}}')   
	          ->where("merchant_id=:merchant_id AND item_name=:item_name",array(
	            ':merchant_id'=>$merchant_id,
	            ':item_name'=>$params['item_name']
	          ))	          
	          ->limit(1)
	          ->queryRow();		
			if(!$resp){					
				
				self::prepareCheckSKUexist(
				  isset($raw_data['sku'])?$raw_data['sku']:''
				);				
							
				if(Yii::app()->db->createCommand()->insert("{{item}}",$params)){
					$last_id = Yii::app()->db->getLastInsertID();
					
					self::InsertItemSize($merchant_id,$last_id,$raw_data,true);
					self::InsertItemCategoryRelationship($merchant_id,$last_id,$raw_data);
					self::InsertItemRelationshipSubcategory($merchant_id,1,$raw_data);
					
					return true;
				} else throw new Exception( "Failed cannot insert records" );
			} else throw new Exception( "Food name already exist" );
		}		
		
		throw new Exception( "an error has occurred" );
	}	
	
	public static function prepareCheckSKUexist($data='',$item_id='', $size='')
	{		
		if(is_array($data) && count($data)>=1){
			foreach ($data as $key=>$sku) {								
				if(self::checkSKUexist($sku,$item_id, isset($size[$key])?$size[$key]:'' )){					
					throw new Exception( translate("Item with SKU:[sku] already exists",array(
					 '[sku]'=>$sku
					)) );					
				}
			}
		} else {
			if(self::checkSKUexist($data,$item_id)){
				throw new Exception( translate("Item with SKU:[sku] already exists",array(
				 '[sku]'=>$data
				)) );
			}
		}
	}
	
	public static function checkSKUexist($sku='',$item_id='', $size_id=0)
	{		
		$resp='';
		if($item_id>0){
			$res = Yii::app()->db->createCommand()
	          ->select('item_size_id')
	          ->from('{{item_relationship_size}}')   
	          ->where("item_id=:item_id and size_id=:size_id",array(
	            ':item_id'=>$item_id,
	            ':size_id'=>$size_id
	          ))	          
	          ->limit(1)
	          ->queryRow();
	          if($res){	          		          	 
	          	 $resp = Yii::app()->db->createCommand()
		          ->select('sku')
		          ->from('{{item_relationship_size}}')   
		          ->where("sku=:sku AND item_size_id<>:item_size_id",array(
		            ':sku'=>$sku,	      
		            ':item_size_id'=>$res['item_size_id']
		          ))	          
		          ->limit(1)
		          ->queryRow();
	          }	          
		} else {
			$resp = Yii::app()->db->createCommand()
	          ->select('sku')
	          ->from('{{item_relationship_size}}')   
	          ->where("sku=:sku",array(
	            ':sku'=>$sku,	            
	          ))	          
	          ->limit(1)
	          ->queryRow();
		}
		if($resp){
			return true;
		}
		return false;		
	}
	
	public static function InsertItemSize($merchant_id='',$item_id='',$data=array(),$is_new=false)
	{	
			
		if(isset($data['size'])){
			$params=array();
			if(is_array($data['size']) && count($data['size'])>=1){
				foreach ($data['size'] as $size_key=>$size_id) {
					$key_use = $size_key;
					$available_use = $size_key;
					if(!$is_new){
						$available_use = $size_id;
					}
							
					$params=array(
					  'merchant_id'=>(integer)$merchant_id,
					  'item_token'=>self::generateFoodSizeToken(),
					  'item_id'=>(integer)$item_id,
					  'size_id'=>(integer)$size_id,
					  'price'=>isset($data['price'][$key_use])?(float)$data['price'][$key_use]:0,
					  'cost_price'=>isset($data['cost'][$key_use])?(float)$data['cost'][$key_use]:0,
					  'sku'=>isset($data['sku'][$key_use])?(float)$data['sku'][$key_use]:0,
					  'available'=>isset($data['available'][$available_use])?(float)$data['available'][$available_use]:0,
					  'created_at'=>FunctionsV3::dateNow(),
					  'low_stock'=>isset($data['low_stock'][$key_use])?(float)$data['low_stock'][$key_use]:0,
					);		
					
					$in_stock = isset($data['in_stock'][$size_key])?(float)$data['in_stock'][$size_key]:0;					
									
					if($size_id>0 && $params['price']>0){
						$item_size_id = isset($data['item_size_id'][$size_key])?(integer)$data['item_size_id'][$size_key]:0;
						if($item_size_id>0){
							unset($params['item_token']);
				            unset($params['created_at']);
				            $params['updated_at']=FunctionsV3::dateNow();
							$up =Yii::app()->db->createCommand()->update("{{item_relationship_size}}",$params,
				          	    'item_size_id=:item_size_id',
				          	    array(
				          	      ':item_size_id'=>$item_size_id
				          	    )
				          	 );
						} else {
						   $i = Yii::app()->db->createCommand()->insert("{{item_relationship_size}}",$params);
						}
						StocksWrapper::updateStocksEditItem($params['sku'],$params['cost_price'],$in_stock,$merchant_id,UserWrapper::getUserName());
						self::insertSKU();
					}					
				}
			}
		} else {		
			$item_size_id = isset($data['item_size_id'])?(integer)$data['item_size_id']:'';			
			$params=array(
			  'merchant_id'=>$merchant_id,
			  'item_token'=>self::generateFoodSizeToken(),
			  'item_id'=>$item_id,			  
			  'price'=>isset($data['single_price'])?(float)$data['single_price']:0,
			  'cost_price'=>isset($data['cost_price'])?(float)$data['cost_price']:0,
			  'sku'=>isset($data['sku'])?$data['sku']:'',
			  'available'=>isset($data['available'])?(integer)$data['available']:0,
			  'created_at'=>FunctionsV3::dateNow(),			  
			  'low_stock'=>isset($data['low_stock'])?(float)$data['low_stock']:0,
			  'size_id'=>0
			);									
						
			$in_stock = isset($data['in_stock'])?(float)$data['in_stock']:0;			
						
			if($item_size_id>0){				
				unset($params['item_token']);
				unset($params['created_at']);
				$params['updated_at']=FunctionsV3::dateNow();
				$up =Yii::app()->db->createCommand()->update("{{item_relationship_size}}",$params,
	          	    'item_size_id=:item_size_id',
	          	    array(
	          	      ':item_size_id'=>$item_size_id
	          	    )
	          	  );
	          	 
	            // DELETE OLD SIZE 
	            $stmt_del = "
	            DELETE FROM {{item_relationship_size}}
	            WHERE
	            item_id = ".FunctionsV3::q($item_id)."
	            AND 
	            size_id > 0
	            ";
	            Yii::app()->db->createCommand($stmt_del)->query();
	          	  	   
			} else {
				$i = Yii::app()->db->createCommand()->insert("{{item_relationship_size}}",$params);	
			}
			StocksWrapper::updateStocksEditItem($params['sku'], $params['cost_price'], $in_stock, $merchant_id, UserWrapper::getUserName() );
			self::insertSKU();
		}
	}		
	
	public static function deleteItem($merchant_id='', $item_ids=array())
	{			
				
		$stmt="SELECT id FROM {{order_details}} WHERE item_id IN (".implode(",", (array) $item_ids).")";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			throw new Exception( "Cannot delete records it has reference in another table" );
		}		
			
		
		$criteria = new CDbCriteria();
		$criteria->compare('merchant_id', $merchant_id);
		$criteria->addInCondition('item_id', $item_ids );
		$command = Yii::app()->db->commandBuilder->createDeleteCommand('{{item}}', $criteria);		
		$resp = $command->execute();		
		if($resp){
			
			self::removeItemPictureFromDisk($merchant_id,$item_ids);			
			self::deleteInventoryStocks($merchant_id,$item_ids);
			
			if(is_array($item_ids) && count($item_ids)>=1){
				foreach ($item_ids as $item_id) {
				   self::deleteItemSize($merchant_id,$item_id);
	               self::deleteCategoryRelationship($merchant_id,$item_id);
	               self::deleteItemRelationshipSubcategory($merchant_id,$item_id);
				}			
			}
			
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function deleteInventoryStocks($merchant_id='', $item_ids=array())
	{
		$resp = Yii::app()->db->createCommand()
          ->select('sku')
          ->from('{{item_relationship_size}}')           
          ->where(array(
           'in','item_id',$item_ids
          ))
          ->andWhere('merchant_id=:merchant_id',array(
            ':merchant_id'=>$merchant_id
          ))
          ->queryAll();		          	         
        if($resp){
        	$in = '';
        	foreach ($resp as $val) {
        		$in.= FunctionsV3::q($val['sku']).",";
        	}
        	$in = substr($in,0,-1);
        	$stmt="DELETE FROM {{inventory_stocks}}
        	WHERE merchant_id=".FunctionsV3::q($merchant_id)."
        	AND sku IN ($in)
        	";
        	Yii::app()->db->createCommand($stmt)->query();
        }
        return false;     
	}
	
	public static function removeItemPictureFromDisk($merchant_id='',$item_ids=array())
	{				
		$resp = Yii::app()->db->createCommand()
          ->select('photo')
          ->from('{{item}}')           
          ->where(array(
           'in','item_id',$item_ids
          ))
          ->andWhere('merchant_id=:merchant_id',array(
            ':merchant_id'=>$merchant_id
          ))
          ->queryAll();		          	         
        if($resp){
        	foreach ($resp as $photo) {
        		$file_path = FunctionsV3::uploadPath()."/$photo[photo]";
        		if(file_exists($file_path)){
        			@unlink($file_path);
        		}
        	}
        }
        return false;     
	}
	
	public static function deleteItemSize($merchant_id='', $item_id='')
	{
		$resp = Yii::app()->db->createCommand()->delete('{{item_relationship_size}}', 
		'merchant_id=:merchant_id AND item_id=:item_id ', 
		 array( 
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));				
	}
	
	public static function deleteItemSizeByToken($merchant_id='', $item_token='')
	{
		$resp = Yii::app()->db->createCommand()->delete('{{item_relationship_size}}', 
		'merchant_id=:merchant_id AND item_token=:item_token ', 
		 array( 
		  ':merchant_id'=>$merchant_id,
		  ':item_token'=>$item_token
		));			
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function deleteItemSizeBySku($merchant_id='', $item_token='')
	{		
		$resp = Yii::app()->db->createCommand()->delete('{{item_relationship_size}}', 
		'merchant_id=:merchant_id AND sku=:sku ', 
		 array( 
		  ':merchant_id'=>$merchant_id,
		  ':sku'=>$item_token
		));			
		if($resp){
			return true;
		} else throw new Exception( "Failed cannot delete records" );
	}
	
	public static function InsertItemCategoryRelationship($merchant_id='', $item_id='', $data=array())
	{
		self::deleteCategoryRelationship($merchant_id,$item_id);
		if(isset($data['category'])){
			if(is_array($data['category']) && count($data['category'])>=1){
				foreach ($data['category'] as $cat_id) {
					$params = array(
					  'merchant_id'=>$merchant_id,
					  'item_id'=>$item_id,
					  'cat_id'=>$cat_id,
					);
					$i = Yii::app()->db->createCommand()->insert("{{item_relationship_category}}",$params);
				}
			}
		}
	}
	
	public static function deleteCategoryRelationship($merchant_id='', $item_id='')
	{
		$resp = Yii::app()->db->createCommand()->delete('{{item_relationship_category}}', 
		'merchant_id=:merchant_id AND item_id=:item_id ', 
		 array( 
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));		
	}
	
	public static function InsertItemRelationshipSubcategory($merchant_id='', $item_id='', $data=array())
	{
		self::deleteItemRelationshipSubcategory($merchant_id,$item_id);
		if(isset($data['sub_item_id'])){
			if(is_array($data['sub_item_id']) && count($data['sub_item_id'])>=1){
				foreach ($data['sub_item_id'] as $subcat_id=>$val) {
					$params=array(
					  'merchant_id'=>$merchant_id,
					  'item_id'=>$item_id,
					  'subcat_id'=>$subcat_id
					);
					$i = Yii::app()->db->createCommand()->insert("{{item_relationship_subcategory}}",$params);
				}
			}
		}
	}
	
	public static function deleteItemRelationshipSubcategory($merchant_id='', $item_id='')
	{
		$resp = Yii::app()->db->createCommand()->delete('{{item_relationship_subcategory}}', 
		'merchant_id=:merchant_id AND item_id=:item_id ', 
		 array( 
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));		
	}
	
	public static function insertSKU($item_id=1)
	{
		$i = Yii::app()->db->createCommand()->insert("{{item_sku}}",array(
			  'item_id'=>$item_id
			));				
	}
	public static function autoGenerateSKU()
	{
		$db = new DbExt();
		$stmt="SHOW TABLE STATUS LIKE '{{item_sku}}'";
		if ($res = $db->rst($stmt)){			
			$res = $res[0];
			return $res['Auto_increment'];
		}
		unset($db);
		return false;
	}
	
	public static function getItemSizePrice($merchant_id='', $item_id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select()
          ->from('{{item_relationship_size}}')   
          ->where("merchant_id=:merchant_id AND item_id=:item_id",array(
             ':merchant_id'=>$merchant_id,
             ':item_id'=>$item_id
          )) 
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function getItemSizePrices($merchant_id='', $item_id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select()
          ->from('{{item_relationship_size}}')   
          ->where("merchant_id=:merchant_id AND item_id=:item_id",array(
             ':merchant_id'=>$merchant_id,
             ':item_id'=>$item_id
          ))           
          ->order('item_size_id ASC')
          ->queryAll();	
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function getItemBySku($merchant_id='', $sku='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select('')
          ->from('{{view_item}}')   
          ->where("merchant_id=:merchant_id AND sku=:sku",array(
             ':merchant_id'=>$merchant_id,
             ':sku'=>$sku
          ))           
          ->limit(1)
          ->queryRow();		
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function getItemSizePricesWithStocks($merchant_id='', $item_id='')
	{
		$resp = Yii::app()->db->createCommand()
          ->select()
          ->from('{{view_item_stocks}}')   
          ->where("merchant_id=:merchant_id AND item_id=:item_id",array(
             ':merchant_id'=>$merchant_id,
             ':item_id'=>$item_id
          ))           
          ->order('item_size_id ASC')
          ->queryAll();	
          
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function searchItem($merchant_id='', $item_name='', $track_stock='')
	{		
		
		if(!empty($item_name)){
						
			$where = 'merchant_id=:merchant_id';
			$where_val = array(
			  ':merchant_id'=>$merchant_id
			);
			if($track_stock==1){
				$where = 'merchant_id=:merchant_id AND track_stock=:track_stock';
				$where_val = array(
				  ':merchant_id'=>$merchant_id,
				  ':track_stock'=>1
				);
			}			
			
			$resp = Yii::app()->db->createCommand()
	          ->select('merchant_id,item_id,item_name,sku,size_name,item_size_id,cost_price,available_stocks,incoming_balance')
	          ->from('{{view_item_stocks}}')   
	          ->where(array('like', 'item_name', "%$item_name%" ))
	          /*->andWhere('merchant_id=:merchant_id',array(
	            ':merchant_id'=>$merchant_id
	          ))*/
	          ->andWhere($where,$where_val)
	          ->order('item_name ASC')
	          ->queryAll();		          	         
		} else {			
			
			$where = 'merchant_id=:merchant_id';
			$where_val = array(
			  ':merchant_id'=>$merchant_id
			);
			if($track_stock==1){
				$where = 'merchant_id=:merchant_id AND track_stock=:track_stock';
				$where_val = array(
				  ':merchant_id'=>$merchant_id,
				  ':track_stock'=>1
				);
			}				
			$resp = Yii::app()->db->createCommand()
	          ->select('merchant_id,item_id,item_name,sku,size_name,item_size_id,cost_price,available_stocks,incoming_balance')
	          ->from('{{view_item_stocks}}')   
	          ->where($where,$where_val)	          
	          ->order('item_name ASC')
	          ->queryAll();	
		}
          		
        if($resp){
        	return $resp;
        }
        return false;     
	}
	
	public static function prettyName($item_name='', $size_name='')
	{
		if(!empty($size_name)){
			return translate("[item_name] ([size_name])",array(
			  '[item_name]'=>$item_name,
			  '[size_name]'=>$size_name
			));
		}
		return $item_name;
	}
	
	public static function inlineUpdatePrice($merchant_id=0, $item_id=0,$params1=array(), $params2=array())
	{
		 $up =Yii::app()->db->createCommand()->update("{{item}}",$params1,
      	    'item_id=:item_id AND merchant_id=:merchant_id ',
      	    array(
      	      ':item_id'=>$item_id,
      	      ':merchant_id'=>$merchant_id
      	    )
      	  );
      	  if($up){
      	  	 
      	  	 $up =Yii::app()->db->createCommand()->update("{{item_relationship_size}}",$params2,
	      	    'item_id=:item_id AND merchant_id=:merchant_id ',
	      	    array(
	      	      ':item_id'=>$item_id,
	      	      ':merchant_id'=>$merchant_id
	      	    )
	      	  );
	      	  if($up){
	      	  	return true;
	      	  } else throw new Exception( "Failed cannot update records" );
      	  
      	  } else throw new Exception( "Failed cannot update records" );
	}
	
	public static function inlineUpdatePriceBySku($merchant_id='', $sku='', $new_price=0)
	{
		if($sku>0){
			$stmt="
			SELECT 
			a.item_id, 
			a.size_id,
			b.price 
			FROM 
			{{item_relationship_size}} a
			left join {{item}} b
			on
			a.item_id = b.item_id
			WHERE
			a.sku = ".FunctionsV3::q($sku)."		
			LIMIT 0,1
			";
			if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
				$params = array(
				  'price'=> (float) $new_price,
				  'updated_at'=>FunctionsV3::dateNow()
				);				
				$up = Yii::app()->db->createCommand()->update("{{item_relationship_size}}",$params,
		      	    'sku=:sku AND merchant_id=:merchant_id ',
		      	    array(
		      	      ':sku'=>$sku,
		      	      ':merchant_id'=>$merchant_id
		      	    )
		      	  );
		      	if($up){
		      		if(is_array($price = json_decode($res['price'],true))){		      		   
		      		   if(array_key_exists($res['size_id'],(array)$price)){
		      		   	  $price[$res['size_id']]=(float)$new_price;		 
		      		   	       		   	  		      		   	  
		      		   	  $params2 = array(
		      		   	    'price'=>json_encode($price),
		      		   	    'date_modified'=>FunctionsV3::dateNow(),
		      		   	    'ip_address'=>$_SERVER['REMOTE_ADDR'],
		      		   	  );		      		   	  
		      		   	  $up = Yii::app()->db->createCommand()->update("{{item}}",$params2,
				      	    'item_id=:item_id AND merchant_id=:merchant_id ',
				      	    array(
				      	      ':item_id'=>$res['item_id'],
				      	      ':merchant_id'=>$merchant_id
				      	    )
				      	  );
		      		   }		      		   
		      		}
		      	  	return true;
	      	    } else throw new Exception( "Failed cannot update records" );
				
			} else throw new Exception( "Record not found" );
		} else throw new Exception( "Invalid sku number" );
	}
	
	public static function autoFillItem($merchant_id='', $supplier_id = 0, $type='')
	{
		 $and = '';
		 if($type=="lowstock"){		 	
		 	$and.=" AND low_stock>=available_stocks";
		 }
		 
		 $stmt="
		 SELECT merchant_id,item_id,item_name,sku,size_name,item_size_id,cost_price,available_stocks,incoming_balance
		 FROM {{view_item_stocks}}
		 WHERE
		 merchant_id = ".FunctionsV3::q($merchant_id)."
		 AND
		 supplier_id = ".FunctionsV3::q($supplier_id)."
		 $and
		 ";		 		 
		 if($res = Yii::app()->db->createCommand($stmt)->queryAll()){		 	
		 	return $res;
		 } else throw new Exception( "No records found" );
	}
	
}
/*END CLASS*/