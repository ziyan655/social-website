<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>UConcert</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<script	src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<?php
	include ("php/include.php");
	?>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target="#myNavbar">
				<span class="icon-bar"></span> <span class="icon-bar"></span> <span
				class="icon-bar"></span>
			</button>
			<p class="navbar-brand">UConcert</p>
		</div>

		<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="php/areg.php"><span class="glyphicon glyphicon-user"></span>Artist
					Registration</a></li>
					<li><a href="php/ureg.php"><span class="glyphicon glyphicon-user"></span>User
						Registration</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container">
		<div class="fixed-widthDes">
			<p id="description">
				<font color="black">UConcert is a music social website.<br /> Learn
					all the information about ongoing concerts and interact with users
					and artists.<br /> Login and get started!
				</font>
			</p>
		</div>


		<div class="fixed-widthForm">
			<form class="form-horizontal" action="index.php" method="POST"  id="loginForm">

				<div class="form-group">
					<div class="col-sm-4">	
						<input type="text" placeholder="Username"
						value="user" class="form-control"
						name="username" />
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-4">	
						<input type="password" placeholder="Password" value="user" class="form-control" name="password" />
					</div>
				</div>

				<div class="form-group" >
					<div class="col-sm-3">	
						<button type="submit" class="btn btn-success " id="submitButton">Log In</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?php
	//user authentication
	if ($stmt = $mysqli->prepare ( "select userId,userName,password,Name from User where userName = ? and password = ?" )) {
		$stmt->bind_param ( "ss", $_POST ["username"], ($_POST ["password"]) );
		$stmt->execute ();
		$stmt->bind_result ( $userid, $username, $password, $gname );
		$stmt->store_result ();
		if ($stmt->fetch ()) {
			$flag = 1;
			$_SESSION ["userid"] = $userid;
			$_SESSION ["username"] = $username;
			$_SESSION ["password"] = $password;
			$_SESSION ["Name"] = $gname;
		$_SESSION ["REMOTE_ADDR"] = $_SERVER ["REMOTE_ADDR"]; // store clients IP address to help prevent session hijack

	}
	$stmt->close ();
}
	//artist authentication
if ($stmt = $mysqli->prepare ( "select artistId, userName,password,artistName from Artist where userName = ? and password = ?" )) {
	$stmt->bind_param ( "ss", $_POST ["username"], ($_POST ["password"]) );
	$stmt->execute ();
	$stmt->bind_result ( $artistid, $username, $password, $gname );
	// if there is a match set session variables and send user to homepage
	if ($stmt->fetch ()) {
		$_SESSION ["artistid"] = $artistid;
		$_SESSION ["username"] = $username;
		$_SESSION ["password"] = $password;
		$_SESSION ["Name"] = $gname;
		$_SESSION ["REMOTE_ADDR"] = $_SERVER ["REMOTE_ADDR"]; // store clients IP address to help prevent session hijack
		echo '<script>
		function redirect()
		{
			window.location="php/ahome.php";
		}
		setTimeout(redirect, 3000);
		</script>';
		echo '<div class="container"><div class="alert alert-success" role="alert">Artist logging in...</div></div>';
	} else {
		if (isset ($_POST ["username"] ) && $flag != 1) {
			sleep (1); // pause a bit to help prevent brute force attacks
			echo '<div class="container"><div class ="alert alert-danger" role = "alert">username or password is incorrect, please try again</br>user demo login:<i><b>user</b></i></br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsppassword:<i><b>user</b></i></br>artist demo login:<i><b>artist</b></i></br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsppassword:<i><b>artist</b></i></div></div>';
		}
	}
	$stmt->close ();
}
if ($flag) {
	$stmt->close ();
	$stmt = $mysqli->prepare ( "UPDATE User SET lastLogin = current_timestamp WHERE userId = ?" );
	$stmt->bind_param ( "i", $_SESSION ["userid"] );
	$stmt->execute ();
	$stmt->close ();
	echo '<script>
	function redirect()
	{
		window.location="php/uhome.php";
	}
	setTimeout(redirect, 3000);
	</script>';

	echo '<div class="container"><div class="alert alert-success" role="alert">User logging in...</div></div>';
}

$mysqli->close ();
?>

</body>
</html>
