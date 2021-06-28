<?php
session_start(); // Start the session.


// User must be logged in to submit recipes. 
// If no session value is present, redirect the user to login:
if (!isset($_SESSION['user_id'])) {

	/*
    Include the file with the required function, and call its redirect_user()
    function. It will direct the user to the login page, with the current
    page name part of the URL. This will enable the user to automatically
    be redirected to this page after login.
    */
	require('includes/login_functions.inc.php');
    redirect_user('login.php?redirect='.basename($_SERVER['PHP_SELF']));

}

// User is logged in, display the current page:
include("methods.php"); 
$page_title = "Add a Recipe";
include("includes/header.inc.php"); 

echo '<script type="text/javascript" src="add_recipe.js"></script>';
echo '<link rel="stylesheet" href="styles/forms.css">';
echo '<link rel="stylesheet" href="styles/add_recipe.css">';
include("includes/nav.inc.php");
echo '<h1>Add a Recipe</h1>';


/*
The form's action attribute is left empty, which means that it will be submitted
to the page that displayed it. The following "if" checks whether the user is
requesting an empty form, or if it's being submitted. For submitted forms,
the processing occurs in the following block.
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
    require ('../mysqli_connect.php'); // Connect to the db.		
    $errors = array(); // Initialize an error array.
    
    // Get the user id from the session; validate with "escape" for extra security
    $user_id = mysqli_real_escape_string($dbc, trim($_SESSION['user_id']));
    // ensure it's a valid id - count returns 1 if valid
    $q = "SELECT COUNT(c_user_id) FROM contributor WHERE c_user_id = '$user_id'";
    $r = @mysqli_query($dbc, $q); 
    $row = mysqli_fetch_array($r, MYSQLI_NUM);
     // If the query returned 0, the ID is invalid.
    if ($row[0] != 1 ){
        echo '<h1>Error!</h1>';
        echo '<p class="error">This page was accessed in error.<p>';
        // Disconnect from the database and exit the script:
        endScript($dbc);
    }

    // The user ID is valid, now validate all input fields. If any required
    // fields is empty, a message will be added to the errors array. Otherwise
    // it will be assigned to a variable, after being checked for unsafe characters.
    if (empty($_POST['title'])) {
        $errors[] = 'You forgot to enter your title.';
    } else {
        $title = mysqli_real_escape_string($dbc, trim($_POST['title']));
    }
    if (empty($_POST['category'])) {
        $errors[] = 'You forgot to enter your category.';
    } else {
        $category = mysqli_real_escape_string($dbc, trim($_POST['category']));
    }
    if (empty($_POST['description'])) {
        $errors[] = 'You forgot to enter your description.';
    } else {
        $description = mysqli_real_escape_string($dbc, trim($_POST['description']));
    }
    // The ingredients are 2 fields each, and there can be multiple ingredients.
    // Therefore, a nested array is necessary.
    $ingredients = array();
    // "amount" and "ingredient_name" are returned as arrays. Both will have the
    // same length, as empty values are included. Note that the length is dynamic.
    for ($i=0; $i < sizeof($_POST['ingredient_name']); $i++) {
        // For each line, check if only one is filled - an error. Otherwise, if
        // BOTH are filled, add it to the $ingredients array.
        if ((empty($_POST['amount'][$i])) xor (empty($_POST['ingredient_name'][$i]))) {
            $errors[] = 'Ingredient line number ' . ($i + 1) . ' is partly filled';
        } else if ((!empty($_POST['amount'][$i])) && (!empty($_POST['ingredient_name'][$i]))) {
            $ingredients[$i]['amount'] = mysqli_real_escape_string($dbc, trim($_POST['amount'][$i]));
            $ingredients[$i]['ingredient_name'] = mysqli_real_escape_string($dbc, trim($_POST['ingredient_name'][$i]));
        }
    } // end for-loop 

    // When both fields in a line where empty, it was not added. Check to make
    // sure that at least one line was filled.
    if (count($ingredients) == 0) {
        $errors[] = 'You must include at least one ingredient';
    }

    // Check if there are errors. If not, enter the next block to submit the form.
    if (empty($errors)) {

        // Submit the recipe.
        // But first check for duplicates.
        $q = "SELECT recipe_id FROM recipe 
                WHERE r_title = '$title'
                AND r_category = '$category'
                AND c_user_id = '$user_id'";
        $r = @mysqli_query ($dbc, $q); // Run the query.
        
        if (mysqli_num_rows($r) != 0 ){
            // If a duplicate was found, display an error, and resend the form.
            // With $resubmit set as True, the form will be prefilled with the
            // previously submitted values.
            // The script ends after the form is sent.
            echo '<h1>Error!</h1>
            <p class="error">You already have a recipe with this name and category. Please change some of the information.<br /></p>';
            $resubmit = True;
            include('includes/recipe_form.inc.php');
            endScript($dbc); 
        }

        // No errors, now make the query:
        $q = "INSERT INTO recipe (r_title, r_description, r_category, c_user_id, r_date_added, r_last_edit) 
            VALUES ('$title', '$description', '$category', '$user_id', NOW(), NOW())";		
        $r = @mysqli_query ($dbc, $q); // Run the query.

        if ($r) {// everything's ok
            // get recipe id for use in ingredients table (recipe_id was created just now)
            $q = "SELECT recipe_id FROM recipe 
                    WHERE r_title = '$title'
                    AND r_category = '$category'
                    AND r_description = '$description'
                    AND c_user_id = '$user_id'";
            $r = @mysqli_query ($dbc, $q); // Run the query.
            $recipe_id = mysqli_fetch_array($r, MYSQLI_NUM);
            $recipe_id = $recipe_id[0];
            mysqli_free_result($r);

            sort($ingredients); // to get rid of empty rows (extra security)
            $count = 0;

            $q= ""; // initializing the ingredient query
            foreach ($ingredients as $i => $value) {
                $amount = $value['amount'];
                $ingredient = $value['ingredient_name'];
                $q .= "INSERT INTO ingredient (recipe_id, i_line, i_amount, i_name) 
                    VALUES ('$recipe_id', '$i', '$amount', '$ingredient'); ";
                $count ++;
            }	
            $r = @mysqli_multi_query ($dbc, $q); // Running a multi_query.
            if ($r) {
                echo '<p>Success! your recipe has been posted! It contains '.$count.' ingedients<br></p>';
                // A quick link to enable the user to submit another recipe.
                // It will be styled to look like a button
                echo '<div><a class="button" href="'.basename($_SERVER['PHP_SELF']).'">Submit Another Recipe</a></div>';
            } else {
                echo 'Error submitting all ingredients of your recipe<br>';
            }                               

        } // end inserted recipe

    } else {
        /* 
        If there are errors in the submitted form:
        Display all error messages, then redisplay the form. The form will be
        "sticky" - all previous input present, and with the correct number
        of ingredient rows.
         */
        echo '<h1>Error!</h1>
        <p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) { // Print each error.
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
        $resubmit = True;
        include('includes/recipe_form.inc.php');
        
    } // End of if (empty($errors)) IF.

    endScript($dbc);            
    } // end submitted form 

    /* 
    Where the form is accessed for the first time (not via POST):
    Include an empty form and the footer. Note: in the previous cases, the
    footer has been added via the endScript() method that also disconnects
    from the database and exits the script.)
     */
    $resubmit = false;
    include("includes/recipe_form.inc.php");
    include("includes/footer.inc.php");

?>
