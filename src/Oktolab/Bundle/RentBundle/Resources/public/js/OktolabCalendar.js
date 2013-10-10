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
                items: [],
                renderedTimeblocks: []
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

            $.when(
                Calendar.data.events,
                Calendar.data.timeblocks,
                Calendar.data.inventory
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
         * @param {object} timeblocks
         */
        showCalendarBackground: function (timeblocks) {
            $.each(timeblocks, function (date, timeblock) {
                var date = new Date(date);
                var headline = $('<div />').addClass('calendar-headline');

                headline.append($('<span />').addClass('calendar-title').html(timeblock.title));
                $.each(timeblock.blocks, function (key, dataBlock) {
                    var block = $('<div />')
                        .addClass('calendar-timeblock')
                        .html(dataBlock.title)
                        .css('width', (100 / timeblock.blocks.length).toFixed(2) + '%');

                    block.appendTo(headline);
                    Calendar.data.renderedTimeblocks.push({ 'date': new Date(dataBlock.end), 'block': block });
                });

                headline.appendTo($('<div />').addClass('calendar-date').appendTo(Calendar.data.containerWrapper));
            });
        },

        /**
         * Builds the HTML for Events.
         *
         * @param {object} events
         * @returns {jQuery}
         */
        showEvents: function (events) {
            var template = '<div class="calendar-event" style="position:absolute; top:{{style_top}}px; left:{{style_left}}px; width:{{style_width}}px"><strong>{{title}}</strong><div class="calendar-event-description">{{state}} - {{name}}<br/>{{begin}} - {{end}}</div></div>';
            var template = Hogan.compile(template);

            $.each(events, function (identifier, event) {
                var $item = Calendar.data.items[event.objects[0].object_id.toLowerCase()];
                var beginBlock = null;
                var endBlock = null;

                beginBlock = Calendar.findBlockByDate(new Date(event.begin));
                endBlock = Calendar.findBlockByDate(new Date(event.end));

                Calendar.data.containerWrapper.append(template.render($.extend(event, {
                    style_top: $item.position().top + 20,
                    style_left: beginBlock.block.offset().left,
                    style_width: endBlock.block.offset().left - beginBlock.block.offset().left + endBlock.block.width(),
                })));
            });
        },

        /**
         * Finds a rendered block by given Date.
         *
         * @param {object:Date} date
         * @returns {object}
         */
        findBlockByDate: function (date) {
            var block = null;

            for (block in Calendar.data.renderedTimeblocks) {
                var block = Calendar.data.renderedTimeblocks[block];

                if (block.date >= date) {
                    return block;
                }
            }
        }
    };

    window.Oktolab.Calendar = Calendar;
}(window, document, jQuery, Oktolab));
