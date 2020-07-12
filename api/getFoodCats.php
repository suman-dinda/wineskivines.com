<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "category";
$country_code = $config['countryprefixcode'];

$mId = $_POST['mId'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');
$sql = "SELECT cat_id, category_name FROM $tablePrefix$databasetable where merchant_id = '$mId'";
$result = $conn->query($sql);
$response = array();

if ($result->num_rows > 0) {
    while($r = $result->fetch_assoc()) {
	$response[]= $r;
	}
    echo json_encode($response);
} else {
    echo "0 results";
}
$conn->close();
?>