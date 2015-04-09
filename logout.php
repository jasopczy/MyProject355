<?php	
	
	// filename: logout.php, Joshua Sopczynski, cis355, 2015-03-30
	// this file changes the login variable so that you are now logged out then
	// redirects back to the login page
	
	session_start();
	if ($_SESSION["id"] != "loggedIn")
	    header("Location: login.php");
	
        $_SESSION["id"] = "";
	
	header('Location: login.php');

?>