<?php
    /* Start session to save variables in the future, but destroy current variables set */
    session_start();
    session_destroy();
?>

<!DOCTYPE html>
<html>
    <!-- To place important data for site -->
    <head>
        <title>Login to StatKeeper</title>
        <link rel="stylesheet" href="css/login_style.css">
    </head>
    
    <!-- To place main content on page -->
    <body>
        <!-- Div to hold background image and successive content -->
        <div class="background">
            
            <!-- To hold the main interaction and form login -->
            <div class="main_interaction_div">
                <span class="company_symbol">StatKeeper</span>
                
                <!-- Form to handle the login data user provided -->
                <form id="login_form" action="verify.php" method="post">
                    <input id="username_input" type="text" placeholder="Username" name="username" required><br>
                    <input id="password_input" type="password" placeholder="Password" name="password" required><br>
                    <input id="login_button" type="submit" value="Login">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                    <script src="scripts/login_js.js" type="application/javascript"></script>
                </form>
                
                <!-- Span for for providing options to sign up, forgotten password, and about page -->
                <span class="link_span">
                    <a href="Sign-up.php">Create an Account</a>
                    <label class="link_divider">|</label>
                    <a href="recover-information.php">Forgot Password?</a>
                </span><br>
                <span class="link_span"><a href="about.html">About Us</a></span>
                <div class="error_message_div">
                    <?php
                        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                        if(isset($_SESSION['errors']) && strpos($url, "authentication_failure")) {
                            echo "<label>".$_SESSION['errors']."</label>";
                            unset($_SESSION['errors']);
                        }   
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>