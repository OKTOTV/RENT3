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

    var quicksearch_template = [
            '<p class="tt-object-addon">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join('');

    AJS.$('#quicksearch').typeahead([{
            name:       'quicksearch-items',
            valueKey:   'name',
            remote:     { url: oktolab.typeahead.itemRemoteUrl },
            template:   quicksearch_template,
            header:     '<h3 style="color: black">Items</h3>',
            engine:     Hogan
        }, {
            name:       'quicksearch-sets',
            valueKey:   'name',
            remote:     { url: oktolab.typeahead.setRemoteUrl },
            template:   quicksearch_template,
            header:     '<h3 style="color: black">Sets</h3>',
            engine:     Hogan
        }, {
            name:       'quicksearch-events',
            valueKey:   'name',
            remote:     { url: oktolab.typeahead.eventRemoteUrl },
            template:   quicksearch_template,
            header:     '<h3 style="color: black">Verleihscheine</h3>',
            engine:     Hogan
        }, {
            name:   'quicksearch-costunits',
            valueKey:   'name',
            remote:     { url: oktolab.typeahead.costunitRemoteUrl },
            template:   quicksearch_template,
            header:     '<h3 style="color: black">Kostenstellen</h3>',
            engine:     Hogan
        }
    ]);

    AJS.$('#quicksearch').on('typeahead:selected', function (e, datum) {
        window.location.assign(Oktolab.baseUrl +'/'+ datum.showUrl);
    });
});
