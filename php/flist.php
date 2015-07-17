<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Favorite List</title>
	<link rel="stylesheet" href="../css/flist.css"></link>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container" >
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span> <span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<p class="navbar-brand">Favorite Lists</p>
			</div>	

			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
				</ul>
			</div>	
		</div>
	</div>


	<?php
	include "include.php";
	?>

	<div class ="container" id="bgImg">
		<div class="tab">
			<table class="table table-hover">
				<thead>
					<tr>
						<td><b>Favorite List Name</b></td>
						<td><b>Concert Names</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt = $mysqli->prepare("select listName from USer_Recommend_List where userId = ?");
					$stmt->bind_param("i", $_SESSION["userid"]);
					$stmt->execute();
					$stmt->bind_result($lname);
					$stmt->store_result();

					while($stmt->fetch()){
						echo '<tr>';
						echo '<td>'.$lname.'</td>';
						$stmt1 = $mysqli->prepare("select concertName from Concert ct JOIN Recommend_List_Concert rlc JOIN USer_Recommend_List url WHERE url.listName = ? AND url.listId = rlc.listId AND rlc.concertId = ct.concertId AND url.userId=?");
						$stmt1->bind_param("si", $lname,$_SESSION["userid"]);
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


		<?php
		$uid = $_SESSION["userid"];
		$cnameInsertErr = $cnameNewErr = $cnameDelErr = "";
		$cnameInsert = $lnameExisting = $lnameNew = $cnameNew ="";
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (!empty($_POST["cnameInsert"])) {
				$cnameInsert = test_input($_POST["cnameInsert"]);
				$cnameInsert = str_replace(' ', '', $cnameInsert);
				if (!preg_match("~^([a-z0-9]+,)+$~i", $cnameInsert)) {
					$cnameInsertErr = 'Usage: "name1,name2,name3,"';
				}
			}
			if(!empty($_POST["cnameNew"])) {
				$cnameNew = test_input($_POST["cnameNew"]);
				$cnameNew = str_replace(' ', '', $cnameNew);
				if (!preg_match("~^([a-z0-9]+,)+$~i", $cnameNew)) {
					$cnameNewErr = 'Usage: "name1,name2,name3,"';
				}
			}
			if (!empty($_POST["cnameDel"])) {
				$cnameDel = test_input($_POST["cnameDel"]);
				$cnameDel = str_replace(' ', '', $cnameDel);
				if (!preg_match("~^([a-z0-9]+,)+$~i",$cnameDel)) {
					$cnameDelErr = 'Usage: "name1,name2,name3,"';
				}
			}
		}
		function test_input($data) {
			$data = htmlspecialchars($data);
			return $data;
		}

		$stmt = $mysqli->prepare("select listId from USer_Recommend_List where listName = ? AND userId = ?");
		$stmt->bind_param("si", $_POST['lnameExisting'], $_SESSION["userid"]);
		$stmt->execute();
		$stmt->bind_result($lid);
		$stmt->fetch();
		$stmt->close();

		$stmt = $mysqli->prepare("select listId from USer_Recommend_List where listName = ? AND userId = ?");
		$stmt->bind_param("si", $_POST['lnameDel'], $_SESSION["userid"]);
		$stmt->execute();
		$stmt->bind_result($lid2);
		$stmt->fetch();
		$stmt->close();

		//this page allows users to dynamiclly manage their favorite music lists. e.g. add to list, add, delete lists, and delete from a list
		$echoCnameIns = 0;
		$echoLnameIns = 0;
		$echoCnameNew = 0;
		$echoLnameNew = 0;
		$echoCnameDel = 0;
		$echoLnameDel = 0;
		$echoLnameRev = 0;
		$echoSucIns = 0;
		$echoSucNew = 0;
		$echoSucDel = 0;
		$echoSucRev = 0;
		$echoSucClr = 0;

		//New List Name + Initial Concert Names
		if($cnameNewErr !=""){
			$echoCnameNew = 3;
		}
		else if(!empty($_POST["cnameNew"]) && !empty($_POST["lnameNew"]))	{
			$str = $_POST["cnameNew"];
			$str=rtrim($str, ",");
			$ary = explode(',',$str);
			$arrlength = count($ary);

			for($x = 0; $x < $arrlength; $x++) {
				$echoCnameNew = 1;
				$stmt->store_result();
				$stmt = $mysqli->prepare("select concertId from Concert where concertName = ? ");
				$stmt->bind_param("s", $ary[$x]);
				$tmp = $ary[$x];
				$stmt->execute();
				if(!$stmt->fetch())
				{
					$echoCnameNew = 2;
					$_SESSION['notFound'] = $tmp;
					break;			
				}
			}
			$stmt->close();

			$stmt = $mysqli->prepare("select listName from USer_Recommend_List WHERE userId = ? AND listName = ?");
			$stmt->bind_param("is", $_SESSION["userid"], $_POST["lnameNew"]);
			$stmt->execute();
			if ($stmt->fetch()) {
				$echoLnameNew = 2;
			}
			else {
				$echoLnameNew = 1;
			}
			$stmt->close();
		}

		//List Name + Concert Name Insert
		if($cnameInsertErr!=""){
			$echoCnameIns = 4;
		}
		else if(!empty($_POST["cnameInsert"]) && !empty($_POST["lnameExisting"])) {
			$str = $_POST["cnameInsert"];
			$str = rtrim($str, ",");
			$ary = explode(',',$str);
			$arrlength = count($ary);
			for($x = 0; $x < $arrlength; $x++) {
				$echoCnameIns = 1;
				$stmt->store_result();
				$stmt = $mysqli->prepare("select concertId from Concert where concertName = ?");
				$stmt->bind_param("s", $ary[$x]);
				$tmp = $ary[$x];
				$stmt->execute();
				if(!$stmt->fetch()) {
					$echoCnameIns = 2;
					$_SESSION['notFound'] = $tmp;
					break;
				}
			}
			$stmt->close();

			if($echoCnameIns == 1){
				$str = $_POST["cnameInsert"];
				$str = rtrim($str, ",");
				$ary = explode(',',$str);
				$arrlength = count($ary);

				for($x = 0; $x < $arrlength; $x++) {
					$echoCnameIns = 1;
					$stmt->store_result();
					$stmt = $mysqli->prepare("select concertName from Concert ct JOIN USer_Recommend_List url JOIN Recommend_List_Concert rlc where userId = ? AND url.listId = rlc.listId AND url.listName = ? AND rlc.concertId = ct.concertId AND concertName = ?");
					$stmt->bind_param("iss", $_SESSION["userid"],$_POST["lnameExisting"],$ary[$x]);
					$tmp = $ary[$x];
					$stmt->execute();
					$stmt->bind_result($cnam);
					if($stmt->fetch()) {
						$echoCnameIns = 3;
						$_SESSION['notFound'] = $tmp;
						break;
					}
				}
				$stmt->close();
			}

			$stmt = $mysqli->prepare("select listName from USer_Recommend_List where listName = ? AND userId = ?");
			$stmt->bind_param("si", $_POST['lnameExisting'], $_SESSION["userid"]);
			$stmt->execute();
			if(!$stmt->fetch()) {
				$echoLnameIns = 2;
			}
			else {
				$echoLnameIns = 1;
			}
			$stmt->close();
		}


		//Delete Concert Names
		if($cnameDelErr !=""){
			$echoCnameDel = 4;
		}
		else if(!empty($_POST["cnameDel"]) && !empty($_POST["lnameDel"])) {
			$str = $_POST["cnameDel"];
			$str = rtrim($str, ",");
			$ary = explode(',',$str);
			$arrlength = count($ary);
			for($x = 0; $x < $arrlength; $x++) {
				$echoCnameDel = 1;
				$stmt->store_result();
				$stmt = $mysqli->prepare("select concertId from Concert where concertName = ?");
				$stmt->bind_param("s", $ary[$x]);
				$tmp = $ary[$x];
				$stmt->execute();
				if(!$stmt->fetch()) {
					$echoCnameDel = 2;
					$_SESSION['notFound'] = $tmp;
					break;
				}
			}
			$stmt->close();
			if($echoCnameDel == 1){
				$str = $_POST["cnameDel"];
				$str = rtrim($str, ",");
				$ary = explode(',',$str);
				$arrlength = count($ary);

				for($x = 0; $x < $arrlength; $x++) {
					$echoCnameDel = 1;
					$stmt->store_result();
					$stmt = $mysqli->prepare("select concertName from Concert ct JOIN USer_Recommend_List url JOIN Recommend_List_Concert rlc where userId = ? AND url.listId = rlc.listId AND url.listName = ? AND rlc.concertId = ct.concertId AND concertName = ?");
					$stmt->bind_param("iss", $_SESSION["userid"],$_POST["lnameDel"],$ary[$x]);
					$tmp = $ary[$x];
					$stmt->execute();
					if(!$stmt->fetch() ) {
						$echoCnameDel = 3;
						$_SESSION['notFound'] = $tmp;
						break;
					}
				}
			}
			$stmt->close();


			$stmt = $mysqli->prepare("select listName from USer_Recommend_List WHERE userId = ? AND listName = ?");
			$stmt->bind_param("is", $_SESSION["userid"], $_POST["lnameDel"]);
			$stmt->execute();
			if (!$stmt->fetch()) {
				$echoLnameDel = 2;
			}
			else {
				$echoLnameDel = 1;
			}
			$stmt->close();
		}

			//List deletion
		if(!empty($_POST['lnameRemove'])) {
			$stmt = $mysqli->prepare("select listName from USer_Recommend_List where listName = ? AND userId = ?");
			$stmt->bind_param("si", $_POST['lnameRemove'], $_SESSION["userid"]);
			$stmt->execute();
			if($stmt->fetch()) {
				$echoLnameRev = 1;	
			}
			else {
				$echoLnameRev = 2;
			}
			$stmt->close();
		}



		$cnameNew=$_POST['cnameNew'];
		$lnameNew=$_POST['lnameNew'];
		$cnameInsert=$_POST['cnameInsert'];
		$lnameExisting=$_POST['lnameExisting'];
		$cnameDel=$_POST['cnameDel'];
		$lnameDel=$_POST['lnameDel'];
		$lnameRemove=$_POST['lnameRemove'];

		if($echoCnameIns == 1 && $echoLnameIns == 1) {
			$mysqli->query("CALL add_concert_in_list($uid,$lid,'$cnameInsert')");
			$stmt->close(); 
			$echoSucIns = 1;
			echo '<script type="text/javascript">
			function redirect()
			{
				window.location="flist.php";
			}
			setTimeout(redirect, 2000);
			</script>';
		}

		//no score added for this procedure, should add 2 pts.  TODO
		if($echoCnameNew == 1 && $echoLnameNew == 1) {
			$mysqli->query("CALL create_concert_list($uid, '$lnameNew', '$cnameNew')");
			$stmt->close();
			$echoSucNew = 1;
			echo '<script type="text/javascript">
			function redirect()
			{
				window.location="flist.php";
			}
			setTimeout(redirect, 2000);
			</script>';
		}

		if($echoCnameDel == 1 && $echoLnameDel == 1) {
			$mysqli->query("CALL delete_concert_in_list($lid2,'$cnameDel')");
			$stmt->close();
			$echoSucDel = 1;
			echo '<script type="text/javascript">
			function redirect()
			{
				window.location="flist.php";
			}
			setTimeout(redirect, 2000);
			</script>';
		}

		if($echoLnameRev == 1) {
			$mysqli->query("CALL delete_user_list($uid, '$lnameRemove')");
			$stmt->close();
			$echoSucRev = 1;
			echo '<script type="text/javascript">
			function redirect()
			{
				window.location="flist.php";
			}
			setTimeout(redirect, 2000);
			</script>';
		}

		if(isset($_POST["clear"])) {
			$mysqli->query("call delete_all_lists($uid)");
			$echoSucClr = 1;
			echo '<script type="text/javascript">
			function redirect()
			{
				window.location="flist.php";
			}
			setTimeout(redirect, 2000);
			</script>';

		}
		?>



		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("flist.php");?>">
			<div class="form-group"> 
				<label class="control-label col-sm-2"  for="Name"><font color="black">Concert Name</font></label>   
				<div class="col-sm-4">    
					<input type="text" class="form-control" value="<?php echo $_POST['cnameInsert']; ?>" name="cnameInsert">
					<?php if($echoCnameIns == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert <b>'.$_SESSION['notFound'].' </b>not found in database</span>';}
					else if($echoCnameIns == 3) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert <b>'.$_SESSION['notFound'].' </b>already in the list</span>';}
					else if($echoCnameIns == 4) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$cnameInsertErr.'</span>';}
					else if($echoSucIns) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Concert(s) added</span>';} 
					?>
				</div> 
			</div>
			<div class="form-group"> 
				<label class="control-label col-sm-2"  for="Name"><font color="black">List Name</font></label>   
				<div class="col-sm-4">    
					<input type="text" class="form-control" value="<?php echo $_POST['lnameExisting']; ?>" name="lnameExisting">
					<?php if($echoLnameIns == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">List not found</span>';}   
					?>
				</div> 
			</div>
			<div class="form-group" >
				<button type="submit" class="btn margin btn-primary" name="submitAdd" id="submitAdd" >Add</button>
			</div>
		</form>




		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("flist.php");?>">
			<div class="form-group"> 
				<label class="control-label col-sm-2"  for="Name"><font color="black">New List Name</font></label>   
				<div class="col-sm-4">    
					<input type="text" class="form-control" value="<?php echo $_POST['lnameNew']; ?>" name="lnameNew">
					<?php if($echoLnameNew == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">List already exists</span>';}
					?>
				</div> 
			</div>
			<div class="form-group"> 
				<label class="control-label col-sm-2"  for="Name"><font color="black">Initial Concert Name</font></label>   
				<div class="col-sm-4">    
					<input type="text" class="form-control" value="<?php echo $_POST['cnameNew']; ?>" name="cnameNew">
					<?php if($echoCnameNew == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert <b>'.$_SESSION['notFound'].' </b>not found in database</span>';}
					else if($echoCnameNew == 3) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$cnameNewErr.'</span>';}
					else if($echoSucNew) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">List created</span>';} 
					?>
				</div> 
			</div>
			<div class="form-group" >
				<button type="submit" class="btn margin btn-primary" name="submitIns" id="submitNewList" >Create</button>
			</div>
		</form>


		<form class="form-horizontal" id="formNew" method="post" action="<?php echo htmlspecialchars("flist.php");?>">
			<div class="form-group"> 
				<label class="control-label col-sm-2"  for="Name"><font color="black">Concert Deletion Name</font></label>   
				<div class="col-sm-4">    
					<input type="text" class="form-control" value="<?php echo $_POST['cnameDel']; ?>" name="cnameDel">
					<?php if($echoCnameDel == 2 && $echoLnameDel != 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert <b>'.$_SESSION['notFound'].' </b>not found in database</span>';}
					else if($echoCnameDel == 3 && $echoLnameDel != 2) { echo'<span class ="alert alert-danger" id="inputErr" role = "alert">Concert <b>'.$_SESSION['notFound'].' </b>not found in list</span>';} 
					else if($echoCnameDel == 4) { echo'<span class ="alert alert-danger" id="inputErr" role = "alert">'.$cnameDelErr.'</span>';} 
					else if($echoSucDel) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">Concert(s) deleted</span>';} 
					?>
				</div> 
			</div>
			<div class="form-group"> 
				<label class="control-label col-sm-2"  for="Name"><font color="black">List Name</font></label>   
				<div class="col-sm-4">    
					<input type="text" class="form-control" value="<?php echo $_POST['lnameDel']; ?>" name="lnameDel">
					<?php if($echoLnameDel == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">List not found</span>';}   
					?>
				</div> 
			</div>
			<div class="form-group" >
				<button type="submit" class="btn margin btn-primary" name="submitDel" id="submitDel" >Delete</button>
			</div>
		</form>



		<form class="form-horizontal" id="formNewB" method="post" action="<?php echo htmlspecialchars("flist.php");?>">
			<div class="form-group"> 
				<label class="control-label col-sm-2"  for="Name"><font color="black">List Deletion Name</font></label>   
				<div class="col-sm-4">    
					<input type="text" class="form-control" value="<?php echo $_POST['lnameRemove']; ?>" name="lnameRemove">
					<?php if($echoLnameRev == 2) {echo'<span class ="alert alert-danger" id="inputErr" role = "alert">List not found</span>';}
					else if($echoSucRev) { echo'<span class ="alert alert-success" id="inputErr" role = "alert">List removed</span>';} 
					?>
				</div> 
			</div>
			<div class="form-group" >
				<button type="submit" class="btn margin btn-primary" name="submitRev" id="submitRemove" >Remove</button>
			</div>
		</form>


		<form role = "form-horizontal" id="formNew" method="POST" action="<?php echo htmlspecialchars("flist.php");?>">
			<div class="form-group" >
				<button type="submit" class="btn margin btn-primary" id="submitButton" name="clear">Clear All Lists
					<?php if($echoSucClr == 1) {echo'<span class ="alert alert-success" id="sucess" role = "alert">Lists cleared</span>';}
					?></button>
				</div>
			</form>
		</div> 

	</body>
	</html>
