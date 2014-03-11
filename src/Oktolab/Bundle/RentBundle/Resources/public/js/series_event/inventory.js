// if the document (form) is ready, enable all cool jquery features
// needed by the item event series form
// to enhance the user experience
// with datetimepickers, typeahead and item list handling
jQuery(document).ready(function ($) {

    var createForm = $('#rent-series-inventory-form');

    // enables itemsearch typeahead if the selected timerange makes sense.
    var enableItemSearch = function () {
        var eventId = createForm.data('value');
        var begin = $('#orb_series_event_form_event_begin').val();
        var end = $('#orb_series_event_form_event_end').val();

        if (begin !== "" && end !== "") {
            begin = begin.replace('/ /g', 'T');
            end = end.replace('/ /g', 'T');
            $('#orb_series_event_form_inventory_search').prop('disabled', false);
            $('#orb_series_event_form_inventory_search').typeahead([{
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
        } else {
            $('#orb_series_event_form_inventory_search').prop('disabled', true);
        }
    };

    var addObjectToTable = function(datum) {
        var form = $('orb_series_event_form');
        var prototype = $('#orb_series_event_form_object_table').data('prototype');
        console.log(prototype);

        var tr = form.find('tr[data-value="' + datum.value + '"]');
        if (0 === tr.length) { //item is not in table yet. add it
            var template = Hogan.compile(prototype);
            var tablerow = template.render(datum);
//            console.log(tablerow);
            $('#orb_series_event_form_object_table').append(tablerow);
        }
    };

    // make a input field into a typeahead search for costunits
    $('#orb_series_costunit_typeahead').typeahead({
        name:       'costunits',
        valueKey:   'displayName',
        prefetch: { url: oktolab.typeahead.costunitPrefetchUrl },
        remote: { url: oktolab.typeahead.costunitRemoteUrl },
        template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{displayName}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join(''),
        engine: Hogan
    });

    // make all .datetime input fields into nice usable datetimepickers
    $('.datetime').appendDtpicker({
        "firstDayOfWeek": 1,
        "futureOnly"    : true,
        "calendarMouseScroll": false,
        "closeOnSelected": true
    });

    // makes all .event-datetime into datetimepickers and
    // (maybe) enable the item search!
    $('.event-datetime').appendDtpicker({
        "firstDayOfWeek": 1,
        "futureOnly"    : true,
        "calendarMouseScroll": false,
        "closeOnSelected": true,
        "onHide": function(handler){
            enableItemSearch();
            }
    });

    // enable contact selectbox depending on selected costunit
    $('#orb_series_costunit_typeahead').on('typeahead:selected', function(e, datum) {
        // set the costunit to the hidden selectbox
        $('#orb_series_event_form_costunit').val(datum.id);

        // clear options
        $('#orb_series_event_form_contact').empty();

        // get the contacts of the selected costunit and set them as options
        var contacturl = oktolab.jquery.contactsForCostunitUrl + datum.id;
        $.getJSON(contacturl, function(data) {
            var contact_selectbox = $('#orb_series_event_form_contact');
            $.each(data, function(key, contact) {
                contact_selectbox.append($('<option />').val(contact.id).text(contact.name));
            });
        });

        // enable the contact selectbox
        $('#orb_series_event_form_contact').prop('disabled', false);
    });

    // add event object to tablerow, so the form gets the selected items
    $('#orb_series_event_form_inventory_search').on('typeahead:selected', function(e, datum) {
        addObjectToTable(datum);
    });

    // clear the datetime inputs. otherwise, they will contain the "now" date.
    $('.datetime').val('');
    $('.event-datetime').val('');
    // disable the contact selectbox to prevent searching for contact before searching for costunit.
    $('#orb_series_event_form_contact').prop('disabled', true);
});
