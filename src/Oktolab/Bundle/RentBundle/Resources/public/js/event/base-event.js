jQuery(document).ready(function ($) {

    // disable enter in input fields to prevent form submit
    $('input,select').keypress(function(event) { return event.keyCode != 13; });

});