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
        var calendar = new Oktolab.Calendar('#calendar');

        console.time('rendercalendar');
        calendar.render();
        console.timeEnd('rendercalendar');
    }
});
