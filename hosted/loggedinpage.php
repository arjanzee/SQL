<?php

    session_start();

	if(array_key_exists('id', $_COOKIE)) {
        $_SESSION['id'] = $_COOKIE['id'];
		
    }

    if(array_key_exists('id', $_SESSION))	 {        
		include("connection.php");
		$query = "SELECT * FROM `users` WHERE id = ".mysqli_real_escape_string($link, $_SESSION['id'])." LIMIT 1";		
		$row = mysqli_fetch_array(mysqli_query($link, $query));
		$diaryContent = $row['diary'];
		$email = $row['email'];
	} else {
        header ("Location: index.php");
    }

	include("header.php");

?>

	<nav class="navbar navbar-light bg-faded navbar-fixed-top">
	  <a class="navbar-brand" href="#">Secret Diary</a>
	  
	  <div class="float-xs-right">
		<a href="index.php?logout=1"><button class="btn btn-success" type="submit">Log Out</button></a>
	  </div>
	</nav>
	
	<div class="container-fluid" id="containerLoggedInPage">
		<div id="diaryHeader" class="container">Secret diary of: <?php echo $email ?></div>
		<textarea class="form-control" id="diary"><?php	echo $diaryContent;	?></textarea>	
	</div>

<?php include("footer.php"); ?>