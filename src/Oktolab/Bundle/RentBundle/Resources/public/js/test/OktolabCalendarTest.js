"use strict";

module('Oktolab Calendar Rendering');
test('Renders Calendar', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');
    calendar.render();

    equal(1, AJS.$('#qunit-calendar').length, "Calendar is rendered at #qunit-calendar");
    equal(1, AJS.$('#qunit-calendar > .calendar-inventory').length);
    equal(1, AJS.$('#qunit-calendar > .calendar-wrapper').length);
});

test('Renders Inventory', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');

    calendar.renderBasicCalendar();
    calendar.renderInventory();

    equal(1, AJS.$('#qunit-calendar > .calendar-inventory').length, "Exactly one inventory is rendered");
});

test('Renders Calendar Background (Date-Blocks)', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');
    calendar.render();

    ok(1 <= AJS.$('.calendar-wrapper > .calendar-date').length, "Renders the dates");
});

test('Renders Calendar Events', function () {
    // count events von json-api und rendered events
});

module('Oktolab Calendar JSON');
test('getInventory() returns object', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');
    var $inventory = calendar.getInventory();

    ok(typeof $inventory === "object");
});

/*test('getEvents() returns object', function () {
    var calendar = new Oktolab.Calendar('#qunit-calendar');
    var $events = calendar.getEvents();


});*/