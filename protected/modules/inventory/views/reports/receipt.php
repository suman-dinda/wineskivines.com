<div class="row pt-2">
  <div class="col-md-6">
     <h2>#<?php echo $data['data']['order_id']?></h2>
     <h6><?php echo translate("Order id")?></h6>
  </div> <!--col-->
  
  <div class="col-md-6">
    <h2><?php echo FunctionsV3::prettyPrice($data['data']['total_w_tax'])?></h2>
     <h6><?php echo translate("Total")?></h6>
  </div> <!--col-->
</div> <!--row-->
<hr/>

<?php if(is_array($data['print']) && count($data['print'])>=1):?>
<table class="table table-borderless">
<?php foreach ($data['print'] as $val):?>
<tr>
 <td><?php echo t($val['label'])?> : <?php echo $val['value']?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>

<hr/>

<?php if(is_array($data['raw']['item']) && count($data['raw']['item'])>=1):?>
<table class="table table-borderless">
<?php foreach ($data['raw']['item'] as $item):
   $price = $item['normal_price']; $qty = $item['qty']; $addon_total=0;
   if($item['discount']>0){
   	  $price = $item['discounted_price']; 
   }
   $item_total = (integer)$qty* (float) $price;   
?>
<tr>
 <td>
   <b><?php echo $item['category_name']?></b><br/>
   <?php 
   if(!empty($item['size_words'])){
   	   echo translate("[item_name] ([size_words])",array(
   	     '[item_name]'=>$item['item_name'],
   	     '[size_words]'=>$item['size_words'],
   	   ));
   } else echo $item['item_name'];
   ?>
   <p class="text-muted"><?php echo $qty?> x <?php echo FunctionsV3::prettyPrice($price)?>
   
   <?php if(!empty($item['cooking_ref'])):?>
   <p class="text-secondary no_margin"><?php echo $item['cooking_ref']?></p>
   <?php endif;?>
   <?php if(!empty($item['order_notes'])):?>
   <p class="text-info no_margin"><?php echo $item['order_notes']?></p>
   <?php endif;?>
   
   <?php if(is_array($item['ingredients']) && count( (array) $item['ingredients'])>=1 ):?>
   <p class="text-success no_margin pt-1"><?php echo translate("Ingredients")?>:</p>
   <?php foreach ($item['ingredients'] as $ingredients):?>
     <p class="text-secondary no_margin"><?php echo $ingredients?></p>
   <?php endforeach;?>
   <?php endif;?>
   
   
   <?php if(isset($item['sub_item'])):?>
   
   <?php if(is_array($item['sub_item']) && count($item['sub_item'])>=1):?>
   <?php foreach ($item['sub_item'] as $sub_item):?>
     <br>+<?php echo $sub_item['addon_name']?> (<?php echo $sub_item['addon_qty']?>x<?php echo FunctionsV3::prettyPrice($sub_item['addon_price'])?>)
     <?php $addon_total+= $sub_item['addon_qty']*$sub_item['addon_price'];?>
   <?php endforeach;?>
   <?php endif;?>
   </p>
   <?php endif;?>
   
 </td>
 <td class="col-qty"><?php echo FunctionsV3::prettyPrice($item_total+$addon_total)?></td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>

<hr/>

<?php $total = $data['raw']['total']; ?>
<table class="table table-borderless">

<?php if($total['less_voucher']>0):?>
<tr>
 <td><?php echo translate("Less Voucher")?></td>
 <td class="col-qty">(<?php echo FunctionsV3::prettyPrice($total['less_voucher'])?>)</td>
</tr>
<?php endif;?>

<?php if($total['pts_redeem_amt']>0):?>
<tr>
 <td><?php echo translate("Points Discount")?></td>
 <td class="col-qty">(<?php echo FunctionsV3::prettyPrice($total['pts_redeem_amt'])?>)</td>
</tr>
<?php endif;?>

<?php if($total['subtotal']>0):?>
<tr>
 <td><b><?php echo translate("Sub Total")?></b></td>
 <td class="col-qty"><b><?php echo FunctionsV3::prettyPrice($total['subtotal'])?></b></td>
</tr>
<?php endif;?>

<?php if($total['delivery_charges']>0):?>
<tr>
 <td><?php echo translate("Delivery Fee")?></td>
 <td class="col-qty"><?php echo FunctionsV3::prettyPrice($total['delivery_charges'])?></td>
</tr>
<?php endif;?>

<?php if($total['merchant_packaging_charge']>0):?>
<tr>
 <td><?php echo translate("Packaging")?></td>
 <td class="col-qty"><?php echo FunctionsV3::prettyPrice($total['merchant_packaging_charge'])?></td>
</tr>
<?php endif;?>

<?php if($total['taxable_total']>0):?>
<tr>
 <td><?php echo translate("Tax [tax]%",array('[tax]'=> normalPrettyPrice($total['tax']*100) ))?></td>
 <td class="col-qty"><?php echo FunctionsV3::prettyPrice($total['taxable_total'])?></td>
</tr>
<?php endif;?>

<?php if($total['taxable_total']>0):?>
<tr>
 <td><?php echo translate("Tips [tips]",array('[tips]'=> $total['tips_percent'] ))?></td>
 <td class="col-qty"><?php echo FunctionsV3::prettyPrice($total['tips'])?></td>
</tr>
<?php endif;?>

<?php if($total['total']>0):?>
<tr>
 <td><b><?php echo translate("Total")?></b></td>
 <td class="col-qty"><b><?php echo FunctionsV3::prettyPrice($total['total'])?></b></td>
</tr>
<?php endif;?>

</table>