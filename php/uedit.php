<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Edit</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/uedit.css"></link>
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
				<p class="navbar-brand">Edit Profile</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>

	<?php
	//edit user profile
	include "include.php";
	$username=$_SESSION["username"];
	$nameErr = $emailErr = "";
	$name = $email = "";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST["name"]) && $_POST["name"] !="") {
			$name = test_input($_POST["name"]);

			if (!preg_match("/^((?!_)[a-zA-z])+([ ][a-zA-Z]+)*$/",$name)) {
				$nameErr = "Invalid format";
			}
		}
		if (isset($_POST["email"]) && $_POST["email"] !="") {
			$email = test_input($_POST["email"]);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$emailErr = "Invalid email format";
			}
		}
	}
	function test_input($data) {
		$data = htmlspecialchars($data);
		return $data;
	}
	?>


	<?php
	//insert the new data into database after knowing what fields are to be updated
	$insertName = 0;
	$insertEmail = 0;
	$insertCity = 0;
	$insertCountry = 0;
	$insertArtist = 0;
	$insertBand = 0;
	$insertTaste = 0;
	$deleteArtist = 0;
	$deleteBand = 0;
	$deleteTaste = 0;

	$echoName = 0;
	$echoEmail = 0;
	$echoCity = 0;
	$echoCountry = 0;
	$echoTaste = 0;
	$echoFavart = 0;
	$echoFavband=0;
	$echoSucc=0;
	$echoDob = 0;
	$echoDart = 0;
	$echoDband = 0;
	$echoDtaste = 0;

	if($nameErr!=""){
		$echoName = 1;
	}
	else {$insertName = 1;}

	if($emailErr!=""){
		$echoEmail = 1;
	}
	else {$insertEmail = 1;}


	if(!empty($_POST["name"]) && $insertName) {
		$stmt = $mysqli->prepare("UPDATE User SET Name = ? WHERE userId = ?");
		$stmt->bind_param("si",$_POST["name"],$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();
		$_SESSION["Name"] = $_POST["name"];
		$echoName = 2;
	}

	if(!empty($_POST["email"]) && $insertEmail) {
		$stmt = $mysqli->prepare("UPDATE User SET emailId = ? WHERE userId = ?");
		$stmt->bind_param("si",$_POST["email"],$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();
		$echoEmail = 2;
	}


	if(!empty($_POST["dob"])) {
		$stmt = $mysqli->prepare("UPDATE User SET yearOfBirth = ? WHERE userId = ?");
		$stmt->bind_param("si",$_POST["dob"],$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();
		$echoDob = 1;
	}

	if(!empty($_POST["city"])){ 
		$stmt = $mysqli->prepare("select cityId from City where cityName = ?");
		$stmt->bind_param("s", $_POST['city']);
		$stmt->execute();
		$stmt->bind_result($cityid);
		if($stmt->fetch()) {
			$insertCity = 1;
		}
		else { 
			$echoCity = 1;
		}
		$stmt->close();                       
	}

	if(!empty($_POST["country"])) {
		$stmt = $mysqli->prepare("select countryId from Country where countryName = ?");
		$stmt->bind_param("s", $_POST['country']);
		$stmt->execute();
		$stmt->bind_result($countryid);
		if($stmt->fetch()) {
			$insertCountry = 1;
		}
		else { 
			$echoCountry = 1;
		}
		$stmt->close();                       
	}


	if(!empty($_POST["favartist"])) {
		$stmt = $mysqli->prepare("select artistId from Artist where artistName = ?");
		$stmt->bind_param("s", $_POST['favartist']);
		$stmt->execute();
		$stmt->bind_result($artid);
		if($stmt->fetch()) {
			$insertArtist = 1;
		}
		else { 
			$echoFavart = 1;
		}
		$stmt->close();                       
	}


	if(!empty($_POST["favband"])) {
		$stmt = $mysqli->prepare("select bandId from Band where bandName = ?");
		$stmt->bind_param("s", $_POST['favband']);
		$stmt->execute();
		$stmt->bind_result($bandid);
		if($stmt->fetch()) {
			$insertBand = 1;
		}
		else { 
			$echoFavband = 1;
		}
		$stmt->close();                       
	}


	if(!empty($_POST["taste"])) {
		$stmt = $mysqli->prepare("select musicCatId from Music_Category where name = ?");
		$stmt->bind_param("s", $_POST['taste']);
		$stmt->execute();
		$stmt->bind_result($musicid);
		if($stmt->fetch()) {
			$insertTaste = 1;
		}
		else { 
			$echoTaste = 1;
		}
		$stmt->close();                       
	}

	if(!empty($_POST["dart"])) {
		$stmt = $mysqli->prepare("select Artist.artistId
			from User_Artist join Artist
			where User_Artist.artistId = Artist.artistId and Artist.artistName = ? and User_Artist.userId = ?");
		$stmt->bind_param("si", $_POST['dart'],$_SESSION["userid"]);
		$stmt->execute();
		$stmt->bind_result($artid);
		if($stmt->fetch()) {
			$deleteArtist = 1;
		}
		else { 
			$echoDart = 1;
		}
		$stmt->close();                       
	}

	if(!empty($_POST["dband"])) {
		$stmt = $mysqli->prepare("select Band.bandId
			from User_Band join Band
			where User_Band.bandId = Band.bandId and Band.bandName = ? and User_Band.userId = ?");
		$stmt->bind_param("si", $_POST['dband'],$_SESSION["userid"]);
		$stmt->execute();
		$stmt->bind_result($bandid);
		if($stmt->fetch()) {
			$deleteBand = 1;
		}
		else { 
			$echoDband = 1;
		}
		$stmt->close();                       
	}

	if(!empty($_POST["dtaste"])) {
		$stmt = $mysqli->prepare("select Music_Category.musicCatId
			from User_Music_Category join Music_Category
			where User_Music_Category.musicCatId = Music_Category.musicCatId and Music_Category.Name = ? and User_Music_Category.userId = ?");
		$stmt->bind_param("si", $_POST['dtaste'],$_SESSION["userid"]);
		$stmt->execute();
		$stmt->bind_result($musicid);
		if($stmt->fetch()) {
			$deleteTaste = 1;
		}
		else { 
			$echoDtaste = 1;
		}
		$stmt->close();                       
	}


	if($insertCity) {
		$stmt = $mysqli->prepare("UPDATE User SET cityId = ? WHERE userId = ?");
		$stmt->bind_param("ii",$cityid,$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();
		$echoCity = 2;
	}

	if($insertCountry) {
		$stmt = $mysqli->prepare("UPDATE User SET countryId = ? WHERE userId = ?");
		$stmt->bind_param("ii",$countryid,$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();
		$echoCountry = 2;
	}

	if($insertArtist) {
		$userid = $_SESSION["userid"];
		$stmt = $mysqli->prepare("insert into User_Artist (userId, artistId,creationDate) values (?,?,?)");
		$stmt->bind_param("iis", $userid,$artid,$create_date);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();
		$echoFavart = 2;
	}
	if($insertBand) {
		$userid = $_SESSION["userid"];
		$stmt = $mysqli->prepare("insert into User_Band (userId, bandId,creationDate) values (?,?,?)");
		$stmt->bind_param("iis", $userid,$bandid,$create_date);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();
		$echoFavband = 2;
	}
	if($insertTaste) {
		$userid = $_SESSION["userid"];
		$stmt = $mysqli->prepare("insert into User_Music_Category (userId, musicCatId,creationDate) values (?,?,?)");
		$stmt->bind_param("iis", $userid,$musicid,$create_date);
		$create_date=date("Y-m-d H:i:s",time());
		$stmt->execute();
		$stmt->close();
		$echoTaste = 2;
	}
	if($deleteArtist) {	
		$userid = $_SESSION["userid"];
		$stmt = $mysqli->prepare("DELETE FROM User_Artist WHERE userId = ? AND artistId = ?");
		$stmt->bind_param("ii", $userid,$artid);
		$stmt->execute();
		$stmt->close();
		$echoDart = 2;
	}
	if($deleteBand) {	
		$userid = $_SESSION["userid"];
		$stmt = $mysqli->prepare("DELETE FROM User_Band WHERE userId = ? AND bandId = ?");
		$stmt->bind_param("ii", $userid,$bandid);
		$stmt->execute();
		$stmt->close();
		$echoDband = 2;
	}
	if($deleteTaste) {	
		$userid = $_SESSION["userid"];
		$stmt = $mysqli->prepare("DELETE FROM User_Music_Category WHERE userId=? AND musicCatId=?");
		$stmt->bind_param("ii", $userid,$musicid);
		$stmt->execute();
		$stmt->close();
		$echoDtaste = 2;
	}

	$mysqli->close();
	?>

	<div class="container">
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("uedit.php");?>">
			<div class="form-group">	
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3"  for="Name"><font color="black">Change Name</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_POST['name']; ?>" name="name">
					<?php if($echoName == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$nameErr.'</span>';}
					else if($echoName == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Name updated</span>';} 
					?>
				</div> 
			</div>
			
			<div class="form-group" >
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="email"><font color="black">Change Email</font></label>
				<div class="col-sm-3 col-xs-5">
					<input type="email" class="form-control" value="<?php echo $_POST['email']; ?>"name="email" >
					<?php if($echoEmail == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$emailErr.'</span>';} 
					else if($echoEmail == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Email updated</span>';} ?>
				</div>
			</div>
			

			<div class="form-group" >
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="pass"><font color="black">Update Year of Birth</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="number" class="form-control" value="<?php echo $_POST['dob']; ?>" name="dob"  >
					<?php if($echoDob == 1) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Birthday updated</span>';} ?>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="uName"><font color="black">Update Home City</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['city']; ?>"name="city"  >
					<?php if($echoCity == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">City not found</span>';}
					else if($echoCity == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">City updated</span>';}
					?>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="uName"><font color="black">Update Home Country</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['country']; ?>"name="country"  >
					<?php if($echoCountry == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Country not found</span>';}
					else if($echoCountry == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Country updated</span>';}
					?>
				</div>
			</div>


			<div class="form-group">
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="uName"><font color="black">Add Favorite Artist</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['favartist']; ?>"name="favartist"  >
					<?php if($echoFavart == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Artist not found</span>';}
					else if($echoFavart == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Artist updated</span>';}
					?>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="uName"><font color="black">Add Favorite Band</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['favband']; ?>"name="favband"  >
					<?php if($echoFavband == 1){ echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Band not found</span>';}
					else if($echoFavband == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Band updated</span>';}
					?>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="uName"><font color="black">Add Musical Taste</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['taste']; ?>"name="taste"  >
					<?php if($echoTaste == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Taste not found</span>';}
					else if($echoTaste == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Taste updated</span>';}
					?>
				</div>
			</div>


			<div class="form-group">
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="uName"><font color="black">Delete Favorite Artist</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['dart']; ?>"name="dart"  >
					<?php if($echoDart == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Artist not found</span>';}
					else if($echoDart == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Artist deleted</span>';}
					?>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="uName"><font color="black">Delete Favorite Band</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['dband']; ?>"name="dband"  >
					<?php if($echoDband == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Band not found</span>';}
					else if($echoDband == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Band deleted</span>';}
					?>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3 col-sm-offset-1 col-xs-3" for="uName"><font color="black">Delete Musical Taste</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['dtaste']; ?>"name="dtaste"  >
					<?php if($echoDtaste == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Taste not found</span>';}
					else if($echoDtaste == 2) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Taste deleted</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<div class="col-sm-offset-4 col-sm-9 col-xs-9 col-xs-offset-3">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Update</button>
				</div>
			</div>
		</form>
	</div>

</body>
</html>
