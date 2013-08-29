//var Oktolab = {};

Oktolab.Calendar = function Calendar(container) {
    'use strict';

    var options = {
        'container':    container,
        'startdate':    new Date(),
        'eventSrcUrl':  oktolab.baseUrl + '/api/v1/events.json',
        'configSrcUrl': oktolab.baseUrl + '/api/v1/calendarConfiguration.json' // timeblocks and inventory
    };

    /*
     * TODOS:
     *  * srcUrls caching
     *  * srcUrls parametizieren (baseUrls)
     *  * jslint (bamboo!)
     *  * moar tests
     *  * scrolling
     *  * items load
     *  * mvc-pattern durchsetzen!
     *  * jquery-plugin draus machen
     *  * stabilize da stuff (zB event-rendering, json checks, ...)
     */

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
        if (0 === AJS.$(data.container).length) { // if no calendar found, give up
            return;
        }

        this.renderBasicCalendar();
        this.renderInventory();
        this.renderCalendarBackground();
        this.renderEvents();
    };

    /**
     * Returns the Inventory as HTML
     *
     * @returns {String}
     */
    this.getInventoryHtml = function () {
        var $inventory = null;
        var inventoryContainer = AJS.$('.calendar-inventory');
        var list = null;

        AJS.$.getJSON(options.configSrcUrl, function(data) {
            AJS.$.each(data.items, function(key, items) {
                $inventory = AJS.$('<div />', { class: 'calendar-inventory-group' })
                    .append(AJS.$('<strong>').text(key));

                list = AJS.$('<ul />');
                AJS.$.each(items, function(key, item) {
                    var li = AJS.$('<li />');
                    li.append(AJS.$('<a />', { href: '#', id: 'Item' + item.id }).text(item.title));
                    list.append(li);
                });

                $inventory.append(list);
                $inventory.appendTo(inventoryContainer);
            });
        });
    };

    /**
     * renders the full inventory
     *
     * @returns {undefined}
     */
    this.renderInventory = function () {
        this.getInventoryHtml();
//        data.inventoryContainer.append(this.getInventoryHtml());
    };

    /**
     * renders the background (formaly the dates) into the wrapper
     *
     * @returns {undefined}
     */
    this.renderCalendarBackground = function () {
        AJS.$.getJSON(options.configSrcUrl).success(function (json) {
            AJS.$.each(json.dates, function(key, val) { // iterate through each date
                var $date = new Date(val.date),
                    $calendarHeadline = AJS.$('<div />').addClass('calendar-headline').append(
                        AJS.$('<span />').addClass('calendar-title').html($date.getDate() + '.' + ($date.getMonth() + 1))
                    );

                AJS.$.each(val.timeblocks, function(block) { // iterate all timeblocks on date
                    var $startDate  = new Date(val.timeblocks[block][0]),
                        $endDate    = new Date(val.timeblocks[block][1]),
                        $block      = AJS.$('<div />')
                            .addClass('calendar-timeblock')
                            .css('width', (100 / val.timeblocks.length).toFixed(2) + '%')
                            .html($startDate.getHours() + '-' + $endDate.getHours())
                            .appendTo($calendarHeadline);

                    data.timeblocks.push({ 'date': $endDate, 'block': $block });
                });

                data.wrapperContainer.append(AJS.$('<div />').addClass('calendar-date').append($calendarHeadline));
            });
        });
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

    /**
     * renders events on calendar
     * @returns {undefined}
     */
    this.renderEvents = function () {
        AJS.$.getJSON(options.eventSrcUrl, function(json) {
            AJS.$.each(json, function(key, val) {
                var $item   = AJS.$('a#' + val.item),
                    $block  = null,
                    $start  = null,
                    $end    = null;

                for ($block in data.timeblocks) {
                    if (data.timeblocks[$block].date >= new Date(val.start)) {
                        $start = data.timeblocks[$block];
                        break;
                    }
                }

                for ($block in data.timeblocks) {
                    if (data.timeblocks[$block].date >= new Date(val.end)) {
                        $end = data.timeblocks[$block];
                        break;
                    }
                }

//                console.log(AJS.$('#Item-1'));
                console.log($item, val.start);

                data.wrapperContainer.append(
                        AJS.$('<div class="calendar-event">' + val.title + '</div>')
                            .css('position', 'absolute')
                            .offset({ top: $item.position().top + 20, left: $start.block.offset().left})
                            .width($end.block.offset().left - $start.block.offset().left + $end.block.width())
                );
            });
        });

    };
};