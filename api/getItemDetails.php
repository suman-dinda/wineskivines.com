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
$country_code = $config['countryprefixcode'];

$mId = $_POST['mId'];
$category_name = $_POST['category_name'];

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
$sql = "SELECT item_name, item_id FROM $tablePrefix$databasetable2 where merchant_id = '$mId'  and category = '$cat_id' ";
$result = $conn->query($sql);
$conn->set_charset('utf8');

$response1 = array();

if ($result->num_rows > 0) {
    while($r = $result->fetch_assoc()) {
	$response[]= $r;
    }

  echo json_encode($response);
} }
else {
    echo "1";
}

$conn->close();
?>