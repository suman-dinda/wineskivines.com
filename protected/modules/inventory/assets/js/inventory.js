var $timenow;
var $ajax_request = {};
var $available_stocks=0;
var $inventory_allow_negative_order=0;

jQuery.fn.inv_exists = function(){return this.length>0;}

var inventoryGetStocks = function(){
	"use strict";
	var inv_selected=$(".price_cls:checked").length; 	
	var inv_value = $(".price_cls:checked").val(); 	
	var inv_item_id = $("#item_id").val();
	var inv_with_size = $("#with_size").val();
	if(inv_with_size==2){
		inv_with_size=1;
	} else {
		inv_with_size=0;
	}
	var inv_merchant_id = $("#merchant_id").val();		
	
	if(inv_selected>0){
		var data = "inv_value="+inv_value;
		data+= "&inv_item_id="+inv_item_id;
		data+= "&inv_with_size="+inv_with_size;
		data+= "&inv_merchant_id="+inv_merchant_id;		
		InvProcessAjax("getStocks",data,1);
	}
};

var getTimeNow = function(){
	"use strict";
	var d = new Date();
    var n = d.getTime(); 
    return n;
};	

var InvProcessAjax = function(action , data , single_call){
	"use strict";
	data+=addValidationRequest();
	
	$timenow = getTimeNow();
	if(!empty(single_call)){
		var $timenow = 1;
	}		
			
    $ajax_request[$timenow] = $.ajax({
	  url: inv_ajax+"/"+action,
	  method: "POST",
	  data: data ,
	  dataType: "json",
	  timeout: 20000,
	  crossDomain: true,
	  beforeSend: function( xhr ) {   
	  	
	  	 $(".add_to_cart").attr("disabled",true);
	  	 $(".remaining_stock").html( inv_loader );
	  	 $(".remaining_stock").removeClass("out_of_stock");
	  	 
	  	 if($ajax_request[$timenow] != null) {
	  	 	dump("request aborted");     
         	$ajax_request[$timenow].abort();
	  	 }
      }
    });
    
    $ajax_request[$timenow].done(function( data ) {
    	dump('done');
     	dump(data);
     	$(".remaining_stock").html( '' );
     	$(".add_to_cart").attr("disabled",false);
     	
     	if ( data.code==1){     		
     		switch (data.details.next_action){
     			case "display_stocks":
     			  
     			  $inventory_allow_negative_order = data.details.allow_negative_stock;
     			  if($inventory_allow_negative_order){
     			  	 return;
     			  }
     			 
     			  $(".remaining_stock").html( data.details.message );
     			  $available_stocks = parseFloat(data.details.available_stocks);     			  
     			  if(data.details.available_stocks<=0){
     			  	 $(".remaining_stock").addClass("out_of_stock");
     			  } else {
     			  	 $(".remaining_stock").removeClass("out_of_stock");
     			  }
     			  
     			  var initial_qty = parseFloat($("#qty").val());      	
     			  if (isNaN(initial_qty)) {
     			  	InvSetQuantity(1);
     			  } else {     			  	
     			  	InvSetQuantity(initial_qty);
     			  }     			  
     			  
     			break;
     		}
     	} else {
     		dump("failed inventory");
     		switch (data.details.next_action){
     			case "item_not_available":
     			case "item_info_not_available":     
     			  $(".remaining_stock").addClass("out_of_stock");			  
     			  $(".remaining_stock").html( data.msg );
     			  
     			  $(".inv_qty_minus").attr("disabled", true);
		          $(".inv_qty_plus").attr("disabled",true);
		          $(".add_to_cart").attr("disabled",true);
		          $("#qty").val(1);
     			break;
     		}
     	}
    });
    
    $ajax_request[$timenow].always(function() {    	
        dump("ajax always");                
        $ajax_request[$timenow]=null;         
    });
          
    $ajax_request[$timenow].fail(function( jqXHR, textStatus ) {    	    	
        dump("failed "+ textStatus);
    }); 
	
};
/*end ajax*/

jQuery(document).ready(function() {
	"use strict";	
	$( document ).on( "click", ".price_cls", function() {
		inventoryGetStocks();
	});
		
	$( document ).on( "click", ".inv_qty_plus", function() {					
		if ((typeof $(this).attr("disabled") !== "undefined") && ($(this).attr("disabled") !== null)) {			
			return;
		}
		var $qty = parseFloat( $("#qty").val())+1;	
		InvSetQuantity( $qty )	;
	});
	
	$( document ).on( "click", ".inv_qty_minus", function() {
		if ((typeof $(this).attr("disabled") !== "undefined") && ($(this).attr("disabled") !== null)) {			
			return;
		}
		var $qty = parseFloat( $("#qty").val())-1;				
		InvSetQuantity( $qty )	;	
	});
	
	if ( $("#mobile-view-food-item").inv_exists() ){
		 inventoryGetStocks();
	}
	
});
/*end docu*/

var InvSetQuantity = function($qty){
	"use strict";
	if($inventory_allow_negative_order){		
		if($qty<=1){
		   $qty = 1;
		}
		$("#qty").val($qty);
		return;
	}
	
	if($available_stocks<=0){			
		$(".inv_qty_minus").attr("disabled", true);
		$(".inv_qty_plus").attr("disabled",true);
		$(".add_to_cart").attr("disabled",true);
		return;
	}
	
	if($qty<=1){
		$qty = 1;
		$(".inv_qty_minus").attr("disabled", true);
	} else {
		$(".inv_qty_minus").attr("disabled", false);
	}
	
	if($qty>=$available_stocks){		
		$("#qty").val($available_stocks);
		$(".inv_qty_plus").attr("disabled",true);
		return;
	} else {
		$(".inv_qty_plus").attr("disabled",false);
	}
	
	$("#qty").val($qty);
};
