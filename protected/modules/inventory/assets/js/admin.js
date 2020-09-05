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
		if ((typeof inline_loader !== "undefined") && (inline_loader !== null)) {
			if(is_loading==1){
				$(".inline_loader").html( translator.get("loading")+"..." );
			} else {
				$(".inline_loader").html( '' );
			}
		} else {
			if(is_loading==1){		
				$(".content_wrap,.main_login_wraper").loading({
					message : translator.get("loading"),
					zIndex: 999999999,
				});
			} else {
				$(".content_wrap,.main_login_wraper").loading('stop');
			}
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
	
	Dropzone.autoDiscover = false;
	
	jQuery(document).ready(function() {
		
		dict = JSON.parse(dict);	
		
		translator = $('body').translate({lang: lang , t: dict}); 	
		
		$('.menu_nav .webpop').webuiPopover({
			trigger:'hover',
			placement:'right',
			animation :"pop"
		});	
		
		$( document ).on( "click", ".menu_nav li a", function() {
			$(".menu_nav ul").hide();
			current = $(this).parent().find("ul");		
			current.toggle();			
		});
		
		$(document).on('click', function (e) {
		    if ($(e.target).closest(".sidebar_wrap,.menu_nav").length === 0) {
		        $(".menu_nav ul").hide();	        	   	        
		    } 
		    if ($(e.target).closest(".bar_menu").length === 0) {
		    	if ($(e.target).closest(".menu_nav").length === 0) {
		    	   $(".sidebar_wrap").removeClass("nav_open");	    	   
		        }
		    }
		    
		    if ($(e.target).closest(".inline_edit").length === 0) {
		    	$(".floating_inline").hide();	    	
		    }
		});
		
		$("li.active").parents('li').addClass("active");
		
		if(!empty(controller)){		
			$("."+controller).parent().addClass("active");
		}
		
		validator = $("#frm_ajax").validate({
	   	    submitHandler: function(form) {
	   	    	 var extra_data = '';
	   	    	 if ((typeof row_id !== "undefined") && (row_id !== null)) {
	   	    	 	extra_data = "&row_id="+ row_id ;
	   	    	 }
	   	    	 if ((typeof user_type !== "undefined") && (user_type !== null)) {
	   	    	 	extra_data+= "&user_type="+ user_type ;
	   	    	 }   	    	 
	   	    	 processAjax( ajax_action , $("#frm_ajax").serialize() + extra_data , 1);
			},
	   	});
		   	   	
	   	    
	   	if ( $(".data_tables").exists() ){
			init_table();
		}
			
		$( document ).on( "click", ".data_table_refresh", function(e) {
			refresh_table();
		});
		
		
		if( $(".sidebar").exists() ){
		  $(".sidebar.right").sidebar({side: "right"});
		  	  	  
		  $(".sidebar.right").on("sidebar:opened", function () {	   	 
		  });
		  
		  $( document ).on( "click", ".siderbar_nav a", function(e) {
		  	 $(".sidebar.right").trigger("sidebar:toggle");
		  });
		}
		
		$( document ).on( "click", ".data_tables tbody tr", function(e) {
			var row = $(this); var  link = row.attr("id");
			
			if ((typeof table_do !== "undefined") && (table_do !== null)) {
				switch(table_do){
					case "receipt":				  				  
					  $(".sidebar.right").trigger("sidebar:open");			
					  $(".sidebars_content").html('');
					  processAjax("Ajaxreports/receipt" , "order_id="+ link + addCSRF() , 1);
					break;
				}
				return;
			}
			
			var tick_checkboxes = $(e.target).closest(".checkboxes").length;
			var tick_size = $(e.target).closest(".item_show_sizes").length;
			var inline_edit = $(e.target).closest(".inline_edit").length;
			var floating_inline = $(e.target).closest(".floating_inline").length;
			if(tick_checkboxes==0 && tick_size==0 && inline_edit==0 && floating_inline==0){
				 if(!empty(link)){
		            window.location.href=link;		
				 }
			}		
		});
			
		
		
		$('#select_all').on('click', function(){      
	        var rows = data_tables.rows({ 'search': 'applied' }).nodes();      
	        $('input[type="checkbox"]', rows).prop('checked', this.checked);
	        deleteCheckbox();
	    });
	        
	    $( document ).on( "click", ".checkboxes", function() {
	    	deleteCheckbox();
		});
			
		$( document ).on( "click", ".data_tables_delete", function() {    
	    	$.confirm({
	    		theme: 'material',
	    		 animation: 'opacity',             
	             animateFromElement: false,
			    title: t('Please confirm your action'),
			    content: t('Are you sure you want to permanently delete the selected item?'),
			    buttons: {
					confirm:{
						text: t('Confirm'), 
						action: function () {
							processAjax( ajax_delete , $("#frm_table").serialize(), 1 );
						}
					},
					cancel: {
						text: t('Cancel'),
						action: function () {
							//
						}
					}
				}				   
		   });
		});
			
		$( document ).on( "click", ".delete_record", function() {    
	    	$.confirm({
	    		theme: 'material',
	    		 animation: 'opacity',             
	             animateFromElement: false,
			    title: t('Please confirm your action'),
			    content: t('Are you sure you want to permanently delete the selected item?'),
			    buttons: {
					confirm:{
						text: t('Confirm'), 
						action: function () {
							processAjax( ajax_delete , "row_id[]="+ row_id + "&next_action=redirect" + addCSRF(), 1 );
						}
					},
					cancel: {
						text: t('Cancel'),
						action: function () {
							//
						}
					}
				}		  		   
		   });
		});
		
						
		if( $(".date_picker").exists() ){
			$('.date_picker').datetimepicker({
				mouseWheel: false,
				timepicker:false,
				format: "M d, Y",
				onSelectDate:function(dp,$input){
		        	var date_formated=dp.format("YYYY-MM-DD");	        	
		        	parent = $input.parent().parent();
		        	parent.find(".datepicker").val(date_formated);
	            }      
			});		
		}
			
			
		if( $(".date_range").exists() ){		
			
			var start_date = moment().subtract(range_day, 'days');
	        var end_end = moment();
			
			$('.date_range').daterangepicker({
				startDate: start_date,
	            endDate: end_end,
				locale: {
				  format: 'MMM DD, Y'
				}
			}, function(start, end, label){ 
				$(".range1").val( start.format('YYYY-MM-DD') );
				$(".range2").val( end.format('YYYY-MM-DD') );
			});
		}
				
			    	     	   	   
	   	if ( $(".select2").exists() ){
	   		 $('.select2').select2({
	   		 	 placeholder: t('Select an option')
	   		 });
	   	}
	   	   	
	   	$( document ).on( "click", ".bar_menu", function() {
	   		$(".sidebar_wrap").toggleClass("nav_open")
	   	});
	   	   	
	   	$( document ).on( "click", ".inventory_enabled", function() {
			var $mtid = $(this).val(); 
			var $enabled = $(this).is(':checked'); 
		    $enabled = $enabled==true?1:0;
		    processAjax("Ajaxadmin/Allowaccess" , "merchant_id="+ $mtid + "&enabled="+ $enabled + addCSRF() , 1);		
		});	
		
		 $( document ).on( "click", ".parent_access", function() {
	   		var $parent = $(this).parent().parent().parent();    		
	   		$parent.find(".child_access").prop('checked', this.checked );   		   		
	   	});
	   	
	   	$( document ).on( "click", ".init_field_search, .close_search", function() {
	   		$(".init_field_search").toggleClass("hide");
	   		$(".search_wrap").toggleClass( "resize" );
	   		if ( $(".search_wrap").hasClass("resize") ){
	   		   $(".search_field").focus();   		   		   
	   		} else {
	   		  $(".search_field").val('');
			  data_tables.destroy();  data_tables.clear(); init_table();    	  
	   		}
	   	});   	   
		   	
	   	 $("#frm_table_filter").validate({
	   	    submitHandler: function(form) {
	   	    	 data_tables.destroy();  data_tables.clear(); init_table();
	   	    	 if ( $(".chart").exists() ){   	    	 	 
	   	    	 	 clearChart(); setTimeout(requestChart, 100);
	   	    	 }
			},
	   	});
	   	
		if( $("#inventory_show_stock").exists()){
			var ischeck = $("#inventory_show_stock").is(':checked'); 
			if(ischeck){
				$(".show_item_stock_wrap").show();
			} else {
				$(".show_item_stock_wrap").hide();
			}
			
			$( document ).on( "change", "#inventory_show_stock", function() {
				var $ischeck = $(this).is(':checked'); 
				if($ischeck){
					$(".show_item_stock_wrap").show();
				} else {
					$(".show_item_stock_wrap").hide();
				}
			});
		}
		
		
		/*dashboard*/
		if( $(".data_tables_stock_alert").exists() ){
			processAjax("Ajaxadmin/ItemStockAlert" , addCSRF() );		
		}
		if( $(".data_tables_sales_monthly").exists() ){
			processAjax("Ajaxadmin/Saleslast30days" , addCSRF() );		
		}
	
		setTimeout(function(){ 	
		   processAjax("Ajaxadmin/CheckData" , addCSRF() );
		}, 200);     			
		
		if ( $(".update_data_list").exists() ){		
			runMigrateData();
		}
		
		if ( $(".update_table_list").exists() ){		
			runCreateTable();
		}
		
		$( document ).on( "click", ".inline_edit", function(e) {		
			var close_inline = $(e.target).closest(".close_inline").length;
			if(close_inline==0){		  
			   $(this).find(".floating_inline").show();
			   $(this).find(".inline_value").select();
			}
		});
		
		$( document ).on( "click", ".close_inline", function(e) {											
			$parent = $(this).parent().parent().parent().parent().parent().parent();
		    $parent.hide();		
		});	
			
		$( document ).on( "click", ".check_inline", function(e) {		
			var $inline_value = $(this).parent().parent().parent().parent().find(".inline_value").val();
			var $inline_id = $(this).parent().parent().parent().parent().find(".inline_id").val();
			var $inline_action = $(this).data("action");
			processAjax("Ajaxadmin/"+$inline_action , "inline_value="+$inline_value + "&inline_id="+ $inline_id  + addCSRF() , 1);
		});		
		
		$( document ).on( "click", ".show_filter", function() {
			$(".action_top_filter").toggle();		
		});
		
		if ( $(".fixed_report").exists() ){		
			runFixedreport();
		}	
		
	});
	/*END DOCU*/
	
	
	var runMigrateData = function(){
	   processAjax(ajax_action, "counter=" + $("#counter").val() + "&total_item="+ total_item  + addCSRF() ) ;
	};
	
	var runCreateTable = function(){
	   processAjax(ajax_action, "counter=" + $("#counter").val() + "&total_table="+ total_table  + addCSRF() ) ;
	};
	
	var runFixedreport = function(){
	   processAjax( "Ajaxfixedreport/index", addCSRF() ) ;
	};
	
	var deleteCheckbox = function(){	
		var checkbox_count = $('input[name="row_id[]"]:checked').length;
		if(checkbox_count>0){
		  $(".data_tables_delete").show();
		} else {
	      $(".data_tables_delete").hide();
		}
		
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
	     			case "redirect":     		
	     			    notify( data.msg,'success' );	  
		     			setTimeout(function(){ 
						 	 window.location.href=data.details.redirect;
						 }, 1000);     			
	     			break;
	     			
	     			case "refresh_table":
	     			   refresh_table();
	     			break;
	     			
	     			case "silent":
	     			 // do nothing
	     			break;
	     			
	     			case "stock_alert":
	     			    var html=''; var $class_stocks = '';
	    			    $.each(data.details.data, function(key, val){      
	    			     var $in_stocks = parseFloat(val.value);    			     
	    			     if($in_stocks<=0){
	    			     	$class_stocks = 'text-danger';
	    			     } else {
	    			     	$class_stocks = 'text-success';
	    			     }
	    			  	 html+='<tr>';
	    			  	   html+='<td width="8%"><span class="circle rounded-circle" style="background:'+ val.color +';" ></span></td>';
	    			  	   html+='<td>'+ val.item_name +'</td>';
	    			  	   html+='<td>'+ val.merchant +'</td>';    			  	   
	    			  	   html+='<td class="col-qty '+$class_stocks+' ">'+ val.value +'</td>';
	    			  	 html+='</tr>';        			  	         			     
	    			    });
	    			    $(".data_tables_stock_alert tbody").html( html );
	     			break;
	     			
	     			case "sales_last30":
	     			  html=''; 
	     			  $.each(data.details.data, function(key, val){      
		     			  html+='<tr>';			  	    
					  	    html+='<td>'+ val.sale_date +'</td>';
					  	    html+='<td class="col-qty">'+ val.net_sale +'</td>';
					  	   html+='</tr>';        			  	         			     
				      });
	    			  $(".data_tables_sales_monthly tbody").html( html );
	     			break;
	     			
	     			case "update_notification":      			  
	     			  if(data.details.count>0){
	     			  	 $(".drop_notification_badge").html( data.details.count );
		     			 $(".drop_notification_list a").remove();
		     			 $.each(data.details.link, function(linkkey, linkval){
		     			  	 $(".drop_notification_list").append(linkval);
		     			 });	     			  	
	     			  }
	     			break;
	     			
	     			
	     			case "next_item":
	     			  $("#counter").val( data.details.counter );
	     			  $(".counter").html( data.details.counter );
	     			  
	     			  $(".update_data_list").append('<li class="list-group-item">'+ data.details.message +'</li>');
	     			  
	     			  setTimeout(function() {
	     			     runMigrateData();
	     			  }, 900); 
	     			break;
	     			
	     			case "done":     		
	     			  $(".results").html('<p class="text-success">'+ data.details.message +'</p>'); 
	     			  $(".drop_notification_badge").html( '');
	     			  $(".drop_notification_list a").remove();			  
	     			break;
	     			
	     			case "next_table":
	     			  $("#counter").val( data.details.counter );
	     			  $(".counter").html( data.details.counter );
	     			  
	     			  $(".update_table_list").append('<li class="list-group-item">'+ data.details.message +'</li>');
	     			  
	     			  setTimeout(function() {
	     			     runCreateTable();
	     			  }, 900);      			  
	     			  
	     			break;
	     			
	     			case "fixed_report":
	     			  if ( data.details.is_continue==1){
	     			  	  $(".fixed_report").append(data.details.message);
	     			  	  setTimeout(function() {
	     			        runFixedreport();
	     			      }, 900); 
	     			  } else {
	     			  	  $(".fixed_report").after(data.details.message);
	     			  }     			  
	     			break;
	     			     			     			
	     			default:
	     			 notify( data.msg,'success' );
	     			break;
	     		}
	     	} else{
	     		var error_type=''; var error_title='';
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
	
	
	var scrollTo = function(element){
			
		$('html, body').animate({
	        scrollTop: $(element).offset().top
	    }, 2000);	
	};
	
	var initPopOver = function(){
		setTimeout(function(){ 
		 	$('.pop_over').webuiPopover({
				trigger:'hover',
				placement:'top'
			});	
		}, 500);	
	}
	
	/*DATA TABLES*/
	var init_table = function(){
		$.fn.dataTable.ext.errMode = 'none';
		
		var extra_data = {
			'YII_CSRF_TOKEN': YII_CSRF_TOKEN
		};
		
		if ( $("#frm_table_filter").exists()){
			var filter_array =  $("#frm_table_filter").serializeArray();		
			$.each(filter_array, function(key, val){
				extra_data[val.name]=val.value;
			});
		}
			
		var table_sort_by = "DESC";
		if ((typeof data_sort_by !== "undefined") && (data_sort_by !== null)) {
			table_sort_by = data_sort_by;
		} 			
			
		data_tables = $('.data_tables').on('preXhr.dt', function ( e, settings, data ) {
	        dump('loading');        
	        $(".refresh_datatables").html( t("Loading...") + '&nbsp;<ion-icon name="refresh"></ion-icon>' );
	     }).on('xhr.dt', function ( e, settings, json, xhr ) {
	     	
	     	dump('table done');     	
	     	$(".refresh_datatables").html( t("Refresh") + '&nbsp;<ion-icon name="refresh"></ion-icon>' );
	     	$(".dataTables_processing").hide();
	     	     	     	
	     }).on( 'error.dt', function ( e, settings, techNote, message ) {
	     	notify( t(error_ajax_message) + ": " + message,'danger' );
	     }).DataTable( {
	     	"aaSorting": [[ 0, table_sort_by ]],	
	        "processing": true,
	        "serverSide": true,
	        "bFilter":false,
	        //"bSort":false,
	        //"dom": 'trip',
	        "dom": "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>" +
	                "<'row'<'col-sm-12'tr>>" +
	               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
	               
	        "pageLength": page_length,        
	         "ajax": {
			    "url": ajaxurl+"/"+ajax_action,
			    "type": "POST",
			    "data": extra_data 
			},
			'columns' : data_columns ,
			'columnDefs': [{
	         'targets': 'col-checkbox',
	         'searchable': false,
	         'orderable': false,
	         'className': 'dt-body-center',
	         'render': function (data, type, full, meta){         	      
	             return '<input type="checkbox" class="checkboxes" name="row_id[]" value="' + $('<div/>').text(data).html() + '">'; 
	         }
	      },{
	      	 'targets'  : 'no-sort',
	      	 'orderable': false,
	      }],
	        language: {
		        url: ajaxurl+"/Datablelocalize"
		    }
	     });
	};
	
	var refresh_table = function(){
		$('.data_tables').DataTable().ajax.reload();
		$(".data_tables_delete").hide();
	};
	
	
	/*END DATA TABLES*/
	
	var inArray = function(value,data){
		var result = false;
		dump("value=>"+value);dump(data);	
		if(data.length>0){
			$.each(data, function(key, val){			
				if (val.sku==value){				
					result = true;
					return false; 
				}
			});
		}
		return result;
	};
	
	var removeArray = function(value,data){	
		dump("removeArray=>"+value);dump(data);	
		if(data.length>0){
			var i =data.length-1;		
			for(i ; i>= 0;i--){
				if(data[i].sku == value){
					data.splice( i, 1 );
				}
			}
		}
	};
	
	var prettyPrice = function(price){
		price = number_format(price, 2, '.' , '') ;
		return price;
	};
	
	var prettyQty = function(price){
		price = number_format(price, 0, '' , '') ;
		return price;
	};
	
	function number_format(number, decimals, dec_point, thousands_sep) 
	{
	  number = (number + '')
	    .replace(/[^0-9+\-Ee.]/g, '');
	  var n = !isFinite(+number) ? 0 : +number,
	    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	    s = '',
	    toFixedFix = function(n, prec) {
	      var k = Math.pow(10, prec);
	      return '' + (Math.round(n * k) / k)
	        .toFixed(prec);
	    };
	  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
	    .split('.');
	  if (s[0].length > 3) {
	    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	  }
	  if ((s[1] || '')
	    .length < prec) {
	    s[1] = s[1] || '';
	    s[1] += new Array(prec - s[1].length + 1)
	      .join('0');
	  }
	  return s.join(dec);
	}
	

    /*CHART CODE STARTS HERE*/	
    var chart;
	var chart_ajax;
	var chart_timer;
	
	
	jQuery(document).ready(function() {		
	
		if ( $("#main_chart").exists() ){
			chart = Highcharts.chart('main_chart', {
			    chart: {
			        type: chart_type_series,
			        events: {
			            load: requestChart()
			        }
			    },
			    title: {
			        text: '',
			        align: 'left',
			        x: 20
			    },
			   subtitle: {
			   	  useHTML : true,
			      text: chart_type_options,
			      align: 'right',
			      y: -5,
			      x: -80
			   },
			   xAxis: {        
			        categories: [],
			        tickmarkPlacement: 'on',
			        title: {
			            enabled: false
			        }
			    },
			    yAxis: {
			        title: {
			            text: ''
			        },        
			    },
			    tooltip: {
			      formatter: function() {
			      	if(this.series.name=="pie_chart"){
			      	   return this.key + "<br/><b>"+  prettyPrice(this.y) +"</b>";
			      	} else {
			           return this.x + '<br/>' + this.series.name + "<br/><b>"+  prettyPrice(this.y) +"</b>";
			      	}
			      }
			    },    
			    plotOptions: {
			     	pie: {
			            allowPointSelect: true,
			            cursor: 'pointer',
			            dataLabels: {
			                enabled: false,
			                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
			            }
			        }
			    },
			    series: [{
			        name: '',
			        showInLegend : false,
			        data: []
			    }]
			});        
		}
	
	});
	/*end docu*/
	
	function requestChart() {
		
		var extra_data = {
			'YII_CSRF_TOKEN': YII_CSRF_TOKEN,
			'chart_type': chart_type_series
		};
		
		if ( $("#frm_table_filter").exists()){
			var filter_array =  $("#frm_table_filter").serializeArray();		
			$.each(filter_array, function(key, val){
				extra_data[val.name]=val.value;
			});
		}
		
		var data = extra_data;
		
	    chart_ajax = $.ajax({
	    	method: "POST",
		    data: data ,
		    dataType: "json",
		    timeout: 20000,
		    crossDomain: true,
	        url: ajaxurl+ ajax_charts ,
	        beforeSend: function( xhr ) {   
	        	 loader(1);
		         if(chart_ajax != null) {	
		         	dump("request aborted");     
		         	chart_ajax.abort();
		            clearTimeout( chart_timer );
		         } else {         	
		         	chart_timer = setTimeout(function() {				
						chart_ajax.abort();
						notify( t('Request taking lot of time. Please try again') );
			        }, 20000); 
		         }
	       },
	        success: function(data) {        	
	        	if(data.code==1){
	
	        		chart.setTitle({ text: data.details.chart_title });        		
	        		var series = chart.series[0]; var shift = series.data.length > 20;
	        		
	        		var categories = [];
		        	$.each(data.details.categories, function(key, val){
		        		categories.push(val);
		        	});        	
		        	chart.xAxis[0].setCategories(categories);
	        		        			        	
	        		switch(data.details.next_action){
	        			case "sales_summary_chart":
	        			   $.each(data.details.summary, function(summary_key, summary_val){
		        			  $("."+summary_key+"_value").html(summary_val);
		        		   });
		        		   
		        		   chart.addSeries({
			        			type: chart_type_series ,
				            	data : data.details.series,
				            	name: '',
				                showInLegend : false,
				                color: '#9CCC65',
				                tooltip: {
								    valueDecimals: 2 ,
								},
				            }); 
	        			break;
	        			
	        			case "chart_sales_item":
	        			
	        			  if(chart_type_series=="pie"){        			  	 
	        			  	  chart.addSeries({
			        			type: chart_type_series ,
				            	data : data.details.series ,
				            	name: "pie_chart" ,
				                showInLegend : false,			                	               
				              }); 
	        			  } else {
		        			  $.each(data.details.series, function(series_key, series_val){        	        			  	  
		        			  	  chart.addSeries({
				        			type: chart_type_series ,
					            	data : series_val.data,
					            	name: series_key ,
					                showInLegend : false,
					                color: series_val.color,			                
					              }); 
		        			  });
	        			  }
	        			          			 
	        			break;
	        		}        		        		        	      		
	        		        		        		          	
		        		        		        	        		
	        	} else {
	        		notify(data.msg,"danger");
	        	}
	        },
	        cache: false
	    });
	    
	    /*ALWAYS*/
	    chart_ajax.always(function() {    	
	    	loader(2);
	        dump("ajax always");
	        chart_ajax = null;  
	        clearTimeout(chart_timer);
	    });
	    
	    /*FAIL*/
	    chart_ajax.fail(function( jqXHR, textStatus ) {    	
	    	clearTimeout(chart_timer);
	        notify( t("Failed") + ": " + textStatus ,'danger' );        
	    }); 
	}
	
	var clearChart  = function(){	
		for (var i = 0; i < chart.series.length; i++) {
	       chart.series[0].remove();
	    }    
	    for (var i = 0; i < chart.series.length; i++) {
	       chart.series[0].remove();
	    }    
	    chart.addSeries({
	       name: '',
	       showInLegend : false,
	       data: []
	   });
	};
	
	jQuery(document).ready(function() {	
		$( document ).on( "click", ".summary_nav a, .drop_summary_nav a", function(e) {
			$('.summary_nav a').removeClass('active');
			var a = $(this).addClass("active");		
			$("#chart_type").val( $(this).data("id") );
			
			clearChart(); setTimeout(requestChart, 100);
		});
			
		$( document ).on( "click", ".chart_type_options", function(e) {
			 $("#btn_chart_type").html( $(this).data("label") );		
			 chart_type_series = $(this).data("id");	    		 
			 clearChart();
			 clearChart(); setTimeout(requestChart, 100);
		});
		
	});
	
	var randomColors = function(){
	  return "#000000".replace(/0/g,function(){return (~~(Math.random()*16)).toString(16);});
	};
	/*end docu*/
	
    /*CHART CODE ENDS HERE*/		

})(jQuery);