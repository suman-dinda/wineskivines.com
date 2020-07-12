<?php
class languageBar extends CWidget {
		
	public function run() {
		$this->render('language_bar',array(
		 'lang'=>FunctionsV3::getEnabledLanguage()
		));
	}

}