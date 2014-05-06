// if the document (form) is ready, enable all cool jquery features
// needed by the item event series form
// to enhance the user experience
// with datetimepickers, typeahead and item list handling
jQuery(document).ready(function ($) {

    // disable the contact selectbox to prevent searching for contact before searching for costunit.
    $('.orb_event_contact').prop('disabled', true);

    // enables itemsearch typeahead if the selected timerange makes sense.
    var enableItemSearch = function (handler) {
        var formGroup = handler.parents(".object-date-search"); // yeah, more searches on one page!
        var eventId = formGroup.data('event-id');
        var searchfield = $(formGroup.find(".orb_event_form_inventory_search"));
        var roomSearchField = $(formGroup.find(".orb_event_form_room_search"));
        var begin = $(formGroup.find('.orb_event_form_event_begin')).val();
        var end = $(formGroup.find('.orb_event_form_event_end')).val();
        if ((begin !== undefined && begin !== "" ) && (end !== "" && end !== undefined)) {
            begin = begin.replace(' ', 'T');
            end = end.replace(' ', 'T');
            // enable inventory search
            searchfield.prop('disabled', false);
            searchfield.typeahead('destroy');
            searchfield.typeahead([{
                name: 'rent-items',
                valueKey: 'displayName',
                remote: { url: oktolab.typeahead.eventItemRemoteUrl + '/'+begin+'/'+end },
                prefetch: { url: oktolab.typeahead.eventItemPrefetchUrl + '/' + eventId + '/'+begin+'/'+end, ttl: 0 },
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
                prefetch: { url: oktolab.typeahead.eventSetPrefetchUrl + '/'+begin+'/'+end, ttl: 0 },
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
    };

    // adds a typeahead datum to the tablerow in e
    var addObjectToTable = function(e, datum) {
        var formGroup = $(e.currentTarget).parents(".object-date-search");
        var table = formGroup.find('.event-objects');
        var prototype = table.data('prototype');

        var tr = table.find('tr[data-value="' + datum.value + '"]');
        if (0 === tr.length) { //item is not in table yet. add it!
            var index    = table.data('index');
            var template = Hogan.compile(prototype);
            var tablerow = template.render($.extend(datum, {'index': index +1}));

            table.data('index', index +1);
            table.append(tablerow);
        }
    };

    var itemDatumForValue = function(e, item) {
        var typeaheadSearch = $(e.currentTarget);
        var datum;
        $.each(typeaheadSearch.data().ttView.datasets, function(datasetKey, dataset) {
           $.each(dataset.itemHash, function (itemKey, itemHash) {
              if (item === itemHash.datum.value) {
                  datum = itemHash.datum;
              }
           });
        });
        return datum;
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
       if (costunit != undefined) { // already set costunit? set the contacts for that!
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
                console.log(oldOption);
                $(contactSelectBox).find('option').each(function( i, opt ) {
                    console.log(opt.value);
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
        input.val("");
        var currentStamp = current.getFullYear();
        currentStamp = currentStamp+"-"+Oktolab.leadingZero(current.getMonth()+1);
        currentStamp = currentStamp+'-'+Oktolab.leadingZero(current.getDate().toString());
        currentStamp = currentStamp+' '+Oktolab.leadingZero(current.getHours().toString());
        currentStamp = currentStamp+':'+Oktolab.leadingZero(current.getMinutes().toString());
        // handler is the jquery object with the datetimepicker
        input.appendDtpicker({
            "firstDayOfWeek": 1,
            "futureOnly"    : true,
            "calendarMouseScroll": false,
            "closeOnSelected": true,
            "current": currentStamp,//"2014-03-27 17:30",
            "onHide": function(handler){ enableItemSearch(handler); }
        });
        enableItemSearch(input);
    });

    // enable contact selectbox depending on selected costunit
    $('.orb_event_costunit_typeahead').on('typeahead:selected', function(e, datum) {
        var formGroup = $(e.currentTarget).parents(".costunit-contact-search");
        var costunitSelectBox = $(formGroup.find('.orb_event_costunit'));
        var contactSelectBox = $(formGroup.find('.orb_event_contact'));

        // set the costunit to the hidden selectbox
        costunitSelectBox.val(datum.id);
        // clear options
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
        if ('set' == datum.type) { // todo: add setitems!
            $.each(datum.items, function(key, itemValue) {
                var itemDatum = itemDatumForValue(e, itemValue);
                addObjectToTable(e, itemDatum);
            });
        }
        $(e.currentTarget).typeahead('setQuery', '');
    });

    $('.orb_event_form_room_search').on('typeahead:selected', function(e, datum) {
        addObjectToTable(e, datum);
        $(e.currentTarget).typeahead('setQuery', '');
    });

    // enables removing of event objects
   $('.aui-oktolab-form-table').on('click', 'a.remove', function (e) {
        e.preventDefault();
        $(e.currentTarget).closest('tr').remove();
    });
});
