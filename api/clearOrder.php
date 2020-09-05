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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');
$datafinal = "order_details";
$sql = "UPDATE $tablePrefix$databasetable set isCleared = 'true' where order_id_epos = '$oId' and merchant_id = '$mId' and isCleared <> 'true' ";
$sql1 = "UPDATE $tablePrefix$datafinal set isCleared = 'true' where order_id = '$oId' and merchant_id = '$mId' and isCleared <> 'true' ";

$result = $conn->query($sql);
$result1 = $conn->query($sql1);

echo $result1;
echo $result;

$conn->close();
?>