<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Review</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/ureview.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	//user post reviews to concerts
	include "include.php";
	$revErr = $cnameErr = $scoreErr = "";
	$cname = $rev = "";
	$score = $_POST['score'];
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (empty($_POST["cname"])) {
			$cnameErr = "Concert name is required"; 
		} else {
			$cname = test_input($_POST["cname"]);
		}
		if (empty($_POST["rev"])) {
			$revErr = "Review is required";
		} else {
			$rev = test_input($_POST["rev"]);
		}
	}
	function test_input($data) {
		$data = htmlspecialchars($data);
		return $data;
	}
	?>



	<?php	
	$insertReview1 = 0;
	$insertReview2 = 0;
	$echoCname = 0;
	$echoRev = 0;
	$echoSucc = 0;

	if($cnameErr!=""){
		$echoCname = 1;
	}
	else if(isset($_POST["cname"])) {
		$stmt = $mysqli->prepare("select concertId from Concert where concertName = ?");
		$stmt->bind_param("s", $_POST['cname']);
		$stmt->execute();
		$stmt->bind_result($cid);
		if(!$stmt->fetch()){
			$echoCname = 3;
		}
		else { 
			$insertReview1 = 1;
		}
		$stmt->close();

		$stmt = $mysqli->prepare("select concertId from Concert_Review_Rating where concertId = ? AND userId = ?");
		$stmt->bind_param("ii", $cid,$_SESSION["userid"]);
		$stmt->execute();
		$stmt->bind_result($cid, $userid);
		if ($stmt->fetch()) {
			$echoCname = 2;
		}
		else {
			$insertReview2 = 1;
		}
		$stmt->close();
	}

	if($revErr!=""){
		$echoRev = 1;
	}

	//input validation done. now safe to insert them into data base
	if(isset($_POST["cname"]) && isset($_POST["rev"])&& isset($_POST['score'])&& $_POST["cname"]!="" && $_POST["rev"]!="" && $_POST["score"]!="" && $insertReview1 && $insertReview2) {

		$userid =  $_SESSION["userid"];
		$mysqli->query("CALL User_review_rating($cid,$userid,'$rev',$score)");
		
		$echoSucc = 1;
		echo '<script type="text/javascript">
		function redirect()
		{
			window.location="uhome.php";
		}
		setTimeout(redirect, 2000);
		</script>';
	}
	$mysqli->close();
	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand">Review Posting</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>

	<div class="container">
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("ureview.php");?>">
			
			
			<div class="form-group">	
				<label class="control-label col-sm-3"  for="Name"><font color="black">Concert Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control" value="<?php echo $_POST['cname']; ?>" name="cname">
					<?php if($echoCname == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$cnameErr.'</span>';}
					else if($echoCname == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert already reviewed</span>';}
					else if($echoCname == 3) { echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert not found</span>';} 
					else if($echoSucc) { echo'<span class ="alert alert-success" role = "alert">Concert reviewed!   Trust Score +3!</span>';} 
					?>
				</div> 
			</div>
			
			
			<div class="form-group">
				<label class="control-label col-sm-3" for="score"><font color="black">Select Score</font></label>
				<div class="col-sm-3">
					<select class="form-control"  name = "score">
						<option value="1" <?php if ($_POST['score'] ==1) echo 'selected'; ?>>1</option>
						<option value="2" <?php if ($_POST['score'] ==2) echo 'selected'; ?>>2</option>
						<option value="3" <?php if ($_POST['score'] ==3) echo 'selected'; ?>>3</option>
						<option value="4" <?php if ($_POST['score'] ==4) echo 'selected'; ?>>4</option>
						<option value="5" <?php if ($_POST['score'] ==5) echo 'selected'; ?>>5</option>
						<option value="6" <?php if ($_POST['score'] ==6) echo 'selected'; ?>>6</option>
						<option value="7" <?php if ($_POST['score'] ==7) echo 'selected'; ?>>7</option>
						<option value="8" <?php if ($_POST['score'] ==8) echo 'selected'; ?>>8</option>
						<option value="9" <?php if ($_POST['score'] ==9) echo 'selected'; ?>>9</option>
						<option value="10" <?php if ($_POST['score'] ==10) echo 'selected'; ?>>10</option>
					</select>
				</div>
			</div>
			

			<div class="form-group" >
				<label class="control-label col-sm-3" for="wall"><font color="black">Review<font color="red"> *</font></font></label>
				<div class="col-sm-4">
					<textarea class="form-control" placeholder="Write review here.." name="rev" rows="4"><?php echo $_POST['rev']; ?></textarea>
					<?php if($echoRev) {echo'<span class ="alert alert-danger" id="inputErrNew" role = "alert">'.$revErr.'</span>';}?>
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
