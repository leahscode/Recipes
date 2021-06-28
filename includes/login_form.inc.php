<?php # Script 12.1 - login_page.inc.php
// This page prints any errors associated with logging in
// and it creates the entire login page, including the form.
// Include the header:
$page_title = 'Login';
include('includes/header.inc.php'); 
?>
<script type="text/javascript" src="add_recipe.js"></script>
<link rel="stylesheet" href="styles/forms.css">
<!-- <link rel="stylesheet" href="styles/add_recipe.css"> -->
<?php include("includes/nav.inc.php"); 

// Print any error messages, if they exist:
if (isset($errors) && !empty($errors)) {
	echo '<h1>Error!</h1>
	<p class="error">The following error(s) occurred:<br>';
	foreach ($errors as $msg) {
		echo " - $msg<br>\n";
	}
	echo '</p><p>Please try again.</p>';
}

// Display the form:
?><h1>Login</h1>
<form action="" method="post">
    <!-- <p>Before adding your recipe, make sure that you are registered.</p> -->
    <p>Please enter your login:</p>
    <p><label for="email">Email: </label><input type="text" name="email" size="20" maxlength="60" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" > </p>
    <p><label for="password">Password: </label><input type="password" name="pass" size="10" maxlength="20" value="<?php if (isset($_POST['pass'])) echo $_POST['pass']; ?>" ></p>
    <button type="submit" name="submit" value="login">Login</button>
    <p><a href="register.php" target="_blank">Or register for a new account.</a></p>
</form>
<?php include('includes/footer.inc.php'); ?>
