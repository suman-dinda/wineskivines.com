(function($) {
   "use strict";
   
	var translator;
	var data_tables;
	var ajax_request = {};
	var timer = {};
	var validator;
	var timenow;
	var item_row;
	
	jQuery.fn.exists = function(){return this.length>0;}
	
	
	var dump = function(data) {
		console.debug(data);
	};
	
	var dump2 = function(data) {
		alert(JSON.stringify(data));	
	};
	
	$( document ).on( "keyup", ".numeric_only", function() {
	  this.value = this.value.replace(/[^0-9\.]/g,'');
	});	 
	
	var loader = function(is_loading){
		if(is_loading==1){				
			$(".inline_loader").html( translator.get("loading")+"..." );
		} else {		
			$(".inline_loader").html( '' );
		}
	};
	
	var empty = function(data){	
		if (typeof data === "undefined" || data==null || data=="" || data=="null" || data=="undefined" ) {	
			return true;
		}
		return false;
	};
	
	var t = function(words){
		return translator.get(words);
	};
	
	var addCSRF = function(){
		return "&YII_CSRF_TOKEN="+ YII_CSRF_TOKEN;
	};
	
	var notify = function(message, alert_type ){
		
		if(empty(alert_type)){
			alert_type='success';
		}
		
		var notify_icon = '';
		
		switch(alert_type)
		{
			case "success":
			notify_icon = 'fa fa-check-circle';
			break;
			
			case "danger":
			notify_icon = 'fa fa-bell-o';
			break;
		}
		
		$.notify({		
			icon: notify_icon ,
			message: message,		
		},{
			type: alert_type ,		
			placement: {
			  from: "top",
			  align: "right"
		    },
		    animate:{
				enter: "animated fadeInUp",
				exit: "animated fadeOutDown"
			},
			delay : 1000*notify_delay,
			showProgressbar : true,		
			z_index: 9999999,
		});
	};
	
	var jAlert = function(error_title, message){
		if(empty(error_title)){
			error_title='';
		}
		if(empty(message)){
			message='';
		}
		$.alert({
	      title: error_title,
	      content: message ,
	      type: 'orange',
	      animation: 'opacity',             
	     animateFromElement: false,
	  });
	}
	
	jQuery(document).ready(function() {
		
		dict = JSON.parse(dict);	
		
		translator = $('body').translate({lang: lang , t: dict}); 	
		
		if ( $(".install_list_tables").exists() ){		
			runCreateTable();
		}
		
		if ( $(".install_list_item").exists() ){		
			runMigrateData();
		}
		
	});
	/*end docu*/
	
	
	var runCreateTable = function(){
	   processAjax(ajax_action, "counter=" + $("#counter").val() + "&total_table="+ total_table  + addCSRF() ) ;
	};
	
	var runMigrateData = function(){
	   processAjax(ajax_action, "counter=" + $("#counter").val() + "&total_table="+ total_table  + addCSRF() ) ;
	};
	
	var getTimeNow = function(){
		var d = new Date();
	    var n = d.getTime(); 
	    return n;
	};	
	
	/*MYCALL*/
	var processAjax = function(action , data , single_call){
			
	    timenow = getTimeNow();
		if(!empty(single_call)){
			var timenow = 1;
		}	
		
		ajax_request[timenow] = $.ajax({
		  url: ajaxurl+"/"+action,
		  method: "POST",
		  data: data ,
		  dataType: "json",
		  timeout: 20000,
		  crossDomain: true,
		  beforeSend: function( xhr ) {   
		  	 loader(1);        
	         if(ajax_request[timenow] != null) {	
	         	dump("request aborted");     
	         	ajax_request[timenow].abort();
	            clearTimeout( timer[timenow] );
	         } else {         	
	         	timer[timenow] = setTimeout(function() {				
					ajax_request[timenow].abort();
					notify( t('Request taking lot of time. Please try again') );
		        }, 20000); 
	         }
	      }
	    });
	    
	    ajax_request[timenow].done(function( data ) {
	     	dump('done');
	     	dump(data);
	     	if ( data.code==1){     		     		
	     		switch(data.details.next_action){
	     			case "next_table":
	     			  $("#counter").val( data.details.counter );
	     			  $(".counter").html( data.details.counter );
	     			  
	     			  $(".install_list_tables").append('<li class="list-group-item">'+ data.details.message +'</li>');
	     			  
	     			  setTimeout(function() {
	     			     runCreateTable();
	     			  }, 900);      			  
	     			  
	     			break;
	     			
	     			case "next_item":
	     			  $("#counter").val( data.details.counter );
	     			  $(".counter").html( data.details.counter );
	     			  
	     			  $(".install_list_item").append('<li class="list-group-item">'+ data.details.message +'</li>');
	     			  
	     			  setTimeout(function() {
	     			     runMigrateData();
	     			  }, 900); 
	     			break;
	     			     			
	     			case "done":     		
	     			  $(".results").html('<p class="text-success">'+ data.details.message +'</p>')
	     			break;
	     			
	     			case "failed":
	     			  $(".install_list_tables").append('<li class="list-group-item">'+ data.details.message +'</li>');
	     			break;
	     			
	     			default:
	     			 notify( data.msg,'success' );
	     			break;
	     		}
	     	} else{
	     		error_type=''; error_title='';
	     		if(!empty(data.details)){
		     		if(!empty(data.details.error_type)){
		     		   error_type = data.details.error_type;
		     		}
		     		if(!empty(data.details.error_title)){
		     		   error_title = data.details.error_title;
		     		}
	     		}
	     		switch(error_type){
	     			case "alert":
	     			  $.alert({
					    title: error_title,
					    content: data.msg ,
					    type: 'orange',
					    animation: 'opacity',             
	                    animateFromElement: false,
					  });
	     			break;
	     			
	     			case "silent":
	     			  //silent
	     			break;
	     			
	     			default:
	     			notify( data.msg,'danger' );
	     		}     		
	     	}
	    });
	    
	      
	     /*ALWAYS*/
	    ajax_request[timenow].always(function() {
	    	loader(2);
	        dump("ajax always");
	        ajax_request[timenow]=null;  
	        clearTimeout(timer[timenow]);
	    });
	    
	    /*FAIL*/
	    ajax_request[timenow].fail(function( jqXHR, textStatus ) {    	
	    	clearTimeout(timer[timenow]);
	        notify( t("Failed") + ": " + textStatus ,'danger' );        
	    }); 
	        
	};
	
	/*END MYCALL*/
	
})(jQuery);