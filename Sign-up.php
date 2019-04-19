<?php
    session_start();
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
                
                <!-- Sign-up form details -->
                <form id="sign_up_form" action="verify.php" method="post">
                    <!-- Div containing signup form details-->
                    <div id="sign_up_container">
                        <h1 style="color: darkblue; text-align: left;">Sign-Up</h1>
                        
                        <!-- 
                            labels and input items for:
                                - userID
                                - password
                                - re-typed password
                                - email
                                - phone number
                        -->
                        <label for="userID" class="sign_up_label"><b>Username</b></label>
                        <input type="text" placeholder="Enter Username" name="userID" required>
                        <label for="password" class="sign_up_label"><b>Password</b></label>
                        <input type="password" placeholder="Enter Password" name="password" required>
                        <label for="re_password" class="sign_up_label"><b>Re-type Password</b></label>
                        <input type="password" placeholder="Repeat Password" name="re_password" required>
                        <label for="email" class="sign_up_label"><b>Email</b></label>
                        <input type="text" placeholder="Enter Email" name="email" required>
                        <label for="phone_num" class="sign_up_label"><b>Phone Number</b></label>
                        <input type="text" placeholder="Enter Phone Number" name="phone_num">
                        <label for="carrier" class="sign_up_label"><b>Carrier</b></label>
                        <span>
                            <input type="radio" name="carrier" value="ATT" checked><label>ATT</label>
                            <input type="radio" name="carrier" value="Verizon"><label>Verizon</label>
                            <input type="radio" name="carrier" value="Sprint"><label>Sprint</label>
                            <input type="radio" name="carrier" value="TMobile"><label>T-Mobile</label>
                        </span>
                        <!-- Span for two action buttons of form -->
                        <span class="link_span">
                            <!-- Button to cancel the form and return to login page-->
                            <button id="cancel_button" type="button">Cancel</button>
                            <!-- Button to sbumit the form and place control to php page -->
                            <button id="create_button">Create!</button>
                            
                            <!-- Scripts to handle both button presses differently -->
                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                            <script src="scripts/login_js.js" type="application/javascript"></script>
                            <script type="application/javascript">
                                /* Function to be run when cancel button is clicked */
                                document.getElementById('cancel_button').onclick = function() {
                                    location.href='login.php'; 
                                    document.getElementById('sign_up_form').reset();
                                };
                            </script>
                        </span>
                   </div>
                </form>
                <div class="error_message_div">
                    <?php
                        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                        if(isset($_SESSION['errors']) && strpos($url, "signup_failure")) {
                            echo "<ul style='text-align: left'>";
                            foreach($_SESSION['errors'] as $error) {
                                echo "<li>".$error."</li>";
                            }
                            echo "</ul>";
                            unset($_SESSION['errors']);
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>