<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_ajax",
  'onsubmit'=>"return false;"	  
)); 
?> 	
	
<DIV class="main_box_wrap">

<div class="row">
<div class="col-md-6">

 <div class="card card_medium" id="box_wrap">
	<div class="card-body">
			 
	 <?php echo ItemHtmlWrapper::formTextField2("role_name","Name",
	 isset($data['role_name'])?$data['role_name']:'',array(
	   'class'=>"form-control",
	   'required'=>true
	 ))?>
	 
	 <?php 
	 $access = array();
	 if(isset($data['access'])){
	 	if ( !$access = json_decode($data['access'],true)){
	 		  $access = array();
	 	}
	 }	 
	 ?>
	 	 	 	 
	 <?php if(is_array($menu) && count($menu)>=1):?>
	 <ul class="access_role_list list-groupx pt-4">
	 <?php foreach ($menu['items'] as $val): ?>
	   <li class="list-group-itemx" style="<?php echo $val['id']=="userprofile"?"display:none;":"";?>" >	   		 
	   <?php echo ItemHtmlWrapper::formRadio('access[]', $val['linkOptions']['data-content'],
	   in_array($val['id'],(array)$access)?true:false
	   ,array(
	     'value'=>$val['id'],
	     'class'=>"parent_access"
	   ))?>
	   
	   
	   <?php if(isset($val['items'])):?>
	   <?php if(is_array($val['items']) && count($val['items'])>=1):?>
	   <ul class="list-groupx" >
	      <?php foreach ($val['items'] as $submenu):?>
	      <li class="list-group-itemx">
	      <?php echo ItemHtmlWrapper::formRadio('access[]',$submenu['label'],
	      in_array($submenu['id'],(array)$access)?true:false
	      ,array(
	       'value'=>$submenu['id'],
	       'class'=>"child_access"
	      ))?>	      
	      </li>

	      <?php if(isset($submenu['sub_items'])):?>
	      <ul>  
	        <?php foreach ($submenu['sub_items'] as $sub_items):?>
	        <li>
	        <?php echo ItemHtmlWrapper::formRadio('access[]',$sub_items['label'],
		      in_array($sub_items['id'],(array)$access)?true:false
		      ,array(
		       'value'=>$sub_items['id'],
		       'class'=>"child_access"
		      ))?>	      
	        </li>
	        <?php endforeach;?>
	      </ul>
	      <?php endif;?>
	            
	      <?php endforeach;?>
	   </ul>
	   <?php endif;?>
	   <?php endif;?>
	   
	   </li>
	   
	 <?php endforeach;?>
	 </ul>
	 <?php endif;?>
	
	</div>
 </div> <!--card-->
  
 
</div> <!--COL-->


</div> <!--end row-->

</DIV>


<div class="floating_action">
       <a href="<?php echo Yii::app()->createUrl(APP_FOLDER.'/user/access_rights')?>" class="btn btn-secondary ">
       <?php echo translate("CANCEL")?>
       </a>
       
       <?php if(isset($data['role_id'])):?>
       <a href="javascript:;" class="btn btn-danger delete_record"><?php echo translate("DELETE")?></a>        
       <?php endif;?>
       
       <button type="submit" class="btn btn-info"><?php echo translate("SAVE")?></button>                  
       
</div>

<?php echo CHtml::endForm(); ?>