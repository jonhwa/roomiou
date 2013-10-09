<?php
include_once("connect.php");

/****ROOM FUNCTIONS****/
//Get an array of roommate IDs for a room given 
function getRoommates($roomid) {
	$query = "SELECT roommates FROM rooms WHERE id='".$roomid."'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$roommates = $row['roommates'];
	$roommates = trim($roommates,',');
	$arr = explode(",", $roommates);
	return $arr;
}

//Get the room's name given its ID
function getRoomName($roomid) {
	$query = "SELECT name FROM rooms WHERE id='".$roomid."'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$name = $row['name'];
	return $name;
}

/****USER FUNCTIONS****/
//Get user's name by user ID
function getName($userid) {
	$query = "SELECT firstname, lastname FROM users WHERE id='$userid'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$name = $row['firstname'].' '.$row['lastname'];
	return $name;	
}

//Get user's balance by ID
function getBalance($userid) {
	$query = "SELECT balance FROM users WHERE id='$userid'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$balance = $row['balance'];
	return $balance;	
}

/****TRANSACTION FUNCTIONS****/
//Get transaction's item by transaction ID
function getItem($transactionid) {
	$query = "SELECT item FROM transactions WHERE id='$transactionid'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$item = $row['item'];
	return $item;
}

//Get transaction's total by transaction ID
function getTotal($transactionid) {
	$query = "SELECT total FROM transactions WHERE id='$transactionid'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$total = $row['total'];
	return $total;	
}

function getPurchaser($transactionid) {
	$query = "SELECT purchaser FROM transactions WHERE id='$transactionid'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$purchaser = $row['purchaser'];
	return $purchaser;	
}

//Get and parse transaction split by transaction ID
function getSplit($transactionid) {
	$query = "SELECT split FROM transactions WHERE id='$transactionid'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$string = $row['split'];
	$split = unserialize($string);
	return $split;
}

function sumBalance($roomid) {
	$balanceArr = array();
	
	$query = "SELECT id, purchaser, total FROM transactions WHERE roomid='$roomid'";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		//Sum expenses
		$splitarr = getSplit($row['id']);
		foreach ($splitarr as $key => $value) {
			$balanceArr[$key] -= $value;
		}
		
		//Add back purchase total
		$purchaser = $row['purchaser'];
		$total = $row['total'];
		$balanceArr[$purchaser] += $total;
	}
	
	return $balanceArr;
}
?>