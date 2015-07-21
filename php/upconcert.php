<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Post Concert</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/upconcert.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	include "include.php";

	$cnameErr = $dateErr = $locErr = $timeErr= $countryErr=$cityErr=$webErr="";
	$cname = $date = $loc=$time=$country=$city=$webErr="";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (empty($_POST["cname"])) {
			$cnameErr = "Concert Name is required"; 
		} else {
			$cname = test_input($_POST["cname"]);
			if (!preg_match("/^[a-zA-Z ]*$/",$cname)) {
				$cnameErr = "Only letters and white space allowed";
			}
		}
		if (empty($_POST["date"])) {
			$dateErr = "Event Date is required";
		} else {
			$date = test_input($_POST["date"]);
		}
		if (empty($_POST["time"])) {
			$timeErr = "Event Time is required";
		} 
		else{
			$time = test_input($_POST["time"]);
		}
		if (empty($_POST["loc"])) {
			$locErr = "Location is required";
		} else {
			$loc = test_input($_POST["loc"]);
		}

		if (empty($_POST["city"])) {
			$cityErr = "City name is required";
		} else {
			$city = test_input($_POST["city"]);
		}
		if (empty($_POST["country"])) {
			$countryErr = "Country name is required";
		} else {
			$country = test_input($_POST["country"]);
		}
		if (!empty($_POST["link"])) {
			$website = test_input($_POST["link"]);
			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
				$webErr = "Invalid URL";
			}
			else { $webErr = "";}
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
				<p class="navbar-brand">Add Concert</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>


	<?php
	//user posts a concert
	$insertCname = 0;
	$insertDate = 0;
	$insertTime = 0;
	$insertLoc = 0;
	$insertCity = 0;
	$insertCountry = 0;
	$insertHostBy = 0;

	$echoCname = 0;
	$echoTime = 0;
	$echoDate = 0;
	$echoLoc = 0;
	$echoCity = 0;
	$echoCountry=0;
	$echoBand = 0;
	$echoSucc = 0;
	$echoArt = 0;

	if(!empty($_POST['band']) && !empty($_POST["artist"])) {
		$echoBand = 2;
	}
	else if (isset($_POST['band']) && isset($_POST["artist"]) && empty($_POST['band']) && empty($_POST["artist"]))  {
		$echoBand = 3;
	}
	else {
		if(!empty($_POST['band'])){
			$stmt = $mysqli->prepare("select bandId from Band where bandName = ?");
			$stmt->bind_param("s", $_POST['band']);
			$stmt->execute();
			$stmt->bind_result($bandid);
			if($stmt->fetch()) {
				$insertHostBy = 1;
			}
			else {
				$echoBand = 1;
			}
			$stmt->close();
		}
		else if(!empty($_POST['artist'])) {
			$stmt = $mysqli->prepare("select artistId from Artist where artistName = ?");
			$stmt->bind_param("s", $_POST["artist"]);
			$stmt->execute();
			$stmt->bind_result($artid);
			if($stmt->fetch()) {
				$insertHostBy = 1;
			}
			else {
				$echoArt = 1;
			}
			$stmt->close(); 
		}		
	}

	if($cnameErr!=""){
		$echoCname = 1;
	}
	else if(isset($_POST["cname"])) {
		$stmt = $mysqli->prepare("select concertName from Concert where concertName = ?");
		$stmt->bind_param("s", $_POST["cname"]);
		$stmt->execute();
		$stmt->bind_result($var);
		if ($stmt->fetch()) {
			$echoCname = 2;
		}
		else {  
			$insertCname = 1;
		}
		$stmt->close();
	}

	$t = time();
	$curdate=date("Y-m-d",$t);
	if($dateErr!=""){
		$echoDate = 1;
	}
	else if(isset($_POST["date"])) {
		if($_POST["date"] < $curdate ) {      
			$echoDate = 2;
		}
	}


	if($timeErr!=""){
		$echoTime=1;
	}
	else { 
		$insertTime = 1;
	}

	if($locErr!=""){
		$echoLoc = 1;
	}
	else if (isset($_POST["loc"])) {
		$stmt = $mysqli->prepare("select locationName from Location where locationName = ?");
		$stmt->bind_param("s", $_POST["loc"]);
		$stmt->execute();
		$stmt->bind_result($var);
		if($stmt->fetch()) {
			$insertLoc = 1;
		}
		else { 
			$echoLoc = 2;
			
		}
		$stmt->close();                       
	}

	if($cityErr!=""){
		$echoCity = 1;
	}
	else if (isset($_POST["city"])) {
		$stmt = $mysqli->prepare("select cityName from City where CityName = ?");
		$stmt->bind_param("s", $_POST["city"]);
		$stmt->execute();
		$stmt->bind_result($var);
		if($stmt->fetch()) {
			$insertCity = 1;
		}
		else { 
			$echoCity = 2; 
		}
		$stmt->close();                       
	}

	if($countryErr!=""){
		$echoCountry = 1;
	}
	else if (isset($_POST["country"])) {
		$stmt = $mysqli->prepare("select countryName from Country where countryName = ?");
		$stmt->bind_param("s", $_POST["country"]);
		$stmt->execute();
		$stmt->bind_result($var);
		if($stmt->fetch()) {
			$insertCountry = 1;
		}
		else { 
			$echoCountry = 2; 
		}
		$stmt->close();                       
	}

	if($insertLoc && $insertCity && $insertCountry){
		$stmt = $mysqli->prepare("select locationId 
			from Location JOIN City JOIN Country 
			where Location.cityID = City.cityID AND City.countryID = Country.countryID AND locationName = ? AND City.cityName = ? AND Country.countryName = ?");
		$stmt->bind_param("sss", $_POST['loc'], $_POST["city"], $_POST["country"]);
		$stmt->execute();
		$stmt->bind_result($locid);
		$stmt->fetch();
		$stmt->close();

		$stmt = $mysqli->prepare("select eventDate from Concert where locationId = ? AND eventDate = ? ");
		$stmt->bind_param("is", $locid,$_POST["date"]);
		$stmt->execute();
		$stmt->bind_result($var);
		if ($stmt->fetch()) {
			$echoDate = 3;
		}
		else {
			$insertDate = 1;
		}
		$stmt->close();
	}

	if($insertCname && $insertDate && $insertLoc && $insertCity && $insertCountry && $insertTime && $insertHostBy) {
		$cname=$_POST["cname"];
		$stmt = $mysqli->prepare("insert into Concert (concertName, description,locationId,eventDate,eventTime,ticketPrice,availableSeats,bookingSiteLink,overallRating,createdByType,createdBy,creationDate,bandId) values (?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param("ssissiisisisi", $_POST["cname"],$_POST["des"], $locid,$_POST["date"],$_POST["time"],$_POST["price"],$_POST["seats"],$_POST["link"],$rating,$type,$by,$create_date,$bandid);
		$create_date = date("Y-m-d H:i:s",time());
		$rating = 0;
		$type = 'BAND';
		$by = 1;
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("UPDATE User SET trustScore = trustScore + 4 WHERE userId = ?");
		$stmt->bind_param("i",$_SESSION["userid"]);
		$stmt->execute();
		$stmt->close();
		
		$echoSucc = 1;
		echo '<script type="text/javascript">
		function redirect()
		{
			window.location="uhome.php";
		}
		setTimeout(redirect, 2000);
		</script>';
		$stmt = $mysqli->prepare("select concertId from Concert where concertName = ?");
		$stmt->bind_param("s", $_POST["cname"]);
		$stmt->execute();
		$stmt->bind_result($cid);
		$stmt->fetch();
		$stmt->close();
	//if concert belongs to band, then only put band name. if solo concert, then band does not have this concert
	//if artist belong to band, and band hold this concert, then must put band name and leave artist name blank. do not insert both fields
		if(empty($_POST["band"])) {
			$stmt = $mysqli->prepare("insert into Artist_Concert (artistId,concertId) values (?,?) ");
			$stmt->bind_param("ii", $artid,$cid);
			$stmt->execute();
			$stmt->close();
		}
	}
	$mysqli->close();
	?>


	<div class="container">
		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("upconcert.php");?>">
			
			<div class="form-group">	
				<label class="control-label col-sm-3 col-xs-3"  for="Name"><font color="black">Concert Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_POST['cname']; ?>" name="cname">
					<?php if($echoCname == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$cnameErr.'</span>';}
					else if($echoCname == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert already exists</span>';}
					else if($echoSucc) { echo'<span class ="alert alert-success" role = "alert">Successfully posted!</span>';} 

					?>
				</div> 
			</div>


			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="email"><font color="black">Event Date<font color="red">*</font></font></label>
				<div class="col-sm-3 col-xs-5">
					<input type="date" class="form-control" value="<?php echo $_POST['date']; ?>"name="date" >
					<?php if($echoDate == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$dateErr.'</span>';}
					else if($echoDate == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Date must be in the future</span>';}
					else if($echoDate == 3) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">There is a time conflict</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="uName"><font color="black">Event Time<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-6">		
					<input type="time" class="form-control"  value="<?php echo $_POST['time']; ?>" name="time" >
					<?php if($echoTime == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$timeErr.'</span>';}?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="pass"><font color="black">Location Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_POST['loc']; ?>" name="loc"  >
					<?php if($echoLoc == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$locErr.'</span>';}
					else if($echoLoc == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Location not found</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="pass"><font color="black">City Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_POST['city']; ?>" name="city"  >
					<?php if($echoCity == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$cityErr.'</span>';}
					else if($echoCity == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">City not found</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="pass"><font color="black">Country Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_POST['country']; ?>" name="country"  >
					<?php if($echoCountry == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$countryErr.'</span>';}
					else if($echoCountry == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Country not found</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="uName"><font color="black">Description</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_POST['des']; ?>" name="des" >
				</div>
			</div>


			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="uName"><font color="black">Ticket Price</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="number" class="form-control"  value="<?php echo $_POST['price']; ?>" name="price" >
				</div>
			</div>


			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="uName"><font color="black">Available Seats</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="number" class="form-control"  value="<?php echo $_POST['seats']; ?>" name="seats" >
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="uName"><font color="black">Booking Link</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="url" class="form-control"  value="<?php echo $_POST['link']; ?>" name="link" >
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="pass"><font color="black">Band Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_POST['band']; ?>" name="band"  >
					<?php if($echoBand == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Band not found</span>';}
					else if($echoBand == 2) {echo'<span class ="alert alert-danger" id="inputErrNew" role = "alert">Leave artist field blank if artist belongs to a band</span>';}
					else if($echoBand == 3) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Must enter band or artist name</span>';}
					?>
				</div>
			</div>

			<div class="form-group" >
				<label class="control-label col-sm-3 col-xs-3" for="pass"><font color="black">Artist Name<font color="red">*</font></font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_POST['artist']; ?>" name="artist"  >
					<?php if($echoArt == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Artist not found</span>';}  ?>
				</div>
			</div>

			<div class="form-group" id="inputForm">
				<div class="col-sm-offset-3 col-sm-8 col-xs-offset-3">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Post</button>
				</div>
			</div>
		</form>
	</div>

</body>
</html>
