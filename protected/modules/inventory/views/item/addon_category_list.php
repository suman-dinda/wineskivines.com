<DIV class="main_box_wrap">

<div class="row">
<div class="col-lg-8 col-md-12">

<div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<div class="row">
	<div class="col-sm-7 col-md-7">
	
		<div class="row action_top_wrap button_small_wrap desktop">   
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
     
     <div class="col-sm-5 col-md-5 relative">
    
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
<table id="table_list" class="table data_tables table-hover">
<thead>
<tr>
 <th width="7%" class="col-checkbox"><input name="select_all" value="1" id="select_all" type="checkbox"></th> 
 <th><?php echo translate("Name")?></th> 
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


