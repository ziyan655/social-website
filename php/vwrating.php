<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>All Reviews</title>
	<link rel="stylesheet" href="../css/vwrating.css"></link>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	//see all the concert ratings. just fetch related info from the database
	include ("include.php");
	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand">Concert Ratings</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="ahome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>


	<div class="container-fluid" id="bgImg">
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concert Name</b></td>
						<td><b>Review</b></td>
						<td><b>Score</b></td>
					</tr>
				</thead>
				<tbody><p id="thead">All Ratings</p>
					<?php
					$stmt = $mysqli->prepare("select concertName, reviewDescription, rating 
						from Concert c join Concert_Review_Rating crr
						where c.concertId = crr.concertId;");
					$stmt->execute();
					$stmt->bind_result($name,$review,$score);

					while($stmt->fetch()){
						echo '<tr>';
						echo '<td>'.$name.'</td>';
						echo '<td>'.$review.'</td>';
						echo '<td>'.$score.'</td>';
						echo '</tr>';
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>

		<div class="tabNew">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concert Name</b></td>
						<td><b>Review</b></td>
						<td><b>Score</b></td>
					</tr>
				</thead>
				<tbody><p id="theadNew">Ratings Related to your band</p>
					<?php
					$stmt = $mysqli->prepare("select distinct crr.reviewDescription, crr.rating,ct.concertName FROM Concert_Review_Rating crr JOIN Concert ct WHERE crr.concertId=ct.concertId AND ct.bandId= ?");
					$stmt->bind_param("i", $_SESSION["bandid"]);
					$stmt->execute();
					$stmt->bind_result($review,$score,$name);
					while($stmt->fetch()){
						echo '<tr>';
						echo '<td>'.$name.'</td>';
						echo '<td>'.$review.'</td>';
						echo '<td>'.$score.'</td>';
						echo '</tr>';
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>


		<div class="tabNew2">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concert Name</b></td>
						<td><b>Review</b></td>
						<td><b>Score</b></td>
					</tr>
				</thead>
				<tbody><p id="theadNew2">Ratings Related to you</p>
					<?php
					$stmt = $mysqli->prepare("select distinct crr.reviewDescription, crr.rating,ct.concertName FROM Concert_Review_Rating crr JOIN Concert ct JOIN Artist_Concert ac JOIN Artist WHERE crr.concertId=ct.concertId AND ct.concertId= ac.concertId AND ac.artistId=Artist.artistId AND Artist.artistId=?");
					$stmt->bind_param("i", $_SESSION["artistid"]);
					$stmt->execute();
					$stmt->bind_result($review,$score,$name);
					while($stmt->fetch()){
						echo '<tr>';
						echo '<td>'.$name.'</td>';
						echo '<td>'.$review.'</td>';
						echo '<td>'.$score.'</td>';
						echo '</tr>';
					}
					$stmt->close();
					$mysqli->close();
					?>
				</tbody>
			</table>
		</div>
	</div> <!-- container class-->

</body>
</html>
