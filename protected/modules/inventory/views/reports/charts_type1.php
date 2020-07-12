<div class="btn-group">
<button class="btn dropdown-toggle" type="button" id="btn_chart_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
<?php echo translate($default_value)?>
</button>
<div class="dropdown-menu" aria-labelledby="btn_chart_type">
<?php if(is_array($data) && count($data)>=1):?>
<?php foreach ($data as $key=>$val):?>
<a class="dropdown-item chart_type_options" data-id="<?php echo $key?>" 
data-label="<?php echo translate($val)?>" href="javascript:;"><?php echo translate($val)?></a>
<?php endforeach;?>
<?php endif;?>
</div>
</div>
