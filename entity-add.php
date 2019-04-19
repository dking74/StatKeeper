<?php
    session_start();
    
    /* If we are not logged in, direct back to login */
    if(!isset($_SESSION['userID']) || !isset($_SESSION['username']))
        die(header("location: login.php"));
    
    /* Determine if a page referred us to this page-> if not, we need to go to the main page */
    if(!isset($_SERVER["HTTP_REFERER"]))
        die(header("location: ".$_SESSION['main_page']));

    /* If we did get referred here, and the previous page has not been set
       save the referring variable into session variable to use on form later on */
    if(!isset($_SESSION['previous_page']))
        $_SESSION['previous_page'] = $_SERVER["HTTP_REFERER"];

    /* Get the query string, which is the extension of the url, from the url */
    $type_add = $_SERVER['QUERY_STRING'];

    /* If there is no query string, we won't add */
    if(!$type_add) {
       header("location.login.php");
        
    /* If the query string is teamAdd, take user to that form */
    } else if($type_add === "teamAdd") {
        include "scripts/common_funcs.php";
?>
        <!DOCTYPE html>
        <html>
            <head>
                <!-- Title of page should be unique to username -->
                <title><?php echo "Team Add for ".$_SESSION['username']; ?></title>
                <link rel="stylesheet" href="css/team_style.css">
            </head>

            <body>
                 <!-- Div to hold background image and successive content -->
                <div class="background">
                    <span class="account_opt_span">
                        <label id="account_button">Account</label>
                        <label>|</label>
                        <label id="sign_out_button">Sign Out</label>
                    </span>
            
                    <!-- To hold the main interaction and adding form -->
                    <div class="main_interaction_div">
                        <span class="company_symbol">StatKeeper</span>
                        <div class="adding_entity_div">
                            <!-- Header for the form -->
                            <h1>Add Team</h1>
                            <form class="adding_entity_form" id="adding_form" action="#" method="post">
                                
                                <!-- Span for main team info -->
                                <span class="row_span">
                                    <span><label for="TeamName">Team Name:</label><input name="TeamName" type="text" required></span>
                                    <span><label for="HeadCoach">Head Coach:</label><input name="HeadCoach" type="text" required></span>
                                </span>
                                
                                <!-- Span for team colors -->
                                <span class="row_span">
                                    <span><label for="PrimaryColor">Primary Color:</label><input name="PrimaryColor" type="text" required></span>
                                    <span><label for="SecondaryColor">Secondary Color:</label><input name="SecondaryColor" type="text"></span>
                                </span>
                                
                                <!-- Span to specific team data -->
                                <span class="row_span">
                                    <span><label for="NumPlayers">Num. of Players:</label><input name="NumPlayers" value="12" min="0" type="number"></span>
                                    <span><label for="Year">Year:</label><input name="Year" value="2019" min="2015" type="number"></span>
                                </span>
                                
                                <!-- Span to hold geography data -->
                                <span class="row_span">
                                    <span><label for="City">City:</label><input name="City" type="text"></span>
                                    <span><label for="State">State:</label>
                                        <!-- Selector for state -->
                                        <select form="adding_form" name="State">
                                            <option value="AL">Alabama</option>
                                            <option value="AK">Alaska</option>
                                            <option value="AZ">Arizona</option>
                                            <option value="AR">Arkansas</option>
                                            <option value="CA">California</option>
                                            <option value="CO">Colorado</option>
                                            <option value="CT">Connecticut</option>
                                            <option value="DE">Delaware</option>
                                            <option value="DC">District Of Columbia</option>
                                            <option value="FL">Florida</option>
                                            <option value="GA">Georgia</option>
                                            <option value="HI">Hawaii</option>
                                            <option value="ID">Idaho</option>
                                            <option value="IL">Illinois</option>
                                            <option value="IN">Indiana</option>
                                            <option value="IA">Iowa</option>
                                            <option value="KS">Kansas</option>
                                            <option value="KY">Kentucky</option>
                                            <option value="LA">Louisiana</option>
                                            <option value="ME">Maine</option>
                                            <option value="MD">Maryland</option>
                                            <option value="MA">Massachusetts</option>
                                            <option value="MI">Michigan</option>
                                            <option value="MN">Minnesota</option>
                                            <option value="MS">Mississippi</option>
                                            <option value="MO">Missouri</option>
                                            <option value="MT">Montana</option>
                                            <option value="NE">Nebraska</option>
                                            <option value="NV">Nevada</option>
                                            <option value="NH">New Hampshire</option>
                                            <option value="NJ">New Jersey</option>
                                            <option value="NM">New Mexico</option>
                                            <option value="NY">New York</option>
                                            <option value="NC">North Carolina</option>
                                            <option value="ND">North Dakota</option>
                                            <option value="OH">Ohio</option>
                                            <option value="OK">Oklahoma</option>
                                            <option value="OR">Oregon</option>
                                            <option value="PA">Pennsylvania</option>
                                            <option value="RI">Rhode Island</option>
                                            <option value="SC">South Carolina</option>
                                            <option value="SD">South Dakota</option>
                                            <option value="TN">Tennessee</option>
                                            <option value="TX">Texas</option>
                                            <option value="UT">Utah</option>
                                            <option value="VT">Vermont</option>
                                            <option value="VA">Virginia</option>
                                            <option value="WA">Washington</option>
                                            <option value="WV">West Virginia</option>
                                            <option value="WI">Wisconsin</option>
                                            <option value="WY">Wyoming</option>
                                        </select></span>
                                </span>
                                
                                <!-- Span for game action control -->
                                <span class="row_span">
                                    <span><label for="GamesWon">Games Won:</label><input name="GamesWon" min="0" type="number"></span>
                                    <span><label for="GamesLost">Games Lost:</label><input name="GamesLost" min="0" type="number"></span>
                                </span>
                                
                                <!-- Span for container for buttons -->
                                <span class="add_button_container">
                                    <button class="add_button" id="cancel_button">Cancel</button>
                                    <input class="add_button" type="submit" value="Add team">
                                </span>
                            </form>
                            <?php
                                /* If post is set, that means submit button was pressed --> Send team add info to controller and add to database */
                                if($_POST) {
                                    /* Modify post variables to send to user server and then create database entry */
                                    $_POST['UserID'] = $_SESSION['userID'];
                                    $_POST['NumPlayers'] = intval($_POST['NumPlayers']);
                                    $_POST['Year'] = intval($_POST['Year']);
                                    $_POST['GamesWon'] = intval($_POST['GamesWon']);
                                    $_POST['GamesLost'] = intval($_POST['GamesLost']);
                                    $_POST['GamesPlayed'] = $_POST['GamesWon'] + $_POST['GamesLost'];

                                    /* Determine if the data entered has not already been seen in database */
                                    $valid = true;
                                    $user_teams = get_user_teams($_SESSION['userID']);
                                    while($row = mysqli_fetch_assoc($user_teams)) {
                                        if($row['TeamName'] == $_POST['TeamName'] && 
                                           $row['Year'] == $_POST['Year']) {
                                            $valid = false;
                                            break;
                                        }
                                    }
            
                                    /* If the data has not been seen, create entry in database and go back */
                                    if($valid) {
                                        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
                                        $verify = add_to_database($database, "Teams", $_POST);
                                        
                                        /* If the team was able to be created, create database entry for team stats */
                                        if($verify) {
                                            $team_id = get_team_id($_POST['UserID'], $_POST['TeamName'], $_POST['Year']);

                                            /* Initialize team stats in database */
                                            add_to_database($database, "TeamHittingStats", array(
                                                                             "TeamID" => $team_id));
                                            add_to_database($database, "TeamPitchingStats", array(
                                                                             "TeamID" => $team_id));
                                        }
                                        close_connection($database);

                                        /* Get the session variable for the previous page, 
                                           unset the session variable, then go to that prior page */
                                        $previous_page = $_SESSION['previous_page'];
                                        unset($_SESSION['previous_page']);
                                        echo "<script>window.location.href = '".$previous_page."';</script>";
                                    }

                                    /* Otherwise, stay here with error message */
                                    else
                                        echo "<label class='error_message'>The Team Name/Year combination is already available</label>";
                                }  
                            ?>
                        </div>
                    </div>
                </div>
        
                <!-- Add script sources to footer -->
                <footer>
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>          
                    <script type='application/javascript' src="scripts/team_js.js"></script>
                    <script type="application/javascript">
                        $('#cancel_button').on('click', function() {
                           window.location.href = '<?php echo $_SESSION['main_page']; ?>'; 
                        });
                    </script>
                </footer>
            </body>
        </html>
<?php
    /* If the query string is gameAdd and the team page has been visited, take user to that form */
    } else if($type_add === "gameAdd" && isset($_SESSION['team_id']) && $_SESSION['team_id']) {
        include "scripts/common_funcs.php";
?>    
        <!DOCTYPE html>
        <html>
            <head>
                <!-- Title of page should be unique to username -->
                <title><?php echo "Game Add for ".$_SESSION['username']; ?></title>
                <link rel="stylesheet" href="css/team_style.css">
            </head>

            <body>
                 <!-- Div to hold background image and successive content -->
                <div class="background">
                    <span class="account_opt_span">
                        <label id="account_button">Account</label>
                        <label>|</label>
                        <label id="sign_out_button">Sign Out</label>
                    </span>

                    <!-- To hold the main interaction and adding form -->
                    <div class="main_interaction_div">
                        <span class="company_symbol">StatKeeper</span>
                        <div class="adding_entity_div">
                            <h1>Add Game</h1>
                            <form class="adding_entity_form" id="adding_form" action="#" method="post">
                                
                                <fieldset class="game_fieldset">
                                    <legend>Basic Game Info</legend>
                                    
                                    <!-- Span for innings being entered -->
                                    <span class="game_radio_options">
                                        <label for="Innings" style="margin-right: 10px;">Innings</label>
                                        <select form="adding_form" name="Innings">
                                            <?php
                                                /* Go through numbers 1 through 20 to make innings field; 7th inning should be defualt */
                                                for($i=1; $i<20; $i++) {
                                                    $selected = "";
                                                    if($i == 7) $selected = "selected";
                                                    echo "<option style='width: 20px;' ".$selected.">".$i."</option>";
                                                }
                                            ?>
                                        </select>
                                    </span>

                                    <!-- Span for Win Loss -->
                                    <span class="game_radio_options">
                                        <label style="margin-right: 20px;">Win?</label>
                                        <label>Yes</label><input type="radio" name="Win_Loss" value="Win" checked>
                                        <label>No</label><input type="radio" name="Win_Loss" value="Loss">
                                    </span>

                                    <!-- Span for home away control -->
                                    <span class="game_radio_options">
                                        <label>Home</label><input type="radio" name="Home_Away" value="Home" checked>
                                        <label style="margin-left: 15px;">Away</label><input type="radio" name="Home_Away" value="Away">
                                    </span>
                                    
                                    <!-- Span to enter date of game -->
                                    <span class="game_radio_options">
                                        <label for="GameDate" style="margin-right: 20px;">Game Date</label>
                                        <input style="width: 100px; border: 1px solid darkblue;" name="GameDate" type="date" placeholder="yyyy-mm-dd">
                                    </span><br><br>
                                    
                                    <!-- Span to enter visiting team name -->
                                    <span class="row_span">
                                        <span><label for="Location">Location</label><input type="text" name="Location" required></span>
                                        <span><label for="Against">Opposing Team</label><input type="text" name="Against" required></span>
                                    </span>
                                    
                                    <!-- Span to enter scores for game -->
                                    <span class="row_span">
                                        <span><label for="TeamScore">Your Score</label><input type="number" name="TeamScore" min="0" required></span>
                                        <span><label for="OpposingScore">Opponents Score</label><input type="number" name="OpposingScore" min="0" required></span>
                                    </span>

                                </fieldset>
                                
                                <!-- Fieldset to enter hitting data for players during game -->
                                <fieldset class="game_fieldset">
                                    <legend>Player Hitting Data</legend>
                                    <?php
                                        /* Get the players and num players for the specific team */
                                        $players = get_team_players($_SESSION['team_id']);
                                        $num_players = $players->num_rows;
                                    
                                        /* Determine if we have players in database; present table accordingly */
                                        $invalid = false;
                                        if($num_players < 9) {
                                            $invalid = true;
                                            echo "<label class='error_message'>Please enter at least 9 player names before entering game data.</label>";
                                        } else {
                                            
                                            /* Generate a table element for all players hitting data */
                                            echo "<table class='entity_view_table'>".
                                                    "<tr>".
                                                        "<th>#</th>".
                                                        "<th>Player Name</th>".
                                                        "<th>Singles</th>".
                                                        "<th>Doubles</th>".
                                                        "<th>Triples</th>".
                                                        "<th>Home Runs</th>".
                                                        "<th>RBIs</th>".
                                                        "<th>BBs</th>".
                                                        "<th>Ks</th>".
                                                        "<th>FOs</th>".
                                                        "<th>GOs</th>".
                                                        "<th>Runs</th>".
                                                        "<th>Stolen Bases</th>".
                                                        "<th>Sacrifices</th>".
                                                    "</tr>";
                                            for($i = 1; $i < $num_players + 1; $i++){
                                                /* Configure options for dropdown display name select */
                                                $options = "<option selected value>-- select an option --</option>";
                                                $players->data_seek(0);
                                                while($row = mysqli_fetch_assoc($players))
                                                    $options .= "<option>".$row["DisplayName"]."</option>";
                                                
                                                /* Configure table row */
                                                echo "<tr>".
                                                        "<td>".$i."</td>".
                                                        "<td>".
                                                            "<select form='adding_form' value='' name='HittingName".$i."'>".$options."</select>".
                                                        "</td>".
                                                        "<td><input name='Singles".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Doubles".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Triples".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Homers".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='RBIs".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Walks".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Strikeouts".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Flyouts".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Groundouts".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Runs".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='StolenBases".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input name='Sacrifices".$i."' type='number' min='0' value=0 class='player_stat_td'></td>".
                                                     "</tr>";
                                            }
                                            echo "</table><br>";
                                        }   
                                    ?>
                                </fieldset>
                            
                                <!-- Fieldset to enter pitching data for players during game -->
                                <fieldset class="game_fieldset">
                                    <legend>Player Pitching Data</legend>
                                    <?php
                                    
                                        /* Determine if we have players in database; present table accordingly */
                                        if($num_players < 9) {
                                            echo "<label class='error_message'>Please enter at least 9 player names before entering game data.</label>";
                                        } else {
                                            
                                            /* Generate a table element for all players pitching data */
                                            echo "<table class='entity_view_table'>".
                                                    "<tr>".
                                                        "<th>#</th>".
                                                        "<th>Player Name</th>".
                                                        "<th>Started</th>".
                                                        "<th>Win</th>".
                                                        "<th>Innings Pitched</th>".
                                                        "<th>Hits</th>".
                                                        "<th>Runs</th>".
                                                        "<th>Earned Runs</th>".
                                                        "<th>Walks</th>".
                                                        "<th>Strikouts</th>".
                                                        "<th>Double Plays</th>".
                                                        "<th>Pitches</th>".
                                                    "</tr>";
                                            
                                            /* Reset the pointer to beginning and go through each player */
                                            $players->data_seek(0);
                                            for($i = 1; $i < $num_players + 1; $i++) {
                                                /* Configure options for dropdown display name select */
                                                $options = "<option selected value>-- select an option --</option>";
                                                $players->data_seek(0);
                                                while($row = mysqli_fetch_assoc($players))
                                                    $options .= "<option>".$row["DisplayName"]."</option>";
                                                
                                                /* Configure table row */
                                                echo "<tr>".
                                                        "<td>".$i."</td>".
                                                        "<td>".
                                                            "<select form='adding_form' name='PitchingName".$i."'>".$options."</select>".
                                                        "</td>".
                                                        "<td>".
                                                            "<select form='adding_form' name='GamesStarted".$i."'><option>Yes</option><option>No</option></select>".
                                                        "</td>".
                                                        "<td>".
                                                            "<select form='adding_form' name='Wins".$i."'><option>No decision</option><option>Yes</option><option>No</option></select>".
                                                        "</td>".
                                                        "<td><input type='text' name='InningsPitched".$i."' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input type='number' name='PHits".$i."' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input type='number' name='PRuns".$i."' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input type='number' name='EarnedRuns".$i."' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input type='number' name='PWalks".$i."' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input type='number' name='PStrikeouts".$i."' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input type='number' name='DoublePlays".$i."' min='0' value=0 class='player_stat_td'></td>".
                                                        "<td><input type='number' name='Pitches".$i."' min='0' value=0 class='player_stat_td'></td>".
                                                     "</tr>";
                                            }
                                            echo "</table><br>";
                                        }   
                                    ?>
                                </fieldset>
                                
                                <!-- Span for container for buttons -->
                                <span class="add_button_container">
                                    <button class="add_button" id="cancel_button">Cancel</button>
                                    <input class="add_button" type="submit" value="Add game">
                                </span>
                            </form>
                            <?php
                                /* If post is set, that means submit button was pressed --> 
                                Send team add info to controller and add to database */
                                if($_POST) {
                                    $valid_date = check_valid_game_date($_POST['GameDate'], $_SESSION['team_year']);
                                    
                                    /* If the data has not been seen, create entry in database and go back */
                                    if($valid_date) {
                                        /* Array for keeping track of errors in form */
                                        $errors = array();
                                        
                                        /* Get updated info for basic game info, player hitting, and player pitching and validate the data entered by user */
                                        $_POST['TeamID'] = $_SESSION['team_id'];
                                        $basic_game_info = get_basic_game_info($_POST);
                                        
                                        /* Check to make sure basic game data has not been entered before */
                                        $checked_game = check_game_existing($basic_game_info);
                                        if($checked_game && !$invalid) {
                                            
                                            /* Get the proper game number for the game to be added and add additional parameters to request */
                                            $_POST['GameNum'] = get_game_num_from_date($_SESSION['team_id'], $_POST['GameDate']);
                                            $basic_game_info['GameNum'] = $_POST['GameNum'];
                                            
                                            /* Go through each player and add data */
                                            $hitters = $pitchers = array();
                                            for($i = 1; $i < $num_players + 1; $i++) {
                                                $basic_player_hitting = get_game_player_hitting($_POST, $i);

                                                /* If there isn't a hitters name when less than 9 entries in, raise error */
                                                if((!$_POST['HittingName'.$i] && $i <= 9) || in_array($_POST['HittingName'.$i], array_keys($hitters))) {
                                                    array_push($errors, "You must enter in 9 valid, different hitters to register a game.");
                                                    break;
                                                }
                                                
                                                /* Valid that the hitting values are valid */
                                                $updated_hitting = validate_hitting($basic_player_hitting, $i);
                                                if($updated_hitting != "Success") {
                                                    array_push($errors, $updated_hitting);
                                                    break;
                                                }

                                                /* If the name being looked at is not empty, add it to hitters list */
                                                if($_POST['HittingName'.$i]) 
                                                    $hitters[$_POST['HittingName'.$i]] = $basic_player_hitting;

                                                $basic_player_pitching = get_game_player_pitching($_POST, $i);
                                                if((!$_POST['PitchingName'.$i] && $i == 1) || in_array($_POST['PitchingName'.$i], array_keys($pitchers))) {
                                                    array_push($errors, "The game must have at least one pitcher and all different pitchers.");
                                                    break;
                                                }

                                                /* Valid that the pitching values are valid */
                                                $updated_pitching = validate_pitching($basic_player_pitching, $i);
                                                if($updated_pitching != "Success") {
                                                    array_push($errors, $updated_pitching);
                                                    break;
                                                }
                                                
                                                /* If the pitching name being looked at is not empty, add it to pitchers list */
                                                if($_POST['PitchingName'.$i]) 
                                                    $pitchers[$_POST['PitchingName'.$i]] = $basic_player_pitching;
                                            }

                                            /* Determine if validation produced errors --> if so, print them
                                               If not, add the entries to the database */
                                            if($errors) {
                                                /* print error messages */
                                                foreach($errors as $error)
                                                    echo "<label class='error_message'>".$error."</label>";
                                            
                                            /* If there are no errors, and 9 hitters entered and 1 pitcher entered, we can complete querying */
                                            } else if(!$errors && count($hitters) >= 9 && count($pitchers) >= 1) {
                                                /* Go through each hitter added and update their information */
                                                $hitting_stats = array();
                                                foreach($hitters as $hitter => $value) {
                                                    $player_id = get_player_id($_SESSION['team_id'], $hitter);

                                                    /* Get the stats for the current player, then get the data associated */
                                                    $current_player_info = get_player_stat_info($player_id, "PlayerHittingStats");
                                                    $current_data = mysqli_fetch_assoc($current_player_info);

                                                    /* Build the appropriate hitting array and save it in master */
                                                    $hitting_array = build_hitting_array($value, $current_data, $player_id);

                                                    /* Build the base hitting array for game data */
                                                    $game_hitting = build_base_hitting($value, $player_id);
                                                    $hitting_stats[$hitter] = $game_hitting;
                                                    add_player_hitting_stats($current_data, $hitting_array, $player_id);
                                                }

                                                /* Go through each pitcher added and update their information */
                                                $pitching_stats = array();
                                                foreach($pitchers as $pitcher => $value) {
                                                    $player_id = get_player_id($_SESSION['team_id'], $pitcher);

                                                    /* Get the stats for the current player, then get the data associated */
                                                    $current_player_info = get_player_stat_info($player_id, "PlayerPitchingStats");
                                                    $current_data = mysqli_fetch_assoc($current_player_info);

                                                    /* Build the appropriate pitching array to updata player pitching */
                                                    $pitching_array = build_pitching_array($value, $current_data, $player_id);

                                                    /* Build the base pitching array for game data */
                                                    $game_pitching = build_base_pitching($value, $player_id);
                                                    $pitching_stats[$pitcher] = $game_pitching;
                                                    add_player_pitching_stats($current_data, $pitching_array, $player_id);
                                                }

                                                /* Serialize the stats so that we can add them to the database */
                                                $serialized_hitting = serialize_player_stats($hitting_stats);
                                                $serialized_pitching = serialize_player_stats($pitching_stats);

                                                /* Connect to database and add the game entered to database */
                                                $basic_game_info['HittingStats'] = $serialized_hitting;
                                                $basic_game_info['PitchingStats'] = $serialized_pitching;
                                                add_game_num($_SESSION['team_id'], $_POST['GameNum'], $basic_game_info);
                                                
                                                /* Add win results to team */
                                                add_game_winners($_SESSION['team_id'], $_POST['Win_Loss']);

                                                /* Add the stats we just got to hitting and pitching tables */
                                                add_team_hitting_stats($_SESSION['team_id'], $hitting_stats);
                                                add_team_pitching_stats($_SESSION['team_id'], $pitching_stats);

                                                /* Get the session variable for the previous page, 
                                                   unset the session variable, then go to that prior page */
                                                $previous_page = $_SESSION['previous_page'];
                                                unset($_SESSION['previous_page']);
                                                echo "<script>window.location.href = '".$previous_page."';</script>";
                                            } else
                                                echo "<label class='error_message'>Nine players must be entered in batting data and one player for pitching to complete form.</label>";
                                        } else if(!$checked_game) {
                                            echo "<label class='error_message'>The game information is a duplicate game entered.</label>";
                                        }
                                        
                                    /* Otherwise, print error messages to screen */
                                    } else
                                        echo "<label class='error_message'>The date entered for game date must be for the current team year.</label>";
                                    insert_form_data("adding_form", $_POST);
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Add script sources to footer -->
                <footer>
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>          
                    <script type="application/javascript" src="scripts/team_js.js"></script>
                    <script type="application/javascript">
                        $('#cancel_button').on('click', function() {
                            console.log("button click");
                            /* If cancel button is pressed, see if the team page is set; if not, go to main page */
                            <?php
                                if(isset($_SESSION['team_page'])) $reference = $_SESSION['team_page'];
                                else $reference = $_SESSION['main_page'];
                            ?>
                                               
                            /* Change the state of the form and return back */
                            if(window.history.replaceState)
                                window.history.replaceState(null, null, '<?php echo $reference; ?>');
                            window.location.href = '<?php echo $reference; ?>';
                        });
                    </script>
                </footer>
            </body>
        </html>
<?php
    /* If the query string is playerAdd and the team page has been visited, take user to that form */
    } else if($type_add === "playerAdd" && isset($_SESSION['team_id']) && $_SESSION['team_id']) {
        include "scripts/common_funcs.php";
?>
        <!DOCTYPE html>
        <html>
            <head>
                <!-- Title of page should be unique to username -->
                <title><?php echo "Player Add for ".$_SESSION['username']; ?></title>
                <link rel="stylesheet" href="css/team_style.css">
            </head>

            <body>
                 <!-- Div to hold background image and successive content -->
                <div class="background">
                    <span class="account_opt_span">
                        <label id="account_button">Account</label>
                        <label>|</label>
                        <label id="sign_out_button">Sign Out</label>
                    </span>
            
                    <!-- To hold the main interaction and adding form -->
                    <div class="main_interaction_div">
                        <span class="company_symbol">StatKeeper</span>
                        <div class="adding_entity_div">
                            <!-- Header for the form -->
                            <h1>Add Player</h1>
                            <form class="adding_entity_form" id="adding_form" action="#" method="post">
                                <!-- Span for name identification -->
                                <span class="row_span">
                                    <span><label for="FirstName">First Name</label><input type="text" name="FirstName" required></span>
                                    <span><label for="LastName">Last Name</label><input type="text" name="LastName" required></span>
                                </span>
                                
                                <!-- Span for position account -->
                                <span class="row_span">
                                    <span>
                                        <label for="MainPosition">Main Position</label> 
                                        <select form="adding_form" name="MainPosition">
                                            <option>P</option>
                                            <option>C</option>
                                            <option>1B</option>
                                            <option>2B</option>
                                            <option>3B</option>
                                            <option>SS</option>
                                            <option>LF</option>
                                            <option>CF</option>
                                            <option>RF</option>
                                        </select>
                                    </span>
                                    <span>
                                        <label for="SecondaryPosition">Secondary Position</label> 
                                        <select form="adding_form" name="SecondaryPosition">
                                            <option>P</option>
                                            <option>C</option>
                                            <option>1B</option>
                                            <option>2B</option>
                                            <option>3B</option>
                                            <option>SS</option>
                                            <option>LF</option>
                                            <option>CF</option>
                                            <option>RF</option>
                                        </select>
                                    </span>
                                </span>
                                
                                <!-- Span for birth date and jersey number -->
                                <span class="row_span">
                                    <span><label for="JerseyNumber">Jersey Number</label><input name="JerseyNumber" type="number" min="1" max="99" value=1 required></span>
                                    <span><label for="BirthDate">Birthday</label><input name="BirthDate" type="date" placeholder="yyyy-mm-dd"></span>
                                </span>
                                
                                <!-- Span for physical attributes -->
                                <span class="row_span">
                                    <span><label for="Weight">Weight</label><input name="Weight" type="text"></span>
                                    <span><label for="Height">Height</label><input name="Height" type="text"></span>
                                </span>
                                
                                <!-- Span for container for buttons -->
                                <span class="add_button_container">
                                    <button class="add_button" id="cancel_button">Cancel</button>
                                    <input class="add_button" type="submit" value="Add player">
                                </span>
                            </form>
                            <?php
                                /* If post is set, that means submit button was pressed --> 
                                Send team add info to controller and add to database */
                                if($_POST) {
                                    /* Check if birthdate and height are valid, if they are entered */
                                    $valid_birth = $valid_height = true;
                                    if($_POST['BirthDate'])
                                        $valid_birth = check_birthdate($_POST['BirthDate']);
                                    if($_POST['Height'])
                                        $valid_height = check_height($_POST['Height']);
                                    
                                    $_POST['TeamID'] = intval($_SESSION['team_id']);
                                    $_POST['DisplayName'] = $_POST['FirstName']." ".$_POST['LastName'];
                                    $_POST['JerseyNumber'] = intval($_POST['JerseyNumber']);
                                    
                                    /* If the data has not been seen, create entry in database and go back */
                                    if($valid_birth && $valid_height) {
                                        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
                                        $result = add_to_database($database, "Players", $_POST);
                                        close_connection($database);
                                        
                                        /* Get the session variable for the previous page, 
                                           unset the session variable, then go to that prior page */
                                        $previous_page = $_SESSION['previous_page'];
                                        unset($_SESSION['previous_page']);
                                        echo "<script>window.location.href = '".$previous_page."';</script>";
                                        
                                    /* Otherwise, print error messages to screen */
                                    } else {
                                        if(!$valid_birth)
                                            echo '<label class="error_message">The birth date must be in yyyy-mm-dd form.</label>';
                                        if(!$valid_height)
                                            echo '<label class="error_message">The height must be similiar to the form 5"10.</label>';
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
        
                <!-- Add script sources to footer -->
                <footer>
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>        
                    <script type='application/javascript' src="scripts/team_js.js"></script>
                    <script type="application/javascript">
                        $('#cancel_button').on('click', function() {
                            /* If cancel button is pressed, see if the team page is set; if not, go to main page */
                            <?php
                                if(isset($_SESSION['team_page'])) $reference = $_SESSION['team_page'];
                                else $reference = $_SESSION['main_page'];
                            ?>
                            window.location.href = '<?php echo $reference; ?>'; 
                        });
                    </script>
                </footer>
            </body>
        </html>
<?php
    /* If we could not find any of these, go to either team page or main page */
    } else {
        if(isset($_SESSION['team_page'])) header("location: ".$_SESSION['team_page']);
        else header("location: ".$_SESSION['main_page']);
    }
?>
