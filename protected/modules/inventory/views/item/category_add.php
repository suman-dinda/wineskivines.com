<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_ajax",
  'onsubmit'=>"return false;"	  
)); 
?> 	
	
<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-6">

 <div class="card card_medium" id="box_wrap">
	<div class="card-body">
			 
	   <?php 
	   MultilangForm::setForm(array(
	      'Category name','Description'
	   ), array(
	      'category_name','category_description'
	   ), (array)$data,
	      array(
	        true, false
	      ),array(
	       'text','text'
	      ),array(
	        '',''
	      )
	   );
	   
	   MultilangForm::setStatusList($data);
	   ?>
	   	   
	  
	  <p><?php echo translate("Featured image")?></p>
	  <div class="div_dropzone dropzone" id="single_dropzone">
	    <div class="dz-default dz-message">
	     <i class="fas fa-cloud-upload-alt"></i>
	     <p><?php echo translate("Drop files here to upload")?></p>
	     </div>
	  </div>

	 <h3><?php echo translate("Dish")?></h3> 
	 <?php if(is_array($dish) && count($dish)>=1):?>	 
	 <?php $dish_selected  = !empty($data['dish'])?json_decode($data['dish'],true):false;?>
	 
	 <?php foreach ($dish as $dish_val):?>
	 
	   <?php 	   
	   echo ItemHtmlWrapper::checkboxInline('dish[]',$dish_val['dish_name'],
	     in_array($dish_val['dish_id'],(array)$dish_selected)?true:false,
	     array(
		    'value'=>$dish_val['dish_id'],
		    'id'=>"dish_".$dish_val['dish_id']
		  )
	   );
	   ?>
	 
	 <?php endforeach;?>
	 <?php endif;?>		 
	  
	 <div class="height20"></div>         
	
	</div>
 </div>
 
  
 
 
</div> <!--COL-->


</div> <!--end row-->

</DIV>


<div class="floating_action">
       <a href="<?php echo Yii::app()->createUrl(APP_FOLDER.'/item/category_list')?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?>">
       <?php echo translate("CANCEL")?>
       </a>
       
       <?php if(isset($data['cat_id'])):?>
       <a href="javascript:;" class="<?php echo ItemHtmlWrapper::deleteBtnClass()?> delete_record"><?php echo translate("DELETE")?></a>        
       <?php endif;?>
       
       <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo translate("SAVE")?></button>                  
       
</div>

<?php echo CHtml::endForm(); ?>