<?php

    session_start();
    
    $error = "";
	$errorMessage = "";

    //log out
    if (array_key_exists("logout", $_GET)) {
        unset($_SESSION['id'], $_COOKIE['id']);
        setcookie('id', "", time() - (60*60));
        $_COOKIE['id'] = "";
    } else if ((array_key_exists('id', $_SESSION) AND $_SESSION['id']) OR (array_key_exists('id', $_COOKIE) AND $_COOKIE['id'])) {
      	header("Location: loggedinpage.php");
    }

    //Sign up form
    if (array_key_exists("submit", $_POST)) {
        include("connection.php");
        if (!$_POST['email']) {
            $error .= "An email address is required<br>";
        }
        if (!$_POST['password']) {
            $error .= "An password address is required<br>";
        }
        if ($error) {
            $error = "<strong>There were error(s) in your form:</strong><br>".$error;
        } else {
            if ($_POST['signUp'] == '1') {
            
                $query = "SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
                $result = mysqli_query($link, $query);
                if (mysqli_num_rows($result) > 0) {
                    $error .= "That email address is already used<br>";
                } else {
                    $query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";
                    if (!mysqli_query($link, $query)) {
                        $error .= "Could not sign you up - please try again later<br>";
                    } else {
                        $query = "UPDATE `users` SET password = '".password_hash($_POST['password'], PASSWORD_DEFAULT)."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";
                        mysqli_query($link, $query);
                        $_SESSION['id'] = mysqli_insert_id($link);
                        if ($_POST['stayLoggedIn'] == '1') {
                            setcookie("id", mysqli_insert_id($link), time() + (60*60*24));
                        }
                        header ("Location: loggedinpage.php");
                    }
                }
            } else {
                $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
                $result = mysqli_query($link, $query);
                $row = mysqli_fetch_array($result);
				echo $row;
                if (isset($row)) {
                    $hashedpassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    if(password_verify($_POST['password'], $row['password'])){
                        $_SESSION['id'] = $row['id'];
                        if ($_POST['stayLoggedIn'] == '1') {
                                setcookie("id", $row['id'], time() + (60*60*24));
                            }
                            header("Location: loggedinpage.php");
                    } else {
                        $error .= "That email/password combination could not be found<br>";
                    }
                } else {
                    $error .= "That email/password combination could not be found<br>";
                }
            }
        }
    }
	if ($error) {
		 $errorMessage = '<div class="alert alert-warning" role="alert">' . $error . '</div>'; 
	}


?>
<?php include ("header.php"); ?>

<div id="homePageContainer" class="container">
			<h1>Secret diary</h1>
			<p><strong>Store your thoughts permanently and securely</strong></p>
			<form method="post" id="signUpForm">
				<p>Interested? Sign up now!</p>
				<div class="form-group">
					<input class="form-control" name="email" type="email" placeholder="Your email">
				</div>
				<div class="form-group">
					<input class="form-control" name="password" type="password" placeholder="Password">
				</div>
				<div class="checkbox"><label>
					<input name="stayLoggedIn" type="checkbox" value=1>Stay Logged in
				</label></div>
				<div class="form-group">
					<input type="hidden" name="signUp" value="1">
					<input class="btn btn-primary" name="submit" type="submit" value="Sign up">
				</div>
				<p><a class="toggleForms">Log in</a></p>
			</form>			

			<form method="post" id="logInForm">
				<p>Enter your email address and password to log in</p>
				<div class="form-group">
					<input class="form-control" name="email" type="email" placeholder="Your email">
				</div>
				<div class="form-group">
					<input class="form-control" name="password" type="password" placeholder="Password">
				</div>
				<div class="checkbox"><label>
					<input name="stayLoggedIn" type="checkbox" value=1>Stay Logged in
				</label></div>
				<div class="form-group">
					<input class="form-control" type="hidden" name="signUp" value="0">
					<input class="btn btn-success" name="submit" type="submit" value="Log in">
				</div>
				<p><a class="toggleForms">Sign up</a></p>
			</form>


			<div id="error"><?php echo $errorMessage ?>
			</div>
</div>
		
<?php include ("footer.php"); ?>

    