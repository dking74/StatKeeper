<?php
    /* If the user tries entering the url into browser return them to login page*/
    if($_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
        die(header("location: login.php"));
    
    include "scripts/common_funcs.php";

    /* Send the message to the inputted value and see if it was able to send correctly */
    $message_sent = send_recovery_message($_POST['value']);
    if($message_sent)
        echo "Recovery message was sent to: ".$_POST['value'];
    else
        echo "Recovery message was unable to be sent to: ".$_POST['value'];
?>