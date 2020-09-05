<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-12">

<div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	<div class="row">
	<div class="col-lg-6 col-md-12" >
	
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
    
    <div class="col-lg-6 col-md-12 relative" >
    
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
	     ));
	     ?>
	     <a href="javascript:;" class="icon close_search"><i class="fas fa-times"></i></a>
	   </div> <!--search_wrap-->
	  
	   
        
      <div class="row action_top_wrap button_small_wrap action_top_filter">
         <div class="col-sm-4 col-md-4" >           
           <?php 
           $this->renderPartial(APP_FOLDER.'.views.item.dropdown',array(
               'name'=>"cat_id",
               'type'=>'checkbox',
			   'list'=>$category_list,
			   'data_field'=>"category",
			   'default_selection'=>"All category"
			),false);
           ?>                 
         </div> <!--col-->
         
         <div class="col-sm-4 col-md-4"     >           
           <?php 
           $this->renderPartial(APP_FOLDER.'.views.item.dropdown',array(
               'name'=>"items",
               'type'=>'radio',
			   'list'=>$stock_list,
			   'data_field'=>"items",
			   'default_selection'=>"All items"
			),false);
           ?>                 
         </div> <!--col-->
         
         <div class="col-sm-4 col-md-4"    >	      	       	        	        
	        <button type="submit" class="<?php echo ItemHtmlWrapper::filterBtnClass()?>">
			<?php echo translate("Apply Filter")?>
			</button>  	       
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
<table id="table_list" class="table data_tables table-hover table_item_list">
<thead>
<tr>
 <th width="3%" class="col-center col-checkbox"><input name="select_all" value="1" id="select_all" type="checkbox"></th> 
 <th width="3%" class="col-center no-sort"></th> 
 <th width="25%"><?php echo translate("Name")?></th>
 <th width="15%"><?php echo translate("Category")?></th>
 <th width="10%" class="col-qty"><?php echo translate("Price")?></th>
 <th width="10%" class="col-qty"><?php echo translate("Cost")?></th>
 <th width="10%" class="col-qty"><?php echo translate("In Stock")?></th>
 <th width="10%" class="col-center no-sort"></th>
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


