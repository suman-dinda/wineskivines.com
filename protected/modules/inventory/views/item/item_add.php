<?php
 echo CHtml::beginForm('','post',array(
  'id'=>"frm_ajax",
  'onsubmit'=>"return false;"	  
)); 
?> 	
	
<DIV class="main_box_wrap">

<div class="card card_medium" id="box_wrap">
	<div class="card-body">
	
<div class="row">
<div class="col-md-6">
 
			 
	   <?php MultilangForm::setForm(array(
	      'Item Name','Description'
	   ), array(
	      'item_name','item_description'
	   ), (array)$data,
	      array(
	        true, false
	      ),array(
	       'text','textarea'
	      )
	   );
	   
	   MultilangForm::setStatusList($data);
	   ?>	  
		
</div> <!--COL-->


<div class="col-md-6 pad_form">

  <h3><span class="font-weight-light text-danger">*</span><?php echo translate("Category")?></h3> 
  
  <?php 
   $category_data = isset($data['category'])?json_decode($data['category'],true):false;	   
   ?>       
   <?php if(is_array($category) && count($category)>=1):?>
   <?php foreach ($category as $key => $category_val): ?>
   
   <?php 
    $min_length= array(
	   'required'=>false,
	   'value'=>$category_val['cat_id'],
	   'id'=>"cattegory_".$category_val['cat_id'],
	  );
	  if($key==0){
	  	 $min_length = array(
	  	   'required'=>true,
	  	   'minlength'=>1,
	  	   'value'=>$category_val['cat_id'],
	       'id'=>"cattegory_".$category_val['cat_id'],
	  	 );
	  }		  		
	  
	  echo ItemHtmlWrapper::checkboxInline('category[]',
	   $category_val['category_name'],
	   in_array($category_val['cat_id'] , (array) $category_data)?true:false,
	   $min_length
	  );
   ?>
 
   
   <?php endforeach;?>
   <?php endif;?>  
   
   
   <div class="row">
   <div class="col-md-12">
   <!--SINGLE UPLOAD-->
   <div class="form-group pad_form_top"> 
    <p><?php echo translate("Featured image")?></p>
	  <div class="div_dropzone dropzone" id="single_dropzone">
	    <div class="dz-default dz-message">
	     <i class="fas fa-cloud-upload-alt"></i>
	     <p><?php echo translate("Drop files here to upload")?></p>
	     </div>
	  </div>
   </div>      
   <!--SINGLE UPLOAD-->
   </div>     
   </div> <!--row-->
      
</div> <!--col-->

</div> <!--end row-->
 

  </div>
 </div> <!--card-->
 
 
<div class="row">
    <div class="col-md-6">
    
    	<!--INVENTORY-->
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
			      
	     <h3><?php echo translate("Inventory")?></h3>
	      	     	     	      
	     <div class="row"> 
	       <div class="col-md-6">
	        <!--<b><?php echo translate("Track stock")?></b>-->
	        
	         <?php 
	          if(!isset($data['track_stock'])){
		         $data['track_stock']=0;
		      }
		      echo ItemHtmlWrapper::formSwitch('track_stock','Track stock',
		        $data['track_stock']==1?true:false,
		        array(
		          'value'=>1,
		          'class'=>"track_stock"
		        )
		      );
		      ?>  
	        
	       </div>
	       <div class="col-md-6 text-right">
	       
	       
	       </div>
	     </div> <!--row-->
	     
	     
	     <div class="row pt-4 inventory_track_stock">
	       <div class="col-md-6">
	       
	         <div class="form-group">
	           <label for=""><?php echo translate("In stock")?></label>
	           <?php            
	           echo CHtml::textField('in_stock',
	           ''
	           ,array(
	            'class'=>"form-control numeric_only",
	            'placeholder'=>"0"
	           ));
	           ?>
	         </div>	 
	       
	       </div>
	       <div class="col-md-6">
	       
	       <div class="form-group">
	           <label for=""><?php echo translate("Low stock")?></label>
	           <?php            
	           echo CHtml::textField('low_stock',
	           ''
	           ,array(
	            'class'=>"form-control numeric_only",
	            'placeholder'=>"0"
	           ));
	           ?>
	           <small><?php echo translate("Item quantity at which you will be notified about low stock")?></small>
	         </div>	 
	       
	       </div> <!--col-->
	       
	        <div class="col-md-6">
	       
	         <div class="form-group">
	           <label for=""><?php echo translate("Primary supplier")?></label>
	           <?php            
	           echo CHtml::dropDownList('supplier_id',
	           isset($data['supplier_id'])?$data['supplier_id']:''
	           ,
	           (array)$supplier_list
	           ,array(
	             'class'=>"form-control"
	           ));
	           ?>
	         </div>	 
	       
	       </div>
	       
	     </div> <!--row-->
	      	       	      
		</div> <!--card-body-->
	</div> <!--card-->	
	<!--END INVENTORY-->

    
    <!--PRICE-->
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
			      
	     <h3><?php echo translate("Price")?></h3>
	      
	    
	        
	        <?php 
	        if(!isset($data['with_size'])){
	        	$data['with_size']=0;
	        }
	        echo ItemHtmlWrapper::formSwitch('with_size',
	         'With Size',
	         $data['with_size']==1?true:false,
	         array(
		         'value'=>1,
		         'class'=>"with_size"
		        )	         
	        );
	        ?>
	        
	     
	      <div class="price_wrap pt-4"></div> 
	      	       	      
		</div> <!--card-body-->
	</div> <!--card-->	
	<!--END PRICE-->
	
	
	<!--DISCOUNT-->
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
			      
	     <h3><?php echo translate("Discount")?></h3>
	      	     	     
	      <div class="form-group">
           <label for=""><?php echo translate("Fixed Amount")?></label>
           <?php            
           echo CHtml::textField('discount',
           isset($data['discount'])?$data['discount']:''
           ,array(
            'class'=>"form-control numeric_only",
            'placeholder'=>translate("0.00"),
           ));
           ?>
         </div>	     	
	      	       	      
		</div> <!--card-body-->
	</div> <!--card-->	
	<!--END DISCOUNT-->
	
	
	
	<!--TWO FLAVOR-->
	<?php if(is_array($ingredients) && count($ingredients)>=1):?>
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
	      <h3><?php echo translate("Two Flavors")?></h3> 
	      	      	    	        
	      <?php 
	      if(!isset($data['two_flavors'])){
	         $data['two_flavors']=0;
	      }
	      echo ItemHtmlWrapper::formSwitch('two_flavors',"Enabled",
	        $data['two_flavors']==2?true:false,
	        array(
	         'value'=>2,
	         'class'=>"two_flavors"
	        )
	      );
	      ?>
	        	      
	      
		</div> <!--card-body-->
	</div> <!--card-->
	<?php endif;?>
	<!--TWO FLAVOR-->
	
	<!--ADDON-->
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
	
		<div class="row pb-4">
		 <div class="col-md-4"><h3><?php echo translate("Addon item")?></h3></div>
		 <div class="col-md-4 ">
		    <?php 
		    echo CHtml::dropDownList('addon_category_list','',$addon_category,array(
		     'class'=>"form-control"
		    ));
		    ?>		    
		 </div> <!--col-->
		 <div class="col-md-4 ">
		   <a href="javascript:;" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?> add_addon_category">
			    <i class="fas fa-plus"></i> <?php echo translate("ADD NEW ADDON")?>
			 </a>  
		 </div> <!--col-->
		</div> <!--row-->
		
		<ul class="addon_list"></ul>
					
		</div> <!--card-body-->
	</div> <!--card-->	
	<!--END ADDON-->

	
	</div> <!--col-->
	
	<div class="col-md-6">
	
	<!-- GALLERY -->
	<div class="card card_medium" id="box_wrap">
	   <div class="card-body">
	     <h3><?php echo translate("Gallery")?></h3>
	     
		  <!--MULTIPLE UPLOAD-->
		   <div class="form-group pad_form_top"> 		    
			  <div class="div_dropzone dropzone" id="multiple_dropzone">
			    <div class="dz-default dz-message">
			     <i class="fas fa-cloud-upload-alt"></i>
			     <p><?php echo translate("Drop files here to upload")?></p>
			     </div>
			  </div>
		   </div>      
		   <!--MULTIPLE UPLOAD-->
	     
	   </div> <!--card-body-->
	</div> <!--card-->	
	<!-- END GALLERY -->
	
	<!--COOKING REF-->
	<?php $cooking_ref = isset($data['cooking_ref'])?(array)json_decode($data['cooking_ref'],true):array() ;?>
	<?php if(is_array($cooking) && count((array)$cooking)>=1):?>
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
	      <h3><?php echo translate("Cooking Reference")?></h3> 	    
	      	      
	      <?php foreach ($cooking as $cooking_val):?>
	      <?php 
	      echo ItemHtmlWrapper::checkboxInline(
	        'cooking_ref[]',
	        $cooking_val['cooking_name'],
	        in_array($cooking_val['cook_id'],$cooking_ref)?true:false,
	        array(
	         'value'=>$cooking_val['cook_id']
	        )
	      );
	      ?>
	      <?php endforeach;?>
	      
	        
		</div> <!--card-body-->
	</div> <!--card-->	
	<?php endif;?>
	<!--END COOKING REF-->
	
	<!--INGREDIENTS-->
	<?php $ingredients_data = isset($data['ingredients'])?(array)json_decode($data['ingredients'],true):array() ;?>
	<?php if(is_array($ingredients) && count( (array) $ingredients)>=1):?>
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
	      <h3><?php echo translate("Ingredients")?></h3> 
	      
	      <?php foreach ($ingredients as $ingredients_val):?>
	      <?php 
	      echo ItemHtmlWrapper::checkboxInline(
	        'ingredients[]',
	        $ingredients_val['ingredients_name'],
	        in_array($ingredients_val['ingredients_id'],$ingredients_data)?true:false,
	        array(
	           'value'=>$ingredients_val['ingredients_id']
	        )
	      );
	      ?>
	      <?php endforeach;?>
	      
		</div> <!--card-body-->
	</div> <!--card-->
	<?php endif;?>
	<!--END INGREDIENTS-->	
	
	
	<!--DISH-->
	<?php $dish_data = isset($data['dish'])?(array)json_decode($data['dish'],true):array() ;?>
	
	<?php if(is_array($dish) && count( (array) $dish)>=1):?>
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
	      <h3><?php echo translate("Dish")?></h3> 
	      
	      <?php foreach ($dish as $dish_val):?>
	      <?php 
	      echo ItemHtmlWrapper::checkboxInline(
	        'dish[]',
	        $dish_val['dish_name'],
	        in_array($dish_val['dish_id'],$dish_data)?true:false,
	        array(
	          'value'=>$dish_val['dish_id']
	        )
	      );
	      ?>     
	      <?php endforeach;?>
	      
		</div> <!--card-body-->
	</div> <!--card-->	
	<?php endif;?>
	<!--END DISH-->
	
	<!--TAX-->
	<?php if(is_array($dish) && count($dish)>=1):?>
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
	      <h3><?php echo translate("Tax")?></h3> 
	     
	      <?php 
	      if(!isset($data['non_taxable'])){
	         $data['non_taxable']='';
	      }
	      echo ItemHtmlWrapper::formSwitch('non_taxable',"Non taxable",
	        $data['non_taxable']==2?true:false,
	        array(
	         'value'=>2
	        )
	      );
	      ?> 
	     
		</div> <!--card-body-->
	</div> <!--card-->	
	<?php endif;?>
	<!--END TAX-->
	
	
	<!--PIINTS-->
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
	      <h3><?php echo translate("Points")?></h3> 
	      
	       <div class="form-group row"> 
			   <div class="col-md-3">
			   <?php
			   echo CHtml::textField('points_earned',			   
			   isset($data['points_earned'])? $data['points_earned']>0?$data['points_earned'] :''  :''
			   ,array(
			    'class'=>'form-control numeric_only',
			    'placeholder'=>translate("Points earn"),
			   ));
			   ?>  	
		       </div>   
	       </div>
	       
	        <?php 
	        if(!isset($data['points_disabled'])){
	           $data['points_disabled']='';
	        }
	        echo ItemHtmlWrapper::formSwitch('points_disabled','Disabled Points on this item',
	          $data['points_disabled']==2?true:false,
	          array(
		          'value'=>2
		        )
	        );
	        ?>
	      
		</div> <!--card-body-->
	</div> <!--card-->	
	<!--END PIINTS-->
	
	<!--PACKAGIN WISE-->
	<div class="card card_medium" id="box_wrap">
		<div class="card-body">
	      <h3><?php echo translate("Packaging Wise")?></h3> 
	      
	      <div class="form-group row"> 
			   <div class="col-md-3">
			   <?php
			   echo CHtml::textField('packaging_fee',			   
			   isset($data['packaging_fee'])? $data['packaging_fee']>0? normalPrettyPrice($data['packaging_fee']) :''  :''
			   ,array(
			    'class'=>'form-control numeric_only',
			    'placeholder'=>translate("Fee"),
			   ));
			   ?>  	
		       </div>   
	       </div>
	       
	        <?php 
	        if(!isset($data['packaging_incremental'])){
	           $data['packaging_incremental']='';
	        }
	        echo ItemHtmlWrapper::formSwitch('packaging_incremental','Enabled Packaging Incremental',
	          $data['packaging_incremental']==1?true:false,
	          array(
		          'value'=>1
		        )
	        );
	        ?>
	      
		</div> <!--card-body-->
	</div> <!--card-->	
	<!--END PACKAGIN WISE-->
	
	</div> <!--col-->
</div> <!--row-->



<div class="row">
    <div class="col-md-6">
	
	</div> <!--col-->
	
	<div class="col-md-6">
	
	</div> <!--col-->
</div> <!--row-->


<div class="row">
    <div class="col-md-6">
	
	</div> <!--col-->
	
	<div class="col-md-6">
	
	</div> <!--col-->
</div> <!--row-->


</DIV><!-- box-->


<div class="floating_action">
       <a href="<?php echo Yii::app()->createUrl(APP_FOLDER.'/item/list')?>" class="<?php echo ItemHtmlWrapper::cancelBtnClass()?>">
       <?php echo translate("CANCEL")?>
       </a>
              
       <?php if(isset($data['item_id'])):?>
       <a href="javascript:;" class="<?php echo ItemHtmlWrapper::deleteBtnClass()?> delete_record"><?php echo translate("DELETE")?></a>        
       <?php endif;?>
       
       <button type="submit" class="<?php echo ItemHtmlWrapper::saveBtnClass()?>"><?php echo translate("SAVE")?></button>                  
       
</div>

<?php echo CHtml::endForm(); ?>