AJS.$(document).on('typeahead:initialized', function(e) {
    // typeahead CSS/AUI fix (Better solution: use raw CSS)
    AJS.$(e.target).siblings('.tt-hint').addClass('text'); // add aui-class "text" to hint-input
});

AJS.$(document).ready(function() {
    // AUI Date-Picker activation
    AJS.$('.aui-date-picker').each(function() {
        AJS.$(this).datePicker({'overrideBrowserDefault': true, 'firstDay': -1, 'languageCode': 'de'});
    });

    // Activate Oktolab.Calendar on Dashboard
    if (AJS.$('#calendar').length) {
        Oktolab.Calendar.init();
        AJS.$('#calendar-date').appendDtpicker({
                "firstDayOfWeek": 1,
                "dateOnly"      : true,
                "locale"        : "de",
                "dateFormat"    : "YYYY-MM-DD",
                "calendarMouseScroll": false,
                "closeOnSelected": true,
                "onHide": function(handler) {Oktolab.Calendar._loadCalendarStartingAtDate(new Date(AJS.$('#calendar-date').val()));}
            });
    }

    if (AJS.$('#room-calendar').length) {
        Oktolab.RoomCalendar.init();
        AJS.$('#calendar-date').appendDtpicker({
                "firstDayOfWeek": 1,
                "dateOnly"      : true,
                "locale"        : "de",
                "dateFormat"    : "YYYY-MM-DD",
                "calendarMouseScroll": false,
                "closeOnSelected": true,
                "onHide": function(handler) {Oktolab.RoomCalendar._loadCalendarStartingAtDate(new Date(AJS.$('#calendar-date').val()));}
            });
    }

    if (AJS.$('#room-day-calendar').length) {
        Oktolab.RoomDayCalendar.init();
        AJS.$('#calendar-date').appendDtpicker({
                "firstDayOfWeek": 1,
                "dateOnly"      : true,
                "locale"        : "de",
                "dateFormat"    : "YYYY-MM-DD",
                "calendarMouseScroll": false,
                "closeOnSelected": true,
                "onHide": function(handler) {Oktolab.RoomDayCalendar._loadCalendarStartingAtDate(new Date(AJS.$('#calendar-date').val()));}
            });
    }

    if (AJS.$('#inventory-day-calendar').length) {
        Oktolab.InventoryDayCalendar.init();
        AJS.$('#calendar-date').appendDtpicker({
                "firstDayOfWeek": 1,
                "dateOnly"      : true,
                "locale"        : "de",
                "dateFormat"    : "YYYY-MM-DD",
                "calendarMouseScroll": false,
                "closeOnSelected": true,
                "onHide": function(handler) {Oktolab.InventoryDayCalendar._loadCalendarStartingAtDate(new Date(AJS.$('#calendar-date').val()));}
            });
    }

    AJS.$('a.fancybox').fancybox();

    // Activate typeahead quicksearch in project

    var quicksearch_template = [
            '<p class="tt-object-addon">{{displayName}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join('');

    AJS.$('#quicksearch').typeahead([{
            name:           'quicksearch-items',
            valueKey:       'displayName',
            remote:         { url: oktolab.typeahead.itemRemoteUrl },
            template:       quicksearch_template,
            minLength:      2,
            header:         '<h3 style="color: black">Items</h3>',
            engine:         Hogan
        }, {
            name:           'quicksearch-sets',
            valueKey:       'displayName',
            remote:         { url: oktolab.typeahead.setRemoteUrl },
            template:       quicksearch_template,
            minLength:      2,
            header:         '<h3 style="color: black">Sets</h3>',
            engine:         Hogan
        }, {
            name:           'quicksearch-events',
            valueKey:       'displayName',
            remote:         { url: oktolab.typeahead.eventRemoteUrl },
            template:       quicksearch_template,
            minLength:      2,
            header:         '<h3 style="color: black">Verleihscheine</h3>',
            engine:         Hogan
        }, {
            name:           'quicksearch-costunits',
            valueKey:       'displayName',
            remote:         { url: oktolab.typeahead.costunitRemoteUrl },
            minLength:      2,
            template:       '<p class="tt-object-addon">{{displayName}}</p>',
            header:         '<h3 style="color: black">Kostenstellen</h3>',
            engine:         Hogan
        }, {
            name:           'quicksearch-rooms',
            valueKey:       'displayName',
            remote:         { url: oktolab.typeahead.roomRemoteUrl },
            template:       quicksearch_template,
            minLength:      2,
            header:         '<h3 style="color: black">Räume</h3>',
            engine:         Hogan
        }
    ]);

    AJS.$('#quicksearch').on('typeahead:selected', function (e, datum) {
        window.location.assign(Oktolab.baseUrl +'/'+ datum.showUrl);
    });
});
