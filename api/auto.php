<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "view_merchant";
$country_code = $config['countryprefixcode'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');
$mId = mysqli_real_escape_string($conn, $_POST['mId']);
$sql = "SELECT lontitude, latitude FROM $tablePrefix$databasetable WHERE merchant_id='$mId'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
   while($roww = $result->fetch_assoc()) {
        $lat = $roww["latitude"];
        $lng = $roww["lontitude"];
    }
		$api_key = $config['googleapikey'];
        $query = $_POST["q"];
    	$url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input='.$query.'&types=address&location='.$lat.','.$lng.'&strictbounds&radius=30000&oe=utf8&sensor=false&hl=de&key='.$api_key;
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	$data1 = curl_exec($ch);
    	curl_close($ch);
    	$details = json_decode($data1, true);
    	header("Content-Type: application/json");
    
    	foreach($details['predictions'] as $key=>$row) {
    		$arr[] = array("value"=>$row['description']);
    	}
    	echo json_encode($arr);
}
else 
{
echo "hi";
}
?>