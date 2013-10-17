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
                '<p class="tt-object-addon" data-barcode="{{barcode}}">{{barcode}}</p>'
            ].join(''),
            engine: Hogan
        });

        var addObject = function (object) {
            if (0 === collectionHolder.find('span[data-value="' + object.type + object.id + '"]').length) {
                Oktolab.appendPrototypeTemplate(collectionHolder, object);         // add table row
                Oktolab.appendPrototypeTemplate(hiddenInputCollection, object);    // add hidden input field
            }
        };

        searchField.on('typeahead:selected', function (e, datum) {
            // check if is already here, if not add it, else get "scanned"
            addObject(datum);
            searchField.typeahead('setQuery', '');
        });

        searchField.on('keypress', function (e) {
            if (e.which === 13 || e.which === 0) {
                console.log('Enter/Tab was pressed');

                // TODO: Not save to do this.
                jQuery.each(searchField.data().ttView.datasets[0].itemHash, function (key, value) {
                    if (searchField.val() === value.datum.barcode) {
                        addObject(value.datum);
                        searchField.typeahead('setQuery', '');
                    }
                });

                e.preventDefault();
            }
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