
<div class="main_login_wraper">
<?php
$this->renderPartial(APP_FOLDER.'.views.install.nav',array(
    'step'=>$step
));
$error_count=0;
?>

<div class="install_content pt-5">
  <p><?php echo translate("Congratulation you have successfuly install [addon_name]",array(
   '[addon_name]'=>"<span class=\"text-success\">Karenderia inventory addon</span>"
  ))?></p>
  
  <p class="text-right">
  <a href="<?php echo Yii::app()->createUrl(APP_FOLDER."/login")?>" class="btn btn-raised btn-primary" ><?php echo translate("Next")?></a>
  </p>
</div>
<!--install_content-->

</div> <!--main-->