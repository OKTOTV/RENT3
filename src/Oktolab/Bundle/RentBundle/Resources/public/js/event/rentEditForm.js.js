(function (window, document, $, Oktolab) {
    'use strict';

    var OktolabRentBundleEditForm = function() {
        var searchField = $('#oktolabrentbundle_event_editform_searchfield')
        var form = searchField.closest('form');
        var collectionHolder = form.find('.event-objects');
        var hiddenInputCollection = form.find('.oktolab-event-objects.hidden');
        var scannedInputCollection = form.find('.oktolab-event-scanned-objects.hidden');

        searchField.typeahead({
            name: 'rent-items',
            valueKey: 'name',
            prefetch: { url: oktolab.typeahead.itemPrefetchUrl, ttl: 60000 },
            template: [
                '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                '<p class="tt-object-name">{{name}}</p>',
                '<p class="tt-object-addon">{{barcode}}</p>'
            ].join(''),
            engine: Hogan
        });

        searchField.on('typeahead:selected', function (e, datum) {
            // check if is already here, if not add it, else get "scanned"
            if (0 === collectionHolder.find('span[data-value="' + datum.type + datum.id + '"]').length) {
                Oktolab.appendPrototypeTemplate(collectionHolder, datum);         // add table row
                Oktolab.appendPrototypeTemplate(hiddenInputCollection, datum);    // add hidden input field
            }

            searchField.typeahead('setQuery', '');
        });

        // Remove an EventObject
        collectionHolder.on('click', '.remove-object', function (event) {
            event.preventDefault();
            var value = $(this).data('value');

            $(event.target).closest('tr').remove();     // remove from collectionHolder
            hiddenInputCollection.find('div[data-object="' + value + '"]').remove(); // remove from hiddenInputCollection
            scannedInputCollection.find('div[data-object="' + value + '"]').remove(); // remove from scannedInputCollection
        });

    }

    // If Found: Initialize OktolabRentBundle EditForm
    if (0 !== $('#oktolabrentbundle_event_editform_searchfield').length) {
        OktolabRentBundleEditForm();
    }
}(window, document, jQuery, Oktolab));