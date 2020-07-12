<?php
class MenuWrapper
{
	public static function menu($access=array(), $user='', $email='')
	{		
		return  array(  		    		    
		    'activeCssClass'=>'active', 
		    'encodeLabel'=>false,
		    'htmlOptions' => array(
		      'class'=>'menu_nav',
		     ),
		    'items'=>array(

		       array(		        
		        'label'=>'<i><div class="rounded-circle"><span class="fas fa-user"></span></div></i> <span class="i_title">'.$user.'<br/>'.$email.'</span>',
		        'id'=>"userprofile",
		        'url'=>array('/'.APP_FOLDER.'/index/profile'),
		        'linkOptions'=>array(
		          'class'=>"userprofile webpop",
		          'data-content'=>translate("Profile")
		        )
		       ),
		    
		       array(
		         'visible'=>true,
		         'label'=>'<i class="fas fa-tachometer-alt"></i> <span class="i_title">'.translate("Dashboard").'</span> ',
		         'id'=>"dashboard",
		         'url'=>array('/'.APP_FOLDER.'/index/dashboard'),
			      'linkOptions'=>array(
			          'class'=>"webpop",
			          'data-content'=>translate("Dashboard")
			      )
		       ),
		       
		       array('visible'=> in_array('reports', (array)$access)?true:false ,
		        'label'=>'<i class="fas fa-chart-bar"></i> <span class="i_title">'.translate("Reports").'</span> <span class="menu_expand"><div class="fas fa-chevron-down"></div></span>',
		        'id'=>'reports',
		        'url'=>'javascript:;','linkOptions'=>array(
		          'class'=>'reports webpop',
		          'data-content'=>translate("Reports")
		        ),
		        
		        'itemOptions'=>array('class'=>''), 
		        'items'=>array(
		              array(
		                 'visible'=>in_array('sales_summary', (array)$access)?true:false ,
		                 'label'=>translate("Sales summary"),
		                 'id'=>'sales_summary',
		                 'url'=>array('/'.APP_FOLDER.'/reports/sales_summary'),		                 
		               ),
		               array(
		                 'visible'=>in_array('sales_item', (array)$access)?true:false ,
		                 'label'=>translate("Sales by item"),
		                 'id'=>'sales_item',
		                 'url'=>array('/'.APP_FOLDER.'/reports/sales_item')
		               ),
		               array(
		                 'visible'=>in_array('sales_category', (array)$access)?true:false ,
		                 'label'=>translate("Sales by category"),
		                 'id'=>'sales_category',
		                 'url'=>array('/'.APP_FOLDER.'/reports/sales_category')
		               ),
		               array(
		                 'visible'=>in_array('sales_payment_type', (array)$access)?true:false ,
		                 'label'=>translate("Sales by payment type"),
		                 'id'=>'sales_payment_type',
		                 'url'=>array('/'.APP_FOLDER.'/reports/sales_payment_type')
		               ),
		               array(
		                  'visible'=>in_array('sales_receipt', (array)$access)?true:false ,
		                 'label'=>translate("Receipts"),
		                 'id'=>'sales_receipt',
		                 'url'=>array('/'.APP_FOLDER.'/reports/sales_receipt')
		               ),
		               array(
		                 'visible'=>in_array('sales_by_addon', (array)$access)?true:false ,
		                 'label'=>translate("Sales by addon"),
		                 'id'=>'sales_by_addon',
		                 'url'=>array('/'.APP_FOLDER.'/reports/sales_by_addon')
		               ),
		           )                 
		        ), 
		        
		        array('visible'=> in_array('items', (array)$access)?true:false ,
		        'label'=>'<i class="fas fa-shopping-bag"></i> <span class="i_title">'.translate("Items").'</span> <span class="menu_expand"><div class="fas fa-chevron-down"></div></span>',
		        'id'=>'items',
		        'url'=>'javascript:;','linkOptions'=>array(
		          'class'=>'item webpop',
		          'data-content'=>translate("Items")
		        ),
		        'itemOptions'=>array('class'=>''), 
		        'items'=>array(
		                   array(
		                     'visible'=>in_array('list', (array)$access)?true:false ,
		                     'label'=>translate("Items"),
		                     'id'=>'list',
		                     'url'=>array('/'.APP_FOLDER.'/item/list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add item"),
			                       'id'=>'item_new',
			                     )
			                 ),
		                   ),
		                   array(
		                     'visible'=>in_array('category_list', (array)$access)?true:false ,
		                     'label'=>translate("Categories"),
		                     'id'=>'category_list',
		                     'url'=>array('/'.APP_FOLDER.'/item/category_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add category"),
			                       'id'=>'category_add',
			                     )
			                 ),
		                   ),
		                   array(
		                      'visible'=>in_array('addon_category_list', (array)$access)?true:false ,
		                     'label'=>translate("Addon categories"),
		                     'id'=>'addon_category_list',
		                     'url'=>array('/'.APP_FOLDER.'/item/addon_category_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add addon category"),
			                       'id'=>'addon_category_add',
			                     )
			                 ),
		                   ),
		                   array(
		                     'visible'=>in_array('addon_item_list', (array)$access)?true:false ,
		                     'label'=>translate("Addon item"),
		                     'id'=>'addon_item_list',
		                     'url'=>array('/'.APP_FOLDER.'/item/addon_item_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add addon item"),
			                       'id'=>'addon_add',
			                     )
			                 ),
		                   ),
		                   array(
		                     'visible'=>in_array('size_list', (array)$access)?true:false ,
		                     'label'=>translate("Size"),
		                     'id'=>'size_list',
		                     'url'=>array('/'.APP_FOLDER.'/item/size_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add size"),
			                       'id'=>'size_add',
			                     )
			                 ),
		                   ),
		                   array(  
		                     'visible'=>in_array('ingredients_list', (array)$access)?true:false ,
		                     'label'=>translate("Ingredients"),
		                     'id'=>'ingredients_list',
		                     'url'=>array('/'.APP_FOLDER.'/item/ingredients_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add ingredients"),
			                       'id'=>'ingredients_add',
			                     )
			                 ),
		                   ),
		                   array(
		                     'visible'=>in_array('cooking_list', (array)$access)?true:false ,
		                     'label'=>translate("Cooking Reference"),
		                     'id'=>'cooking_list',
		                     'url'=>array('/'.APP_FOLDER.'/item/cooking_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add Cooking Reference"),
			                       'id'=>'cooking_add',
			                     )
			                 ),
		                   ),
		                 )
		        ),               
		        
		         array('visible'=> in_array('purchases', (array)$access)?true:false ,        
		        'label'=>'<i class="fas fa-dolly-flatbed"></i> <span class="i_title">'.translate("Inventory management").'</span> <span class="menu_expand"><div class="fas fa-chevron-down"></div></span>',
		        'id'=>'purchases',
		        'url'=>'javascript:;',
		          'linkOptions'=>array(
		           'class'=>'stocks webpop',
		          'data-content'=>translate("Inventory management")
		        ),
		        
		        'items'=>array(
		                   array(
		                     'visible'=>in_array('order_list', (array)$access)?true:false ,
		                     'label'=>translate("Purchase orders"),
		                     'id'=>'order_list',
		                     'url'=>array('/'.APP_FOLDER.'/stocks/order_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Purchase new"),
			                       'id'=>'purchase_new',
			                     ),
			                     array(
			                       'label'=>translate("Purchase view"),
			                       'id'=>'purchase_view',
			                     ),
			                     array(
			                       'label'=>translate("Purchase receive"),
			                       'id'=>'purchase_receive',
			                     )
			                 ),
		                   ),
		                   array(
		                     'visible'=>in_array('adjustment_list', (array)$access)?true:false ,
		                     'label'=>translate("Stock adjustments"),
		                     'id'=>'adjustment_list',
		                     'url'=>array('/'.APP_FOLDER.'/stocks/adjustment_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add stock adjustments"),
			                       'id'=>'adjustment_new',
			                     ),
			                     array(
			                       'label'=>translate("Adjustments details"),
			                       'id'=>'adjustment_details',
			                     )
			                 ),
		                   ),
		                   array(
		                      'visible'=>in_array('supplier_list', (array)$access)?true:false ,
		                     'label'=>translate("Suppliers"),
		                     'id'=>'supplier_list',
		                     'url'=>array('/'.APP_FOLDER.'/stocks/supplier_list'),
		                     'sub_items'=>array(
			                     array(
			                       'label'=>translate("Add suppliers"),
			                       'id'=>'supplier_new',
			                     )
			                 ),
		                   ),
		                   array(
		                     'visible'=>in_array('history', (array)$access)?true:false ,
		                     'label'=>translate("Inventory history"),
		                     'id'=>'history',
		                     'url'=>array('/'.APP_FOLDER.'/stocks/history')
		                   ),
		                   array(
		                     'visible'=>in_array('valuation', (array)$access)?true:false ,
		                     'label'=>translate("Inventory valuation"),
		                     'id'=>'valuation',
		                     'url'=>array('/'.APP_FOLDER.'/stocks/valuation')
		                   )
		                 )
		        
		        ), 
		
		     
		        array('visible'=> in_array('user', (array)$access)?true:false  ,
		        'label'=>'<i class="fas fa-user-friends"></i> <span class="i_title">'.translate("User management").'</span> <span class="menu_expand"><div class="fas fa-chevron-down"></div></span>',
		        'id'=>'user',
		        'url'=>array('/'.APP_FOLDER.'/user/userlist'),
		        'linkOptions'=>array(
		          'class'=>'user webpop',
		          'data-content'=>translate("User management")
		        ),
		        		        
		        'items'=>array(		           
		           )                 
		        ), 
		         
		        array('visible'=> in_array('general', (array)$access)?true:false ,
		        'label'=>'<i class="fas fa-cog"></i> <span class="i_title">'.translate("Settings").'</span>',
		        'id'=>'general',
		        'url'=>array('/'.APP_FOLDER.'/settings/general'),'linkOptions'=>array(
		          'class'=>'settings webpop',
		          'data-content'=>translate("Settings")
		        )), 
		     )   
		);       
	}
	
	public static function adminMenu($access=array(), $user='', $email='')
	{
		return  array(
		    'activeCssClass'=>'active', 
		    'encodeLabel'=>false,
		    'htmlOptions' => array(
		      'class'=>'menu_nav',
		     ),
		     'items'=>array(
		       array(		       
		        'label'=>'<i><div class="rounded-circle"><span class="fas fa-user"></span></div></i> <span class="i_title">'.$user.'<br/>'.$email.'</span>',
		        'id'=>"userprofile",
		        'url'=>array('/'.APP_FOLDER.'/index/profile'),
		        'linkOptions'=>array(
		          'class'=>"userprofile webpop",
		          'data-content'=>translate("Profile")
		        )
		       ),
		       
		       
		       array(
		         'visible'=>true,
		         'label'=>'<i class="fas fa-tachometer-alt"></i> <span class="i_title">'.translate("Dashboard").'</span> ',
		         'id'=>"dashboard",
		         'url'=>array('/'.APP_FOLDER.'/index/dashboard'),
			      'linkOptions'=>array(
			          'class'=>"webpop",
			          'data-content'=>translate("Dashboard")
			      )
		       ),
		       
		       array('visible'=> true,
		        'label'=>'<i class="fas fa-warehouse"></i> <span class="i_title">'.translate("Merchant").'</span>',
		        'id'=>'general',
		        'url'=>array('/'.APP_FOLDER.'/adm/merchant_list'),'linkOptions'=>array(
		          'class'=>'merchant_list webpop',
		          'data-content'=>translate("Merchant")
		        )), 
		        
		       array('visible'=> true,
		        'label'=>'<i class="fas fa-shield-alt"></i> <span class="i_title">'.translate("Access right").'</span>',
		        'id'=>'general',
		        'url'=>array('/'.APP_FOLDER.'/adm/access_rights'),'linkOptions'=>array(
		          'class'=>'access_right webpop',
		          'data-content'=>translate("Access right")
		        )), 
		        
		       array('visible'=> true,
		        'label'=>'<i class="fas fa-truck-loading"></i> <span class="i_title">'.translate("Low stock logs").'</span>',
		        'id'=>'general',
		        'url'=>array('/'.APP_FOLDER.'/adm/low_stock_logs'),'linkOptions'=>array(
		          'class'=>'low_stock_logs webpop',
		          'data-content'=>translate("Low stock logs")
		        )),  
		         
		       array('visible'=> true,
		        'label'=>'<i class="fas fa-cog"></i> <span class="i_title">'.translate("Settings").'</span>',
		        'id'=>'general',
		        'url'=>array('/'.APP_FOLDER.'/adm/general'),'linkOptions'=>array(
		          'class'=>'settings webpop',
		          'data-content'=>translate("Settings")
		        )), 
		       
		     ) /*end items*/
		);
	}
}
/*end class */