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
	
	<div class="row"> 
	  <div class="col-md-9"><h2><?php echo translate("Items")?></h2></div>
	  <div class="col-md-3">
	   <a href="javascript:;" class="btn btn-primary mark_all_receive"><?php echo translate("MARK ALL RECEIVE")?></a>
	  </div>
	</div>
	
	<div class="table_auto_scroll">
	<table class="table table-hover">
	 <thead>
	  <tr>
	   <th width="25%"><?php echo translate("Item")?></th>
	   <th width="10%" class="col-qty"><?php echo translate("Ordered")?></th>
	   <th width="10%" class="col-qty"><?php echo translate("Receive")?></th>
	   <th width="10%" class="col-qty"><?php echo translate("To receive")?></th>
	  </tr>
	 </thead>
	 <tbody>
	 <?php foreach ($data as $key=> $val):?> 
	 <?php 
	  $qty = $val['qty'];
	  $receive_qty = $val['total_receive'];
	  $max = $qty-$receive_qty;	  
	  echo CHtml::hiddenField("po_details_id[$key]",$val['po_details_id']);	  
	 ?>
	  <tr>
	   <td><?php echo InventoryWrapper::prettyItemName($val['item_name'],$val['size_name'],$val['sku'])?></td>
	   <td class="col-qty"><?php echo InventoryWrapper::prettyQuantity($val['qty'])?></td>
	   <td class="col-qty"><?php echo InventoryWrapper::prettyQuantity($receive_qty)?></td>
	   <td class="col-qty">
	     <?php 
	     if($val['total_receive']>=$val['qty']){
	     	echo InventoryWrapper::prettyQuantity($val['total_receive']);
	     } else {
		     echo CHtml::textField("receive_qty[$key]",'',array(
		      'class'=>"form-control numeric_only text-right receive_qty",
		      'required'=>true,	      
		      'max'=>$max,	
		      'data-msg-max'=>translate("Received quantity can't exceed ordered quantity")
		     ));
	     }
	     ?>
	   </td>
	  </tr>
	 <?php endforeach;?> 
	 </tbody>
	</table>
	</div>

	</div> <!--body-->
 </div> <!--cart-->
  
 
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