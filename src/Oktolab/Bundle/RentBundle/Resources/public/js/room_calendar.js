(function (window, document, $, Oktolab) {
    'use strict';

    /**
     * OktolabRoomCalendar used by Oktolab RENT
     *
     * @author: rs
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
                container:       '#room-calendar',
                eventSrcUri:     '/api/calendar/room_events.json',
                timeblockSrcUri: '/api/calendar/room_timeblock.json',
                roomSrcUri:      '/api/calendar/rooms.json'
            };

            $.extend({}, Calendar.config, settings);

            Calendar.data = {
                container: $(Calendar.config.container),
                containerWrapper: $('<div class="calendar-wrapper" />'),
                containerTimeblocks: $('<div class="calendar-timeblocks" />'),
                containerInventory: $('<div class="calendar-inventory" />'),
                inventory: $.getJSON(Calendar.buildUrl(Calendar.config.roomSrcUri)).promise(),
                events: $.getJSON(Calendar.buildUrl(Calendar.config.eventSrcUri)).promise(),
                timeblocks: $.getJSON(Calendar.buildUrl(Calendar.config.timeblockSrcUri)).promise(),
                items: [],
                renderedTimeblocks: [],
                today_button: $('#calendar-show-today'),
                date_button:  $('#calendar-show-date'),
                date_input:   $('#calendar-input-date')
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
            console.log('init room calendar');
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

            //add onclick to search and today button
            Calendar.data.today_button.on('click', function () {
                Calendar._loadCalendarStartingAtDate(new Date());
            });

            Calendar.data.date_button.on('click', function (){
                if (Calendar.data.date_input.val() !== '') {
                    Calendar._loadCalendarStartingAtDate(new Date(Calendar.data.date_input.val()));
                }
            });

        },

        /**
         * Removes events from DOM, loads new Events starting at given date
         */
        _loadEventsStartingAtDate: function (date) {
            var $date = new Date(date);
            var eventUrl = Calendar.buildUrl(Calendar.config.eventSrcUri);
            eventUrl = eventUrl+'/'+$.datepicker.formatDate('yy-mm-dd', $date);
            $date.setDate($date.getDate()+7);
            eventUrl = eventUrl+'/'+$.datepicker.formatDate('yy-mm-dd', $date);

            //load new events.
            Calendar.data.events = $.getJSON(eventUrl).promise();
            //render them if loading has finished.
            $.when(Calendar.data.events, Calendar.data.timeblocks).done(function (data) {
                Calendar.showEvents(data[0]);
            })
        },

        /**
         * Removes timeblocks from DOM, loads new Timeblocks starting at given date
         */
        _loadTimeblocksStartingAtDate: function (date) {
            var $date = new Date(date);
            var timeBlockUrl = Calendar.buildUrl(Calendar.config.timeblockSrcUri);
            timeBlockUrl = timeBlockUrl+ '/' + $.datepicker.formatDate('yy-mm-dd', $date);
            $date.setDate($date.getDate()+7);
            timeBlockUrl = timeBlockUrl+ '/' + $.datepicker.formatDate('yy-mm-dd', $date);

            //load new timeblocks.
            Calendar.data.timeblocks = $.getJSON(timeBlockUrl).promise();
            //render new timeblocks
            Calendar.data.timeblocks.then(function (data) {
                Calendar.showCalendarBackground(data);
            });
        },

        /**
         * Reloads Calendarinformation for Timeblocks and Events.
         */
        _loadCalendarStartingAtDate: function (date) {
            Calendar.data.containerWrapper.empty();
            Calendar.data.renderedTimeblocks = [];
            Calendar.data.events = [];

            Calendar._loadTimeblocksStartingAtDate(date);
            Calendar._loadEventsStartingAtDate(date);
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
            $('.calendar-wrapper').height($('.calendar-inventory').height()+54);
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
            $.each(events, function (key, event) {
                console.log('render event');
                var begin = Calendar.findBlockByDate(new Date(event.begin));
                var end = Calendar.findBlockByDate(new Date(event.end));

                $.each(event.objects, function (key, object) {
                    console.log(object);
                    var inventoryObject = Calendar._getInventoryObjectByIdentifier(object.object_id.toLowerCase());
                    if (typeof(inventoryObject) === 'undefined') {
                        return true;    // skip object
                    }
                    console.log('render chip');

                    var eventIdentifier = Calendar._renderEventChip(event, inventoryObject, begin.block, end.block);
                    Calendar._renderEventDescription(event, eventIdentifier);
                });
            });
        },

        /**
         * Renders an Event Chip (that small thing you see on Calendar).
         *
         * @param {object} event
         * @param {object|jQuery} inventoryObject
         * @param {object|jQuery} begin
         * @param {object|jQuery} end
         *
         * @returns {String}
         */
        _renderEventChip: function (event, inventoryObject, begin, end) {
            var icon = Calendar._getIconForEvent(event);
            var template = '<div class="calendar-event" style="position:absolute; top:{{style_top}}px; left:{{style_left}}px; width:{{style_width}}px" id="{{eventIdentfier}}"><a href="#">'+icon+'</a><strong style="display: block; width: 100%; height: inherit">{{title}}</strong></div>';
            var template = Hogan.compile(template);
            var objectId = 'event-' + event.id + '-' + inventoryObject.attr('id').replace(':', '-');
            var eventView = $.extend({}, event, {
                style_top:      inventoryObject.position().top + 20,
                style_left:     begin.offset().left,
                style_width:    end.offset().left - begin.offset().left + end.width(),
                eventIdentfier: objectId,
            });

            Calendar.data.containerWrapper.append(template.render(eventView));

            return objectId;
        },

        /**
         * Renders the Event description and triggers AJS.InlineDialog
         *
         * @param {object} event
         * @param {string} eventIdentifier
         */
        _renderEventDescription: function (event, eventIdentifier) {
            var lozenge = Calendar._getLozengeForEvent(event);

            var description = '<div class="calendar-event-description"><div class="event-image"><img width="50px" height="50px" src="{{image}}" alt="{{image_alt}}" /></div><div class="event-fields"><div class="event-field event-summary"><strong>{{name}}</strong>'+lozenge+'</div><div class="event-field event-duration"><em>{{begin_view}}</em> - <em>{{end_view}}</em></div><div class="event-field event-description">{{description}}</div><div class="event-field event-objects">{{{objects}}}</div></div><div class="event-controls buttons-container"><div class="buttons"><a class="aui-button aui-button-primary" href="{{uri}}">Bearbeiten</a></div></div></div>';
            var description = Hogan.compile(description);
            var eventView = $.extend({}, event, {
                image:   oktolab.baseUrl + '/../aui-5.1/images/user-avatar-blue-48@2x.png',
                objects: Calendar._getRenderedEventObjects(event),
            });

            AJS.InlineDialog($('#' + eventIdentifier), 1,
                function(content, trigger, showPopup) {
                    content.html(description.render(eventView));
                    showPopup();
                    return false;
                }, { 'onHover': false, 'showDelay': 200, 'onTop': true, 'width': 350 }
            );
        },

        /**
         * Returns the DOM Inventory object by given id
         *
         * @param {String} id
         *
         * @returns {Calendar.data.items}
         */
        _getInventoryObjectByIdentifier: function (id) {
            return Calendar.data.items[id];
        },

        /**
         * Builds an ul-list with EventObjects for rendering.
         *
         * @param {object} event
         * @returns {String}
         */
        _getRenderedEventObjects: function (event) {
            var renderObjects = '<ul>';
            $.each(event.objects, function (key, object) {
                renderObjects = renderObjects + '<li><a href="' + object.uri + '">'  + object.title + '</a></li>';
            });

            return renderObjects + '</ul>';
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
        },

        _getLozengeForEvent: function (event) {
            switch(event.state) { //see event entity
                case 'PREPARED':
                    return '<span class="aui-lozenge aui-lozenge-complete">Reserviert</span>';
                case 'RESERVED':
                    return '<span class="aui-lozenge aui-lozenge-complete">Reserviert</span>';
                case 'LENT':
                    return '<span class="aui-lozenge aui-lozenge-moved aui-lozenge-subtle">Ausgegeben</span>';
                case 'DELIVERED':
                    return '<span class="aui-lozenge aui-lozenge-current aui-lozenge-subtle">Rückgegeben</span>';
                case 'CHECKED':
                    return '<span class="aui-lozenge aui-lozenge-current aui-lozenge-subtle">Geprüft</span>';
                case 'COMPLETED':
                    return '<span class="aui-lozenge aui-lozenge-success aui-lozenge-subtle">Abgeschlossen</span>';
                case 'CANCELED':
                    return '<span class="aui-lozenge">Storniert</span>';
                case 'DEFERRED':
                    return '<span class="aui-lozenge aui-lozenge-error">In Verzug</span>';
                default:
                    return '<span class="aui-lozenge">Unknown</span>';
            }
        },

        _getIconForEvent: function (event) {
            switch(event.state) { //see event entity
                case 'PREPARED':
                    return '<span class="aui-icon aui-icon-info">Reserviert</span>';
                case 'RESERVED':
                    return '<span class="aui-icon aui-icon-info">Reserviert</span>';
                case 'LENT':
                    return '<span class="aui-icon aui-icon-small aui-iconfont-devtools-task-in-progress">Ausgegeben</span>';
                case 'DELIVERED':
                    return '<span class="aui-icon aui-icon-warning">Rückgegeben</span>';
                case 'CHECKED':
                    return '<span class="aui-icon aui-icon-success">Geprüft</span>';
                case 'COMPLETED':
                    return '<span class="aui-icon aui-icon-success">Reserviert</span>';
                case 'CANCELED':
                    return '<span class="aui-icon aui-icon-close">Storniert</span>';
                case 'DEFERRED':
                    return '<span class="aui-icon aui-icon-error">In Verzug</span>';
                default:
                    return '<span class="aui-lozenge">Unknown</span>';
            }
        }
    };

    // Registers OktolabCalendar
    window.Oktolab.RoomCalendar = Calendar;
}(window, document, jQuery, Oktolab));

