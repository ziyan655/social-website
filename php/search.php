<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Search Result</title>
	<link rel="stylesheet" href="../css/search.css"></link>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>

<body>
	<?php
	include ("include.php");
	$uid=$_SESSION["userid"];
	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<p class="navbar-brand">Search Result</p>
			</div>	

			<div class="collapse navbar-collapse" id = "myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<?php if($_SESSION["bmark"] == 1) { ?>
					<li><a href="bookmark.php"><span class="glyphicon glyphicon-pawn"></span> Bookmarks</a></li>
					<?php } ?>
					<?php if($_SESSION["bmark"] == 0) { ?>
					<li><a href="advSearch.php"><span class="glyphicon glyphicon-pawn"></span> Advanced Search</a></li>
					<?php } ?>
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
				</ul>
			</div>
		</div>
	</div>

	<?php
	//check what parameters hava been submitted by the user. use GET method to pass by url.
	if( !empty($_GET["aname"]) && $_GET["bname"]=="" && $_GET["mc"]=="" && $_GET["msc"]=="" && $_GET["city"]=="" ){
		$artistOnly = 1;
	}
	else if( !empty($_GET["bname"]) && $_GET["aname"]=="" && $_GET["mc"]=="" && $_GET["msc"]=="" && $_GET["city"]=="" ){
		$bandOnly = 1;
	}
	else if( !empty($_GET["mc"]) && $_GET["bname"]=="" && $_GET["aname"]=="" && $_GET["msc"]=="" && $_GET["city"]=="" ){
		$musicCatOnly=1;
	}
	else if( !empty($_GET["msc"]) && $_GET["bname"]=="" && $_GET["aname"]=="" && $_GET["mc"]=="" && $_GET["city"]=="" ){
		$musicSCatOnly = 1;
	}
	else if( !empty($_GET["city"]) && $_GET["bname"]=="" && $_GET["aname"]=="" && $_GET["mc"]=="" && $_GET["msc"]=="" ){
		$cityOnly = 1;
	}
	else if( !empty($_GET["city"]) && !empty($_GET["mc"]) && $_GET["bname"]=="" && $_GET["aname"]==""  && $_GET["msc"]=="" ){
		$mcCityOnly = 1;
	}
	else if( !empty($_GET["city"]) && !empty($_GET["msc"]) && $_GET["bname"]=="" && $_GET["aname"]==""  && $_GET["mc"]=="" ){
		$mscCityOnly = 1;
	}
	else if( !empty($_GET["city"]) && !empty($_GET["aname"]) && $_GET["bname"]=="" && $_GET["msc"]==""  && $_GET["mc"]=="" ){
		$artCityOnly = 1;
	}
	else if(!empty($_GET["city"]) && !empty($_GET["bname"]) && $_GET["aname"]=="" && $_GET["msc"]==""  && $_GET["mc"]=="" ){
		$bandCityOnly = 1;
	}
	else if( !empty($_GET["rate"]) && $_GET["msc"]=="" && $_GET["bname"]=="" && $_GET["aname"]=="" && $_GET["mc"]=="" && $_GET["city"]=="" ){
		$rateOnly = 1;
	}
	else if( !empty($_GET["rate"]) && !empty($_GET["city"]) && !empty($_GET["msc"]) && !empty($_GET["mc"]) && $_GET["bname"]=="" && !empty($_GET["aname"])){
		$exceptBand = 1;
	}


	?>
	<!--  fetch and display search results-->
	<div class="container">
		<?php if($artistOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts by artist <font color=red><?php echo $_GET["aname"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where artistName = ?)
						UNION (select distinct bconName,bconcertId from artSearch where artistName = ?)");
					$stmt->bind_param("ss", $_GET["aname"], $_GET["aname"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>



		<?php if($bandOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts by band <font color=red><?php echo $_GET["bname"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("select distinct bconName,bconcertId from artSearch where bandName = ?");
					$stmt->bind_param("s", $_GET["bname"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}

					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($musicCatOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts of Music Category <font color=red><?php echo $_GET["mc"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where mname = ?)
						UNION (select distinct bconName,bconcertId from artSearch where mname = ?)");
					$stmt->bind_param("ss", $_GET["mc"], $_GET["mc"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}

					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($musicSCatOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts of Music Sub-Category <font color=red><?php echo $_GET["msc"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where subCatName = ?)
						UNION (select distinct bconName,bconcertId from artSearch where subCatName = ?)");
					$stmt->bind_param("ss", $_GET["msc"], $_GET["msc"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}

					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($cityOnly ) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts at city <font color=red><?php echo $_GET["city"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where cityName = ?)
						UNION (select distinct bconName,bconcertId from artSearch where cityName = ?)");
					$stmt->bind_param("ss", $_GET["city"], $_GET["city"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($mcCityOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts of Music Category <font color=red><?php echo $_GET["mc"];?></font>at city <font color=red><?php echo $_GET["city"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where cityName = ? AND mname=?)
						UNION (select distinct bconName,bconcertId from artSearch where cityName = ? AND mname=?)");
					$stmt->bind_param("ssss", $_GET["city"], $_GET["mc"],$_GET["city"],$_GET["mc"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($mscCityOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts of Music Sub-Category <font color=red><?php echo $_GET["msc"];?></font>at city <font color=red><?php echo $_GET["city"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where cityName = ? AND subCatName=?)
						UNION (select distinct bconName,bconcertId from artSearch where cityName = ? AND subCatName=?)");
					$stmt->bind_param("ssss", $_GET["city"], $_GET["msc"],$_GET["city"],$_GET["msc"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($artCityOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts of artist <font color=red><?php echo $_GET["aname"];?></font>at city <font color=red><?php echo $_GET["city"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where cityName = ? AND artistName=?)
						UNION (select distinct bconName,bconcertId from artSearch where cityName = ? AND artistName=?)");
					$stmt->bind_param("ssss", $_GET["city"], $_GET["aname"],$_GET["city"],$_GET["aname"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($bandCityOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts of band <font color=red><?php echo $_GET["bname"];?></font>at city <font color=red><?php echo $_GET["city"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("select distinct bconName,bconcertId from artSearch where cityName = ? AND bandName=?");
					$stmt->bind_param("ss", $_GET["city"], $_GET["bname"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($rateOnly) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts of overall rating above <font color=red><?php echo $_GET["rate"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where overallRating > ?)
						UNION
						(select distinct bconName,bconcertId from artSearch where overallRating > ?)");
					$stmt->bind_param("ii", $_GET["rate"],$_GET["rate"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>


		<?php if($exceptBand) { ?>
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concerts by artist <font color=red><?php echo $_GET["aname"];?></font> on music category <font color=red><?php echo $_GET["mc"];?></font> sub-category <font color=red><?php echo $_GET["msc"];?></font> in city <font color=red><?php echo $_GET["city"];?></font> at an overall rating of <font color=red><?php echo $_GET["rate"];?></font></b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("(select distinct concertName,concertId from artSearch where overallRating > ? AND artistName = ? AND mname=? AND subCatName=? AND cityName=?)");
					$stmt->bind_param("issss", $_GET["rate"],$_GET["aname"],$_GET["mc"],$_GET["msc"],$_GET["city"]);
					$stmt->execute();
					$stmt->bind_result($concertName,$concertId);
					while($stmt->fetch()){
						if($concertName!= null && $concertId!=null) {
							echo '<tr>'.'<td>'.'<a href="vcon.php">'.$concertName.'</a>'.'</td>'.'</tr>';
						}
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
		<?php }?>
	</div>


	<?php

	$url=$_SERVER['REQUEST_URI']; 
	$_SESSION['urlSet'] = $url;
	if($_SESSION["bmark"]==0){
		$stmt = $mysqli->prepare("select * from User_Bookmark where userId = ? and url = ?");
		$stmt->bind_param("is", $uid,$url);
		$stmt->execute();
		if($stmt->fetch()) {
			$Bookmarked = 1;
		}
		$stmt->close();

		?> 
		<div class="container">
			<form role = "form" method="POST" action="<?php echo $url;?>">  
				<div class="form-group" >
					<button type="submit" class="btn margin btn-primary <?php if($Bookmarked) {echo 'disabled';}?> submitButton" name="submitBookm" id="submitButton"><?php if($Bookmarked) echo 'Bookmarked'; else echo 'Bookmark';?></button>
				</div>
			</form>
		</div>

		<?php
		if(isset($_POST["submitBookm"])){            
			$stmt = $mysqli->prepare("insert into User_Bookmark (userId, url) values (?,?)");
			$stmt->bind_param("is", $uid,$url);
			$stmt->execute();
			$stmt->close();
			echo '<div class="container"><div class ="alert alert-success" role = "alert">Search result bookmarked! &nbsp&nbspTrust Score + 1 </div></div>';
		}
	}
	$mysqli->close();
	?>

</body>
</html>
