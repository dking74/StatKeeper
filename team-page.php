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
        parse_str($decoded_query_str, $team_name);
        
        /* Make sure array value is set so we can get the full Team name and year of team */
        $teamName = $year = "";
        if(isset($team_name['teamName']) && $team_name['teamName'])
            $teamName = $team_name['teamName'];
        if(isset($team_name['Year']) && $team_name['Year'])
            $year = $team_name['Year'];
        
        /* Set session variables for team page, team name, and year so we can use them later */
        $_SESSION['team_page'] = basename($_SERVER["REQUEST_URI"], ".php");
        $_SESSION['team_id'] = get_team_id($_SESSION['userID'], $teamName, $year);
        $_SESSION['team_year'] = $year;
?>
        <!-- Deliver this html content if session variables are set -->
        <!DOCTYPE html>
        <html>
            <!-- Deliver head content for html -->
            <head>
                <!-- The title must be the team name entered -->
                <title>Team Page for <?php echo $teamName; ?></title>
                <link rel="stylesheet" href="css/team_style.css">
            </head>
            
            <!-- Deliver body content for html -->
            <body>
                 <!-- Div to hold background image and successive content -->
                <div class="background">
                    <!-- A link to go back to last page -->
                    <span class="back_link">
                        <a href="<?php echo $_SESSION['main_page']; ?>">Back to Team Option page</a>
                    </span>
                    <span class="account_opt_span">
                        <label id="account_button">Account</label>
                        <label>|</label>
                        <label id="sign_out_button">Sign Out</label>
                    </span>

                    <!-- To hold the main interaction and form login -->
                    <div class="main_interaction_div">
                        <span class="company_symbol">StatKeeper</span>
                        
                        <!-- Div for viewing past games -->
                        <div class ="entity_viewing_div">
                            <span>
                                <?php 
                                    /* If access is basic, disable buttons so user can't use them */
                                    if($_SESSION['privileges'] === "basic") {
                                        echo '<button id="game_delete_button" class="delete_button" disabled>- Delete Game</button>';
                                        echo '<button id="game_add_button" class="add_button" disabled>+ Add Game</button>';
                                    
                                    /* Otherwise, allow edit access to user */
                                    } else{
                                        echo '<button id="game_delete_button" class="delete_button">- Delete Game</button>';
                                        echo '<button id="game_add_button" class="add_button">+ Add Game</button>';
                                    }
                                ?>
                            </span>
                            <fieldset class="fieldset_team">
                                <legend class="fieldset_legend">Recent Games</legend>
                                <table id="game_table" class="entity_view_table">
                                    <tr>
                                        <th style='background-color: white; border: none;'></th>
                                        <th>Game #</th>
                                        <th>Opponent</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Result</th>
                                        <th>Team Score</th>
                                        <th>Opponent Score</th>
                                        <th>Innings</th>
                                    </tr>
                                    <?php
                                        /* Get the team id, and then the players with that team */
                                        $games = get_team_games($_SESSION['team_id']);

                                        /* Go through each player and print in table to screen in player div */
                                        $counter = 1;
                                        while($row = mysqli_fetch_assoc($games)) {
                                            /* Append either vs. or @ to opponent depending on home or away */
                                            $game_descr = "";
                                            if($row['Home_Away'] && $row['Home_Away'] === "Home")
                                                $game_descr = "vs. ".$row['Against'];
                                            else if($row['Home_Away'] && $row['Home_Away'] === "Away")
                                                $game_descr = "@ ".$row['Against'];

                                            /* Get the game id of the current element */
                                            $game_id = get_game_id(array(
                                                                    "Against" => $row["Against"],
                                                                    "GameNum" => $row["GameNum"],
                                                                    "Date" => $row['Date']));

                                            /* Print row contents to screen */
                                            echo "<tr id='Game".$counter."' name='".$row['GameNum']."'>".
                                                    "<td style='border: none;'><input type='checkbox' name='".$row['GameNum']."' value=".$counter."></td>".
                                                    "<td>".$row['GameNum']."</td>".
                                                    "<td><a href='game-page.php?&GameId=".$game_id."'>".$game_descr."</a></td>".
                                                    "<td>".$row['Date']."</td>".
                                                    "<td>".$row['Location']."</td>".
                                                    "<td>".$row['Win_Loss']."</td>".
                                                    "<td>".$row['TeamScore']."</td>".
                                                    "<td>".$row['OpposingScore']."</td>".
                                                    "<td>".$row['Innings']."</td>".
                                                "</tr>";
                                            $counter += 1;
                                        }
                                    ?>
                                </table>
                            </fieldset>
                        </div><br><br>
                        
                        <!-- Div for viewing players -->
                        <div class ="entity_viewing_div">
                            <span>
                                <?php 
                                    /* If access is basic, disable buttons so user can't use them */
                                    if($_SESSION['privileges'] === "basic") {
                                        echo '<button id="player_delete_button" class="delete_button" disabled>- Delete Player</button>';
                                        echo '<button id="player_add_button" class="add_button" disabled>+ Add Player</button>';
                                    
                                    /* Otherwise, allow edit access to user */
                                    } else{
                                        echo '<button id="player_delete_button" class="delete_button">- Delete Player</button>';
                                        echo '<button id="player_add_button" class="add_button">+ Add Player</button>';
                                    }
                                ?>
                            </span>
                            <fieldset class="fieldset_team">
                                <legend class="fieldset_legend">Players</legend>
                                <table id="player_table" class="entity_view_table">
                                    <tr>
                                        <th style='background-color: white; border: none;'></th>
                                        <th>Player Name</th>
                                        <th>Jersey #</th>
                                        <th>Main Position</th>
                                        <th>Secondary Position</th>
                                        <th>Birthday</th>
                                        <th>Height</th>
                                        <th>Weight</th>
                                    </tr>
                                    <?php
                                        /* Get the team id, and then the players with that team */
                                        $teamId = get_team_id($_SESSION['userID'], $teamName, $year);
                                        $players = get_team_players($teamId);

                                        /* Go through each player and print in table to screen in player div */
                                        $counter = 1;                
                                        while($row = mysqli_fetch_assoc($players)) {
                                            echo "<tr id='Player".$counter."' name='".$row['DisplayName']."'>".
                                                    "<td style='border: none;'><input type='checkbox' name='".$row['DisplayName']."' value=".$counter."></td>".
                                                    "<td><a href='player-page.php?PlayerId=".$row['PlayerID']."'>".$row['DisplayName']."</a></td>".
                                                    "<td>".$row['JerseyNumber']."</td>".
                                                    "<td>".$row['MainPosition']."</td>".
                                                    "<td>".$row['SecondaryPosition']."</td>".
                                                    "<td>".$row['BirthDate']."</td>".
                                                    "<td>".$row['Height']."</td>".
                                                    "<td>".$row['Weight']."</td>".
                                                "</tr>";
                                            $counter += 1;
                                        }
                                    ?>
                                </table>
                            </fieldset>
                        </div><br><br>
                        
                        <!-- Div for viewing team stats -->
                        <div class="entity_viewing_div">
                            <fieldset class="fieldset_team">
                                <legend class="fieldset_legend">Team Stats</legend>
                                <!-- Table for team hitting -->
                                <h1 class="team_stat_header">Hitting</h1>
                                <table class="entity_view_table">
                                    <tr>
                                        <th>PA</th>
                                        <th>AB</th>
                                        <th>Avg</th>
                                        <th>Hits</th>
                                        <th>2B</th>
                                        <th>3B</th>
                                        <th>HR</th>
                                        <th>RBI</th>
                                        <th>Runs</th>
                                        <th>BB</th>
                                        <th>K</th>
                                        <th>OBP</th>
                                        <th>SLG</th>
                                        <th>OPS</th>
                                        <th>FO</th>
                                        <th>GO</th>
                                        <th>Sac.</th>
                                        <th>SB</th>
                                    </tr>
                                    <?php
                                        /* Deliver team data to screen, getting team hitting stats */
                                        $hitting_stats = get_team_hitting_stats($_SESSION['team_id']);
                                        $stats = mysqli_fetch_assoc($hitting_stats);
                                        echo "<tr>";
                                        foreach($stats as $key => $value)
                                            /* Dont print team id field */
                                            if($key !== "TeamID")
                                                echo "<td>".$value."</td>";
                                        echo "</tr>";
                                    ?>  
                                </table>
                                
                                <!-- Table for team pitching -->
                                <h1 class="team_stat_header">Pitching</h1>
                                <table class="entity_view_table">
                                    <tr>
                                        <th>Innings</th>
                                        <th>ERA</th>
                                        <th>Wins</th>
                                        <th>Losses</th>
                                        <th>Hits</th>
                                        <th>Runs</th>
                                        <th>Earned Runs</th>
                                        <th>BB</th>
                                        <th>K</th>
                                        <th>Pitches</th>
                                        <th>K/BB</th>
                                        <th>P/IP</th>
                                        <th>P/G</th>
                                        <th>K/7</th>
                                        <th>DP</th>
                                    </tr>
                                    <?php
                                        /* Deliver team data to screen, getting team pitching stats */
                                        $pitching_stats = get_team_pitching_stats($_SESSION['team_id']);
                                        $stats = mysqli_fetch_assoc($pitching_stats);
                                        echo "<tr>";
                                        foreach($stats as $key => $value)
                                            /* Dont print team id field */
                                            if($key !== "TeamID" && $key !== "GamesPitched" && $key !== "GamesStarted")
                                                echo "<td>".$value."</td>";
                                        echo "</tr>";
                                    ?>
                                </table><br><br>
                            </fieldset>
                        </div>
                    </div>
                </div>
                
                <!-- Add script sources to footer -->
                <footer>
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>          
                    <script type='application/javascript' src="scripts/team_js.js"></script>
                    <script type="application/javascript">
                        /* Register player delete button to delete*/
                        $('#player_delete_button').on('click', function() {
                            delete_function('player_table', 'DisplayName', 'TeamID', 'Player', <?php echo $_SESSION['team_id']; ?>, false);
                        });
                        
                        /* Register game delete button to delete specific game */
                        $('#game_delete_button').on('click', function() {
                            delete_function('game_table', 'GameNum', 'TeamID', 'Game', <?php echo $_SESSION['team_id']; ?>,
                            false);
                        });
                    </script>
                </footer>
            </body>
        </html>
        <!-- End delivering html content -->
<?php
    /* If the session values aren't set and we don't have query string set, lets return to login */
    } else 
        header("location: login.php");
?>
