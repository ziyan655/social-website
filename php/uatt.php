<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Concert Attendance</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/uatt.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	include "include.php";

	$attErr="";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (empty($_POST["cname"])) {
			$attErr = "Concert name not entered"; 
		} 
	}

	?>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand">Concert Attendance</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>

	<?php
	//user concert attendance
	$insertConcert1 = 0;
	$insertConcert2 = 0;
	$echoCname = 0;
	$echoSucc = 0;

	if (!empty($_POST["cname"])) {
		$stmt = $mysqli->prepare("select concertId from Concert where concertName = ?");
		$stmt->bind_param("s", $_POST["cname"]);
		$stmt->execute();
		$stmt->bind_result($cid);
		if(!$stmt->fetch()) {
			$echoCname = 2;
		}
		else { 
			$insertConcert1 = 1;
		}
		$stmt->close();

		$uid=$_SESSION["userid"];
		$stmt = $mysqli->prepare("select concertId from Concert_Attend_User where userId = ? AND concertId= ?");
		$stmt->bind_param("ii", $uid,$cid);
		$stmt->execute();
		$stmt->bind_result($cidCheck);
		if ($stmt->fetch()) {
			$echoCname = 1;
		}
		else {
			$insertConcert2 = 1;
		}
		$stmt->close();
	}


	if($insertConcert1 && $insertConcert2 && isset($_POST["cname"]) && $_POST["cname"]!="") {
		$cname=$_POST["cname"];
		$stmt = $mysqli->prepare(" insert into Concert_Attend_User (concertId, userId) values (?,?)" );
		$stmt->bind_param("ii", $cid,$uid);
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("UPDATE User SET trustScore = trustScore + 1 WHERE userId = ?");
		$stmt->bind_param("i",$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();
		$echoSucc = 1;
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
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("uatt.php");?>">

			<div class="form-group">	
				<label class="control-label col-sm-3"  for="Name"><font color="black">Concert Name<font color="red">*</font></font></label> 	
				<div class="col-sm-4">		
					<input type="text" class="form-control" value="<?php echo $_POST['cname']; ?>" name="cname">
					<?php if($echoCname == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert already attended</span>';}
					else if($echoCname == 2) { echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert not found</span>';} 
					else if($echoSucc) { echo'<span class ="alert alert-success" role = "alert">Concert attendence declared!   Trust Score +3!</span>';} 
					?>
				</div> 
			</div>

			<div class="form-group" id="inputForm">
				<div class="col-sm-offset-3 col-sm-5">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Add</button>
				</div>
			</div>
		</form>
	</div>

</body>
</html>
