<?php $x=0;?>
<div class="dropdown">
   <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" 
     aria-haspopup="true" aria-expanded="true">
     <i class="glyphicon glyphicon-cog"></i>
     <span class="filter_label" data-field="<?php echo $data_field?>"><?php echo translate($default_selection)?></span>
     <span class="caret"></span>
   </button>
  
   <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">  
      <?php              
      foreach ($list as $list_key=>$list_val):?>
    <li>
      <label>       
        <?php 
        
        $class_name = "filter_data";
        if($list_key=="all"){
        	$class_name = "filter_all";
        }
        
        $field_name = $name."[".$x."]";                
        
        if($type=="checkbox"){
	        echo CHtml::checkBox($field_name,true,array(
	         'value'=>$list_key,
	         'class'=>$class_name
	        ));
        } else {
        	echo CHtml::radioButton($name, $x==0?true:false,array(
	         'value'=>$list_key,
	         'class'=>$class_name
	        ));
        }
        $x++;
        ?>
        
        <?php echo $list_val;?>
        
      </label>      
      <?php if($list_key=="all"):?>
      <hr></hr>
      <?php endif;?>
    </li>        
    <?php endforeach;?>
    </ul>
  </div> <!--dropdown-->