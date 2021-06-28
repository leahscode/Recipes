<?php 
session_start(); // Start the session.


// If no session value is present, redirect the user to login:
if (!isset($_SESSION['user_id'])) {
	require('includes/login_functions.inc.php');
    redirect_user('login.php?redirect='.basename($_SERVER['PHP_SELF'])."?id={$_GET['id']}");
}


$page_title = 'Edit a Recipe';
include ('includes/header.inc.php');
?>
<script type="text/javascript" src="add_recipe.js"></script>
<link rel="stylesheet" href="styles/forms.css">
<link rel="stylesheet" href="styles/add_recipe.css">
<?php include("includes/nav.inc.php"); ?>
<h1>Edit Your Recipe:</h1>

<?php
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

// Recipe ID is valid, connect to the db.
require ('../mysqli_connect.php'); 	

/*
Just like in add_recipes, here too the form's action attribute is left empty
 which means that it will be submitted to the page that displayed it. 
 The following "if" checks whether the user is requesting an empty form,
 or if it's being submitted. For submitted forms, the processing occurs 
 in the following block.
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // accessing the actual recipe form
    // checking if all hidden inputs are set - recipe id was checked at the top of the page
    if (empty($_POST['title']) || empty($_POST['category'])) {
        echo '<p class="error">This page has been accessed in error.</p>';
        include('includes/footer.inc.php');
        exit();
    }
        
    // First, all the form values are saved. This is done even if the fields are
    //  empty - resubmitted form can contain empty values
    $user_id = mysqli_real_escape_string($dbc, trim($_SESSION['user_id'])); //re-escaping for extra security
    $title = mysqli_real_escape_string($dbc, trim($_POST['title']));
    $category = mysqli_real_escape_string($dbc, trim($_POST['category']));
    $description = mysqli_real_escape_string($dbc, trim($_POST['description']));
    
    // Now, check if all required fields are present.
    if (empty($_POST['description'])) {
        $errors[] = 'You forgot to enter your description.';
    }
    // A nested array will hold all ingredients
    $ingredients = array();
    // both amount and ingredient_name will have the same length - empty values are included
    // For each line, check if only one is filled - an error. Otherwise, if
    // BOTH are filled, add it to the $ingredients array.
    for ($i=0; $i < sizeof($_POST['ingredient_name']); $i++) {
        if ((empty($_POST['amount'][$i])) xor (empty($_POST['ingredient_name'][$i]))) {
            $errors[] = 'Ingredient line number ' . ($i + 1) . ' is partly filled';
        } else if ((!empty($_POST['amount'][$i])) && (!empty($_POST['ingredient_name'][$i]))) {
            $ingredients[$i]['amount'] = mysqli_real_escape_string($dbc, trim($_POST['amount'][$i]));
            $ingredients[$i]['name'] = mysqli_real_escape_string($dbc, trim($_POST['ingredient_name'][$i]));
        }
    } // end for-loop 

    if (count($ingredients) == 0) {
        $errors[] = 'You must include at least one ingredient';
    }
    if (empty($errors)) { // If everything's OK.
        sort($ingredients); // to get rid of empty rows
        $q = "UPDATE recipe SET r_description='$description', r_last_edit = NOW()
            WHERE c_user_id='$user_id' AND recipe_id = '$recipe_id';\n";
        $q .= "DELETE FROM ingredient WHERE recipe_id = '$recipe_id';\n";
        $count = 0;

        foreach ($ingredients as $i => $value) {
            $amount = $value['amount'];
            $ingredient = $value['name'];
            $q .= "INSERT INTO ingredient (recipe_id, i_line, i_amount, i_name)
                 VALUES ('$recipe_id', '$i', '$amount', '$ingredient'); ";
            $count ++;
        }
        // A multi_query - safest way to delete old ingredients before setting the new ones
        $r = @mysqli_multi_query ($dbc, $q); 
        if ($r) {
            echo '<p>Success! your recipe has been updated! It contains '.$count.' ingedients</p><p><br></p>';
            echo '<p><a class="button" href="add_recipe.php">Submit Another Recipe</a>';
            echo '<a  class="button" href="recipes_list.php">Go Back to Recipe List</a>.</p>';                    
        } else {
            echo 'Error updating your recipe<br>';
        } 	


    } else {
        // If there are errors: show the errors and resend the form.
        echo '<h1>Error!</h1>
        <p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) { // Print each error.
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
        $resubmit = True;
        include('includes/edit_recipe_form.inc.php');
        
    } // End of if (empty($errors)) IF.

    // For submitted forms, script ends here:
    endScript($dbc);
}


// When requesting a new form: create the form with the recipe values prefilled
// get the user-id
$user_id = mysqli_real_escape_string($dbc, trim($_SESSION['user_id']));

// validate the recipe owner - and get the recipe (the where clause does the validation)
$q = 'SELECT r_title, r_category, r_description FROM recipe WHERE (c_user_id="'.$user_id.'" AND recipe_id="'.$recipe_id.'")';
$r = @mysqli_query($dbc, $q);
$num = @mysqli_num_rows($r);
if ($num == 1) { //valid match
    $row = mysqli_fetch_array($r, MYSQLI_ASSOC); 
    $title = $row['r_title'];
    $category = $row['r_category'];
    $description = $row['r_description'];
    mysqli_free_result($r);
    echo '<h2>'. $title. ' (Category: '. $category .')</h2>';

    // retrieve the ingredients - array will be used to display it in the form
    $ingredients = array();
    $i = 0;

    $q = "SELECT i_amount, i_name FROM ingredient WHERE recipe_id='$recipe_id' ORDER BY i_line";
    $r = mysqli_query ($dbc, $q); // Running the query
    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
        $ingredients[$i]['amount'] = $row['i_amount'];
        $ingredients[$i]['name'] = $row['i_name'];
        $i++;
    }
    mysqli_free_result($r);

    // Send the form
    $resubmit = False;
    include('includes/edit_recipe_form.inc.php');

} else {
    // Recipe ID doesn't match $_SESSION['user_id']
    echo '<h1>Error!</h1>
    <p class="error">You cannot edit someone else\'s recipes.<br /></p>';
    echo '<p><a href="recipes_list.php">Back to Recipe List</a>.</p>';
}
include ('includes/footer.inc.php');

?>