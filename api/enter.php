<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");

if($_POST['username'] != null && $_POST['password'] != null &&  $_POST['username'] != "" && $_POST['password'] != ""){
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "merchant";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');
$user = mysqli_real_escape_string($conn, $_POST['username']);
$pass = md5(mysqli_real_escape_string($conn, $_POST['password']));

$sql = "SELECT * FROM $tablePrefix$databasetable WHERE username='$user' AND password='$pass'";
$result = $conn->query($sql);

if ($result->num_rows != 1) {
	 $response = array("mName"=>"error", "status"=>"error", "mId"=>"0");
    $response = json_encode($response);
    echo $response;
	}
	else { 
    while($row = $result->fetch_assoc()) {
        $mStatus = $row["status"];
        $mName = $row["restaurant_name"];
        $mId = $row["merchant_id"];
        $street = $row["street"];
         $city = $row["city"];
        $post_code = $row["post_code"];
        $restaurant_phone = $row["restaurant_phone"];
        $taxId = $row["state"];
    }
    if($mStatus == "active"){
$response = array("mName"=>$mName, "taxId"=>$taxId,"status"=>"ok", "mId"=>$mId, "post_code"=>$post_code, "street"=>$street, "city"=>$city, "mPhone"=>$restaurant_phone);
    $response = json_encode($response);
    echo $response;
}
 else {
    $response = array("mName"=>"error", "status"=>"error", "mId"=>"0", "post_code"=>"0");
    $response = json_encode($response);
    echo $response;
		} 
	}
$conn->close(); 
}
?>
