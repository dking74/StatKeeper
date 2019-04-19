<?php
    /* If the user tries entering the url into browser return them to login page*/
    if($_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
        die(header("location: login.php"));

    /* Start session and then destroy all variables */
    session_start();
    session_destroy();
?>