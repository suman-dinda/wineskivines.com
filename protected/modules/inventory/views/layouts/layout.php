<?php $this->renderPartial('/layouts/header');?>
<body>

<div class="main_wrap">

<!--Header wrap-->
<div class="header_wrap">

  <a href="javascript:;" class="bar_menu">
    <span class="fas fa-bars"></span>
  </a>
  
  <h3><?php echo $this->pageTitle?></h3>

  <ul class="top_nav">   
  
  <?php 
  $usertype =  UserWrapper::getUserType();
  if($usertype=="merchant"):
  ?>
  <li>
  <?php       
  echo ItemHtmlWrapper::formSwitch('inventory_live','Live',false  
  ,array(
    'value'=>1,
    'class'=>"inventory_live"
  ));
  ?>
  </li>
  <?php endif;?>
  
   <li>
      
   <div class="dropdown top_notification">
	  <button class="btn btn-secondary dropdown-toggle" type="button" id="drop_notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	    <i class="fas fa-bell"></i> <span class="badge badge-danger drop_notification_badge"></span>
	  </button>
	  <div  class="dropdown-menu drop_notification_list" aria-labelledby="drop_notification">
	    <a class="dropdown-item" href="javascript:;"><?php echo translate("No new notification")?></a>	    
	  </div>
  </div>
  
  <li>
  <?php
  $this->widget(APP_FOLDER.'.components.languageBar');
  ?>
  </li>   
   
   <li>
    <a class="btn btn-secondary" href="<?php echo Yii::app()->createUrl('/inventory/index/logout')?>" title="<?php echo translate("Logout")?>" >
    <i class="fas fa-sign-out-alt" ></i>
    </a>
   </li>
  </ul>
</div>
<!--end header_wrap-->

<div class="sidebar_wrap ">
  <?php $this->renderPartial('/layouts/left_menu');?>  
</div>
<!--sidebar_wrap-->

<div class="content_wrap">  
   <?php echo $content;?>   
</div> <!--content_wrap-->

</div> 
<!--main_wrap-->


</body>
<?php $this->renderPartial('/layouts/footer');?>