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
            if (!EventForm._loadConfiguration(settings)) {
                return;
            }

            EventForm._setup();
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
                objectCollection:   '.event-objects',
                costUnitSearch:     'costunit-search-field',
                contactSearch:      'contact-search-field',
                beginDate:          'event-beginDate',
                beginTime:          'event-beginTime',
                endDate:            'event-endDate',
                endTime:            'event-endTime',
                hideButtons:        false,
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
            EventForm.data.nameContainer = container.find('input[name*=name]');
            EventForm.data.beginContainer = container.find('input[name*=begin]');
            EventForm.data.endContainer = container.find('input[name*=end]');

            // Hide some form fields
            EventForm.data.costUnitContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.contactContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.nameContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.beginContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.endContainer.closest('div.field-group').addClass('hidden');

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
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config.beginDate }).append(beginLabel);
            var inputDate = $('<input />', { 'id': EventForm.config.beginDate }).addClass('aui-date-picker text event-date');
            var inputTime = $('<select />', { 'id': EventForm.config.beginTime }).addClass('select event-select');

            if (EventForm.data.container.find('input[id*=_begin]').val() !== '') {
                var datetime = new Date(EventForm.data.container.find('input[id*=_begin]').val());
                inputDate.val($.datepicker.formatDate('yy-mm-dd',datetime));
                inputTime.append(
                    $("<option></option>").text(Oktolab.leadingZero(datetime.getHours().toString())
                    +':'+Oktolab.leadingZero(datetime.getMinutes().toString())
                    +':'+Oktolab.leadingZero(datetime.getSeconds().toString()))
                );
            }

            fieldGroup.append(label).append(inputDate).append(inputTime);
            EventForm.data.container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data.beginDate = inputDate;
            EventForm.data.beginTime = inputTime;
        },

        _renderEndTimeFields: function () {
            var endLabel = EventForm.data.container.find('label[for*=_end]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config.beginDate }).append(endLabel);
            var inputDate = $('<input />', { 'id': EventForm.config.endDate }).addClass('aui-date-picker text event-date').val($.datepicker.formatDate('yy-mm-dd',datetime));
            var inputTime = $('<select />', { 'id': EventForm.config.endTime }).addClass('select event-select');

            if (EventForm.data.container.find('input[id*=_end]').val() !== '') {
                var datetime = new Date(EventForm.data.container.find('input[id*=_end]').val());
                inputDate.val($.datepicker.formatDate('yy-mm-dd',datetime));
                inputTime.append(
                    $("<option></option>").text(Oktolab.leadingZero(datetime.getHours().toString())
                    +':'+Oktolab.leadingZero(datetime.getMinutes().toString())
                    +':'+Oktolab.leadingZero(datetime.getSeconds().toString()))
                );
            }


            fieldGroup.append(label).append(inputDate).append(inputTime);
            EventForm.data.container.find('fieldset:first').prepend(fieldGroup);
            EventForm.data.endDate = inputDate;
            EventForm.data.endTime = inputTime;
        },

        _registerBeginTimeListener: function () {
            EventForm.data.beginTime.on('mouseenter', function (){
                var date = EventForm.data.beginDate.val();
                date = parseInt(date.replace(/-/g, ''));
                if (date != EventForm.data.beginTime.data('selected-date')) {
                    EventForm.data.beginTime.data('selected-date', date);
                    var selectBox = EventForm.data.beginTime;
                    selectBox.empty(); // remove old options

                    var timeblocks = EventForm.data.beginContainer.data('timeblockstarts');
                    if (timeblocks) {
                        selectBox.append("<option></option>");
                        $.each(timeblocks[date], function(key, value) {
                            selectBox.append($("<option></option>")
                             .attr("value", value).text(value));
                        });
                    }
                }
            });
        },

        _registerEndTimeListener: function () { //redundant!
            EventForm.data.endTime.on('mouseenter', function (){
                var date = EventForm.data.endDate.val();
                if (date != EventForm.data.endTime.data('selected-date')) {
                    EventForm.data.endTime.data('selected-date', date);
                    date = parseInt(date.replace(/-/g, ''));

                    var selectBox = EventForm.data.endTime;
                    selectBox.empty(); // remove old options

                    var timeblocks = EventForm.data.endContainer.data('timeblockends');
                    if (timeblocks) {
                        selectBox.append("<option></option>");
                        $.each(timeblocks[date], function(key, value) {
                            selectBox.append($("<option></option>")
                             .attr("value", value).text(value));
                        });
                    }
                }
            });
        },

        _registerBeginTimeChange: function() {
            EventForm.data.beginTime.on('change', function (){
               var date = EventForm.data.beginDate.val();
               var time = EventForm.data.beginTime.val();
               EventForm.data.beginContainer.val(date+'T'+time);
               EventForm._checkTimesForSearch();
            });
        },

        _registerEndTimeChange: function() { //redundant!
            EventForm.data.endTime.on('change', function (){
               var date = EventForm.data.endDate.val();
               var time = EventForm.data.endTime.val();
               EventForm.data.endContainer.val(date+'T'+time);
               EventForm._checkTimesForSearch();
            });
        },

        _checkTimesForSearch: function() {
            if (EventForm.data.beginContainer.val() !== "" &&
                EventForm.data.endContainer.val() !== "") {
                var beginDateTime = new Date(EventForm.data.beginContainer.val());
                var endDateTime = new Date(EventForm.data.endContainer.val());

                oktolab.activateTypeaheadSearch($.datepicker.formatDate('yy-mm-dd', beginDateTime),
                $.datepicker.formatDate('yy-mm-dd', endDateTime));
            }
        }
    };

    // Register EventForm
    window.Oktolab.EventForm = Oktolab.EventForm = EventForm;

})(window, Oktolab, jQuery);
