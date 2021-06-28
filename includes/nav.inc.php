<?php
/* 
This file must be included in every page of this website. It contains the
closing head tag, and the opening for the body. The page header and the nav-bar
is included here, as well as the opening for the main content. The <div id="body">
is for styling purposes. The php $categories will be used for database operations.
*/
	$categories = ["egg_free", "gluten_free", "low_carb", "other"];

?>

</head>
<body>
	<div id="body">
	<header>
		<h2><a href="index.php"><i>Un</i>Limited</a></h2>
		<hr>
		<h3>Providing Unlimited Recipes and Support For Those on a Limited Diet.</h3>
	</header>
	<nav>
		<ul id="menu">
			<li>
				<a href="account.php">Account</a>
			</li>
			<li>
				<a href="recipes_list.php">Find a Recipe</a>
				<ul>
					<li><a href="recipes_list.php">All Recipes</a></li>
					<li><a href="recipes_list.php?category=<?php echo $categories[0] ?>">Egg Free</a></li>
					<li><a href="recipes_list.php?category=<?php echo $categories[1] ?>">Gluten Free</a></li>
					<li><a href="recipes_list.php?category=<?php echo $categories[2] ?>">Low Carb</a></li>
					<li><a href="recipes_list.php?category=<?php echo $categories[3] ?>">Other Specialty</a></li>
				</ul>
			</li>   				
			<li><a href="add_recipe.php">Post a Recipe</a></li>
			<li><a href="conversions.php">Conversions</a>
				<ul>
					<li><a href="conversions.php">Egg Substitutes</a></li>
					<li><a href="conversions.php">Gluten Free Flour</a></li>
				</ul>
			</li>
			<li><a href="index.php">Contact Us</a></li>
		</ul>
	</nav>
		
	<main>
    	