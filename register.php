<?php 
session_start(); // Start the session.


$page_title = 'Register';
include ('includes/header.inc.php');
echo '<link rel="stylesheet" href="styles/forms.css">';
include ('includes/nav.inc.php');

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	require ('../mysqli_connect.php'); // Connect to the db.
		
	$errors = array(); // Initialize an error array.
	
	// Check for a first name:
	if (empty($_POST['first_name'])) {
		$errors[] = 'You forgot to enter your first name.';
	} else {
		$fname = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
	}
	
	// Check for a last name:
	if (empty($_POST['last_name'])) {
		$errors[] = 'You forgot to enter your last name.';
	} else {
		$lname = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
	}
	
	// Check for an email address:
	if (empty($_POST['email'])) {
		$errors[] = 'You forgot to enter your email address.';
	} else {
        $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
        
        // check for unique email
        $q = "SELECT c_user_id FROM contributor WHERE c_email = '$email'";
        $r = @mysqli_query($dbc, $q); // Run the query.
        // $num = mysqli_num_rows($r);
        if (mysqli_num_rows($r) != 0 ){
            $errors[] = "email already exists";
        }
    }
    
 	
	// Check for a password and match against the confirmed password:
	if (!empty($_POST['pass1'])) {
		if ($_POST['pass1'] != $_POST['pass2']) {
			$errors[] = 'Your password did not match the confirmed password.';
		} else {
			$password = mysqli_real_escape_string($dbc, trim($_POST['pass1']));
		}
	} else {
		$errors[] = 'You forgot to enter your password.';
	}
	
	if (empty($errors)) { // If everything's OK.
	
		// Register the user in the database...
 
		// Make the query:
		$q = "INSERT INTO contributor (c_fname, c_lname, c_email, c_password) VALUES ('$fname', '$lname', '$email', SHA1('$password'))";		
		$r = @mysqli_query ($dbc, $q); // Run the query.
		if ($r) { // If it ran OK.

			// get user id			
			$q = 'SELECT c_user_id FROM contributor WHERE (c_email="'.$email.'" AND c_password=SHA1("'.$password.'"))';
			$r = @mysqli_query($dbc, $q); // Run the query.

			// Check the result:
			if (mysqli_num_rows($r) == 1) {

				// Fetch the record:
				$row = mysqli_fetch_array($r, MYSQLI_ASSOC);

				// session_start();
				$_SESSION['user_id'] = $row['c_user_id'];
				$_SESSION['first_name'] = $fname;

				// Print a message:
				echo "<h1>Thank you, $fname!</h1>
				<p>You are now registered. You can now submit recipes!</p><p><br /></p>";	
			}

		
		} else { // If it did not run OK.
			
			// Public message:
			echo '<h1>System Error</h1>
			<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>'; 
			
			// Debugging message:
			echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
						
		} // End of if ($r) IF.
		
		mysqli_close($dbc); // Close the database connection.

		// Include the footer and quit the script:
		include ('includes/footer.inc.php'); 
		exit();
		
	} else { // Report the errors.
	
		echo '<h1>Error!</h1>
		<p class="error">The following error(s) occurred:<br />';
		foreach ($errors as $msg) { // Print each error.
			echo " - $msg<br />\n";
		}
		echo '</p><p>Please try again.</p><p><br /></p>';
		
	} // End of if (empty($errors)) IF.
	
	mysqli_close($dbc); // Close the database connection.

} // End of the main Submit conditional.

// The form will be sent when: the user requests it the first time (not via POST)
// or if errors were found. For successful registration, the script is exited 
// before it reaches this point.
include ('includes/register_form.inc.php');
echo '<p><a href="login.php">Registered? Log in here.</a></p>';
include ('includes/footer.inc.php');
?>