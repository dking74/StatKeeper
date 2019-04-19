<?php
    /* Include common functions to express functionality */
    include "scripts/common_funcs.php";

    session_start();

    /* 
        See if user session variables are set and display team info 
        Query String must also be set --> to get team data 
    */
    if(isset($_SESSION['userID']) && isset($_SESSION['username']) && $_SERVER['QUERY_STRING']) {
        /* Get query string and get the team name from it */
        $decoded_query_str = urldecode($_SERVER['QUERY_STRING']);
        parse_str($decoded_query_str, $player_id);
        
        /* Get the player Id from the query string; if not available, go back to team page */
        $playerId = 0;
        if(isset($player_id['PlayerId']) && $player_id['PlayerId'])
            $playerId = $player_id['PlayerId'];
        else
            die(header("location: ".$_SESSION['team_page']));
?>
        <!-- Deliver this html content if session variables are set -->
        <!DOCTYPE html>
        <html>
            <!-- Deliver head content for html -->
            <head>
                <!-- The title must be the team name entered -->
                <title>Player Page for player id <?php echo $playerId; ?></title>
                <link rel="stylesheet" href="css/team_style.css">
            </head>
            
            <body>
                <!-- Div to hold background image and successive content -->
                <div class="background">
                    <!-- A link to go back to last page -->
                    <span class="back_link">
                        <a href="<?php echo $_SESSION['team_page']; ?>">Back to Team View page</a>
                    </span>
                    <span class="account_opt_span">
                        <label id="account_button">Account</label>
                        <label>|</label>
                        <label id="sign_out_button">Sign Out</label>
                    </span>

                    <!-- To hold the main interaction and form login -->
                    <div class="main_interaction_div">
                        <span class="company_symbol">StatKeeper</span>
                        <div class="adding_entity_div">
                            <!-- Header for player hitting stats -->
                            <h1>Player Hitting Stats</h1>
                            <?php
                                $hitting_stats = get_player_stat_info($playerId, "PlayerHittingStats");
                                if($hitting_stats && $hitting_stats->num_rows) {    
                                    $hitter = mysqli_fetch_assoc($hitting_stats);

                                    /* Go through each hitter and print data inside table */
                                    $counter = 1;
                                    $last_element = count($hitter);
                                    unset($hitter['PlayerID']);
                                    echo "<table class='player_stat_table'>";
                                    foreach($hitter as $key => $value) {
                                        /* If we are an odd number counter,
                                           open up the table row and print data */
                                        if($counter % 2 != 0) {
                                            echo "<tr>".
                                                    "<td>".
                                                        "<div class='stat_key'>".
                                                            $key.
                                                        "</div>".
                                                        "<div>".
                                                            $value.
                                                        "</div>".
                                                    "</td>";
                                            /* If this is last element, make sure to close row */
                                            if($counter == $last_element)
                                                echo "</tr>";
                                        /* If we are an even number counter,
                                           print the data and close the row */
                                        } else {
                                            echo "<td>".
                                                    "<div class='stat_key'>".
                                                        $key.
                                                    "</div>".
                                                    "<div>".
                                                        $value.
                                                    "</div>".
                                                "</td>".
                                                "</tr>";
                                        }
                                        $counter += 1;
                                    }
                                    echo "</table><br><br>";
                                } else
                                    echo "<label class='error_message'>There is no hitting data for the user yet.</label>";
                            ?>
                            
                            <!-- Header for player pitching stats -->
                            <h1>Player Pitching Stats</h1>
                            <?php
                                $pitching_stats = get_player_stat_info($playerId, "PlayerPitchingStats");
                                if($pitching_stats && $pitching_stats->num_rows) {  
                                    $pitcher = mysqli_fetch_assoc($pitching_stats);
        
                                    /* Go through each hitter and print data inside table */
                                    $counter = 1;
                                    $last_element = count($pitcher);
                                    unset($pitcher['PlayerID']);
                                    echo "<table class='player_stat_table'>";
                                    foreach($pitcher as $key => $value) {
                                        /* If we are an odd number counter,
                                           open up the table row and print data */
                                        if($counter % 2 != 0) {
                                            echo "<tr>".
                                                    "<td>".
                                                        "<div class='stat_key'>".
                                                            $key.
                                                        "</div>".
                                                        "<div>".
                                                            $value.
                                                        "</div>".
                                                    "</td>";
                                            /* If this is last element, make sure to close row */
                                            if($counter == $last_element)
                                                echo "</tr>";
                                        /* If we are an even number counter,
                                           print the data and close the row */
                                        } else {
                                            echo "<td>".
                                                    "<div class='stat_key'>".
                                                        $key.
                                                    "</div>".
                                                    "<div>".
                                                        $value.
                                                    "</div>".
                                                "</td>".
                                                "</tr>";
                                        }
                                        $counter += 1;
                                    }
                                    echo "</table><br><br>";
                                } else 
                                    echo "<label class='error_message'>There is no pitching data for the user yet.</label>";
                            ?>
                        </div>
                    </div>
                </div>
            </body>
        </html>
<?php       
    /* Otherwise direct back to login */
    } else {
        header("location: login.php");
    }
?>