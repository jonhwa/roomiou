<?php
include_once("connect.php");
include_once("functions.php");

$roomid = $_SESSION['room_id'];

$id = $_POST['id'];
$type = $_POST['type'];

$query = "SELECT * FROM transactions WHERE id='$id'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_assoc($result);

$roommates = getRoommates($roomid);
if ($type == 'bill') {
	$currentSplit = unserialize($row['split']);
	$origPurchaser = $row['purchaser'];
	$origTotal = $row['total'];
	$purchaser = $_POST['purchaser'];
	$item = $_POST['item'];
	$total = $_POST['total'];
	$newSplit = array();
	
	for ($i = 0; $i < count($roommates); $i++) {
		$userid = $roommates[$i];
		$amount = $_POST[$i]; //This is the expense to be allocated to this user
		$balance = getBalance($roommates[$i]);
		$currentAmount = $currentSplit[$userid];
		$difference = 0;
		
		if ($origPurchaser != $purchaser) { //If the purchaser changed
			if ($userid == $origPurchaser) {
				$difference -= $origTotal;
			}
			if ($userid == $purchaser) {
				$difference += $total;	
			}	
		} else if ($origTotal != $total && $userid == $purchaser) { //If purchaser hasn't changed but the total has, only impacts purchaser
			$difference += $total - $origTotal;
		}
			
		$difference += $currentAmount - $amount;
		$newBalance = $balance + $difference;
		
		$newSplit[$userid] = $amount;
						
		$query = "UPDATE users SET balance='$newBalance' WHERE id='$userid'";
		mysql_query($query) or die(mysql_error());
	}

	$stringSplit = serialize($newSplit);
	$query = "UPDATE transactions SET item='$item', total='$total', purchaser='$purchaser', split='$stringSplit' WHERE id='$id'"; 
	mysql_query($query) or die(mysql_error());
} else if ($type == 'payment') {
	$origPayer = $row['payer'];
	$origRecipient = $row['recipient'];
	$origAmount = $row['total'];
	$payer = $_POST['payer'];
	$recipient = $_POST['recipient'];
	$amount = $_POST['amount'];
	
	$difference = $amount - $origAmount;
	if ($payer != $origPayer) {
		$difference = $difference + $origAmount;
		mysql_query("UPDATE users SET balance=balance-'$difference' WHERE id='$origPayer'") or die(mysql_error());
	}
	mysql_query("UPDATE users SET balance=balance+'$difference' WHERE id='$payer'") or die(mysql_error());
	
	$difference = $amount - $origAmount;
	if ($recipient != $origRecipient) {
		$difference = $difference + $origAmount;
		mysql_query("UPDATE users SET balance=balance+'$difference' WHERE id='$origRecipient'") or die(mysql_error());
	}
	mysql_query("UPDATE users SET balance=balance-'$difference' WHERE id='$recipient'") or die(mysql_error());
	
	$split = $recipient.','.$amount;
	mysql_query("UPDATE transactions SET total='$amount', payer='$payer', recipient='$recipient', split='$split' WHERE id='$id'") or die(mysql_error());
}
		
header("Location: ../summary.php");

?>