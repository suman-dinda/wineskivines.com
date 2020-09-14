<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "category";
$databasetable2 = "item";
$databasetable3 = "subcategory_item";
$country_code = $config['countryprefixcode'];

$mId = $_POST['mId'];
$category_name = $_POST['category_name'];
$item_name = $_POST['item_name'];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');
$sql1 = "SELECT cat_id FROM $tablePrefix$databasetable where merchant_id = '$mId' and category_name = '$category_name' limit 1";
$result1 = $conn->query($sql1);

if ($result1->num_rows > 0) {
    while($r1 = $result1->fetch_assoc()) { 
	$response1[]= $r1['cat_id'];
	$cat_id = '["'.$response1[0].'"]';
    }
$sql = "SELECT addon_item FROM $tablePrefix$databasetable2 where merchant_id = '$mId'  and category = '$cat_id' and item_name = '$item_name' ";
$result = $conn->query($sql);

$response = array();

if ($result->num_rows > 0) {
    while($r = $result->fetch_assoc()) {
	$response[]= $r;
$data = $r['addon_item'];
$data = json_decode($data, true);
$data = (array) $data;

        foreach ($data as $key => $value) {
            $ffOutput = array();

        $subcat_id = '["'.$key.'"]';
            $ids = join("','",$value);

            $sql2 = "SELECT sub_item_name, IFNULL(NULLIF(price, ''), 0.00) as 'price' FROM $tablePrefix$databasetable3 where merchant_id = '$mId'  and category = '$subcat_id' and sub_item_id IN('$ids')";

        $result2 = $conn->query($sql2);
          while($r2 = $result2->fetch_assoc()) {
            $response2[]= $r2;
        }
        array_push($ffOutput, $response2);

        }
    }

print_r(json_encode($ffOutput[0]));
} 
}
else {
    echo "1";
}

$conn->close();
?>