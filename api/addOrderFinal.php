<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "epos_order_details";
$country_code = $config['countryprefixcode'];

date_default_timezone_set('Europe/Berlin');

$client_id = $_POST['client_id'];
$mId =  $_POST['mId'];
$order_id = $_POST['order_id'];
$dTime = $_POST['dTime'];
$delivery_date = date("Y-m-d");
$time_created = date("h:i:s");
$date_created = $delivery_date;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');

$getTotalSql = "select round(sum(price), 2) as total_w_tax from $tablePrefix$databasetable where client_id='$client_id' and order_id='$order_id' and merchant_id='$mId'  ";

$result = $conn->query($getTotalSql);
while($row = $result->fetch_assoc()) {
        $total_w_tax = $row["total_w_tax"];
}
$payment_type="cod";
$isCleared = "0";

$datafinal = "epos_order";
$sql = "INSERT INTO $tablePrefix$datafinal (order_id_epos, client_id, delivery_date, delivery_time, date_created, time_created, total_w_tax, merchant_id, payment_type) VALUES ('$order_id', '$client_id', '$delivery_date', '$dTime', '$date_created', '$time_created', '$total_w_tax', '$mId', '$payment_type')";
$result1 = $conn->query($sql);
$conn->close();
?>