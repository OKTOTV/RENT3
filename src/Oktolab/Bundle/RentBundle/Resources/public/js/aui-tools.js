AJS.$(document).ready(function() {
    AJS.$('.aui-date-picker').each(function() {
        AJS.$(this).datePicker({'overrideBrowserDefault': true, 'firstDay': -1, 'languageCode': 'de'});
    });
});

AJS.$(document).ready(function() {
    if (AJS.$('#calendar').length) {
        var calendar = new Oktolab.Calendar('#calendar');

        console.time('rendercalendar');
        calendar.render();
        console.timeEnd('rendercalendar');
    }
});