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
                beginSearch:        '',
                endSearch:          '',
            };

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

            // Hide some Form fields
            EventForm.data.costUnitContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.contactContainer.closest('div.field-group').addClass('hidden');
            EventForm.data.nameContainer.closest('div.field-group').addClass('hidden');

            // Render Typeahead's
            EventForm._renderContactField();
            EventForm._renderCostUnitField();

            // Register Listeners
            EventForm._registerCostUnitListener();
        },

        _renderCostUnitField: function () {
            var costUnitLabel = EventForm.data.container.find('label[for*=costunit]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config.costUnitSearch }).append(costUnitLabel);
            var input = $('<input />', { 'id': EventForm.config.costUnitSearch }).addClass('text');

            fieldGroup.append(label).append(input);
            EventForm.data.container.find('fieldset:first-child').prepend(fieldGroup);
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
        },

        _renderContactField: function () {
            var contactLabel = EventForm.data.container.find('label[for*=contact]').html();
            var fieldGroup = $('<div />').addClass('field-group');
            var label = $('<label />', { 'for': EventForm.config.contactSearch }).append(contactLabel);
            var input = $('<input />', { 'id': EventForm.config.contactSearch, 'disabled': 'disabled' }).addClass('text');

            fieldGroup.append(label).append(input);
            EventForm.data.container.find('fieldset:first-child').prepend(fieldGroup);
            EventForm.data.contactSearchContainer = input;
        },

        _registerCostUnitListener: function () {
            EventForm.data.costUnitSearchContainer.on('typeahead:selected', function(e, datum) {
                var remoteUrl = oktolab.typeahead.costunitcontactRemoteUrl.replace('__id__', datum.id);

                EventForm.data.costUnitContainer.val(datum.id);
                EventForm.data.costUnitContainer.val(datum.name);

                EventForm.data.contactSearchContainer.attr('disabled', false).typeahead({
                    name:       'costunit-contacts-' + datum.id,
                    valueKey:   'name',
                    remote :    remoteUrl,
                    template: [
                        '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
                        '<p class="tt-object-name">{{name}}</p>'
                    ].join(''),
                    engine: Hogan
                });
            });
        }
    };

    // Register EventForm
    window.Oktolab.EventForm = Oktolab.EventForm = EventForm;

})(window, Oktolab, jQuery);