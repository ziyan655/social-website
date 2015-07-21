<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>View Bands</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/vb.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>


	<?php
	//view list of bands
	include ("include.php");
	$gname = $_SESSION["Name"];
	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand"><?php echo $gname?></p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
				</ul>
			</div>	
		</div>
	</div>

	<div class="container">
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>All Bands</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("select distinct bandName,bandId from Band");
					$stmt->execute();
					$stmt->bind_result($bandname,$bandid);
					while($stmt->fetch()){
						echo '<tr>'.'<td>'.'<a href="bprof.php?bandid='.$bandid.'">'.$bandname.'</a>'.'</td>'.'</tr>';
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
						<td><b>Bands Liked</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("select distinct bandName,bandId from userProfile WHERE userId = ?");
					$stmt->bind_param("i", $_SESSION["userid"]);
					$stmt->execute();
					$stmt->bind_result($bandname,$bandid);
					while($stmt->fetch()){
						echo '<tr>'.'<td>'.'<a href="bprof.php?bandid='.$bandid.'">'.$bandname.'</a>'.'</td>'.'</tr>';
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
