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

$cPhone = $_POST['cPhone'];
$mId =  $_POST['mId'];
 
if($cPhone[0]=="0"){
$cPhone = substr_replace($cPhone, '+'.$country_code, 0, ($cPhone[0] == '0'));
}
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//else {echo $_POST["a"];};
$conn->set_charset('utf8');

$sqlEpos = "SELECT t1.client_id AS cId, CONCAT(t1.first_name,' ',t1.last_name) AS cName, t1.email_address, t1.street, t1.zipcode, t1.city, t1.state, t1.cNotes, t1.createdBy, t1.date_created as dCreated from $tablePrefix$databasetable t1 JOIN $tablePrefix$databasetable2 t2 ON  t1.client_id = t2.client_id AND t2.merchant_id = '$mId' where t1.contact_phone = '$cPhone' and t2.merchant_id = '$mId' " ;

$resultEpos = $conn->query($sqlEpos);

$createdEposSql = "SELECT client_id AS cId, first_name AS cName, street, zipcode, city, state, cNotes, createdBy, date_created as dCreated from $tablePrefix$databasetable where contact_phone = '$cPhone' and createdBy = '$mId'";

$resultcreatedEposSql = $conn->query($createdEposSql);

if($resultEpos->num_rows > 0 )
{
   while($row = $resultEpos->fetch_assoc()) {


        $cId = $row["cId"];
        $cName = $row["cName"];
        $email_address = $row["email_address"];
        $street = $row["street"];
        $zipcode = $row["zipcode"];
        $city = $row["city"];
        $notes= $row["cNotes"];
        $state = $row["state"];
        
$dCreated = $row["dCreated"];

$newSql  = "SELECT sum(total_w_tax) AS totalIncome, count(client_id) AS totalOrders, MIN(total_w_tax) AS minOrders, MAX(total_w_tax) as maxOrders, avg(total_w_tax) as averageOrders from $tablePrefix$databasetable2 where client_id = '$cId' and merchant_id = '$mId' ";

$newResult = $conn->query($newSql);

  while($newRow = $newResult->fetch_assoc()) {
    $totalIncome= round($newRow["totalIncome"], 2);
    $totalOrders= $newRow["totalOrders"];
    $minOrders= round($newRow["minOrders"], 2);
    $maxOrders= round($newRow["maxOrders"], 2);
    $averageOrders= round($newRow["averageOrders"], 2);
       }
    }


$response = array("cName"=>$cName, "cid"=>$cId, "emailAddress"=>$email_address, "street"=>$street, "city"=>$city, "notes"=>$notes, "date_created"=>$dCreated, "zipcode"=>$zipcode, "averageOrders"=>$averageOrders,  "state"=>$state, "totalIncome"=>$totalIncome, "totalOrders"=>$totalOrders, "minOrders"=>$minOrders, "maxOrders"=>$maxOrders, "status"=>"ok", "cType"=>"existing");
    $response = json_encode($response);
    echo $response;

}

else if($resultcreatedEposSql->num_rows > 0)
{

   while($row = $resultcreatedEposSql->fetch_assoc()) {


        $cId = $row["cId"];
        $cName = $row["cName"];
        $email_address = $row["email_address"];
        $street = $row["street"];
        $zipcode = $row["zipcode"];
        $city = $row["city"];
        $notes= $row["cNotes"];
        $state = $row["state"];
		$dCreated = $row["dCreated"];
    $totalIncome= "0.00";
    $totalOrders= "0.00";
    $minOrders= "0.00";
    $maxOrders= "0.00";
    $averageOrders= "0.00";
    }


$response = array("cName"=>$cName, "cid"=>$cId, "emailAddress"=>$email_address, "street"=>$street, "city"=>$city, "notes"=>$notes, "date_created"=>$dCreated, "zipcode"=>$zipcode, "averageOrders"=>$averageOrders,  "state"=>$state, "totalIncome"=>$totalIncome, "totalOrders"=>$totalOrders, "minOrders"=>$minOrders, "maxOrders"=>$maxOrders, "status"=>"ok", "cType"=>"existing");
    $response = json_encode($response);
    echo $response;
}

else if($resultcreatedEposSql->num_rows < 1) 
{ 
    $response = array("status"=>"ok", "cType"=>"new");
    $response = json_encode($response);
    echo $response;
}
$conn->close();
?>