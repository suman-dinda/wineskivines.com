(function($) {
   "use strict";
   
	var translator;
	var data_tables;
	var ajax_request = {};
	var timer = {};
	var validator;
	var timenow;
	var item_row;
	var heart_beat;
        var transaction_type='';
	
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
			//icon: 'fa fa-check-circle',
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
			showProgressbar : false,		
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
	};
	
	Dropzone.autoDiscover = false;
	
	jQuery(document).ready(function() {
		
		dict = JSON.parse(dict);
		//dump(dict);
		
		translator = $('body').translate({lang: lang , t: dict}); 	
		
		$('.menu_nav .webpop').webuiPopover({
			trigger:'hover',
			placement:'right',
			animation :"pop"
		});	
		
		$( document ).on( "click", ".menu_nav li a", function() {
			$(".menu_nav ul").hide();
			var current = $(this).parent().find("ul");		
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
		   	   	
	   	/*DROPZONE INIT*/   	
	    singleDropZone();
	    multippleDropZone();
	   	
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
			var row = $(this); var link = row.attr("id");
			
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
			
		$( document ).on( "click", ".inline_edit", function(e) {		
			var close_inline = $(e.target).closest(".close_inline").length;
			if(close_inline==0){		  
			   $(this).find(".floating_inline").show();
			   $(this).find(".inline_value").select();
			}
		});
		
		$( document ).on( "click", ".close_inline", function(e) {											
			var $parent = $(this).parent().parent().parent().parent().parent().parent();
		    $parent.hide();		
		});	
			
		$( document ).on( "click", ".check_inline", function(e) {		
			var $inline_value = $(this).parent().parent().parent().parent().find(".inline_value").val();
			var $inline_id = $(this).parent().parent().parent().parent().find(".inline_id").val();
			var $inline_action = $(this).data("action");
			processAjax("Ajaxitem/"+$inline_action , "inline_value="+$inline_value + "&inline_id="+ $inline_id  + addCSRF() , 1);
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
		
		$( document ).on( "click", ".size_add_new_row", function() {		
			
			var with_size = $(".with_size").is(':checked'); 
			with_size = with_size==true?1:0;
			var params_size  = "with_size="+ with_size + addCSRF();
			if ((typeof row_id !== "undefined") && (row_id !== null)) {
			 	params_size+= "&row_id="+ row_id ;
			}			
					 
			processAjax("Ajaxitem/LoadWithSizeForm", params_size );
		});
			
		$( document ).on( "click", ".size_delete", function() {
			var item_token = $(this).data("id");		
			if(!empty(item_token)){
				$.confirm({
	    		 theme: 'material',
	    		 animation: 'opacity',             
	             animateFromElement: false,
			     title: t('Modify Item Size'),
			     content: t('Are you sure to delete this row?'),
			     buttons: {
					confirm:{
						text: t('Confirm'), 
						action: function () {
							processAjax("Ajaxitem/DeleteItemSize", "item_token="+ item_token + addCSRF() ,1 );
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
			} else {
			    $(this).parent().parent().remove();
			}
		});	
			
		$( document ).on( "click", ".add_addon_category", function() {
			var subcat_id = $("#addon_category_list option:selected").val();
			if(subcat_id>0){
			   var row_count = $(".addon_list .addon_row_"+subcat_id ).length;
			   if(row_count<=0){
			      processAjax("Ajaxitem/AddAddonRow","subcat_id="+ subcat_id + addCSRF() , 1);
			   } else {
			   	  jAlert( t("Addon"), t("Addon Category already exist") );
			   }		   
			} else {
				jAlert( t("Addon"),t("Please select addon category"));
			}
		});	
		
		$( document ).on( "click", ".check_all", function() {
			var addon_id = $(this).val();		
			if($(this).is(':checked')){			
				$(".check_all_"+addon_id).prop( "checked", true );
			} else {
				$(".check_all_"+addon_id).prop( "checked", false );
			}
		});
			
		$( document ).on( "click", ".remove_addon_subcategory", function() {
			var addon_row_id = $(this).data("id");
			$.confirm({
	    		theme: 'material',
	    		 animation: 'opacity',             
	             animateFromElement: false,
			    title: t('Please confirm your action'),
			    content: t('Are you sure?'),
			    buttons: {
					confirm:{
						text: t('Confirm'), 
						action: function () {
							$(".addon_row_"+ addon_row_id).remove();
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
			
		$( document ).on( "change", ".multi_option", function() {		
			parent = $(this).parent().parent();
			var multi_option = $(this).val();
			two_flavors = $(".two_flavors").is(':checked'); 
			
			if(!two_flavors){
				if(multi_option=="custom"){			
					parent.find(".custom_qty_div").show();			
				} else {
					parent.find(".custom_qty_div").hide();
				}
			}
		});
			
		$( document ).on( "change", ".two_flavors", function() {		
			EnabledTwoFlavor($(this).is(':checked'));
		});
		
		if ( $(".with_size").exists() ){		
			loadSizeForm();
			loadAddonItem();
		}
			
		$( document ).on( "change", ".with_size", function() {		
			loadSizeForm();		
		});		
		
		if ( $(".typhead_item").exists() ){
			autoFillItem();
		}	
		//case-implementation
		$( document ).on( "keyup", ".input_case", function(event) {
			var case_qty = parseFloat( $(this).val() )+ 0;
			var item_size = parseInt($(this).parent().parent().find("td:nth-child(1) span").text().slice(1,-1));
			transaction_type = $(".adjustment_transaction_type").val();		
			dump("transaction_type=>"+ transaction_type);
			var cal_qty=0;

			switch(transaction_type){
				case "inventory_count":			  
				  return;
				break;
				case "purchase_order":
					switch(item_size){
						case 750:
							cal_qty=case_item_qty(case_qty,12);
							$(this).parent().parent().find("td:nth-child(5) input").val(cal_qty).keyup();
							break;
						case "375":
							cal_qty=case_item_qty(case_qty,24);
							$(this).parent().parent().find("td:nth-child(5) input").val(cal_qty).keyup();
							break;
						case "180":
							cal_qty=case_item_qty(case_qty,48);
							$(this).parent().parent().find("td:nth-child(5) input").val(cal_qty).keyup();
							break;
						case "90":
							cal_qty=case_item_qty(case_qty,96);
							$(this).parent().parent().find("td:nth-child(5) input").val(cal_qty).keyup();
							break;
						case "650":
							cal_qty=case_item_qty(case_qty,12);
							$(this).parent().parent().find("td:nth-child(5) input").val(cal_qty).keyup();
							break;
						case "500":
							cal_qty=case_item_qty(case_qty,24);
							$(this).parent().parent().find("td:nth-child(5) input").val(cal_qty).keyup();
							break;
						case "330":
							cal_qty=case_item_qty(case_qty,48);
							$(this).parent().parent().find("td:nth-child(5) input").val(cal_qty).keyup();
							break;
						case "275":
							cal_qty=case_item_qty(case_qty,96);
							$(this).parent().parent().find("td:nth-child(5) input").val(cal_qty).keyup();
							break;
						default:
							notify( "Size not supported. Please register size in ML/L.","info" );
							break;
					}
				break;
			}

		});
		$( document ).on( "keyup", ".input_qty", function(event) {
			
			var qty = parseFloat( $(this).val() )+ 0;
			
			transaction_type = $(".adjustment_transaction_type").val();		
			dump("transaction_type=>"+ transaction_type);
						
			var in_stock = parseFloat( $(this).parent().parent().find("td:nth-child(2)").text() );
				
			switch(transaction_type){
				case "inventory_count":			  
				  return;
				break;
				
				case "loss":
				case "damage":
				  var stock_after_div = $(this).parent().parent().find("td:nth-child(4)") ;
				  if(!isNaN(qty)){
					stock_after = in_stock-qty;
				  } else stock_after = in_stock;
				break;
				
				case "purchase_order":
				  var stock_after_div = $(this).parent().parent().find("td:nth-child(7)") ;
				  if(!isNaN(qty)){
				  	var cost  = parseFloat( $(this).parent().parent().find(".input_cost").val() ) ;
					var stock_after = prettyPrice(qty * cost);
				  } else stock_after = 0;
				break;
				
				default:			  
				  stock_after_div = $(this).parent().parent().find("td:nth-child(5)") ;
				  if(!isNaN(qty)){
					stock_after = in_stock+qty;
				 } else stock_after = in_stock;
				break;
			}		
					
			if(stock_after<=-1){
				stock_after_div.html( '<span class="text-danger">'+stock_after+'</span>' );
			} else {
				stock_after_div.html(stock_after);
			}		
		});	
		
		$( document ).on( "keyup", ".input_cost", function(event) {
			var cost = parseFloat( $(this).val() )+ 0;
			transaction_type = $(".adjustment_transaction_type").val();		
			
			switch (transaction_type){
				case "purchase_order":
				  var qty = parseFloat( $(this).parent().parent().find(".input_qty").val() );
				  var stock_after_div = $(this).parent().parent().find("td:nth-child(6)");
				  stock_after_div.html( (qty*cost)  );
				break;
				
				case "receive_items":
				  qty = parseFloat( $(this).parent().parent().find(".input_qty").val() );
				  stock_after_div = $(this).parent().parent().find("td:nth-child(5)");
				  stock_after_div.html( (qty*cost)  );
				break;
				
			}
		});
			
		$( document ).on( "click", ".input_delete_row", function(event) {		
			var remove_sku =  $(this).data("sku").toString();
			removeArray(remove_sku, added_sku);
			
			var $parent = $(this).parent().parent();
			$parent.remove();
		});
		
		$( document ).on( "change", ".adjustment_transaction_type", function() {		
			transaction_type = $(this).val();		
			dump("transaction_type=>"+ transaction_type);
			var table_prop =  JSON.parse(table_properties);
			if(!empty(table_prop[transaction_type])){
			   dump(table_prop[transaction_type]);
			   var $th = '';
			   $.each(table_prop[transaction_type].label, function(key, label){
			   	   $th+= '<th width="'+ table_prop[transaction_type].sizes[key] +'">'+ label +'</th>';
			   });
			   $(".table_adjustment_new thead tr").html( $th );
			   
			   if(added_sku.length>0){
			   	  clearTableData($(".table_adjustment_new tbody tr"),$(".table_adjustment_new tbody"));		   	  
			   	  $.each(added_sku, function(key, val){
				   	 fillAdjustmentData( $(".table_adjustment_new tbody tr:last"), transaction_type,val);
				  });
			   }		   
			}
			
		});
			
		if( $(".date_picker").exists() ){
			
			if ((typeof datetime_lang !== "undefined") && (datetime_lang !== null)) {
			   jQuery.datetimepicker.setLocale(datetime_lang);				
			}
			
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
		
		if ((typeof purchase_data !== "undefined") && (purchase_data !== null)) {
			purchase_data = JSON.parse(purchase_data);
			if(purchase_data.length>0){
				$.each(purchase_data, function(purchase_data_key, purchase_data_val){						
					fillAdjustmentTable(purchase_data_val);
				});
			}
		}
		
		
		$( document ).on( "click", ".mark_all_receive", function(event) {
			if( $(".receive_qty").exists() ){
				$.each( $(".receive_qty") , function(key, val){
					var max = $(this).attr("max");
					$(this).val( parseInt(max) );
				});
			}
		});
		
		
		$( document ).on( "change", ".filter_change", function() {		
			data_tables.destroy();  data_tables.clear(); init_table();
		});
			
		$(".checkbox-menu").on("change", "input[type='checkbox']", function() {
		   $(this).closest("li").toggleClass("active", this.checked);
		});
	
		$(document).on('click', '.allow-focus', function (e) {
		   e.stopPropagation();	  
		});
		
		
		if( $(".date_range").exists() ){		
			
			var start_date = moment().subtract(range_day, 'days');
	        var end_end = moment();
	        
	        if ((typeof date_range_trans !== "undefined") && (date_range_trans !== null)) {        
	        	date_range_trans = JSON.parse(date_range_trans);
	        } else {
	        	date_range_trans = {
	        	   format: 'MMM DD, Y'
	        	};
	        }       
	        
	        dump(date_range_trans);
			
			$('.date_range').daterangepicker({
				startDate: start_date,
	            endDate: end_end,
	            locale: date_range_trans			
			}, function(start, end, label){ 
				$(".range1").val( start.format('YYYY-MM-DD') );
				$(".range2").val( end.format('YYYY-MM-DD') );				
			});
			
			$('.date_range').on('apply.daterangepicker', function(ev, picker) {
				$_month = t(picker.startDate.format('MMM'));
				$_day_year = picker.startDate.format(' DD, Y');			
				
				$_end_month = t(picker.endDate.format('MMM'));
				$_end_day_year = picker.endDate.format(' DD, Y');	
				
				$(".date_range").val( $_month+""+$_day_year + " - " + $_end_month+""+$_end_day_year   );
			});
		}
				
		
		 $("#frm_table_filter").validate({
	   	    submitHandler: function(form) {
	   	    	 data_tables.destroy();  data_tables.clear(); init_table();
	   	    	 if ( $(".chart").exists() ){   	    	 	 
	   	    	 	 clearChart(); setTimeout(requestChart, 100);
	   	    	 	 topItems();
	   	    	 }
			},
	   	});
	   	
	   	$( document ).on( "click", ".filter_all", function(event) {
	   		var $parent = $(this).parent().parent().parent().parent();
	   		var parent = $(this).parent().parent().parent();
	   		
	   		var $filter_label = $parent.find(".filter_label");
	   		var $filter_label_field = $filter_label.data("field");   		
	   		
	   		var filter_type = $(this).attr("type");   		
	   		
	   		if(filter_type=="radio"){
	   			$filter_label.html( t("All") + " "+ t($filter_label_field) );
	   			return;
	   		} 
	   		
	   		parent.find(".filter_data").prop('checked', this.checked);
	   		   		   		
	   		if(this.checked){   			
	   			$filter_label.html( t("All") + " "+ t($filter_label_field) );
	   		} else {
	   			$filter_label.html( 0 + " "+ t($filter_label_field) );
	   		}
	   	});
	   	
	   	$( document ).on( "click", ".filter_data", function(event) {
	   		 		 
	   		 var $parent = $(this).parent().parent().parent().parent();
	   		 var parent = $(this).parent().parent().parent();
	   		 var $filter = parent.find(".filter_data");
	   		 var filter_data_lenght = $filter.length;
	   		 dump("filter_data_lenght=>"+ filter_data_lenght);
	   		 
	   		 var $filter_check = 0;
	   		 $filter.each(function(){   		 
	   		 	if( $(this).is(':checked') ){
	   		 		$filter_check++;
	   		 	} 
	   		 });
	   		 
	   		 dump("filter_check=>"+$filter_check);   		    	   		 
	   		 var $filter_label = $parent.find(".filter_label");
	   		 var $filter_label_field = $filter_label.data("field");
	   		 
	   		 //alert(t($filter_label_field));
	   		 
	   		 if( parseInt(filter_data_lenght) == parseInt($filter_check) ){   		 	 
	   		 	parent.find(".filter_all").prop('checked', true);   		 	
	   		 	$filter_label.html( t("All") + " "+ t($filter_label_field) );
	   		 } else {   		 	
	   		 	parent.find(".filter_all").prop('checked', false);
	   		 	$filter_label.html( $filter_check + " "+ t($filter_label_field) );
	   		 }
	   		 
	   	});
	   	   	
	   	$( document ).on( "click", ".item_show_sizes", function() {
	   		var $class_name = $(this).find("i").attr("class");
	   		
	   		item_row = $(this).parent().parent();
	   		var item_id = $(this).data("id");   		   		
	   		
	   		if($class_name=="fas fa-chevron-down"){
	   			$(this).find("i").attr("class","fas fa-chevron-up");
	   			$(".tr_item_size_list").remove();
	   			setTimeout(function(){ 
				   processAjax("Ajaxitem/LoadItemSizeList", "item_id="+ item_id + addCSRF() , 1 );
				}, 200);     			   			
	   		} else {
	   			$(".tr_item_size_list").remove();
	   			$(this).find("i").attr("class","fas fa-chevron-down");   			
	   		}
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
	   	   	
	   	$( document ).on( "click", ".parent_access", function() {
	   		$parent = $(this).parent().parent().parent();    		
	   		$parent.find(".child_access").prop('checked', this.checked );   		   		
	   	});
	   	
	   	if ( $(".select2").exists() ){
	   		 $('.select2').select2({
	   		 	 placeholder: t('Select an option')
	   		 });
	   	}
	   	   	
	   	$( document ).on( "click", ".bar_menu", function() {
	   		$(".sidebar_wrap").toggleClass("nav_open");
	   	});
	   	   	
	   	$( document ).on( "change", ".supplier_id", function() {
			var supplier_id = parseInt($(this).val());
			if(supplier_id>0){
				$(".autofill_item").removeClass("disabled");
			} else {
				$(".autofill_item").addClass("disabled");
			}
		});
		
		$( document ).on( "click", ".autofill_item", function() {	
			 var $type = $(this).data("id"); 
			 processAjax("Ajaxstocks/AutoFillItem", "type="+ $type + "&supplier_id="+  $(".supplier_id").val()  + addCSRF() , 1 );
		});
		
		
		if ((typeof pop_receipt !== "undefined") && (pop_receipt !== null)) {
			 $(".sidebar.right").trigger("sidebar:open");			
			 $(".sidebars_content").html('');
			 processAjax("Ajaxreports/receipt" , "order_id="+ pop_receipt + addCSRF() , 1);
		}
		
		if( $("#inventory_show_stock").exists()){
			ischeck = $("#inventory_show_stock").is(':checked'); 
			if(ischeck){
				$(".show_item_stock_wrap").show();
			} else {
				$(".show_item_stock_wrap").hide();
			}
			
			$( document ).on( "change", "#inventory_show_stock", function() {
				$ischeck = $(this).is(':checked'); 
				if($ischeck){
					$(".show_item_stock_wrap").show();
				} else {
					$(".show_item_stock_wrap").hide();
				}
			});
		}
		
		$( document ).on( "click", ".inventory_enabled", function() {
			$mtid = $(this).val(); 
			$enabled = $(this).is(':checked'); 
		    $enabled = $enabled==true?1:0;
		    processAjax("Ajaxadmin/Allowaccess" , "merchant_id="+ $mtid + "&enabled="+ $enabled + addCSRF() , 1);		
		});	
		
		
		/*dashboard*/
		if( $(".data_tables_stock_alert").exists() ){
			processAjax("Ajaxreports/ItemStockAlert" , addCSRF() );		
		}
		if( $(".data_tables_sales_monthly").exists() ){
			processAjax("Ajaxreports/Saleslast30days" , addCSRF() );		
		}
		
				
		if ( $("#drop_notification").exists() ){		
			setTimeout(function(){ 
			   processAjax("Ajaxitem/CheckNotification" , addCSRF() , 200 , 1 );
		    }, 200);     			
		}
		
	    if ( $(".update_data_list").exists() ){		
			runMigrateData();
		}
		
		if ((typeof check_heart_beat !== "undefined") && (check_heart_beat !== null)) {
			if(check_heart_beat==1){
				heart_beat  = setInterval( checkHeartBeat , 240000);
			}
		}
				
		$( document ).on( "click", ".show_filter", function() {
			$(".action_top_filter").toggle();
		});
		
		$( document ).on( "change", ".inventory_live", function() {
			var check = $(this).is(':checked');
			processAjax("Ajaxuser/Updatestatus" , "enabled="+check +  addCSRF() , 1, 1);
		});
		
		
		if ( $(".track_stock").exists()){	
			var track_check = $(".track_stock").is(':checked'); 	
			if(track_check){
				$(".inventory_track_stock").show();
			} else {
				$(".inventory_track_stock").hide();
			}
		}
		
		$( document ).on( "change", ".track_stock", function() {
			track_check = $(this).is(':checked'); 
			if(track_check){
				$(".inventory_track_stock").show();
			} else {
				$(".inventory_track_stock").hide();
			}
		});	
		
		if ( $(".fixed_report").exists() ){		
			runFixedreport();
		}	
		
		if ( $(".data_tables_top_items").exists() ){
			topItems();
		}
					
		
	});
	/*END DOCU*/
	
	var topItems = function(){
		if ( $(".data_tables_top_items").exists() ){	
			var topitem_data = $("#frm_table_filter").serializeArray();
			processAjax("Ajaxreports/"+top_items , topitem_data , '', 1);
		}
	};
	
	var runMigrateData = function(){
	   processAjax(ajax_action, "counter=" + $("#counter").val() + "&total_item="+ total_item  + addCSRF() ) ;
	};
	
	var runFixedreport = function(){
	   processAjax( "Ajaxfixedreport/index", addCSRF() ) ;
	};
	
	var checkHeartBeat = function(){
		processAjax("Ajaxheartbeat/check", addCSRF() , 100 , 1  ) ;
	};
	
	var loadSizeForm = function(){
		var with_size = $(".with_size").is(':checked'); 
		with_size = with_size==true?1:0;
		var params_size  = "with_size="+ with_size + addCSRF();
		if ((typeof row_id !== "undefined") && (row_id !== null)) {
		 	params_size+= "&row_id="+ row_id ;
		}
		processAjax("Ajaxitem/LoadSizeForm", params_size , 1 );
	};
	
	var loadAddonItem = function(){
		var params='';
		if ((typeof row_id !== "undefined") && (row_id !== null)) {
		 	params+= "&row_id="+ row_id + addCSRF();
		 	processAjax("Ajaxitem/LoadAddonItem", params );
		}
	};
	
	var deleteCheckbox = function(){	
		var checkbox_count = $('input[name="row_id[]"]:checked').length;
		if(checkbox_count>0){
		  $(".data_tables_delete").show();
		} else {
	      $(".data_tables_delete").hide();
		}
		
	};
	
	var EnabledTwoFlavor = function(check){
		if(check){
			$(".custom_qty_div").hide();
			$(".two_flavor_div").show();
			$(".multi_option").val("one");
			$(".multi_option").attr("disabled",true);
		} else {
			$(".two_flavor_div").hide();
			$(".multi_option").attr("disabled",false);
		}
	};
	
	var EnabledItemCustomField = function(){	
		$('.multi_option').each(function(){
			var parent = $(this).parent().parent();
			var multi_option = $(this).val();
			two_flavors = $(".two_flavors").is(':checked'); 		
			if(!two_flavors){
				if(multi_option=="custom"){			
					parent.find(".custom_qty_div").show();			
				} else {
					parent.find(".custom_qty_div").hide();
				}
			}
		});
			
	};
	
	var getTimeNow = function(){
		var d = new Date();
	    var n = d.getTime(); 
	    return n;
	};	
	
//case quantity calculation

var case_item_qty = function(case_qty , case_count){
	var res = parseInt(case_qty)*parseInt(case_count);
	return res;
};

	/*MYCALL*/
	var processAjax = function(action , data , single_call, silent){
			
	    timenow = getTimeNow();
		if(!empty(single_call)){
			//var timenow = 1;
			var timenow = single_call;
		}	
			
		
		ajax_request[timenow] = $.ajax({
		  url: ajaxurl+"/"+action,
		  method: "POST",
		  data: data ,
		  dataType: "json",
		  timeout: 20000,
		  crossDomain: true,
		  beforeSend: function( xhr ) {   
		  	 if ((typeof silent !== "undefined") && (silent !== null)) {	  	    
		  	 } else {
		  	 	loader(1); 
		  	 }
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
	     			
	     			case "fill_addon_list":
	     			  $(".addon_list").append(data.details.html);
	     			  
	     			  if(!empty(data.details.subcat_id)){
	     			    scrollTo('.addon_row_'+ data.details.subcat_id);
	     			  }
	     			  
	     			  initPopOver();
	     			  setTimeout(function(){ 
	     			  	 EnabledTwoFlavor($(".two_flavors").is(':checked'));
	     			  	 EnabledItemCustomField();     			  	 
		              }, 500);	      			  
	     			break;
	     			
	     			case "load_size_form":     			  
	     			  $(".price_wrap").html( data.details.html);
	     			  if ( data.details.with_size<=0){ 
	     			  	  //$(".inventory_track_stock").show();     			  	  
	     			  	  if ( data.details.row_id>0){
		     			   	  $("#in_stock").val( prettyQty(data.details.in_stock) );
		     			  	  $("#low_stock").val( prettyQty(data.details.data.low_stock) ); 
		     			  }
		     			  $(".price_wrap").removeClass("scroll-x");	     			  	     			 
	     			  } else {
	     			  	  //$(".inventory_track_stock").hide();     			  	  
	     			  	  $(".price_wrap").addClass("scroll-x");
	     			  }     			       			       			  
	     			break;
	     			     			
	     			case "load_size_form_append":     			   
	     			   $(".size_table tbody tr:last").after(data.details.html);
	     			break;
	     			
	     			case "re_load_size_form": 
	     			   loadSizeForm();
	     			break;
	     			
	     			case "load_item_ist":          			 
	     			  item_row.after( data.details.data );
	     			break;
	     			
	     			case "show_receipt":
	     			  $(".sidebars_content").html(data.details.html);
	     			break;
	     			
	     			case "auto_fill_purchase":
	     			  $.each(data.details.data, function(key, item){
	     			  	 fillAdjustmentTable(item);
	     			  });
	     			break;
	     			
	     			case "stock_alert":
	     			    var html='';  var $class_stocks = '';
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
	     			  if(data.details.inventory_live==1){
	     			     $(".inventory_live").prop( "checked", true );
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
	     			
	     			case "sesssion_expired":     			  
	     			  window.location.href = data.details.redirect;
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
	     			
	     			case "show_top_items":
	     			   html=''; 
	        			  $.each(data.details.data, function(top_key, top_val){           		        			  		  	 	  
	        			  	 html+='<tr>';
	        			  	   html+='<td width="8%"><span class="circle rounded-circle" style="background:'+ top_val.color +';" ></span></td>';
	        			  	   html+='<td>'+ top_val.item_name +'</td>';
	        			  	   html+='<td>'+ top_val.value +'</td>';
	        			  	 html+='</tr>';        			  	         			     
	        			  });
	        		  $(".data_tables_top_items tbody").html( html );
	     			break;
	     			
	     			case "clear_top_items":  
	     			  $(".data_tables_top_items tbody").html('');
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
	};
	
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
	     	dump('done');     	
	     	$(".refresh_datatables").html( t("Refresh") + '&nbsp;<ion-icon name="refresh"></ion-icon>' );
	     	$(".dataTables_processing").hide();
	     }).on( 'error.dt', function ( e, settings, techNote, message ) {
	     	notify( t(error_ajax_message) + ": " + message,'danger' );
	     }).DataTable( {
	     	"aaSorting": [[ 0, table_sort_by ]],	
	        "processing": true,
	        "serverSide": true,
	        "bFilter":false,        
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
	
	
	var singleDropZone = function(){
		if( $("#single_dropzone").exists() ){
			$("#single_dropzone").dropzone({    	
				paramName :"uploadfile",
			    maxFilesize: file_limit,        
			    maxFiles: 1,
			    addRemoveLinks : true,
			    dictRemoveFileConfirmation : t("are you sure?"),
			    url: ajaxurl+"/upload/single",
			    success: function (file, response) {	        	
			        var resp = JSON.parse(response);
			        dump(resp);            
			        if(resp.code==1){	            	
			        	$(".file_name").remove();
			        	$("#frm_ajax").append('<input type="hidden" name="file_name" class="file_name" value="'+resp.details.file_name+'">');
			        } else {
			        	notify( resp.msg );
			        }
			    },
			    init: function() {
			    	
			    	var thisDropzone = this;
			    	if ((typeof uploaded_filename !== "undefined") && (uploaded_filename !== null)) {
			    		if(!empty(uploaded_filename)){		    			
					    	$.get( ajaxurl+"/upload/get?filename="+ uploaded_filename, function(data) {						    		 				    		 
					    		 if(data.code==1){				    		 	 
						    		 var mockFile = { name: data.details.name , size: data.details.size };
						    		 thisDropzone.options.addedfile.call(thisDropzone, mockFile);
						    		 thisDropzone.options.thumbnail.call(thisDropzone, mockFile, data.details.link);
						    		 $("#frm_ajax").append('<input type="hidden" name="file_name" class="file_name" value="'+data.details.name+'">');
					    		 }
					    	});
			    		}
			    	}
			    	
			        this.on("maxfilesexceeded", function(file) {
			            this.removeAllFiles();
			            this.addFile(file);
			        });
			        
			        let removeCallback = undefined;
			        
			        Dropzone.confirm = function(question, fnAccepted, fnRejected) {
			        	removeCallback = fnAccepted;
			        	
			        	$.confirm({
				    		theme: 'material',
				    		 animation: 'opacity',             
				             animateFromElement: false,
						    title: t('Please confirm your action'),
						    content: t('Are you sure you want to permanently delete this image?'),
						    buttons: {
								confirm:{
									text: t('Confirm'), 
									action: function () {
									   if (removeCallback) {
	                                       removeCallback();
	                                   }
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
			        	
			        };
			        		        
			        this.on("removedfile", function(file) {		            
			        	var filename = $(".file_name").val() ;
			        	if(!empty(filename)){
				            processAjax("upload/remove" , "filename=" + filename , 1 );		            
				            $(".file_name").remove();
			        	}
			        });
			    }
			});
					
		}
	};
		
	var multippleDropZone = function(){
		if( $("#multiple_dropzone").exists() ){
			$("#multiple_dropzone").dropzone({    	
				paramName :"uploadfile",
			    maxFilesize: file_limit,        
			    maxFiles: 20,
			    addRemoveLinks : true,		    
			    url: ajaxurl+"/upload/single",
			    success: function (file, response) {	        	
			        var resp = JSON.parse(response);
			        var fileuploded = file.previewElement.querySelector("[data-dz-name]");	                
			        if(resp.code==1){	            			        	       
			        	fileuploded.innerHTML = resp.details.file_name;
			        	$("#frm_ajax").append('<input type="hidden" name="file_name_multiple[]" class="file_name_multiple" value="'+resp.details.file_name+'">');
			        } else {
			        	notify( resp.msg );
			        }
			    },
			    init: function() {
			    	
			    	var thisDropzone2 = this;
			    	if ((typeof gallery_photo !== "undefined") && (gallery_photo !== null)) {
			    		if(!empty(gallery_photo)){
			    			var json_gallery_photo = JSON.parse(gallery_photo);
			    			$.each(json_gallery_photo, function(key, val){
			    				$.get( ajaxurl+"/upload/get?filename="+ val, function(data) {		
			    					 if(data.code==1){    		 
							    		 var mockFile2 = { name: data.details.name , size: data.details.size };					    		 
							    		 thisDropzone2.options.addedfile.call(thisDropzone2, mockFile2);
							    		 thisDropzone2.options.thumbnail.call(thisDropzone2, mockFile2, data.details.link);
							    		 $("#frm_ajax").append('<input type="hidden" name="file_name_multiple[]" class="file_name_multiple '+ data.details.classname +' " value="'+data.details.name+'">');
			    					 }
						    	});
			    			});
			    		}
			    	}
			    	
			        this.on("maxfilesexceeded", function(file) {
			            this.removeAllFiles();
			            this.addFile(file);
			        });
			        
			        this.on("removedfile", function(file, response) {		        	
			        	var filename = file.previewElement.querySelector('[data-dz-name]').innerHTML;		        			        
			        	var classname = filename.replace(".","");		        	
			        	if(!empty(filename)){
				            processAjax("upload/remove" , "filename=" + filename , 1);		            
				            $("."+classname).remove();
			        	}
			        });
			    }
			});
					
		}
	};
	
	var my_typehead;
	
	var autoFillItem = function(){
	
		var track_stock = '';
		if ((typeof track_stock_item !== "undefined") && (track_stock_item !== null)) {	   
		   track_stock = track_stock_item;
		}
		
	    my_typehead = $.typeahead({
		    input: '.typhead_item',
		    minLength: 0,
		    maxItem: 10,
		    order: "asc",
		    dynamic: true,
		    delay: 500,
		    hint: true,
			accent: true,
			searchOnFocus: true,	    
		    template: function (query, item) { 	        
		    	var size_name = '';
		    	if(!empty(item.size_name)){
		    		size_name='<span class="pl-1">({{size_name}})</span>';
		    	}
		        return '<span class="pl-1">' +            
		            '<span>{{item_name}}</span>'+ size_name +
		            '<div class="text-muted small">SKU {{sku}}</div>'+
		        "</span>";
		    },
		    emptyTemplate: t("no result for")+ " {{query}}",
		    source: {
		        user: {
		            display: "item_name",                        
		            ajax: function (query) {
		                return {
		                    type: "POST",
		                    url: ajaxurl+"/Ajaxitem/Get_item",
		                    path: "data.item",
		                    data: {
		                        q: "{{query}}",
		                        YII_CSRF_TOKEN : YII_CSRF_TOKEN,
		                        track_stock : track_stock
		                    },
		                    callback: {
		                        done: function (data) {	        	                        	
		                            return data;
		                        }
		                    }
		                }
		            }
		 
		        },       
		    },
		    callback: {
				        onClick: function (node, a, item, event) {             			        				        				        	
				            fillAdjustmentTable(item);
				            setTimeout(function(){			               		              
				               node.val('').trigger('input.typeahead');
				            }, 100);
				        },
				        onSendRequest: function (node, query) {
				            console.log('request is sent');
				        },
				        onReceiveRequest: function (node, query) {
				            console.log('request is received');
				        }
				    },
				    debug: true
		 });	
	
	};
	
	var added_sku = [];
	var table_last;
	
	var fillAdjustmentTable = function(item){	
		var html='';
		transaction_type = $("#transaction_type").val();	
		table_last  = $(".table_adjustment_new tbody tr:last");	
		if(!empty(item)){						    	
	    	var found = inArray(item.sku,added_sku);    	
	    	if(found){
	    	  jAlert( t("Items"), t("This item has already been added to this adjustment.") );
	    	  return;			        	
	    	}			        										
			added_sku.push(item);				
			fillAdjustmentData( $(".table_adjustment_new tbody tr:last"), transaction_type,item);				
		}
	};
	
	var fillAdjustmentData = function(target, transaction_type,item){
		dump("transaction_type=>"+ transaction_type);
		dump(JSON.stringify(item));	
		var html=''; table_last  = target;
		
		if(!empty(transaction_type) && !empty(item) ){
			
			var input_sku_hidden = '<input type="hidden" name="sku[]" value="'+ item.sku +'" >';	
			var input_cost_hidden = '<input type="hidden" name="cost[]" value="'+ prettyPrice(item.cost_price) +'" >';
			
			var size_name = '';
	    	if(!empty(item.size_name)){
	    		size_name='<span class="pl-1">('+ item.size_name+')</span>';
	    	}
	    	
	    	var stocks = 0;    	
	    	if(!empty(item.available_stocks)){
	    		stocks = item.available_stocks;
	    	}
			
			switch(transaction_type){
				case "receive_items":
				case "item_edit":	
				case "sale":
					html+='<tr>';
					 html+='<td>'+ input_sku_hidden + item.item_name + size_name +  '<div>' + t("SKU") +" " +  item.sku + '</div>'  +'</td>';
					 html+='<td>'+ prettyQty(stocks) +'</td>';
					 html+='<td><input type="text" class="form-control numeric_only text-right input_qty" name="qty[]" required="required" ></td>';
					 html+='<td><input type="text" class="form-control numeric_only text-right input_cost" name="cost[]" value="'+ prettyPrice(item.cost_price) +'"  required="required" ></td>';
					 html+='<td></td>';
					 html+='<td><a href="javascript:;" data-sku="'+ item.sku +'" class="input_delete_row"><h4><i class="fas fa-trash"></i></h4></a></td>';
					html+='</tr>';			
					table_last.after( html );		
				break;
				
				case "inventory_count":			   
				   html+='<tr>';
					 html+='<td>'+ input_cost_hidden + input_sku_hidden + item.item_name + size_name +  '<div>' + t("SKU") +" " +  item.sku + '</div>'  +'</td>';
					 html+='<td>'+ prettyQty(stocks) +'</td>';
					 html+='<td><input type="text" class="form-control numeric_only text-right input_qty" name="qty[]" required="required" ></td>';								 
					 html+='<td><a href="javascript:;" data-sku="'+ item.sku +'" class="input_delete_row"><h4><i class="fas fa-trash"></i></h4></a></td>';
					html+='</tr>';			
					table_last.after( html );		
				break;
				
				case "loss":
				  html+='<tr>';
					 html+='<td>'+ input_cost_hidden + input_sku_hidden + item.item_name + size_name +  '<div>' + t("SKU") +" " +  item.sku + '</div>'  +'</td>';
					 html+='<td>'+ prettyQty(stocks) +'</td>';
					 html+='<td><input type="text" class="form-control numeric_only text-right input_qty" name="qty[]" required="required" ></td>';				 
					 html+='<td></td>';
					 html+='<td><a href="javascript:;" data-sku="'+ item.sku +'" class="input_delete_row"><h4><i class="fas fa-trash"></i></h4></a></td>';
					html+='</tr>';			
					table_last.after( html );	
				break;
				
				case "damage":
				  html+='<tr>';
					 html+='<td>'+ input_cost_hidden + input_sku_hidden + item.item_name + size_name +  '<div>' + t("SKU") +" " +  item.sku + '</div>'  +'</td>';
					 html+='<td>'+ prettyQty(stocks) +'</td>';
					 html+='<td><input type="text" class="form-control numeric_only text-right input_qty" name="qty[]" required="required" ></td>';				 
					 html+='<td></td>';
					 html+='<td><a href="javascript:;" data-sku="'+ item.sku +'" class="input_delete_row"><h4><i class="fas fa-trash"></i></h4></a></td>';
					html+='</tr>';			
					table_last.after( html );	
				break;
				
				case "purchase_order":			
	               					   
				   var $item_qty = ''; var $total_amount = ''; var $po_details_id='';
				   var shtml=''; var less_receive=0; var $total_receive =0;
				   //var $item_case = '1';
				   
				   if ((typeof item.total_receive !== "undefined") && (item.total_receive !== null)) {
				   	   less_receive =  parseFloat(item.qty) - parseFloat(item.total_receive) ;			   	   
				   	   $total_receive =  parseFloat(item.total_receive) ;			   	   
				   	   //alert(less_receive);
				   	   if($total_receive>0){			
				   	   	
				   	   	   $total_amount =   parseFloat(item.total_receive) * parseFloat(item.cost_price);			   	   	      	   	   
				   	   	   
				   	   	   shtml+='<tr>';
							 shtml+='<td>'+  item.item_name + size_name +  '<div>' + t("SKU") +" " +  item.sku + '</div>'  +'</td>';
							 shtml+='<td>'+ prettyQty(stocks) +'</td>';
							 shtml+='<td>'+ prettyQty(item.incoming_balance) +'</td>';
							 shtml+='<td>'+ prettyQty(item.total_receive) +'</td>';				 
							 shtml+='<td>'+ prettyPrice(item.cost_price) +'</td>';
							 shtml+='<td>'+ prettyPrice($total_amount) +'</td>';
							 shtml+='<td></td>';
							shtml+='</tr>';									 			   	   
				   	   }
				   } 
				   
				   if ((typeof item.po_id !== "undefined") && (item.po_id !== null)) {
				      $item_qty = ' value="'+ prettyQty(item.qty) +'" ';	 
				      if($total_receive>0){
				      	$item_qty = ' value="'+ prettyQty(less_receive) +'" ';	 
					  }
					
				      $total_amount =   parseFloat(item.qty) * parseFloat(item.cost_price);
				      $po_details_id = '<input type="hidden" name="po_details_id[]" value="'+ item.po_details_id +'" >';
				   }			   						   			   
				  				   			  
				   html+='<tr>';
					 html+='<td>'+ $po_details_id + input_sku_hidden + item.item_name + size_name +  '<div>' + t("SKU") +" " +  item.sku + '</div>'  +'</td>';
					 html+='<td>'+ prettyQty(stocks) +'</td>';				 
					 html+='<td>'+ prettyQty(item.incoming_balance) +'</td>';
					 html+='<td><input type="text" class="form-control numeric_only text-right input_case" name="case[]" required="required"></td>';
					 html+='<td><input type="text" class="form-control numeric_only text-right input_qty" name="qty[]" required="required" '+ $item_qty +' ></td>';				 
					 html+='<td><input type="text" class="form-control numeric_only text-right input_cost" name="cost[]" value="'+ prettyPrice(item.cost_price) +'"  required="required" ></td>';
					 html+='<td>'+ prettyPrice($total_amount) +'</td>';
					 html+='<td><a href="javascript:;" data-sku="'+ item.sku +'" class="input_delete_row"><h4><i class="fas fa-trash"></i></h4></a></td>';
					html+='</tr>';			
					
					if($total_receive>0 && less_receive<=0){
				   	   html='';
				    } 
					
					table_last.after( shtml+html );
				break;
				
			}
		}
	};
	
	var clearTableData = function(target, target2){
		target.remove();
		target2.html('<tr></tr>');
	};
	
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
