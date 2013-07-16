var Oktolab = {};

Oktolab.Calendar = function Calendar(container) {

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
        $wrapper.height($inventory.outerHeight(true));
        console.log(wrapperLength);

        // draw calendar background
        var $date = options['startdate'];
        $blocks = '';
        for (var i = 0; i <= (wrapperLength -300); i+= 100) {
            $blocks = $blocks + this.getBlockForDate($date);
            $date.setDate($date.getDate() + 1);
        };
        $wrapper.append($blocks);

        // draw events (for and so on ...)
        position = AJS.$('#test-element').offset();
        console.log(position);
        $wrapper.append('<div id="testevent" style="background-color: red;">aaaaaaa</div>');
        $testevent = AJS.$('#testevent');
        $testevent.offset({ top: position.top });
        $testevent.width(300);
        $testevent.height(29);

        // draw events (for and so on ...)
        position = AJS.$('#test-element2').offset();
        console.log(position);
        $wrapper.append('<div id="testevent2" style="background-color: red;">aaaaaaa</div>');
        $testevent = AJS.$('#testevent2');
        $testevent.offset({ top: position.top, left: 430 });
        $testevent.width(300);
        $testevent.height(29);
    };

    this.getBlockForDate = function(date) {
        return '<div class="calendar-date"><div class="calendar-headline"><span class="calendar-title">' + date.getDate() + '.' + (date.getMonth() + 1) + '</span><div class="calendar-timeblock">09-12</div><div class="calendar-timeblock">17-20</div></div></div>';
    };
};