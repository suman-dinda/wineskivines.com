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
			 	 	 
	 <?php echo ItemHtmlWrapper::formTextField2("email","Email",
	 isset($data['email_address'])?$data['email_address']:'',array(
	   'class'=>"form-control",
	   'required'=>true
	 ))?>
	 	 
	  <?php echo ItemHtmlWrapper::formTextField2("phone","Phone",
	 isset($data['contact_number'])?$data['contact_number']:'',array(
	   'class'=>"form-control",
	   'required'=>true
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

	 <?php 
	 $disabled_role = false;
	 $user_type = isset($data['user_type'])?$data['user_type']:'';
	 if($user_type=="merchant"){
	 	$disabled_role=true;
	 }
	 ?>
	 
	 <div class="form-group">
	    <label for=""><?php echo translate("Role")?></label>
	    <?php 	    
	    echo CHtml::dropDownList('role_id',
	    isset($data['role_id'])?$data['role_id']:''
	    ,(array)$role,array(
	      'class'=>"form-control",
	      'required'=>true,
	      'disabled'=>$disabled_role
	    ));
	    ?>
	  </div>
	
	</div>
 </div> <!--card-->
  
 
</div> <!--COL-->


</div> <!--end row-->

</DIV>


<div class="floating_action">
       <a href="<?php echo Yii::app()->createUrl(APP_FOLDER.'/user/userlist')?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?>">
       <?php echo translate("CANCEL")?>
       </a>
       
       <?php if(isset($data['role_id'])):?>
       <a href="javascript:;" class="<?php echo ItemHtmlWrapper::deleteBtnClass()?> delete_record"><?php echo translate("DELETE")?></a>        
       <?php endif;?>
       
       <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo translate("SAVE")?></button>                  
       
</div>

<?php echo CHtml::endForm(); ?>