<div class="floating_inline">
<div class="card">
  <div class="card-body">
  
  <div class="row">
     <div class="col-md-6">
     
      <div class="btn-group" role="group">
       <button type="button" class="btn btn-secondary close_inline"><i class="fa fa-times"></i></button>
       <button type="button" class="btn btn-secondary check_inline" data-action="<?php echo $action?>"><i class="fa fa-check"></i></button>
     </div>
     
     </div> <!--col-->
     <div class="col-md-6">
     
       <?php 
	   echo CHtml::textField('inline_value',$price,array(
	    'class'=>"form-control inline_value numeric_only"
	   ));
	   echo CHtml::hiddenField('inline_id',$item_id,array(
	    'class'=>"form-control inline_id"
	   ));
      ?>
     
     </div> <!--col-->
  </div> <!--row--> 
 
  </div> <!--body-->
</div> <!-- card-->
</div>