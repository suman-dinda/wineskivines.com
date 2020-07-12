<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-7">

<div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	
	<div class="row">
	<div class="col-md-6">
	
		<div class="row action_top_wrap button_small_wrap">   
	    <a href="<?php echo $add_link;?>" class="btn btn-info">
	    <i class="fas fa-plus"></i> <?php echo $add_label?>
	    </a>   
	    
	    <a href="javascript:;" class="data_table_refresh btn btn-primary">
	      <i class="fas fa-sync"></i> <?php echo translate("REFRESH")?>
	    </a>
	    
	    <a href="javascript:;" class="data_tables_delete btn btn-secondary">
	      <i class="fas fa-trash"></i> <?php echo translate("DELETE")?>
	    </a>
	    </div>
    
    </div> <!--col-->
    
    <div class="col-md-6 relative">
    
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
		  'placeholder'=>translate("Search role"),
		  'class'=>"form-control search_field",	
		  //'required'=>true
		 ));
		 ?>
		 <a href="javascript:;" class="icon close_search"><i class="fas fa-times"></i></a>
	   </div> <!--search_wrap-->
	   
	   <div class="row action_top_wrap button_small_wrap">
	   
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
<table id="table_list" class="table data_tables data_tables_user table-hover">
<thead>
<tr>
 <th width="7%" class="col-checkbox"><input name="select_all" value="1" id="select_all" type="checkbox"></th> 
 <th><?php echo translate("Role")?></th> 
 <th><?php echo translate("Access")?></th> 
 <th class="col-qty"><?php echo translate("Employee")?></th> 
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


