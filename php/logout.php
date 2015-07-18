<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Log out</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>

	<style>
	body {
		background-image: url("../img/bg.jpg");
		background-size: repeat;
		min-height:100%;
	}
	html{
		height:100%;
		min-height:100%;
	}
	.alert-info {
		height:35px;
		text-align: center;
		width:300px;
		padding-top:6px;
		position:absolute;
		top: 270px;
		left:730px;
		font-size:1.1em;
		padding-left:6px;
	}
	</style>
</head>
<body>
	<?php
	session_start();
	session_destroy();
	header("refresh: 2; ../index.php");
	echo '<div class = "alert alert-info" role = "alert">Logging out..</div>';
	?>
</body>
</html>
