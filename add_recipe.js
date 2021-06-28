// I created this function, with a double dollar sign since I include jQuery for the nav-bar
function $$(id) {
	return document.getElementById(id);
};

// this function add more input fields to the form, for more ingredients
// the name attribute is an array, enabling it to be dynamic
// the fields are kept sticky with whatever the user had typed in - not with what came from the php.
function moreLines() {
    // Get all the table rows from the "ingredient" fieldset
    var tRows = document.getElementsByTagName("tr");

    // For each input field, set its value attribute to be whatever is
    // currently typed in there. This ensures that the input is not lost
    // when updating the table.
    var amount = document.getElementsByClassName("amount");
    for (var i = 0; i < amount.length; i++) {
        amount[i].setAttribute("value", amount[i].value);
    }
    var ingredient_name = document.getElementsByClassName("ingredient_name");
    for (var i = 0; i < ingredient_name.length; i++) {
        ingredient_name[i].setAttribute("value", ingredient_name[i].value);
    }

    // Define a new row and add it to the table.
    var newRow = '<tr><td class="line_number"> '+ tRows.length + ' </td>';
    newRow += '<td><input type="text" name="amount[]" class="amount"></input></td>';
    newRow += '<td><input type="text" name="ingredient_name[]" class="ingredient_name"></td></tr>';
    $$("ingredients").innerHTML += newRow;
   }



window.onload = function(){
    // Call the function when the user clicks the designated button.
    $$("more").onclick = moreLines;
};