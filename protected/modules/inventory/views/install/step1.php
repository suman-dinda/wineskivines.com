
<div class="main_login_wraper">
<?php
$this->renderPartial(APP_FOLDER.'.views.install.nav',array(
    'step'=>$step
));
$error_count=0;
?>

<div class="install_content">
  <ul class="list-group">
  <?php foreach ($data as $table):?>
    <li class="list-group-item">
      <?php       
      $stats = "<span class=\"text-success\">[OK]</span>";
      if(!FunctionsV3::checkIfTableExist($table)){      	 
      	 $stats = "<span class=\"text-danger\">[table not found]</span>";
      	 $error_count++;
      }
      echo translate("Checking table [table] ... [stats]",array(
        '[table]'=>$table,
        '[stats]'=>$stats
      ))
      ?>
    </li>
  <?php endforeach;?>
  </ul>
  
  <?php if($error_count<=0):?>
   <p class="text-right">
    <a href="<?php echo Yii::app()->createUrl(APP_FOLDER.'/install/step2')?>" class="btn btn-raised btn-primary"><?php echo translate("Next")?></a>
   </p>   
   <?php 
   Yii::app()->functions->updateOptionAdmin('inventory_install_steps',1);
   ?>
  <?php else :?>
  <p class="text-danger text-center">
    <i class="fas fa-exclamation-circle" style="font-size:20px;"></i>
    <?php echo translate("Missing table cannot continue with installation")?>
  </p>
  <?php endif;?> 
  
</div>

</div>
<!--main_login_wraper-->
