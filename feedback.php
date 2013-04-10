<?php
session_start();
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
<link rel="stylesheet" type="text/css" href="css/feedback.css"/>

<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
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
</script>
</head>

<body>
<div id="wrapper">
    <div id="header">
        <div id="headerContainer" class="container_24">
        	<a href="summary.php"><img src="img/Roomioularge.png" /></a>
            <div id="headerContent">
            	<a href="settings.php">Settings</a>
            	<a href="feedback.php" class="selected">Feedback</a>
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
	<div id="feedback"><a href="summary.php">Send us feedback!</a></div>
	</div>
		<div class="grid_18">
	   		<div id="nav" class="roundCorner">
            		<a href="summary.php">Summary</a>
            		<a href="addbill.php">Add Bill</a>
        		<a href="addpayment.php">Add Payment</a>
			</div>
            <div id="body" class="roundCorner">
		    <form method="post" class="feedbackform" name="feedbackform" action="php/sendfeedback.php">
		    	<textarea id="feedbacktxt" name="feedbacktxt" placeholder="Thank you for helping us improve RoomIOU!"></textarea>
	            	<button type="submit" class="submit">Send Feedback</button>
		    </form>

		<?php
			session_start();
			echo '<span style="font-size:13px; color:#898989;">';
			print $_SESSION['message'];
			$_SESSION['message']='';
			echo '</span>';
		?>
            </div>
	</div>

    </div>
	<div id="push"></div>
</div>
</body>
</html>