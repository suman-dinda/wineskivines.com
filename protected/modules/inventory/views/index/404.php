<div class="page-wrap d-flex flex-row align-items-center pt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <span class="display-1 d-block"><?php echo translate("404")?></span>
                <div class="mb-4 lead"><?php echo translate("The page you are looking for was not found")?>.</div>
                <a href="<?php echo Yii::app()->createUrl(APP_FOLDER.'/index/dashboard')?>" class="btn btn-link">
                 <?php echo translate("Back to Home")?>
                </a>
            </div>
        </div>
    </div>
</div>