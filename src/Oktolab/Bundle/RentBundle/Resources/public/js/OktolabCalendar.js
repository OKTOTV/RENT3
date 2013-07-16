var Oktolab = {};

Oktolab.Calendar = function Calendar(container) {
    'use strict';

    this.options = {
        'container': container,
        'startdate': new Date(),
        'eventSrcPath': 'gone.json',
        'configSrcPath': 'gone.json' // timeblocks and inventory
    };

    this.data = {
        container: AJS.$(this.options.container),
    };

    /**
     * renders the calendar
     * @returns {undefined}
     */
    this.render = function () {
        this.renderBasicCalendar();
        this.renderInventory();
        this.renderCalendarBackground();

        //this.renderEvents();

        /*/ draw events (for and so on ...)
        var position = AJS.$('#test-element').offset();
        $wrapper.append('<div id="testevent" style="background-color: red;">aaaaaaa</div>');
        $testevent = AJS.$('#testevent');
        $testevent.offset({ top: position.top });
        $testevent.width(300);
        $testevent.height(29);

        // draw events (for and so on ...)
        var position = AJS.$('#test-element2').offset();
        $wrapper.append('<div id="testevent2" style="background-color: red;">aaaaaaa</div>');
        $testevent = AJS.$('#testevent2');
        $testevent.offset({ top: position.top, left: 430 });
        $testevent.width(300);
        $testevent.height(29);*/
    };

    /**
     * Returns the HTML needed by rendering the background calendar
     *
     * @param {Date} date
     * @returns {String}
     */
    this.getBlockForDate = function (date) {
        return '<div class="calendar-date"><div class="calendar-headline"><span class="calendar-title">' + date.getDate() + '.' + (date.getMonth() + 1) + '</span><div class="calendar-timeblock">09-12</div><div class="calendar-timeblock">17-20</div></div></div>';
    };

    /**
     * Returns the Inventory as HTML
     *
     * @returns {String}
     */
    this.getInventory = function () {
        var $json       = AJS.$.parseJSON('{"stuff":["item","item","item"],"some more stuff":["item bar","item foo","item baz"]}'),
            $key        = null, // will be used to iterate itemgroups
            $item       = null, // will be used to iterate items
            $inventory  = '';

        // iterate through inventory information and build html
        for ($key in $json) {
            $inventory = $inventory +
                    '<div class="calendar-inventory-group">' +
                        '<strong>' + $key + '</strong>' +
                        '<ul>';

            for ($item in $json[$key]) {
                $inventory = $inventory + '<li><a href="#">' + $json[$key][$item] + '</a></li>';
            }
            $inventory = $inventory + '</ul>' + '</div>';
        }

        return $inventory;
    };

    /**
     * renders the full inventory
     *
     * @returns {undefined}
     */
    this.renderInventory = function () {
        var $inventory = this.getInventory();
        this.data.inventory.append($inventory);
    };

    /**
     * renders the background (formaly the dates) into the wrapper
     *
     * @returns {undefined}
     */
    this.renderCalendarBackground = function () {
        var $wrapperLength = this.data.container.width() - this.data.inventory.width() - 300,
            $date          = this.options.startdate,
            $blocks        = '',
            $i             = 0;

        for ($i; $i <= $wrapperLength; $i += 100) {
            $blocks = $blocks + this.getBlockForDate($date);
            $date.setDate($date.getDate() + 1);
        }

        this.data.wrapper.append($blocks);
    };

    /**
     * renders the basic html block, needed by OktolabCalendar
     *
     * @returns {undefined}
     */
    this.renderBasicCalendar = function () {
        this.data.container.append('<div class="calendar-inventory"></div><div class="calendar-wrapper"></div>');

        this.data.inventory = this.data.container.children('.calendar-inventory');
        this.data.wrapper = this.data.container.children('.calendar-wrapper');
    }
};