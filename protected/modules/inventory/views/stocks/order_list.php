<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-12">

<div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<div class="row">
	  <div class="col-lg-6 col-md-12">
	  
	  <div class="row action_top_wrap desktop button_small_wrap">   
	    <a href="<?php echo $add_link;?>" class="<?php echo ItemHtmlWrapper::newBtnClass()?>">
	    <i class="fas fa-plus"></i> <?php echo $add_label?>
	    </a>   
	    
	    <a href="javascript:;" class="data_table_refresh <?php echo ItemHtmlWrapper::refreshBtnClass()?>">
	      <i class="fas fa-sync"></i> <?php echo translate("REFRESH")?>
	    </a>
	    
	    <a href="javascript:;" class="data_tables_delete btn btn-secondary">
	      <i class="fas fa-trash"></i> <?php echo translate("DELETE")?>
	    </a>
      </div>
      
        <div class="action_top_mobile">	    
		  <button class="btn bmd-btn-icon dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    <i class="material-icons">more_vert</i>
		  </button>  
		  <div class="dropdown-menu dropdown-menu-left" >
		    <a class="dropdown-item" href="<?php echo $add_link;?>"><?php echo $add_label?></a>		    
		    <a class="dropdown-item data_table_refresh" href="javascript:;" ><?php echo translate("REFRESH")?></a>
		    <a class="dropdown-item show_filter" href="javascript:;" ><?php echo translate("FILTER")?></a>
		  </div>
		  
		  <a href="javascript:;" class="data_tables_delete">
		     <i class="fas fa-trash"></i> 
		  </a>		
       </div> <!--mobile-->
	  
	  </div> <!--col-->
	  <div class="col-lg-6 col-md-12 relative">
	  
	   <?php
		 echo CHtml::beginForm('','post',array(
		  'id'=>"frm_table_filter",		  
		)); 
	  ?> 	
  
	   <div class="search_wrap">
	      <button type="submit" class="btn">
		   <i class="fas fa-search"></i>
	     </button>  	
	     <?php 
	     echo CHtml::textField('search_field','',array(
	      'placeholder'=>translate("Search supplier, purchase order# or note"),
	      'class'=>"form-control search_field"
	     ));
	     ?>
	     <a href="javascript:;" class="icon close_search"><i class="fas fa-times"></i></a>
	   </div> <!--search_wrap-->
	  
	  
	  <?php
		 echo CHtml::beginForm('','post',array(
		  'id'=>"frm_table_filter",		  
		)); 
		?> 	    
	    <div class="row action_top_wrap button_small_wrap action_top_filter">   
	    	    
	       <div class="col-sm-4 col-md-4" >	      
	        
<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" 
     aria-haspopup="true" aria-expanded="true">
     <i class="glyphicon glyphicon-cog"></i>
     <span class="filter_label" data-field="status"><?php echo translate("All STATUS")?></span>
     <span class="caret"></span>
  </button>
  
  <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">  
      <?php        
      foreach ($purchase_status as $purchase_key=>$purchase_status_val):?>
    <li>
      <label>       
        <?php 
        
        $class_name = "filter_data";
        if($purchase_key=="all"){
        	$class_name = "filter_all";
        }
        
        echo CHtml::checkBox("status[$x]",true,array(
         'value'=>$purchase_key,
         'class'=>$class_name
        ));
        $x++;
        ?>
        
        <?php echo $purchase_status_val;?>
        
      </label>      
      <?php if($purchase_key=="all"):?>
      <hr></hr>
      <?php endif;?>
    </li>        
    <?php endforeach;?>
  </ul>
</div> <!--dropdown-->

	       
	       </div> <!--col-->	     
	       
	       <div class="col-sm-4 col-md-4"  >	      
	         
	       
<?php $x = 0;?>
	       
<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" 
     aria-haspopup="true" aria-expanded="true">
     <i class="glyphicon glyphicon-cog"></i>
     <span class="filter_label" data-field="supplier"><?php echo translate("All supplier")?></span>
     <span class="caret"></span>
  </button>
  
  <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">  
      <?php        
      foreach ($supplier as $supplier_key=>$supplier_val):?>
    <li>
      <label>       
        <?php 
        
        $class_name = "filter_data";
        if($supplier_key=="all"){
        	$class_name = "filter_all";
        }
        
        echo CHtml::checkBox("supplier_id[$x]",true,array(
         'value'=>$supplier_key,
         'class'=>$class_name
        ));
        $x++;
        ?>
        
        <?php echo $supplier_val;?>
        
      </label>      
      <?php if($supplier_key=="all"):?>
      <hr></hr>
      <?php endif;?>
    </li>        
    <?php endforeach;?>
  </ul>
</div> <!--dropdown-->


	       </div> <!--col-->	
	       
	       <div class="col-sm-4 col-md-4" >	      
	        
	        <button type="submit" class="btn btn-info">
			<?php echo translate("Apply Filter")?>
			</button>  	  

			<a href="javascript:;" class="icon init_field_search">
	          <i class="fas fa-search"></i>
	        </a>
	        		
	       </div> <!--col-->	
	       
	       
         
	    
	    </div>  <!--action-->
	    <?php echo CHtml::endForm(); ?>	    
	  
	  </div> <!--col-->
	</div> <!--row-->
	

<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_table",
  'onsubmit'=>"return false;"
)); 
?> 	    
<table id="table_list" class="table purchase_list data_tables table-hover">
<thead>
<tr> 
 <th width="10%"><?php echo translate("PO#")?></th> 
 <th width="10%"><?php echo translate("Date")?></th> 
 <th width="15%"><?php echo translate("Supplier")?></th> 
 <th width="10%"><?php echo translate("Status")?></th> 
 <th width="15%"><?php echo translate("Received")?></th> 
 <th width="15%"><?php echo translate("Expected on")?></th> 
 <th width="10%" class="col-qty"><?php echo translate("Total")?></th> 
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


