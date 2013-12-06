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
            EventForm.config = [];
            EventForm.data = [];
            // load configuration, abort on error
            if (EventForm._loadConfiguration(settings, 'newInventory')) {
                EventForm._setup('newInventory');
            }

            var roomSettings = {
                container:          '#room_form',
                objectSearch:       '#room-search-field',
                objectCollection:   '#room-event-objects',
                beginDate:          '.room-beginDate',
                endDate:            '.room-endDate',
                beginHour:          'room-beginHour',
                type:               'room',
            };

            if (EventForm._loadConfiguration(roomSettings, 'newRoom')) {
                EventForm._setup('newRoom');
            }

            var editInventorySettings = {
                container:          '#inventory-edit-form',
                beginDate:          '.inventory-edit-beginDate',
                beginHour:          'inventory-edit-beginHour',
                endDate:            '.inventory-edit-endDate',
                endHour:            'inventory-edit-endHour',
                objectCollection:   '#inventory-edit-event-objects',
                objectSearch:       '#inventory-edit-search-field'
            };

            if (EventForm._loadConfiguration(editInventorySettings, 'editInventory')) {
                EventForm._setup('editInventory');
                EventForm._activateObjectSearch('editInventory', false);
            }

            var editRoomSettings = {
                container:          '#room-edit-form',
                objectSearch:       '#room-edit-search-field',
                objectCollection:   '#room-edit-event-objects',
                beginDate:          '.room-edit-beginDate',
                beginHour:          'room-edit-beginHour',
                endDate:            '.room-edit-endDate',
                endHour:            'room-editendHour',
                type:               'room',
            };

            if (EventForm._loadConfiguration(editRoomSettings, 'editRoom')) {
                EventForm._setup('editRoom');
                EventForm._activateObjectSearch('editRoom', false);
            }
        },

        /**
         * Loads configuration. Returns false if something went wrong.
         *
         * @param {object} settings     key-value array
         *
         * @returns {Boolean}
         */
        _loadConfiguration: function (settings, id) {
            EventForm.config[id] = {
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
            };

            // TODO: this can lead to unusual behaviour, use: $.extend({}, EventForm.config[id], settings); instead
            $.extend(EventForm.config[id], settings);

            var container = $(EventForm.config[id].container);
            if (0 === container.length) {
                window.console.log('EventForm >> Container not found: ' + EventForm.config[id].container);
                return false;
            }

            EventForm.data[id] = {
                container:          container,
                objectSearch:       container.find(EventForm.config[id].objectSearch),
                objectCollection:   container.find(EventForm.config[id].objectCollection),
            };

            return true;    // configuration successfully loaded
        },

        _setup: function (id) {
            var container = EventForm.data[id].container;

            EventForm.data[id].costUnitContainer = container.find('select[name*=costunit]');
            EventForm.data[id].contactContainer = container.find('select[name*=contact]');
            EventForm.data[id].typeContainer = container.find('select[name*=type]');
            EventForm.data[id].nameContainer = container.find('input[name*=name]');
            EventForm.data[id].beginContainer = $(EventForm.config[id].beginDate);
            EventForm.data[id].endContainer = $(EventForm.config[id].endDate);
            EventForm.data[id].objectSearch = $(EventForm.config[id].objectSearch);

            // Hide some form fields
            EventForm.data[id].costUnitContainer.closest('div.field-group').addClass('hidden');
            EventForm.data[id].contactContainer.closest('div.field-group').addClass('hidden');
            EventForm.data[id].nameContainer.closest('div.field-group').addClass('hidden');
            EventForm.data[id].beginContainer.closest('div').addClass('hidden');
            EventForm.data[id].endContainer.closest('div').addClass('hidden');
            EventForm.data[id].typeContainer.closest('div.field-group').addClass('hidden');

            if (EventForm.config[id].hideButtons) {
                container.find('.buttons-container').addClass('hidden');
            }

            // Render form inputs
            EventForm._renderEndTimeFields(id);
            EventForm._renderBeginTimeFields(id);
            EventForm._renderContactField(id);
            EventForm._renderCostUnitField(id);

            // Register Listeners
            EventForm._registerCostUnitListener(id);
            EventForm._registerContactListener(id);
            EventForm._registerBeginTimeListener(id);
            EventForm._registerEndTimeListener(id);
            EventForm._registerBeginTimeChange(id);
            EventForm._registerEndTimeChange(id);
            EventForm._registerRemoveClick(id);
        },

        _renderCostUnitField: function (id) {
            var costUnitLabel = EventForm.data[id].container.find('label[for*=costunit]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config[id].costUnitSearch }).append(costUnitLabel);
            var input = $('<input />', { 'id': EventForm.config[id].costUnitSearch }).addClass('text');
            var value = EventForm.data[id].costUnitContainer.data('name');

            fieldGroup.append(label).append(input);
            EventForm.data[id].container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data[id].costUnitSearchContainer = input;

            EventForm.data[id].costUnitSearchContainer.typeahead({
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
                EventForm.data[id].costUnitSearchContainer.typeahead('setQuery', value);
            }
        },

        _renderContactField: function (id) {
            var contactLabel = EventForm.data[id].container.find('label[for*=contact]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config[id].contactSearch }).append(contactLabel);
            var input = $('<input />', { 'id': EventForm.config[id].contactSearch, 'disabled': 'disabled' }).addClass('text');
            var value = EventForm.data[id].contactContainer.data('name');

            if ('undefined' !== typeof(value) && 0 !== value.length) {
                input.attr('disabled', false).val(value);
            }

            fieldGroup.append(label).append(input);
            EventForm.data[id].container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data[id].contactSearchContainer = input;
        },

        _registerCostUnitListener: function (id) {
            EventForm.data[id].costUnitSearchContainer.on('typeahead:selected', function(e, datum) {
                var remoteUrl = oktolab.typeahead.costunitcontactRemoteUrl.replace('__id__', datum.id);

                // Set Values to hidden input fields
                EventForm.data[id].costUnitContainer.val(datum.id);
                EventForm.data[id].nameContainer.val(datum.name);

                // load new Typeahead on Contacts
                EventForm.data[id].contactSearchContainer.typeahead('destroy');
                EventForm.data[id].contactSearchContainer.attr('disabled', false).typeahead({
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

        _registerContactListener: function (id) {
            EventForm.data[id].contactSearchContainer.on('typeahead:selected', function (e, datum) {
                EventForm.data[id].contactContainer.val(datum.id);
            });
        },

        _renderBeginTimeFields: function (id) {
            var beginLabel = EventForm.data[id].container.find('label[for*=_begin]').html();
            var label = $('<label />', { 'for': EventForm.config[id].beginDate }).append(beginLabel);
            var fieldGroup = $('<div />').addClass('field-group');

            var inputDate = $('<input />', { 'id': EventForm.config[id].beginDate }).addClass('aui-date-picker text event-date');
            var inputHour = $('<select />').addClass('select event-select ' + EventForm.config[id].beginHour);
            var inputMinute = $('<select />').addClass('select event-select');

            if ('' !== EventForm.data[id].container.find('input[id*=_begin]').val()) {
                var datetime = new Date(EventForm.data[id].container.find('input[id*=_begin]').val());
                inputDate.val($.datepicker.formatDate('yy-mm-dd',datetime));
                inputHour.append($("<option></option>").text(Oktolab.leadingZero(datetime.getHours().toString())));
                inputMinute.append($("<option></option>").text(Oktolab.leadingZero(datetime.getMinutes().toString())));
            }

            fieldGroup.append(label).append(inputDate).append(inputHour).append(inputMinute);
            EventForm.data[id].container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data[id].beginDate = inputDate;
            EventForm.data[id].beginHour = inputHour;
            EventForm.data[id].beginMinute = inputMinute;
        },

        _renderEndTimeFields: function (id) {
            var endLabel = EventForm.data[id].container.find('label[for*=_end]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config[id].beginDate }).append(endLabel);

            var inputDate = $('<input />', { 'id': EventForm.config[id].endDate }).addClass('aui-date-picker text event-date').val($.datepicker.formatDate('yy-mm-dd',datetime));
            var inputHour = $('<select />').addClass('select event-select ' + EventForm.config[id].endHour);
            var inputMinute = $('<select />').addClass('select event-select');

            if ('' !== EventForm.data[id].container.find('input[id*=_end]').val()) {
                var datetime = new Date(EventForm.data[id].container.find('input[id*=_end]').val());
                inputDate.val($.datepicker.formatDate('yy-mm-dd',datetime));
                inputHour.append($("<option></option>").text(Oktolab.leadingZero(datetime.getHours().toString())));
                inputMinute.append($("<option></option>").text(Oktolab.leadingZero(datetime.getMinutes().toString())));
            }

            fieldGroup.append(label).append(inputDate).append(inputHour).append(inputMinute);
            EventForm.data[id].container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data[id].endDate = inputDate;
            EventForm.data[id].endHour = inputHour;
            EventForm.data[id].endMinute = inputMinute;
        },

        /**
         * Sets the begin hour/minute selectboxes depending on selected date/hour
         */
        _registerBeginTimeListener: function (id) {
            EventForm.data[id].beginHour.on('mouseenter', function (){
                var date = EventForm.data[id].beginDate.val();
                var selectBox = EventForm.data[id].beginHour;
                var timeblocks = EventForm.data[id].beginContainer.data('timeblock-times');

                EventForm._setHourTimesForSelect(date, timeblocks, selectBox);
            });
            EventForm.data[id].beginHour.on('change', function () {
                var date = EventForm.data[id].beginDate.val();
                var hour = EventForm.data[id].beginHour.val();
                var selectBox = EventForm.data[id].beginMinute;
                var timeblocks = EventForm.data[id].beginContainer.data('timeblock-times');

                EventForm._setMinuteTimesForSelect(date, hour, timeblocks, selectBox);
            });
        },

        /**
         * Sets the end hour/minute selectboxes depending on selected date/hour
         */
        _registerEndTimeListener: function (id) {
            EventForm.data[id].endHour.on('mouseenter', function (){
                var date = EventForm.data[id].endDate.val();
                var selectBox = EventForm.data[id].endHour;
                var timeblocks = EventForm.data[id].endContainer.data('timeblock-times');

                EventForm._setHourTimesForSelect(date, timeblocks, selectBox);
            });
            EventForm.data[id].endHour.on('change', function() {
                var date = EventForm.data[id].endDate.val();
                var hour = EventForm.data[id].endHour.val();
                var selectBox = EventForm.data[id].endMinute;
                var timeblocks = EventForm.data[id].endContainer.data('timeblock-times');

                EventForm._setMinuteTimesForSelect(date, hour, timeblocks, selectBox);
            });
        },

        /**
         * Sets the Event begin input on newest changes of hour or minute selectboxes
         */
        _registerBeginTimeChange: function(id) {
            EventForm.data[id].beginHour.on('change', function (){
               EventForm._setEventBeginInputTime(id);
            });
            EventForm.data[id].beginMinute.on('change', function(){
                EventForm._setEventBeginInputTime(id);
            })
        },

        /**
         * Sets the Event end input on newest changes of hour or minute selectboxes
         */
        _registerEndTimeChange: function(id) { //redundant!
            EventForm.data[id].endHour.on('change', function (){
                EventForm._setEventEndInputTime(id);
            });
            EventForm.data[id].endMinute.on('change', function(){
               EventForm._setEventEndInputTime(id);
            });
        },

        /**
         * register the selection of a typeahead thing and act accordingly
         */
        _registerTypeaheadSelect: function(id) {
            EventForm.data[id].objectSearch.on('typeahead:selected', function (e, datum) {
                var form = EventForm.data[id].objectCollection.closest('form');
                if (0 === form.find('div[data-object="' + datum.value + '"]').length) {
//                    oktolab.addTypeaheadObjectToEventForm(EventForm.data[id].objectCollection, datum);

                    var index    = EventForm.data[id].objectCollection.data('index');
                    var template = Hogan.compile(EventForm.data[id].objectCollection.data('prototype'));
                    var output   = template.render(AJS.$.extend(datum, { index: index + 1 }));

                    EventForm.data[id].objectCollection.data('index', index + 1);
                    EventForm.data[id].objectCollection.append(output);

                }
                jQuery(this).typeahead('setQuery', '');
            });
        },

        /**
         *
         * @param {type} id
         * @returns {undefined}
         */
        _registerRemoveClick: function(id) {
            $(EventForm.data[id].objectCollection).on('click', 'a.remove', function (e) {
                e.preventDefault();
                $(e.currentTarget).closest('tr').remove();
            });
        },

        /**
         * Sets Evenform input for begin and tries to activate the object search
         */
        _setEventBeginInputTime: function (id) {
            var date = EventForm.data[id].beginDate.val();
            var hour = EventForm.data[id].beginHour.val();
            var min  = EventForm.data[id].beginMinute.val();
            if (date && hour && min) {
                var datetime = date+'T'+hour+':'+min;
                EventForm.data[id].beginContainer.val(datetime);
            } else {
                EventForm.data[id].beginContainer.val('');
            }
            EventForm._activateObjectSearch(id, true);
        },

        /**
         * Sets Eventform input for end and tries to activate the object search
         */
        _setEventEndInputTime: function(id) {
            var date = EventForm.data[id].endDate.val();
            var hour = EventForm.data[id].endHour.val();
            var min  = EventForm.data[id].endMinute.val();
            if (date && hour && min) {
                var datetime = date+'T'+hour+':'+min;
                EventForm.data[id].endContainer.val(datetime);
            } else {
                EventForm.data[id].beginContainer.val('');
            }
            EventForm._activateObjectSearch(id, true);
        },

        /**
         * Sets the objectSearch to either room or inventory objects depending on EventForm.config[id].type
         */
        _activateObjectSearch: function (id, clearTable) {
            if (clearTable) { EventForm._refreshObjectTable(id); }

            if ("" !== EventForm.data[id].beginContainer.val() && "" !== EventForm.data[id].endContainer.val()) {
                var begin = EventForm.data[id].beginContainer.val();
                var end = EventForm.data[id].endContainer.val();
                EventForm.data[id].objectSearch.attr('disabled', false);

                if (EventForm.config[id].type === "room") {
                    console.log('activate room search');
                    EventForm._typeaheadRoom(EventForm.data[id].objectSearch, begin, end);
                } else if (EventForm.config[id].type === "inventory") {
                    console.log('activate inventory search');
                    EventForm._typeaheadInventory(EventForm.data[id].objectSearch, begin, end);
                }
                EventForm._registerTypeaheadSelect(id);
            } else {
                EventForm.data[id].objectSearch.attr('disabled', true);
                EventForm.data[id].objectSearch.typeahead('destroy');
            }
        },

        _refreshObjectTable: function (id) {
            console.log(id);
            //(TODO: add api the get availabilityinformation for each item and highlight unavailable ones
            $(EventForm.data[id].objectCollection).empty();
        },

        /**
         * Adds Typeahead for room search to an inputfield object
         * @param {object} searchInput
         * @param {string} begin datetime string (yy-mm-ddThh:mm)
         * @param {string} end   datetime string (yy-mm-ddThh:mm)
         */
        _typeaheadRoom: function (searchInput, begin, end) {
            searchInput.typeahead([{
                name: 'rent-rooms',
                valueKey: 'name',
                remote: { url: oktolab.typeahead.eventRoomRemoteUrl + '/'+begin+'/'+end },
                template: [
                    '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                    '<p class="tt-object-name">{{name}}</p>',
                    '<p class="tt-object-addon">{{barcode}}</p>'
                ].join(''),
                header: '<h3>Räume</h3>',
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
