/* When login form submitted -> add hidden field to POST data */
$("#login_form").submit(function(event) {
    $(this).append("<input type='hidden' name='form' value='login_form'>");
});

/* When sign up form submitted -> add hidden field to POST data */
$("#sign_up_form").submit(function(event) {
    $(this).append("<input type='hidden' name='form' value='sign_up_form'>");
});