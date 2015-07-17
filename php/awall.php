<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Artist Wall</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/awall.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	//artist post on wall
	include "include.php";
	$revErr = "";
	$rev = "";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (empty($_POST["rev"])) {
			$revErr = "Can not post empty wall";
		} else {
			$rev = test_input($_POST["rev"]);
		}
	}
	function test_input($data) {
		$data = htmlspecialchars($data);
		return $data;
	}
	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand">Post on Wall</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="ahome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>


	<div class="container">
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("awall.php");?>">
			<div class="form-group" >
				<div class="col-sm-6" >
					<textarea class="form-control" placeholder="Say something.." name="rev" rows="4" ><?php echo $_POST['rev']; ?></textarea>
				</div>
			</div>

			<div class="form-group">
				<div class=" col-sm-6">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Post</button>
				</div>
			</div>
		</form>
	</div>

	<?php

	if(!empty($_POST["rev"])) {
		$stmt = $mysqli->prepare("UPDATE Artist SET wall = ? WHERE artistId = ?");
		$stmt->bind_param("si",$_POST["rev"],$_SESSION["artistid"]);
		$stmt->execute();
		$stmt->close();

		echo '<div class="container"><div class="alert alert-success" role="alert">Successfully Posted!</div></div>';
		echo '<script type="text/javascript">
		function redirect()
		{
			window.location="ahome.php";
		}
		setTimeout(redirect, 3000);
		</script>';
	}

	$mysqli->close();
	?>

</body>
</html>



