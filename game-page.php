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
        parse_str($decoded_query_str, $game_info);
        
        /* Get the gameId from the query string; if not available, go back to team page */
        $gameId = 0;
        if(isset($game_info['GameId']) && $game_info['GameId'])
            $gameId = $game_info['GameId'];
        else
            die(header("location: ".$_SESSION['team_page']));
?>
        <!-- Deliver this html content if session variables are set -->
        <!DOCTYPE html>
        <html>
            <!-- Deliver head content for html -->
            <head>
                <!-- The title must be the team name entered -->
                <title>Game ID <?php echo $gameId; ?></title>
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
                        <div class="adding_entity_div" style="border: none;">
                            <!-- Header for player hitting stats -->
                            <fieldset class="fieldset_team">
                                <legend class="fieldset_legend">Basic Game Stats</legend>
                                <?php
                                    /* Get all the information about that specific game */
                                    $game_info = get_game_info($gameId);
                                    $game_data = mysqli_fetch_assoc($game_info);
                                ?>
                                <!-- Place the basic data on screen -->
                                <span class='basic_game_span'><label class='basic_game_label'>Against:</label></span>
                                <span class="basic_game_span"><?php echo $game_data['Against'] ?></span>
                                <span class='basic_game_span'><label class='basic_game_label'>Date:</label></span>
                                <span class="basic_game_span"><?php echo $game_data['Date'] ?></span>
                                <span class='basic_game_span'><label class='basic_game_label'>Score: </label></span>
                                <span class='basic_game_span'><?php echo $game_data['TeamScore']."-".$game_data['OpposingScore'] ?></span>
                                <span class='basic_game_span'><label class='basic_game_label'>Innings:</label></span>
                                <span class='basic_game_span'><?php echo $game_data['Innings'] ?></span>
                                <span class='basic_game_span'><label class='basic_game_label'>Result: </label></span>
                                <span class='basic_game_span'><?php echo $game_data['Win_Loss'] ?></span>
                                <span class='basic_game_span'><label class='basic_game_label'>Home or Away:</label></span>
                                <span class='basic_game_span'><?php echo $game_data['Home_Away'] ?></span>
                                <span class='basic_game_span'><label class='basic_game_label'>Location: </label></span>
                                <span class='basic_game_span'><?php echo $game_data['Location'] ?></span>
                            </fieldset><br><br>
                            
                            <!-- Header for game hitting stats -->
                            <fieldset class="fieldset_team">
                                <legend class="fieldset_legend">Game hitting stats</legend>
                                <?php
                                    /* Get the serialized data from the database */
                                    $serialized_hitting_data = $game_data['HittingStats'];
                                    $players = unserialize_player_stats($serialized_hitting_data);
        
                                    echo "<table class='entity_view_table'>".
                                            "<tr>".
                                                "<th>Player Name</th>".
                                                "<th>PA</th>".
                                                "<th>AB</th>".
                                                "<th>Avg</th>".
                                                "<th>Hits</th>".
                                                "<th>2B</th>".
                                                "<th>3B</th>".
                                                "<th>HR</th>".
                                                "<th>RBI</th>".
                                                "<th>K</th>".
                                                "<th>FO</th>".
                                                "<th>GO</th>".
                                                "<th>BB</th>".
                                                "<th>Runs</th>".
                                                "<th>OBP</th>".
                                                "<th>SLG</th>".
                                                "<th>OPS</th>".
                                                "<th>SB</th>".
                                            "</tr>";
        
                                    /* Go through each player and add his/her data to screen */
                                    foreach($players as $player => $data) {
                                        echo "<tr>".
                                                "<td>".$player."</td>".
                                                "<td>".$data['PlateAppearances']."</td>".
                                                "<td>".$data['AtBats']."</td>".
                                                "<td>".$data['Avg']."</td>".
                                                "<td>".$data['Hits']."</td>".
                                                "<td>".$data['Doubles']."</td>".
                                                "<td>".$data['Triples']."</td>".
                                                "<td>".$data['Homers']."</td>".
                                                "<td>".$data['RBIs']."</td>".
                                                "<td>".$data['Strikeouts']."</td>".
                                                "<td>".$data['Flyouts']."</td>".
                                                "<td>".$data['Groundouts']."</td>".
                                                "<td>".$data['Walks']."</td>".
                                                "<td>".$data['Runs']."</td>".
                                                "<td>".$data['Obp']."</td>".
                                                "<td>".$data['Slg']."</td>".
                                                "<td>".$data['Ops']."</td>".
                                                "<td>".$data['StolenBases']."</td>".
                                             "</tr>";
                                    }
                                    echo "</table>";      
                                ?>
                            </fieldset><br><br>
                            
                            <!-- Header for game pitching stats -->
                            <fieldset class="fieldset_team">
                                <legend class="fieldset_legend">Game pitching stats</legend>
                                <?php
                                    /* Get the serialized data from the database */
                                    $serialized_pitching_stats = $game_data['PitchingStats'];
                                    $players = unserialize_player_stats($serialized_pitching_stats);
        
                                    echo "<table class='entity_view_table'>".
                                            "<tr>".
                                                "<th>Player Name</th>".
                                                "<th>Started</th>".
                                                "<th>IP</th>".
                                                "<th>Hits</th>".
                                                "<th>Runs</th>".
                                                "<th>Earned Runs</th>".
                                                "<th>BB</th>".
                                                "<th>K</th>".
                                                "<th>W</th>".
                                                "<th>L</th>".
                                                "<th>DP</th>".
                                                "<th>Pitches</th>".
                                                "<th>ERA</th>".
                                                "<th>K/BB</th>".
                                                "<th>P/IP</th>".
                                                "<th>K/7</th>".
                                            "</tr>";
        
                                    /* Go through each player and add his/her data to screen */
                                    foreach($players as $player => $data) {
                                        echo "<tr>".
                                                "<td>".$player."</td>".
                                                "<td>".$data['GamesStarted']."</td>".
                                                "<td>".$data['InningsPitched']."</td>".
                                                "<td>".$data['Hits']."</td>".
                                                "<td>".$data['Runs']."</td>".
                                                "<td>".$data['EarnedRuns']."</td>".
                                                "<td>".$data['Walks']."</td>".
                                                "<td>".$data['Strikeouts']."</td>".
                                                "<td>".$data['Wins']."</td>".
                                                "<td>".$data['Losses']."</td>".
                                                "<td>".$data['DoublePlays']."</td>".
                                                "<td>".$data['Pitches']."</td>".
                                                "<td>".$data['ERA']."</td>".
                                                "<td>".$data['K_per_BB']."</td>".
                                                "<td>".$data['P_per_IP']."</td>".
                                                "<td>".$data['K_per_7']."</td>".
                                             "</tr>";
                                    }
                                    echo "</table>"; 
                                ?>
                            </fieldset>
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