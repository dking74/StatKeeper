<?php
    /* Include common functions to express functionality */
    include "scripts/common_funcs.php";

    session_start();

    /* See if user session variables are set and display team info 
    Query String must also be set --> to get team data
    */
    if(isset($_SESSION['userID']) && isset($_SESSION['username']) && $_SERVER['QUERY_STRING']) {
        
        /* Temporary hack to allow players to see team data */
        if($_SESSION['userID'] == 10)
            $_SESSION['userID'] = 9;
        
        /* Set the main page session variable */
        $_SESSION['main_page'] = basename($_SERVER["REQUEST_URI"], ".php");
?>
        <!-- Deliver this html content if session variables are set -->
        <!DOCTYPE html>
        <html>
            <head>
                <!-- Title of page should be unique to username -->
                <title><?php echo "Team Page for ".$_SESSION['username']; ?></title>
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

                    <!-- To hold the main interaction and form login -->
                    <div class="main_interaction_div">
                        <span class="company_symbol">StatKeeper</span>
                        <div class="entity_viewing_div">
                            <span>
                                <?php 
                                    /* If access is basic, disable buttons so user can't use them */
                                    if($_SESSION['privileges'] === "basic") {
                                        echo '<button id="team_delete_button" class="delete_button" disabled>- Delete Team</button>';
                                        echo '<button id="team_add_button" class="add_button" disabled>+ Add Team</button>';
                                    
                                    /* Otherwise, allow edit access to user */
                                    } else{
                                        echo '<button id="team_delete_button" class="delete_button">- Delete Team</button>';
                                        echo '<button id="team_add_button" class="add_button">+ Add Team</button>';
                                    }
                                ?>
                            </span>
                            <fieldset class="fieldset_team">
                                <legend class="fieldset_legend">Teams</legend>
                                <table id="team_table" class="entity_view_table">
                                    <tr>
                                        <th style='background-color: white; border: none;'></th>
                                        <th>Team Name</th>
                                        <th>Head Coach</th>
                                        <th>Year</th>
                                        <th>Primary Color</th>
                                        <th>Secondary Color</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Num. Players</th>
                                        <th>Games Played</th>
                                        <th>Games Won</th>
                                        <th>Games Lost</th>
                                    </tr>
                                    <?php
                                        /* Get all the teams for the specific user */
                                        $teams = get_user_teams($_SESSION['userID']);
                                                                 
                                        /* 
                                        Go through each column and add data to table;
                                        counter is used to delete rows later on
                                        */
                                        $counter = 1;
                                        while($row = mysqli_fetch_assoc($teams)) {
                                            echo "<tr id='Team".$counter."' name='".$row['TeamName']."'>".
                                                    "<td style='border: none;'><input type='checkbox' name='".$row['TeamName']."' value=".$counter."></td>".
                                                    "<td><a href='team-page.php?userID=".$_SESSION['userID']."&teamName=".$row['TeamName']."&Year=".$row['Year']."'>".$row['TeamName']."</a></td>".
                                                    "<td>".$row['HeadCoach']."</td>".
                                                    "<td>".$row['Year']."</td>".
                                                    "<td>".$row['PrimaryColor']."</td>".
                                                    "<td>".$row['SecondaryColor']."</td>".
                                                    "<td>".$row['City']."</td>".
                                                    "<td>".$row['State']."</td>".
                                                    "<td>".$row['NumPlayers']."</td>".
                                                    "<td>".$row['GamesPlayed']."</td>".
                                                    "<td>".$row['GamesWon']."</td>".
                                                    "<td>".$row['GamesLost']."</td>".
                                                "</tr>";
                                            $counter += 1;
                                        }
                                    ?>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <!-- Add script sources to footer -->
                <footer>
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>          
                    <script type='application/javascript' src="scripts/team_js.js"></script>
                    <script type="application/javascript">
                        /* Register click event for delete team button */
                        $('#team_delete_button').on('click', function() { 
                            delete_function('team_table', 'TeamName', 'Year', 'Team', 3);
                        });
                    </script>
                </footer>
            </body>
        </html>
        <!-- End of html content to deliver -->
<?php
    /* Redirect to login if session variables are not set */
    } else {
        header("location: login.php");
    }
?>
