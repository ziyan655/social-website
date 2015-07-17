<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Home</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/uhome.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>

<body>
	<?php
	include ("include.php");
	$username =  $_SESSION["username"];
	$userid = $_SESSION["userid"];
	$gname = $_SESSION["Name"];
	$_SESSION["bmark"] = 0;
	$_SESSION['urlSet'] = "";
	?>
	<?php

	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">

			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<p class="navbar-brand"><?php echo $gname?></p>
			</div>	

			<div class="collapse  navbar-collapse" id = "myNavbar">
				<ul class="nav navbar-nav">
					<li ><a href="ureview.php"><span class="glyphicon glyphicon-pencil"></span> Post Review</a></li>
					<li ><a href="flist.php"><span class="glyphicon glyphicon-heart"></span> Favorites</a></li>
					<li ><a href="uedit.php"><span class="glyphicon glyphicon-edit"></span> Edit Profile</a></li>
					<li ><a href="uwall.php"><span class="glyphicon glyphicon-upload"></span> Post Wall</a></li>
					<li ><a href="uatt.php"><span class="glyphicon glyphicon-music"></span> Concert Attendance</a></li>
					<li ><a href="uprof.php?userid=<?php echo $userid?>"><span class="glyphicon glyphicon-pawn"></span> Profile</a></li>
					<li ><a href="chkTS.php?userid=<?php echo $userid?>"><span class="glyphicon glyphicon-plus"></span> Add Concerts</a></li>
					<li class = "dropdown">
						<a class = "dropdown-toggle" data-toggle = "dropdown" href="#"><span class="glyphicon glyphicon-th-list"></span> Browse<span class="caret"></span></a>
						<ul class = "dropdown-menu">
							<li ><a href="vart.php"><span class="glyphicon glyphicon-pawn"></span> View Artists</a></li>
							<li ><a href="vuser.php"><span class="glyphicon glyphicon-pawn"></span> View Users</a></li>
							<li ><a href="vb.php"><span class="glyphicon glyphicon-pawn"></span> View Bands</a></li>
							<li ><a href="vcon.php"><span class="glyphicon glyphicon-pawn"></span> View Concerts</a></li>
							<li ><a href="bookmark.php"><span class="glyphicon glyphicon-pawn"></span> View Bookmarks</a></li>
							<li ><a href="advSearch.php"><span class="glyphicon glyphicon-search"></span> Advanced Search</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href = "logout.php"><span class = "glyphicon glyphicon-log-out"></span> Log out</a></li>
				</ul>
			</div> 
		</div>	
	</div>

	<?php



	$stmt = $mysqli->prepare("select count(*) FROM User u JOIN User_Followers uf JOIN User r WHERE u.userId=uf.userId AND uf.followingId=r.userId AND r.userId = ?");
	$stmt->bind_param("i", $_SESSION["userid"]);
	$stmt->execute();
	$stmt->bind_result($followerCount);
	$stmt->fetch();
	echo '<div class="container"><div class="fixed-widthFollower"><div class="follower">You now have ';
	echo '<span class="highlight" id = "followerColor">';
	echo $followerCount;
	echo '</span> followers!</div></div></div>';
	$_SESSION['fanCount'] = $followerCount;
	$stmt->close();


	if ($stmt = $mysqli->prepare("select concertName from Concert where eventDate > current_timestamp")) {
		$stmt->execute();
		$stmt->bind_result($cname);
		echo '<div class="container"><div class="fixed-widthContent"><div class="announceText">Upcoming Concerts ';
		echo '<ul class="highlight">';
		while($stmt->fetch()) {
			$cname = htmlspecialchars($cname);
			echo '<li>';
			echo $cname;
			echo '</li>';
		}
		echo '</ul>';
		echo '</div></div></div>';
		$stmt->close();
	}

	echo '<div class="container"><div class="fixed-widthContent"><div class ="announceText">Concerts by the artists you liked ';
	echo '<ul class = "highlight">';
	if ($stmt = $mysqli->prepare("SELECT cr.concertName FROM Concert cr, User u, User_Artist ua, Artist_Concert ac, Artist a, Location l, City c, Country cou WHERE c.cityId=l.cityId AND c.countryId=cou.countryId AND l.locationId=cr.locationId AND u.userId=ua.userId AND ua.artistId= ac.artistId AND ac.concertId=cr.concertId AND ua.artistId=a.artistId AND eventDate > current_timestamp AND u.userName= ?")) {
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($cname);
		while($stmt->fetch()) {
			$cname = htmlspecialchars($cname);
			echo '<li>';
			echo $cname;
			echo '</li>'; 
		}
		echo '</ul>';
		echo '</div></div></div>';
		$stmt->close();
	}

	echo '<div class="container"><div class="fixed-widthContent"><div class="announceText">Recommendations for you ';
	echo '<ul class = "highlight">';
	$res=$mysqli->query("CALL recommended_Concert('$username')");
	while($row = $res -> fetch_assoc()){
		echo '<li>';
		echo $row["concertName"];
		echo '</li>';
	}
	echo '</ul>';
	echo '</div></div></div>';
	$mysqli->close();
	?>

</body>
</html>
