<div class="btn-group">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="lang_selection" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <!--<?php echo translate("Select language")?>-->
    <i class="fas fa-flag"></i>
  </button>
  
  <?php 
  $app_con = Yii::app()->controller->id;  
  $app_action = Yii::app()->controller->action->id;  
  ?>
  
  <?php if(is_array($lang) && count($lang)>=1):?>
  <div class="dropdown-menu" aria-labelledby="lang_selection">
  <?php foreach ($lang as $val):?>
    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl(APP_FOLDER."/$app_con/$app_action",array(
     'lang'=>$val
    ))?>">
      <?php echo strtoupper(t($val))?>
    </a>
  <?php endforeach;?>
  </div>
  <?php endif;?>
</div> 