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
$x = 0;
?> 	   

<div class="row top_filter">

<div class="col-lg-3 col-md-3 col-sm-3"   >
<div class="card"><div class="card-body">
  <?php echo CHtml::textField('date_range','',array(
    'class'=>"form-control date_range no_border",
    'readonly'=>true
  ))?>
</div></div> <!--card-->
</div> <!--col-->

<div class="col-lg-2 col-md-3 col-sm-3"   >
<div class="card"><div class="card-body">
<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" 
     aria-haspopup="true" aria-expanded="true">
     <i class="glyphicon glyphicon-cog"></i>
     <span class="filter_label" data-field="user"><?php echo translate("All user")?></span>
     <span class="caret"></span>
  </button>
  
  <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">  
      <?php  foreach ($user_list as $user_key=>$user_val):?>
    <li>
      <label>       
        <?php 
        
        $class_name = "filter_data";
        if($user_key=="all"){
        	$class_name = "filter_all";
        }
        
        echo CHtml::checkBox("user[$x]",true,array(
         'value'=>$user_key,
         'class'=>$class_name
        ));
        $x++;
        ?>
        
        <?php echo $user_val;?>
        
      </label>      
      <?php if($user_key=="all"):?>
      <hr></hr>
      <?php endif;?>
    </li>        
    <?php endforeach;?>
  </ul>
</div> <!--dropdown-->
</div></div> <!--card-->
</div> <!--col-->



<div class="col-lg-2 col-md-3 col-sm-3"   >
<div class="card"><div class="card-body">
<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" 
     aria-haspopup="true" aria-expanded="true">
     <i class="glyphicon glyphicon-cog"></i>
     <span class="filter_label" data-field="reasons"><?php echo translate("All reasons")?></span>
     <span class="caret"></span>
  </button>
  
  <?php $x = 0;?>
  <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">  
    <?php  foreach ($reason as $reason_key=>$reason_val):?>
    <li>
      <label>       
        <?php 
        
        $class_name = "filter_data";
        if($reason_key=="all"){
        	$class_name = "filter_all";
        }
        
        echo CHtml::checkBox("reason[$x]",true,array(
         'value'=>$reason_key,
         'class'=>$class_name
        ));
        $x++;
        ?>
        
        <?php echo $reason_val;?>
        
      </label>      
      <?php if($reason_key=="all"):?>
      <hr></hr>
      <?php endif;?>
    </li>        
    <?php endforeach;?>
  </ul>
</div> <!--dropdown-->
</div></div> <!--card-->
</div> <!--col-->

<div class="col-lg-2 col-md-3 col-sm-3"   >
 <div class="card"><div class="card-body">
 <button type="submit" class="btn btn-info">
   <?php echo translate("Apply Filter")?>
 </button>
 </div></div> <!--card-->
</div>

</div><!-- top_filter-->


<div class="pt-3"></div>

<div class="row">
<div class="col-md-12">


<div class="card card_medium" id="box_wrap">
	<div class="card-body">
			  
    <div class="row">     
      <div class="col-md-7"></div> 
      <div class="col-md-5 text-right relative">
      
  
  
  <div class="search_wrap" style="margin-top: -10px;">
	 <button type="submit" class="btn">
		<i class="fas fa-search"></i>
	 </button>  	       
	 <?php 
	 echo CHtml::textField('search_field','',array(
	  'placeholder'=>translate("Search by name or SKU"),
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
   
     
      
      
      </div> <!--col-->
    </div> <!--row-->
        
    
<?php echo CHtml::endForm(); ?>	      
    
<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_table",
  'onsubmit'=>"return false;"
)); 
?> 	    
<table id="table_list" class="table data_tables table-hover table_stocks_history not_editable">
<thead>
<tr> 
 <th width="15%" class="no-sort"><?php echo translate("Date")?></th> 
 <th width="15%" class="no-sort"><?php echo translate("Item")?></th> 
 <th width="10%" class="no-sort"><?php echo translate("By")?></th> 
 <th width="15%" class="no-sort"><?php echo translate("Reason")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Adjustment")?></th> 
 <th width="10%" class="col-qty no-sort"><?php echo translate("Stock after")?></th> 
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


