<?php
class DatablelocalizeController extends CController
{
	public $layout='layout';
	public $ajax_controller='Ajaxadmin';		
	public $access_actions;	
	
	public function init()
	{
		FunctionsV3::handleLanguage();
	}
	
	public function actionIndex()
	{
		header('Content-type: application/json');
    	$data = array(
    	  'decimal'=>'',
    	  'emptyTable'=> translate('No data available in table'),
    	  'info'=> translate('Showing [start] to [end] of [total] entries',array(
    	    '[start]'=>"_START_",
    	    '[end]'=>"_END_",
    	    '[total]'=>"_TOTAL_",
    	  )),
    	  'infoEmpty'=> translate("Showing 0 to 0 of 0 entries"),
    	  'infoFiltered'=>translate("(filtered from [max] total entries)",array(
    	    '[max]'=>"_MAX_"
    	  )),
    	  'infoPostFix'=>'',
    	  'thousands'=>',',
    	  'lengthMenu'=> translate("Show [menu] entries",array(
    	    '[menu]'=>"_MENU_"
    	  )),
    	  'loadingRecords'=>translate('Loading...'),
    	  'processing'=>translate("Processing..."),
    	  'search'=>translate("Search:"),
    	  'zeroRecords'=>translate("No matching records found"),
    	  'paginate' =>array(
    	    'first'=>translate("First"),
    	    'last'=>translate("Last"),
    	    'next'=>translate("Next"),
    	    'previous'=>translate("Previous")
    	  ),
    	  'aria'=>array(
    	    'sortAscending'=>translate(": activate to sort column ascending"),
    	    'sortDescending'=>translate(": activate to sort column descending")
    	  )
    	);
    	echo json_encode($data);
	}
	
	public function actionValidation()
	{
		ob_start();
		header("Content-type:application/javascript");
		?>			
		(function( factory ) {
			if ( typeof define === "function" && define.amd ) {
				define( ["jquery", "../jquery.validate"], factory );
			} else if (typeof module === "object" && module.exports) {
				module.exports = factory( require( "jquery" ) );
			} else {
				factory( jQuery );
			}
		}(function( $ ) {
		
		/*
		 * Translated default messages for the jQuery validation plugin.
		 * Locale: DE (German, Deutsch)
		 */
		$.extend( $.validator.messages, {
			required: "<?php echo translate("This field is a required field.")?>",
			maxlength: $.validator.format( "<?php echo translate("Please enter a maximum of {0} characters.")?>" ),
			minlength: $.validator.format( "<?php echo translate("Please enter at least {0} characters.")?>" ),
			rangelength: $.validator.format( "<?php echo translate("Please enter at least {0} and a maximum of {1} characters.")?>" ),
			email: "<?php echo translate("Please enter a valid email address.")?>",
			url: "<?php echo translate("Please enter a valid URL.")?>",
			date: "<?php echo translate("Pease enter a valid date.")?>",
			number: "<?php echo translate("Please enter a number.")?>",
			digits: "<?php echo translate("Please only enter digits.")?>",
			equalTo: "<?php echo translate("Please repeat the same value.")?>",
			range: $.validator.format( "<?php echo translate("Please enter a value between {0} and {1}.")?>" ),
			max: $.validator.format( "<?php echo translate("Please enter a value less than or equal to {0}.")?>" ),
			min: $.validator.format( "<?php echo translate("Please enter a value greater than or equal to {0}.")?>" ),
			creditcard: "<?php echo translate("Please enter a valid credit card number.")?>"
		} );
		return $;
		}));
		<?php
		$forms = ob_get_contents();
        ob_end_clean();	
        echo $forms;
	}
	
}
/*end class*/