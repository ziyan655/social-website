<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Band Profile</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/bprof.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	//display band profile]
	include "include.php";
	$bid=$_GET["bandid"];
	$uid = $_SESSION["userid"];
	$stmt = $mysqli->prepare( "select description from Band where bandId = ?" );
	$stmt->bind_param( "i", $bid );
	$stmt->execute();
	$stmt->bind_result( $bio );
	$stmt->fetch();
	$stmt->close();


	$stmt = $mysqli->prepare( "select bandName from Band where bandId = ?" );
	$stmt->bind_param( "i", $bid );
	$stmt->execute();
	$stmt->bind_result( $bname );
	$stmt->fetch();
	$stmt->close();

	$stmt = $mysqli->prepare( "select distinct concertName from Concert where bandId = ? AND eventDate < current_timestamp" );
	$stmt->bind_param( "i", $bid );
	$stmt->execute();
	$stmt->bind_result( $cname );
	$cNameArr = array();
	$i = 0;
	while ( $stmt->fetch() ) {
		$cNameArr[$i] = $cname;
		$i++;
	}
	$stmt->close();

	$stmt = $mysqli->prepare( "select distinct concertName from Concert where bandId = ? AND eventDate > current_timestamp" );
	$stmt->bind_param( "i", $bid );
	$stmt->execute();
	$stmt->bind_result( $cfname );
	$cfNameArr = array();
	$i = 0;
	while ( $stmt->fetch() ) {
		$cfNameArr[$i] = $cfname;
		$i++;
	}
	$stmt->close();

	$stmt = $mysqli->prepare( "select distinct mc.name from Music_Category mc JOIN Band_M_Cat bmc JOIN Band b WHERE b.bandId = ? AND b.bandId=bmc.bandId AND bmc.musicCatId=mc.musicCatId" );
	$stmt->bind_param( "i", $bid );
	$stmt->execute();
	$stmt->bind_result( $bmcatname );
	$stmt->fetch();
	$stmt->close();

	$stmt = $mysqli->prepare( "select distinct msc.subCatName from Music_SubCategory msc JOIN Band_M_Sub bms JOIN Band b WHERE b.bandId = ? AND b.bandId=bms.bandId AND bms.subCatId=msc.subCatId" );
	$stmt->bind_param( "i", $bid );
	$stmt->execute();
	$stmt->bind_result( $bmscatName );
	$bmscatNameArr = array();
	$i = 0;
	while ( $stmt->fetch() ) {
		$bmscatNameArr[$i] = $bmscatName;
		$i++;
	}
	$stmt->close();


	$stmt = $mysqli->prepare( "select artistName from Band JOIN Artist WHERE Band.bandId = ? AND Band.bandId=Artist.bandId" );
	$stmt->bind_param( "i", $bid );
	$stmt->execute();
	$stmt->bind_result( $bandMem );
	$bandMemArr = array();
	$i = 0;
	while ( $stmt->fetch() ) {
		$bandMemArr[$i] = $bandMem;
		$i++;
	}
	$stmt->close();

	$stmt = $mysqli->prepare( "select count(*) from User_Band where bandId = ?;" );
	$stmt->bind_param( "i", $bid );
	$stmt->execute();
	$stmt->bind_result( $fanCount );
	$stmt->fetch();
	$stmt->close();
	?>



	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<p class="navbar-brand"><?php echo $bname?></p>
			</div>

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<?php if ( isset( $_SESSION["userid"] ) && $_SESSION["bmark"] != 1 ) { ?>
					<li><a href="vb.php"><span class="glyphicon glyphicon-pawn"></span> Bands</a></li>
					<?php } ?>
					<?php if ( $_SESSION["bmark"] == 1 ) { ?>
					<li><a href="bookmark.php"><span class="glyphicon glyphicon-pawn"></span> Bookmarks</a></li>
					<?php } ?>
					<li><a href=
						<?php if ( isset( $_SESSION["userid"] ) ) {
							echo "uhome.php";
						}
						else {
							echo "ahome.php";
						}
						?>
						><span class="glyphicon glyphicon-home"></span> Home</a>
					</li>
				</ul>
			</div>
		</div>
	</div>




	<div class ="container" >
		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<p class = "bio"> <?php echo $bio;?></p>
			</div>
		</div>
	</div>

	<div class ="container" >
		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<p class = "prof"> Past Concerts: <?php
				echo "| ";
				foreach ( $cNameArr as $s ) {
					echo $s." | ";
				}
				?></p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<p class = "prof"> Future Concerts: <?php
				echo "| ";
				foreach ( $cfNameArr as $s ) {
					echo $s." | ";
				}
				?></p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<p class = "prof"> Band Musical Category: <?php echo $bmcatname;?>
				</p>
				<p class = "prof"> Band Musical Sub-Category: <?php
				echo "| ";
				foreach ( $bmscatNameArr as $s ) {
					echo $s." | ";
				}
				?></p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<p class = "prof"> Band Members: <?php
				echo "| ";
				foreach ( $bandMemArr as $s ) {
					echo $s." | ";
				}
				?></p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<p class = "prof"> Total number of fans: <?php echo $fanCount;?></p>
			</div>
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

		$stmt = $mysqli->prepare( "select * from User_Band where userId = ? and bandId = ?" );
		$stmt->bind_param( "ii", $uid, $bid );
		$stmt->execute();
		if ( $stmt->fetch() ) {
			$Followed = 1;
		}
		$stmt->close();
		?>

		<div class="container">                                       
			<form class="form-inline" role = "form" method="POST" action="bprof.php?bandid=<?php echo $bid?>">
				<div class="form-group" >
					<div class="col-xs-offset-0">
						<button type="submit" class="btn margin btn-primary <?php if ( $Followed ) {echo 'disabled';}?> submitButton" name="submitFollow" id="button" style="width:175px"><?php if ( $Followed ) echo 'Followed'; else echo 'Follow';?></button>
					</div>
				</div>
				<div class="form-group" >
					<div class="col-xs-offset-0">
						<button type="submit" class="btn margin btn-primary <?php if ( $Bookmarked ) {echo 'disabled';}?> submitButton" name="submitBookm" id="button"><?php if ( $Bookmarked ) echo 'Bookmarked'; else echo 'Bookmark';?></button>
					</div>
				</div>
			</form>
		</div>

		<?php

		if ( isset( $_POST["submitFollow"] ) ) {
			$mysqli->query( "CALL User_Band_like($uid,$bid)" );
			echo '<div class="container"><div class ="alert alert-success" role = "alert">You\'re a fan of this band now! &nbsp&nbspTrust Score + 1 </div></div>';
		}

		if ( isset( $_POST["submitBookm"] ) ) {
			$stmt = $mysqli->prepare( "insert into User_Bookmark (userId, url) values (?,?)" );
			$stmt->bind_param( "is", $uid, $url );
			$stmt->execute();
			$stmt->close();
			echo '<div class="container"><div class ="alert alert-success" role = "alert">Profile Page Bookmarked! &nbsp&nbspTrust Score + 1 </div></div>';
		}
	}
	else if ( $_SESSION["bmark"]==1 ) {
		//if this band is already followed, then the follow button should be unclickable
		$stmt = $mysqli->prepare( "select * from User_Band where userId = ? and bandId = ?" );
		$stmt->bind_param( "ii", $uid, $bid );
		$stmt->execute();
		if ( $stmt->fetch() ) {
			$Followed = 1;
		}
		$stmt->close();
		?>
		<div class="container">
			<form class="form-inline" role = "form" method="POST" action="bprof.php?bandid=<?php echo $bid?>">
				<div class="form-group" >
					<div class="col-xs-offset-0">
						<button type="submit" class="btn margin btn-primary <?php if ( $Followed ) {echo 'disabled';}?> submitButton" name="submitFollow" id="button" ><?php if ( $Followed ) echo 'Followed'; else echo 'Follow';?></button>
					</div>
				</div>
			</form>
		</div>
		<?php

		if ( isset( $_POST["submitFollow"] ) ) {
			$mysqli->query( "CALL User_Band_like($uid,$bid)" );
			echo '<div class="container"><div class ="alert alert-success" role = "alert">You\'re a fan of this band now! &nbsp&nbspTrust Score + 1 </div></div>';
		}
	}
	$mysqli->close();
	?>

</body>
</html>
