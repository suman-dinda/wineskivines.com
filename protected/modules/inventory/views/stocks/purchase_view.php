<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_ajax",
  'onsubmit'=>"return false;"	  
)); 
$received = isset($data_receive['received'])? (float) $data_receive['received'] :0;
$total_qty = isset($data_receive['total_qty'])? (float) $data_receive['total_qty']:0;
$percent =  (float) ($received/$total_qty)*100;
?> 	
	
<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-8">

 <div class="card card_medium" id="box_wrap">
	<div class="card-body">
			 	
	<div class="row">
	  <div class="col-md-9">
	    <h2 class="mb-0"><?php echo StocksWrapper::transactionCode('po').$data[0]['po_id'] ?></h2>
	    <p><?php echo translate($data[0]['status'])?></p>
	  </div>
	  <div class="col-md-3 text-right">
	     <div class="progress mt-3">
	       <div class="progress-bar" role="progressbar" style="width:<?php echo $percent;?>%"></div>	       
	     </div>
	     <div class="text-muted text-left pt-1">
	       <?php 
	       echo translate("Receive [receive] of [total]",array(
	         '[receive]'=>$received,
	         '[total]'=>$total_qty,
	       ));
	       ?>
	     </div>
	  </div>
	</div><!-- row-->
	
	<p class="mb-0 pt-4"><b><?php echo translate("Date")?></b>: <?php echo FunctionsV3::prettyDate($data[0]['purchase_date'])?></p>
	<p class="mb-0"><b><?php echo translate("Ordered by")?></b>: <?php echo $data[0]['added_by']?></p>
	
	<p class="mb-0 pt-4"><b><?php echo translate("Supplier")?></b></p>
	<p class="mb-0"><?php echo $data[0]['supplier_name']?></p>
	
	<?php if(!empty($data[0]['notes'])):?>
	<p class="mb-0 pt-4"><b><?php echo translate("Notes")?></b></p>
	<p class="mb-0"><?php echo $data[0]['notes']?></p>
	<?php endif;?>
	
	<div class="border-top mt-4 mb-4"></div>
	
	<h2><?php echo translate("Items")?></h2>
	<?php 	
	$total=0;
	?>
	
	<div class="table_auto_scroll">
	<table class="table table-hover">
	 <thead>
	  <tr>
	   <th width="25%"><?php echo translate("Item")?></th>
	   <th width="10%" class="col-qty"><?php echo translate("Quantity")?></th>
	   <th width="10%" class="col-qty"><?php echo translate("Purchase cost")?></th>
	   <th width="10%" class="col-qty"><?php echo translate("Amount")?></th>
	  </tr>
	 </thead>
	 <tbody>
	  <?php foreach ($data as $val):?> 
	  <?php 
	       $amount = (float)$val['qty']*(float)$val['cost_price'];
	       $total+=$amount;
	  ?>
	   <tr>
	   <td><?php echo InventoryWrapper::prettyItemName($val['item_name'],$val['size_name'],$val['sku'])?></td>
	   <td class="col-qty"><?php echo InventoryWrapper::prettyQuantity($val['qty'])?></td>
	   <td class="col-qty"><?php echo FunctionsV3::prettyPrice($val['cost_price'])?></td>
	   <td class="col-qty"><?php echo FunctionsV3::prettyPrice($amount)?></td>
	   </tr>
	  <?php endforeach;?>
	 </tbody>
	 <tfoot>
	  <tr>
	   <td colspan="2"></td>
	   <td class="col-qty"><b><?php echo translate("Total")?></b></td>
	   <td class="col-qty"><b><?php echo FunctionsV3::prettyPrice($total)?></b></td>
	  </tr>
	 </tfoot>
	</table>
	</div>
	
	</div> <!--body-->
 </div> <!--cart-->
  
 
</div> <!--COL-->


</div> <!--end row-->

</DIV>


<div class="floating_action">

       <a href="<?php echo $cancel_link?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?> ">
       <?php echo translate("ALL PURCHASE ORDER")?>
       </a>
              
       <?php if($is_editable):?>
       <a href="javascript:;" class="<?php echo ItemHtmlWrapper::deleteBtnClass()?> delete_record"><?php echo translate("CANCEL THIS ORDER")?></a>              
       
       <a href="<?php echo $edit_link?>" class="<?php echo ItemHtmlWrapper::newBtnClass()?>"><?php echo translate("EDIT")?></a>              
              
       <a href="<?php echo $receive_link?>" class="<?php echo ItemHtmlWrapper::saveBtnClass()?> pr-3"><?php echo translate("RECEIVE")?></a>        
       <?php endif;?>
       
</div>

<?php echo CHtml::endForm(); ?>