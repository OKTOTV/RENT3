"use strict";

module('Oktolab Calendar');
test('1.1 Renders calendar', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');
    calendar.render();

    equal(1, AJS.$('#qunit-calendar').length, "Calendar is rendered at #test-calendar");
});

test('1.2 Renders inventory', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');

    calendar.renderBasicCalendar();
    calendar.renderInventory();

    equal(1, AJS.$('#qunit-calendar > .calendar-inventory').length, "Exactly one inventory is rendered");
});

