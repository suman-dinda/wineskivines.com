<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "epos_order";
$databasetable1 = "epos_order_details";

$country_code = $config['countryprefixcode'];


$oId= $_POST['order_id'];
$mId = $_POST['mId'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//else {echo $_POST["a"];};
$conn->set_charset('utf8');

$sql = "UPDATE $tablePrefix$databasetable set total_w_tax = '0.00' where order_id_epos = '$oId' and merchant_id = '$mId' and isCleared <> 'true' ";
$sql1 = "UPDATE $tablePrefix$databasetable1 set price = '0.00' where order_id = '$oId' and merchant_id = '$mId' and isCleared <> 'true' ";
$result = $conn->query($sql);
$result1 = $conn->query($sql1);

echo $result;
echo $result1;

$conn->close();
?>