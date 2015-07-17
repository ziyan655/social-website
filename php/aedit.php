<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Edit Artist</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/aedit.css"></link>
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
					<li><a href="ahome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>

	<?php
	include "include.php";
	$username=$_SESSION["username"];
	$artid = $_SESSION["artistid"];
	$websiteErr = "";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (!empty($_POST["web"])) {
			$website = test_input($_POST["web"]);
			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
				$websiteErr = "Invalid URL";
			} 
		}
	}
	function test_input($data) {
		$data = htmlspecialchars($data);
		return $data;
	}
	?>


	<?php
	//Edit artist profile
	$insertMcat = 0;
	$insertMscat = 0;
	$insertBand = 0;
	$insertWeb = 0;		
	$removeMscat = 0;

	$echoBand = 0;
	$echoWeb = 0;
	$emailSuc = 0;
	$echoMcat = 0;
	$echoMscat = 0;
	$echoRmscat = 0;
	$echoBio = 0;
	if(!empty($_POST["email"])) {
		$stmt = $mysqli->prepare("UPDATE Artist SET emailId = ? WHERE artistId = ?");
		$stmt->bind_param("si",$_POST["email"],$_SESSION["artistid"]);
		$stmt->execute();
		$stmt->close();
		$emailSuc = 1;
	}

	$stmt = $mysqli->prepare("select musicCatId from ARTIST_Music_Category where artistId = ?");
	$stmt->bind_param("i", $_SESSION['artistid']);		
	$stmt->execute();
	$stmt->bind_result($mcatid);
	$stmt->fetch();	
	$stmt->close();

	if(!empty($_POST["mcat"])) {
		$stmt = $mysqli->prepare("select musicCatId from Music_Category where name = ?");
		$stmt->bind_param("s", $_POST['mcat']);
		$stmt->execute();
		$stmt->bind_result($mcatidNew);
		if($stmt->fetch()) {	
			if($mcatidNew != $mcatid) {
				$mcatid = $mcatidNew;
				$insertMcat = 1;
			}
		}
		else { 
			$echoMcat = 1;
		}
		$stmt->close();                       
	}
//does not update same category. no prompt	
	if($insertMcat) {	
		$artid = $_SESSION["artistid"];
		$stmt = $mysqli->prepare("UPDATE ARTIST_Music_Category SET musicCatId =? WHERE artistId = ?");
		$stmt->bind_param("ii", $mcatid, $artid);
		$stmt->execute();
		$stmt->close();
		
		$stmt = $mysqli->prepare("DELETE FROM ARTIST_Music_SubCategory WHERE artistId = ?");
		$stmt->bind_param("i", $artid);
		$stmt->execute();
		$stmt->close();
		
		$echoMcat = 2;
	}

	if(!empty($_POST["mscat"])) {
		$stmt = $mysqli->prepare("select subCatId from Music_SubCategory where subCatName = ? AND musicCatId = ?");
		$stmt->bind_param("si", $_POST['mscat'],$mcatid);
		$stmt->execute();
		$stmt->bind_result($mscatid);
		if($stmt->fetch()) {
			$insertMscat = 1;
		}
		else { 
			$echoMscat = 1;
		}
		$stmt->close();                       
	}

	if($insertMscat) {	
		$artid = $_SESSION["artistid"];
		$stmt = $mysqli->prepare("INSERT INTO ARTIST_Music_SubCategory (artistId, subCatId) values (?,?)");
		$stmt->bind_param("ii", $artid,$mscatid);
		$stmt->execute();
		$stmt->close();
		$echoMscat = 2;
	}

//0 value is treated as empty $_POST[]
	if(!empty($_POST["rmscat"])) {
		$stmt = $mysqli->prepare("select a.subCatId 
			from ARTIST_Music_SubCategory a join Music_SubCategory b 
			where a.subCatId = b.subCatId AND subCatName = ? AND artistId = ?");
		$stmt->bind_param("si", $_POST['rmscat'], $_SESSION['artistid']);
		$stmt->execute();
		$stmt->bind_result($rmscatid);
		if($stmt->fetch()) {
			$removeMscat = 1;
		}
		else { 
			$echoRmscat = 1;
		}
		$stmt->close();                       
	}
	if($removeMscat) {
		$stmt = $mysqli->prepare("DELETE FROM ARTIST_Music_SubCategory WHERE artistId = ? AND subCatId = ?");
		$stmt->bind_param("ii", $artid, $rmscatid);
		$stmt->execute();
		$stmt->close();
		$echoRmscat = 2;
	}

	if($websiteErr!="") {
		$echoWeb = 1;
	}
	else {$insertWeb = 1;}

	if(!empty($_POST['web']) && $insertWeb) {
		$artid = $_SESSION["artistid"];
		$stmt = $mysqli->prepare("UPDATE Artist SET websiteLink =? WHERE artistId=?");
		$stmt->bind_param("si", $_POST["web"],$artid);
		$stmt->execute();
		$stmt->close();
		echo '<div class = "alert alert-success" role = "alert" id="websuc">Official website successfully updated!</div>';
	}

	if(!empty($_POST["band"])) {
		$stmt = $mysqli->prepare("select bandId from Band where bandName = ?");
		$stmt->bind_param("s", $_POST['band']);
		$stmt->execute();
		$stmt->bind_result($bandid);
		if($stmt->fetch()) {
			$insertBand = 1;
		}
		else { 
			$echoBand = 1;
		}
		$stmt->close();                       
	}
//no quit band option, only change band possible	
	if($insertBand) {
		$artid = $_SESSION["artistid"];
		$stmt = $mysqli->prepare("UPDATE Artist SET bandId =? WHERE artistId = ?");
		$stmt->bind_param("ii", $bandid, $artid);
		$stmt->execute();
		$stmt->close();
		$echoBand = 2;
	}


	if(!empty($_POST["bio"])) {
		$artid = $_SESSION["artistid"];
		$stmt = $mysqli->prepare("UPDATE Artist SET description =? WHERE artistId = ?");
		$stmt->bind_param("si", $_POST["bio"],$artid);
		$stmt->execute();
		$stmt->close();
		$echoBio = 1;
	}
	$mysqli->close();
	?>


	<div class="container">
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("aedit.php");?>">
			
			<div class="form-group" >
				<label class="control-label col-sm-3" for="email"><font color="black">Change Email</font></label>
				<div class="col-sm-3">
					<input type="email" class="form-control" value="<?php echo $_POST['email']; ?>"name="email" >
					<?php if($emailSuc) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Email updated</span>';}	?>
				</div>
			</div>
			
			<div class="form-group" >
				<label class="control-label col-sm-3" for="uName"><font color="black">Update Musical Category</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control"  value="<?php echo $_POST['mcat']; ?>" name="mcat" >
					<?php if($echoMcat == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music category not found</span>';} 
					else if($echoMcat == 2) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Music category updated</span>';}
					?>
				</div>
			</div>
			
			<div class="form-group" >
				<label class="control-label col-sm-3" for="pass"><font color="black">Add Musical Sub-category</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control" value="<?php echo $_POST['mscat']; ?>" name="mscat"  >
					<?php if($echoMscat == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music sub-category not found</span>';
					else if($echoMscat == 2) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Music sub-category updated</span>';}	?>
				</div>
			</div>
			
			<div class="form-group" >
				<label class="control-label col-sm-3" for="pass"><font color="black">Remove Musical Sub-category</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control" value="<?php echo $_POST['rmscat']; ?>" name="rmscat"  >
					<?php if($echoRmscat == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music sub-category not found</span>';
					else if($echoRmscat == 2) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Music sub-category removed</span>';}	?>
				</div>
			</div>	
			
			<div class="form-group" >
				<label class="control-label col-sm-3" for="pass"><font color="black">Update Official Website</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control" value="<?php echo $_POST['web']; ?>" name="web"  >
					<?php if($echoWeb == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$websiteErr.'</span>';
					else if($echoWeb == 2) echo'<span class ="alert alert-success" id="inputErr" role = "alert">Official website updated</span>';
					?>
				</div>
			</div>
			
			
			<div class="form-group" >
				<label class="control-label col-sm-3" for="pass"><font color="black">Member of which Band Change</font></label> 	
				<div class="col-sm-3">		
					<input type="text" class="form-control" value="<?php echo $_POST['band']; ?>" name="band"  >
					<?php if($echoBand == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Band not found</span>';
					else if($echoBand == 2) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Band updated</span>';}	?>
				</div>
			</div>	
			
			<div class="form-group" >
				<label class="control-label col-sm-3" for="pass"><font color="black">Update Home Page Bio</font></label> 	
				<div class="col-sm-4">		
					<textarea class="form-control" placeholder="Write here.." name="bio" rows="2"><?php echo $_POST['bio']; ?></textarea>
					<?php if($echoBio) echo'<span class ="alert alert-success" id="inputSuc" role = "alert">Bio updated</span>';	?>
				</div>
			</div>
			
			
			<div class="form-group" >
				<div class="col-sm-offset-3 col-sm-6">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Update</button>
				</div>
			</div>
		</form>
	</div>

</body>
</html>
