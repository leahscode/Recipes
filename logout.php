<?php
// This page lets the user logout.


session_start(); // Access the existing session.

if (isset($_SESSION['user_id'])) {

	
	$_SESSION = []; // Clear the variables.
	session_destroy(); // Destroy the session itself.
	setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0); // Destroy the cookie.

    echo "success";
}
?>