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
			 
	  <div class="form-group" style="width:50%;">
	     <label for=""><?php echo translate("Reason")?></label>
	     <?php 
	     echo CHtml::dropDownList('transaction_type','',$adjustment_type,array(
	      'class'=>"form-control adjustment_transaction_type"
	     ));
	     ?>
	  </div>
	  
	  <div class="form-group">
	     <label for=""><?php echo translate("Notes")?></label>
	     <?php 
	     echo CHtml::textField('notes','',array(
	       'class'=>"form-control"
	     ));
	     ?>
	  </div>
	
	</div>
 </div> <!--card-->
 
 <div class="card card_medium" id="box_wrap">
	<div class="card-body">
      <label><?php echo translate("Items")?></label>
      
      <div class="table_auto_scroll">
      <table class="table_adjustment_new table table-hover">
        <thead>
         <tr>
          <th width="30%"><?php echo translate("Item")?></th>
          <th width="10%"><?php echo translate("In Stock")?></th>
          <th width="12%"><?php echo translate("Add Stock")?></th>
          <th width="12%"><?php echo translate("Cost")?></th>
          <th width="12%"><?php echo translate("Stock After")?></th>
          <th width="10%"></th>
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
      
    </div>
 </div> <!--card-->	
 
</div> <!--COL-->


</div> <!--end row-->

</DIV>


<div class="floating_action">
       <a href="<?php echo $cancel_link?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?>">
       <?php echo translate("CANCEL")?>
       </a>
              
       <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo translate("SAVE")?></button>                  
       
</div>

<?php echo CHtml::endForm(); ?>