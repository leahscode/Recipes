<?php
session_start(); // Start the session.
// Build the page by including all files and links
$page_title = "View Recipes";
include("includes/header.inc.php"); 
?>
<link rel="stylesheet" href="styles/recipes_list.css">
<script type="text/javascript" src="recipes_list.js" defer></script>
<?php
include("includes/nav.inc.php");

// Get the heading and connect to the database
echo '<div id="main-content">';
echo '<h1>Recipes - for your pleasure</h1>';
require('../mysqli_connect.php');

/* 
This script retrieves recipes from the database and display them. The query for
which recipes to retrieve is defined by various factors. These are provided as
"GET" submissions in the URL.
 */
$get = ""; // initialize
/* 
For the user's own recipes, an option to edit or delete will be added.
The following checks for login and assigns the user id to a variable, to
be compared to the user id associated with each recipe. Additionally,
there is an option to view only the user's recipes (from the account page).
 */
if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
    if (isset($_GET['user'])) {
        $by_user = True;
        $get .= "&user=user";
    } else {
        $by_user = False;
    }
} else { 
    $user = null;
    $by_user = False;
}
// Check if a category has been submitted. The default is "all recipes" (no category).
// in_array() ensures a valid category.
// $has_category is defined as True or False, and will determine if the query
// should have an extra "where" condition.
if (isset($_GET['category']) && in_array($_GET['category'], $categories)) {
    $category = $_GET['category'];
    $has_category = True;
    $get .= "&category=$category";
} else {
    $has_category = False;
}

// Determine the number of records to show per page, and where to start.
// The latter will be determined from the GET values. 
$display = 10;  // How many records to display

// Determine how many pages there are, either from the GET or by querying the database
if (isset($_GET['p']) && is_numeric($_GET['p'])) {
    $pages = $_GET['p'];
} else {
    // Count the number of records:
    $q = "SELECT COUNT(recipe_id) FROM recipe";
    if ($has_category && $by_user) {
        $q .= " WHERE  r_category = '$category' AND c_user_id = '$user'";
    }
    elseif ($has_category) {
        $q .= " WHERE  r_category = '$category'";
    }
    elseif ($by_user) {
        $q .= " WHERE  c_user_id = '$user'";
    }
    $r = @mysqli_query($dbc, $q);
    $row = @mysqli_fetch_array($r, MYSQLI_NUM);
    $records = $row[0];
    // Calculate the number of pages...
    if ($records > $display) { // More than 1 page.
        $pages = ceil ($records/$display);
    } else {
        $pages = 1;
    }
}

// Determine where in the database to start returning results.
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
    $start = $_GET['s'];
} else {
    $start = 0;
}

// Determine the sorting order.
// Default (not set) is by recipe title.
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'title';

switch ($sort) {
    case 'name':
        $order_by = 'Name ASC';
        break;
    case 'ctg':
        $order_by = 'r_category ASC';
        break;
    case 'date':
        $order_by = 'r_date_added desc';
        break;
    case 'title':
        $order_by = 'r_title ASC';
        break;
    default:
        $order_by = 'r_title ASC';
        $sort = 'title';
        break;
}

// Define the query:
$q = "SELECT contributor.c_user_id, concat(c_lname, ', ', c_fname) AS Name, r_title, r_category,
    DATE_FORMAT(r_date_added, '%M %d, %Y') AS Date, r_description, recipe_id 
    FROM contributor, recipe
    WHERE contributor.c_user_id = recipe.c_user_id";
if ($has_category) {
    $q .= " AND  r_category = '$category'";
}
if ($by_user) {
    $q .= " AND  contributor.c_user_id = '$user'";
}
$q .= " ORDER BY $order_by LIMIT $start, $display";

$r = @mysqli_query($dbc, $q); // Run the query.

// Table header: 
/* <th align="left"><strong>Edit</strong></th>
<th align="left"><strong>Delete</strong></th>

<td align="left"><a href="edit_recipe.php?id=' . $row['recipe_id'] . '">Edit</a></td>
<td align="left"><a href="delete_recipe.php?id=' . $row['recipe_id'] . '">Delete</a></td> */
// Create a table header or the results
echo '<table>
<thead>
<tr>
	<th align="left"><strong><a href="recipes_list.php?sort=name'.$get.'">Contributor Name</a></strong></th>
	<th align="left"><strong><a href="recipes_list.php?sort=title'.$get.'">Recipe Title</a></strong></th>
	<th align="left"><strong><a href="recipes_list.php?sort=ctg'.$get.'">Recipe Category</a></strong></th>
	<th align="left"><strong><a href="recipes_list.php?sort=date'.$get.'">Date Submitted</a></strong></th>
	<th align="left"></th>
</tr>
</thead>
<tbody>
';

// Fetch all the records. Add it to the recipe_fetched array. Display the
// title etc. as a table row.
$bg = '#C88FB8';
$recipes_fetched = array();
while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
	$bg = ($bg=='#C88FB8' ? '#FFF5EF' : '#C88FB8');
    $recipes_fetched[] = $row;
    // 
    $id = $row['recipe_id'];
        echo '<tr bgcolor="' . $bg . '" class="title '.$id.'">
		<td align="left">' . $row['Name'] . '</td>
		<td align="left">' . $row['r_title'] . '</td>
		<td align="left">' . $row['r_category'] . '</td>
		<td align="left">' . $row['Date'] . '</td>
		<td align="left"><span class="view">View recipe</span></td>
	</tr>
    ';
}
// Add the closing tags.
echo '</tbody></table></div>';

// Retrieve the ingredients and create a modal div, where it will be displayed
// when the user clicks on the corresponding table row.
foreach ($recipes_fetched as $row) {
    $id = $row['recipe_id'];
    $desc = $row['r_description'];
    // get the ingredients for each recipe.
    $qi = "SELECT i_amount, i_name FROM ingredient WHERE recipe_id = '$id' ORDER BY i_line";
    $ri = @mysqli_query($dbc, $qi); // Run the query.

    // Create a modal to display the specifics of the recipe.
    echo '<div class="myModal modal '.$id.'">
        <div class="modal-content '.$id.'">
            <span class="close">&times;</span>
            <h2>'.$row['r_title'].'</h2>
            <h4>Ingredients:</h4>
            <ol>';
            $li = 0; // overriding the default numbering - for styling purposes
        while ($rowi = mysqli_fetch_array($ri, MYSQLI_ASSOC)) {
            echo '<li><span>'.++$li.': </span>'.$rowi['i_amount'].' '.$rowi['i_name'].'</li>';
        }
        echo '</ol>
            <h4>Directions:</h4>
            <p style="white-space:pre-wrap">'.$desc.'</p>';
        if ($user && $user == $row['c_user_id']) {
            echo '<p class="modal-links"><a href="edit_recipe.php?id=' . $row['recipe_id'] . '">Edit</a> 
                <a href="delete_recipe.php?id=' . $row['recipe_id'] . '">Delete</a></p>';
        } 
        echo '</div>
        </div>';
}

mysqli_free_result($r);
mysqli_close($dbc);

// Make the links to other pages, if the number of records are more than
// what's displayed (prev, next, numbers).
// $get is either empty or a get request for the category.
if ($pages > 1) {

	echo '<br><p>';
    // Define the current page, based on previously-defined variables.
	$current_page = ($start/$display) + 1;

	// If it's not the first page, make a Previous button:
	if ($current_page != 1) {
		echo '<a href="recipes_list.php?s=' . ($start - $display) . '&p=' 
            . $pages . '&sort=' . $sort . $get . '">Previous</a> ';
	}

	// Make all the numbered pages, as links. The current page is NOT a link.
	for ($i = 1; $i <= $pages; $i++) {
		if ($i != $current_page) {
			echo '<a href="recipes_list.php?s=' . (($display * ($i - 1))) . '&p='
                 . $pages . '&sort=' . $sort . $get . '">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	}

	// If it's not the last page, make a Next button:
	if ($current_page != $pages) {
		echo '<a href="recipes_list.php?s=' . ($start + $display) . '&p='
             . $pages . '&sort=' . $sort . $get . '">Next</a>';
	}

	echo '</p>'; // Close the paragraph.

} // End of links section.

include('includes/footer.inc.php');

?>
