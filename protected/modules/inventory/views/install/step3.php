
<div class="main_login_wraper">
<?php
$this->renderPartial(APP_FOLDER.'.views.install.nav',array(
    'step'=>$step
));
$error_count=0;
echo CHtml::hiddenField('counter',0);
?>

<div class="install_content">

  <p class="text-center"><span class="counter">0</span> of <?php echo $total_table?></p>

  <ul class="list-group install_list_item">
    
  </ul>
  
  <div class="inline_loader"></div>
  
  <p class="text-right results">
  </p>

</div>
<!--install_content-->

</div> <!--main-->