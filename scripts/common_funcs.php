<?php
    /**
     *  Function to place all form data back into form
     *
     *  - $form_id: The id of the form that submitted
     *  - $post_data: The data that was submitted with form
     */
    function insert_form_data($form_id, $post_data) {
        
        $form_data = json_encode($post_data);
        echo "<script>\n".
                 "var form_data = JSON.parse('".$form_data."');\n".
                 "var form_element = document.getElementById('".$form_id."');\n".
                 "var form_comp;\n".
                 "for(form_comp in form_data) {\n".
                    "var element = form_element.elements[form_comp];\n".
                    "if(element) element.value = form_data[form_comp];\n".
                 "}\n".
             "</script>\n";
    }
    
    /**
     *  Function to connect to a specific database
     *
     *      - host: str: Database server to connect to
     *      - username: str: The username logging in
     *      - password: str: The password associated with user name
     *      - database: str: The database connecting to
     *
     *  Return the database handle connected to or null if unable to
     */
    function database_connect($host, $username, $password, $database) {
        if(($sql_database = mysqli_connect($host, $username, $password, $database)) == false)
            return null;
        return $sql_database;
    }

    /**
     *  Close the connection to the database providided by connection handle
     */
    function close_connection($connection_handle) {
        $connection_handle->close();
    }

    /**
     *  Function to add a user into database
     *
     *      - database: database_handle: The database to add to
     *      - table: str: The table to place the data in
     *      - fields: array: The fields and corresponding values to add to database
     *
     *  Return True if added, False if not
     */       
    function add_to_database($database, $table, $fields) {
        /* Iterate through each element in fields, making key and value string */
        if(sizeof($fields) > 0) {
            /* Get the keys and values in array form each */
            $key_str = implode(', ', array_keys($fields));
            $values = array_values($fields);
            
            /* Go through each value and determine if to add puncuation to request */
            $counter = 1;
            $last_element = sizeof($fields);
            $value_str = '';
            foreach($values as $value) {
                /* If value is string, add ticks around value */
                if(gettype($value) === "string")
                    $value_str .= ($value) ? "'" . $value . "'" : "NULL";
                
                /* If value is integer, simply add value */
                else if(gettype($value) === "integer" || 
                        gettype($value === "float"))
                    $value_str .= ($value) ? $value : 0;
            
                /* Append comma if we are not the last element and increase counter */
                if($counter != $last_element) {
                    $value_str .= ", ";
                    $counter += 1;
                }
            }
            
            /* Create query based on $table and fields entered and query on it*/
            $query = "INSERT INTO " . $table . " (" . $key_str . ") VALUES " . "(" . $value_str . ")";
            $result = $database->query($query);
            return $result;
        }
        return false;
    }

    /**
     *  Function to remove entry from database
     *
     *  - database: database_handle: The database to remove from
     *  - table: str: The table to remove from
     *  - entries: array: The entries to remove from table
     *
     *  Return true if deleted, false if not
     */
    function remove_from_database($database, $table, $keyword, $entries) {
        /* If the user entered an array we can query on */
        if($entries) {
            
            /* Go through each element in entries and form the delete elements */
            $counter = 1;
            $last_element = sizeof($entries);
            $value_str = '';
            foreach($entries as $key => $value) {
                if($counter != $last_element) {
                    $value_str .= $key." = '".$value."' ". $keyword. " ";
                } else {
                    $value_str .= $key." = '".$value."'";
                }
                $counter += 1;
            }
            
            /* Build the query and run it */
            $query = "DELETE FROM ".$table." WHERE ".$value_str;
            $result = $database->query($query);
            return $result;
        }
        return false;
    }

    /**
     *  Function to determine if the user has already been created
     *
     *  Return true if so, false if not
     */
    function is_user_created($username, $password) {
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $query = "SELECT Username, Password, Email FROM Users";
        $results = $database->query($query);
        
        /* Loop through the results and see if we see username/email and password */
        while($row = mysqli_fetch_assoc($results)) {
            if(($row['Username'] == $username && $row['Password'] == $password) ||
               ($row['Email'] == $username && $row['Password'] == $password))
                return true;
        }
        return false;
    }

    /**
     *  Function to check if birthdate is valid
     *
     *  - birthdate: str: The birthdate entered
     *
     *  Return True if valid, false if not
     */
    function check_birthdate($birthdate) {
        /* First check to see if the form submitted is correct */
        preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $birthdate, $match);
        if(!$match)
            return false;
        
        $broken_date = explode("-", $birthdate);
        
        /* Check if there are three breaking points in string */
        if(sizeof($broken_date) > 3 || sizeof($broken_date) < 3)
            return false;
        
        /* See if the first component (the year is too big or too small) */
        if(intval($broken_date[0]) < 1995 || intval($broken_date[0]) > 2020)
            return false;
        
        /* See if the second component (the month is too big or too small) */
        if(intval($broken_date[1]) < 1 || intval($broken_date[1]) > 12)
            return false;
        
        /* See if the third component (the day is too big or too small) */
        if(intval($broken_date[2]) < 1 || intval($broken_date[2]) > 31)
            return false;
    
        /* Go through each component and determine if it meets credentials */
        foreach($broken_date as $date) {
            /* Determine if the component is an integer */
            if(!is_numeric($date))
                return false;
        }
        
        /* If tests go well, return true */
        return true;
    }

    /**
     *  Function to check the height value to make sure it is correct
     *
     *  Return true if valid, false if not
     */
    function check_height($height_entered) {
        $split_height = explode('"', $height_entered);
        
        /* See if there are two components to split height */
        if(sizeof($split_height) < 2 || sizeof($split_height) > 2)
            return false;
            
        /* Determine if footage is greater than 8 or less than 4 */
        if(intval($split_height[0]) < 4 || intval($split_height[0]) > 8)
            return false;
            
        /* Determine if inches is greater than 11 */
        if(intval($split_height[1]) > 11)
            return false;
        
        /* If tests are good return true */
        return true;
    }

    /**
     *  Create a user in database with inputted criteria
     *
     *  Return true if complete
     */
    function create_database_user($username, $password, $email, $phone_number, $carrier) {
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $added = add_to_database($database, 
                                 "Users", 
                                 array(
                                    "Username" => mysqli_real_escape_string($database, $username),
                                    "Password" => mysqli_real_escape_string($database, $password),
                                    "Email" => mysqli_real_escape_string($database, $email),
                                    "PhoneNumber" => mysqli_real_escape_string($database, $phone_number),
                                    "Carrier" => mysqli_real_escape_string($database, $carrier),
                                    "Privileges" => mysqli_real_escape_string($database, "root")
                                 ));
        close_connection($database);
        return $added;
    }

    /**
     *  Function to verify that the signup form entered is correct
     *
     *  - username: str: The username entered
     *  - password: str: The password entered
     *  - retyped_pass: str: The retyped password
     *  - email: str: The email entered
     *  - phone_number: str: The phone number entered
     *  - carrier: str: The carrier that the user has
     *
     *  Returns the errors found 
     */
    function check_signup_form($username, $password, $retyped_pass, $email, $phone_number, $carrier) {
        $errors = array();
        /* Determine if username exists already */
        if(check_user_exists($username, $email, $phone_number))
            array_push($errors, "The username and/or email address and/or phone number entered already exists in database.");
        
        /* Determine if username is valid */
        if(!check_username($username))
            array_push($errors, "The username entered does not contain a digit and/or is not 5 characters long.");
        
        /* Determine if password is valid */
        if(!check_password($password))
            array_push($errors, "The password enter must contain the following: 8 characters, 1 lowercase letter, 1 uppercase letter, and 1 digit");
        
        /* Determine if password and retyped password are same */
        if(!compare_entered_passwords($password, $retyped_pass))
            array_push($errors, "The passwords entered do not match each other.");
        
        /* Determine if email is valid */
        if(!check_email($email))
            array_push($errors, "A valid email address must be entered.");
        
        /* Determine if phone number is valid and not empty */
        if(!check_phone($phone_number))
            array_push($errors, "The phone number entered must only digits and must be 10 digits");
        return $errors;
    }

    /* Check in the database if the inputted username and email and phone number exists */
    function check_user_exists($username, $email, $phone_num) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query and determine if username and email and phone number already exists */
        $query = "SELECT * FROM Users WHERE Username='".$username."' OR Email='".$email."' OR PhoneNumber='".$phone_num."'";
        $results = $database->query($query);
        close_connection($database);
        if($results->num_rows) 
            return true;
        return false;
    }

    /**
     *  Funtion to verify username is valid
     *
     *  Return True if so, False if not
     */
    function check_username($username) {
        /* If the username is filled out and the length is greater than 5, continue checking */
        if($username && strlen($username) >= 5) {
            for($i = 0; $i < strlen($username); $i++) {
                if(is_numeric($username[$i])) 
                    return true;
            }
            return false;
        }
        return false;
    }

    /**
     *  Funtion to verify password is valid
     *
     *  Return True if so, False if not
     */
    function check_password($password) {
        /* If the password is filled out and the length is greater than 8, continue checking */
        if($password && strlen($password) >= 8) {
            $uppercase = $lowercase = $digit = false;
            for($i = 0; $i < strlen($password); $i++) {
                if(is_numeric($password[$i])) 
                    $digit = true;
                if($password[$i] === strtoupper($password[$i]))
                    $uppercase = true;
                if($password[$i] === strtolower($password[$i]))
                    $lowercase = true;
            }
            /* If user inputted number, uppercase letter, and lowercase letter, return true */
            if($digit && $uppercase && $lowercase)
                return true;
            return false;
        }
        return false;
    }

    /**
     *  Funtion to verify password is same for both
     *
     *  Return True if so, False if not
     */
    function compare_entered_passwords($password, $confirmed_pass) {
        if($password === $confirmed_pass)
            return true;
        return false;
    }

    /**
     *  Funtion to verify email is valid
     *
     *  Return True if so, False if not
     */
    function check_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     *  Funtion to verify phone number is valid
     *
     *  Return True if so, False if not
     */
    function check_phone($phone_num) {
        return (strlen($phone_num) == 10 and ctype_digit($phone_num));
    }


/*********************************************************

        Functions pertaining to getting specific 
        entities from the database

***********************************************************/

    /**
     *  Function to turn array of player stats into serialized string
     *
     *  - player_data: array: All data to serialize
     *
     *  Return the serialized data
     */
    function serialize_player_stats($player_data) {
        $serialized_data = "";
        
        /* Go through each player that we have */
        
        foreach($player_data as $player_name => $player) {
            $serialized_data .= "DisplayName=".$player_name.",";
            
            $counter = 1;
            $last_data = sizeof($player);
            
            /* Now go through each data for player and add it appropriately to data */
            foreach($player as $key => $value) {
                
                /* Check if we have player id value--> dont add that to serialized array */
                if($key === "PlayerID") {}
                else {
                    /* If the counter we are on is not last, append comma to end */
                    if($counter != $last_data)    
                        $serialized_data .= $key."=".$value.",";

                    /* If the counter we are on is the last, append semicolon to separate entities */
                    else
                        $serialized_data .= $key."=".$value.";";
                }
                $counter += 1;
            }
        }
        
        return $serialized_data;
    }

    /**
     *  Function to unserialize the data
     *
     *  - serialized_data: str: The data serialized in database
     *
     *  Return an array of unserialized data
     */
    function unserialize_player_stats($serialized_data) {
        $unserialized_data = array();
        
        $player_data = explode(";", $serialized_data);
        foreach($player_data as $player) {
            if($player) {
                $current_player_name = '';
                $player_entities = explode(",", $player);
                
                $data = array();
                foreach($player_entities as $entity) {
                    $keys = explode("=", $entity);
                    $key = $keys[0];
                    $value = $keys[1];
                    if($key === "DisplayName")
                        $current_player_name = $value;
                    else
                        $data[$key] = $value;
                }
                $unserialized_data[$current_player_name] = $data;
            }
        }
        return $unserialized_data;
    }

    function add_game_winners($team_id, $win_loss) {
        /* Get the count for wins and losses by team initially */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $query = "SELECT GamesWon, GamesLost, GamesPlayed FROM Teams WHERE TeamID=".$team_id;
        $result = $database->query($query);
        
        /* Determine if we won or lost that game and increment counter */
        $win = $loss = 0;
        if($win_loss === "Win")
            $win = 1;
        if($win_loss === "Loss")
            $loss = 1;
        $played = 1;
        
        /* Get the current wins and losses and increase correspondingly */
        $row = mysqli_fetch_assoc($result);
        $games_won = $row['GamesWon'] + $win;
        $games_lost = $row['GamesLost'] + $loss;
        $games = $row['GamesPlayed'] + $played;
        
        /* Update team with win info */
        $query = "UPDATE Teams SET GamesWon=".$games_won.", GamesLost=".$games_lost.", GamesPlayed=".$games." WHERE TeamID=".$team_id;
        $database->query($query);
        close_connection($database);
    }

    /**
     *  Function to add the specific game to database
     *
     *  - teamId: Team to add game to
     *  - gameNum: The number to give game on season
     *  - $basic_game_info: The info associated with the game
     *
     *  Return Nothing
     */
    function add_game_num($teamId, $gameNum, $basic_game_info) {
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $results = add_to_database($database, "Games", $basic_game_info);
        close_connection($database);
                                        
        /* If we get results from adding to database, get the id of the entity added
           and use it to adjust other games ahead of it */
        if($results) {
            $current_game_id = get_game_id($basic_game_info);
            adjust_other_gamenums($teamId, $gameNum, $current_game_id);
        }
    }

    /**
     *  Function to build an array for a hitter that has no data
     */
    function build_base_hitting($adding_fields, $player_id) {
        /* Get computed values for hitting */
        $outs = intval($adding_fields['Strikeouts']) + intval($adding_fields['Flyouts']) + intval($adding_fields['Groundouts']);
        $hits = intval($adding_fields['Singles']) + intval($adding_fields['Doubles']) + intval($adding_fields['Triples']) + intval($adding_fields['Homers']);
        $at_bats = $hits + $outs;
        $plate_appearances = $at_bats + intval($adding_fields['Walks']) + intval($adding_fields['Sacrifices']);
        
        /* Create an associate array containing all datat to import into play stats table */
        $updated_fields = array(
            "PlayerID" => $player_id,
            "PlateAppearances" => $plate_appearances,
            "AtBats" => $at_bats,
            "Runs" => intval($adding_fields['Runs']),
            "Hits" => $hits,
            "Doubles" => intval($adding_fields['Doubles']),
            "Triples" => intval($adding_fields['Triples']),
            "Homers" => intval($adding_fields['Homers']),
            "RBIs" => intval($adding_fields['RBIs']),
            "StolenBases" => intval($adding_fields['StolenBases']),
            "Walks" => intval($adding_fields['Walks']),
            "Strikeouts" => intval($adding_fields['Strikeouts']),
            "Flyouts" => intval($adding_fields['Flyouts']),
            "Groundouts" => intval($adding_fields['Groundouts']),
            "Sacrifices" => intval($adding_fields['Sacrifices']),
            "Avg" => round(floatval(calculate_batting_average($at_bats, $hits)), 3),
            "Obp" => round(floatval(calculate_onbase_percentage(
                                            $hits, 
                                            intval($adding_fields['Walks']),
                                            $plate_appearances)), 3),
            "Slg" => round(floatval(calculate_slugging_percentage($hits, 
                                            intval($adding_fields['Doubles']), 
                                            intval($adding_fields['Triples']),
                                            intval($adding_fields['Homers']),
                                            $at_bats)), 3),
            "Ops" => round(floatval(calculate_ops(
                                        floatval(
                                            calculate_onbase_percentage(
                                                $hits, 
                                                intval($adding_fields['Walks']),
                                                $plate_appearances)),
                                        floatval(calculate_slugging_percentage(
                                                $hits, 
                                                intval($adding_fields['Doubles']), 
                                                intval($adding_fields['Triples']),
                                                intval($adding_fields['Homers']),
                                                $at_bats)))), 3)
        );
        return $updated_fields;
    }

    /**
     *  Function to add to hitting data array
     */
    function build_installed_hitting($current_data, $adding_fields) {
        /* Get computed values for hitting */
        $outs = intval($adding_fields['Strikeouts']) + intval($adding_fields['Flyouts']) + intval($adding_fields['Groundouts']);
        $hits = intval($adding_fields['Singles']) + intval($adding_fields['Doubles']) + intval($adding_fields['Triples']) + intval($adding_fields['Homers']);
        $at_bats = $hits + $outs;
        $plate_appearances = $at_bats + intval($adding_fields['Walks']) + intval($adding_fields['Sacrifices']);
        $hits = intval($adding_fields['Singles']) + intval($adding_fields['Doubles']) + intval($adding_fields['Triples']) + intval($adding_fields['Homers']);
        
        /* If we have current data, add that data to the data we are trying to add */
        $updated_fields = array(
            "PlateAppearances" => intval($current_data['PlateAppearances']) + $plate_appearances,
            "AtBats" => intval($current_data['AtBats']) + $at_bats,
            "Runs" => intval($current_data['Runs']) + intval($adding_fields['Runs']),
            "Hits" => intval($current_data['Hits']) + $hits,
            "Doubles" => intval($current_data['Doubles']) + intval($adding_fields['Doubles']),
            "Triples" => intval($current_data['Triples']) + intval($adding_fields['Triples']),
            "Homers" => intval($current_data['Homers']) + intval($adding_fields['Homers']),
            "RBIs" => intval($current_data['RBIs']) + intval($adding_fields['RBIs']), 
            "StolenBases" => intval($current_data['StolenBases']) + intval($adding_fields['StolenBases']),
            "Walks" => intval($current_data['Walks']) + intval($adding_fields['Walks']),
            "Strikeouts" => intval($current_data['Strikeouts']) + intval($adding_fields['Strikeouts']),
            "Flyouts" => intval($current_data['Flyouts']) + intval($adding_fields['Flyouts']),
            "Groundouts" => intval($current_data['Groundouts']) + intval($adding_fields['Groundouts']),
            "Sacrifices" => intval($current_data['Sacrifices']) + intval($adding_fields['Sacrifices'])
        );

        /* Calculate the advanced statistics and update to fields */
        $updated_fields['Avg'] = round(floatval(calculate_batting_average($updated_fields['AtBats'], $updated_fields['Hits'])), 3);
        $updated_fields['Obp'] = round(floatval(calculate_onbase_percentage($updated_fields['Hits'], 
                                                                      $updated_fields['Walks'],
                                                                      $updated_fields['PlateAppearances'])), 3);

        /* Get the number of singles by taking the total number of hits and subtract all other hits */
        $up_hits = $updated_fields['Hits'] - $updated_fields['Doubles'] - $updated_fields['Triples'] - $updated_fields['Homers'];
        $updated_fields['Slg'] = round(floatval(calculate_slugging_percentage(
                                                                        $up_hits, 
                                                                        $updated_fields['Doubles'], 
                                                                        $updated_fields['Triples'],
                                                                        $updated_fields['Homers'],
                                                                        $updated_fields['AtBats'])), 3);
        $updated_fields['Ops'] = round(floatval(calculate_ops($updated_fields['Obp'], $updated_fields['Slg'])), 3);
        return $updated_fields;
    }

    /**
     *  Function to build an array for a pitcher that has no data
     */
    function build_base_pitching($adding_fields, $player_id) {
        /* Set the number of games started and win/loss parameters */
        $games_started = ($adding_fields['GamesStarted'] === "Yes") ? 1 : 0;
        $wins = $losses = 0;
        if($adding_fields['Wins'] === "Yes")
            $wins = 1;
        else if($adding_fields['Wins'] === "No")
            $losses = 1;
        
        /* Create an associate array containing all datat to import into play stats table */
        $updated_fields = array(
            "PlayerID" => $player_id,
            "GamesPitched" => 1,
            "GamesStarted" => $games_started,
            "InningsPitched" => floatval($adding_fields['InningsPitched']),
            "Hits" => intval($adding_fields['Hits']),
            "Runs" => intval($adding_fields['Runs']),
            "EarnedRuns" => intval($adding_fields['EarnedRuns']),
            "Walks" => intval($adding_fields['Walks']),
            "Strikeouts" => intval($adding_fields['Strikeouts']),
            "Wins" => $wins,
            "Losses" => $losses,
            "DoublePlays" => intval($adding_fields['DoublePlays']),
            "Pitches" => intval($adding_fields['Pitches']),
            "ERA" => round(floatval(calculate_era(
                                    7,
                                    $adding_fields['InningsPitched'],
                                    $adding_fields['EarnedRuns'])), 3),
            "K_per_BB" => round(floatval(calculate_k_per_walks(
                                    $adding_fields['Walks'],
                                    $adding_fields['Strikeouts'])), 3),
            "P_per_IP" => round(floatval(calculate_pitch_per_ip(
                                    $adding_fields['Pitches'],
                                    $adding_fields['InningsPitched'])), 3),
            "K_per_7" => round(floatval(calculate_k_per_seven(
                                    $adding_fields['Strikeouts'],
                                    $adding_fields['InningsPitched'])), 3),
            "P_per_G" => round(floatval(calculate_pitches_per_game(
                                    $adding_fields['Pitches'], 1)), 3) 
        );
        return $updated_fields;
    }

    /**
     *  Function to add to pitching data array
     */
    function build_installed_pitching($current_data, $adding_fields) {
        /* Set the number of games started and win/loss parameters */
        $games_started = ($adding_fields['GamesStarted'] === "Yes") ? 1 : 0;
        $wins = $losses = 0;
        if($adding_fields['Wins'] === "Yes")
            $wins = 1;
        else if($adding_fields['Wins'] === "No")
            $losses = 1;
        
        /* If we have current data, add that data to the data we are trying to add */
        $updated_fields = array(
            "GamesPitched" => intval($current_data['GamesPitched']) + 1,
            "GamesStarted" => intval($current_data['GamesStarted']) + $games_started,
            "InningsPitched" => floatval($current_data['InningsPitched']) + floatval($adding_fields['InningsPitched']),
            "Hits" => intval($current_data['Hits']) + intval($adding_fields['Hits']),
            "Runs" => intval($current_data['Runs']) + intval($adding_fields['Runs']),
            "EarnedRuns" => intval($current_data['EarnedRuns']) + intval($adding_fields['EarnedRuns']),
            "Walks" => intval($current_data['Walks']) + intval($adding_fields['Walks']),
            "Strikeouts" => intval($current_data['Strikeouts']) + intval($adding_fields['Strikeouts']),
            "Wins" => intval($current_data['Wins']) + $wins,
            "Losses" => intval($current_data['Losses']) + $losses,
            "DoublePlays" => intval($current_data['DoublePlays']) + intval($adding_fields['DoublePlays']),
            "Pitches" => intval($current_data['Pitches']) + intval($adding_fields['Pitches'])
        );

        /* Calculate the advanced statistics and update to fields */
        $updated_fields["ERA"] = round(floatval(calculate_era(
                                                    7,
                                                    $updated_fields['InningsPitched'],
                                                    $updated_fields['EarnedRuns'])), 3);
        $updated_fields['K_per_BB'] = round(floatval(calculate_k_per_walks(
                                                    $updated_fields['Walks'],
                                                    $updated_fields['Strikeouts'])), 3);
        $updated_fields['P_per_IP'] = round(floatval(calculate_pitch_per_ip(
                                                    $updated_fields['Pitches'],
                                                    $updated_fields['InningsPitched'])), 3);
        $updated_fields['K_per_7'] = round(floatval(calculate_k_per_seven(
                                                    $updated_fields['Strikeouts'],
                                                    $updated_fields['InningsPitched'])), 3);
        $updated_fields['P_per_G'] = round(floatval(calculate_pitches_per_game(
                                                    $updated_fields['Pitches'],
                                                    $updated_fields['GamesPitched'])), 3);
        return $updated_fields;
    }

    /**
     *  Function to build the hitting array to place in database
     *
     *  Return the array
     */
    function build_hitting_array($adding_fields, $current_data, $player_id) {
        if($current_data)
            $updated_fields = build_installed_hitting($current_data, $adding_fields);
        else
            $updated_fields = build_base_hitting($adding_fields, $player_id);
        
        /* If we have updated field set return it */
        if($updated_fields)
            return $updated_fields;
        return NULL;
    }

    /**
     *  Function to build the pitching array to place in database
     *
     *  Return the array
     */
    function build_pitching_array($adding_fields, $current_data, $player_id) {
        if($current_data)
            $updated_fields = build_installed_pitching($current_data, $adding_fields);
        else
            $updated_fields = build_base_pitching($adding_fields, $player_id);
            
        /* If we have updated field set return it */
        if($updated_fields)
            return $updated_fields;
        return NULL;
    }

    /**
     *  Function to update and add player stats
     *
     *  - hitting_array: array: Associative array of all data for entity
     *  - player_id: int: The player id to add to
     *
     *  Return True if successful
     */
    function add_player_hitting_stats($current_data, $hitting_array, $player_id) {
                
        /* Get the built hitting array to inject into database */
        if($current_data) {
            /* Go through each element in array and create a string to update */
            $built_string = '';
            $counter = 1;
            $last_element = sizeof($hitting_array);
            foreach($hitting_array as $key => $val) {
                if($counter != $last_element)
                    $built_string .= $key."=".$val.", ";
                else
                    $built_string .= $key."=".$val;
                $counter += 1;
            }
        
            /* Connect to database and perform request */
            $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
            $query = "UPDATE PlayerHittingStats SET ".$built_string." WHERE PlayerID=".$player_id;
            $result = $database->query($query);
            close_connection($database);
        
        } else {
            /* A database entry does not currently exist, so create one */
            $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
            $result = add_to_database($database, "PlayerHittingStats", $hitting_array);
            
        }
        
        return $result;
    }

    /**
     *  Function to update and add player stats
     *
     *  - built_pitching: array: Associative array of all data for entity
     *  - player_id: int: The player id to add to
     *
     *  Return True if successful
     */
    function add_player_pitching_stats($current_data, $built_pitching, $player_id) {
        if($current_data) {
            /* Go through each element in array and create a string to update */
            $built_string = '';
            $counter = 1;
            $last_element = sizeof($built_pitching);
            foreach($built_pitching as $key => $val) {
                if($counter != $last_element)
                    $built_string .= $key."=".$val.", ";
                else
                    $built_string .= $key."=".$val;
                $counter += 1;
            }
            
            /* Connect to database and perform request */
            $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
            $query = "UPDATE PlayerPitchingStats SET ".$built_string." WHERE PlayerID=".$player_id;
            $result = $database->query($query);
            close_connection($database);
            
        } else {
            /* A database entry does not currently exist, so create one */
            $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
            $result = add_to_database($database, "PlayerPitchingStats", $built_pitching);
        }
        
        return $result;
    }

    /* Function to update the team hitting stats for particular team */
    function add_team_hitting_stats($team_id, $game_data) {
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Get the current team stats */
        $query = "SELECT * FROM TeamHittingStats WHERE TeamID=".$team_id;
        $results = $database->query($query);
        $row = mysqli_fetch_assoc($results);
        $initial_team_data = array(
            "PlateAppearances" => $row['PlateAppearances'],
            "AtBats" => $row['AtBats'],
            "Runs" => $row['Runs'],
            "Hits" => $row['Hits'],
            "Doubles" => $row['Doubles'],
            "Triples" => $row['Triples'],
            "Homers" => $row['Homers'],
            "RBIs" => $row['RBIs'],
            "StolenBases" => $row['StolenBases'],
            "Walks" => $row['Walks'],
            "Strikeouts" => $row['Strikeouts'],
            "Flyouts" => $row['Flyouts'],
            "Groundouts" => $row['Groundouts'],
            "Sacrifices" => $row['Sacrifices']
        );
        
        /* Go through each player and add their stats to the existing stats */
        foreach($game_data as $player => $data) {
            $initial_team_data['PlateAppearances'] += $data['PlateAppearances'];
            $initial_team_data['AtBats'] += $data['AtBats'];
            $initial_team_data['Runs'] += $data['Runs'];
            $initial_team_data['Hits'] += $data['Hits'];
            $initial_team_data['Doubles'] += $data['Doubles'];
            $initial_team_data['Triples'] += $data['Triples'];
            $initial_team_data['Homers'] += $data['Homers'];
            $initial_team_data['RBIs'] += $data['RBIs'];
            $initial_team_data['StolenBases'] += $data['StolenBases'];
            $initial_team_data['Walks'] += $data['Walks'];
            $initial_team_data['Strikeouts'] += $data['Strikeouts'];
            $initial_team_data['Flyouts'] += $data['Flyouts'];
            $initial_team_data['Groundouts'] += $data['Groundouts'];
            $initial_team_data['Sacrifices'] += $data['Sacrifices'];
        }
        
        /* Calculate new advanced statistics */
        $initial_team_data['Avg'] = round(calculate_batting_average(
                                                $initial_team_data['AtBats'],
                                                $initial_team_data['Hits']), 3);
        $initial_team_data['Obp'] = round(calculate_onbase_percentage(
                                                $initial_team_data['Hits'],
                                                $initial_team_data['Walks'],
                                                $initial_team_data['PlateAppearances']), 3);
        
        $singles = $initial_team_data['Hits'] - $initial_team_data['Doubles'] - $initial_team_data['Triples'] - $initial_team_data['Homers'];
        $initial_team_data['Slg'] = round(calculate_slugging_percentage(
                                                $singles,
                                                $initial_team_data['Doubles'],
                                                $initial_team_data['Triples'],
                                                $initial_team_data['Homers'],
                                                $initial_team_data['AtBats']), 3);
        $initial_team_data['Ops'] = round(calculate_ops(
                                                $initial_team_data['Obp'],
                                                $initial_team_data['Slg']), 3);

        
        /* Build a string of all new parameters to update the team with */
        $built_string = '';
        $counter = 1;
        $last_element = sizeof($initial_team_data);
        foreach($initial_team_data as $key => $val) {
            if($counter != $last_element)
                $built_string .= $key."=".$val.", ";
            else
                $built_string .= $key."=".$val;
            $counter += 1;
        }

        /* Build the query and run it */
        $query = "UPDATE TeamHittingStats SET ".$built_string." WHERE TeamID=".$team_id;
        $result = $database->query($query);
        close_connection($database);  
    }

    /* Function to update the team pitching stats */
    function add_team_pitching_stats($team_id, $game_data) {
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Get the current team stats */
        $query = "SELECT * FROM TeamPitchingStats WHERE TeamID=".$team_id;
        $results = $database->query($query);
        $row = mysqli_fetch_assoc($results);
        $initial_team_data = array(
            "GamesPitched" => $row['GamesPitched'],
            "GamesStarted" => $row['GamesStarted'],
            "InningsPitched" => $row['InningsPitched'],
            "Hits" => $row['Hits'],
            "Runs" => $row['Runs'],
            "EarnedRuns" => $row['EarnedRuns'],
            "Walks" => $row['Walks'],
            "Strikeouts" => $row['Strikeouts'],
            "Wins" => $row['Wins'],
            "Losses" => $row['Losses'],
            "DoublePlays" => $row['DoublePlays'],
            "Pitches" => $row['Pitches']
        );
        
        /* Go through each player and add their stats to the existing stats */
        foreach($game_data as $player => $data) {
            $initial_team_data['GamesPitched'] += $data['GamesPitched'];
            $initial_team_data['GamesStarted'] += $data['GamesStarted'];
            $initial_team_data['InningsPitched'] += $data['InningsPitched'];
            $initial_team_data['Hits'] += $data['Hits'];
            $initial_team_data['Runs'] += $data['Runs'];
            $initial_team_data['EarnedRuns'] += $data['EarnedRuns'];
            $initial_team_data['Walks'] += $data['Walks'];
            $initial_team_data['Strikeouts'] += $data['Strikeouts'];
            $initial_team_data['Wins'] += $data['Wins'];
            $initial_team_data['Walks'] += $data['Walks'];
            $initial_team_data['Losses'] += $data['Losses'];
            $initial_team_data['DoublePlays'] += $data['DoublePlays'];
            $initial_team_data['Pitches'] += $data['Pitches'];
        }
        
        /* Calculate new advanced statistics */
        $initial_team_data['ERA'] = round(calculate_era(
                                                7,
                                                $initial_team_data['InningsPitched'],
                                                $initial_team_data['EarnedRuns']), 3);
        $initial_team_data['K_per_BB'] = round(calculate_k_per_walks(
                                                $initial_team_data['Walks'],
                                                $initial_team_data['Strikeouts']), 3);
        $initial_team_data['P_per_IP'] = round(calculate_pitch_per_ip(
                                                $initial_team_data['Pitches'],
                                                $initial_team_data['InningsPitched']), 3);
        $initial_team_data['K_per_7'] = round(calculate_k_per_seven(
                                                $initial_team_data['Strikeouts'],
                                                $initial_team_data['InningsPitched']), 3);
        $initial_team_data['P_per_G'] = round(calculate_pitches_per_game(
                                                $initial_team_data['Pitches'],
                                                $initial_team_data['GamesPitched']), 3);

        /* Build a string of all new parameters to update the team with */
        $built_string = '';
        $counter = 1;
        $last_element = sizeof($initial_team_data);
        foreach($initial_team_data as $key => $val) {
            if($counter != $last_element)
                $built_string .= $key."=".$val.", ";
            else
                $built_string .= $key."=".$val;
            $counter += 1;
        }

        /* Build the query and run it */
        $query = "UPDATE TeamPitchingStats SET ".$built_string." WHERE TeamID=".$team_id;
        $result = $database->query($query);
        close_connection($database);  
    }

    /**
     *  Function to get the player id of an associated player
     *
     *  Return the id
     */
    function get_player_id($teamId, $player_name) {
        /* Connect to database and see if we have results for the player name under specific team */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $query = "SELECT PlayerID FROM Players WHERE TeamID=".$teamId." AND DisplayName='".$player_name."'";
        $results = $database->query($query);
        close_connection($database);
        
        /* See if we have results --> if so, get the specific player id */
        if($results && $results->num_rows) {
            $row = mysqli_fetch_assoc($results);
            return $row['PlayerID'];
        }
        
        /* Return Null if not found */
        return NULL;
    }
    
    /**
     *  Function to get the current stats of a player
     *
     *  Return all the current stats
     */
    function get_player_stat_info($playerID, $table) {
        /* Connect to database and see if we have results for the player name under specific team */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $query = "SELECT * FROM ".$table." WHERE PlayerID=".$playerID;
        $results = $database->query($query);
        close_connection($database);
        
        return $results;
    }

    /**
     *  Function to lower game nums after game is deleted
     *
     *  - teamId: int: The team to look at
     *  - old_gamenum: int: The old gamenum that just got deleted
     *
     *  Return nothing
     */
    function adjust_gamenums_after_removal($teamId, $old_gamenum) {
        
        /* Connect to database and get all games in database greater than old game num */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $query = "SELECT GameID, GameNum FROM Games WHERE TeamID=".$teamId." AND GameNum > ".$old_gamenum;
        $results = $database->query($query);
        
        /* Go through each result and decrease game num by 1 */
        while($row = mysqli_fetch_assoc($results)) {
            $updated_game_num = $row['GameNum'] - 1;
            $query = "UPDATE Games SET GameNum=".$updated_game_num." WHERE GameID=".$row['GameID'];
            $database->query($query);
        }
        
        /* Close the connection */
        close_connection($database);
    }

    /**
     *  Function to get data from post request and return separate array
     *
     *  Return the separate array
     */
    function get_basic_game_info($post_data) {
        $basic_data = array(
            "Innings" => $post_data["Innings"],
            "Win_Loss" => $post_data["Win_Loss"],
            "Home_Away" => $post_data["Home_Away"],
            "Date" => $post_data["GameDate"],
            "Location" => $post_data["Location"],
            "Against" => $post_data["Against"],
            "TeamScore" => $post_data["TeamScore"],
            "OpposingScore" => $post_data["OpposingScore"],
            "TeamID" => $post_data["TeamID"]
        );
        return $basic_data;
    }

    /**
     *  Function to check to see if the basic game info is already in table
     *
     *  Return True if not, False if so
     */
    function check_game_existing($basic_info) {
        $counter = 1;
        $last_element = sizeof($basic_info);
        $value_str = '';
        foreach($basic_info as $key => $value) {
            if($counter != $last_element) {
                $value_str .= $key." = '".$value."' AND ";
            } else {
                $value_str .= $key." = '".$value."'";
            }
            $counter += 1;
        }

        /* Build the query and run it */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $query = "SELECT * FROM Games WHERE ".$value_str;
        $result = $database->query($query);
        close_connection($database);
        
        /* Return True if there are no results currently */
        if($result && $result->num_rows)
            return false;
        return true;
    }

    /**
     *  Function to get data from post request and return separate array for hitting data
     *
     *  Return the separate array
     */
    function get_game_player_hitting($post_data, $player_number) {
        $player_hitting = array(
            "Singles" => $post_data['Singles'.$player_number],
            "Doubles" => $post_data['Doubles'.$player_number],
            "Triples" => $post_data['Triples'.$player_number],
            "Homers" => $post_data['Homers'.$player_number],
            "RBIs" => $post_data['RBIs'.$player_number],
            "Walks" => $post_data['Walks'.$player_number],
            "Strikeouts" => $post_data['Strikeouts'.$player_number],
            "Flyouts" => $post_data['Flyouts'.$player_number],
            "Groundouts" => $post_data['Groundouts'.$player_number],
            "Runs" => $post_data['Runs'.$player_number],
            "StolenBases" => $post_data['StolenBases'.$player_number],
            "Sacrifices" => $post_data['Sacrifices'.$player_number]
        );
        
        return $player_hitting;
    }

    /**
     *  Function to get data from post request and return separate array for pitching data
     *
     *  Return the separate array
     */
    function get_game_player_pitching($post_data, $player_number) {
        $player_pitching = array(
            "GamesStarted" => $post_data['GamesStarted'.$player_number],
            "InningsPitched" => $post_data['InningsPitched'.$player_number],
            "Wins" => $post_data['Wins'.$player_number],
            "Hits" => $post_data['PHits'.$player_number],
            "Runs" => $post_data['PRuns'.$player_number],
            "EarnedRuns" => $post_data['EarnedRuns'.$player_number],
            "Walks" => $post_data['PWalks'.$player_number],
            "Strikeouts" => $post_data['PStrikeouts'.$player_number],
            "DoublePlays" => $post_data['DoublePlays'.$player_number],
            "Pitches" => $post_data['Pitches'.$player_number]
        );
        
        return $player_pitching;
    }
    
    /**
     *  Function to make sure data entered by user is at least valid
     *
     *  - $info_retrieved: array: The info that the user put in
     *  - $player_num: int: The number player entered
     *
     *  Return error if one, Success if none
     */
    function validate_hitting($info_retrieved, $player_num) {
        return "Success";
    }

    /**
     *  Function to make sure data entered by user is at least valid
     *
     *  - $info_retrieved: array: The info that the user put in
     *  - $player_num: int: The number player entered
     *
     *  Return error if one, Success if none
     */
    function validate_pitching($info_retrieved, $player_num) {
        if(floatval($info_retrieved['InningsPitched']) * 3 < intval($info_retrieved['Strikeouts']))
            return "The number of strikeouts entered cannot exceed innings pitched times 3.";
        if(intval($info_retrieved['DoublePlays']) > intval($info_retrieved['InningsPitched']))
            return "You cannot have more double plays than innings pitched.";
        if(intval($info_retrieved['Runs']) < intval($info_retrieved['EarnedRuns']))
            return "There cannot be more earned runs than runs in a game.";
        if(strpos($info_retrieved['InningsPitched'], '.') !== false) {
            $decimal = explode(".", $info_retrieved['InningsPitched']);
            if($decimal[1] && ($decimal[1] !== "0" && $decimal[1] !== "33" && $decimal[1] !== "66"))
                return "Innings pitched must have 0, 33, or 66 after decimal";
        }
        return "Success";
    }

    /**
     *  Function to get all game information from game id
     *
     *  Return the information
     */
    function get_game_info($game_id) {
        /* Create a connection to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query for all the game nums there Ordered by date */
        $query = "SELECT * FROM Games WHERE GameID=".$game_id." ORDER BY GameNum";
        $results = $database->query($query);
        close_connection($database);
        
        /* See if we have results and return them */
        if($results && $results->num_rows)
            return $results;
        return NULL;
    }

    /**
     *  Function to get the game id of the data entered
     *
     *  Return the id of the game
     */
    function get_game_id($game_info) {
        if($game_info) {
            /* Go through each element in entries and form the delete elements */
            $counter = 1;
            $last_element = sizeof($game_info);
            $value_str = '';
            foreach($game_info as $key => $value) {
                if($counter != $last_element) {
                    $value_str .= $key." = '".$value."' AND ";
                } else {
                    $value_str .= $key." = '".$value."'";
                }
                $counter += 1;
            }

            /* Build the query and run it */
            $query = "SELECT GameID FROM Games WHERE ".$value_str;
            $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
            $result = $database->query($query);
            close_connection($database);
            
            /* If we only have one row, return the id of that row -->
               otherwise return null */
            if($result && $result->num_rows && $result->num_rows == 1) 
                return mysqli_fetch_assoc($result)['GameID'];
            else
                return NULL;
        }
    }

    /**
     *  Function to directly adjust the game numbers of game
     *  numbers greater than current one
     *
     *  - teamId: int: The id of the team
     *  - current_game_num: int: The current game number being added
     *
     *  Return None
     */
    function adjust_other_gamenums($teamId, $current_game_num, $current_game_id) {
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Run query to get all entries with game number greater than current one and not against same team */
        $query = "SELECT GameID, GameNum FROM Games WHERE TeamID=".$teamId.
                " AND GameNum >= ".$current_game_num." AND GameID != ".$current_game_id." ORDER BY Date";
        $results = $database->query($query);
        
        /* Go through and update the current game num of each consecutive game */
        while($row = mysqli_fetch_assoc($results)) {
            $updated_game_num = $row['GameNum'] + 1;
            $query = "UPDATE Games SET GameNum=".$updated_game_num." WHERE GameID=".$row['GameID'];
            $database->query($query); 
        }
        close_connection($database);
    }

    /**
     *  Function to get the first open game num for user
     *
     *  - teamId: The team id of the team
     *
     *  Return the first open game num
     */
    function get_first_open_game_num($teamId) {
        /* Create a connection to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query for all the game nums there Ordered by date */
        $query = "SELECT GameNum FROM Games WHERE TeamID=".$teamId." ORDER BY GameNum";
        $results = $database->query($query);
        close_connection($database);
        
        /* Go through each result and determine what game num should be returned */
        $counter = 1;
        while($row = mysqli_fetch_assoc($results)) {
            if($counter != $row['GameNum'])
                return $counter;
            $counter += 1;
        }
        
        return $counter;
    }

    /**
     *  Function to get what game number a team should be based on date
     *
     *  - team_id: int: The id of the team
     *  - date_entered: date: The date entered by user
     *
     *  Return the game number
     */
    function get_game_num_from_date($teamId, $date_entered) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query for all the game nums there Ordered by date */
        $query = "SELECT GameNum, Date FROM Games WHERE TeamID=".$teamId." ORDER BY Date";
        $results = $database->query($query);
        close_connection($database);
        
        /* Set initial, default properties to return to user */
        $game_num = get_first_open_game_num($teamId);
        
        /* Go through each game and determine if database entry is greater than date entered
           adjust the game number that new game entry should go to */
        while($row = mysqli_fetch_assoc($results)) {
            if($row['Date'] > $date_entered) {
                $game_num = $row['GameNum'];
                break;
            }
        }
        return $game_num;
    }

    /**
     *  Function to check if the game entered is valid year
     *
     *  - ent_date: str: The date entered
     *  - team_year: int: The year of the team
     *
     *  Return True if valid, False if not
     */
    function check_valid_game_date($ent_date, $team_year) {
        /* First check to see if the form submitted is correct */
        preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $ent_date, $match);
        if(!$match)
            return false;
        
        $broken_date = explode("-", $ent_date);
        
        /* Check if there are three breaking points in string */
        if(sizeof($broken_date) > 3 || sizeof($broken_date) < 3)
            return false;
        
        /* See if the first component (the year is too big or too small) */
        if(intval($broken_date[0]) != $team_year)
            return false;
        
        /* See if the second component (the month is too big or too small) */
        if(intval($broken_date[1]) < 1 || intval($broken_date[1]) > 12)
            return false;
        
        /* See if the third component (the day is too big or too small) */
        if(intval($broken_date[2]) < 1 || intval($broken_date[2]) > 31)
            return false;
    
        /* Go through each component and determine if it meets credentials */
        foreach($broken_date as $date) {
            /* Determine if the component is an integer */
            if(!is_numeric($date))
                return false;
        }
        
        /* If tests go well, return true */
        return true;
    }

    /**
     *  Function to get the first open gamenum to display
     *
     *  Return the open game num
     */
    function get_first_open_gamenum($teamId) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query for all the game nums there */
        $query = "SELECT GameNum FROM Games WHERE TeamID=".$teamId." ORDER BY GameNum";
        $results = $database->query($query);
        close_connection($database);
        
        /* Find the first number that is open */
        $counter = 1;
        while($row = mysqli_fetch_assoc($results)) {
            if($row['GameNum'] != $counter)
                return $counter;
            $counter = $counter + 1;
        }
        
        /* If no missing numbers, return the next one */
        return $counter;
    }

    /**
     *  Function to get the teams a user has based on their userId
     *
     *  - userId: int: The userId to analyze
     *  
     *  Return the teams a user has
     */
    function get_user_teams($userId) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query and determine if username and email and phone number already exists */
        $query = "SELECT * FROM Teams WHERE UserID=".$userId;
        $results = $database->query($query);
        close_connection($database);
        return $results;
    }

    /**
     *  Function to get the hitting stats of a team
     *
     *  Return the results of the query
     */
    function get_team_hitting_stats($team_id) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query and determine if username and email and phone number already exists */
        $query = "SELECT * FROM TeamHittingStats WHERE TeamID=".$team_id;
        $results = $database->query($query);
        close_connection($database);
        return $results;
    }

    /**
     *  Function to get the pitching stats of a team
     *
     *  Return the results of the query
     */
    function get_team_pitching_stats($team_id) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query and determine if username and email and phone number already exists */
        $query = "SELECT * FROM TeamPitchingStats WHERE TeamID=".$team_id;
        $results = $database->query($query);
        close_connection($database);
        return $results;
    }

    /**
     *  Function to return the team Id of a team
     *
     *  - userId: int: The user id for the team
     *  - teamName: str: The name of the team
     *  - year: int: the year of the team
     *
     *  Return the team id --> if not found, return NULL
     */
    function get_team_id($userId, $teamName, $year) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query for all players in team */
        $query = "SELECT TeamID FROM Teams WHERE UserID=".$userId." AND TeamName='".$teamName."' AND Year=".$year;
        $results = $database->query($query);
        close_connection($database);
        try {    
            $row = mysqli_fetch_assoc($results);
            return $row['TeamID'];
        } catch(Exception $e) {
            return NULL;
        }
    }

    /**
     *  Function to get all games from current team
     *
     *  - teamId: int: The id of the team
     *  
     *  Return the games of the team
     */
    function get_team_games($teamId) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query for all players in team */
        $query = "SELECT * FROM Games WHERE TeamID=".$teamId." ORDER BY Date";
        $results = $database->query($query);
        close_connection($database);
        return $results;
    }

    /**
     *  Function get all the players belonging to a specific team
     *
     *  - teamId: int: The team id of the team
     *
     *  Return the players for a specific team
     */
    function get_team_players($teamId) {
        /* Connect to database */
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        
        /* Query for all players in team */
        $query = "SELECT * FROM Players WHERE TeamID=".$teamId." ORDER BY DisplayName";
        $results = $database->query($query);
        close_connection($database);
        return $results;
    }

    /**
     *  Function to session variables for all user info
     */
    function get_user_login_variables($username) {
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        /* Username may be either actual username or email */
        $query = "SELECT UserID, Username, Email, PhoneNumber, Carrier, Privileges FROM Users WHERE Username='".$username."' OR Email='".$username."'";
        $results = $database->query($query);
        if($results->num_rows) {
            $row = mysqli_fetch_assoc($results);
            $_SESSION['userID'] = $row['UserID'];
            $_SESSION['username'] = $row['Username'];
            $_SESSION['email'] = $row['Email'];
            $_SESSION['phone'] = $row['PhoneNumber'];
            $_SESSION['carrier'] = $row['Carrier'];
            $_SESSION['privileges'] = $row['Privileges'];
        }
    }

/*********************************************************

        Functions pertaining to phone and recovery

***********************************************************/

    /**
     *  Function to convert phone number and carrier into proper email
     *
     *  Return the full email address
     */
    function convert_phone_to_email($phone_num, $carrier) {
        $extension = "";
        if($carrier === "ATT")
            $extension = "@txt.att.net";
        else if($carrier === "Verizon")
            $extension = "@vtext.com";
        else if($carrier === "Sprint")
            $extension = "@messaging.sprintpcs.com";
        else if($carrier === "TMobile")
            $extension = "@tmomail.net";
        return $phone_num . $extension;
    }

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    /**
     *  Function to send a recovery code to user when needed
     *
     *  Return true if message was sent
     */
    function send_recovery_message($sendTo) {
        
        /**
         *  Generate a code for recovery
         *
         *  Return the code
         */
        function generate_recovery_code() {
            $code = '';
            for($i = 0; $i < 7; $i++)
                $code .= mt_rand(0, 9);
            return $code;
        }
        
        /* Files to include to send mail */
        include "PHPMailer/src/PHPMailer.php";
        include "PHPMailer/src/Exception.php";
        include "PHPMailer/src/SMTP.php";
        
        /* Try to create a mail handler and set properties. Then try to send */
        $mail = new PHPMailer;
        try {
            /* Set basie properties of message with correct credentials */
            $mail->isSMTP(true);
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'statkeeper1@gmail.com';
            $mail->Password = 'Desoto_Legion';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            /* Set the address we are sending to */
            $mail->setFrom('statkeeper1@gmail.com', 'StatKeeper');
            $mail->addAddress($sendTo);

            /* Create the content of message by generating code - Save code for later use */
            $mail->isHTML(true);
            $mail->Subject = 'StatKeeper Password Recovery';
            $_SESSION['recovery_code'] = generate_recovery_code();
            $mail->Body = 'Your code is: '. $_SESSION['recovery_code'];

            /* Send the message and return success or failure */
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *  Function to send the password to user when needed
     *
     *  Return true if message was sent
     */
    function send_password_message($sendTo, $password) {
        /* Files to include to send mail */
        include "PHPMailer/src/PHPMailer.php";
        include "PHPMailer/src/Exception.php";
        include "PHPMailer/src/SMTP.php";
        
        /* Try to create a mail handler and set properties. Then try to send */
        $mail = new PHPMailer;
        try {
            /* Set basie properties of message with correct credentials */
            $mail->isSMTP(true);
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'statkeeper1@gmail.com';
            $mail->Password = 'Desoto_Legion';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            /* Set the address we are sending to */
            $mail->setFrom('statkeeper1@gmail.com', 'StatKeeper');
            $mail->addAddress($sendTo);

            /* Create the content of message by sending password */
            $mail->isHTML(true);
            $mail->Subject = 'StatKeeper Current Password';
            $mail->Body = 'Your password is: '.$password;

            /* Send the message and return success or failure */
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *  Lookup email and phone number and return to javascript 
     */
    function recover_user_info($username) {
        $database = database_connect("localhost", "dking", "Test_Pass", "StatKeeper");
        $query = "SELECT Email, PhoneNumber, Carrier FROM Users WHERE Username='".$username."'";
        $results = $database->query($query);
        return $results;
    }

/*****************************************************************

    Functions for calculating baseball statistics

******************************************************************/

    function calculate_batting_average($atbats, $hits) {
        if(!$atbats)
            return 0;
        return $hits / $atbats;
    }

    function calculate_onbase_percentage($hits, $walks, $plate_appearances) {
        if(!$plate_appearances)
            return 0;
        return ($hits + $walks) / $plate_appearances;
    }

    function calculate_slugging_percentage($hits, $doubles, $triples, $homers, $atbats) {
        if(!$atbats)
            return 0;
        return ($hits + $doubles + (2 * $triples) + (3 * $homers)) / $atbats;
    }

    function calculate_ops($obp, $slg) {
        return $obp + $slg;
    }

    function calculate_era($regulation_innings, $innings_pitched, $earned_runs) {
        if(!$innings_pitched)
            return 0;
        return ($earned_runs / $innings_pitched) * $regulation_innings;
    }

    function calculate_k_per_walks($walks, $ks) {
        if(!$walks)
            return 0;
        return $ks / $walks;
    }

    function calculate_pitch_per_ip($pitches, $innings_pitched) {
        if(!$innings_pitched)
            return 0;
        return $pitches / $innings_pitched;
    }

    function calculate_k_per_seven($strikeouts, $inningsPitched) {
        if(!$inningsPitched)
            return 0;
        return $strikeouts / ($inningsPitched / 7);
    }

    function calculate_pitches_per_game($pitches, $games) {
        if(!$games)
            return 0;
        return $pitches / $games;
    }
?>
