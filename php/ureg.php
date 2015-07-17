<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Registration</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/ureg.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>

<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand">User Registration</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="../index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>

	<?php
	//user registration page
	include "../php/include.php";
	$nameErr = $emailErr = $passwErr = $unameErr = "";
	$name = $email = $password = $username = "";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (empty($_POST["name"])) {
			$nameErr = "Name is required"; 
		} else {
			$name = test_input($_POST["name"]);
			if (!preg_match("/^((?!_)[a-zA-z])+([ ][a-zA-Z]+)*$/",$name)) {
				$nameErr = "Invalid format";
			}
		}
		if (empty($_POST["email"])) {
			$emailErr = "Email is required";
		} else {
			$email = test_input($_POST["email"]);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$emailErr = "Invalid email format";
			}
		}
		if (empty($_POST["username"])) {
			$unameErr = "Username is required";
		} else {
			$username = test_input($_POST["username"]);
			if (!preg_match("/^[a-z0-9_-]{2,16}$/", $username)) {
				$unameErr = "Invalid format";
			}
		}
		if (empty($_POST["password"])) {
			$passwErr = "Password is required";
		} else {
			$password = test_input($_POST["password"]);
		}
	}
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	?>
	<?php

	if(isset($_SESSION["userid"])) {
		header("refresh: 2; ../php/uhome.php");
		echo '<div class ="alert alert-info" role = "alert">You are logged in already</div>';
	}
	else if(isset($_SESSION["artistid"])) {
		header("refresh: 2; ../php/ahome.php");
		echo '<div class ="alert alert-info" role = "alert">You are logged in already</div>';
	}
	//some are required, some are optional, needs to validate every field.
	$insertUname = 0;
	$insertEmail = 0;
	$echoName = 0;
	$echoEmail = 0;
	$echoUname = 0;
	$echoPass = 0;
	$echoCity = 0;
	$echoCountry = 0;
	$echoTaste = 0;
	$echoFavart = 0;
	$echoFavband=0;
	$echoSucc=0;

	if($nameErr!=""){
		$echoName = 1;
	}
	if($emailErr!=""){
		$echoEmail = 1;
	}
	else if (isset($_POST["email"])) {
		$stmt = $mysqli->prepare("select emailid from User where emailid = ?");
		$stmt->bind_param("s", $_POST["email"]);
		$stmt->execute();
		$stmt->bind_result($email);
		if ($stmt->fetch()) {
			$echoEmail = 2;
		}
		else {
			$insertEmail = 1;
		}
		$stmt->close();
	}
	if($unameErr!=""){
		$echoUname = 1;
	}
	else if (isset($_POST["username"])) {
		$stmt = $mysqli->prepare("select userName from User where userName = ?");
		$stmt->bind_param("s", $_POST["username"]);
		$stmt->execute();
		$stmt->bind_result($username);
		if ($stmt->fetch()) {
			$echoUname = 2;
		}
		else {
			$insertUname = 1;
		}
		$stmt->close();
	}
	if($passwErr!=""){
		$echoPass = 1;
	}


	$insertCity = 1;
	if(isset($_POST["city"])&& $_POST["city"]!=""){
		$stmt = $mysqli->prepare("select cityId from City where cityName = ?");
		$stmt->bind_param("s", $_POST['city']);
		$stmt->execute();
		$stmt->bind_result($cityid);
		if($stmt->fetch()) {
			$insertCity = 1;
		}
		else { $echoCity = 1;
			$insertCity = 0;
		}
		$stmt->close();                       
	}
	$insertCountry = 1;
	if(isset($_POST["country"])&& $_POST["country"]!="") {
		$stmt = $mysqli->prepare("select countryId from Country where countryName = ?");
		$stmt->bind_param("s", $_POST['country']);
		$stmt->execute();
		$stmt->bind_result($countryid);
		if($stmt->fetch()) {
			$insertCountry = 1;
		}
		else { $echoCountry = 1;
			$insertCountry = 0;
		}
		$stmt->close();                       
	}
	$insertFavArtist = 1;
	if(isset($_POST["favartist"])&& $_POST["favartist"]!="") {
		$stmt = $mysqli->prepare("select artistId from Artist where artistName = ?");
		$stmt->bind_param("s", $_POST['favartist']);
		$stmt->execute();
		$stmt->bind_result($artid);
		if($stmt->fetch()) {
			$insertFavArtist = 1;
		}
		else { $echoFavart=1;
			$insertFavArtist = 0;
		}
		$stmt->close();
	}
	$insertTaste = 1;
	if(isset($_POST["taste"])&& $_POST["taste"]!="") {
		$stmt = $mysqli->prepare("select musicCatId from Music_Category where name = ?");
		$stmt->bind_param("s", $_POST['taste']);
		$stmt->execute();
		$stmt->bind_result($musicid);
		if($stmt->fetch()) {
			$insertTaste = 1;
		}
		else { $echoTaste=1;
			$insertTaste = 0;
		}
		$stmt->close();
	}
	$insertFavBand = 1;
	if(isset($_POST["favband"])&& $_POST["favband"]!="") {
		$stmt = $mysqli->prepare("select bandId from Band where bandname = ?");
		$stmt->bind_param("s", $_POST['favband']);
		$stmt->execute();
		$stmt->bind_result($bandid);
		if($stmt->fetch()) {
			$insertFavBand = 1;
		}
		else { $echoFavband=1;
			$insertFavBand = 0;
		}
		$stmt->close();
	}
	//validation done. now safe to insert into database
	if(isset($_POST["username"]) && isset($_POST["password"])&& isset($_POST['name'])&& isset($_POST['email']) && $_POST["username"]!="" && $_POST["password"]!="" && $_POST["name"]!="" && $_POST["email"]!="" && !$nameErr && !$emailErr && $insertFavBand && $insertTaste && $insertFavArtist && $insertCountry && $insertCity && $insertUname && $insertEmail) {

		$stmt = $mysqli->prepare("insert into User (Name, username,password,emailid,yearOfBirth,cityid,countryid,trustscore,lastLogin,creationDate,updationDate,wall) values (?,?,?,?,?,?,?,?,?,?,?,?)"); 
		$stmt->bind_param("ssssiiiissss", $_POST['name'],$_POST['username'], ($_POST['password']),$_POST['email'],$_POST['dob'],$cityid,$countryid,$score,$lastlog,$create_date,$update_date,$_POST["wall"]);
		$score=0;
		$lastlog=date("Y-m-d H:i:s",time());
		$create_date=date("Y-m-d H:i:s",time());
		$update_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("select userId from User where userName = ?");
		$stmt->bind_param("s", $_POST['username']);
		$stmt->execute();
		$stmt->bind_result($userid);
		$stmt->fetch();
		$stmt->close();

		$stmt = $mysqli->prepare("insert into User_Artist (userId, artistId,creationDate) values (?,?,?)");
		$stmt->bind_param("iis", $userid,$artid,$create_date);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("insert into User_Music_Category (userId, musicCatId,creationDate) values (?,?,?)");
		$stmt->bind_param("iis", $userid,$musicid,$create_date);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("insert into User_Band (userId, bandId,creationDate) values (?,?,?)");
		$stmt->bind_param("iis", $userid,$bandid,$create_date);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();

		$echoSucc = 1;

		echo '<script type="text/javascript">
		function redirect()
		{
			window.location="../index.php";
		}
		setTimeout(redirect, 3000);
		</script>';
	}

	$mysqli->close();
	?>



	<div class="container">
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("ureg.php");?>">

			<div class="form-group">	
				<label class="control-label col-sm-3"  for="Name"><font color="black">Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control" value="<?php echo $_POST['name']; ?>" name="name">
					<?php if($nameErr!="") {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$nameErr.'</span>';}
					else if($echoSucc) { echo'<span class ="alert alert-success" role = "alert">Successfully registered!</span>';} 
					?>
				</div> 
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3" for="email"><font color="black">Email<font color="red">*</font></font></label>
				<div class="col-sm-3">
					<input type="email" class="form-control" value="<?php echo $_POST['email']; ?>"name="email" >
					<?php if($echoEmail == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$emailErr.'</span>';}
					else if($echoEmail == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Email already exists</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3" for="uName"><font color="black">Username<font color="red">*</font></font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control"  value="<?php echo $_POST['username']; ?>" name="username" >
					<?php if($echoUname == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$unameErr.'</span>';}
					else if($echoUname == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Username already exists</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3" for="pass"><font color="black">Password<font color="red">*</font></font></label> 	
				<div class="col-sm-3">		
					<input type="password" class="form-control" value="<?php echo $_POST['password']; ?>" name="password"  >
					<?php if($echoPass == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Password is required</span>';?>
				</div>
			</div>


			<div class="form-group" >
				<label class="control-label col-sm-3" for="pass"><font color="black">Year of Birth</font></label> 	
				<div class="col-sm-3">		
					<input type="number" class="form-control" value="<?php echo $_POST['dob']; ?>" name="dob"  >
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="uName"><font color="black">Home City</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control"  value="<?php echo $_POST['city']; ?>"name="city"  >
					<?php if($echoCity == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">City not found</span>';?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="uName"><font color="black">Home Country</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control"  value="<?php echo $_POST['country']; ?>"name="country"  >
					<?php if($echoCountry == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Country not found</span>';?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="uName"><font color="black">Favorite Artist</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control"  value="<?php echo $_POST['favartist']; ?>"name="favartist"  >
					<?php if($echoFavart == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Artist not found</span>';?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="uName"><font color="black">Favorite Band</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control"  value="<?php echo $_POST['favband']; ?>"name="favband"  >
					<?php if($echoFavband == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Band not found</span>';?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="uName"><font color="black">Musical Taste</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control"  value="<?php echo $_POST['taste']; ?>"name="taste"  >
					<?php if($echoTaste == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Taste not found</span>';?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3" for="wall"><font color="black">First Status Wall</font></label>
				<div class="col-sm-5">
					<textarea class="form-control" placeholder="Write something here.." name="wall" rows="2"><?php echo $_POST['wall']; ?></textarea>
				</div>
			</div>

			<div class="form-group" >
				<div class="col-sm-offset-3 col-sm-6">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Register</button>
				</div>
			</div>
		</form>
	</div><!-- container class-->

</body>
</html>
