<?php
include 'config/database.php';
header("Content-Type: text/html; charset=utf-8");
$servername = $config['DB_HOST'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_DATABASE'];
$tablePrefix = $config['tablePrefix'];
$databasetable = "category";
$databasetable2 = "item";
$databasetable3 = "size";

$country_code = $config['countryprefixcode'];

$mId = $_POST['mId'];
$category_name = $_POST['category_name'];
$item_name = $_POST['item_name'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset('utf8');
$sql1 = "SELECT cat_id FROM $tablePrefix$databasetable where merchant_id = '$mId' and category_name = '$category_name' limit 1";
$result1 = $conn->query($sql1);
if ($result1->num_rows > 0) {
    while($r1 = $result1->fetch_assoc()) { 
	$response1[]= $r1['cat_id'];
	$cat_id = '["'.$response1[0].'"]';
    }
$sql = "SELECT price FROM $tablePrefix$databasetable2 where merchant_id = '$mId'  and category = '$cat_id' and item_name = '$item_name'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($r = $result->fetch_assoc()) {
	$response[]= $r;

$number = json_decode($response[0]['price']);
$output = array();
foreach ($number as $key => $value) {
            $sqlF = "SELECT size_name FROM $tablePrefix$databasetable3 where merchant_id = '$mId'  and size_id = '$key' ";
            $resultF = $conn->query($sqlF);
            if ($resultF->num_rows > 0) {
               
                 while($r = $resultF->fetch_assoc()) {

                    foreach ($r as $keyF => $valueF) {
                       // $output = json_encode(array($valueF,$value));
                           //$output = array($valueF => $value);
                      array_push($output, array('size_name'=>$valueF, 'size_price' => $value));
                    }
                 }

                }else{
                // set default size label if none
                array_push($output, array('size_name'=>' ', 'size_price' => $value));
                }

                
            
}
//str_replace('[["','[\'"',$fResults);
//str_replace('"]]','"\']',$fResults);
echo json_encode($output);
    }
$output = str_replace( array('[',']') , ''  , $output);
} }
else {
    echo "1";
}
$conn->close();
?>