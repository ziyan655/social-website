<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Profile</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/uprof.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	//displays user profile
	include ("include.php");
	//must use GET method, so queries can be stored in the url. then we can fetch data according to the user id
	//POST method stores user id in $POST[] array which would be gone after submitting from another page. 
	$uid = $_GET["userid"];
	$suid = $_SESSION["userid"];
	$stmt = $mysqli->prepare("select wall from userProfile where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($wall);
	$stmt->fetch();
	$stmt->close();

	$stmt = $mysqli->prepare("select Name from User where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($gname);
	$stmt->fetch();
	$stmt->close();

	$stmt = $mysqli->prepare("select Name,yearOfBirth,cityName,countryName from userProfile where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($name,$yob,$city,$country);
	$stmt->fetch();
	$stmt->close();

	$stmt = $mysqli->prepare("select distinct artistName from userProfile where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($artname);
	$artNameArr = array();
	$i = 0;
	while($stmt->fetch()){
		$artNameArr[$i] = $artname;
		$i++;
	}
	$stmt->close();

	$stmt = $mysqli->prepare("select distinct bandName from userProfile where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($bandname);
	$bandNameArr = array();
	$i = 0;
	while($stmt->fetch()){
		$bandNameArr[$i] = $bandname;
		$i++;
	}
	$stmt->close();

	$stmt = $mysqli->prepare("select distinct mname from userProfile where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($mcatname);
	$mcatNameArr = array();
	$i = 0;
	while($stmt->fetch()){
		$mcatNameArr[$i] = $mcatname;
		$i++;
	}
	$stmt->close();

	$stmt = $mysqli->prepare("select distinct fName from userProfile where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($fname);
	$fNameArr = array();
	$i = 0;
	while($stmt->fetch()){
		$fNameArr[$i] = $fname;
		$i++;
	}
	$stmt->close();

	$stmt = $mysqli->prepare("select distinct attName from userProfile where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($attname);
	$attNameArr = array();
	$i = 0;
	while($stmt->fetch()){
		$attNameArr[$i] = $attname;
		$i++;
	}
	$stmt->close();


	$stmt = $mysqli->prepare("select distinct concertName,reviewDescription,rating from userProfile where userId = ?");
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($cName,$r,$sco);
	$concertArr = array();
	$reviewArr = array();
	$ratingArr = array();
	$i = 0;
	while($stmt->fetch()){
		$concertArr[$i] = $cName;
		$reviewArr[$i] = $r;
		$ratingArr[$i] = $sco;
		$i++;
	}
	$stmt->close();
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
					<?php if( $suid != $uid && $_SESSION["bmark"]==0) {?>
					<li class = "pull-right"><a href="vuser.php"><span class="glyphicon glyphicon-pawn"></span> Users</a></li>	
					<?php } ?>	
					<?php if($_SESSION["bmark"] == 1) { ?>
					<li><a href="bookmark.php"><span class="glyphicon glyphicon-pawn"></span> Bookmarks</a></li>
					<?php } ?>
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
				</ul>
			</div>	
		</div>
	</div>

	<div class ="container">
		<div class="fixed-widthProf">
			<p class = "wall"> <?php echo $wall;?></p>
		</div>	
	</div>

	<div class ="container">
		<div class="fixed-widthProf">
			<p class = "prof"> Name: <?php echo $name;?></p>
			<p class = "prof"> Year of Birth: <?php echo $yob;?></p>
			<p class = "prof"> Home City: <?php echo $city;?></p>
			<p class = "prof"> Home Country: <?php echo $country;?></p>
			<p class = "prof"> Liked Artists: <?php 
			echo "| ";
			foreach($artNameArr as $s) {
				echo $s." | ";
			}
			?></p>
			<p class = "prof"> Liked Bands: <?php 
			echo "| ";
			foreach($bandNameArr as $s) {
				echo $s." | ";
			}
			?></p>
			<p class = "prof"> Musical Tastes: <?php 
			echo "| ";
			foreach($mcatNameArr as $s) {
				echo $s." | ";
			}
			?></p>
			<p class = "prof"> Currently Following: <?php 
			echo "| ";
			foreach($fNameArr as $s) {
				echo $s." | ";
			}
			?></p>
			<p class = "prof"> Concert Attendance: <?php 
			echo "| ";
			foreach($attNameArr as $s) {
				echo $s." | ";
			}
			?></p>
			<p class = "prof"> Number of followers: <?php echo $_SESSION['fanCount'];?></p>
		</div>	
	</div>


	<div class ="container">
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Concert Name</b></td>
						<td><b>Score</b></td>
						<td><b>Review</b></td>
					</tr>
				</thead>
				<tbody><p class="thead">Concert Ratings</p> 
					<?php
					$length = sizeof($concertArr);
					$k = 0;
					while($k < $length) {
						echo '<tr>';
						echo '<td>'.$concertArr[$k].'</td>';
						echo '<td>'.$ratingArr[$k].'</td>';
						echo '<td>'.$reviewArr[$k].'</td>';
						echo '</tr>';
						$k++;
					}
					?>
				</tbody>
			</table>
		</div> 
	</div>

	<div class ="container">
		<div class="tabNew">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>List Name</b></td>
						<td><b>Concert Names</b></td>
					</tr>
				</thead>
				<tbody><p id="theadNew">Favorite Concert Lists</p>
					<?php
					$stmt = $mysqli->prepare("select listName from USer_Recommend_List where userId = ?");
					$stmt->bind_param("i", $uid);
					$stmt->execute();
					$stmt->bind_result($lname);
					$stmt->store_result();

					while($stmt->fetch()){
						echo '<tr>';
						echo '<td>'.$lname.'</td>';
						$stmt1 = $mysqli->prepare("select concertName from Concert ct JOIN Recommend_List_Concert rlc JOIN USer_Recommend_List url WHERE url.listName = ? AND url.listId = rlc.listId AND rlc.concertId = ct.concertId");
						$stmt1->bind_param("s", $lname);
						$stmt1->execute();
						$stmt1->bind_result($cname);
						echo '<td>';
						echo "| ";
						while($stmt1->fetch()){
							echo $cname." ";
							echo " | ";
						}
						echo '</td>';
						echo '</tr>';
						$stmt1->close();
					}
					$stmt->close();
					?>
				</tbody>
			</table>
		</div>
	</div>

	<?php
	$url=$_SERVER['REQUEST_URI'];
	if( $suid != $uid && $_SESSION["bmark"]==0) {


		$stmt = $mysqli->prepare("select * from User_Bookmark where userId = ? and url = ?");
		$stmt->bind_param("is", $suid,$url);
		$stmt->execute();
		if($stmt->fetch()) {
			$Bookmarked = 1;
		}
		$stmt->close();

		$stmt = $mysqli->prepare("select * from User_Followers where userId = ? and followingId = ?");
		$stmt->bind_param("ii", $suid,$uid);
		$stmt->execute();
		if($stmt->fetch()) {
			$Followed = 1;
		}
		$stmt->close();
		?>
		<div class="container">
			<form class="form-inline" role = "form" method="POST" action="uprof.php?userid=<?php echo $uid?>">  
				<div class="form-group" >
					<button type="submit" class="btn margin btn-primary <?php if($Followed) {echo 'disabled';}?> submitButton" name="submitFollow" id="submitButton" ><?php if($Followed) echo 'Followed'; else echo 'Follow';?></button>
				</div>
				<div class="form-group" >
					<button type="submit" class="btn margin btn-primary <?php if($Bookmarked) {echo 'disabled';}?> submitButton" name="submitBookm" id="submitButtonNew"><?php if($Bookmarked) echo 'Bookmarked'; else echo 'Bookmark';?></button>
				</div>
			</form>
		</div>

		<?php

		if(isset($_POST["submitFollow"])){
			$mysqli->query("CALL user_follow($suid,$uid)");
			echo '<div class="container"><div class ="alert alert-success" id="suc" role = "alert">Successfully followed '.$gname.'!  Trust Score + 1 </div></div>';
		}

		if(isset($_POST["submitBookm"])){
			$stmt = $mysqli->prepare("insert into User_Bookmark (userId, url) values (?,?)");
			$stmt->bind_param("is", $suid,$url);
			$stmt->execute();
			$stmt->close();
			echo '<div class="container"><div class ="alert alert-success" id="suc" role = "alert">Profile Page Bookmarked! &nbsp&nbspTrust Score + 1 </div></div>';
		}
	}
	else if($_SESSION["bmark"]==1){
		$stmt = $mysqli->prepare("select * from User_Followers where userId = ? and followingId = ?");
		$stmt->bind_param("ii", $suid,$uid);
		$stmt->execute();
		if($stmt->fetch()) {
			$Followed = 1;
		}
		$stmt->close();
		?>
		<div class="container">
			<form class="form-inline" role = "form" method="POST" action="uprof.php?userid=<?php echo $uid?>">  
				<div class="form-group" >
					<button type="submit" class="btn margin btn-primary <?php if($Followed) {echo 'disabled';}?> submitButton" name="submitFollow" id="submitButton" ><?php if($Followed) echo 'Followed'; else echo 'Follow';?></button>
				</div>
			</form>
		</div>
		<?php

		if(isset($_POST["submitFollow"])){
			$mysqli->query("CALL user_follow($suid,$uid)");
			echo '<div class="container"><div class ="alert alert-success" role = "alert">Successfully followed '.$gname.'! &nbsp&nbspTrust Score + 1 </div></div>';
		}
	}
	$mysqli->close();
	?>

</body>
</html>
