<?php
include_once("php/connect.php");
include_once("php/functions.php");
if (!isset($_SESSION['user_id'])) {
	header('Location: index.php');
}
$user = $_SESSION['user_id'];
$room = $_SESSION['room_id'];
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

/*
function show() {
	document.getElementById('taxtip').style.display = 'block';
	var element = document.getElementById('addtaxtip');
	element.innerHTML = 'Hide tax and tip';
	element.onclick = 'hide();';
}

function hide() {
	document.getElementById('taxtip').style.display = 'none';
	var element = document.getElementById('addtaxtip');
	element.innerHTML = 'Add tax and tip separaetly';
	element.onclick = 'show();';
}*/
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
            	<a href="addbill.php" class="selected">Add Bill</a>
            	<a href="addpayment.php">Add Payment</a>
        	</div>

	        <div id="body" class="roundCorner">
	        	<?php 
	        		$roommates = getRoommates($room);
	        		$options = '';
	        		for ($i = 0; $i < count($roommates); $i++) {
	        			$name = getName($roommates[$i]);
	        			$options .= '<option id="op'.$roommates[$i].'" value="'.$roommates[$i].'">'.$name.'</option>';
	        		}
	        		
	        		$inputs = '';
	        		for ($i = 0; $i < count($roommates); $i++) {
	        			$name = getName($roommates[$i]);
	        			$inputs .= '<label id="n'.$i.'" title="'.$name.'"><input type="checkbox" class="checkbox" name="'.$i.'check" id="'.$i.'check" onclick="evenSplit();"/>
	        				'.$name.'</label>
	        				<input type="text" class="roommates" name="'.$i.'" id="'.$i.'" onchange="formatDecimal(this);" onkeyup="reviewAmounts();" />';
	        		}
	        		
	        		echo '<form action="php/addtransaction.php" method="get">';
	        		echo '<span id="formLeft">
					<span style="margin-bottom: 14px;display: block;"><strong>Bill details</strong></span>
	        		<input type="hidden" name="type" id="type" value="bill" />
	        		<label><span id="leftLabel">Purchaser</span></label><select name="purchaser" id="purchaser">'.$options.'</select>
	        		<label><span id="leftLabel">Item</span></label><input type="text" name="item" id="item"/>
	        		<div class="error">'.$_SESSION['errors']['item'].'</div>
	        		<label><span id="leftLabel">Total</span></label><input type="text" name="total" id="total" onchange="formatDecimal(this); reviewAmounts();"/>
	        		<div class="error">'.$_SESSION['errors']['total'].'</div>
	        		<button type="button" class="submit" onclick="checkAll(); evenSplit();">Split evenly</button>';
	        		//<a class="small" id="addtaxtip" onclick="show();">Add tax and tip separately</a>
	        		//<span id="taxtip" style="display: none;"><label>Tax</label><input type="text" name="tax" id="tax" onchange="formatDecimal(this);" />
	        		//<label>Tip</label><input type="text" name="tip" id="tip" onchange="formatDecimal(this);" /></span>
	        		echo '</span>
	        		<span id="formRight">
				<strong>Expense split</strong>
	        		'.$inputs.'
	        		<input type="hidden" name="roomid" id="roomid" value="'.$room.'" /></span>
	        		<span id="reviewSpan"></span></form>';
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