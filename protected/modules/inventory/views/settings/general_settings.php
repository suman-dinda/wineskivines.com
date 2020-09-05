<div class="row">
  <div class="col-md-5 col-sm-5">
  
  <div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<?php 
	 $this->renderPartial(APP_FOLDER.'.views.settings.menu');
	?>
	
	</div> <!--card body-->
  </div> <!--card-->
  
  </div> <!--col-->
  <div class="col-md-7 col-sm-7">
  
  <div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<h2><?php echo translate("General settings")?></h2>
	
	<?php
	 echo CHtml::beginForm('','post',array(
	  'id'=>"frm_ajax",
	  'onsubmit'=>"return false;",
	  'class'=>"pt-3"
	)); 
	?> 	
	
	<div class="form-group">
	<label for=""><?php echo translate("Email")?></label>
	<?php 	
	echo CHtml::emailField('inventory_email_notify',
	getOption($merchant_id,'inventory_email_notify')
	,array(
	  'class'=>"form-control",
	  'required'=>true
	));	
	?>
	</div>
	
	<p class="text-muted"><?php echo translate("Email that will receive notification about low stock and out of stock")?></p>
	
	<ul class="list-group">
	 	
      
	  <?php if($hide_out_stock!=1):?>
	  <a class="list-group-item">
       <i class="fa fa-eye-slash"></i>
	    <div class="bmd-list-group-col">
	      <p class="list-group-item-heading"><?php echo translate("Hide items if out of stock")?></p>
	      <p class="list-group-item-text"><?php echo translate("Hide item if out of stock or negative stock")?></p>
	    </div>
       <div class="pull-xs-right">
       
	    <?php 
	    echo ItemHtmlWrapper::formSwitch('inventory_hide_out_stock','',
	    getOption($merchant_id,'inventory_hide_out_stock')==1?true:false
	    ,array(
	      'value'=>1
	    ));
	    ?>      
       </div>
      </a>
      <?php endif;?>
      	   
      
      <?php if($allow_negative_order!=1):?>
     <a class="list-group-item">
       <i class="fa fa-pallet"></i>
	    <div class="bmd-list-group-col">
	      <p class="list-group-item-heading"><?php echo translate("Allow negative stock order")?></p>
	      <p class="list-group-item-text"><?php echo translate("Allow customer to order even stock is negative")?></p>
	    </div>
       <div class="pull-xs-right">
       
        <?php 
	    echo ItemHtmlWrapper::formSwitch('inventory_allow_negative_order','',
	    getOption($merchant_id,'inventory_allow_negative_order')==1?true:false
	    ,array(
	      'value'=>1
	    ));
	    ?>
       
       </div>
      </a>
      <?php endif;?>
            
      <a class="list-group-item">
       <i class="fa fa-envelope"></i>
	    <div class="bmd-list-group-col">
	      <p class="list-group-item-heading"><?php echo translate("Low stock notifications")?></p>
	      <p class="list-group-item-text"><?php echo translate("Get daily email on items that are low or out of stock")?></p>
	    </div>
       <div class="pull-xs-right">
       
	    <?php 
	    echo ItemHtmlWrapper::formSwitch('inventory_low_stock_notify','',
	    getOption($merchant_id,'inventory_low_stock_notify')==1?true:false
	    ,array(
	      'value'=>1
	    ));
	    ?>      
       </div>
      </a>      
	
	</ul>
	
	

<div class="floating_action">
   
   <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo translate("SAVE")?></button>       
</div>	
	
	<?php echo CHtml::endForm(); ?>
	
	</div> <!--card body-->
  </div> <!--card-->
  
  </div> <!--col-->
</div> <!--row-->