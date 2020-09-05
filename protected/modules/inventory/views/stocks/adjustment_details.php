<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-8">

<div class="card card_medium" id="box_wrap">
	<div class="card-body">	 
	 <h1><?php echo $data[0]['transaction_code'].$data[0]['transaction_id']?></h1>
	 
	 <p class="mb-0 pt-4"><b><?php echo translate("Date")?></b>: <?php echo FunctionsV3::prettyDate($data[0]['created_at'])." ".FunctionsV3::prettyTime($data[0]['created_at'])?></p>
	 <p class="mb-0"><b><?php echo translate("Reason")?></b>: <?php echo $data[0]['transaction_type']?></p>
	 <p class="mb-0"><b><?php echo translate("Adjusted by")?></b>: <?php echo $data[0]['added_by']?></p>
	 <p class="mb-0"><b><?php echo translate("Notes")?></b>: <?php echo InventoryWrapper::purify($data[0]['notes'])?></p>
	 
	 <div class="border-top mt-4 mb-4"></div>
	 
	 <h2><?php echo translate("Items")?></h2>
	 <?php 
	 $transaction_type = isset($data[0]['transaction_type'])?$data[0]['transaction_type']:'';	 
	 ?>	
	 <?php if(isset($table_prop[$transaction_type])):?>
	 <div class="table_auto_scroll">
	 <table class="table table-hover">
	 <thead>
	  <tr>	
		  <?php foreach ($table_prop[$transaction_type]['label'] as $key=> $label):?>  
		  <td class="<?php echo $table_prop[$transaction_type]['class'][$key]?>" ><?php echo $label?></td> 
		  <?php endforeach;?>
	  </tr>
	 </thead>
	 
	 <tbody>
	   <?php foreach ($data as $val):?>
	   <tr>
	     <?php foreach ($table_prop[$transaction_type]['fields'] as $key=> $field):?>  
		  <td class="<?php echo $table_prop[$transaction_type]['class'][$key]?>">
		  <?php 		  
		  switch ($field) {
		  	case "item_name":
		  		echo isset($val[$field])?  InventoryWrapper::prettyItemName($val[$field],$val['size_name'],$val['sku']) : '';
		  		break;
		  
		  	case "qty":
		  		echo isset($val[$field])?  InventoryWrapper::prettyQuantity($val[$field]) : '';
		  		break;
		  		
		  	case "cost_price":
		  		echo isset($val[$field])?  FunctionsV3::prettyPrice($val[$field]) : '';
		  		break;	
		  		
		  	default:
		  		echo isset($val[$field])?  $val[$field] : '';
		  		break;
		  }		  
		  ?>
		  </td> 
		  <?php endforeach;?>
	   </tr>
	   <?php endforeach;?>
	 </tbody>
	 
	 </table>
	 </div>
	 <?php endif;?>
	 
	</div> <!--body-->
</div>	 <!--card-->

</div> <!--col-->
</div> <!--row-->

</DIV>


<div class="floating_action">
       <a href="<?php echo $cancel_link?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?> ">
       <?php echo translate("ALL STOCK ADJUSTMENT")?>
       </a>                    
       
</div>
