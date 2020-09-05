<?php $this->renderPartial('/layouts/header');?>
<body>
<?php echo $content;?> 
<p class="pt-3 text-center text-muted">&copy; <?php echo date("Y")?> Karenderia inventory system. All rights reserved.</p>
</body>
<?php $this->renderPartial('/layouts/footer');?>