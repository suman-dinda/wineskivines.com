<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_ajax",
  'onsubmit'=>"return false;"	  
)); 
?> 	
	
<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-5">

 <div class="card card_medium" id="box_wrap">
	<div class="card-body">
			 
	 <?php 
	 $fields[] = array('supplier_name','Supplier name', isset($data['supplier_name'])?$data['supplier_name']:'', true );
	 $fields[] = array('contact_name','Contact', isset($data['contact_name'])?$data['contact_name']:'' );
	 $fields[] = array('email','Email', isset($data['email'])?$data['email']:'' );
	 $fields[] = array('phone_number','Phone number', isset($data['phone_number'])?$data['phone_number']:'' );
	 $fields[] = array('address_1','Address 1', isset($data['address_1'])?$data['address_1']:'' );
	 $fields[] = array('address_2','Address 2', isset($data['address_2'])?$data['address_2']:'' );
	 $fields[] = array('city','City', isset($data['city'])?$data['city']:'' );
	 $fields[] = array('postal_code','Postal/zip code', isset($data['postal_code'])?$data['postal_code']:'' );
	 
	 echo ItemHtmlWrapper::generateFormField($fields);
	 
	 ?>
	 <div class="form-group">
	 <label for=""><?php echo translate("Country")?></label>
	 <?php
	 echo CHtml::dropDownList('country_code',
	 isset($data['country_code'])?$data['country_code']:''
	 ,Yii::app()->functions->CountryList(),array(
	  'class'=>'form-control'
	 ));
	 ?>
	 </div>
	 <?php
	 
	 unset($fields);
	 $fields[] = array('region','Region', isset($data['region'])?$data['region']:'' );
	 $fields[] = array('notes','Notes', isset($data['notes'])?$data['notes']:'' );
	 echo ItemHtmlWrapper::generateFormField($fields);
	 ?>
	
	</div>
 </div>
  
 
</div> <!--COL-->


</div> <!--end row-->

</DIV>


<div class="floating_action">
       <a href="<?php echo $cancel_link?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?>">
       <?php echo translate("CANCEL")?>
       </a>
       
       <?php if(isset($data['supplier_id'])):?>
       <a href="javascript:;" class="<?php echo ItemHtmlWrapper::deleteBtnClass()?> delete_record"><?php echo translate("DELETE")?></a>        
       <?php endif;?>
       
       <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo translate("SAVE")?></button>                  
       
</div>

<?php echo CHtml::endForm(); ?>