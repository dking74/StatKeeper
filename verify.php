<?php
    /* If the user tries entering the url into browser return them to login page*/
    if($_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
        die(header("location: login.php"));
    
    /* Common funcs contains functions for manipulating database */
    include "scripts/common_funcs.php";

    /* Start the session if one does not exist already */
    session_start();

    /* Handle if login form submits request --> determine if login credentials right */
    if(isset($_POST['form']) && $_POST['form'] === "login_form") {
        $results = is_user_created($_POST['username'], $_POST['password']);

        /* If we successfully are able to login, set session variables of user and redirect to individual users page */
        if($results === true) {
            get_user_login_variables($_POST['username']);
            header("location: team-stats.php?UserID=".$_SESSION['userID']);
        /* If user is not verified, add to error messages and go back to login screen */
        } else {
            $_SESSION['errors'] = "The username/password combination did not match any of our records";
            header("location: login.php?authentication_failure");
        }
    /* Handle if sign up form submits request --> determine if sign up details are appropriate */
    } else if(isset($_POST['form']) && $_POST['form'] === "sign_up_form") {
        /* Determine if the signup form is valid; print the errors */
        $errors = check_signup_form(
                        $_POST['userID'], 
                        $_POST['password'], 
                        $_POST['re_password'],
                        $_POST['email'],
                        $_POST['phone_num'],
                        $_POST['carrier']);
        /* If there are no errors in the signup form, alert user and create database object */
        if(!sizeof($errors)) {
            // echo "<script>var userID = '".$_POST['userID']."'; alert('Account has been created for: ' + userID);</script>";
            create_database_user(
                        $_POST['userID'], 
                        $_POST['password'],
                        $_POST['email'],
                        $_POST['phone_num'],
                        $_POST['carrier']);
            get_user_login_variables($_POST['userID']);
            header("location: team-stats.php?UserID=".$_SESSION['userID']);
        /* If there are errors, set the session variable of the errors, and return to signup form */
        } else {
            $_SESSION['errors'] = $errors;
            header("location: Sign-up.php?signup_failure");
        }
    }
?> 