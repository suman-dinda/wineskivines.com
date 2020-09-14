<?php
$tables = array();
$table_prefix=Yii::app()->db->tablePrefix;
$date_default = "datetime NOT NULL DEFAULT CURRENT_TIMESTAMP";
$stmt = "SELECT VERSION() as mysql_version";
if($res = Yii::app()->db->createCommand($stmt)->queryRow()){	
	$mysql_version = (float)$res['mysql_version'];	
	if($mysql_version<=5.5){				
		$date_default="datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
	}
}

$tables[]=array( 
  'type'=>"Creating",
  'name'=>"subcategory_item_relationships",
  'fields'=> array(
    'id'=>'pk',
    'sub_item_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'subcat_id'=>"integer(14) NOT NULL DEFAULT '0'",    
  ),
  'index'=>array('sub_item_id','subcat_id')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"item_relationship_size",
  'fields'=> array(
    'item_size_id'=>'pk',
    'merchant_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'item_token'=>"string NOT NULL DEFAULT ''",    
    'item_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'size_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'price'=>"float(14,4) NOT NULL DEFAULT '0'",
    'cost_price'=>"float(14,4) NOT NULL DEFAULT '0'",
    'sku'=>"string NOT NULL DEFAULT ''",    
    'available'=>"integer(1) NOT NULL DEFAULT '1'",
    'low_stock'=>"float(14,2) NOT NULL DEFAULT '0'",
    'created_at'=>"varchar(50) NOT NULL DEFAULT ''", 
    'updated_at'=>"varchar(50) NOT NULL DEFAULT ''"
  ),
  'index'=>array('item_id','size_id','item_token','merchant_id','sku')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"item_relationship_category",
  'fields'=> array(
    'id'=>'pk',
    'merchant_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'item_id'=>"integer(14) NOT NULL DEFAULT '0'", 
    'cat_id'=>"integer(14) NOT NULL DEFAULT '0'",
  ),
  'index'=>array('merchant_id','item_id','cat_id')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"item_relationship_subcategory",
  'fields'=> array(
    'id'=>'pk',
    'merchant_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'item_id'=>"integer(14) NOT NULL DEFAULT '0'", 
    'subcat_id'=>"integer(14) NOT NULL DEFAULT '0'",
  ),
  'index'=>array('merchant_id','item_id','subcat_id')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"item_sku",
  'fields'=> array(
    'sku_id'=>'pk',
    'item_id'=>"varchar(14) NOT NULL DEFAULT ''",    
  )  
);

$tables[]=array(
  'type'=>"Query",
  'name'=>"item_sku",
  'stmt'=>"ALTER TABLE ".$table_prefix."item_sku AUTO_INCREMENT = 1000"
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_transaction",
  'fields'=> array(
    'transaction_id'=>'pk',
    'merchant_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'transaction_type'=>"varchar(100) NOT NULL DEFAULT 'receive_items'",   
    'transaction_code'=>"varchar(5) NOT NULL DEFAULT ''", 
    'notes'=>"text ",
    'created_at'=>$date_default,
    'added_by'=>"varchar(100) NOT NULL DEFAULT ''", 
    'ip_address'=>"varchar(50) NOT NULL DEFAULT ''", 
  ),
  'index'=>array('merchant_id','transaction_type','transaction_code')
);


$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_transaction_details",
  'fields'=> array(
    'transaction_details_id'=>'pk',   
    'transaction_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'sku'=>"varchar(50) NOT NULL DEFAULT ''", 
    'cost_price'=>"float(14,4) NOT NULL DEFAULT '0'",
    'qty'=>"float(14,2) NOT NULL DEFAULT '0'",
    'created_at'=>$date_default, 
    'ip_address'=>"varchar(50) NOT NULL DEFAULT ''", 
  ),
  'index'=>array('transaction_id','sku')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_supplier",
  'fields'=> array(
    'supplier_id'=>'pk',
    'merchant_id'=>"integer(14) NOT NULL DEFAULT '0'",   
    'supplier_name'=>"string NOT NULL DEFAULT ''", 
    'contact_name'=>"string NOT NULL DEFAULT ''", 
    'email'=>"string NOT NULL DEFAULT ''", 
    'phone_number'=>"varchar(50) NOT NULL DEFAULT ''", 
    'address_1'=>"varchar(255) NOT NULL DEFAULT ''", 
    'address_2'=>"varchar(255) NOT NULL DEFAULT ''", 
    'city'=>"varchar(100) NOT NULL DEFAULT ''", 
    'postal_code'=>"varchar(100) NOT NULL DEFAULT ''", 
    'country_code'=>"varchar(5) NOT NULL DEFAULT ''", 
    'region'=>"varchar(100) NOT NULL DEFAULT ''", 
    'notes'=>"text", 
    'created_at'=>$date_default, 
    'updated_at'=>$date_default, 
    'ip_address'=>"varchar(50) NOT NULL DEFAULT ''", 
  ),
  'index'=>array('merchant_id','supplier_name')
);


$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_purchase_order",
  'fields'=> array(
    'po_id'=>'pk',
    'merchant_id'=>"integer(14) NOT NULL DEFAULT '0'", 
    'purchase_date'=>"date DEFAULT NULL",   
    'supplier_id'=>"integer(14) NOT NULL DEFAULT '0'",
    'notes'=>"text",
    'status'=>"varchar(100) NOT NULL DEFAULT ''", 
    'expected_on'=>"date DEFAULT NULL",  
    'created_at'=>$date_default,
    'updated_at'=>$date_default,
    'added_by'=>"varchar(100) NOT NULL DEFAULT ''", 
    'ip_address'=>"varchar(50) NOT NULL DEFAULT ''", 
  ),
  'index'=>array('merchant_id','supplier_id','status')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_purchase_order_details",
  'fields'=> array(
    'po_details_id'=>'pk',
    'po_id'=>"integer(14) NOT NULL DEFAULT '0'", 
    'sku'=>"varchar(50) NOT NULL DEFAULT ''", 
    'qty'=>"float(14,2) NOT NULL DEFAULT '0'",
    'cost_price'=>"float(14,4) NOT NULL DEFAULT '0'",
    'amount'=>"float(14,4) NOT NULL DEFAULT '0'",
  ),
  'index'=>array('po_id','sku','amount')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_purchase_receive",
  'fields'=> array(
    'receive_id'=>'pk',
    'po_id'=>"integer(14) NOT NULL DEFAULT '0'", 
    'po_details_id'=>"integer(14) NOT NULL DEFAULT '0'", 
    'sku'=>"varchar(50) NOT NULL DEFAULT ''", 
    'qty'=>"float(14,2) NOT NULL DEFAULT '0'",
    'added_by'=>"varchar(255) NOT NULL DEFAULT ''", 
    'created_at'=>$date_default, 
    'ip_address'=>"varchar(50) NOT NULL DEFAULT ''", 
  ),
  'index'=>array('po_id','sku','qty')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_sales",
  'fields'=> array(
    'id'=>'pk',
    'order_id'=>"integer(14) NOT NULL DEFAULT '0'",  
    'transaction_type'=>"varchar(50) NOT NULL DEFAULT 'sale'", 
    'order_status'=>"varchar(255) NOT NULL DEFAULT 'pending'", 
    'process_status'=>"varchar(255) NOT NULL DEFAULT 'pending'", 
    'created_at'=>"varchar(100) NOT NULL DEFAULT ''", 
    'updated_at'=>"varchar(100) NOT NULL DEFAULT ''", 
    'ip_address'=>"varchar(50) NOT NULL DEFAULT ''", 
  ) ,
  'index'=>array('order_id','transaction_type','order_status')
);


$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_stocks",
  'fields'=> array(
    'stock_id'=>'pk',    
    'created_at'=>$date_default, 
    'sku'=>"varchar(50) NOT NULL DEFAULT ''", 
    'merchant_id'=>"integer(14) NOT NULL DEFAULT '0'", 
    'transaction_type'=>"varchar(100) NOT NULL DEFAULT ''",   
    'transaction_id'=>"integer(14) NOT NULL DEFAULT '0'", 
    'transaction_code'=>"varchar(100) NOT NULL DEFAULT ''",   
    'adjustment'=>"float(14,2) NOT NULL DEFAULT '0'",
    'stock_after'=>"float(14,2) NOT NULL DEFAULT '0'",
    'cost_price'=>"float(14,4) NOT NULL DEFAULT '0'",
    'added_by'=>"varchar(100) NOT NULL DEFAULT ''",   
    'remarks'=>"varchar(255) NOT NULL DEFAULT ''",   
    'ip_address'=>"varchar(50) NOT NULL DEFAULT ''",   
  ) ,
  'index'=>array('sku','merchant_id','transaction_type','transaction_id','transaction_code','added_by')
);


$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_access_role",
  'fields'=> array(
    'role_id'=>'pk',    
    'role_name'=>"varchar(255) NOT NULL DEFAULT ''",     
    'access'=>"text",
    'is_protected'=>"integer(1) NOT NULL DEFAULT '0'",  
    'created_at'=>$date_default, 
    'updated_at'=>$date_default, 
    'ip_address'=>"varchar(50) NOT NULL DEFAULT ''", 
  ) 
);

$inventory_access_role=array();
$inventory_access_role[]=array(
  'role_name'=>'Administrator',
  'access'=>'["reports","sales_summary","sales_item","sales_category","sales_payment_type","sales_receipt","sales_by_addon","items","list","item_new","category_list","category_add","addon_category_list","addon_category_add","addon_item_list","addon_add","size_list","size_add","ingredients_list","ingredients_add","cooking_list","cooking_add","purchases","order_list","purchase_new","purchase_view","purchase_receive","adjustment_list","adjustment_new","adjustment_details","supplier_list","supplier_new","history","valuation","user","userlist","add_user","access_rights","create_role","general"]
',
  'is_protected'=>1,
  'created_at'=>FunctionsV3::dateNow(),
  'updated_at'=>FunctionsV3::dateNow(),
  'ip_address'=>$_SERVER['REMOTE_ADDR']
);

$inventory_access_role[]=array(
  'role_name'=>'Employee',
  'access'=>'["items","list","item_new","category_list","category_add","addon_category_list","addon_category_add","addon_item_list","addon_add","size_list","size_add","ingredients_list","ingredients_add","cooking_list","cooking_add","purchases","order_list","purchase_new","purchase_view","purchase_receive","adjustment_list","adjustment_new","adjustment_details","supplier_list","supplier_new","history","valuation"]
',
  'is_protected'=>1,
  'created_at'=>FunctionsV3::dateNow(),
  'updated_at'=>FunctionsV3::dateNow(),
  'ip_address'=>$_SERVER['REMOTE_ADDR']
);


$inventory_access_role[]=array(
  'role_name'=>'Manager',
  'access'=>'["reports","sales_summary","sales_item","sales_category","sales_payment_type","sales_receipt","sales_by_addon","items","list","item_new","category_list","category_add","addon_category_list","addon_category_add","addon_item_list","addon_add","size_list","size_add","ingredients_list","ingredients_add","cooking_list","cooking_add","purchases","order_list","purchase_new","purchase_view","purchase_receive","adjustment_list","adjustment_new","adjustment_details","supplier_list","supplier_new","history","valuation"]',
  'is_protected'=>1,
  'created_at'=>FunctionsV3::dateNow(),
  'updated_at'=>FunctionsV3::dateNow(),
  'ip_address'=>$_SERVER['REMOTE_ADDR']
);

$tables[]=array(
  'type'=>"Insert_data",
  'name'=>"inventory_access_role",  
  'data'=>$inventory_access_role
);


$option_data = array();
$option_data[]=array(
  'option_name'=>'inventory_show_stock',
  'option_value'=>1
);
$option_data[]=array(
  'option_name'=>'inventory_in_stock',
  'option_value'=>11
);
$option_data[]=array(
  'option_name'=>'inventory_low_stock',
  'option_value'=>10
);
$option_data[]=array(
  'option_name'=>'inventory_items_left',
  'option_value'=>8
);

$option_data[]=array(
  'option_name'=>'inventory_reports_default_status',
  'option_value'=>'["accepted","acknowledged","delivered","inprogress","paid","pending","started","successful"]'
);

$option_data[]=array(
  'option_name'=>'inventory_accepted_order_status',
  'option_value'=>'["accepted","paid","pending","successful"]'
);

$option_data[]=array(
  'option_name'=>'inventory_cancel_order_status',
  'option_value'=>'["cancelled","decline","declined","failed"]'
);

$tables[]=array(
  'type'=>"Insert_data",
  'name'=>"option",  
  'data'=>$option_data
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"inventory_lowstock_notification",
  'fields'=> array(
    'id'=>'pk',        
    'merchant_id'=>"integer(14) NOT NULL DEFAULT '0'",  
    'sku'=>"varchar(50) NOT NULL DEFAULT ''",   
    'available_stocks'=>"varchar(14) NOT NULL DEFAULT '0'", 
    'date_process'=>"date DEFAULT NULL",  
  ) ,  
  'index'=>array('merchant_id','sku')
);

$tables[]=array(
  'type'=>"Creating",
  'name'=>"order_details_addon",
  'fields'=> array(
    'id'=>'pk',        
    'order_id'=>"integer(14) NOT NULL DEFAULT '0'",  
    'subcat_id'=>"integer(14) NOT NULL DEFAULT '0'",  
    'sub_item_id'=>"integer(14) NOT NULL DEFAULT '0'",  
    'addon_price'=>"float(14,4) NOT NULL DEFAULT '0'",
    'addon_qty'=>"float(14,2) NOT NULL DEFAULT '0'",
  ) ,  
  'index'=>array('order_id','subcat_id','sub_item_id')
);

$tables[]=array(
  'type'=>"addColumn",
  'name'=>"admin_user",
  'fields'=> array(
    'status'=>"varchar(100) NOT NULL DEFAULT 'active'", 
    'contact_number'=>"varchar(50) NOT NULL DEFAULT ''", 
    'inventory_role_id'=>"integer(1) NOT NULL DEFAULT '0'",  
    'inventory_enabled'=>"integer(1) NOT NULL DEFAULT '1'",     
  )
);


$tables[]=array(
  'type'=>"addColumn",
  'name'=>"merchant",
  'fields'=> array(        
    'inventory_role_id'=>"integer(1) NOT NULL DEFAULT '1'",  
    'inventory_enabled'=>"integer(1) NOT NULL DEFAULT '1'",     
    'inventory_low_stock_notify'=>"integer(1) NOT NULL DEFAULT '0'",     
    'inventory_email_notify'=>"varchar(255) NOT NULL DEFAULT ''",
  )
);


$tables[]=array(
  'type'=>"addColumn",
  'name'=>"merchant_user",
  'fields'=> array(        
    'contact_number'=>"varchar(50) NOT NULL DEFAULT ''", 
    'inventory_role_id'=>"integer(1) NOT NULL DEFAULT '0'",  
    'inventory_enabled'=>"integer(1) NOT NULL DEFAULT '1'",     
  )
);


$tables[]=array(
  'type'=>"addColumn",
  'name'=>"order_history",
  'fields'=> array(        
    'update_by_type'=>"varchar(100) NOT NULL DEFAULT ''",     
    'update_by_id'=>"integer(14) NOT NULL DEFAULT '0'",  
    'update_by_name'=>"varchar(255) NOT NULL DEFAULT ''",     
  )
);

$tables[]=array(
  'type'=>"addColumn",
  'name'=>"item",
  'fields'=> array(        
    'item_token'=>"varchar(50) NOT NULL DEFAULT ''",   
    'with_size'=>"integer(1) NOT NULL DEFAULT '0'",    
    'track_stock'=>"integer(1) NOT NULL DEFAULT '1'", 
    'supplier_id'=>"integer(14) NOT NULL DEFAULT '0'", 
  )
);

$tables[]=array(
  'type'=>"addColumn",
  'name'=>"order_details",
  'fields'=> array(            
    'size_id'=>"integer(14) NOT NULL DEFAULT '0'",    
    'cat_id'=>"integer(14) NOT NULL DEFAULT '0'",     
  )
);


/*VIEW TABLES*/

$stmt="
create OR REPLACE VIEW ".$table_prefix."user_master_list as
select 
a.admin_id as id,
'merchant_id',
'user_type',
a.email_address,
a.contact_number,
a.inventory_role_id as role_id,
a.username,
a.password,
a.session_token,
a.status,
a.inventory_enabled
from ".$table_prefix."admin_user a

UNION ALL

select 
b.merchant_user_id as id,
b.merchant_id,
'merchant_user',
b.contact_email as email_address,
b.contact_number,
b.inventory_role_id as role_id,
b.username,
b.password,
b.session_token,
b.status,
b.inventory_enabled
from ".$table_prefix."merchant_user b


UNION ALL
select
c.merchant_id as id,
c.merchant_id,
'merchant',
c.contact_email as email_address,
c. contact_phone as contact_number,
c.inventory_role_id as role_id,
c.username,
c.password,
c.session_token,
c.status,
c.inventory_enabled
from  ".$table_prefix."merchant c
";
$tables[]=array(
  'type'=>"View",
  'name'=>"user_master_list",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_item as
select 
a.item_id,
a.item_token,
a.merchant_id,
a.item_name,
a.item_name_trans,
a.item_description_trans,
a.status,
a.with_size,
a.supplier_id,
IFNULL(b.item_size_id,'') as item_size_id,
IFNULL(b.size_id,0) as size_id,
IFNULL(c.size_name,'') as size_name,
IFNULL(c.size_name_trans,'') as size_name_trans,
IFNULL(b.price,0) as price,
IFNULL(b.cost_price,0) as cost_price,
IFNULL(b.sku,'') as sku,
a.track_stock,
IFNULL(b.available,0) as available,
IFNULL(b.low_stock,0) as low_stock

from ".$table_prefix."item  a
left join ".$table_prefix."item_relationship_size b
on
a.item_id = b.item_id

left join ".$table_prefix."size c
on
b.size_id = c.size_id
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_item",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_item_cat as
select 
a.cat_id,
c.category_name,
c.category_description,
c.category_name_trans,
c.category_description_trans,
b.*
from
".$table_prefix."item_relationship_category a
left join ".$table_prefix."view_item b
on 
a.item_id = b.item_id

left join ".$table_prefix."category c
on 
a.cat_id = c.cat_id
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_item_cat",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_rs_category as
select 
a.id,
a.merchant_id,
a.item_id,
a.cat_id,
b.category_name,
b.category_description,
b.category_name_trans,
b.category_description_trans,
b.photo,
b.status,
b.sequence
from ".$table_prefix."item_relationship_category a
left join ".$table_prefix."category b
on 
a.cat_id = b.cat_id
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_rs_category",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_purchase_order_details as
select 
a.*,
b.status
from ".$table_prefix."inventory_purchase_order_details a
LEFT JOIN ".$table_prefix."inventory_purchase_order b
ON 
a.po_id = b.po_id
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_purchase_order_details",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_purchase_receive as
select 
a.*,
b.status
from ".$table_prefix."inventory_purchase_receive a
LEFT JOIN ".$table_prefix."inventory_purchase_order b
ON 
a.po_id = b.po_id
";
$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_purchase_receive",
  'stmt'=>$stmt
);



$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_purchase_stocks as
select a.sku,a.item_name,
IFNULL(( 
 select sum(qty) from ".$table_prefix."view_inventory_purchase_order_details
 where sku = a.sku and status not in ('closed','cancel')
),0)
 - 
IFNULL(( select sum(qty) from ".$table_prefix."view_inventory_purchase_receive  
 where sku = a.sku  and status not in ('closed','cancel') ),0) 
as incoming_balance
from ".$table_prefix."view_item a
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_purchase_stocks",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_item_stocks as
select a.*,

IFNULL((
 select stock_after from
 ".$table_prefix."inventory_stocks 
 where
 sku = a.sku
  order by stock_id desc
limit 0,1
),0) as available_stocks,

IFNULL((
select incoming_balance
from ".$table_prefix."view_inventory_purchase_stocks
where
sku = a.sku
limit 0,1
),0) as incoming_balance

from ".$table_prefix."view_item a
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_item_stocks",
  'stmt'=>$stmt
);

$stmt="
create OR REPLACE VIEW ".$table_prefix."view_item_list as

select a.item_id, a.merchant_id, a.photo, a.item_name, a.category, a.with_size, a.track_stock,

IFNULL((
  select group_concat(category_name separator ',' )
  from ".$table_prefix."view_rs_category where item_id= a.item_id
),'') as categories_name,

IFNULL((
  select sku from ".$table_prefix."item_relationship_size
  where
  item_id = a.item_id
  and size_id = 0
),'') as sku,

IFNULL((
  select price from ".$table_prefix."item_relationship_size
  where
  item_id = a.item_id
  and size_id = 0
),0) as price,


IFNULL((
  select cost_price from ".$table_prefix."item_relationship_size
  where
  item_id = a.item_id
  and size_id = 0
),0) as cost_price,


IFNULL((
  select low_stock from ".$table_prefix."item_relationship_size
  where
  item_id = a.item_id
  and size_id = 0
),0) as low_stock,

IFNULL((
select sum(available_stocks)
from ".$table_prefix."view_item_stocks
where 
item_id = a.item_id
),0) as available_stocks

from ".$table_prefix."item a
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_item_list",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_transaction as
SELECT 
a.transaction_id,
concat(a.transaction_code,a.transaction_id) as reference,
a.merchant_id,
a.transaction_type , a.created_at,
(
	select sum(qty) from ".$table_prefix."inventory_transaction_details
	where 
	transaction_id = a.transaction_id
) as quantity
FROM ".$table_prefix."inventory_transaction a
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_transaction",
  'stmt'=>$stmt
);

$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_purchase_order as
select
a.po_id,
a.merchant_id,
a.purchase_date,
a.supplier_id,
b.supplier_name,
a.status,
a.expected_on,
a.notes,
a.added_by,
(
 select sum(qty)
 from ".$table_prefix."inventory_purchase_order_details
 where
 po_id = a.po_id
) as total_qty,
(
 select sum(amount)
 from ".$table_prefix."inventory_purchase_order_details
 where
 po_id = a.po_id
) as total,

(
 select sum(qty)
 from ".$table_prefix."inventory_purchase_receive
 where
 po_id = a.po_id
) as received


from ".$table_prefix."inventory_purchase_order a
LEFT JOIN ".$table_prefix."inventory_supplier b
ON
a.supplier_id = b.supplier_id
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_purchase_order",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_stocks as
select 
a.stock_id,
a.merchant_id,
a.created_at,
IFNULL(b.item_name,'') as item_name,
IFNULL(b.size_name,'') as size_name,
IFNULL(b.sku,'') as sku,
a.added_by,
a.transaction_type,
a.transaction_code,
a.transaction_id,
concat(a.transaction_code,'',a.transaction_id) as reference,
a.adjustment,
a.stock_after,
a.remarks

from
".$table_prefix."inventory_stocks a
left join ".$table_prefix."view_item b
on
a.sku = b.sku
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_stocks",
  'stmt'=>$stmt
);

$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_evaluation as
select
item_id,
merchant_id,
item_name,
( IFNULL(available_stocks,0) * IFNULL(cost_price,0) ) as inventory_value,
( IFNULL(available_stocks,0) * IFNULL(price,0) ) as retail_value,
( IFNULL(available_stocks,0) * IFNULL(price,0) ) - ( IFNULL(available_stocks,0) * IFNULL(cost_price,0) )  as potential_profit

FROM ".$table_prefix."view_item_stocks
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_evaluation",
  'stmt'=>$stmt
);

$stmt='
create OR REPLACE VIEW '.$table_prefix.'view_item_stocks_status as
select *,
IFNULL(
case 
  when available_stocks<=0 THEN "Out of stocks" 
  when available_stocks<=low_stock then "Low stock"
end,"")  as stock_status
from 
'.$table_prefix.'view_item_stocks 
';

$tables[]=array(
  'type'=>"View",
  'name'=>"view_item_stocks_status",
  'stmt'=>$stmt
);

$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_order_details as
select 
a.order_id,
c.merchant_id,
a.client_id,
a.item_id,
IFNULL(b.item_name,'') as item_name,
a.cat_id,
IFNULL(d.category_name,'') as category_name,
a.normal_price as sale_price,
(a.normal_price-a.discounted_price) as discount,
a.qty,
IFNULL((a.normal_price*a.qty),0) as total_sale,
IFNULL(b.sku,'') as sku,
IFNULL(a.size_id,'0') as size_id,
IFNULL(b.size_name,'') as size_name ,
IFNULL(b.price,0) as original_price,
IFNULL(b.cost_price,0) as cost_price,
IFNULL((b.cost_price*a.qty),0) as total_cost,
IFNULL(b.track_stock,0) as track_stock,
IFNULL(b.available,0) as available,
c.status,
DATE_FORMAT(c.date_created,'%Y-%m-%d') as sale_date,
c.payment_type

from  ".$table_prefix."order_details a
left join ".$table_prefix."view_item b
on 
a.item_id = b.item_id
and
a.size_id = b.size_id

left join ".$table_prefix."order c
on 
a.order_id = c.order_id

left join ".$table_prefix."category d
on 
a.cat_id = d.cat_id
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_order_details",
  'stmt'=>$stmt
);

$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_sales_summary as
select
a.merchant_id,
a.sale_date,
sum(a.total_sale) as gross_sale,
sum(a.discount) as discount,
(sum(a.total_sale)-sum(a.discount)) as net_sale,
sum(a.total_cost) as total_cost,

(sum(a.total_sale)-sum(a.discount)) -  sum(a.total_cost) as gross_profit

from ".$table_prefix."view_inventory_order_details a
group by a.sale_date,a.merchant_id
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_sales_summary",
  'stmt'=>$stmt
);

$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_sales_addon as
select 
a.sub_item_id,
b.sub_item_name,
a.addon_price,
a.addon_qty,
(a.addon_price * a.addon_qty) as total,
c.merchant_id,
c.status,
c.date_created,
c.delivery_date

from
".$table_prefix."order_details_addon a
left join ".$table_prefix."subcategory_item b
on
a.sub_item_id = b.sub_item_id

left join ".$table_prefix."order c
on
a.order_id = c.order_id 
";

$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_sales_addon",
  'stmt'=>$stmt
);


$stmt="
create OR REPLACE VIEW ".$table_prefix."view_inventory_lowstock_notification as
select
a.id,
IFNULL(b.restaurant_name,'') as merchant,
IFNULL(c.item_name,'') as item_name,
IFNULL(a.available_stocks,0) as available_stocks,
IFNULL(a.date_process,'') as date_process

FROM ".$table_prefix."inventory_lowstock_notification a
left join ".$table_prefix."merchant b
on
a.merchant_id = b.merchant_id

left join ".$table_prefix."view_item c
on
a.sku = c.sku
";
$tables[]=array(
  'type'=>"View",
  'name'=>"view_inventory_lowstock_notification",
  'stmt'=>$stmt
);