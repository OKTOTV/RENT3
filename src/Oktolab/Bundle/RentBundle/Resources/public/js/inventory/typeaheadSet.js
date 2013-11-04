jQuery(document).ready(function ($) {
    var searchItemsField = $('#oktolab_rentbundle_inventory_set_searchItems');

    // Abort if no action is needed
    if (0 === searchItemsField.length) {
        return;
    }

    var form                  = searchItemsField.closest('form');
    var hiddenInputCollection = $('.hidden-items', form);
    var itemCollection        = $('.set-items', form);

    searchItemsField.typeahead({
        name:       'set-items',
        valueKey:   'name',
        prefetch:  { url: oktolab.typeahead.itemPrefetchUrl, ttl: 60000 },
        remote: { url: oktolab.typeahead.itemRemoteUrl},
        template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join(''),
        engine: Hogan
    });

    // Remove an EventObject
    itemCollection.on('click', '.remove-object', function (event) {
        event.preventDefault();
        var value = $(this).data('value');
        $(event.target).closest('tr').remove();     // remove from collectionHolder
        hiddenInputCollection.find('div[data-object="' + value + '"]').remove(); // remove from hiddenInputCollection
    });

    searchItemsField.on('typeahead:selected', function (e, datum) {
        console.log(hiddenInputCollection.data('prototype'));
        console.log(datum);
        Oktolab.appendPrototypeTemplate(itemCollection, datum);         // add table row
        Oktolab.appendPrototypeTemplate(hiddenInputCollection, datum);  // add hidden input field
        searchItemsField.typeahead('setQuery', '');
    });
});
