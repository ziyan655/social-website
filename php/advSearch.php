<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Advanced Search</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link rel="stylesheet" href="../css/advSearch.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</head>
<body>

	<?php
	include ("include.php");
	$_SESSION['urlSet'] = "";
	$username =  $_SESSION["username"];
	$userid = $_SESSION["userid"];
	?>

	<div class="navbar navbar-inverse">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<p class="navbar-brand">Advanced Search</p>
			</div>	

			<div class="collapse navbar-collapse" id = "myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="uhome.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="container">
		<form class="form-horizontal" id="formNew" method="GET" action="<?php echo htmlspecialchars("search.php");?>">
			
			<div class="form-group">	
				<label class="control-label col-sm-offset-1 col-xs-3"  for="Name"><font color="black">Artist Name</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_GET['aname']; ?>" name="aname">
				</div> 
			</div>
			
			<div class="form-group" >
				<label class="control-label col-sm-offset-1 col-xs-3" for="bandbelong"><font color="black">Band Name</font></label> 	 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_GET['bname']; ?>" name="bname"  >
				</div>
			</div>
			
			<div class="form-group" >
				<label class="control-label col-sm-offset-1 col-xs-3" for="bmc"><font color="black">Music Category</font></label>  	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_GET['mc']; ?>" name="mc"   >
				</div>
			</div>
			
			<div class="form-group" >
				<label class="control-label col-sm-offset-1 col-xs-3" for="subcat"><font color="black">Musical Subcategory</font></label>  	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control" value="<?php echo $_GET['msc']; ?>" name="msc" >
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-offset-1 col-xs-3" for="uName"><font color="black">City</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<input type="text" class="form-control"  value="<?php echo $_GET['city']; ?>"name="city"  >
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label  col-sm-offset-1 col-xs-3" for="uName"><font color="black">Overall Rating Above</font></label> 	
				<div class="col-sm-3 col-xs-5">		
					<select class="form-control" value="<?php echo $_GET['rate']; ?>" name = "rate">
						<option value="0"> </option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
					</select>
				</div>
			</div>		
			
			<div class="form-group" >
				<div class="col-sm-offset-4 col-xs-offset-2">
					<button type="submit" class="btn btn-success margin" name="submit" id="submitButton" >Search</button>
				</div>
			</div>
		</form>
	</div>

</body>
</html>


