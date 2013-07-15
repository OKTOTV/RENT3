AJS.$(document).ready(function() {
    AJS.$('.aui-date-picker').each(function() {
        AJS.$(this).datePicker({'overrideBrowserDefault': true, 'firstDay': -1, 'languageCode': 'de'});
    });

    AJS.$('#dashboard-calendar').cal({
        masktimelabel: { '00': 'H:i' },
        gridincrement: '30 mins',
    });
});

AJS.$(document).ready(function() {
    var calendar = new Oktolab.Calendar('#calendar');
    calendar.render();
});