<?php
/*This form is included in the edit_recipe.php page. Variables are set either
via previous post (if errors) or from database retrieval (first attempt). 
The first two hidden input are used to display the title after resubmission
(if there were errors). Note that the title and category cannot be changed
when editing a recipe.
*/
?>
<form action="" method="post">
    <input type="hidden" name="title" value="<?php echo $title; ?>">
    <input type="hidden" name="category" value="<?php echo $category; ?>">
    
    <fieldset>
        <legend>Ingredients</legend>
        <table id="ingredients">
            <thead>
                <th></th>
                <th>Amount</th>
                <th>Name</th>
            </thead>
            <?php
            if ($resubmit) {
                // echo the number of rows corresponding to what was submitted previously.
                // This includes partially filled row.
                for ($i=0; $i < sizeof($_POST['ingredient_name']); $i++){
                    echo '<tr>
                    <td class="line_number">'.($i + 1).'</td>
                    <td><input type="text" maxlength="40" name="amount[]" class="amount" value="'. $_POST['amount'][$i]. '"></td>
                    <td><input type="text" maxlength="40" name="ingredient_name[]" class="ingredient_name" value="'. $_POST['ingredient_name'][$i]. '"></td>   
                    </tr>';
                }
            } else {
                // echo a row for each line retrieved from the database, with its data.
                for ($i=0; $i < sizeof($ingredients); $i++){
                    echo '<tr>
                    <td class="line_number">'.($i + 1).'</td>
                    <td><input type="text" maxlength="40" name="amount[]" class="amount" value="'. $ingredients[$i]['amount']. '"></td>
                    <td><input type="text" maxlength="40" name="ingredient_name[]" class="ingredient_name" value="'. $ingredients[$i]['name'] . '"></td>   
                    </tr>';
                }
            }
                 
                    
               
            ?>
        </table>
    </fieldset>
    <p><span id="more">Click to add more ingredients</span></p>
    <p>Instructions and Comments:</p>
    <textarea name="description" id="" cols="50" rows="15" maxlength="700" placeholder="Enter Instructions and Comments Here."><?php echo $description; ?></textarea><br>
    <input type="hidden" name="id" value="<?php echo $recipe_id; ?>">
    <button type="submit" name="submit">Post</button>

</form>