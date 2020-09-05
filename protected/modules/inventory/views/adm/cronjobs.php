<div class="row">
  <div class="col-md-5 col-sm-5">
  
  <div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<?php 
	 $this->renderPartial(APP_FOLDER.'.views.adm.menu');
	?>
	
	</div> <!--card body-->
  </div> <!--card-->
  
  </div> <!--col-->
  <div class="col-md-7 col-sm-7">
  
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
	
	<?php if(is_array($crons) && count($crons)>=1):?>
	<ul class="list-group">
	 
	  <?php foreach ($crons as $val):?>
	  <a class="list-group-item"  href="<?php echo $val['link']?>" target="_blank" >
	    <div class="bmd-list-group-col">
	      <p class="list-group-item-heading"><?php echo $val['link']?></p>
	      <p class="list-group-item-text"><?php echo $val['notes']?></p>
	    </div>
	  </a>	  
	  <?php endforeach;?>
	  
	</ul>
	<?php endif;?>
	

	<?php echo CHtml::endForm(); ?>
	
	</div> <!--card body-->
  </div> <!--card-->
  
  </div> <!--col-->
</div> <!--row-->