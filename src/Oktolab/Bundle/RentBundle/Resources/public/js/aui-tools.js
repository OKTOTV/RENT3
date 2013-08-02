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
    height:                 900,
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


var collectionHolder = AJS.$('.event-objects');
var $addTagLink = AJS.$('<a href="#" class="add_tag_link">Add a tag</a>');
var $newLinkLi = AJS.$('#rent-inventory-form').append($addTagLink);
/*
function addTagFormDeleteLink($tagFormLi) {
    var $removeFormA = AJS.$('<a href="#">delete this tag</a>');
    $tagFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $tagFormLi.remove();
    });
}*/

function addTagForm(collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = collectionHolder.data('prototype');

    // get the new index
    var index = collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    console.log(newForm);
    var $newFormTr = AJS.$('tbody.event-objects').append(newForm);
//    var $newFormLi = AJS.$('<tr></tr>').append(newForm);
//    $newLinkLi.after($newFormLi);

    // add a delete link to the new form
    //addTagFormDeleteLink($newFormLi);
}

AJS.$(document).ready(function() {
//    collectionHolder.find('li').each(function() {
//        addTagFormDeleteLink(AJS.$(this));
//    });

    // add the "add a tag" anchor and li to the tags ul
//    collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    collectionHolder.data('index', collectionHolder.find(':input').length);

    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addTagForm(collectionHolder, $newLinkLi);
    });
});