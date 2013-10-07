jQuery(document).ready(function ($) {
    var searchItemsField = $('#oktolab_rentbundle_admin_costunit_searchContacts');

    // Abort if no action is needed
    if (0 === searchItemsField.length) {
        return;
    }

    var form                  = searchItemsField.closest('form');
    var hiddenInputCollection = $('.hidden-contacts', form);
    var contactCollection        = $('.costunit-contacts', form);
    var mainContactCollection   = $('#oktolab_bundle_rentbundle_costunit_mainContact', form);

    searchItemsField.typeahead({
        name:       'costunit-contacts',
        valueKey:   'name',
        prefetch:  { url: oktolab.typeahead.contactPrefetchUrl, ttl: 60000 },
        template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{name}}</p>'
        ].join(''),
        engine: Hogan
    });

        // Remove an EventObject
    contactCollection.on('click', '.remove-object', function (event) {
        event.preventDefault();
        var value = $(this).data('value');

        $(event.target).closest('tr').remove();     // remove from collectionHolder
        hiddenInputCollection.find('input[value="' + value + '"]').remove(); // remove from hiddenInputCollection
    });

    searchItemsField.on('typeahead:selected', function (e, datum) {
        console.log(hiddenInputCollection.data('prototype'));
        console.log(datum);
        Oktolab.appendPrototypeTemplate(contactCollection, datum);         // add table row
        Oktolab.appendPrototypeTemplate(hiddenInputCollection, datum);  // add hidden input field
        searchItemsField.typeahead('setQuery', '');
    });
});
