"use strict";

module('Oktolab Calendar');
test('1.1 Renders Calendar', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');
    calendar.render();

    equal(1, AJS.$('#qunit-calendar').length, "Calendar is rendered at #qunit-calendar");
    equal(1, AJS.$('#qunit-calendar > .calendar-inventory').length);
    equal(1, AJS.$('#qunit-calendar > .calendar-wrapper').length);
});

test('1.2 Renders Inventory', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');

    calendar.renderBasicCalendar();
    calendar.renderInventory();

    equal(1, AJS.$('#qunit-calendar > .calendar-inventory').length, "Exactly one inventory is rendered");
});

test('1.3 Renders Calendar Background (Date-Blocks)', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');
    calendar.render();

    ok(1 <= AJS.$('.calendar-wrapper > .calendar-date').length, "Renders the dates");
});
