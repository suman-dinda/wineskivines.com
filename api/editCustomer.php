<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "epos_client";
$country_code = $config['countryprefixcode'];

$first_name = $_POST['first_name'];
$street = $_POST['street'];
$city = $_POST['city'];
$state = $_POST['state'];
$zipcode = $_POST['zipcode'];
$Notes = $_POST['Notes'];
$client_id = $_POST['client_id'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//else {echo $_POST["a"];};
$conn->set_charset('utf8');
$sql = "UPDATE $tablePrefix$databasetable set first_name = '$first_name', street = '$street', city = '$city', state = '$state' , zipcode = '$zipcode', cNotes = '$Notes' where client_id = '$client_id'";

$result1 = $conn->query($sql);

echo $result1;

$conn->close();
?>