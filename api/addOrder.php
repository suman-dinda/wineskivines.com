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


$client_id = $_POST['client_id'];
$mId =  $_POST['mId'];
$order_id = $_POST['order_id'];
$item_name = $_POST['item_name'];
$addon = $_POST['addon'];
$price = str_replace( ',', '.', $_POST['price']);
$size = $_POST['size'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$conn->set_charset('utf8');
$sql = "INSERT INTO $tablePrefix$databasetable (id, order_id, client_id, item_name, addon, price, merchant_id, size) VALUES (NULL, '$order_id', '$client_id', '$item_name', '$addon', '$price', '$mId', '$size')";
$result = $conn->query($sql);
echo $result;
$conn->close();
?>