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

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');
$phoneNo = $_POST['phoneNo'];
$mId = $_POST['mId'];
if($phoneNo[0]=='0')
{
$phoneNo = substr_replace($phoneNo, '+'.$country_code, 0, ($phoneNo[0] == '0'));
}
else{
$phoneNo = "+".$country_code . $phoneNo;
}
date_default_timezone_set('Europe/London');
$cTime = date("Y-m-d H:i:s"); 

$sql = "INSERT INTO $tablePrefix$databasetable (date_created, contact_phone, createdBy) VALUES ('$cTime','$phoneNo', '$mId')";

$result = $conn->query($sql);

$ssql = "SELECT client_id FROM $tablePrefix$databasetable where  contact_phone= '$phoneNo' ORDER BY client_id DESC LIMIT 1";
$resultt = $conn->query($ssql);

if ($resultt->num_rows > 0) {
    while($row = $resultt->fetch_assoc()) {
        $client_id = $row["client_id"];
    }
}
echo $client_id;
$conn->close();
?>