<DIV class="main_box_wrap">

<div class="card"><div class="card-body">
  <div class="row">
     <div class="col-sm-4 col-md-4">
       <p><?php echo translate("Total inventory value")?></p>
       <h1><?php echo isset($data['inventory_value'])? $data['inventory_value']>0?FunctionsV3::prettyPrice($data['inventory_value']):0 : 0?></h1>       
     </div>
     <div class="col-sm-4 col-md-4">
       <p><?php echo translate("Total retail value")?></p>
       <h1><?php echo isset($data['retail_value'])? $data['retail_value']>0? FunctionsV3::prettyPrice($data['retail_value']) : 0 : 0?></h1>       
     </div> 
     <div class="col-sm-4 col-md-4">
      <p><?php echo translate("Potential profit")?></p>
       <h1><?php echo isset($data['potential_profit'])? $data['potential_profit']>0 ? FunctionsV3::prettyPrice($data['potential_profit']) : 0 : 0?></h1>       
     </div>
  </div> <!--row-->
</div></div> <!--card-->

</DIV>

<div class="pt-3"></div>

<div class="row">
<div class="col-md-12">


<div class="card card_medium" id="box_wrap">
	<div class="card-body">

<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_table",
  'onsubmit'=>"return false;"
)); 
?> 	    

<table id="table_list" class="table data_tables table-hover table_evaluation not_editable">
<thead>
<tr> 
 <th width="15%"><?php echo translate("Item")?></th> 
 <th width="10%" class="col-qty "><?php echo translate("In stocks")?></th> 
 <th width="10%" class="col-qty "><?php echo translate("Cost")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Inventory value")?></th> 
 <th width="10%" class="col-qty "><?php echo translate("Price")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Retail value")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Potential profit")?></th> 
</tr>
</thead>
<tbody>  
</tbody>
</table>
    
<?php echo CHtml::endForm(); ?>
	
	</div> <!--body-->
</div> <!--card-->

 
</div> <!--COL-->
</div> <!--end row-->
</DIV>


