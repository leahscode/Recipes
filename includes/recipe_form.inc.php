<?php
/*This form is included in the add_recipe.php page. It checks for previously
submitted input, and adds it. If the user needs to resubmit because of missing
fields, the previous data is not lost.
*/
?>
<form action="" method="post">
    <label for="title">Recipe Title</label>
    <input type="text" name="title" maxlength="40" value="<?php if (isset($_POST['title'])) echo $_POST['title']; ?>"><br>
    <label for="category">Recipe Category</label>
    <select name="category">
    <option value="">Select a Specialty Type</option>
        <?php
            // create an option for each element in the $categories array in the nav page
            foreach ($categories as $value) {
                echo '<option value="'.$value.'"';
                if (isset($_POST['category']) && ($_POST['category'] == $value)) {
                    echo ' selected="selected"';
                }
                echo '>'.$value.'</option>';
            }
        ?>
    </select>
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
                    // Since the user can add lines for ingredients, the number
                    // of lines must match the previous submission.
                    for ($i=0; $i < sizeof($_POST['ingredient_name']); $i++){
                        echo '<tr>
                        <td class="line_number">'.($i + 1).'</td>
                        <td><input type="text" maxlength="40" name="amount[]" class="amount" value="'. $_POST['amount'][$i]. '"></td>
                        <td><input type="text" maxlength="40" name="ingredient_name[]" class="ingredient_name" value="'. $_POST['ingredient_name'][$i]. '"></td>   
                        </tr>';
                    }
                } else {
                    // not sticky since $_POST is not set
                    echo '<tr>
                        <td class="line_number">1</td>
                        <td><input type="text" maxlength="40" name="amount[]" class="amount" value=""></td>
                        <td><input type="text" maxlength="40" name="ingredient_name[]" class="ingredient_name" value=""></td>   
                    </tr>
                    <tr>
                        <td class="line_number">2</td>
                        <td><input type="text" maxlength="40" name="amount[]" class="amount" value=""></td>
                        <td><input type="text" maxlength="40" name="ingredient_name[]" class="ingredient_name" value=""></td>   
                    </tr>';
                }
            ?>
        </table>
    </fieldset>
    <p><span id="more">Click to add more ingredients</span></p>
    <p>Instructions and Comments:</p>
    <textarea name="description" id="" cols="50" rows="15" maxlength="700" placeholder="Enter Instructions and Comments Here."><?php if (isset($_POST['description'])) echo $_POST['description']; ?></textarea><br>
    <button type="submit">Post</button>

</form>