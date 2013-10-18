(function (window, document, $, Oktolab) {
    'use strict';

    var OktolabRentBundleEditForm = function() {
        var searchField = $('#oktolabrentbundle_event_editform_searchfield');
        var form = searchField.closest('form');
        var collectionHolder = form.find('.event-objects');
        var hiddenInputCollection = form.find('.oktolab-event-objects.hidden');

        /**
         * Adds the (scanned) Object to Template
         *
         * @param {typeahead-datum|object} object
         */
        var addObject = function (object) {
            if (0 === collectionHolder.find('span[data-value="' + object.type + object.id + '"]').length) {
                Oktolab.appendPrototypeTemplate(collectionHolder, object);         // add table row
                Oktolab.appendPrototypeTemplate(hiddenInputCollection, object);    // add hidden input field
            }

            if ('set' === object.type) {
                $.each(object.items, function (key, itemValue) {
                    var item = findItem(itemValue);
                    Oktolab.appendPrototypeTemplate(collectionHolder, item);
                    Oktolab.appendPrototypeTemplate(hiddenInputCollection, item);
                });
            }
        };

        var removeObject = function (object) {
            var value = object.data('value');

            // remove from collectionHolder
            object.closest('tr').remove();
        };

        /**
         * Finds the item from the rent-items Dataset of searchField
         *
         * @param {string} itemValue
         *
         * @return {object} Typeahead-Datum
         */
        var findItem = function (itemValue) {
            var itemDatums = searchField.data().ttView.datasets[0].itemHash;
            var datum;

            $.each(itemDatums, function (key, object) {
                if (itemValue === object.datum.value) {
                    datum = object.datum;
                }
            });

            return datum;
        };

        /**
         * Finds suggested Datum.
         *
         * @return {typeahead-datum|object}
         */
        var findSuggestion = function () {
            var searchValue = searchField.val();
            var datum;

            $.each(searchField.data().ttView.datasets, function (datasetKey, dataset) {
                $.each(dataset.itemHash, function (itemKey, itemHash) {
                    if (searchValue === itemHash.datum.barcode) {
                        datum = itemHash.datum;
                    }
                });
            });

            return datum;
        };

        var template = [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join('');

        searchField.typeahead([{
            name:       'rent-items',
            valueKey:   'name',
            prefetch:   { url: oktolab.typeahead.itemPrefetchUrl, ttl: 60000 },
            template:   template,
            header:     '<h3>Items</h3>',
            engine:     Hogan
        }, {
            name:       'rent-sets',
            valueKey:   'name',
            prefetch:   { url: oktolab.typeahead.setPrefetchUrl, ttl: 60000 },
            template:   template,
            header:     '<h3>Sets</h3>',
            engine:     Hogan
        }]);

        searchField.on('typeahead:selected', function (e, datum) {
            // check if is already here, if not add it, else get "scanned"
            addObject(datum);
            searchField.typeahead('setQuery', '');
        });

        searchField.keydown(function (e) {
            var keyCode = e.which || e.keyCode;

            if (keyCode === 13 ||                       // ENTER
                keyCode === 9 ||                        // TAB
                (keyCode === 74 && e.ctrlKey == true)   // LF (Barcode Scanner)
            ) {
                e.preventDefault();
            }
        });

        searchField.keyup(function(e) {
            var keyCode = e.which || e.keyCode;

            // block keys: *, /, -, +, ... from numpad (barcode scanner ...)
            if ((keyCode >= 106 && keyCode <= 111) || keyCode === 16 || keyCode === 17 || keyCode === 18) {
                e.preventDefault();
                return;
            }

            if ((keyCode === 13 && e.ctrlKey == true) || (keyCode === 74 && e.ctrlKey == true) || keyCode === 9) {
                e.preventDefault();

                addObject(findSuggestion());
                searchField.typeahead('setQuery', '');
                searchField.focus();
            }
        });

        collectionHolder.on('click', 'a.remove', function (e) {
            e.preventDefault();
            removeObject($(e.currentTarget));
        });
    };

    // If Found: Initialize OktolabRentBundle EditForm
    if (0 !== $('#oktolabrentbundle_event_editform_searchfield').length) {
        OktolabRentBundleEditForm();
    }

}(window, document, jQuery, Oktolab));