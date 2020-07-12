<div class="main_box_wrap">

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
 
    
</div></div> <!--card-->

<div class="pt-3"></div>

<div class="row">
	<div class="col-md-5">

	  <div class="card">
	   <div class="card-body">
	     <div class="row">
	       <div class="col-md-12">
	       
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
			if(is_array($default_status) && count($default_status)>=1){
				foreach ($default_status as $key=>$status) {
				    echo CHtml::hiddenField("status[$key]",$status);
				}
			}
			echo CHtml::hiddenField('chart_type','gross_sale');
			?> 	   
			
	       <table id="table_list" class="table data_tables_stock_alert table-hover  not_editable">
			<thead>
			<tr> 
			 <th width="40%" class="no-sort" colspan="2" ><?php echo translate("Item stock alert")?></th> 
			 <th width="40%" class="no-sort" ><?php echo translate("Merchant")?></th> 
			 <th width="20%" class="no-sort col-qty"><?php echo translate("In stock")?></th>  
			</tr>
			</thead>
			<tbody>  
			</tbody>
			</table>
			
			<?php echo CHtml::endForm(); ?>
	       
	       </div>
	       
	     </div>
	   </div> <!--card body-->
	  </div> <!--card-->
	
	</div> <!--col-->
	
	<div class="col-md-7">
	
	  <div class="card">
	   <div class="card-body">
	     <div class="row">
	       <div class="col-md-5">
	       
	      
	       <table id="table_list" class="table data_tables_sales_monthly table-hover  not_editable">
			<thead>
			<tr> 
			 <th width="60%" class="no-sort"><?php echo translate("Sales last 7 days")?></th> 
			 <th width="40%" class="no-sort col-qty"><?php echo translate("Net sales")?></th>  
			</tr>
			</thead>
			<tbody>  
			</tbody>
			</table>
	       </div>
	       <div class="col-md-7">
	          <div class="chart dashboard_chart" id="main_chart"></div>
	       </div> <!--col-->
	     </div>
	   </div> <!--card body-->
	  </div> <!--card-->
	
	</div> <!--col-->
</div> <!--row-->

</div> <!--main_box_wrap-->