<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-8">

<div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	
	<div class="row">
	<div class="col-sm-6 col-md-6" >
	
		<div class="row action_top_wrap desktop button_small_wrap">   
	    <a href="javascript:;" class="data_table_refresh btn btn-primary">
	      <i class="fas fa-refresh"></i> <?php echo translate("REFRESH")?>
	    </a>
	    </div>
	    
		<div class="action_top_mobile">	    
		  <button class="btn bmd-btn-icon dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="material-icons">more_vert</i>
		  </button>  
		  <div class="dropdown-menu dropdown-menu-left" >		
			<a class="dropdown-item data_table_refresh" href="javascript:;" ><?php echo translate("REFRESH")?></a>
			<a class="dropdown-item show_filter" href="javascript:;" ><?php echo translate("FILTER")?></a>
		  </div>  
		  <a href="javascript:;" class="data_tables_delete">
			 <i class="fas fa-trash"></i> 
		  </a>
		</div> <!--mobile-->
	    	    
    </div> <!--col-->
    
    <div class="col-sm-6 col-md-6 relative">
    
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
		  'placeholder'=>translate("Search name"),
		  'class'=>"form-control search_field",	
		  //'required'=>true
		 ));
		 ?>
		 <a href="javascript:;" class="icon close_search"><i class="fas fa-times"></i></a>
	   </div> <!--search_wrap-->
	   
	   <div class="row action_top_wrap button_small_wrap action_top_filter">
	   
	   <div class="col-md-12 text-right">	      	       
			<a href="javascript:;" class="icon init_field_search">
			  <i class="fas fa-search"></i>
			</a>	        
		 </div> <!--col-->
	   
	   </div> <!--action-->  
	   
	  <?php echo CHtml::endForm(); ?>	     
	
	</div> <!--col-->
	</div> <!--row-->
    

<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_table",
  'onsubmit'=>"return false;"
)); 
?> 	    
<table id="table_list" class="table data_tables table-hover data_table_merchant not_editable">
<thead>
<tr> 
 <th width="25%"><?php echo translate("Name")?></th> 
 <th><?php echo translate("ID")?></th> 
 <th width="20%"><?php echo translate("Status")?></th> 
 <th width="35%" class="col-qty"><?php echo translate("Access to inventory platform")?></th> 
 <th width="30%" class="col-qty"><?php echo translate("Access role")?></th> 
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


