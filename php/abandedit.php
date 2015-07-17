<!DOCTYPE html>
<html>		
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Edit Band</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/abandedit.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>

<div class="navbar navbar-inverse">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
			</button>
			<p class="navbar-brand">Edit Band Profile</p>
		</div>	

		<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="ahome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>	
			</ul>
		</div>	
	</div>
</div>

<?php
include "include.php";
$websiteErr = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (!empty($_POST["web"])) {
		$website = test_input($_POST["web"]);

		if (!preg_match("/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/",$website)) {
			$websiteErr = "Invalid URL";
		} 
	}
}
function test_input($data) {
	$data = htmlspecialchars($data);
	return $data;
}
?>

<?php
//Edit band profile
$insertBcat = 0;
$insertBscat = 0;
$insertWeb = 0;
$removeBscat = 0;
$bandid = $_SESSION["bandid"];


$echoWeb = 0;
$echoBcat = 0;
$echoBscat = 0;
$echoRmscat = 0;
$echoBio = 0;

$stmt = $mysqli->prepare("select musicCatId from Band_M_Cat where bandId = ?");
$stmt->bind_param("i", $bandid);		
$stmt->execute();
$stmt->bind_result($mcatid);
$stmt->fetch();	
$stmt->close();


if(!empty($_POST["bcat"])) {
	$stmt = $mysqli->prepare("select musicCatId from Music_Category where name = ?");
	$stmt->bind_param("s", $_POST['bcat']);
	$stmt->execute();
	$stmt->bind_result($mcatidNew);
	if($stmt->fetch()) {	
                  if($mcatidNew != $mcatid) {		//if not updating on the same category name
                  	$mcatid = $mcatidNew;
                  	$insertBcat = 1;
                  }
                }
                else { 
                	$echoBcat = 1;
                }
                $stmt->close();                       
              }

              if($insertBcat) {	
              	$stmt = $mysqli->prepare("UPDATE Band_M_Cat SET musicCatId =? WHERE bandId = ?");
              	$stmt->bind_param("ii", $mcatid, $bandid);
              	$stmt->execute();
              	$stmt->close();
								//remove all the sub-cats from previous main category
              	$stmt = $mysqli->prepare("DELETE FROM Band_M_Sub WHERE bandId = ?");
              	$stmt->bind_param("i", $bandid);
              	$stmt->execute();
              	$stmt->close();
              	$echoBcat = 2;
              }



              if(!empty($_POST["bscat"])) {
              	$stmt = $mysqli->prepare("select subCatId from Music_SubCategory where subCatName = ? AND musicCatId = ?");
              	$stmt->bind_param("si", $_POST['bscat'],$mcatid);
              	$stmt->execute();
              	$stmt->bind_result($bscatid);
              	if($stmt->fetch()) {
              		$insertBscat = 1;
              	}
              	else { 
              		$echoBscat = 1;
              	}
              	$stmt->close();                       
              }

              if($insertBscat) {	
              	$stmt = $mysqli->prepare("INSERT INTO Band_M_Sub (bandId, subCatId) values (?,?)");
              	$stmt->bind_param("ii", $bandid, $bscatid);
              	$stmt->execute();
              	$stmt->close();
              	$echoBscat = 2;
              }


              if(!empty($_POST["rbscat"])) {
              	$stmt = $mysqli->prepare("select a.subCatId 
              		from Band_M_Sub a join Music_SubCategory b 
              		where a.subCatId = b.subCatId AND subCatName = ? AND bandId = ?");
              	$stmt->bind_param("si", $_POST['rbscat'], $bandid);
              	$stmt->execute();
              	$stmt->bind_result($removeBscatid);
              	if($stmt->fetch()) {
              		$removeBscat = 1;
              	}
              	else { 
              		$echoRmscat = 1;
              	}
              	$stmt->close();                       
              }
              if($removeBscat) {
              	$stmt = $mysqli->prepare("DELETE FROM Band_M_Sub WHERE bandId = ? AND subCatId = ?");
              	$stmt->bind_param("ii", $bandid, $removeBscatid);
              	$stmt->execute();
              	$stmt->close();
              	$echoRmscat = 2;
              }

              if($websiteErr!="") {
              	$echoWeb = 1;
              }
              else {$insertWeb = 1;}

              if(!empty($_POST['web']) && $insertWeb) {
              	$stmt = $mysqli->prepare("UPDATE Band SET url =? WHERE bandId=?");
              	$stmt->bind_param("si", $_POST["web"],$bandid);
              	$stmt->execute();
              	$stmt->close();
              	$echoWeb = 2;
              }


              if(!empty($_POST["bio"])) {
              	$stmt = $mysqli->prepare("UPDATE Band SET description =? WHERE bandId = ?");
              	$stmt->bind_param("si", $_POST["bio"],$bandid);
              	$stmt->execute();
              	$stmt->close();
              	$echoBio = 1;
              }
              $mysqli->close();
              ?>


              <div class="container">
              	<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("abandedit.php");?>">
              		<div class="form-group" >
              			<label class="control-label col-sm-4" for="uName"><font color="black">Update Band Musical Category</font></label> 	
              			<div class="col-sm-3">		
              				<input type="text" class="form-control"  value="<?php echo $_POST['bcat']; ?>" name="bcat" >
              				<?php if($echoBcat == 1) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music category not found</span>';} 
              				else if($echoBcat == 2) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Music category updated</span>';}
              				?>
              			</div>
              		</div>

              		<div class="form-group" >
              			<label class="control-label col-sm-4" for="pass"><font color="black">Add Band Musical Sub-category</font></label> 	
              			<div class="col-sm-3">		
              				<input type="text" class="form-control" value="<?php echo $_POST['bscat']; ?>" name="bscat"  >
              				<?php if($echoBscat == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music sub-category not found</span>';
              				else if($echoBscat == 2) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Music sub-category updated</span>';}	?>
              			</div>
              		</div>

              		<div class="form-group" >
              			<label class="control-label col-sm-4" for="pass"><font color="black">Remove Band Musical Sub-category</font></label> 	
              			<div class="col-sm-3">		
              				<input type="text" class="form-control" value="<?php echo $_POST['rbscat']; ?>" name="rbscat"  >
              				<?php if($echoRmscat == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Music sub-category not found</span>';
              				else if($echoRmscat == 2) {echo'<span class ="alert alert-success" id="inputErr" role = "alert">Music sub-category removed</span>';}	?>
              			</div>
              		</div>	

              		<div class="form-group" >
              			<label class="control-label col-sm-4" for="pass"><font color="black">Update Official Website</font></label> 	
              			<div class="col-sm-3">		
              				<input type="text" class="form-control" value="<?php echo $_POST['web']; ?>" name="web"  >
              				<?php if($echoWeb == 1) echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$websiteErr.'</span>';
              				else if($echoWeb == 2) echo'<span class ="alert alert-success" id="inputErr" role = "alert">Official website updated</span>';
              				?>
              			</div>
              		</div>

              		<div class="form-group" >
              			<label class="control-label col-sm-4" for="pass"><font color="black">Update Home Page Bio</font></label> 	
              			<div class="col-sm-4">		
              				<textarea class="form-control" placeholder="Write here.." name="bio" rows="2"><?php echo $_POST['bio']; ?></textarea>
              				<?php if($echoBio) echo'<span class ="alert alert-success" id="inputSuc" role = "alert">Bio updated</span>';	?>
              			</div>
              		</div>


              		<div class="form-group" >
              			<div class="col-sm-offset-4 col-sm-6">
              				<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Update</button>
              			</div>
              		</div>
              	</form>
              </div>
            </body>
            </html>
