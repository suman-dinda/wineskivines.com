<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_ajax",
  'onsubmit'=>"return false;"	  
)); 
?> 	
	
<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-8">

 <div class="card card_medium" id="box_wrap">
	<div class="card-body">

	<?php 
	$first_row = isset($data[0])?$data[0]:'';	
	echo CHtml::hiddenField('transaction_type','purchase_order',array(
	  'class'=>"adjustment_transaction_type"
	));
	?>
	
	<div class="form-group" style="width:50%;">
	     <label for=""><?php echo translate("Supplier")?></label>
	     <?php 
	     echo CHtml::dropDownList('supplier_id',
	     isset($first_row['supplier_id'])?$first_row['supplier_id']:''
	     ,$supplier_list,array(
	      'class'=>"form-control supplier_id",
	      'required'=>true
	     ));
	     ?>
	  </div>
	  
	  <div class="row pb-3">
	    <div class="col-md-6">
	      <?php 
	      $date_now = isset($first_row['purchase_date'])?$first_row['purchase_date']:date("M d, Y");
	      echo CHtml::hiddenField('purchase_date',
	      isset($first_row['purchase_date'])?$first_row['purchase_date']:date("Y-m-d")
	      ,array('class'=>"datepicker"));	      
	      echo ItemHtmlWrapper::formTextField2('purchase_date1','Purchase order date',$date_now,array(
	        'class'=>"form-control date_picker",
	        'required'=>true,
	        'readonly'=>true
	      ));
	      ?>
	    </div>
	    <div class="col-md-6">
	     <?php 	     
	      $expected_on  = isset($first_row['expected_on'])? date("M d, Y",strtotime($first_row['expected_on'])) :'';
	      echo CHtml::hiddenField('expected_on',
	      isset($first_row['expected_on'])?$first_row['expected_on']:''
	      ,array('class'=>"datepicker"));	      
	      
	      echo ItemHtmlWrapper::formTextField2('expected_on1','Expected on',$expected_on,array(
	        'class'=>"form-control date_picker",
	        'required'=>false,
	        'readonly'=>true
	      ));
	      
	      ?>
	    </div>
	  </div> <!--row-->
	  
	  <div class="form-group">
	     <label for=""><?php echo translate("Notes")?></label>
	     <?php 
	     echo CHtml::textField('notes',
	     isset($first_row['notes'])?$first_row['notes']:''
	     ,array(
	       'class'=>"form-control"
	     ));
	     ?>
	  </div>
	
	
	</div> <!--body-->
 </div> <!--card--> 
  
 
 <div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	 <div class="text-right">
	 
	 <div class="btn-group">
	  <button class="btn dropdown-toggle autofill" type="button" id="btnautofill" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <?php echo translate("Autofill")?>
	  </button>
	  <div class="dropdown-menu" aria-labelledby="btnautofill">
	    <a class="dropdown-item autofill_item disabled" href="javascript:;" data-id="all" >
	    <?php echo translate("All items from the supplier")?>
	    </a>
	    <a class="dropdown-item autofill_item disabled" href="javascript:;" data-id="lowstock">
	     <?php echo translate("Low stock items from the supplier")?>
	    </a>	    
	  </div>
	</div>
	 
	 </div> <!--text-right-->
	
	 <label><?php echo translate("Items")?></label>
	 
	 <div class="table_auto_scroll">
	     <table class="table_adjustment_new table table-hover">
        <thead>
         <tr>
          <th width="25%"><?php echo translate("Item")?></th>
          <th width="10%"><?php echo translate("In Stock")?></th>
          <th width="10%"><?php echo translate("Incoming")?></th>
          <th width="10%"><?php echo translate("Quantity")?></th>
          <th width="15%"><?php echo translate("Purchase cost")?></th>
          <th width="10%"><?php echo translate("Amount")?></th>
          <th width="5%"></th>
         </tr>
        </thead>
        <tbody>
          <tr></tr>
        </tbody>
      </table>
      </div>
	 
      
       <div class="typeahead__container pt-3">
        <div class="typeahead__field">
            <div class="typeahead__query">                
                <?php 
                echo CHtml::textField('search_item','',array(
                  'class'=>"form-control typhead_item",
	              'placeholder'=>translate("Search item"),
	              'autocomplete'=>'off'
                ));
                ?>
            </div>            
        </div>
    </div>
      
    </div> <!--body-->
 </div> <!--card--> 	
 
</div> <!--COL-->


</div> <!--end row-->

</DIV>


<div class="floating_action">
       <a href="<?php echo $cancel_link?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?>">
       <?php echo translate("CANCEL")?>
       </a>
                            
       <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo $save_label?></button>                  
       
</div>

<?php echo CHtml::endForm(); ?>