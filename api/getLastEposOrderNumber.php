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

$mId = $_POST['mId'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');

$sql = "SELECT order_id_epos FROM $tablePrefix$databasetable where merchant_id = '$mId' and isCleared <> 'true' ORDER BY order_id_epos DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $lastOrder = $row["order_id_epos"];
    }
    echo $lastOrder;


} else {
    echo "0";
}

$conn->close();


?>