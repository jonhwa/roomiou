<?php
include_once("php/connect.php");
include_once("php/functions.php");
if (!isset($_SESSION['user_id'])) {
	header('Location: index.php');
}
$user = $_SESSION['user_id'];
$room = $_SESSION['room_id'];
$page = $_GET['go'];
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
<link rel="stylesheet" type="text/css" href="css/settings.css"/>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="js/modernizr.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
function remove(user, name, balance) {
	if (balance == 0) {
		var html = '<p>Are you sure you want to remove ' + name + ' from your room?</p>';
	} else {
		var html = '<p>If you remove ' + name + ' from your room, the balance of $' + balance + ' will be split among the remaining roommates evenly. Are you sure you want to proceed?</p>';
	}
	html += '<button type="submit" class="removesubmit" onclick="removeRoommate('+user+','+balance+')">Yes</button>';
	html += '<button type="submit" class="removesubmit" onclick="no()"> No</button>';
	document.getElementById('confirm').innerHTML = html;
}

function no() {
	document.getElementById('confirm').innerHTML = '';
}

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
</script>
</head>

<body>
<div id="wrapper">
    <div id="header">
        <div id="headerContainer" class="container_24">
        	<a href="summary.php"><img src="img/Roomioularge.png" /></a>
            <div id="headerContent">
                <a href="settings.php" class="selected">Settings</a>
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
			<?php 
				if ($page == '') {
					echo '<div id="settingbuttons"><div class="error">'.$_SESSION['success'].'</div>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=name\')">Change my Name or Email</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=pass\')">Change my Password</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=room\')">Manage my Room</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=email\')">Manage my Notifications</button></div>';
				} else if ($page == 'name') {
					$query = "SELECT * FROM users WHERE id='$user'";
					$result = mysql_query($query) or die(mysql_error());
					$row = mysql_fetch_assoc($result);
					$firstname = $row['firstname'];
					$lastname = $row['lastname'];
					$email = $row['email'];
					$password = $row['password'];
				    
					echo '<div id="settingbuttons"><div class="error">'.$_SESSION['success'].'</div>
						<button type="submit" class="submitted" id="submitted" onclick="window.location.replace(\'settings.php?go=name\')">Change my Name or Email</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=pass\')">Change my Password</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=room\')">Manage my Room</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=email\')">Manage my Notifications</button></div>
				    	
						<form action="php/changename.php" method="post">
						<label>First Name</label><input type="text" id="firstname" name="firstname" value="'.$firstname.'" />
						<div class="error">'.$_SESSION['errors']['firstname'].'</div>
						<label>Last Name</label><input type="text" id="lastname" name="lastname" value="'.$lastname.'" />
						<div class="error">'.$_SESSION['errors']['lastname'].'</div>
						<label>Email</label><input type="email" id="email" name="email" value="'.$email.'" />
						<div class="error">'.$_SESSION['errors']['email'].'</div>
						<label>Confirm Password</label><input type="password" id="password" name="password" />
						<div class="error">'.$_SESSION['errors']['password'].'</div>
						<input type="hidden" id="check" name="check" value="'.$password.'" />
						<button type="submit" id="smallerbutton" name="submit">Change</button>
						</form>';
				} else if ($page == 'pass') {
					echo '<div id="settingbuttons"><div class="error">'.$_SESSION['success'].'</div>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=name\')">Change my Name or Email</button>
						<button type="submit" class="submitted" id="submitted" onclick="window.location.replace(\'settings.php?go=pass\')">Change my Password</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=room\')">Manage my Room</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=email\')">Manage my Notifications</button></div>
					
						<form action="php/changepass.php" method="post">
						<label>Current Password</label><input type="password" id="oldpass" name="oldpass" value="'.$_SESSION['values']['oldpass'].'" />
						<div class="error">'.$_SESSION['errors']['oldpass'].'</div>
						<label>New Password</label><input type="password" id="newpass" name="newpass" value="'.$_SESSION['values']['newpass'].'" />
						<div class="error">'.$_SESSION['errors']['newpass'].'</div>
						<label>Confirm</label><input type="password" id="confirm" name="confirm" value="'.$_SESSION['values']['confirm'].'" />
						<div class="error">'.$_SESSION['errors']['confirm'].'</div>
						<button type="submit" id="smallerbutton" name="submit">Change Password</button>
						</form>';
				} else if ($page == 'room') {
					$roommates = getRoommates($room);
					$roomName = getRoomName($room);
					
					echo '<div id="settingbuttons"><div class="error">'.$_SESSION['success'].'</div>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=name\')">Change my Name or Email</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=pass\')">Change my Password</button>
						<button type="submit" class="submitted" id="submitted" onclick="window.location.replace(\'settings.php?go=room\')">Manage my Room</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=email\')">Manage my Notifications</button></div>';
					
					echo '<div id="rightside">
						<form action="php/changeroomname.php" method="post">
						<label>Room Name</label><input type="text" id="name" name="name" value="'.$roomName.'" />
						<div class="error">'.$_SESSION['errors']['name'].'</div>
						<button type="submit" id="smallerbutton" name="submit">Change Room Name</button>
						</form></br></br></br>';
					
					echo '<h4>Invite Roommate</h4>
						<p>You have to send your roommate an email invite, but he or she can join either through the email link or the join ID <b>'.$room.'</b>.</p>
						<label>Name</label><input type="text" id="invitename" name="invitename"/>
						<label>Email</label><input type="email" id="inviteemail" name="inviteemail"/>
						<button type="button" id="smallerbutton" onclick="invite('.$room.','.$user.');">Invite Roommate</button>
						<span id="inviteResult"></span>
						</br></br>';
					
					echo '<h4>Remove Roommates</h4>';
					for ($i = 0; $i < count($roommates); $i++) {
						$user = $roommates[$i];
						$name = getName($user);
						$balance = getBalance($user);
						echo '<p id="'.$user.'"><span style="font-size:18px; font-family: HelveticaNeueLTStdThinCnRg;">'.$name.' </span><span id="removebutton" onclick="remove('.$user.',\''.$name.'\','.$balance.');"> [Remove]</span></p></br>';
					}
					echo '<span id="confirm"></span>
					</div>';
				} else if ($page == 'email') {
					$query = "SELECT notificationfreq, transactionnotifications, roommatenotifications, summarynotifications FROM users WHERE id='$user'";
					$result = mysql_query($query) or die(mysql_error());
					$row = mysql_fetch_assoc($result);
					$frequency = $row['notificationfreq'];
					$transaction = $row['transactionnotifications'];
					$roommates = $row['roommatenotifications'];
					$summary = $row['summarynotifications'];
					
					echo '<div id="settingbuttons"><div class="error">'.$_SESSION['success'].'</div>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=name\')">Change my Name or Email</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=pass\')">Change my Password</button>
						<button type="submit" class="submit" id="submit" onclick="window.location.replace(\'settings.php?go=room\')">Manage my Room</button>
						<button type="submit" class="submitted" id="submitted" onclick="window.location.replace(\'settings.php?go=email\')">Manage my Notifications</button></div>

						<form action="php/changenotifications.php" method="post">
						<label><strong>Email Frequency</strong></label><input type="radio" id="frequency" name="frequency" value="Daily"/ ';if ($frequency=='Daily') echo 'checked'; echo'><span id="time">Daily</span></br>
						<input type="radio" id="frequency" name="frequency" value="Weekly"/ ';if ($frequency=='Weekly') echo 'checked'; echo'><span id="time">Weekly</span></br>
						<input type="radio" id="frequency" name="frequency" value="Never"/ ';if ($frequency=='Never') echo 'checked'; echo'><span id="time">Never</span></br>
						<label><strong>Email Content </strong></br></br>Receive Updates on New Transactions</label><input type="radio" id="transactions" name="transactions" value="yes"/ ';if ($transaction=='yes') echo 'checked'; echo'><span id="time">Yes</span></br>
						<input type="radio" id="transactions" name="transactions" value="no"/ ';if ($transaction=='no') echo 'checked'; echo'><span id="time">No</span></br>
						<label>Receive Updates on Roommate Activity</label><input type="radio" id="roommates" name="roommates" value="yes"/ ';if ($roommates=='yes') echo 'checked'; echo'><span id="time">Yes</span></br>
						<input type="radio" id="roommates" name="roommates" value="no"/ ';if ($roommates=='no') echo 'checked'; echo'><span id="time">No</span></br>
						<label>Receive Balance Summaries</label><input type="radio" id="summary" name="summary" value="yes"/ ';if ($summary=='yes') echo 'checked'; echo'><span id="time">Yes</span></br>
						<input type="radio" id="summary" name="summary" value="no"/ ';if ($summary=='no') echo 'checked'; echo'><span id="time">No</span></br>
						<button type="submit" id="smallerbutton" name="submit">Change Notifications</button>
						</form>';
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
unset($_SESSION['values']);
unset($_SESSION['success']);
?>