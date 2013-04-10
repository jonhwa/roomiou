<?php 
include_once("php/connect.php");
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
<link rel="stylesheet" type="text/css" href="css/index.css"/>
</head>

<body>
<div id="wrapper">
    
    <div id="bodyWrapper" class="container_24">
	<div id="sidelogin" class="grid_7 prefix_2 suffix_5">
	    <div id="sidelogincontent">
		<span style="color:#FFFFFF;"><span style="font-family:'BEBAS'; font-size: 46px;">ROOM<span style="color:#e6d61b;">IOU</span></span>
		<span style="font-family:'HelveticaNeueLTStdThinCnRg'; font-size:35px;"> is a room expense tracker and sharer.</span></span>
	    <div id="sideloginregister"> New to RoomIOU? <div id="signup"><a href="register.php">Sign up</a></div> </div>
	    </div>
	</div>
	<div id="resetpw" class="grid_6">
	<div id="logintitle">Reset Password</div>
	<div id="logincontent">
		<form action="php/lostpass.php" method="post">
	        <div id="form" class="email">
	       		<label>email</label><input type="email" name="email" id="email" value="<?php echo $_SESSION['values']['email']; ?>"/>
	       		<div class="error"><?php echo $_SESSION['errors']['email']; ?></div>
			</div>
			<button type="submit" class="submit" id="submit">Get Password</button>
	    </form>
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