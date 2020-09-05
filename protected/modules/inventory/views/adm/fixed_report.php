<?php
echo CHtml::hiddenField('counter',0);
?>

<div class="main_login_wraper">
<div class="install_content pt-5">

  <p class="text-center">
   <?php echo translate("Total records to update [total]",array(
    '[total]'=>$total_item
   ))?>
  </p>

  <ul class="list-group fixed_report">
    <!--<li class="list-group-item"></li>-->
  </ul>
  
  <div class="inline_loader"></div>
  
  <p class="text-right results">
  </p>

</div>
<!--install_content-->
</div>
