<?php
    if($_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
        die(header("location: login.php"));

    include "common_funcs.php";

    /* Start a session to make sure we are logged in */
    session_start();

    /* See if session name is set and TeamNme is sent in post data */
    if(isset($_POST["TeamName"]) && $_POST["TeamName"] &&
       isset($_POST["Year"]) && $_POST["Year"] &&
       isset($_SESSION['userID']) && isset($_SESSION['username'])) {
        
        /* Get the team id associated with team */
        $team_id = get_team_id($_SESSION['userID'], $_POST["TeamName"], $_POST["Year"]);
        
        /* Form database connection and remove from database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $deleted = remove_from_database($database, "Teams", "AND", 
                            array("TeamID" => $team_id));
        
        /* Remove the stats associated with team */
        $deleted = remove_from_database($database, "TeamHittingStats", "AND",
                            array("TeamID" => $team_id));
        $deleted = remove_from_database($database, "TeamPitchingStats", "AND",
                            array("TeamID" => $team_id));
        
        /* Remove all games for team */
        $deleted = remove_from_database($database, "Games", "AND",
                            array("TeamID" => $team_id));
        
        /* Remove all players for team */
        $deleted = remove_from_database($database, "Players", "AND",
                            array("TeamID" => $team_id));
        
        /* Query to find all the player ids that are associated with team */
        $query = "SELECT PlayerID FROM Players WHERE TeamID=".$team_id;
        $result = $database->query($query);
        
        /* Go through each player entry and delete their stats from database */
        if($result && $result->num_rows) {
            while($row = mysqli_fetch_assoc($result)) {
                remove_from_database($database, "PlayerHittingStats", "AND",
                                array("PlayerID" => $row['PlayerID']));
                remove_from_database($database, "PlayerPitchingStats", "AND",
                                array("PlayerID" => $row['PlayerID']));
            }
        }
        close_connection($database);
    }
    
    /* See if session name is set and Game Name is sent in post data */
    else if(isset($_POST['GameNum']) && $_POST['GameNum'] &&
            isset($_SESSION['userID']) && isset($_SESSION['username'])) {
        
        /* Connect to database and remove the specific player */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $deleted = remove_from_database($database, "Games", "AND",
                            array("GameNum" => $_POST['GameNum'], "TeamID" => intval($_POST['TeamID'])));
        if($deleted)
            adjust_gamenums_after_removal(intval($_POST['TeamID']), $_POST['GameNum']);
        
        /*
            1. Recompute stats for all players on the specific team after removing game
            2. Recompute team stats after game being removed
            
            update_player_hitting_stats();
            update_player_pitching_stats();
            update_team_hitting_stats();
            update_team_pitching_stats();
        */
        close_connection($database);
    }

    /* See if session name is set and player name is sent in request */
    else if(isset($_POST['DisplayName']) && $_POST['DisplayName'] &&
            isset($_POST['TeamID']) && $_POST['TeamID'] &&
            isset($_SESSION['userID']) && isset($_SESSION['username'])) {
        
        /* Connect to database and remove the specific player */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $deleted = remove_from_database($database, "Players", "AND",
                            array("DisplayName" => $_POST['DisplayName'], "TeamID" => intval($_POST['TeamID'])));
        close_connection($database);
    }

    /* Otherwise, direct back to login */
    else 
        header("location: login.php");
?>