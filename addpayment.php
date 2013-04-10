<?php
include_once("php/connect.php");
include_once("php/functions.php");
if (!isset($_SESSION['user_id'])) {
	header('Location: index.php');
}
$user = $_SESSION['user_id'];
$room = $_SESSION['room_id'];
$go = $_GET['go'];
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

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="js/addtransaction.js"></script>
<script src="js/modernizr.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
$(function() {
	// check placeholder browser support
	if (!Modernizr.input.placeholder) {	    
		// set placeholder values
		$(this).find('[placeholder]').each(function(){
			if ($(this).val() == '') {
				$(this).val( $(this).attr('placeholder') );
			}
		});
				        
		// focus and blur of placeholders
		$('[placeholder]').focus(function(){
			if ($(this).val() == $(this).attr('placeholder')) {
				$(this).val('');
				$(this).removeClass('placeholder');
			}
		}).blur(function(){
			if ($(this).val() == '' || $(this).val() == $(this).attr('placeholder')) {
				$(this).val($(this).attr('placeholder'));
				$(this).addClass('placeholder');
			}
		});
				        
		// remove placeholders on submit
		$('[placeholder]').closest('form').submit(function() {
			$(this).find('[placeholder]').each(function() {
				if ($(this).val() == $(this).attr('placeholder')) {
					$(this).val('');
				}
			})
		});
	}
});

function displayExplanation() {
	var element = document.getElementById('explanation');
	var html = 'These person-to-person debts are calculated by looking at bills between the two of you. The net balance of these bills is how much is owed. This represents a fair way to pay individual roommates.';
	element.innerHTML = html;
}
</script>
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
            	<a href="addpayment.php" class="selected">Add Payment</a>
        	</div>

	        <div id="body" class="roundCorner">
	        	<?php 
	        	if ($go == '') {
	        		echo '<div id="settingbuttons"><button type="submit" class="submit" id="submit" onclick="window.location.replace(\'addpayment.php?go=roommate\')">Pay a Roommate</button>
	        		<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'addpayment.php?go=balance\')">Pay your Balance</button></div>';
	        	} else {
	        		$roommates = getRoommates($room);
	        		
	        		$inputs = '';
	        		for ($i = 0; $i < count($roommates); $i++) {
	        			$name = getName($roommates[$i]);
	        			$inputs .= '<label id="n'.$i.'">'.$name.'</label><input type="text" class="roommates" name="'.$i.'" id="'.$i.'" onchange="formatDecimal(this);" onkeyup="reviewAmounts();" />';
	        		}
	        		if ($go == 'roommate') {
						echo '<div id="settingbuttons"><button type="submit" class="submitted" id="submitted" onclick="window.location.replace(\'addpayment.php?go=roommate\')">Pay a Roommate</button>
	        				<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'addpayment.php?go=balance\')">Pay your Balance</button></div>';
						echo '<div id="paymentform"><form action="php/addtransaction.php" method="get">
		        				<input type="hidden" name="type" id="type" value="payment" />
				        		<label>Payer</label>
		        				<select name="payer" id="payer" onchange="recommendedPayment(this.value,document.getElementById(\'recipient\').value);">';
	        			for ($i = 0; $i < count($roommates); $i++) {
	        				$name = getName($roommates[$i]);
	        				echo '<option id="op'.$roommates[$i].'" value="'.$roommates[$i].'"';
	        				if ($roommates[$i] == $user) echo 'selected="selected"';
	        				echo '>'.$name.'</option>';
	        			}
			        	echo '</select><label>Recipient</label>
			        		<select name="recipient" id="recipient" onchange="recommendedPayment(document.getElementById(\'payer\').value,this.value);">
			        		<option value="0"></option>';
			        	for ($i = 0; $i < count($roommates); $i++) {
			        		$name = getName($roommates[$i]);
			        		echo '<option id="op'.$roommates[$i].'" value="'.$roommates[$i].'">'.$name.'</option>';
			        	}
			        	echo '</select>
			        		<div class="error">'.$_SESSION['errors']['recipient'].'</div>
			        		<label>Amount</label><input type="text" name="amount" id="amount" onchange="formatDecimal(this);" value="'.$_SESSION['values'].'"/>
			        		<div class="error">'.$_SESSION['errors']['amount'].'</div>
			        		<input type="hidden" name="roomid" id="roomid" value="'.$room.'" />
			        		<button type="submit" name="submit" id="submit" class="submit" value="Add Payment">Add Payment</button></form>
			        		<span id="reviewSpan"></span></div>';
	        		} else if ($go == 'balance') {
	        			echo '<div id="settingbuttons"><button type="submit" class="submit" id="submit" onclick="window.location.replace(\'addpayment.php?go=roommate\')">Pay a Roommate</button>
	        				<button type="submit" class="submitted" id="submitted" onclick="window.location.replace(\'addpayment.php?go=balance\')">Pay your Balance</button></div>';
						echo '<div id="paymentform"><form action="php/addtransaction.php" method="get">
	        				<input type="hidden" name="type" id="type" value="balance" />
	        				<label>Payer</label>
	        				<select name="payer" id="payer" onchange="recommendedBalance(this.value, '.$room.');">';
	        			for ($i = 0; $i < count($roommates); $i++) {
	        				$name = getName($roommates[$i]);
	        				echo '<option id="op'.$roommates[$i].'" value="'.$roommates[$i].'"';
	        				if ($roommates[$i] == $user) echo 'selected="selected"';
	        				echo '>'.$name.'</option>';
	        			}
		        		echo '</select><div id="roommates"></div>
		        			<input type="hidden" name="roomid" id="roomid" value="'.$room.'" />
	        				<button type="submit" name="submit" id="submit" class="submit">Pay All Debts</button></form>
	        				<script type="text/javascript">recommendedBalance(document.getElementById("payer").value,'.$room.');</script>';
	        		}
	        	}
	        	?>
	        </div>
		
		</div>
    </div>

	<div id="push"></div>
</div>
</body>
</html>
<?php 
unset($_SESSION['errors']);
?>