<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Artist Home</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/ahome.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	//Artist homepage
	include ("include.php");
	$artistid = $_SESSION["artistid"];
	$stmt = $mysqli->prepare("select Band.bandId from Band JOIN Artist WHERE Band.bandId = Artist.bandId AND artistId= ?");
	$stmt->bind_param("i", $artistid);
	$stmt->execute();
	$stmt->bind_result($var);
	if ($stmt->fetch()) {
		$_SESSION["bandid"] = $var; 
		$stmt->close();
	}
	$username =  $_SESSION["username"];
	$gname = $_SESSION["Name"];
	?>

	<div class = "navbar navbar-inverse navbar-fixed-top">
		<div class = "container">
			<div class = "navbar-header ">	
				<button type = "button" class = "navbar-toggle" data-toggle = "collapse" data-target = "#myNavbar">
					<span class = "icon-bar"></span>
					<span class = "icon-bar"></span>
					<span class = "icon-bar"></span>
				</button>
				<p class = "navbar-brand"><?php echo $gname; ?></p> 
			</div>

			<div class = "collapse navbar-collapse" id = "myNavbar">
				<ul class = "nav navbar-nav">
					<li><a href = "apost.php"><span class = "glyphicon glyphicon-plus"></span> Post Concerts</a></li>
					<!-- <?php if(isset($_SESSION["bandid"])){ ?> -->
					<li class = "dropdown">
						<a class = "dropdown-toggle" data-toggle = "dropdown" href="#"><span class="glyphicon glyphicon-th-list"></span> Edit <span class="caret"></span></a>
						<ul class = "dropdown-menu">
							<li><a href = "aedit.php"><span class = "glyphicon glyphicon-edit"></span> Edit Profile</a></li>
							<li><a href = "abandedit.php"><span class = "glyphicon glyphicon-edit"></span> Edit Band Profile</a></li>
						</ul>
					</li>
					<!--      <?php } ?>-->


					<li><a href = "vwrating.php"><span class = "glyphicon glyphicon-stats"></span> Ratings</a></li>
					<li><a href = "awall.php"><span class = "glyphicon glyphicon-upload"></span> Post on Wall</a></li>
					<li><a href = "aprofile.php?artistid=<?php echo $artistid?>"><span class = "glyphicon glyphicon-pawn"></span> Profile</a></li>
					<?php 
					if(isset($_SESSION["bandid"])){
						echo '<li><a href = "bprof.php?bandid=';
						echo $var;
						echo '"><span class = "glyphicon glyphicon-pawn"></span> Band Profile</a></li>';
					}	?>
				</ul>
				<ul class = "nav navbar-nav navbar-right">
					<li><a href = "logout.php"><span class = "glyphicon glyphicon-log-out"></span> Log out</a></li>
				</ul>
			</div>
		</div>
	</div>

	<?php
	//fetch from database and display concerts of different categories as news feed
	$aid =  $_SESSION["artistid"];
	$stmt = $mysqli->prepare("select count(*) from User_Artist ua JOIN Artist a where a.artistId = ? AND a.artistId=ua.artistId");
	$stmt->bind_param("i", $aid);
	$stmt->execute();
	$stmt->bind_result($attname);
	$stmt->fetch();
	echo '<div class="container"><div class="row"><div class="col-xs-12 col-xs-offset-3 col-sm-offset-4"><div class="follower">You now have <font color=red><b>' .$attname. '</b></font></span> followers!</div></div></div></div>';
	$stmt->close();

	if ($stmt = $mysqli->prepare("select concertName from Concert where eventDate > current_timestamp")) {
 // $stmt->bind_param("s", $_GET["user_id"]);
		$stmt->execute();
		$stmt->bind_result($cname);
		echo '<div class="container"><div class="row"><div class="col-xs-12 col-xs-offset-3 col-sm-offset-4"><div class="announceText">Upcoming Concerts ';
		echo '<ul>';
		while($stmt->fetch()) {
			$cname = htmlspecialchars($cname);
			echo '<li>';
			echo $cname;
			echo '</li>'; 
		}
		echo '</ul>';
		echo '</div></div></div></div>';
		$stmt->close();
	}
	$username =  $_SESSION["username"];
	if ($stmt = $mysqli->prepare("select concertName from Concert ct JOIN Artist_Concert ac where ct.concertId=ac.concertId AND artistId = ?")) {
		$stmt->bind_param("i", $_SESSION["artistid"]);
		$stmt->execute();
		$stmt->bind_result($cname);
		echo '<div class="container"><div class="row"><div class="col-xs-12 col-xs-offset-3 col-sm-offset-4"><div class="announceText">Concerts of your performance ';
		echo '<ul>';
		while($stmt->fetch()) {
			$cname = htmlspecialchars($cname);
			echo '<li>';
			echo $cname;
			echo '</li>';       
		}
		echo '</ul>';
		echo '</div></div></div></div>';
		$stmt->close();
	}

	$mysqli->close();
	?>
</body>
</html>