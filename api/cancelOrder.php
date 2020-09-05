<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "epos_order";
$country_code = $config['countryprefixcode'];

$oId= $_POST['order_id'];
$mId = $_POST['mId'];
$cReason = $_POST['cReason'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//else {echo $_POST["a"];};
$datafinal = "epos_order_details";
$conn->set_charset('utf8');
$sql = "UPDATE $tablePrefix$databasetable set total_w_tax = '0.00' where order_id_epos = '$oId' and merchant_id = '$mId' and isCleared <> 'true' ";
$sql0 = "UPDATE $tablePrefix$databasetable set cReason = '$cReason' where order_id_epos = '$oId' and merchant_id = '$mId' and isCleared <> 'true' ";
$sql1 = "UPDATE $tablePrefix$datafinal set price = '0' where order_id = '$oId' and merchant_id = '$mId' and isCleared <> 'true' ";

$result0 = $conn->query($sql0);
$result = $conn->query($sql);
$result1 = $conn->query($sql1);

echo $result1;
echo $result;

$conn->close();
?>