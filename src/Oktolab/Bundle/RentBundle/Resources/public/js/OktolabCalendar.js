(function (window, document, $, Oktolab) {
    'use strict';

    /**
     * OktolabCalendar used by Oktolab RENT
     *
     * Missing Features:
     * - scrolling
     * - reloading
     *
     * @type Object
     */
    var Calendar = {

        /**
         * Initializes the Calendar settings
         *
         * @this Calendar
         * @param {array} settings  Key-Value Settings for Calendar
         */
        init: function (settings) {
            Calendar.config = {
                container:    '#calendar',
                eventSrcUri:  '/api/v1/events.json',
                configSrcUri: '/api/v1/calendarConfiguration.json'
            };

            $.extend(Calendar.config, settings);

            Calendar.data = {
                container:            $(Calendar.config.container),
                containerWrapper:     $('<div class="calendar-wrapper" />'),
                containerInventory:   $('<div class="calendar-inventory" />'),
                configuration:        $.getJSON(Calendar.buildUrl(Calendar.config.configSrcUri)).promise(),
                events:               $.getJSON(Calendar.buildUrl(Calendar.config.eventSrcUri)).promise(),
                timeblocks:           [],
                items:                []
            };

            Calendar.setup();
        },

        /**
         * Setup and renders the Calendar.
         *
         * @this Calendar
         * @fires OktolabCalendar:rendered
         */
        setup: function () {
            Calendar.data.configuration.then(function (data) {
                Calendar.showInventory(data.items);
                Calendar.showCalendarBackground(data.dates);
            });

            $.when(
                Calendar.data.events,
                Calendar.data.configuration
            ).done(function (events) {
                Calendar.showEvents(events[0]);
            });

            Calendar.data.container
                    .append(Calendar.data.containerInventory)
                    .append(Calendar.data.containerWrapper);

            Calendar.data.container.trigger('OktolabCalendar:rendered');
        },

        /**
         * Builds the absolute URL.
         *
         * @param {string} url  the relative URL to application
         * @return {string}
         */
        buildUrl: function (url) {
            return Oktolab.baseUrl + url;
        },

        /**
         * Builds the HTML for Inventory items.
         *
         * @param {object} items
         */
        showInventory: function (items) {
            $.each(items, function (key, items) {
                var $inventory = $('<div />').addClass('calendar-inventory-group').append($('<strong />').text(key));
                var $list = $('<ul />').appendTo($inventory);

                $.each(items, function (key, item) {
                    var $item = $('<a />', { href: '#', id: key }).text(item.title);

                    Calendar.data.items[key] = $item;
                    $('<li />').append($item).appendTo($list);
                });

                $inventory.appendTo(Calendar.data.containerInventory);
            });
        },

        /**
         * Builds the HTML for Calendar Background.
         *
         * @param {object} dates
         */
        showCalendarBackground: function (dates) {
            $.each(dates, function (key, value) {
                var $date = new Date(value.date);
                var $headline = $('<div />').addClass('calendar-headline').append(
                        $('<span />').addClass('calendar-title').html($date.getDate() + '.' + ($date.getMonth() + 1))
                    );

                // iterate all timeblocks on per date
                $.each(value.timeblocks, function (block) {
                    var $begin = new Date(value.timeblocks[block][0]);
                    var $end   = new Date(value.timeblocks[block][1]);
                    var $block = $('<div />')
                        .addClass('calendar-timeblock')
                        .css('width', (100 / value.timeblocks.length).toFixed(2) + '%')
                        .html($begin.getHours() + '-' + $end.getHours())
                        .appendTo($headline);

                    Calendar.data.timeblocks.push({ 'date': $end, 'block': $block });
                });

                Calendar.data.containerWrapper.append($('<div />').addClass('calendar-date').append($headline));
            });
        },

        /**
         * Builds the HTML for Events.
         *
         * @param {object} events
         * @returns {jQuery}
         */
        showEvents: function (events) {
            $.each(events, function (key, value) {
                var $item = Calendar.data.items[value.item];
                var $block, $begin, $end = null;

                // find beginning block
                for ($block in Calendar.data.timeblocks) {
                    if (Calendar.data.timeblocks[$block].date >= new Date(value.start)) {
                        $begin = Calendar.data.timeblocks[$block];
                        break;
                    }
                }

                // find ending block
                for ($block in Calendar.data.timeblocks) {
                    if (Calendar.data.timeblocks[$block].date >= new Date(value.end)) {
                        $end = Calendar.data.timeblocks[$block];
                        break;
                    }
                }

                $('<div />').addClass('calendar-event').html(value.title)
                    .css('position', 'absolute')
                    .offset({ top: $item.position().top + 20, left: $begin.block.offset().left })
                    .width($end.block.offset().left - $begin.block.offset().left + $end.block.width())
                    .appendTo(Calendar.data.containerWrapper);
            });
        }
    };

    window.Oktolab.Calendar = Calendar;
}(window, document, jQuery, Oktolab));

Oktolab.Calendar.init();