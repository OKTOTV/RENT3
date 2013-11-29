(function (window, Oktolab, $) {
    'use strict';

    /**
     * Controls an Event Form.
     *
     * @type Object
     */
    var EventForm = {

        /**
         * Initializes the EventForm.
         *
         * @param {object} settings
         */
        init: function (settings) {
            // load configuration, abort on error
            if (EventForm._loadConfiguration(settings)) {
                EventForm._setup();
            }


            //TODO: setup room
            var roomSettings = {
                container:          '#room_form',
                objectSearch:       '#room-search-field',
                objectCollection:   '#room-event-objects',
                beginDate:          '.room-beginDate',
                endDate:            '.room-endDate',
                beginHour:          'room-beginHour',
                type:               'room',
                id:                 'newRoom'
            };

            if (EventForm._loadConfiguration(roomSettings)) {
                EventForm._setup();
            }

        },

        /**
         * Loads configuration. Returns false if something went wrong.
         *
         * @param {object} settings     key-value array
         *
         * @returns {Boolean}
         */
        _loadConfiguration: function (settings) {
            EventForm.config = {
                container:          '#OktolabRentBundle_Event_Form',
                objectSearch:       '#inventory-search-field',
                objectCollection:   '#inventory-event-objects',
                costUnitSearch:     'costunit-search-field',
                contactSearch:      'contact-search-field',
                beginDate:          '.inventory-beginDate',
                endDate:            '.inventory-endDate',
                beginHour:          'inventory-beginHour',
                hideButtons:        false,
                type:               'inventory',
                id:                 'newInventory'
            };

            // TODO: this can lead to unusual behaviour, use: $.extend({}, EventForm.config, settings); instead
            $.extend(EventForm.config, settings);

            var container = $(EventForm.config.container);
            if (0 === container.length) {
                window.console.log('EventForm >> Container not found: ' + EventForm.config.container);
                return false;
            }

            EventForm.data = {
                container:          container,
                objectSearch:       container.find(EventForm.config.objectSearch),
                objectCollection:   container.find(EventForm.config.objectCollection),
            };

            return true;    // configuration successfully loaded
        },

        _setup: function () {
            var container = EventForm.data.container;

            EventForm.data.costUnitContainer = container.find('select[name*=costunit]');
            EventForm.data.contactContainer = container.find('select[name*=contact]');
            EventForm.data.typeContainer = container.find('select[name*=type]');
            EventForm.data.nameContainer = container.find('input[name*=name]');
            EventForm.data.beginContainer = $(EventForm.config.beginDate);//container.find('input[name*=begin]');
            EventForm.data.endContainer = $(EventForm.config.endDate);//container.find('input[name*=end]');
            EventForm.data.objectSearch = $(EventForm.config.objectSearch);

            // Hide some form fields
            EventForm.data.costUnitContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.contactContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.nameContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.beginContainer.closest('div').addClass('hidden');
            EventForm.data.endContainer.closest('div').addClass('hidden');
            EventForm.data.typeContainer.closest('div.field-group').addClass('hidden');

            if (EventForm.config.hideButtons) {
                container.find('.buttons-container').addClass('hidden');
            }

            // Render form inputs
            EventForm._renderEndTimeFields();
            EventForm._renderBeginTimeFields();
            EventForm._renderContactField();
            EventForm._renderCostUnitField();

            // Register Listeners
            EventForm._registerCostUnitListener();
            EventForm._registerContactListener();
            EventForm._registerBeginTimeListener();
            EventForm._registerEndTimeListener();
            EventForm._registerBeginTimeChange();
            EventForm._registerEndTimeChange();
        },

        _renderCostUnitField: function () {
            var costUnitLabel = EventForm.data.container.find('label[for*=costunit]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config.costUnitSearch }).append(costUnitLabel);
            var input = $('<input />', { 'id': EventForm.config.costUnitSearch }).addClass('text');
            var value = EventForm.data.costUnitContainer.data('name');

            fieldGroup.append(label).append(input);
            EventForm.data.container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data.costUnitSearchContainer = input;

            EventForm.data.costUnitSearchContainer.typeahead({
                name:       'costunits',
                valueKey:   'name',
                prefetch: { url: oktolab.typeahead.costunitPrefetchUrl, ttl: 5 },
                template: [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{name}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                engine: Hogan
            });

            if ('undefined' !== typeof(value) && 0 !== value.length) {
                input.val(value);
                EventForm.data.costUnitSearchContainer.typeahead('setQuery', value);
            }
        },

        _renderContactField: function () {
            var contactLabel = EventForm.data.container.find('label[for*=contact]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config.contactSearch }).append(contactLabel);
            var input = $('<input />', { 'id': EventForm.config.contactSearch, 'disabled': 'disabled' }).addClass('text');
            var value = EventForm.data.contactContainer.data('name');

            if ('undefined' !== typeof(value) && 0 !== value.length) {
                input.attr('disabled', false).val(value);
            }

            fieldGroup.append(label).append(input);
            EventForm.data.container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data.contactSearchContainer = input;
        },

        _registerCostUnitListener: function () {
            EventForm.data.costUnitSearchContainer.on('typeahead:selected', function(e, datum) {
                var remoteUrl = oktolab.typeahead.costunitcontactRemoteUrl.replace('__id__', datum.id);

                // Set Values to hidden input fields
                EventForm.data.costUnitContainer.val(datum.id);
                EventForm.data.nameContainer.val(datum.name);

                // load new Typeahead on Contacts
                EventForm.data.contactSearchContainer.typeahead('destroy');
                EventForm.data.contactSearchContainer.attr('disabled', false).typeahead({
                    name:       'costunit-contacts-' + datum.id,
                    valueKey:   'name',
                    prefetch :  remoteUrl,
                    template: [
                        '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                        '<p class="tt-object-name">{{name}}</p>'
                    ].join(''),
                    engine: Hogan
                });
            });
        },

        _registerContactListener: function () {
            EventForm.data.contactSearchContainer.on('typeahead:selected', function (e, datum) {
                EventForm.data.contactContainer.val(datum.id);
            });
        },

        _renderBeginTimeFields: function () {
            var beginLabel = EventForm.data.container.find('label[for*=_begin]').html();
            var label = $('<label />', { 'for': EventForm.config.beginDate }).append(beginLabel);
            var fieldGroup = $('<div />').addClass('field-group');

            var inputDate = $('<input />', { 'id': EventForm.config.beginDate }).addClass('aui-date-picker text event-date');
            var inputHour = $('<select />').addClass('select event-select ' + EventForm.config.beginHour);
            var inputMinute = $('<select />').addClass('select event-select');

            if ('' !== EventForm.data.container.find('input[id*=_begin]').val()) {
                var datetime = new Date(EventForm.data.container.find('input[id*=_begin]').val());
                inputDate.val($.datepicker.formatDate('yy-mm-dd',datetime));
                inputHour.append($("<option></option>").text(Oktolab.leadingZero(datetime.getHours().toString())));
                inputMinute.append($("<option></option>").text(Oktolab.leadingZero(datetime.getMinutes().toString())));
            }

            fieldGroup.append(label).append(inputDate).append(inputHour).append(inputMinute);
            EventForm.data.container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data.beginDate = inputDate;
            EventForm.data.beginHour = inputHour;
            EventForm.data.beginMinute = inputMinute;
        },

        _renderEndTimeFields: function () {
            var endLabel = EventForm.data.container.find('label[for*=_end]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config.beginDate }).append(endLabel);

            var inputDate = $('<input />', { 'id': EventForm.config.endDate }).addClass('aui-date-picker text event-date').val($.datepicker.formatDate('yy-mm-dd',datetime));
            var inputHour = $('<select />', { 'id': EventForm.config.endHour }).addClass('select event-select');
            var inputMinute = $('<select />', { 'id': EventForm.config.endMinute }).addClass('select event-select');

            if ('' !== EventForm.data.container.find('input[id*=_end]').val()) {
                var datetime = new Date(EventForm.data.container.find('input[id*=_end]').val());
                inputDate.val($.datepicker.formatDate('yy-mm-dd',datetime));
                inputHour.append($("<option></option>").text(Oktolab.leadingZero(datetime.getHours().toString())));
                inputHour.append($("<option></option>").text(Oktolab.leadingZero(datetime.getMinutes().toString())));
            }

            fieldGroup.append(label).append(inputDate).append(inputHour).append(inputMinute);
            EventForm.data.container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data.endDate = inputDate;
            EventForm.data.endHour = inputHour;
            EventForm.data.endMinute = inputMinute;
        },

        /**
         * Sets the begin hour/minute selectboxes depending on selected date/hour
         */
        _registerBeginTimeListener: function () {
            EventForm.data.beginHour.on('mouseenter', function (){
                console.log(EventForm.data.beginDate);
                console.log(EventForm.data.beginDate.val());
                var date = EventForm.data.beginDate.val();
                var selectBox = EventForm.data.beginHour;
                var timeblocks = EventForm.data.beginContainer.data('timeblock-times');

                EventForm._setHourTimesForSelect(date, timeblocks, selectBox);
            });
            EventForm.data.beginHour.on('change', function () {
                var date = EventForm.data.beginDate.val();
                var hour = EventForm.data.beginHour.val();
                var selectBox = EventForm.data.beginMinute;
                var timeblocks = EventForm.data.beginContainer.data('timeblock-times');

                EventForm._setMinuteTimesForSelect(date, hour, timeblocks, selectBox);
            });
        },

        /**
         * Sets the end hour/minute selectboxes depending on selected date/hour
         */
        _registerEndTimeListener: function () {
            EventForm.data.endHour.on('mouseenter', function (){
                var date = EventForm.data.endDate.val();
                var selectBox = EventForm.data.endHour;
                var timeblocks = EventForm.data.endContainer.data('timeblock-times');

                EventForm._setHourTimesForSelect(date, timeblocks, selectBox);
            });
            EventForm.data.endHour.on('change', function() {
                var date = EventForm.data.endDate.val();
                var hour = EventForm.data.endHour.val();
                var selectBox = EventForm.data.endMinute;
                var timeblocks = EventForm.data.endContainer.data('timeblock-times');

                EventForm._setMinuteTimesForSelect(date, hour, timeblocks, selectBox);
            });
        },

        /**
         * Sets the Event begin input on newest changes of hour or minute selectboxes
         */
        _registerBeginTimeChange: function() {
            EventForm.data.beginHour.on('change', function (){
               EventForm._setEventBeginInputTime();
            });
            EventForm.data.beginMinute.on('change', function(){
                EventForm._setEventBeginInputTime();
            })
        },

        /**
         * Sets the Event end input on newest changes of hour or minute selectboxes
         */
        _registerEndTimeChange: function() { //redundant!
            EventForm.data.endHour.on('change', function (){
                EventForm._setEventEndInputTime();
            });
            EventForm.data.endMinute.on('change', function(){
               EventForm._setEventEndInputTime();
            });
        },

        /**
         * register the selection of a typeahead thing and act accordingly
         */
        _registerTypeaheadSelect: function() {
            EventForm.data.objectSearch.on('typeahead:selected', function (e, datum) {
                var form = EventForm.data.objectCollection.closest('form');
                if (0 === form.find('div[data-object="' + datum.value + '"]').length) {
//                    oktolab.addTypeaheadObjectToEventForm(AJS.$('.event-objects'), datum);
                    oktolab.addTypeaheadObjectToEventForm(EventForm.data.objectCollection, datum);
                }

                jQuery(this).typeahead('setQuery', '');
            });
        },

        /**
         * Sets Evenform input for begin and tries to activate the object search
         */
        _setEventBeginInputTime: function () {
            var date = EventForm.data.beginDate.val();
            var hour = EventForm.data.beginHour.val();
            var min  = EventForm.data.beginMinute.val();
            if (date && hour && min) {
                var datetime = date+'T'+hour+':'+min;
//                if (datetime !== EventForm.data.beginContainer.val()) {
                EventForm.data.beginContainer.val(datetime);
//                }
            } else {
                EventForm.data.beginContainer.val('');
            }
            EventForm._activateObjectSearch();
        },

        /**
         * Sets Eventform input for end and tries to activate the object search
         */
        _setEventEndInputTime: function() {
            var date = EventForm.data.endDate.val();
            var hour = EventForm.data.endHour.val();
            var min  = EventForm.data.endMinute.val();
            if (date && hour && min) {
                var datetime = date+'T'+hour+':'+min;
//                if (datetime !== EventForm.data.endContainer.val()) {
                EventForm.data.endContainer.val(datetime);
//                }
            } else {
                EventForm.data.beginContainer.val('');
            }
            EventForm._activateObjectSearch();
        },

        /**
         * Sets the objectSearch to either room or inventory objects depending on EventForm.config.type
         */
        _activateObjectSearch: function () {
            EventForm._refreshObjectTable();
            if ("" !== EventForm.data.beginContainer.val() && "" !== EventForm.data.endContainer.val()) {
                var begin = EventForm.data.beginContainer.val();
                var end = EventForm.data.endContainer.val();
                EventForm.data.objectSearch.attr('disabled', false);

                if (EventForm.config.type === "room") {
                    console.log('activate room search');
                    EventForm._typeaheadRoom(EventForm.data.objectSearch, begin, end);
                } else if (EventForm.config.type === "inventory") {
                    console.log('activate inventory search');
                    EventForm._typeaheadInventory(EventForm.data.objectSearch, begin, end);
                }
                EventForm._registerTypeaheadSelect();
            } else {
                EventForm.data.objectSearch.attr('disabled', true);
                EventForm.data.objectSearch.typeahead('destroy');
            }
        },

        _refreshObjectTable: function () {
            //(TODO: add api the get availabilityinformation for each item and highlight unavailable ones
            $(EventForm.data.objectCollection).empty();
        },

        /**
         * Adds Typeahead for room search to an inputfield object
         * @param {object} searchInput
         * @param {string} begin datetime string (yy-mm-ddThh:mm)
         * @param {string} end   datetime string (yy-mm-ddThh:mm)
         */
        _typeaheadRoom: function (searchInput, begin, end) {
            console.log('activate typeahead room. Url: '+ oktolab.typeahead.eventRoomRemoteUrl + '/'+begin+'/'+end);
            searchInput.typeahead([{
                name: 'rent-rooms',
                valueKey: 'name',
                remote: { url: oktolab.typeahead.eventRoomRemoteUrl + '/'+begin+'/'+end },
                template: [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{name}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                header: '<h3>Items</h3>',
                engine: Hogan
            }]);
        },

        /**
         * Adds Typeahead for inventory search to an inputfield object
         * @param {object} searchInput
         * @param {string} begin datetime string (yy-mm-ddThh:mm)
         * @param {string} end   datetime string (yy-mm-ddThh:mm)
         */
        _typeaheadInventory: function (searchInput, begin, end) {
            searchInput.typeahead([{
                name: 'rent-items',
                valueKey: 'name',
                remote: { url: oktolab.typeahead.eventItemRemoteUrl + '/'+begin+'/'+end },
                template: [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{name}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                header: '<h3>Items</h3>',
                engine: Hogan
            }, {
                name:       'rent-sets',
                valueKey:   'name',
                remote: { url: oktolab.typeahead.eventSetRemoteUrl + '/'+begin+'/'+end },
                template: [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{name}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                header: '<h3>Sets</h3>',
                engine: Hogan
            }]);
        },

        /**
         * Sets options of a selectbox of choice to the hours of given date out of given timeblocks
         * @param {string} actualDatumVal
         * @param {array} timeblocks
         * @param {object} selectBoxToFill
         */
        _setHourTimesForSelect: function(actualDatumVal, timeblocks, selectBoxToFill) {
            actualDatumVal = parseInt(actualDatumVal.replace(/-/g, ''));
            var selectedOption = selectBoxToFill.val(); //save old selection
            selectBoxToFill.empty(); // remove old options
            if (timeblocks[actualDatumVal]) {
                selectBoxToFill.append("<option></option>");
                $.each(timeblocks[actualDatumVal], function(key, value) {
                    selectBoxToFill.append($("<option></option>").attr("value", key).text(key));
                    if (selectedOption === key) {
                        selectBoxToFill.val(key); //set prev option if possible.
                    }
                });
            }
        },

        /**
         * Sets the selectable minutes from the timeblocks on a specific hour on a specific day
         * @param {string} actualDatumVal
         * @param {string} actualHourVal
         * @param {array} timeblocks
         * @param {object} selectBoxToFill
         */
        _setMinuteTimesForSelect: function(actualDatumVal, actualHourVal, timeblocks, selectBoxToFill) {
            actualDatumVal = parseInt(actualDatumVal.replace(/-/g, ''));
            var selectedOption = selectBoxToFill.val(); //save old selection
            selectBoxToFill.empty(); // remove old options
            if (timeblocks[actualDatumVal][actualHourVal]) {
                $.each(timeblocks[actualDatumVal][actualHourVal], function(key, value) {
                   selectBoxToFill.append($("<option></option>").attr("value", value).text(value));
                       if (selectedOption === value) {
                           selectBoxToFill.val(value); //set prev option if possible.
                       }
                });
            }
        }
    };

    // Register EventForm
    window.Oktolab.EventForm = Oktolab.EventForm = EventForm;

})(window, Oktolab, jQuery);
