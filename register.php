<?php
include_once("php/connect.php");
include_once("php/functions.php");
if ($_GET['email'] != '') {
	$email = $_GET['email'];
} else {
	$email = $_SESSION['values']['email'];
}

if ($_GET['room'] != '') {
	$room = $_GET['room'];
} else {
	$room = $_SESSION['values']['roomid'];
}

if ($room != '') {
	$invite = true;
} else {
	$invite = false;
}

$roomName = getRoomName($room);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RoomIOU</title>

<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="css/reset.css"/>
<link rel="stylesheet" type="text/css" href="css/960_24_col.css"/>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<link rel="stylesheet" type="text/css" href="css/register.css"/>

<script src="js/ajax.js"></script>
<script type="text/javascript">
function switchID() {
	document.getElementById('roomidSpan').style.display = 'block';
	document.getElementById('roomnameSpan').style.display = 'none';
	document.getElementById('explanation').innerHTML = '';
}

function switchName() {
	document.getElementById('roomidSpan').style.display = 'none';
	document.getElementById('roomnameSpan').style.display = 'block';
	document.getElementById('explanation').innerHTML = '';
}

function explanation() {
	var element = document.getElementById('explanation');
	var html = 'Each room has a unique ID. Your roommate can find this in the room\'s Settings page.';
	element.innerHTML = html;
}
</script>
</head>

<body>
<div id="wrapper">
    
    <div id="bodyWrapper" class="container_24">
	<div id="sidelogin" class="grid_7 prefix_2 suffix_5">
	    <div id="sidelogincontent">
		<span style="color:#FFFFFF;"><span style="font-family:'BEBAS'; font-size: 46px;">ROOM<span style="color:#e6d61b;">IOU</span></span>
		<span style="font-family:'HelveticaNeueLTStdThinCnRg'; font-size:35px;"> is a room expense tracker and sharer.</span></span>
		<div id="sideloginregister"> Already a member? <div id="signup"><a href="index.php">Sign in</a></div> </div>
	    </div>
	</div>
        <div id="registration" class="grid_6">
	<div id="registrationtitle">Register</div>
	<div id="registrationcontent">
		<div id="registrationform">
		<form action="php/register.php" method="post">
            	<label>First Name</label><input type="text" name="firstname" id="firstname" onchange="validate(this.id,this.value);" value="<?php echo $_SESSION['values']['firstname']; ?>"/>
            	<div class="error" id="firstnameerror"><?php echo $_SESSION['errors']['firstname']; ?></div>
                <label>Last Name</label><input type="text" name="lastname" id="lastname" onchange="validate(this.id,this.value);" value="<?php echo $_SESSION['values']['lastname']; ?>"/>
                <div class="error" id="lastnameerror"><?php echo $_SESSION['errors']['lastname']; ?></div>
                <label>Email</label><input type="email" name="email" id="email" onchange="validate(this.id,this.value);" value="<?php echo $email; ?>"/>
                <div class="error" id="emailerror"><?php echo $_SESSION['errors']['email']; ?></div>
                <label>Password</label><input type="password" name="password" id="password" onchange="validate(this.id,this.value);" value="<?php echo $_SESSION['values']['password']; ?>"/>
                <div class="error" id="passworderror"><?php echo $_SESSION['errors']['password']; ?></div>
                <?php 
                if (!$invite) {
                	echo '<span id="roomnameSpan"><label>Room Name</label><span class="roomSmall" onclick="switchID();">I want to join a room</span>
                		<input type="text" name="room" id="room" value="'.$_SESSION['values']['roomname'].'"/></span>';
                }
                ?>
                <span id="roomidSpan" <?php if (!$invite) echo 'style="display: none;"'; ?>>
                <label id="idLabel">Room ID</label><span class="roomSmall" onclick="switchName();">I want to create a room</span>
                <input type="text" name="roomid" id="roomid" value="<?php echo $room; ?>"/>
                <span class="roomSmall" style="margin-left: 0;" onclick="explanation();">What is a room ID?</span>
                <p id="explanation" style="color: white;" class="small"></p>
                </span>
                <div class="error" id="registererror"><?php echo $_SESSION['errors']['room']; ?></div>
               
                <button type="submit" class="submit" id="submit">Try it now</button>
            	</form>
	    	</div>
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
?>