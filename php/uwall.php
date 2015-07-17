<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Wall</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/uwall.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	//post on userwall
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
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>


	<?php
	$echoSuc = 0;
	if(!empty($_POST["wall"])) {
		$stmt = $mysqli->prepare("UPDATE User SET wall = ? WHERE userId = ?");
		$stmt->bind_param("si",$_POST["wall"],$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("UPDATE User SET trustScore = trustScore + 1 WHERE userId = ?");
		$stmt->bind_param("i",$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();
		$echoSuc = 1;

		echo '<script type="text/javascript">
		function redirect()
		{
			window.location="uhome.php";
		}
		setTimeout(redirect, 3000);
		</script>';
	}

	$mysqli->close();
	?>


	<div class="container">
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("uwall.php");?>">
			<div class="form-group" >
				<div class="col-sm-6">
					<textarea class="form-control" placeholder="Say something.." name="wall" rows="2"><?php echo $_POST['wall']; ?></textarea>
					<?php if($echoSuc) { echo'<span class ="alert alert-success" role = "alert">Successfully posted!  Trust Score +1!</span>';} ?>
				</div>
			</div>

			<div class="form-group" id="inputForm">
				<div class="col-sm-6">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Post</button>
				</div>
			</div>
		</form>
	</div>

</body>
</html>



