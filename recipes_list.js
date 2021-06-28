// this script enables to toggle expanded view of the recipes - just the list is presented,
// the user clicks to expand or collapse individual recipes


/*  */
// Get the modal
var modal = document.getElementsByClassName("myModal");

// Get the button that opens the modal
var title = document.getElementsByClassName("title");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close");

// When the user clicks on the button, open the modal
for (const i in title) {
    title[i].onclick = function() {
        modal[i].style.display = "block";
      }
      }

// When the user clicks on <span> (x), close the modal
for (const i in span) {
    span[i].onclick = function() {
        modal[i].style.display = "none";
      }
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    for (const i in modal) {
        if (event.target == modal[i]) {
            modal[i].style.display = "none";
          }
    }
}
/*  */
