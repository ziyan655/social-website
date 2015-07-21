<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Registration</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/chkTS.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>
	<?php
	include ("include.php");
	//check the Trust score of the user, only above a threshold can the user post concerts
	$threshold = 5;
	$stmt = $mysqli->prepare("select trustScore from User WHERE  userId = ? AND trustScore > ?");
	$stmt->bind_param("ii", $_GET["userid"],$threshold);
	$stmt->execute();
	$stmt->bind_result($var);
	if ($stmt->fetch()) {
		echo '<div class="alert alert-success">Your Trust Score is&nbsp'.$var.'.&nbsp&nbsp&nbspYou can add concerts to our system. Thanks for your contribution!</div>';
		echo '<script type="text/javascript">
		function redirect()
		{
			window.location="upconcert.php";
		}
		setTimeout(redirect, 3000);
		</script>';
	}
	else{ 
		echo '<div class="alert alert-warning" row="2" role="alert"><p>Your Trust Score is&nbsp'.$var.', which is not high enough to add a concert.</p><p>Trust Score can be earned by declaring attended concerts, liking artists or bands, posting reviews, creating favorite concert lists.</p></div>';
		echo '<script type="text/javascript">
		function redirect()
		{
			window.location="upconcert.php";
		}
		setTimeout(redirect, 3000);
		</script>';
	}
	$stmt->close();
	$mysqli->close();
	?>
</body>
</html>
