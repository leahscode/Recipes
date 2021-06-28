<form action="" method="post">
	<input type="radio" name="sure" value="Yes"> Yes
	<input type="radio" name="sure" value="No" checked="checked"> No
	<button type="submit" name="submit">Submit</button>
	<input type="hidden" name="id" value="<?php echo $recipe_id; ?>">
</form>