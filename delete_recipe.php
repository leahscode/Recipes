<?php 
session_start(); // Start the session.


// If no session value is present, redirect the user to login:
if (!isset($_SESSION['user_id'])) {

	require('includes/login_functions.inc.php');
    redirect_user('login.php?redirect='.basename($_SERVER['PHP_SELF'])."?id={$_GET['id']}");

}

$page_title = 'Delete a Recipe';
include ('includes/header.inc.php');
echo '<link rel="stylesheet" href="styles/forms.css">';
include ('includes/nav.inc.php');
include("methods.php");


// Check for a valid recipe ID, through GET or POST:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From recipe_list.php
    $recipe_id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
    $recipe_id = $_POST['recipe_id'];
} else { // No valid ID, kill the script.
    echo '<p class="error">This page has been accessed in error.</p>';
    include('includes/footer.inc.php');
    exit();
}

require ('../mysqli_connect.php'); // Connect to the db.	

// Check if user requests a form, of submits a form to process.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['sure'] == 'Yes') { // Delete the recipe.
        $user_id = mysqli_real_escape_string($dbc, trim($_SESSION['user_id'])); //re-escaping for extra security

        // Make the query:
        $q = "DELETE FROM ingredient WHERE recipe_id=$recipe_id;\n"; // no limit, because varied amounts. Yeah, less safe
        $q .= " DELETE FROM recipe WHERE recipe_id=$recipe_id AND c_user_id=$user_id LIMIT 1;";
        $r = @mysqli_multi_query($dbc, $q);
        if ($r) { // If it ran OK. (can't use mysqli_affected_rows, as the number differs)

            // Print a message:
            echo '<p>The recipe has been deleted.</p>';
            echo '<p><a href="recipes_list.php">Back to Recipe List</a>.</p>';

        } else { // If the query did not run OK.
            echo '<p class="error">The recipe could not be deleted due to a system error.</p>'; // Public message.
            echo '<p><a href="recipes_list.php">Back to Recipe List</a>.</p>';
            // echo '<p>' . mysqli_error($dbc) . '<br>Query: ' . $q . '</p>'; // Debugging message.
        }
        endScript($dbc);

    } else { // User selected "No" on the form.
        echo '<p>The recipe has NOT been deleted.</p>';
        echo '<p><a href="recipes_list.php">Back to Recipe List</a>.</p>';
        endScript($dbc);
    }
}

// Not POST, the user clicked "delete" on the recipe list. Needs to check
// if the recipe owner matched the SESSION ID, and send a confirmation form.
$user_id = mysqli_real_escape_string($dbc, trim($_SESSION['user_id'])); //re-escaping for extra security
// validate the recipe owner
$q = 'SELECT r_title, r_category FROM recipe WHERE (c_user_id="'.$user_id.'" AND recipe_id="'.$recipe_id.'")';
$r = @mysqli_query($dbc, $q);
$num = @mysqli_num_rows($r);
if ($num == 1) { //valid match
    $row = mysqli_fetch_array($r, MYSQLI_ASSOC); 
    echo '<h2>'. $row['r_title']. ' (Category: '. $row['r_category'] .')</h2>';
    echo '<p>Are you sure you want to delete this recipe?</p>';
    include('includes/delete_form.inc.php');
} else {
    echo '<h1>Error!</h1>
    <p class="error">You cannot delete someone else\'s recipes.<br /></p>';
    echo '<p><a href="recipes_list.php">Back to Recipe List</a>.</p>';
}

mysqli_close($dbc); // Close the database connection.
include ('includes/footer.inc.php');
?>