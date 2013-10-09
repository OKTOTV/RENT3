(function (window, document, $, Oktolab) {
    'use strict';

    /**
     * OktolabCalendar used by Oktolab RENT
     *
     * @TODO: scrolling features
     * @TODO: reloading events automatically
     * @TODO: use Hogan for template rendering
     *
     * @type Object
     */
    var Calendar = {

        /**
         * Initializes the Calendar settings
         *
         * @this Calendar
         * @param {array} settings Key-Value Settings for Calendar
         */
        init: function (settings) {
            Calendar.config = {
                container:       '#calendar',
                eventSrcUri:     '/api/calendar/events.json',
                timeblockSrcUri: '/api/calendar/timeblock.json',
                inventorySrcUri: '/api/calendar/inventory.json'
            };

            $.extend(Calendar.config, settings);

            Calendar.data = {
                container: $(Calendar.config.container),
                containerWrapper: $('<div class="calendar-wrapper" />'),
                containerTimeblocks: $('<div class="calendar-timeblocks" />'),
                containerInventory: $('<div class="calendar-inventory" />'),
                inventory: $.getJSON(Calendar.buildUrl(Calendar.config.inventorySrcUri)).promise(),
                events: $.getJSON(Calendar.buildUrl(Calendar.config.eventSrcUri)).promise(),
                timeblocks: $.getJSON(Calendar.buildUrl(Calendar.config.timeblockSrcUri)).promise(),
                items: []
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
            Calendar.data.timeblocks.then(function (data) {
                Calendar.showCalendarBackground(data);
            });

            Calendar.data.inventory.then(function (items) {
                Calendar.showInventory(items);
            });

//            $.when(
//                Calendar.data.events,
//                Calendar.data.timeblocks,
//                Calendar.data.inventory
//            ).done(function (events) {
//                Calendar.showEvents(events[0]);
//            });

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
            $.each(items, function (key, item) {
                var group = $('<div />').addClass('calendar-inventory-group').append($('<strong />').text(item.title));
                var list = $('<ul />').appendTo(group);

                $.each(item.objectives, function (key, objective) {
                    var object = $('<a />', { href: '#', id: objective.objective }).text(objective.title);
                    Calendar.data.items[objective.objective.toLowerCase()] = object;
                    $('<li />').append(object).appendTo(list);
                });

                group.appendTo(Calendar.data.containerInventory);
            });
        },

        /**
         * Builds the HTML for Calendar Background.
         *
         * @param {object} dates
         */
        showCalendarBackground: function (timeblocks) {
            $.each(timeblocks, function (date, timeblock) {
                var date = new Date(date);
                var headline = $('<div />').addClass('calendar-headline');

                headline.append($('<span />').addClass('calendar-title').html(timeblock['title']));

                $.each(timeblock['blocks'], function (key, dataBlock) {
                    var block = $('<div />')
                            .addClass('calendar-timeblock')
                            .html(dataBlock['title'])
                            .css('width', (100 / timeblock['blocks'].length).toFixed(2) + '%');

                    block.appendTo(headline);
                });

                headline.appendTo($('<div />').addClass('calendar-date').appendTo(Calendar.data.containerWrapper));
            });

//            $.each(timeblocks, function (key, value) {
//                var blockDate = new Date(value.date);
//                var begin = new Date(value.begin);
//                var end = new Date(value.end);
//
//                if (currentDate < blockDate || first) {
//                    first = false;
//                    currentDate = blockDate;
//
//                    headline = $('<div />').addClass('calendar-headline').append(
//                        $('<span />').addClass('calendar-title').html(blockDate.getDate() + '.' + (blockDate.getMonth() + 1 ))
//                    ).appendTo($('<div />').addClass('calendar-date').appendTo(Calendar.data.containerWrapper));
//
//                    // .css('width', (100 / value.timeblocks.length).toFixed(2) + '%')
//                }
//
//                var block = $('<div />').addClass('calendar-timeblock').html(begin.getHours() + '-' + end.getHours()).appendTo(headline);
//            });
        },

        /**
         * Builds the HTML for Events.
         *
         * @param {object} events
         * @returns {jQuery}
         */
        showEvents: function (events) {
            $.each(events, function (key, value) {
                var $item = Calendar.data.items[value.item.toLowerCase()];
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
