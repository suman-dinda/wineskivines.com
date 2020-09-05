<div class="row">
  <div class="col-md-5">
  
  <div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<?php 
	 $this->renderPartial(APP_FOLDER.'.views.settings.menu');
	?>
	
	</div> <!--card body-->
  </div> <!--card-->
  
  </div> <!--col-->
  <div class="col-md-7">
  
  <div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<h2><?php echo translate("Reports")?></h2>
	
	<?php
	 echo CHtml::beginForm('','post',array(
	  'id'=>"frm_ajax",
	  'onsubmit'=>"return false;",
	  'class'=>"pt-3"
	)); 		
	?> 		
		
	<div class="form-group pt-2">
	  <label><?php echo translate("Reports Default status")?></label>
	  <div class="select2_css">
	  <?php 
	  echo CHtml::dropDownList('inventory_reports_default_status[]',
	  (array)$stats1,$order_status,array(
	    'class'=>"select2",
	    'multiple'=>'multiple',
	    'style'=>"width: 75%"
	  ));
	  ?>
	  </div>
	  <p class="text-muted">This is the default order status will be use in all reportings</p>
	</div>
	
	<div class="form-group pt-2">
	  <label><?php echo translate("Accepted order status")?></label>
	  
	   <div class="select2_css">
	  <?php 
	  echo CHtml::dropDownList('inventory_accepted_order_status[]',(array)$stats2,$order_status,array(
	    'class'=>"select2",
	    'multiple'=>'multiple',
	    'style'=>"width: 75%"
	  ));
	  ?>
	  </div>
	  <p class="text-muted">This is the status consider as new sale that will deduct item stocks</p>
	</div>
	
	<div class="form-group pt-2">
	  <label><?php echo translate("Cancel order status")?></label>
	  
	    <div class="select2_css">
	  <?php 
	  echo CHtml::dropDownList('inventory_cancel_order_status[]',(array)$stats3,$order_status,array(
	    'class'=>"select2",
	    'multiple'=>'multiple',
	    'style'=>"width: 75%"
	  ));
	  ?>
	  </div>
	  <p class="text-muted">This is the status when the order is cancel,denied or failed, the qty of items will revert back as stock</p>
	</div>

<div class="floating_action">
   <a href="<?php echo Yii::app()->createUrl(APP_FOLDER.'/settings/general')?>" class="btn btn-secondary ">
   <?php echo translate("CANCEL")?>
   </a>             
   <button type="submit" class="btn btn-info"><?php echo translate("SAVE")?></button>       
</div>	
	
	<?php echo CHtml::endForm(); ?>
	
	</div> <!--card body-->
  </div> <!--card-->
  
  </div> <!--col-->
</div> <!--row-->