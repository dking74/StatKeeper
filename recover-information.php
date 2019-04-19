<!DOCTYPE html>
<html>
    <head>
        <title>Recover Information</title>
        <link rel="stylesheet" href="css/login_style.css">
    </head>
    
    <!-- To place main content on page -->
    <body>
        <!-- Div to hold background image and successive content -->
        <div class="background">
            
            <!-- To hold the main interaction and form login -->
            <div class="main_interaction_div">
                <!-- Place logo on screen -->
                <span class="company_symbol">StatKeeper</span>
                
                <!-- Form submits request to self to lookup info -->
                <form method="post" action="#">
                    <!-- Create span for user to input username -->
                    <span class="lookup_span">
                        <label for="username">Username:</label>
                        <input name="username" type="text" placeholder="Username" required>
                    </span>
                    <!-- Create span for cancel button and submit button -->
                    <span class="lookup_span">
                        <button id="cancel_button">Cancel</button>
                        <button id="submit_button">Submit</button>
                    </span>
                </form>
                
                <!-- Div to place recovery information -->
                <div>
                    <?php
                        include "scripts/common_funcs.php";

                        /* See if post request was submitted, if so look up user information in database */
                        if(isset($_POST['username']) && $_POST['username']) {
                            $lookup_results = mysqli_fetch_assoc(recover_user_info($_POST['username']));
                            
                            // convert_phone_to_email
                            /* Add span if we have email for specific username */
                            if(isset($lookup_results['Email']) && $lookup_results['Email'])
                                echo "<span style='display: block; margin-left: 30px; text-align: left; margin-bottom: 5px; font-size: 20px;'>".
                                        "<input name='message_choice' value ='email' type='radio' style='margin-right: 20px' checked>".
                                        "<label>Email <b>".$lookup_results['Email']."</b> recovery code</label>".
                                     "</span>";
                            
                            /* Add span if we have phone number for specific username */
                            if(isset($lookup_results['PhoneNumber']) && $lookup_results['PhoneNumber'] && 
                               isset($lookup_results['Carrier']) && $lookup_results['Carrier']) 
                                echo "<span style='display: block; margin-left: 30px; text-align: left; margin-bottom: 5px; font-size: 20px;'>".
                                        "<input name='message_choice' value='phone' type='radio' style='margin-right: 20px'>".
                                        "<label>Text <b>".$lookup_results['PhoneNumber']."</b> recovery code</label>".
                                     "</span>";
                            
                            /* If we can't find details for email and phone number, print to screen couldn't find username */
                            if(!isset($lookup_results['Email']) && !isset($lookup_results['PhoneNumber']))
                                echo "<b>Could not find any results for username entered. Try again.<b>";
                            
                            /* If we found results, add button to screen to send code with callback */
                            else {
                        ?>
                                <button id='send_code_button' style='border: 1px solid blue; background-color: darkblue; color: white; height: 30px;'>Send Code</button>
                                
                                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                                <!-- Make the onclick event be an ajax request to send code -->
                                <script>
                                    document.getElementById('send_code_button').onclick = function() {
                                        /* Get the value of the radio to determine what kind of message should be sent */
                                        var type = document.querySelector('input[name=message_choice]:checked').value;
                                        var entity = (type == "email" ) ? '<?php echo $lookup_results['Email']; ?>' : '<?php echo convert_phone_to_email($lookup_results['PhoneNumber'], $lookup_results['Carrier']); ?>';
                                        
                                        /* Make the request to server and send message to entity */
                                        $.ajax({
                                            method: 'POST',
                                            url: 'send-code.php',
                                            data: { 'value': entity, 'type': type },
                                            success: function(result) {
                                                alert(result);
                                                window.location.href = 'login.php';
                                            }, 
                                            error: function(error) { alert('Could not send message to: ' + entity); }
                                        });
                                    };
                                </script>
                    <?php
                            }
                        }
                    ?>
                </div>
                
                <!-- Scripts to handle click events for buttons on page -->
                <script type="application/javascript">
                    /* If cancel button is pressed, direct back to login page */
                    document.getElementById('cancel_button').onclick = function() {
                        window.location.href = "login.php";
                    };
                </script>
            </div>
        </div>
    </body>
</html>