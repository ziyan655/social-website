<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>All Concerts</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/vcon.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>
	<?php
	include ("include.php");
	?>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid ">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand">Concerts Overview</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<?php if($_SESSION["urlSet"] != "") { ?>
					<li><a href="<?php echo $_SESSION["urlSet"];?>"><span class="glyphicon glyphicon-pawn"></span> Search Result</a></li>
					<?php } ?>
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
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
						<td><b>Description</b></td>
						<td><b>Date</b></td>
						<td><b>Time</b></td>
						<td><b>Location</b></td>
						<td><b>Price</b></td>
						<td><b>Booking Link</b></td>
						<td><b>Overall Rating</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("select distinct concertName,Concert.description,eventDate,eventTime,locationName,ticketPrice,bookingSiteLink,overallRating FROM Concert JOIN Location WHERE Concert.locationId=Location.locationId");
					$stmt->execute();
					$stmt->bind_result($cname,$description,$date,$time,$location,$price,$booklink,$overalRating);
					while($stmt->fetch()){
						echo '<tr>';
						echo '<td>'.$cname.'</td>';
						echo '<td>'.$description.'</td>';
						echo '<td>'.$date.'</td>';
						echo '<td>'.$time.'</td>';
						echo '<td>'.$location.'</td>';
						echo '<td>'.$price.'</td>';
						echo '<td>'.$booklink.'</td>';
						echo '<td>'.$overalRating.'</td>';
						echo '</tr>';
					}
					$stmt->close();
					$mysqli->close();
					?>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
