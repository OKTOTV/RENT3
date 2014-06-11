// if the document (form) is ready, enable all cool jquery features
// needed by the event form
// to enhance the user experience
// with datetimepickers, typeahead, barcode scanning and item list handling
jQuery(document).ready(function ($) {

// enable the rent button if everything is scanned
    var enableRent = function (table) {
        var allScanned = true;
        var tablerows = table.find('tr');
        $.each(tablerows, function(key, row) {
            if ($(row).find('input.scanner').val() == 0) {
                allScanned = false;
            }
        });
        if (allScanned) {
            $('.event-rent').prop('disabled', false);
        } else {
            $('.event-rent').prop('disabled', true);
        }
    };

    // makes the tick green and sets the scanner value true.
    var checkTick = function (tablerow) {
        tablerow.find('.aui-iconfont-approve').removeClass('aui-iconfont-approve').addClass('aui-icon-success');
        tablerow.find('input.scanner').val('1');
        tablerow.find('input.scanner').prop('checked', true);
    };

    // adds a typeahead datum to the tablerow in e
    var addObjectToTable = function(e, datum) {
        var formGroup;
        if (e.currentTarget == undefined) {
            formGroup = e.parents(".object-date-search");
        } else {
            formGroup = $(e.currentTarget).parents(".object-date-search");
        }
        var table = formGroup.find('.event-objects');
        var prototype = table.data('prototype');

        var tr = table.find('tr[data-value="' + datum.value + '"]');
        if (0 === tr.length) { //item is not in table yet. add it!
            var index    = table.data('index');
            var template = Hogan.compile(prototype);
            var tablerow = template.render($.extend(datum, {'index': index +1}));

            table.data('index', index +1);
            table.append(tablerow);
            enableRent(formGroup.find('table'));
        } else {
            //todo: scan the item (green)
            checkTick(tr);
        }
    };

    // disable the contact selectbox to prevent searching for contact before searching for costunit.
    $('.orb_event_contact').prop('disabled', true);

    // enables removing of event objects
    $('.aui-oktolab-form-table').on('click', 'a.remove', function (e) {
        e.preventDefault();
        $(e.currentTarget).closest('tr').remove();
        enableRent($(e.currentTarget).closest('table'));
    });

   // enable scanning of event objects
   $('.aui-oktolab-form-table').on('click', 'a.scan', function (e) {
        e.preventDefault();
        checkTick($(e.currentTarget).closest('tr')); 
        enableRent($(e.currentTarget).closest('table'));
   });


    var datumForValue = function(e, item) {
        var datum;
        $.each(e.data().ttView.datasets, function(datasetKey, dataset) {
           $.each(dataset.itemHash, function (itemKey, itemHash) {
              if (item === itemHash.datum.value) {
                  datum = itemHash.datum;
              }
           });
        });
        return datum;
    };

    //===================== Barcode Scanning ========================
    var datumForBarcode = function(input) {
        var datum;
        $.each(input.data().ttView.datasets, function (datasetKey, dataset) {
            $.each(dataset.itemHash, function (itemKey, itemHash) {
                if (input.val() === itemHash.datum.barcode) {
                    datum = itemHash.datum;
                }
            });
        });
        return datum;
   };

    $('.scan-search').each(function(index, input) {
        var input = $(input);

        // prevent "Enter" or Post of Form.
        input.keydown(function (e) {
            var keyCode = e.which || e.keyCode;

            if (keyCode === 13 ||                       // ENTER
                keyCode === 9 ||                        // TAB
                (keyCode === 74 && e.ctrlKey == true)   // LF (Barcode Scanner)
            ) {
                e.preventDefault();
            }
        });

        input.keyup(function (e) {
            var keyCode = e.which || e.keyCode;
            // block keys: *, /, -, +, ... from numpad (barcode scanner ...)
            if ((keyCode >= 106 && keyCode <= 111) || keyCode === 16 || keyCode === 17 || keyCode === 18) {
                e.preventDefault();
                return;
            }

            if ((keyCode === 13 && e.ctrlKey == true) || (keyCode === 74 && e.ctrlKey == true) || keyCode === 9) {
                e.preventDefault();
                var datum = datumForBarcode(input);
                if ('undefined' !== typeof(datum)) {
                    addObjectToTable(input, datum);
                    if ('set' == datum.type) { // add setitems!
                        $.each(datum.items, function(key, itemValue) {
                            var itemDatum = datumForValue(input, itemValue);
                            addObjectToTable(e, itemDatum);
                        });
                    }
                    input.typeahead('setQuery', '');
                }
                input.focus();
            }
        });
   });
   //==================================================================

    // enables itemsearch typeahead if the selected timerange makes sense.
    var enableItemSearch = function (handler) {
        var formGroup = handler.parents(".object-date-search"); // yeah, more searches on one page!
        var eventId = formGroup.data('event-id');
        var searchfield = $(formGroup.find(".orb_event_form_inventory_search"));
        var roomSearchField = $(formGroup.find(".orb_event_form_room_search"));
        var begin = $(formGroup.find('.orb_event_form_event_begin')).val();
        var end = $(formGroup.find('.orb_event_form_event_end')).val();
        if ((begin !== undefined && begin !== "" ) && (end !== "" && end !== undefined)) {
            if (eventId) {
                var prefetch = { url: oktolab.typeahead.eventItemPrefetchUrl + '/' + eventId + '/'+begin+'/'+end, ttl: 0 };
            } else {
                prefetch = { url: oktolab.typeahead.eventItemPrefetchUrl + '/undefined/'+begin+'/'+end, ttl: 0 };
            }
            begin = begin.replace(' ', 'T');
            end = end.replace(' ', 'T');
            // enable inventory search
            searchfield.prop('disabled', false);
            searchfield.typeahead('destroy');
            searchfield.typeahead([{
                name: 'rent-items',
                valueKey: 'displayName',
                remote: { url: oktolab.typeahead.eventItemRemoteUrl + '/'+begin+'/'+end },
                prefetch: prefetch,
                limit: 10,
                template: [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{displayName}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                header: '<h3>Gegenstände</h3>',
                engine: Hogan
            }, {
                name:       'rent-sets',
                valueKey:   'displayName',
                remote: { url: oktolab.typeahead.eventSetRemoteUrl + '/'+begin+'/'+end },
                prefetch: { url: oktolab.typeahead.eventSetPrefetchUrl + '/'+begin+'/'+end, ttl:0 },
                template: [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{displayName}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                header: '<h3>Sets</h3>',
                engine: Hogan
            }, {
                name:       'category',
                valueKey:   'displayName',
                remote: { url: oktolab.typeahead.eventItemRemoteUrl + '/'+begin+'/'+end },
                prefetch:   { url: oktolab.typeahead.eventCategoryPrefetchUrl + '/'+begin+'/'+end, ttl:0 },
                template:   [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{displayName}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                header: '<h3>Kategorie</h3>',
                engine: Hogan
            }]);
            // enable room search
            roomSearchField.prop('disabled', false);
            roomSearchField.typeahead('destroy');
            roomSearchField.typeahead([{
                name: 'rent-rooms',
                valueKey: 'displayName',
                remote: { url: oktolab.typeahead.eventRoomRemoteUrl + '/'+begin+'/'+end },
                template: [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{displayName}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                header: '<h3>Räume</h3>',
                engine: Hogan
            }]);
        } else {
            roomSearchField.prop('disabled', true);
            searchfield.prop('disabled', true);
        }
        // enable rent if objects are prescanned.
        enableRent(formGroup.find('.event-objects'));
    };

    // make a input field into a typeahead search for costunits
    $('.orb_event_costunit_typeahead').each(function(index, input) {
       var costunitInput = $(input);
       costunitInput.typeahead({
        name:       'costunits',
        valueKey:   'displayName',
        remote: { url: oktolab.typeahead.costunitRemoteUrl },
        template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{displayName}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join(''),
        engine: Hogan
       });
       var formGroup = costunitInput.parents('.costunit-contact-search');
       var costunit = formGroup.find('.orb_event_costunit').val();

       if (costunit != undefined && costunit != 0) { // already set costunit? set the contacts for that!
           var contactSelectBox = formGroup.find('.orb_event_contact');
           var oldOption = contactSelectBox.val();
            // get the contacts of the selected costunit and set them as options
            var contacturl = oktolab.jquery.contactsForCostunitUrl + costunitInput.data('id');
            $.getJSON(contacturl, function(data) {
                contactSelectBox.empty(); //clear old options
                $.each(data, function(key, contact) {
                    contactSelectBox.append($('<option />').val(contact.id).text(contact.name));
                });
                formGroup.find('.orb_event_contact').prop('disabled', false);
                $(contactSelectBox).find('option').each(function( i, opt ) {
                    if( opt.value === oldOption )
                        $(opt).attr('selected', 'selected');
                });
            });

       }
    });

    // makes all .event-datetime into datetimepickers and
    // (depends) enable the item search!
    $('.event-datetime').each(function(index, input) {
        input = $(input);
        var val = input.val();
        var current = new Date(val);
        input.val('');
        var currentStamp = current.getFullYear();
        currentStamp = currentStamp+"-"+Oktolab.leadingZero(current.getMonth()+1);
        currentStamp = currentStamp+'-'+Oktolab.leadingZero(current.getDate().toString());
        currentStamp = currentStamp+' '+Oktolab.leadingZero(current.getHours().toString());
        currentStamp = currentStamp+':'+Oktolab.leadingZero(current.getMinutes().toString());

        // handler is the jquery object with the datetimepicker
        input.appendDtpicker({
            "firstDayOfWeek": 1,
            "futureOnly"    : true,
            "locale"        : "de",
            "dateFormat"    : "YYYY-MM-DD hh:mm",
            "calendarMouseScroll": false,
            "closeOnSelected": true,
            "autodateOnStart": false,
            "current": currentStamp,//"2014-03-27 17:30",
            "onHide": function(handler){ enableItemSearch(handler); }
        });
        if (currentStamp != "NaN-NaN-NaN NaN:NaN") {
            input.val(currentStamp);
        }
        enableItemSearch(input);
    });

    // make all .datetime input fields into nice usable datetimepickers
    $('.datetime').each(function(index, input) {
        input = $(input);
        var val = input.val();
        var current = new Date(val);
        input.val('');
        var currentStamp = current.getFullYear();
        currentStamp = currentStamp+"-"+Oktolab.leadingZero(current.getMonth()+1);
        currentStamp = currentStamp+'-'+Oktolab.leadingZero(current.getDate().toString());
        currentStamp = currentStamp+' '+Oktolab.leadingZero(current.getHours().toString());
        currentStamp = currentStamp+':'+Oktolab.leadingZero(current.getMinutes().toString());

        input.appendDtpicker({
            "firstDayOfWeek": 1,
            "futureOnly"    : true,
            "locale"        : "de",
            "dateOnly"      : true,
            "dateFormat"    : "YYYY-MM-DD",
            "calendarMouseScroll": false,
            "closeOnSelected": true,
            "current":      currentStamp
        });
    });

    // enable contact selectbox depending on selected costunit
    $('.orb_event_costunit_typeahead').on('typeahead:selected', function(e, datum) {
        var formGroup = $(e.currentTarget).parents(".costunit-contact-search");
        var costunitSelectBox = $(formGroup.find('.orb_event_costunit'));
        var contactSelectBox = $(formGroup.find('.orb_event_contact'));

        // set the costunit to the hidden selectbox
        costunitSelectBox.val(datum.id);
        // clear all options
        contactSelectBox.empty();

        // get the contacts of the selected costunit and set them as options
        var contacturl = oktolab.jquery.contactsForCostunitUrl + datum.id;
        $.getJSON(contacturl, function(data) {
            $.each(data, function(key, contact) {
                contactSelectBox.append($('<option />').val(contact.id).text(contact.name));
            });
        });
        // enable the contact selectbox
        contactSelectBox.prop('disabled', false);
    });

    // add event object to tablerow next to the searchfield, so the form gets the selected items
    $('.orb_event_form_inventory_search').on('typeahead:selected', function(e, datum) {
        addObjectToTable(e, datum);
        if ('set' == datum.type) { // add setitems!
            $.each(datum.items, function(key, itemValue) {
                var itemDatum = datumForValue($(e.currentTarget), itemValue);
                addObjectToTable(e, itemDatum);
            });
        }
        $(e.currentTarget).typeahead('setQuery', '');
    });

    $('.orb_event_form_room_search').on('typeahead:selected', function(e, datum) {
        addObjectToTable(e, datum);
        $(e.currentTarget).typeahead('setQuery', '');
    });
});
