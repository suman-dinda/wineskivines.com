<DIV class="main_box_wrap">

<div class="row">
<div class="col-lg-9 col-md-12" >

<div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
	
	<?php
		 echo CHtml::beginForm('','post',array(
		  'id'=>"frm_table_filter",		  
		)); 
		?> 	    
		
	<div class="row ">   		
	  <div class="col-lg-6 col-md-12"  >		
	    <div class="row action_top_wrap desktop button_small_wrap desktop">
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
		</div> <!--mobile-->

     </div>	<!--col-->
    
     <div class="col-lg-6 col-md-12 relative"  >
       
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
	  'placeholder'=>translate("Search adjusment #"),
	  'class'=>"form-control search_field",	
	  //'required'=>true
	 ));
	 ?>
	 <a href="javascript:;" class="icon close_search"><i class="fas fa-times"></i></a>
   </div> <!--search_wrap-->
   
   <div class="row action_top_wrap button_small_wrap action_top_filter">
   
        <div class="col-sm-6 col-md-6" >
         <?php 
           $this->renderPartial(APP_FOLDER.'.views.item.dropdown',array(
               'name'=>"reason",
               'type'=>'checkbox',
			   'list'=>$reason,
			   'data_field'=>"reason",
			   'default_selection'=>"All reason"
			),false);
           ?>             
        </div> <!--col-->
        
       <div class="col-sm-6 col-md-6" >
           <button type="submit" class="<?php echo ItemHtmlWrapper::filterBtnClass()?>">
			<?php echo translate("Apply Filter")?>
			</button>  	       			
			
			<a href="javascript:;" class="icon init_field_search">
			  <i class="fas fa-search"></i>
			</a>	        
       </div> 
   
	   
   
   </div> <!--action-->  
   
  <?php echo CHtml::endForm(); ?>	     
		       
     </div> <!--col-->
     
    </div> <!--row-->
    
    <?php echo CHtml::endForm(); ?>

<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_table",
  'onsubmit'=>"return false;"
)); 
?> 	    
<table id="table_list" class="table data_tables table-hover table_adjustment_list">
<thead>
<tr> 
 <th><?php echo translate("Adjustment #")?></th> 
 <th><?php echo translate("Date")?></th> 
 <th><?php echo translate("Reason")?></th> 
 <th><?php echo translate("Quantity")?></th> 
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


