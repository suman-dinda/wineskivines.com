<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_ajax",
  'onsubmit'=>"return false;"	  
)); 
?> 	
	
<DIV class="main_box_wrap">

<div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<div class="row">
      <div class="col-md-6">
      
      
       <?php MultilangForm::setForm(array(
	      'Addon Name','Description',''
	   ), array(
	      'sub_item_name','item_description','sequence'
	   ), (array)$data,
	      array(
	        true, false, false
	      ),array(
	       'text','text','hidden'
	      )
	   );
	   
	   MultilangForm::setStatusList($data);
	   ?>	  
      
      </div> <!--col-->
      
      <div class="col-md-6 pad_form">
      
       <h3><?php echo translate("Category")?></h3> 
       
       <?php 
       $category_data = isset($data['category'])?json_decode($data['category'],true):false;	   
       ?>
       
       <?php if(is_array($category) && count($category)>=1):?>
	   <?php foreach ($category as $key => $category_val): ?>
	   
	   <?php 
	    $min_length= array(
	   'required'=>false,
	   'value'=>$category_val['subcat_id'],
	   'id'=>"dish_".$category_val['subcat_id'],
	   );
	   if($key==0){
	  	 $min_length = array(
	  	   'required'=>true,
	  	   'minlength'=>1,
	  	   'value'=>$category_val['subcat_id'],
	       'id'=>"dish_".$category_val['subcat_id'],
	  	 );
	   }		  	
	   echo ItemHtmlWrapper::checkboxInline('category[]',$category_val['subcategory_name'],
	   in_array($category_val['subcat_id'] , (array) $category_data)?true:false,
	   $min_length
	   );
	   ?>
	   
	   <?php endforeach;?>
	   <?php endif;?>
	   
	   <div class="form-group pad_form_top row"> 
		   <div class="col-md-3">
		   <?php
		   echo CHtml::textField('price',
		   isset($data['price'])?$data['price']:''
		   ,array(
		    'class'=>'form-control numeric_only',
		    'placeholder'=>translate("Price"),
		   ));
		   ?>  	
		   </div>   
	   </div>
	   
	    <p><?php echo translate("Featured image")?></p>
	  <div class="div_dropzone dropzone" id="single_dropzone">
	    <div class="dz-default dz-message">
	     <i class="fas fa-cloud-upload-alt"></i>
	     <p><?php echo translate("Drop files here to upload")?></p>
	     </div>
	  </div>
      
      </div> <!--col-->
    </div> <!--row-->   
	
	</div> <!--body-->
</div> <!--card-->	

</DIV> <!--box-->


<div class="floating_action">
       <a href="<?php echo Yii::app()->createUrl(APP_FOLDER.'/item/addon_item_list')?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?>">
       <?php echo translate("CANCEL")?>
       </a>
       
       <?php if(is_array($data) && count($data)>=1):?>
       <a href="javascript:;" class="<?php echo ItemHtmlWrapper::deleteBtnClass()?> delete_record"><?php echo translate("DELETE")?></a>        
       <?php endif;?>
       
       <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo translate("SAVE")?></button>                  
       
</div>

<?php echo CHtml::endForm(); ?>