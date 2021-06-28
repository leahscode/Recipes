<?php

    function endScript($dbc)
    {
        mysqli_close($dbc); // Close the database connection.
        include('includes/footer.inc.php');
        exit();
    }

?>