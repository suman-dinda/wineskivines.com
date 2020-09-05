<div class="page_header">
  <div class="col"><div class="rounded-circle"><i class="fa fa-cog"></i></div></div>
  <div class="col">
    <h2><?php echo translate("Settings")?></h2>
    <h6><?php echo translate("Inventory settings")?></h6>	  
  </div>
</div>


<ul class="list-group pt-4">	  

  <a href="<?php echo Yii::app()->createUrl(APP_FOLDER."/adm/general")?>" class="list-group-item
  <?php echo Yii::app()->controller->action->id=="general"?"active":'';?>
  ">
    <?php echo translate("General")?>
  </a>
  
  <a href="<?php echo Yii::app()->createUrl(APP_FOLDER."/adm/settings_reports")?>" class="list-group-item
  <?php echo Yii::app()->controller->action->id=="settings_reports"?"active":'';?>
  ">
    <?php echo translate("Reports")?>
  </a>
 
  <a href="<?php echo Yii::app()->createUrl(APP_FOLDER."/adm/databaseupdate")?>" class="list-group-item 
  <?php echo Yii::app()->controller->action->id=="databaseupdate"?"active":'';?>
  ">
    <?php echo translate("Database update")?>
  </a>
  
  <a href="<?php echo Yii::app()->createUrl(APP_FOLDER."/adm/cronjobs")?>" class="list-group-item 
  <?php echo Yii::app()->controller->action->id=="cronjobs"?"active":'';?>
  ">
    <?php echo translate("Cron jobs")?>
  </a>
  
  
</ul>