<?php

$user_type = UserWrapper::getUserType();
if($user_type=="admin"){
	$this->widget('zii.widgets.CMenu', MenuWrapper::adminMenu( (array)$this->access_actions , 
	UserWrapper::getUserName(), UserWrapper::getUserEmail() ) );
} else {
	$this->widget('zii.widgets.CMenu', MenuWrapper::menu( (array)$this->access_actions , 
	UserWrapper::getUserName(), UserWrapper::getUserEmail() ) );
}