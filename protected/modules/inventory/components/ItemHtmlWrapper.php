<?php
class ItemHtmlWrapper
{
	public static function ListAddon($data=array(), $options=array() )
	{
		ob_start();		
		?>
		
		<?php if(is_array($data) && count($data)>=1):?>
		<?php 		     		     
		     $data = Yii::app()->request->stripSlashes($data);
		     $subcat_id = $data[0]['subcat_id'];

		     if(!isset($options['require_addon'])){
		     	$options['require_addon']='';
		     }		     		     
		?>
		<li class="addon_row_<?php echo $subcat_id?>">
		 
		<div class="row" >	
		    <div class="col-md-5 text-left text-success">
		       <a href="javascript:;" class="pop_over remove_addon_subcategory" 
		             data-content="<?php echo translate("Remove")?>"
		             data-id="<?php echo $subcat_id?>"
		           >
			      <i class="fas fa-times-circle icon_size_medium"></i>
			    </a>
		       <b><?php echo $data[0]['subcategory_name']?></b>
		    </div> <!--col-->
		    <div class="col-md-7 text-right">
		    
		    <div class="row">
		      <div class="col-md-5">
		         <?php 
		         echo self::formSwitch("require_addon[".$subcat_id."][]",'Required',
				    $options['require_addon']==2?true:false,
				     array(
			           'value'=>2
			         )
				    );
		         ?>
		      </div>
		      <div class="col-md-7">
		       <?php 
		       echo self::formSwitch('check_all','Check/Uncheck',false,
			    array(
			          'value'=>$subcat_id,
			          'class'=>"check_all",
			        )
			    );
		       ?>
		      </div>
		    </div> <!--row-->
		      
		    
		    </div>
		  </div> <!--row-->
		  
		  <div class="row" style="padding:10px 0 20px;">		  
			  <div class="col-md-6">
			   <?php 
			   echo CHtml::dropDownList("multi_option[".$subcat_id."][]",
			   isset($options['multi_option'])?$options['multi_option']:'',
			   (array)Yii::app()->functions->multiOptions(),array(
			    'class'=>"form-control multi_option",
			   ));
			   ?>
			  </div>
			  <div class="col-md-6">
			    <div class="custom_qty_div">
			      <?php 
				    echo CHtml::textField("multi_option_value[".$subcat_id."][]",
				    isset($options['multi_option_value'])?$options['multi_option_value']:''
				    ,array(
				     'class'=>"form-control numeric_only",
				     'placeholder'=>translate("Custom Qty")
				    ));
				    ?>
			    </div> <!--custom_qty-->
			    
			    <div class="two_flavor_div">
			    <?php 
			    echo CHtml::dropDownList("two_flavors_position[".$subcat_id."][]",
			    isset($options['two_flavors_position'])?$options['two_flavors_position']:''
			    ,
			    (array)ItemWrap::twoFlavorSelection(),array(
			     'class'=>"form-control",
			    ));
			    ?>
			    </div>
			    
			  </div> <!--col-->
		  </div> <!--row-->
		  
		  <ul>
		    <li>
		     <div class="row">
		     <?php foreach ($data as $val):?>		      
		       <?php 
		        $sub_item_id_selected=false;
				if(isset($options['sub_item_id'])){
				   $sub_item_id_selected = in_array($val['sub_item_id'],$options['sub_item_id'])?true:false;
			    }
			    
			    $addon_label = translate("[name] ([price])",array(
				  '[name]'=>$val['sub_item_name'],
				  '[price]'=>FunctionsV3::prettyPrice($val['price']),
				));
		       ?>
		       <div class="col-md-6 pb-3">		       
		       
		       <?php 
		       echo self::checkboxInline("sub_item_id[".$subcat_id."][]",$addon_label,
		         $sub_item_id_selected,
		         array(
				 'value'=>$val['sub_item_id'],
				 'class'=>"check_all_$subcat_id"
				)
		       );
		       ?>
		       
		       </div> <!--col-->
		     <?php endforeach;?>
		     </div>
		    </li>
		  </ul>
		  
		</li>
		<?php endif;?>
		
		<?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}
	
	public static function noSizeForm( $data=array() )
	{
		$sku = ItemWrap::autoGenerateSKU();
		ob_start();			
		echo CHtml::hiddenField('item_size_id', isset($data['item_size_id'])?$data['item_size_id']:'' );				
		if(!isset($data['available'])){
			$data = array();
			$data['available']=1;
		}
		?>
		
		 <DIV class="pb-3">
		 		  
	      <?php 
	      echo self::formSwitch('available','The item is available for sale',
	        $data['available']==1?true:false,
	        array(
	         'value'=>1
	        )
	      );
	      ?>
	        
	      
	     </DIV>
	      
		  <div class="row">
	           <div class="col-md-6">	           
	             <div class="form-group">
	               <label for=""><?php echo translate("Price")?></label>
		           <?php 
		           echo CHtml::textField('single_price',
		           isset($data['price'])?normalPrettyPrice($data['price']):''
		           ,array(
		            'class'=>"form-control numeric_only",
		            'placeholder'=>translate("0.00"),
		            'required'=>true
		           ));
		           ?>
		         </div>	            	           
	           </div> <!--col-->
	            <div class="col-md-6">	           
	             <div class="form-group">
	               <label for=""><?php echo translate("Cost")?></label>
		           <?php 
		           echo CHtml::textField('cost_price',
		           isset($data['cost_price'])?normalPrettyPrice($data['cost_price']):''
		           ,array(
		            'class'=>"form-control numeric_only",
		            'placeholder'=>translate("0.00"),
		           ));
		           ?>
		         </div>	            	           
	           </div> <!--col-->
	        </div> <!--row-->
	        
	        <div class="row">
	         <div class="col-md-6">
	            <div class="form-group">
	               <label for=""><?php echo translate("SKU")?></label>
		           <?php 
		           echo CHtml::textField('sku',
		           isset($data['sku'])?$data['sku']:$sku
		           ,array(
		            'class'=>"form-control",
		            'readonly'=>isset($data['sku'])?true:false
		           ));
		           ?>
		         </div>	   
	         </div>
	         
	       
	        </div>
	         
		<?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}
	
	public static function withSizeForm($merchant_id='', $data=array() )
	{
		$sku = ItemWrap::autoGenerateSKU();
		$sizes = (array)ItemWrap::dropdownFormat((array)ItemWrap::getSizes($merchant_id),'size_id','size_name');
		ob_start();					
		?>
		<!--PRICE-->
	      <table class="table size_table">
	       <thead>
	        <tr>
	         <th width="3%"><?php echo translate("Available")?></th>
	         <th width="15%"><?php echo translate("Size")?></th>
	         <th width="12%"><?php echo translate("Price")?></th>
	         <th width="12%"><?php echo translate("Cost")?></th>	         	        
	         <th width="12%"><?php echo translate("In stock")?></th>	
	         <th width="12%"><?php echo translate("Low stock")?></th>	
	         <th width="12%"><?php echo translate("SKU")?></th>	        
	         <th width="3%"></th>	         
	        </tr>
	       </thead>
	       <tbody>
	        
	        	        
	        <?php if(is_array($data) && count($data)>=1):?>
	        
	        <?php foreach ($data as $val):?>
	        <tr>
	        <td>
	        
	           <?php 
	           $item_token= '';
	           if(isset($val['item_size_id'])){
	           	  $item_token = $val['sku'];
	           	  echo CHtml::hiddenField('item_size_id[]',$val['item_size_id']);
	           }
	           ?>
	           
	           
		       <?php 
		       echo self::formRadio('available['.$val['size_id'].']','',
		         $val['available']==1?true:false,
		         array(
		         'value'=>1
		        )
		       );
		       ?> 
		        
		        
	        </td>
	        
	         <td>
	         <?php 
	         echo CHtml::dropDownList('size[]',
	         isset($val['size_id'])?$val['size_id']:''
	         , (array) $sizes ,array(
	         'class'=>"form-control",
	         'required'=>true
	         ));
	         ?>
	         </td>
	         <td>
	         <?php echo CHtml::telField('price[]',
	         !empty($val['price'])?normalPrettyPrice($val['price']):''
	         ,array(
	          'class'=>"form-control numeric_only",
	          'required'=>true
	         ))?>
	         </td>
	         
	         
	         <td>
	         <?php echo CHtml::telField('cost[]',	         
	         !empty($val['cost_price'])?normalPrettyPrice($val['cost_price']):''
	         ,array(
	          'class'=>"form-control numeric_only"
	         ))?>
	         </td>
	         
	         <td>
	         <?php echo CHtml::telField('in_stock[]',
	         isset($val['available_stocks'])? InventoryWrapper::prettyQuantity($val['available_stocks']) :''
	         ,array(
	          'class'=>"form-control"
	         ))?>
	         </td>
	         
	         <td>
	         <?php echo CHtml::telField('low_stock[]',
	         isset($val['low_stock'])? InventoryWrapper::prettyQuantity($val['low_stock']) :''
	         ,array(
	          'class'=>"form-control"
	         ))?>
	         </td>
	         
	         <td>
	         <?php echo CHtml::telField('sku[]',
	         !empty($val['sku'])?$val['sku']:$sku
	         ,array(
	          'class'=>"form-control",
	          'readonly'=>isset($val['sku'])?true:false
	         ))?>
	         </td>
	         
	         <td>
	         
	         <a href="javascript:;" data-id="<?php echo $item_token?>" class="size_delete btn btn-link" style="font-size:25px;">
			    <i class="fas fa-trash"></i>
			 </a>   	         
	         </td>	         
	        </tr>
	        <?php endforeach;?>
	        
	        <?php else :?>
	        
	        <tr> 
	        <td>
	        	           
		      <?php 
		      echo self::formRadio('available[]','',true,
		      array(
		         'value'=>1
		        )
		      );
		      ?>
	        </td>
	        
	         <td>
	         <?php 
	         echo CHtml::dropDownList('size[]','', (array) $sizes ,array(
	         'class'=>"form-control",
	         'required'=>true
	         ));
	         ?>
	         </td>
	         <td>
	         <?php echo CHtml::telField('price[]','',array(
	          'class'=>"form-control numeric_only",
	          'required'=>true
	         ))?>
	         </td>
	         
	         
	         <td>
	         <?php echo CHtml::telField('cost[]','',array(
	          'class'=>"form-control numeric_only"
	         ))?>
	         </td>
	         
	          <td>
	         <?php echo CHtml::telField('in_stock[]',
	         ''
	         ,array(
	          'class'=>"form-control"
	         ))?>
	         </td>
	         
	         <td>
	         <?php echo CHtml::telField('low_stock[]',
	         ''
	         ,array(
	          'class'=>"form-control"
	         ))?>
	         </td>
	         
	         <td>
	         <?php echo CHtml::telField('sku[]',$sku,array(
	          'class'=>"form-control"
	         ))?>
	         </td>
	         
	         <td>
	         
	         <a href="javascript:;" class="size_delete btn btn-link" style="font-size:25px;">
			    <i class="fas fa-trash"></i>
			 </a>   	         
	         </td>	         
	         </tr>
	         <?php endif;?>
	         	        
	       </tbody>
	       
	       <tfoot>
	        <tr>
	         <td colspan="4">
	         
	         <a href="javascript:;" class="size_add_new_row btn btn-primary">
			    <i class="ion-plus"></i> <?php echo translate("NEW ROW")?>
			 </a>   
	         
	         </td>
	        </tr>
	       </tfoot>
	      </table>
		<?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}
	
	public static function withSizeFormTR($merchant_id='')
	{
		$sku = ItemWrap::autoGenerateSKU();		
		$sizes = (array)ItemWrap::dropdownFormat((array)ItemWrap::getSizes($merchant_id),'size_id','size_name');
		ob_start();		
		//dump($row_count);	
		?>
		 <tr> 
	        <td>
	        	           
		      <?php 
		      echo self::formRadio('available[]','',true,
		      array(
		         'value'=>1
		        )
		      );
		      ?>
	        </td>
	        
	         <td>
	         <?php 
	         echo CHtml::dropDownList('size[]','', (array) $sizes ,array(
	         'class'=>"form-control",
	         'required'=>true
	         ));
	         ?>
	         </td>
	         <td>
	         <?php echo CHtml::telField('price[]','',array(
	          'class'=>"form-control numeric_only",
	          'required'=>true
	         ))?>
	         </td>
	         
	         
	         <td>
	         <?php echo CHtml::telField('cost[]','',array(
	          'class'=>"form-control numeric_only"
	         ))?>
	         </td>
	         
	            <td>
	         <?php echo CHtml::telField('in_stock[]',
	         ''
	         ,array(
	          'class'=>"form-control"
	         ))?>
	         </td>
	         
	         <td>
	         <?php echo CHtml::telField('low_stock[]',
	         ''
	         ,array(
	          'class'=>"form-control"
	         ))?>
	         </td>
	         
	         <td>
	         <?php echo CHtml::telField('sku[]',$sku,array(
	          'class'=>"form-control"
	         ))?>
	         </td>
	         
	         <td>
	         
	         <a href="javascript:;" class="size_delete btn btn-link" style="font-size:25px;">
			    <i class="fas fa-trash"></i>
			 </a>   	         
	         </td>	         
	         </tr>
		<?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}
	
	public static function generateFormField($data=array())
	{
		$html='';
		if(is_array($data) && count($data)>=1){
			foreach ($data as $val) {								
				$html.=self::formTextField($val[0],$val[1],$val[2], isset($val[3])?$val[3]:false );
			}			
		}
		return $html;
	}
	
	public static function formTextField($field_name='',
	  $label='',$value='', $required=false, $class='', $placeholder='')
	{
		ob_start();	
		?>
		 <div class="form-group">
		    <label for=""><?php echo translate($label)?></label>
		    <?php echo CHtml::textField($field_name,
		    $value
		    ,array(
		     'class'=>"form-control $class",
		     'required'=>$required,
		     'placeholder'=>$placeholder
		    ))?>
		  </div>
		<?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}
	
	public static function formTextField2($field_name='',
	  $label='',$value='', $options=array() )
	{
		ob_start();	
		?>
		 <div class="form-group">
		    <label for=""><?php echo translate($label)?></label>
		    <?php echo CHtml::textField($field_name,
		    $value
		    ,(array)$options)?>
		  </div>
		<?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}	

	public static function formRadio($field_name='', $label='',$selected=false,$options)
	{
		ob_start();	
		?>
		<div class="checkbox">
	    <label>
	       <?php echo CHtml::checkBox($field_name,$selected,(array)$options)?>	       
	      <span class="checkbox-decorator">
	        <span class="check"></span>
	           <div class="ripple-container"></div>
	        </span>&nbsp;<?php echo translate($label)?>
	     </label>
	    </div>
	    <?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}
	
	public static function checkboxInline($field_name='', $label='',$selected=false,$options=array())
	{
		ob_start();	
		?>
		 <label class="checkbox-inline">
	    <?php echo CHtml::checkBox($field_name,$selected,(array)$options)?>	   
	    <span class="checkbox-decorator">
	    <span class="check"></span>
	      <div class="ripple-container"></div>
	    </span> <?php echo translate($label)?>
	    </label>
		<?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}
	
	public static function formSwitch($field_name='', $label='',$selected=false,$options=array())
	{
		ob_start();	
		?>		
		<div class="switch">
	    <label>	      
	     <?php echo CHtml::checkBox($field_name,$selected,(array)$options)?>
	      <span class="bmd-switch-track">
	        <div class="ripple-container"></div>
	      </span>
	       &nbsp;<?php echo translate($label)?>
	    </label>
	    </div>
	    <?php
		$forms = ob_get_contents();
        ob_end_clean();	
        return $forms;
	}
	
	public static function cancelBtnClass()
	{
		return 'btn btn-raised';
	}
	
	public static function deleteBtnClass()
	{
		return 'btn btn-raised btn-danger';
	}
	
	public static function saveBtnClass()
	{
		return 'btn btn-raised btn-primary';
	}
	
	public static function newBtnClass()
	{
		return 'btn btn-raised btn-info';
	}
	
	public static function refreshBtnClass()
	{
		return 'btn btn-raised';
	}
	
	public static function filterBtnClass()
	{
		return 'btn btn-raised btn-primary';
	}
	
}
/*end class*/