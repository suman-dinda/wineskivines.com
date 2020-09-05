<?php 
$nav[1] = array(
  'name'=>"Requirements",
  'link'=>"javascript:;"
);
$nav[2] = array(
  'name'=>"Tables",
  'link'=>"javascript:;"
);
$nav[3] = array(
  'name'=>"Updating data",
  'link'=>"javascript:;"
);
$nav[4] = array(
  'name'=>"Done",
  'link'=>"javascript:;"
);

$step = isset($step)?$step:1;
?>

<ul class="nav nav-tabs justify_center" id="myTab" role="tablist">
  <?php foreach ($nav as $key=>$val):?>
  <li class="nav-item">
    <a class="nav-link <?php echo $step==$key?"active":"disabled";?>" id="nav_<?php echo $key?>" href="javascript:;"><?php echo translate($val['name'])?></a>
  </li>      
  <?php endforeach;?>
</ul>
