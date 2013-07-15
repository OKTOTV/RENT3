var Oktolab = {};

Oktolab.calendar = function calendar(container) {

    options = {
        'container': container,
        'startdate': new Date(),
        'eventSrcPath': 'gone.json',
        'configSrcPath': 'gone.json' // timeblocks and inventory
    };

    this.render = function() {
        var $container = AJS.$(options['container']),
            $inventory = AJS.$(options['container'] + '> .calendar-inventory'),
            $wrapper = AJS.$(options['container'] + '> .calendar-wrapper');

        // set height for wrapper
        var wrapperLength = $container.width() - $inventory.width();
        $wrapper.height($inventory.height() + 40);

        // draw calendar background
        var $date = options['startdate'];
        for (var i = 0; i <= wrapperLength; i+= 100) {
            $wrapper.append(this.getBlockForDate($date));
            $date.setDate($date.getDate() + 1);
        };
    };

    this.getBlockForDate = function(date) {
        return '<div class="calendar-date"><div class="calendar-headline"><span class="calendar-title">' + date.getDate() + '.' + (date.getMonth() + 1) + '</span><div class="calendar-timeblock">09-12</div><div class="calendar-timeblock">17-20</div></div></div>';
    };
};