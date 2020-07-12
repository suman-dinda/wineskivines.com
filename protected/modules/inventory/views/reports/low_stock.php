<div style="width:100%;padding:30px 0px 50px 0px;background-color:#f2f2f2;">
  <div style="width:620px;max-width:90%;margin:0 auto">
  
  <table style="width:100%;margin:0 auto;background:#fff;border-radius:6px;">
  <thead>
   <tr>
    <td>
    
    <p style="text-align:center;padding-bottom:20px;padding-top:20px">
        <img src="<?php echo Yii::app()->getBaseUrl(true)."/protected/modules/".APP_FOLDER."/assets/images/lowstock.png"?>" width="110px" alt="" >
    </p>
    
    </td>
   </tr>
  </thead>
  
  <tbody>
  <tr>
   <td style="padding:0;">
	
	<p style="font-family:Arial,Helvetica,sans-serif;font-size:26px;color:#272727;line-height:22px;font-weight:400;margin-bottom:0px;margin-top:0px;padding:0px 20px 0px 20px;text-align:center">
	<span class="il">Low</span> <span class="il">stock</span> notification
	</p>
	
	<p style="font-family:HelveticaNeue Medium,sans-serif;font-size:20px;color:#272727;line-height:20pt;font-weight:500;margin-bottom:0px;margin-top:5px;padding:1px 20px 0px 20px;text-align:center">
	<?php echo $merchant?>
	</p>
	
	<p style="font-family:HelveticaNeue Medium,sans-serif;font-size:20px;text-transform:capitalize;color:#272727;font-weight:500;margin-bottom:0px;margin-top:5px;padding:0px 20px 15px 20px;text-align:center">
	<?php echo $todays_date;?></p>

   </td>
  </tr>
  
  <tr>
	<td style="padding:0px 20px">
	<table style="margin:auto;margin-top:15px;margin-bottom:15px" border="0" cellpadding="0" cellspacing="0" width="90%">	
	   <tbody>
	    <?php if(is_array($data) && count($data)>=1):?>
	    <?php foreach ($data as $val):?>
	     <tr>
	            <td style="font-family:Arial,Helvetica,sans-serif;border-top:1px dotted #fff;border-bottom:1px dotted #fff;padding:2px 0px;font-size:14px;color:#333;text-align:left;">	                
	            <?php echo InventoryWrapper::prettyItemName($val['item_name'],$val['size_name'],$val['sku'])?>
	            </td>	
	            <td style="font-family:Arial,Helvetica,sans-serif;border-top:1px dotted #fff;border-bottom:1px dotted #fff;padding:2px 0px;font-size:16px;color:#f44336;text-align:right;width:90px;height:30px">
	             <?php echo InventoryWrapper::prettyQuantity($val['available_stocks'])?>
	             </td>
	        </tr>		
	    <?php endforeach;?>    
	    <?php endif;?>    
	   </tbody>
	 </table>
	</td>
  </tr>
  
  <tr>
	<td style="padding:0px 0px 30px 0px">
	<p style="text-align:center;font-size:14px;color:#999;font-family:Helvetica Neue,Helvetica,sans-serif;padding-top:15px">
	&copy; 2020 <?php echo $sitename?>. All rights reserved.<br>
	</p>
	
	</td>
  </tr>
  
  </tbody>
  
  </table>
  
  </div>
</div>