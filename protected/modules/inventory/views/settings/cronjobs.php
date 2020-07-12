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
	
	<h2><?php echo translate("Cron jobs")?></h2>
	
	<?php
	 echo CHtml::beginForm('','post',array(
	  'id'=>"frm_ajax",
	  'onsubmit'=>"return false;",
	  'class'=>"pt-3"
	)); 
	?> 		
	

	
	<?php echo CHtml::endForm(); ?>
	
	</div> <!--card body-->
  </div> <!--card-->
  
  </div> <!--col-->
</div> <!--row-->