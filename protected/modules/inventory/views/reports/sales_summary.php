<DIV class="main_box_wrap">

<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_table_filter",		  
  'onsubmit'=>"return false;"	  
)); 
echo CHtml::hiddenField('range1',$start_date,array(
 'class'=>"range1"
));
echo CHtml::hiddenField('range2',$end_end,array(
 'class'=>"range2"
));
echo CHtml::hiddenField('chart_type','gross_sale');
$x = 0;
?> 	   

<div class="row top_filter">

<div class="col-lg-3 col-md-3 col-sm-3" >
<div class="card"><div class="card-body">
  <?php echo CHtml::textField('date_range','',array(
    'class'=>"form-control date_range no_border",
    'readonly'=>true
  ))?>
</div></div> <!--card-->
</div> <!--col-->



<div class="col-lg-2 col-md-3 col-sm-3"  >
<div class="card"><div class="card-body">
<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" 
     aria-haspopup="true" aria-expanded="true">
     <i class="glyphicon glyphicon-cog"></i>
     <span class="filter_label" data-field="status">
     <?php 
       if(is_array($default_status) && count($default_status)>=1){
       	 echo translate("[count] status",array(
       	   '[count]'=>count($default_status)
       	 ));
       } else echo translate("All status");       
     ?>
     </span>
     <span class="caret"></span>
  </button>
  
  <?php $x = 0;?>
  <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">  
    <?php  foreach ($order_status as $order_status_key=>$order_status_val):?>
    <li>
      <label>       
        <?php 
        
        $class_name = "filter_data";
        if($order_status_key=="all"){
        	$class_name = "filter_all";
        }               
        echo CHtml::checkBox("status[$x]",
        in_array($order_status_key, (array) $default_status)?true:false
        ,array(
         'value'=>$order_status_key,
         'class'=>$class_name
        ));
        $x++;
        ?>
        
        <?php echo $order_status_val;?>
        
      </label>      
      <?php if($order_status_key=="all"):?>
      <hr></hr>
      <?php endif;?>
    </li>        
    <?php endforeach;?>
  </ul>
</div> <!--dropdown-->
</div></div> <!--card-->
</div> <!--col-->

<div class="col-lg-2 col-md-3 col-sm-3" >
 <div class="card"><div class="card-body">
 <button type="submit" class="btn btn-info">
   <?php echo translate("Apply Filter")?>
 </button>
 </div></div> <!--card-->
</div>

</div><!-- top_filter-->

<div class="pt-3"></div>

<div class="card"><div class="card-body">

  <ul class="nav nav-tabs summary_nav">
	  <li class="nav-item">
	    <a class="nav-link active" href="javascript:;" data-id="gross_sale">
	      <p><?php echo translate("Gross sales")?></p>
	      <h1 class="gross_sale_value">0</h1>       
	    </a>
	  </li>
	  <li class="nav-item">
	    <a class="nav-link" href="javascript:;" data-id="discount" >
	     <p><?php echo translate("Discounts")?></p>
         <h1 class="discount_value">0</h1>          
	    </a>
	  </li>
	  <li class="nav-item">
	    <a class="nav-link" href="javascript:;" data-id="net_sales" >
	     <p><?php echo translate("Net sales")?></p>
         <h1 class="net_sales_value">0</h1>               
	    </a>
	  </li>
	  <li class="nav-item">
	    <a class="nav-link" href="javascript:;" data-id="gross_profit">
	    <p><?php echo translate("Gross profit")?></p>
        <h1 class="gross_profit_value">0</h1>      
	    </a>
	  </li>
 </ul>
 
  <div class="dropdown drop_summary_nav">
  <button class="btn btn-raised dropdown-toggle" 
   type="button" id="drop_summary_nav" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span class="summary_nav_selected"><?php echo translate("Sales")?></span>
  </button>
  <div class="dropdown-menu" aria-labelledby="drop_summary_nav">
    <a class="dropdown-item" href="javascript:;" data-id="gross_sale" >
       <div class="row">
        <div class="col-md-6"><?php echo translate("Gross sales")?></div>
        <div class="col-md-6"><b><span class="gross_sale_value">0</span></b></div>
       </div>
    </a>    
    <a class="dropdown-item" href="javascript:;" data-id="discount"  >
       <div class="row">
        <div class="col-md-6"><?php echo translate("Discounts")?></div>
        <div class="col-md-6"><b><span class="discount_value">0</span></b></div>
       </div>
    </a>    
    <a class="dropdown-item" href="javascript:;" data-id="net_sales"  >
       <div class="row">
        <div class="col-md-6"><?php echo translate("Net sales")?></div>
        <div class="col-md-6"><b><span class="net_sales_value">0</span></b></div>
       </div>
    </a>    
    <a class="dropdown-item" href="javascript:;" data-id="gross_profit">
       <div class="row">
        <div class="col-md-6"><?php echo translate("Gross profit")?></div>
        <div class="col-md-6"><b><span class="gross_profit_value">0</span></b></div>
       </div>
    </a>    
  </div>
 </div>
 <!--dropdown-->
 
  
  <div class="chart" id="main_chart"></div>
</div></div> <!--card-->

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

<table id="table_list" class="table data_tables table-hover table_evaluation not_editable table-responsive">
<thead>
<tr> 
 <th width="5%" class="no-sort"><?php echo translate("Date")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Gross sales")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Discounts")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Net sales")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Cost of goods")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Gross profit")?></th>  
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


