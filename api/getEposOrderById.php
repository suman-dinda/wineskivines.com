<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "epos_client";
$databasetable2 = "epos_order_details";
$country_code = "+".$config['countryprefixcode'];

$mId = $_POST['mId'];
$oId = $_POST['oId'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//else {echo $_POST["a"];};
$conn->set_charset('utf8');

$sql = "SELECT t1.order_id, t2.client_id, concat(t2.first_name, ' ', t2.last_name) as cName, t2.street, t2.city, t2.state, t2.zipcode, replace(t2.contact_phone, '$country_code', '0') as cPhone, t1.item_name, replace(t1.addon,',','+') as addon, t1.price AS price, t1.size as size FROM $tablePrefix$databasetable2 t1 join $tablePrefix$databasetable t2  on t1.client_id = t2.client_id where t1.merchant_id = '$mId' and t1.order_id = '$oId' and t1.isCleared <> 'true' order by t1.order_id asc";
$result = $conn->query($sql);
//$response = array();

if ($result->num_rows > 0) {
    while($r = $result->fetch_assoc()) {
	$response[]= $r;
    }
$output = json_encode($response);
  echo $output;

} else {
    echo "0 results";
}

$conn->close();
?>