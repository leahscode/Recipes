<?php 
session_start(); // Start the session.


$page_title = 'Account';
include ('includes/header.inc.php');
echo '<link rel="stylesheet" href="styles/forms.css">';
echo '<link rel="stylesheet" href="styles/account.css">';
?>

<script type="text/javascript">
/* This page includes a logout button, which uses AJAX to call a PHP script */
    $(document).ready(function(){
        $('#logout').click(function(){
            $.ajax({
                type: 'GET',
                url: 'logout.php',
                success: function(msg) {
                    if (msg == 'success') {
                        alert('Logout successful');
                        window.location.href = "index.php";
                    } else {
                        alert('Error logging out');
                    };
                }
            });
        }); // end click
    });
</script>

<?php
include ('includes/nav.inc.php');

if (isset($_SESSION['user_id'])) {
    require ('../mysqli_connect.php'); // Connect to the db.
    $id = $_SESSION['user_id'];
    $fname = $_SESSION['first_name'];
    
    $q = "SELECT count(*) FROM recipe WHERE c_user_id='$id'";
    $r = mysqli_query($dbc, $q); // Run the query.

    if (mysqli_num_rows($r) == 1) {

        // Fetch the record:
        $row = mysqli_fetch_array($r, MYSQLI_NUM);

        // Print a message:
        echo "<h1>Welcome back, $fname!</h1>";
        echo "<p>You have submitted $row[0] recipes.</p><p><br /></p>";
        if ($row[0] > 0) {
            echo '<p><a class="button" href="recipes_list.php?user=user">View your recipes</a></p><p><br /></p>';

        }
    }

    echo '<button id="logout">Log Out</button>';
    mysqli_close($dbc); // Close the database connection.
    include('includes/footer.inc.php');
    exit();
}

?>

<div class="section">
    <?php include ('includes/register_form.inc.php'); ?>
</div>

<div class="section">
    <h1>Login</h1>
    <form action="<?php echo 'login.php?redirect='.basename($_SERVER['PHP_SELF']); ?>" method="post">
        <p>Please enter your login:</p>
        <p><label for="email">Email: </label><input type="text" name="email" size="20" maxlength="60" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" > </p>
        <p><label for="password">Password: </label><input type="password" name="pass" size="10" maxlength="20" value="<?php if (isset($_POST['pass'])) echo $_POST['pass']; ?>" ></p>
        <button type="submit" name="submit" value="login">Login</button>
    </form>
</div>
<?php include ('includes/footer.inc.php'); ?>