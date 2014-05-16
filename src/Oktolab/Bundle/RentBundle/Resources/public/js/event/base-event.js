jQuery(document).ready(function ($) {

    // disable enter in input fields to prevent form submit
    $('input,select').keypress(function(event) { return event.keyCode != 13; });

    // disable the contact selectbox to prevent searching for contact before searching for costunit.
    $('.orb_event_contact').prop('disabled', true);

    // enables removing of event objects
    $('.aui-oktolab-form-table').on('click', 'a.remove', function (e) {
        e.preventDefault();
        $(e.currentTarget).closest('tr').remove();
    });

});