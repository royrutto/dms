<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "admin123";
$dbname = "madawa";

$mysqli = new mysqli($host, $user, $pass, $dbname) or die(mysqli_error());

class Drugs
{
	public $drugid;
	public $drugname;
	public $description; 
	public $strength;
	public $form;
	public $route;
	public $manufacturer;
	public $purchaseprice;
	public $sellingprice;
	public $expirydate;
	public $quantity;
	
	public static function getObject($record)
	{
		$drug = new Drugs;
		
		$drug->drugid = $record['drug_id'];
		$drug->drugname = $record['name'];
		$drug->description = $record['description'];
		$drug->strength = $record['strength'];
		$drug->form = $record['form'];
		$drug->route = $record['route'];
		$drug->manufacturer = $record['manufucture'];
		$drug->purchaseprice = $record['purchaseprice'];
		$drug->sellingprice = $record['sellingprice'];
		$drug->expirydate = $record['expirydate'];
		$drug->quantity = $record['quantity'];
		
		return $drug;
	}	
}

class Sale
{
	public $sale_id;
	public $drug_id;
	public $quantity;
	public $date_of_sale;

	public static function getObject($record)
	{
		$sale = new Sale;

		$drug->sale_id = $record['sale_id'];
		$drug->drug_id = $record['drug_id'];
		$drug->quantity = $record['quantity'];
		$drug->date_of_sale = $record['date_of_sale'];

		return $drug;
	}
}

function checkLogin($username, $password){
	global $mysqli;
	$password = md5($password);
	
	$sql = "select * from users where username = '".$username."' and password = '".$password."'";
	$res = $mysqli->query($sql);
	$row = $res->fetch_assoc();
	$_SESSION['dms-role'] = $row['role'];
	if (!empty($row)){
		return true;	
	}else 
		return false;
}

function getDrug($drugid){
	global $mysqli;
	
	$sql = "select * from drugs where drug_id = ".$drugid;	
	$res = $mysqli->query($sql);
	$row = $res->fetch_assoc();
	
	$drugs = Drugs::getObject($row);
	
	return json_encode($drugs);
}

function getDrugByName($drug_name){
	global $mysqli;
	
	$sql = "SELECT * FROM drugs where NAME = '".$drug_name."'";
	
	$res = $mysqli->query($sql);
	$row = $res->fetch_assoc();
	
	$drug = Drugs::getObject($row);
	
	return json_encode($drug);
}

function getDrugsList(){
		
	global $mysqli;
	
	$sql = "select * from drugs";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;	
	}
	
	return json_encode($results);
}

function getAllDrugs(){

	global $mysqli;

	$sql = "select * from drugs";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;
	}

	return $results;
}

function getLowStockDrugs(){

	global $mysqli;

	$sql = "select * from drugs where quantity <=10";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;
	}

	return $results;
}

function getAllRequisitions(){

	global $mysqli;

	$sql = "select * from requisition req, drugs d where req.drug_id = d.drug_id";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;
	}

	return $results;
}

function getProcessedRequisitions(){

	global $mysqli;

	$sql = "select * from requisition req, drugs d where req.drug_id = d.drug_id and processed = 1";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;
	}

	return $results;
}

function getPaidRequisitions(){

	global $mysqli;

	$sql = "select * from requisition req, drugs d where req.drug_id = d.drug_id and processed = 1 and paid = 1";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;
	}

	return $results;
}

function getUnPaidRequisitions(){

	global $mysqli;

	$sql = "select * from requisition req, drugs d where req.drug_id = d.drug_id and processed = 1 and paid = 0";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;
	}

	return $results;
}

function getAllSales(){

	global $mysqli;

	$sql = "select *, s.quantity as qs from sales s, drugs d where s.drug_id = d.drug_id";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;
	}

	return $results;
}


function processRequisition($req_id){

	global $mysqli;

	$sql = "UPDATE requisition SET processed = 1 where req_id = $req_id";

	$res = $mysqli->query($sql);
	if ($res)
		echo 1;
	else 
		echo 0;
}

function processPayment($req_id, $amount){

	global $mysqli;

	$sql = "UPDATE requisition SET paid = 1,  total_amount= $amount where req_id = $req_id";

	$res = $mysqli->query($sql);
	if ($res){
		
		$sql = "SELECT drug_id, quantity_ordered FROM requisition WHERE req_id = $req_id";
		$sql;
		$res = $mysqli->query($sql);
		$row = $res->fetch_assoc();
		$drug_id = $row['drug_id'];
		$amount_ordered = $row['quantity_ordered'];
		
		$sql = "UPDATE drugs SET quantity = quantity + $amount_ordered WHERE drug_id = $drug_id";
		
		$res = $mysqli->query($sql);
		echo 1;
	}
	else
		echo 0;
}

function reduceDrugQuantity($drug_id, $quantity){
		
	global $mysqli;

	$sql = "UPDATE drugs SET quantity = (quantity - ".$quantity.") WHERE drug_id =".$drug_id;

	$res = $mysqli->query($sql);
}

function getSale($sale_id){
	global $mysqli;
	
	$sql = "SELECT * FROM sales WHERE sale_id = ".$sale_id;	
	$res = $mysqli->query($sql);
	$row = $res->fetch_assoc();
	
	$sale = Sale::getObject($row);
	
	return json_encode($drugs);
}

function getSales(){
		
	global $mysqli;
	
	$sql = "SELECT * FROM sales ";

	$res = $mysqli->query($sql);
	$results = array();
	while($row = $res->fetch_assoc()){
		$results[] = $row;	
	}
	
	return json_encode($results);
}

function addSale($drug_id, $quantity){
	global $mysqli;
	$sql = "INSERT INTO sales (drug_id, quantity, date_of_sale) VALUES(".$drug_id.",".$quantity.", '".date("Y-m-d H:i:s")."')";
	$res = $mysqli->query($sql);
	if($res){
		reduceDrugQuantity($drug_id, $quantity);
		return 1;
	}
	return 0;
}
function addRequisition($drug_id, $quantity_requisitioned){
	global $mysqli;
	//new
	$sql = "INSERT INTO requisition (drug_id, quantity_ordered, date_ordered) VALUES(".$drug_id.",".$quantity_requisitioned.",'".date("Y-m-d H:i:s")."')";
	$res = $mysqli->query($sql);
	if($res){
		reduceDrugQuantity($drug_id, $quantity);
		return 1;
	}
	return 0;
}

function addDrug($drug_name, $description, $purchaseprice, $sellingprice, $strength){
	global $mysqli;
	//new
	$sql = "INSERT INTO drugs (name, description, purchaseprice, sellingprice, strength) 
			VALUES('".$drug_name."','".$description."','".$purchaseprice."','".$sellingprice."','".$strength."')";
	$res = $mysqli->query($sql);
	if($res){
		reduceDrugQuantity($drug_id, $quantity);
		return 1;
	}
	return 0;
}

if(isset($_REQUEST['dn'])){
	echo getDrugByName($_REQUEST['dn']);
}

if(isset($_REQUEST['di']) && isset($_REQUEST['q'])){
	echo addSale($_REQUEST['di'],$_REQUEST['q']);
}

if(isset($_REQUEST['di']) && isset($_REQUEST['qr'])){
	echo addRequisition($_REQUEST['di'],$_REQUEST['qr']);
}

if(isset($_REQUEST['add_drug']) && $_REQUEST['add_drug']=='1'){
	echo addDrug($_REQUEST['dn'], $_REQUEST['dd'], $_REQUEST['pp'], $_REQUEST['sp'], $_REQUEST['s']);
}

if(isset($_REQUEST['pr']) && $_REQUEST['pr']=='1'){
	echo processRequisition($_REQUEST['req']);
}

if(isset($_REQUEST['pay']) && $_REQUEST['pay']=='1'){
	echo processPayment($_REQUEST['req'], $_REQUEST['amount']);
}

?>
