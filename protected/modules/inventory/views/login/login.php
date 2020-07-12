<div class="select_language_wrap login">
<?php
$this->widget(APP_FOLDER.'.components.languageBar');
?>
</div>

<div class="main_login_wraper row">

 <div class="col-md-5 left">
  <div class="absolute">
    <h1><?php echo translate("KARENDERIA")?></h1>
    <h5><?php echo translate("Inventory System")?> <?php echo APP_VERSION;?></h5>
  </div>
 </div>
 
 <div class="col-md-7 right">

 <?php echo CHtml::beginForm('','post',array(
	  'id'=>"frm_ajax",
	  'onsubmit'=>"return false;"	  
	)); 
	?> 	
 
  <div class="absolute login_form">
      <h2 class="pb-3"><?php echo translate("Sigin")?></h2>
	  <div class="form-group">    
	    <label for="username" class="bmd-label-static"><?php echo translate("Username")?></label>
	    <?php 
	    echo CHtml::textField('username',
	    ''
	    ,array(
	      'class'=>'form-control',
	      'autocomplete'=>"off",
	      'required'=>true
	    ));
	    ?>
	  </div> 
	  
	   <div class="form-group">    
	    <label for="password" class="bmd-label-static"><?php echo translate("Password")?></label>
	    <?php 
	    echo CHtml::passwordField('password',
	    ''
	    ,array(
	      'class'=>'form-control',
	      'autocomplete'=>"new-password",
	      'required'=>true
	    ));
	    ?>
	  </div> 
	  
	   <div class="form-group">    
	    <?php echo CHtml::dropDownList('usertype','',	  
		  (array)InventoryWrapper::userType(),
		  array(
		  'class'=>'form-control',		  
		  ))?>
	  </div> 
	  
	  <div class="row">
	    <div class="col-md-6">
	    	    	    
	    <?php 
	    echo CHtml::submitButton( translate("Login"),array(
	     'name'=>"btn_login",
	     'class'=>"myrounded  extended_button btn btn-raised btn-info"
	    ));
	    ?>
	    
	    </div>
	    <div class="col-md-6 text_right">
	      
	    </div>
	  </div>	  
	  

	  
  </div>
 
 <?php echo CHtml::endForm(); ?>
 
 </div>

</div>