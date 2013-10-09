<?php
include_once("connect.php");
include_once("functions.php");
include_once("recommendedpayment.php");

$room = $_GET['roomid'];
$roommates = getRoommates($room);
$type = $_GET['type'];

$errors = array();

$date = time();
if ($type == 'bill') {
	$purchaser = $_GET['purchaser'];
	$item = ucfirst($_GET['item']);
	$total = $_GET['total'];
	$split = array();
	
	if ($item == '') $errors['item'] = 'The item must have a name';
	if ($total == '') $errors['total'] = 'You must provide the total';
	if (count($errors) == 0) {
		for ($i = 0; $i < count($roommates); $i++) {
			$userid = $roommates[$i];
			$amount = $_GET[$i]; //This is the expense to be allocated to this user
			$balance = getBalance($roommates[$i]);
			if ($userid == $purchaser) {
				$newBalance = $balance - $amount + $total;
			} else {
				$newBalance = $balance - $amount;
			}
	
			$split[$userid] = $amount;
				
			$query = "UPDATE users SET balance='$newBalance' WHERE id='$userid'";
			mysql_query($query) or die(mysql_error());
		}
		
		$stringSplit = serialize($split);
		$query = "INSERT INTO transactions (roomid, type, item, total, date, purchaser, split) VALUES ('$room', '$type', '$item', '$total', '$date', '$purchaser', '$stringSplit')";
		mysql_query($query) or die(mysql_error());
	
		header("Location: ../summary.php");
	} else {
		$_SESSION['errors'] = $errors;
		$_SESSION['values'] = $values;
		header("Location: ../addbill.php");
	}
} else if ($type == 'payment') {
	$payer = $_GET['payer'];
	$recipient = $_GET['recipient'];
	$amount = $_GET['amount'];
	$item = 'Payment to '.getName($recipient);
	
	if ($payer == $recipient) $errors['recipient'] = "You can't pay yourself.";
	if ($recipient == 0) $errors['recipient'] = "Who did you pay?";
	if ($amount == '') $errors['amount'] = "How much did you pay?";
	if (count($errors) == 0) {
		$split = $recipient.','.$amount;
		updatePaymentBalance($payer, $recipient, $amount);
		$query = "INSERT INTO transactions (roomid, type, item, total, date, split, payer, recipient) VALUES ('$room', '$type', '$item', '$amount', '$date', '$split', '$payer', '$recipient')";
		mysql_query($query) or die(mysql_error());
		
		header("Location: ../summary.php");
	} else {
		$_SESSION['errors'] = $errors;
		$_SESSION['values'] = $amount;
		header("Location: ../addpayment.php?go=roommate");
	}
} else if ($type == 'balance') {
	$payer = $_GET['payer'];
	for ($i=0; $i<count($roommates); $i++) {
		$recipient = $roommates[$i];
		if ($payer != $recipient) {
			$payment = getPayment($payer, $recipient);
			if ($payment < 0) {
				$payment = round(abs($payment),2);
				$item = 'Payment to '.getName($recipient);
				$split = $recipient.','.$payment;
				updatePaymentBalance($payer, $recipient, $payment);
				$query = "INSERT INTO transactions (roomid, type, item, total, date, split, payer, recipient) VALUES ('$room', 'payment', '$item', '$payment', '$date', '$split', '$payer', '$recipient')";
				mysql_query($query) or die(mysql_error());
			}
		}
	}
	header("Location: ../summary.php");
}

function updatePaymentBalance($payer, $recipient, $amount) {
	$payerBalance = getBalance($payer);
	$newPayerBalance = $payerBalance + $amount;
	$recipientBalance = getBalance($recipient);
	$newRecipientBalance = $recipientBalance - $amount;
	
	$query = "UPDATE users SET balance='$newPayerBalance' WHERE id='$payer'";
	mysql_query($query) or die(mysql_error());
	$query = "UPDATE users SET balance='$newRecipientBalance' WHERE id='$recipient'";
	mysql_query($query) or die(mysql_error());
}
?>