<!DOCTYPE html>
<?php
/* 
This file must be included in every page of this website. It contains the tag
for the head, links to scripts and style sheets, and an embedded script for the
mobile nav menu. The closing head tag is in the nav.inc.php file, allowing additional
files to be linked for individual pages. The $page_title must be initialized
before including this file.
*/
?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo $page_title; ?> - UnLimited Recipes for Limited Diets</title>
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" href="styles/normalize.css">
        <link rel="stylesheet" href="SlickNav-master/dist/slicknav.min.css" />
        <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="SlickNav-master/dist/jquery.slicknav.min.js"></script>
        <link rel="stylesheet" href="styles/main.css">

        <script type="text/javascript">
            // This calls the slicknav function, which displays a mobile-friendly
            // nav-menu for smaller screens.
            $(document).ready(function(){
                $('#menu').slicknav();

            });
        </script>

    
    	