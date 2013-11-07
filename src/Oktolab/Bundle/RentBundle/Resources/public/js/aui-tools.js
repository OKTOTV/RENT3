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
//        var calendar = new Oktolab.Calendar('#calendar');

        console.time('rendercalendar');
        Oktolab.Calendar.init();
        console.timeEnd('rendercalendar');
    }

    AJS.$('a.fancybox').fancybox();

    // Activate typeahead quicksearch in project

    var template = [
            '<p class="tt-object-addon">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join('');

    AJS.$('#quicksearch').typeahead([{
            name:       'rent-items',
            valueKey:   'name',
            remote:     { url: oktolab.typeahead.itemRemoteUrl },
            template:   template,
            header:     '<h3>Items</h3>',
            engine:     Hogan
        }, {
            name:       'rent-sets',
            valueKey:   'name',
            remote:     { url: oktolab.typeahead.setRemoteUrl },
            template:   template,
            header:     '<h3>Sets</h3>',
            engine:     Hogan
        }//, {
//            name:       'rent-events',
//            valueKey:   'name',
//            remote:     { url: oktolab.typeahead.eventRemoteUrl },
//            template:   template,
//            header:     '<h3>Events</h3>',
//            engine:     Hogan
//        }
            , {
                name:   'rent-costunits',
                valueKey:   'name',
                remote:     { url: oktolab.typeahead.costunitRemoteUrl },
                template:   template,
                header:     '<h3>Kostenstellen</h3>',
                engine:     Hogan
            }

    ]);

    AJS.$('#quicksearch').on('typeahead:selected', function (e, datum) {
        window.location.assign(Oktolab.baseUrl +'/'+ datum.showUrl);
    });
});
