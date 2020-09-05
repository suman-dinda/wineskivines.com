<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "epos_client";
$databasetable2 = "epos_order";
$country_code = $config['countryprefixcode'];

$mId = $_POST['mId'];
$isCleared = "1";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');
$sql = "SELECT t1.order_id_epos, concat(t2.first_name, ' ', t2.last_name) as cName, CONCAT(t2.street, ' ',t2.city, ' ',t2.state, ' ',t2.zipcode) as cAddress, replace(t2.contact_phone, '+49', '0') as cPhone, CONCAT(t1.delivery_time,' ', date_format(t1.delivery_date, '%e/%c/%Y')) as dDate, date_format(CONCAT(t1.date_created,' ', t1.time_created), '%e/%c/%Y %T')  as cDate, t1.total_w_tax AS total FROM $tablePrefix$databasetable2 t1 join $tablePrefix$databasetable t2  on t2.client_id = t1.client_id where t1.merchant_id = '$mId' and t1.isCleared <> 'true' order by t1.order_id_epos asc";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($r = $result->fetch_assoc()) {
       
	$response[]= $r;
    }
$output = json_encode($response);
  echo $output;

} else {
    echo "0";
}

$conn->close();
?>