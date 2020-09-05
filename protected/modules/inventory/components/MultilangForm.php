<?php
class MultilangForm
{
	
	public static function setForm($field_label=array(),$field_name=array(),$data=array(),
	$required=array(),$field_type=array(), $field_default_value=array() )
	{
		$multi_lang = ItemWrap::isMultiLanguage();
		
		if($multi_lang){
			$fields = FunctionsV3::getLanguageList(false);
			$fields = array_reverse($fields);
			$fields['default'] = 'default';
			$fields = array_reverse($fields);		
		} else {
			$fields['default'] = 'default';
		}
		$x=1;		
		?>
		
	    <ul class="nav nav-tabs" id="tab_settings" role="tablist">	 
	      <?php foreach ($fields as $key=>$val):?>
		  <li class="nav-item" <?php echo $multi_lang!=true?'style="display:none;"':'';?> >
		    <a class="nav-link <?php echo $x==1?"active":''?>" data-toggle="tab" 
		    href="#nav_<?php echo $key?>" role="tab" aria-selected="false">
		     <?php echo translate($val)?>
		    </a>
		  </li>	
		  <?php $x++;?>
		  <?php endforeach;?>  	
	    </ul>
	 
	    
	    <?php 
		$data_json = array();
		if(is_array($data) && count($data)>=1){
			if($multi_lang){
				foreach ($field_name as $field_key=>$field_id){
					if(isset($data[$field_id."_trans"])){
					   $data_json[$field_id."_trans"] = json_decode($data[$field_id."_trans"],true);
					}
				}
			}
		}				
		?>	  
	    
	    <?php $x=1;?>
	   <div class="tab-content" >
	     <?php foreach ($fields as $key=>$val):?>
		  <div class="tab-pane fade <?php echo $x==1?"active show":''?>" id="nav_<?php echo $key?>" role="tabpanel">
		  <?php foreach ($field_name as $field_key=>$field_id):?>
		  <?php 
		  $selected_value = '';
		  if($key!="default"){
		  	 $field_names = $field_id."_trans[$key]";
		  	 if(array_key_exists($field_id."_trans",(array)$data_json)){
		  	 	$selected_value = isset($data_json[$field_id."_trans"][$key])?$data_json[$field_id."_trans"][$key]:'';
		  	 }
		  } else {
		  	$field_names = $field_id;
		  	$selected_value = array_key_exists($field_names,(array)$data)?$data[$field_names]:'';
		  }		  
		  ?>
		  <div class="form-group">    	  
		    <label for="<?php echo $field_names;?>" class="bmd-label-static">
		      <?php echo isset($field_label[$field_key])? translate($field_label[$field_key]) :'';?>
		    </label>  
		   <?php 
		   
		   $_field_type =  isset($field_type[$field_key])?$field_type[$field_key]:'text';
		   
		   switch ($_field_type) {
		   	case "list":
		   		
		   		echo CHtml::dropDownList($field_names,
			    $selected_value,
			    isset($field_default_value[$field_key])?$field_default_value[$field_key]:array()
			    ,array(
			      'class'=>'form-control',	      
			      'placeholder'=>isset($field_label[$field_key])? translate($field_label[$field_key]) :'',
			      'required' =>$required[$field_key]==1?true:false
			    ));
		   		
		   		break;
		   		
		   	case "hidden":		
		   	    echo CHtml::hiddenField($field_names,$selected_value);
		   	    break;
		   	    
		   	case "textarea":
		   		echo CHtml::textArea($field_names,
			    $selected_value
			    ,array(
			      'class'=>'form-control',	      
			      'placeholder'=>isset($field_label[$field_key])? translate($field_label[$field_key]) :'',
			      'required' =>$required[$field_key]==1?true:false
			    ));
			   		
		   		break;
		   
		   	default:
		   		
		   		echo CHtml::textField($field_names,
			    $selected_value
			    ,array(
			      'class'=>'form-control',	      
			      'placeholder'=>isset($field_label[$field_key])? translate($field_label[$field_key]) :'',
			      'required' =>$required[$field_key]==1?true:false
			    ));
			   		
		   		break;
		   }		    
		    ?>
		  </div> 
		  <?php endforeach;?>
		   
		  
		  </div>			
		  <?php $x++;?>	
		  <?php endforeach;?> 
	   </div>
	 
		<?php
				
	}
	
	
	public static function setStatusList($data=array())
	{
		?>
		<div class="form-group" style="padding-left:7px;padding-right:7px;"> 
		<?php 
		echo CHtml::dropDownList('status',
		  isset($data['status'])?$data['status']:"",
		  (array)statusList(),          
		  array(
		  'class'=>'form-control',	      
		  'required'=>"required"
		  ));
		?>
		</div>
		<?php
	}
	
}
/*end class*/