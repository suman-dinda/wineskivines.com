<?php
echo CHtml::hiddenField('counter',0);
?>

<div class="main_login_wraper">
<div class="install_content pt-5">

  <p class="text-center"><span class="counter">0</span> of <?php echo $total_table?></p>

  <ul class="list-group update_table_list">
    <!--<li class="list-group-item"></li>-->
  </ul>
  
  <div class="inline_loader"></div>
  
  <p class="text-right results">
  </p>

</div>
<!--install_content-->
</div>
