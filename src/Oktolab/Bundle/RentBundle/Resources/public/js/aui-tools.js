AJS.$(document).ready(function() {
    AJS.$('.aui-date-picker').each(function() {
        AJS.$(this).datePicker({'overrideBrowserDefault': true, 'firstDay': -1, 'languageCode': 'de'});
    });
});

AJS.$(document).ready(function() {
    if (AJS.$('#calendar').length) {
        var calendar = new Oktolab.Calendar('#calendar');

        console.time('rendercalendar');
        calendar.render();
        console.timeEnd('rendercalendar');
    }
});


var createRentForm = new AJS.Dialog({
    id:                     'create-rent-form-dialog',
    closeOnOutsideClick:    false,
    width:                  700,
    height:                 700,
});

createRentForm.addHeader('Neue Reservierung');
createRentForm.addPanel('Inventar', '#rent-inventory-form');
createRentForm.addPanel('R&auml;ume', '#rent-room-form');
createRentForm.addButton('Reservieren', function (dialog, page) {
    console.log(dialog);
    console.log(page);
    console.log(dialog.getCurrentPanel());

    AJS.$('#rent-inventory-form > form').submit();
});

createRentForm.addLink('Abbrechen', function(dialog) {
    dialog.hide();
});

AJS.$("#create-button").click(function() {
    createRentForm.gotoPanel(0);
    createRentForm.show();
});

$('#target').submit(function() {
    alert('Handler for .submit() called.');
    return false;
});