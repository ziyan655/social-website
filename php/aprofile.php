<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Artist Profile</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/aprofile.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	include "include.php";
	$aid=$_GET["artistid"];
	$uid=$_SESSION["userid"];
	
	$stmt = $mysqli->prepare( "select artistName from Artist where artistId = ?" );
	$stmt->bind_param( "i", $aid );
	$stmt->execute();
	$stmt->bind_result( $gname );
	$stmt->fetch();
	$stmt->close();
	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target="#myNavbar">
				<span class="icon-bar"></span> <span class="icon-bar"></span> <span
				class="icon-bar"></span>
			</button>
			<p class="navbar-brand"><?php echo $gname?></p>
		</div>

		<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav navbar-right">
				<?php if ( isset( $_SESSION["userid"] ) && $_SESSION["bmark"]==0 ) { ?>
				<li><a href="vart.php"><span class="glyphicon glyphicon-pawn"></span> Artists</a></li>
				<?php } ?>
				<?php if ( $_SESSION["bmark"] == 1 ) { ?>
				<li><a href="bookmark.php"><span class="glyphicon glyphicon-pawn"></span> Bookmarks</a></li>
				<?php } ?>
				<?php if ( isset( $_SESSION["userid"] ) ) { ?>
				<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
				<?php } else {?>
				<li><a href="ahome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
				<?php }?>
			</ul>
		</div>
	</div>
</div>


<?php
//select all the data from back-end and display on the page
$stmt = $mysqli->prepare( "select wall from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $wall );
$stmt->fetch();
$stmt->close();

$stmt = $mysqli->prepare( "select artistName,countryName,websiteLink from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $name, $country, $website );
$stmt->fetch();

$stmt->close();

$stmt = $mysqli->prepare( "select distinct concertName from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $conName );
$concertArr = array();
$i = 0;
while ( $stmt->fetch() ) {
	$concertArr[$i] = $conName;
	$i++;
}
$stmt->close();

$stmt = $mysqli->prepare( "select distinct bconName from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $bconName );
$bconcertArr = array();
$i = 0;
while ( $stmt->fetch() ) {
	$bconcertArr[$i] = $bconName;
	$i++;
}
$stmt->close();

$stmt = $mysqli->prepare( "select distinct mname from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $mcatName );
$mcatArr = array();
$i = 0;
while ( $stmt->fetch() ) {
	$mcatArr[$i] = $mcatName;
	$i++;
}
$stmt->close();

$stmt = $mysqli->prepare( "select distinct subCatName from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $mscatName );
$mscatArr = array();
$i = 0;
while ( $stmt->fetch() ) {
	$mscatArr[$i] = $mscatName;
	$i++;
}
$stmt->close();

$stmt = $mysqli->prepare( "select distinct bandName from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $bandName );
$stmt->fetch();
$stmt->close();

$stmt = $mysqli->prepare( "select description from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $des );
$stmt->fetch();
$stmt->close();

$stmt = $mysqli->prepare( "select distinct bmname from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $bandMcatName );
$stmt->fetch();
$stmt->close();

$stmt = $mysqli->prepare( "select distinct bSubName from artProfile where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $bandMscatName );
$bandmscatArr = array();
$i = 0;
while ( $stmt->fetch() ) {
	$bandmscatArr[$i] = $bandMscatName;
	$i++;
}
$stmt->close();

$stmt = $mysqli->prepare( "select count(*) from User_Artist where artistId = ?" );
$stmt->bind_param( "i", $aid );
$stmt->execute();
$stmt->bind_result( $fanCount );
$stmt->fetch();
$stmt->close();
?>


<div class ="container">
	<div class="fixed-width">
		<p class = "wall"> <?php echo $wall;?></p>
	</div>
</div>

<div class ="container">
	<div class="fixed-width">
		<p class = "prof"> Name: <?php echo $name;?></p>
		<p class = "prof"> Country: <?php echo $country;?></p>
		<p class = "prof"> About this artist: <?php echo $des;?></p>
		<p class = "prof"> Website: <?php echo $website;?></p>
		<p class = "prof"> Solo Concerts: <?php
		echo "| ";
		foreach ( $concertArr as $s ) {
			echo $s." | ";
		}
		?></p>
		<p class = "prof"> Band Concerts: <?php
		echo "| ";
		foreach ( $bconcertArr as $s ) {
			echo $s." | ";
		}
		?></p>
		<p class = "prof"> Music Category: <?php
		echo "| ";
		foreach ( $mcatArr as $s ) {
			echo $s." | ";
		}
		?></p>
		<p class = "prof"> Music Sub-category: <?php
		echo "| ";
		foreach ( $mscatArr as $s ) {
			echo $s." | ";
		}
		?></p>
		<p class = "prof"> Band belongs to: <?php echo $bandName;?></p>
		<p class = "prof"> Band Music Category: <?php echo $bandMcatName;?></p>
		<p class = "prof"> Band Music Sub-category: <?php
		echo "| ";
		foreach ( $bandmscatArr as $s ) {
			echo $s." | ";
		}
		?></p>
		<p class = "prof"> Number of fans: <?php echo $fanCount;?></p>
	</div>
</div>


<?php
$url=$_SERVER['REQUEST_URI'];
if ( isset( $_SESSION["userid"] ) && $_SESSION["bmark"]==0 ) {
	$stmt = $mysqli->prepare( "select * from User_Bookmark where userId = ? and url = ?" );
	$stmt->bind_param( "is", $uid, $url );
	$stmt->execute();
	if ( $stmt->fetch() ) {
		$Bookmarked = 1;
	}
	$stmt->close();

	$stmt = $mysqli->prepare( "select * from User_Artist where userId = ? and artistId = ?" );
	$stmt->bind_param( "ii", $uid, $aid );
	$stmt->execute();
	if ( $stmt->fetch() ) {
		$Followed = 1;
	}
	$stmt->close();
	?>

	<div class="container">
		<form class="form-inline" role = "form" method="POST" action="aprofile.php?artistid=<?php echo $aid?>">
			<div class="form-group" >
				<button type="submit" class="btn margin btn-primary <?php if ( $Followed ) {echo 'disabled';}?> submitButton" name="submitFollow" id="submitButton" ><?php if ( $Followed ) echo 'Followed'; else echo 'Follow';?></button>
			</div>
			<div class="form-group" >
				<button type="submit" class="btn margin btn-primary <?php if ( $Bookmarked ) {echo 'disabled';}?> submitButton" name="submitBookm" id="submitButtonNew"><?php if ( $Bookmarked ) echo 'Bookmarked'; else echo 'Bookmark';?></button>
			</div>
		</form>
	</div>

	<?php
	if ( isset( $_POST["submitBookm"] ) ) {
		$stmt = $mysqli->prepare( "insert into User_Bookmark (userId, url) values (?,?)" );
		$stmt->bind_param( "is", $uid, $url );
		$stmt->execute();
		$stmt->close();
		echo '<div class="container"><div class ="alert alert-success" role = "alert">Profile Page Bookmarked! &nbsp&nbspTrust Score + 1 </div></div>';
	}
	
	if ( isset( $_POST["submitFollow"] ) ) {
		$mysqli->query( "CALL User_Artist_fan($uid,$aid)" );
		echo '<div class="container"><div class ="alert alert-success" role = "alert">Successfully followed '.$gname.'!  Trust Score + 1 </div></div>';
	}
}
else if ( $_SESSION["bmark"]==1 ) {
	$stmt = $mysqli->prepare( "select * from User_Artist where userId = ? and artistId = ?" );
	$stmt->bind_param( "ii", $uid, $aid );
	$stmt->execute();
	if ( $stmt->fetch() ) {
		$Followed = 1;
	}
	$stmt->close();
	?>
	<div class="container">
		<form class="form-inline" role = "form" method="POST" action="aprofile.php?artistid=<?php echo $aid?>">
			<div class="form-group" >
				<button type="submit" class="btn margin btn-primary <?php if ( $Followed ) {echo 'disabled';}?> submitButton" name="submitFollow" id="submitButton" ><?php if ( $Followed ) echo 'Followed'; else echo 'Follow';?></button>
			</div>
		</form>
	</div>
	<?php
	
	if ( isset( $_POST["submitFollow"] ) ) {
		$mysqli->query( "CALL User_Artist_fan($uid,$aid)" );
		echo '<div class="container"><div class ="alert alert-success" role = "alert">Successfully followed '.$gname.'!  Trust Score + 1 </div></div>';
	}
}
$mysqli->close();
?>

</body>
</html>
