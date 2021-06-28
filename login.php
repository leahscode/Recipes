<?php # Script 12.3 - login.php
// This page processes the login form submission.
// Upon successful login, the user is redirected.
// Two included files are necessary.
// Send NOTHING to the Web browser prior to the setcookie() lines!

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// For processing the login:
	require('includes/login_functions.inc.php');

	// Need the database connection:
	require('../mysqli_connect.php');

	// Check the login:
	list($check, $data) = check_login($dbc, $_POST['email'], $_POST['pass']);

	if ($check) { // OK!

		session_start();

		$_SESSION['user_id'] = $data['c_user_id'];
		$_SESSION['first_name'] = $data['c_fname'];

		// Redirect:
		if (isset($_GET['redirect'])) {
			$redirect = $_GET['redirect'];
			redirect_user($redirect);
		} else {
			redirect_user();
		}

	} else { // Unsuccessful!

		// Assign $data to $errors for error reporting
		// in the login_form.inc.php file.
		$errors = $data;

	}

	mysqli_close($dbc); // Close the database connection.

} // End of the main submit conditional.

// Create the page:
include('includes/login_form.inc.php');
?>