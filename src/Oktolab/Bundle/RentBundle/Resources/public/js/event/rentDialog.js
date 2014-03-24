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
createRentForm.addPanel('Inventar Serie', '#rent-series-inventory-form');
createRentForm.addPanel('Raum Serie', '#rent-series-room-form');
createRentForm.addButton('Erstellen', function (dialog, page) {

    if (page.curtab == 0) {
        AJS.$('#rent-inventory-form > form').submit();
    } else if (page.curtab == 1) {
        AJS.$('#rent-room-form > form').submit();
    } else if (page.curtab == 2) {
        AJS.$('#rent-series-inventory-form > form').submit();
    } else if (page.curtab == 3) {
        AJS.$('#rent-series-room-form > form').submit();
    }
});

createRentForm.addLink('Abbrechen', function(dialog) {
    dialog.hide();
});

AJS.$("#create-button").click(function() {
    createRentForm.gotoPanel(0);
    createRentForm.show();
});