var Oktolab = {};

Oktolab.Calendar = function Calendar(container) {
    'use strict';

    var options = {
        'container':    container,
        'startdate':    new Date(),
        'eventSrcUrl':  'http://localhost/vhosts/rent/web/app_dev.php/api/v1/events.json',
        'configSrcUrl': 'gone.json' // timeblocks and inventory
    };

    var data = {
        container:           AJS.$(options.container),
        wrapperContainer:   null,
        inventoryContainer: null,
        inventory:            null,
        timeblocks:           [],
    };

    /**
     * renders the calendar
     * @returns {undefined}
     */
    this.render = function () {
        this.renderBasicCalendar();
        this.renderInventory();
        this.renderCalendarBackground();
        this.renderEvents();
    };

    /**
     * Returns the HTML needed by rendering the background calendar
     *
     * @param {Date} date
     * @returns {String}
     */
    this.getBlockForDate = function (date) {
        var $calendarHeadline = AJS.$('<div />').addClass('calendar-headline').append(
            AJS.$('<span />').addClass('calendar-title').html(date.getDate() + '.' + (date.getMonth() + 1))
        );

        var $date = new Date(date);
            $date.setHours(12);
            $date.setMinutes(0);
        var $block = AJS.$('<div />').addClass('calendar-timeblock').html('09-12').appendTo($calendarHeadline);
        data.timeblocks.push({ 'date': $date, 'block': $block });

        var $date = new Date(date);
            $date.setHours(20);
            $date.setMinutes(0);
        var $block = AJS.$('<div />').addClass('calendar-timeblock').html('17-20').appendTo($calendarHeadline);
        data.timeblocks.push({ 'date': $date, 'block': $block });

        //console.log(data.timeblocks);
        return AJS.$('<div />').addClass('calendar-date').append($calendarHeadline);
    };

    this.getInventory = function () {
        data.inventory = AJS.$.parseJSON('{"stuff":["items","itema","itemb"],"some more stuff":["item-bar","item-foo","item-baz"]}')
        return data.inventory;
    };

    this.getEvents = function () {
        data.events = AJS.$.getJSON(options.eventSrcUrl);

        return data.events;
        //console.log(this.data.events);
    };

    /**
     * Returns the Inventory as HTML
     *
     * @returns {String}
     */
    this.getInventoryHtml = function () {
        var $json       = this.getInventory(),
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
                $inventory = $inventory + '<li><a href="#" id="' + $json[$key][$item] + '">' + $json[$key][$item] + '</a></li>';
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
        data.inventoryContainer.append(this.getInventoryHtml());
    };

    /**
     * renders the background (formaly the dates) into the wrapper
     *
     * @returns {undefined}
     */
    this.renderCalendarBackground = function () {
        var $wrapperLength = data.container.width() - data.inventoryContainer.width(),
            $date          = options.startdate,
            $blocks        = [],
            $i             = 0;

        for ($i; $i <= $wrapperLength; $i += 100) {
            data.wrapperContainer.append(this.getBlockForDate($date));
            $date.setDate($date.getDate() + 1);
        }

//        data.wrapperContainer.append($blocks);
    };

    /**
     * renders the basic html block, needed by OktolabCalendar
     *
     * @returns {undefined}
     */
    this.renderBasicCalendar = function () {
        data.inventoryContainer = AJS.$('<div class="calendar-inventory" />').appendTo(data.container);
        data.wrapperContainer   = AJS.$('<div class="calendar-wrapper" />').appendTo(data.container);
    };

    this.renderEvents = function () {

        AJS.$.getJSON(options.eventSrcUrl, function(json) {
//            var items = [],
//                wrapperOffset = data.wrapperContainer.offset();

            AJS.$.each(json, function(key, val) {
                //items.push('<div>' + val.title + '</div>');
                var $item = AJS.$('#' + val.item);
//                console.log($item.offset().top, $item.offsetParent().offset().top, $item.position().top);
//                console.log($item.offset().top - $item.offsetParent().offset().top);
//                console.log($item.offset().top - $item.offsetParent().offset().top + 18)
//                console.log($item.position().top + 20)

                //console.log(new Date('2013-07-17T11:00:00+02:00'));
                var $block = null,
                    $start = null;
                for ($block in data.timeblocks) {
//                    console.log(data.timeblocks[$block].date, new Date(val.start), data.timeblocks[$block].date >= new Date(val.start));
                    if (data.timeblocks[$block].date >= new Date(val.start)) {
                        $start = data.timeblocks[$block];
                        //console.log();
                        console.log(data.timeblocks[$block].date, new Date(val.start));
                        break;
                    }
                }

                console.log($start);

                data.wrapperContainer.append(
                        AJS.$('<div class="calendar-event">' + val.title + '</div>')
                            .css('position', 'absolute')
                            .offset({ top: $item.position().top + 20, left: $start.block.offset().left})
                            .width(val.length)
                );
            });

//            data.wrapperContainer.append(items.join(''));
        });

    };
};