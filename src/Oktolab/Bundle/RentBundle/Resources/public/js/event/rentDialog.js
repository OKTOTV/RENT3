/*
 * Creates the Rent-Dialog in AppHeader
 */

var createRentForm = new AJS.Dialog({
    id:                     'create-rent-form-dialog',
    closeOnOutsideClick:    false,
    width:                  730,
    height:                 700,
});

createRentForm.addHeader('Neue Reservierung');
createRentForm.addPanel('Inventar', '#rent-inventory-form');
createRentForm.addPanel('R&auml;ume', '#rent-room-form');
createRentForm.addButton('Erstellen', function (dialog, page) {

    if (page.curtab == 0) {
        AJS.$('#rent-inventory-form > form').submit();
    } else if (page.curtab == 1) {
        AJS.$('#rent-room-form > form').submit();
    }
});

createRentForm.addLink('Abbrechen', function(dialog) {
    dialog.hide();
});

AJS.$("#create-button").click(function() {
    createRentForm.gotoPanel(0);
    createRentForm.show();
});