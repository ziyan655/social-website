<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Artist Registration</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/areg.css"></link>
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
				<p class="navbar-brand">Artist Registration</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li class="menu"><a href="../index.php"><span class="glyphicon glyphicon-home"></span> Main Page</a></li>	
				</ul>
			</div>	
		</div>
	</div>

	<?php
	include "../php/include.php";
	$nameErr = $websiteErr = $emailErr = $passwErr = $unameErr = $catErr= $scatErr= "";
	$name = $email = $password = $username = $website = $cat=$scat="";
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
				$unameErr = "Only letters, numbers, and underscore";
			}
		}
		if (empty($_POST["password"])) {
			$passwErr = "Password is required";
		} else {
			$password = test_input($_POST["password"]);
		}
		if (!empty($_POST["web"])) {
			$website = test_input($_POST["web"]);
			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
				$websiteErr = "Invalid URL";
			} 
		}
		if (empty($_POST["category"])) {
			$catErr = "Music Category is required";
		} else {
			$cat = test_input($_POST["category"]);
		}
	}
	function test_input($data) {
		$data = htmlspecialchars($data);
		return $data;
	}
	?>

	<?php
//if the user is already logged in, redirect them back to homepage
	if(isset($_SESSION["userid"])) {
		header("refresh: 2; ../php/uhome.php");
		echo '<div class ="alert alert-info" role = "alert">You are logged in already</div>';
	}
	else if(isset($_SESSION["artistid"])) {
		header("refresh: 2; ../php/ahome.php");
		echo '<div class ="alert alert-info" role = "alert">You are logged in already</div>';
	}

	//artist registration process
	$insertUname = 0;
	$insertEmail = 0;
	$echoEmail = 0;
	$echoUname = 0;
	$echoPass = 0;
	$echoCountry = 0;
	$echoMcat = 0;
	$echoMscat = 0;
	$echoWeb = 0;
	$echoBand = 0;
	$echoBmcat = 0;
	$echoBmscat = 0;
	$echoSucc=0;

	if($emailErr!=""){
		$echoEmail = 1;
	}
	else if (isset($_POST["email"])) {
		$stmt = $mysqli->prepare("select Artist.emailid,User.emailid from Artist JOIN User where User.emailid = ? or Artist.emailid = ?"); 
		$stmt->bind_param("ss", $_POST["email"],$_POST["email"]);
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
		$stmt = $mysqli->prepare("SELECT Artist.userName,User.userName FROM Artist JOIN User where Artist.userName = ? or User.userName = ?");
		$stmt->bind_param("ss", $_POST["username"], $_POST["username"]);
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
	if($catErr!=""){
		$echoMcat = 1;
	}
	if($websiteErr!=""){
		$echoWeb = 1;
	}

	$flag=1;
	if(!empty($_POST['scategory'])){
		$stmt = $mysqli->prepare("select subCatId from Music_SubCategory where subCatName = ?");
		$stmt->bind_param("s", $_POST['scategory']);
		$stmt->execute();
		$stmt->bind_result($scatid);
		if(!$stmt->fetch()){
			$echoMscat = 1;
			$flag=0;
		}
		$stmt->close();
	}

	$flag1=0;
	if(isset($_POST['category']) && $_POST['category'] !=""){
		$stmt = $mysqli->prepare("select musicCatId from Music_Category where name = ?");
		$stmt->bind_param("s", $_POST['category']);
		$stmt->execute();
		$stmt->bind_result($catid);
		if($stmt->fetch()){
			$flag1=1;
		}
		else { 
			$echoMcat = 2;
		}
		$stmt->close();         
	}

	$insertBandCat = 1;
	if(isset($_POST["bmc"])&& $_POST["bmc"]!="") {
		$stmt = $mysqli->prepare("select musicCatId from Music_Category where name = ?");
		$stmt->bind_param("s", $_POST['bmc']);
		$stmt->execute();
		$stmt->bind_result($catid1);
		if($stmt->fetch()) {
			$insertBandCat = 1;
		}
		else { $echoBmcat = 1;
			$insertBandCat=0;
		}
		$stmt->close();
	}

	$insertBandSubCat = 1;
	if(isset($_POST["bmsc"])&& $_POST["bmsc"]!="") {
		$stmt = $mysqli->prepare("select subCatId from Music_SubCategory where subCatName = ?");
		$stmt->bind_param("s", $_POST['bmsc']);
		$stmt->execute();
		$stmt->bind_result($scatid1);
		if($stmt->fetch()){
			$insertBandSubCat = 1;
		}
		else { $echoBmscat = 1;
			$insertBandSubCat = 0;
		}
		$stmt->close();
	}
	$insertBandBelong = 1;
	if(isset($_POST["bandbelong"])&& $_POST["bandbelong"]!=""){
		$stmt = $mysqli->prepare("select bandId from Band where bandName = ?");
		$stmt->bind_param("s", $_POST['bandbelong']);
		$stmt->execute();
		$stmt->bind_result($bandid);
		if($stmt->fetch()) {
			$insertBandBelong=1;
		}
		else {
			$echoBand = 1;
			$insertBandBelong = 0;
		}
		$stmt->close();
	}

	$insertCountry = 1;
	if(isset($_POST["country"])&& $_POST["country"]!="") {
		$stmt = $mysqli->prepare("select countryId from Country where countryName = ?");
		$stmt->bind_param("s", $_POST['country']);
		$stmt->execute();
		$stmt->bind_result($countryid);
		if($stmt->fetch()){
			$insertCountry = 1;
		}
		else { 
			$echoCountry = 1;
			$insertCountry = 0;
		}
		$stmt->close();
	}

	//if every required field is filled and all the fields have valid data, then start storing into database
	if(isset($_POST["username"]) && isset($_POST["password"])&& isset($_POST['name'])&& isset($_POST['email']) && $_POST["username"]!="" && $_POST["password"]!="" && $_POST["name"]!="" && $_POST["email"]!="" && !$nameErr && !$emailErr && !$websiteErr && $flag && $flag1 && $insertBandCat && $insertBandSubCat && $insertCountry && $insertBandBelong && $insertUname && $insertEmail) {


		$stmt = $mysqli->prepare("insert into Artist (artistName, userName,password,emailid,description,bandId,websiteLink,creationDate,countryId,wall) values (?,?,?,?,?,?,?,?,?,?)"); 
		$stmt->bind_param("sssssissis", $_POST['name'],$_POST['username'], ($_POST['password']),$_POST['email'],$_POST['bio'],$bandid,$_POST['web'],$create_date,$countryid,$_POST["wall"]);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("select artistId from Artist where userName = ?");
		$stmt->bind_param("s", $_POST['username']);
		$stmt->execute();
		$stmt->bind_result($userid);
		$stmt->fetch();
		$stmt->close();

		$stmt = $mysqli->prepare("insert into ARTIST_Music_SubCategory (artistId, subCatId) values (?,?)");
		$stmt->bind_param("ii", $userid,$scatid);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("insert into ARTIST_Music_Category (artistId, musicCatId) values (?,?)");
		$stmt->bind_param("ii", $userid,$catid);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("insert into Band_M_Cat (bandId, musicCatId) values (?,?)");
		$stmt->bind_param("ii", $bandid, $catid1);
		$create_date=date("Y-m-d H:i:s", time());
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("insert into Band_M_Sub (bandId, subCatId) values (?,?)");
		$stmt->bind_param("ii", $bandid, $scatid1);
		$create_date=date("Y-m-d H:i:s", time());
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
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("areg.php");?>">
			<div class="form-group">	
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1"  for="Name"><font color="black">Artist Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control" value="<?php echo $_POST['name']; ?>" name="name">
					<?php if($nameErr!="") {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$nameErr.'</span>';}
					else if($echoSucc) { echo'<span class ="alert alert-success" role = "alert">Successfully registered!</span>';} 
					?>
				</div> 
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="email"><font color="black">Email<font color="red">*</font></font></label>
				<div class="col-sm-3 col-xs-6">
					<input type="email" class="form-control" value="<?php echo $_POST['email']; ?>"name="email" >
					<?php if($echoEmail == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$emailErr.'</span>';}
					else if($echoEmail == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Email already exists</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="uName"><font color="black">Username<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control"  value="<?php echo $_POST['username']; ?>" name="username" >
					<?php if($echoUname == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$unameErr.'</span>';}
					else if($echoUname == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Username already exists</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="pass"><font color="black">Password<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-6">		
					<input type="password" class="form-control" value="<?php echo $_POST['password']; ?>" name="password"  >
					<?php if($echoPass == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Password is required</span>';?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="uName"><font color="black">Home Country<font color="red"></font></font></label> 	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control"  value="<?php echo $_POST['country']; ?>"name="country"  >
					<?php if($echoCountry == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Country not found</span>';?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="category"><font color="black">Musical Category<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control" value="<?php echo $_POST['category']; ?>"name="category"  >
					<?php if($echoMcat == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music Category is required</span>';}
					else if($echoMcat == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music Category not found</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="subcat"><font color="black">Musical Subcategory</font></label>  	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control" value="<?php echo $_POST['scategory']; ?>" name="scategory" >
					<?php if($echoMscat == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music Sub-Category not found</span>';}?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="offwebsite"><font color="black">Official Website</font></label>  	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control" value="<?php echo $_POST['web']; ?>" name="web" >
					<?php if($echoWeb == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Invalid URL</span>';}?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="bandbelong"><font color="black">Member of which Band</font></label> 	 	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control" value="<?php echo $_POST['bandbelong']; ?>" name="bandbelong"  >
					<?php if($echoBand == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Band not found</span>';}?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="bmc"><font color="black">Band Music Category</font></label>  	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control" value="<?php echo $_POST['bmc']; ?>" name="bmc"   >
					<?php if($echoBmcat == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Band Music Category not found</span>';}?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="bmsc"><font color="black">Band Music Sub-Category</font></label>  	
				<div class="col-sm-3 col-xs-6">		
					<input type="text" class="form-control" value="<?php echo $_POST['bmsc']; ?>" name="bmsc" >
					<?php if($echoBmscat == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Band Music Sub-Category not found</span>';}?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="bio"><font color="black">Bio</font></label>
				<div class="col-sm-3 col-xs-6">	
					<textarea class="form-control" placeholder="Bio goes here.." name="bio" rows="2"><?php echo $_POST['bio']; ?></textarea>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3 col-sm-offset-1" for="wall"><font color="black">First Status Wall for Fans</font></label>
				<div class="col-sm-3 col-xs-6">
					<textarea class="form-control" placeholder="Write something here.." name="wall" rows="2"><?php echo $_POST['wall']; ?></textarea>
				</div>
			</div>

			<div class="form-group" >
				<div class="col-sm-offset-4 col-sm-8 col-xs-offset-3 col-xs-11">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Register</button>
				</div>
			</div>

		</form>
	</div><!--container class-->
</body>
</html>
