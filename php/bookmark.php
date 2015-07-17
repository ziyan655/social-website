<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bookmarks</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/bookmark.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	//dislay bookmarked results from a user
	include "include.php";
	$uid = $_SESSION["userid"];
	$_SESSION["bmark"] = 1;
	$gname = $_SESSION["Name"];
	?>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand"><?php echo $gname;?> Bookmarks</p>
			</div>

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
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
						<td><b>Artist</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					//bookmarked user profiles
					$stmt = $mysqli->prepare( "select url from User_Bookmark where userId = ?" );
					$stmt->bind_param( "i", $uid );
					$stmt->execute();
					$stmt->bind_result( $url );
					$stmt->store_result();
					while ( $stmt->fetch() ) {
						//split the url and find out what are the search filters applied by the user when he first submitted the search query. after that, we need
						//to display search results according to the filter gaven.	
						$str= explode( "?", $url );
						$str = explode( "=", $str[1] );
						echo $_GET['$url'];
						if ( $str[0] =="artistid" ) {
							$stmt1 = $mysqli->prepare( "select artistName from Artist where artistId = ?" );
							$stmt1->bind_param( "i", $str[1] );
							$stmt1->execute();
							$stmt1->bind_result( $artname );
							$stmt1->fetch();
							$stmt1->close();
							echo '<tr><td><a href=';
							echo "$url";
							echo '>';
							echo "$artname";
							echo '</a></td></tr>';
						}
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
						<td><b>User</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					//find bookmarked band profile data
					$stmt = $mysqli->prepare( "select url from User_Bookmark where userId = ?" );
					$stmt->bind_param( "i", $uid );
					$stmt->execute();
					$stmt->bind_result( $url );
					$stmt->store_result();
					while ( $stmt->fetch() ) {
						$str= explode( "?", $url );
						$str = explode( "=", $str[1] );
						echo $_GET['$url'];
						if ( $str[0] == "userid" ) {
							$stmt2 = $mysqli->prepare( "select Name from User where userId = ?" );
							$stmt2->bind_param( "i", $str[1] );
							$stmt2->execute();
							$stmt2->bind_result( $uname );
							$stmt2->fetch();
							$stmt2->close();
							echo '<tr><td><a href=';
							echo "$url";
							echo '>';
							echo "$uname";
							echo '</a></td></tr>';
						}
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
						<td><b>Band</b></td>
					</tr>
				</thead>
				<tbody>

					<?php
					//use userId as a key to find the bookmarked url in the database
					$stmt = $mysqli->prepare( "select url from User_Bookmark where userId = ?" );
					$stmt->bind_param( "i", $uid );
					$stmt->execute();
					$stmt->bind_result( $url );
					$stmt->store_result();
					while ( $stmt->fetch() ) {
						$str= explode( "?", $url );
						$str = explode( "=", $str[1] );
						echo $_GET['$url'];
						if ( $str[0] == "bandid" ) {
							$stmt3 = $mysqli->prepare( "select bandName from Band where bandId = ?" );
							$stmt3->bind_param( "i", $str[1] );
							$stmt3->execute();
							$stmt3->bind_result( $bname );
							$stmt3->fetch();
							$stmt3->close();
							echo '<tr><td><a href=';
							echo "$url";
							echo '>';
							echo "$bname";
							echo '</a></td></tr>';
						}
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
						<td><b>Search</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					//find bookmarked search results
					$stmt = $mysqli->prepare( "select url from User_Bookmark where userId = ?" );
					$stmt->bind_param( "i", $uid );
					$stmt->execute();
					$stmt->bind_result( $url );
					$stmt->store_result();
					while ( $stmt->fetch() ) {
						$str= explode( "?", $url );
						$str = explode( "=", $str[1] );
						echo $_GET['$url'];
						if ( $str[0] != "bandid" && $str[0] != "userid" && $str[0] != "artistid" ) {
							$p=parse_url( $url );
							$url_q= $p['query'];
							parse_str( $url_q, $out );
							$aname=$out['aname'];
							$bname=$out['bname'];
							$mc=$out['mc'];
							$msc=$out['msc'];
							$city=$out['city'];
							$rate=$out['rate'];
							echo '<tr><td><a href=';
							echo "$url";
							echo '>';
							echo " Artist: <font color=red>$aname</font>";
							echo " Band: <font color=red>$bname</font>";
							echo " Music Category: <font color=red>$mc</font>";
							echo " Music Sub-Category: <font color=red>$msc</font>";
							echo " City: <font color=red>$city</font>";
							echo " Rating: <font color=red>$rate</font>";
							echo '</a></td></tr>';
						}
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
