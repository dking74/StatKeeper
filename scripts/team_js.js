/**
 *  Account button, in future releases, will allow user to modify account
 *  For now, alert the user that the button was pressed
 */
$('#account_button').on('click', function() {
    alert("Account button pressed.");
});

/**
 *  When sign-out button is pressed, sign the user out by clearing sessions 
 */
$('#sign_out_button').on('click', function() {
    /* Clear the data by submitting request to clear data */
    $.ajax({
        method: "POST",
        url: 'clear-data.php'
    });
    
    /* Change location of script after clearing data */
    alert("Thanks for visiting!");
    window.location.href = "login.php";
});

/**
 *  The team add button is when user wishes to add team
 *  Direct browser to page
 */
$('#team_add_button').on('click', function() {
    /* When add team is pressed, submit request to entity add page with adding team */
    window.location.href = "entity-add.php?teamAdd";
});

/**
 *  The game add button is when user wishes to add game
 *  Direct browser to page
 */
$('#game_add_button').on('click', function() {
    /* When add game is pressed, submit request to entity add page with adding game */
    window.location.href = "entity-add.php?gameAdd";
});

/**
 *  The player add button is when user wishes to add player
 *  Direct browser to page
 */
$('#player_add_button').on('click', function() {
    /* When add player is pressed, submit request to entity add page with adding player */
    window.location.href = "entity-add.php?playerAdd";
});

/**
 *  Function to delete an entity in table and from database
 * 
 *  - table_id: str: The id of the table to delete from
 *  - entity_name_data: str: The name for variable to send to server
 *  - additional_val: str: The additional query parameter to determine deletion
 *  - entity: str: The type of entity it is
 *  - item_delete: int: The row column to delete from 
 */
function delete_function(table_id, entity_name_data, additional_val, entity, item_delete, from_table=true) {

    /* Get all checkboxes that are checked on page */
    var checkboxes = document.querySelectorAll('input[type=checkbox]:checked');
    
    /* Get the table we are deleting from */
    var table = document.getElementById(table_id);
    
    /* Array to keep track of nodes to delete from DOM */
    var rows_delete = new Array();
    
    /* Iterate through all checkboxes and delete the row from screen and database */
    for(var i = 0; i < checkboxes.length; i++) {
        var item = checkboxes[i];
        
        /* Push the row to delete to array to delete later */
        rows_delete.push(document.getElementById(entity + item.value));
    }
    
    for(var i = 0; i < rows_delete.length; i++) {
        
        /* Get the element by the id of the rows to delete */
        var elem = document.getElementById(rows_delete[i].id);
        var name = elem.getAttribute('name');
        
        /* 
           Determine if second parameter is from table or inserted 
           If from table, get the child element of the table corresponding to id and delete 
        */
        var extra_val = '';
        if(from_table) {
            var item_id = elem.id;
            extra_val = document.getElementById(item_id).childNodes[item_delete].innerHTML;
            
        /* Otherwise, the extra parameter should be directly used */
        } else
            extra_val = item_delete;

        /* Make a request to entity remove to remove the entity */
        var data = {};
        data[entity_name_data] = name;
        data[additional_val] = extra_val;
        
        /* Submit request to delete entries from database and then update game nums there */
        $.ajax({
            method: "POST",
            url: "scripts/entity-remove.php",
            async: false,
            data: data,
            success: function(result) { console.log(result); },
            error: function(error) { console.log(error); }
        });
        
        /* Remove the child from the DOM */
        elem.parentNode.removeChild(elem);
    }
}