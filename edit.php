<?php
include_once("php/connect.php");
include_once("php/functions.php");
if (!isset($_SESSION['user_id'])) {
	header('Location: index.php');
}
$user = $_SESSION['user_id'];
$room = $_SESSION['room_id'];
$transactionid = $_GET['id'];
$type = $_GET['type'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RoomIOU</title>

<link rel="stylesheet" type="text/css" href="css/reset.css"/>
<link rel="stylesheet" type="text/css" href="css/960_24_col.css"/>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<link rel="stylesheet" type="text/css" href="css/summary.css"/>
<link rel="stylesheet" type="text/css" href="css/addtransaction.css"/>

<script src="js/addtransaction.js"></script>
<script src="js/ajax.js"></script>
</head>

<body>
<div id="wrapper">
    <div id="header">
        <div id="headerContainer" class="container_24">
        	<a href="summary.php"><img src="img/Roomioularge.png" /></a>
            <div id="headerContent">
                <a href="settings.php">Settings</a>
                <a href="feedback.php">Feedback</a>
            	<a href="php/logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <div id="bodyWrapper" class="container_24">
	<div class="grid_6">
	<div id="sidebody">
            <div id="sidebodyContent">
            	<h3></h3>
                <ul>
                <?php
					$roommates = getRoommates($room);
					for ($i=0; $i<count($roommates); $i++) {
						$uid = $roommates[$i];
						$name = getName($uid);
						$balance = number_format(getBalance($uid), 2);
					if ($balance < 0) {
							$balance = substr_replace($balance, "", 0, 1);
							echo '<li class="red"><p class="name">'.$name.'</p><p><span class="small">owes</span> $'.$balance.'</p></li>';
						} else if ($balance > 0) {
							echo '<li class="green"><p class="name">'.$name.'</p><p><span class="small">gets back</span> $'.$balance.'</p></li>';	
						} else {
							echo '<li class="grey"><p class="name">'.$name.'</p><p><span class="small">is even</span> $'.$balance.'</p></li>';	
						}
					}
				?>
                </ul>

                <button type="button" id="invitebutton" onclick="window.location.href='settings.php?go=room'">Invite Roommates</button>
	    </div>
	</div>
	<div id="feedback"><a href="feedback.php">Send us feedback!</a></div>
	</div>
		<div class="grid_18">
	   		<div id="nav" class="roundCorner">
            		<a href="summary.php">Summary</a>
            		<a href="addbill.php">Add Bill</a>
			<a href="addpayment.php">Add Payment</a>
        	</div>

       <div id="body" class="roundCorner">
	   		<div id="edittitle">Edit Transaction
	   		<span class="roomSmall" style="font-family: Tahoma, Arial;" onclick="deleteTransaction(document.getElementById('id').value);">Delete transaction</span>
	   		</div>
	   		
            <form action="php/edit.php" method="post">
            <?php
            $roommates = getRoommates($room);
            if ($type == 'bill') {
				echo'<span id="formLeft">
					<span style="margin-bottom: 14px;display: block;"><strong>Bill details</strong></span>
				     <input type="hidden" name="type" id="type" value="bill" />
				     <input type="hidden" name="id" id="id" value="'.$transactionid.'"/>
				     <label><span id="leftLabel">Purchaser</span></label><select id="purchaser" name="purchaser">';
				for ($i = 0; $i < count($roommates); $i++) {
					$userid = $roommates[$i];
					$name = getName($userid);
					if ($userid == getPurchaser($transactionid)) {
						echo '<option selected="selected" id="op'.$userid.'" value='.$userid.'>'.$name.'</option>';
					} else {
						echo '<option id="op'.$userid.'" value='.$userid.'>'.$name.'</option>';
					}
				}
                echo '</select>';
				echo '<label><span id="leftLabel">Item</span></label><input type="text" name="item" id="item" value="'.getItem($transactionid).'"/>';
				echo '<div class="error">'.$_SESSION['errors']['item'].'</div>';
				echo '<label><span id="leftLabel">Total</span></label><input type="text" name="total" id="total" value="'.number_format(getTotal($transactionid),2,'.','').'" onchange="formatDecimal(this);" onkeyup="reviewAmounts();" />';
				echo '<div class="error">'.$_SESSION['errors']['total'].'</div>';
				echo '<button type="button" class="submit" onclick="checkAll(); evenSplit();">Split evenly</button>';
				echo '</span>';
				
				//List all the roommates with their respective expense splits
				echo '<span id="formRight"><strong>Expense split</strong>';
				$splitArr = getSplit($transactionid);
				for ($i = 0; $i < count($roommates); $i++) {
					$userid = $roommates[$i];
					$name = getname($userid);
					echo '<label id="n'.$i.'" title="'.$name.'"><input type="checkbox" class="checkbox" name="'.$i.'check" id="'.$i.'check" onclick="evenSplit();"';
					
					//If the roommate was in the original split, print it here. If not, give a default value of zero
					if (array_key_exists($userid, $splitArr)) {
						$expense = $splitArr[$userid];
					} else {
						$expense = '0.00';
					}
					if ($expense > 0) echo 'checked="checked"';
					echo '/>'.$name.'</label><input type="text" class="roommates" id="'.$i.'" name="'.$i.'"';
					
					echo 'value="'.$expense.'" onchange="formatDecimal(this);" onkeyup="reviewAmounts();" />';
				}
				echo '</span>';
            } else if ($type == 'payment') {
            	$query = "SELECT total, payer, recipient FROM transactions WHERE id='$transactionid'";
            	$result = mysql_query($query) or die(mysql_error());
            	$row = mysql_fetch_assoc($result);
            	$payer = $row['payer'];
            	$recipient = $row['recipient'];
            	$amount = number_format($row['total'],2,'.','');
            	
            	echo '<input type="hidden" name="type" id="type" value="payment" />
            	<input type="hidden" name="id" id="id" value="'.$transactionid.'"/>
            	<label>Payer</label>
            	<select name="payer" id="payer">';
            	for ($i = 0; $i < count($roommates); $i++) {
            		$name = getName($roommates[$i]);
            		echo '<option id="op'.$roommates[$i].'" value="'.$roommates[$i].'"';
            		if ($roommates[$i] == $payer) echo 'selected="selected"';
            		echo '>'.$name.'</option>';
            	}
            	echo '</select><label>Recipient</label>
            	<select name="recipient" id="recipient">';
            	for ($i = 0; $i < count($roommates); $i++) {
            		$name = getName($roommates[$i]);
            		echo '<option id="op'.$roommates[$i].'" value="'.$roommates[$i].'"';
            		if ($roommates[$i] == $recipient) echo 'selected="selected"';
            		echo '>'.$name.'</option>';
            	}
            	echo '</select><div class="error">'.$_SESSION['errors']['recipient'].'</div>
            		<label>Amount</label><input type="text" name="amount" id="amount" onchange="formatDecimal(this);" value="'.$amount.'"/>
            		<div class="error">'.$_SESSION['errors']['amount'].'</div>
            		<button type="submit" name="submit" id="submit" class="submit">Edit Payment</button></form>';
            }
			?>
            <span id="reviewSpan"></span></span>
            </form>
        </div>
    </div>

	<div id="push"></div>
</div>

</body>
</html>