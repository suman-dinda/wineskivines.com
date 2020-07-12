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
	
	 <div class="form-group">
	   <label><?php echo translate("Email")?></label>
	   <?php 
	   echo CHtml::emailField('email',
	   isset($data['email_address'])?$data['email_address']:'',
	   array(
	     'class'=>"form-control",
	   'required'=>true
	   ));
	   ?>
	 </div>
	 	 
	  <?php echo ItemHtmlWrapper::formTextField2("phone","Phone",
	 isset($data['contact_number'])?$data['contact_number']:'',array(
	   'class'=>"form-control",
	   'required'=>true,
	   'maxlength'=>20
	 ))?>	 
	 
	 <?php echo ItemHtmlWrapper::formTextField2("username","Username",
	 isset($data['username'])?$data['username']:'',array(
	   'class'=>"form-control",
	   'required'=>true,	  
	 ))?>
	 
	   <div class="form-group">
	    <label for=""><?php echo translate("Password")?></label>
	    <?php 	    
	    echo CHtml::passwordField('password','',
	    array(
	      'class'=>"form-control",
	      'autocomplete'=>'new-password'
	    ));
	    ?>
	  </div>
	  
	  <div class="form-group">
	    <label for=""><?php echo translate("Confirm Password")?></label>
	    <?php 	    
	    echo CHtml::passwordField('cpassword','',
	    array(
	      'class'=>"form-control",
	      'autocomplete'=>'new-cpassword'
	    ));
	    ?>
	  </div>
	
	 
	
	</div>
 </div> <!--card-->
  
 
</div> <!--COL-->


</div> <!--end row-->

</DIV>


<div class="floating_action">             
   <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo translate("SAVE PROFILE")?></button>                         
</div>

<?php echo CHtml::endForm(); ?>