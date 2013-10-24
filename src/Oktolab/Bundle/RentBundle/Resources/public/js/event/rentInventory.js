var collectionHolder = AJS.$('.event-objects');

/**
 * Adds EventObjects to an event form
 *
 * @param jQueryObject   collectionHolder
 * @param TypeaheadDatum datum
 */
oktolab.addTypeaheadObjectToEventForm = function(collectionHolder, datum) {
    var index    = collectionHolder.data('index');
    var template = Hogan.compile(collectionHolder.data('prototype'));
    var output   = template.render(AJS.$.extend(datum, { index: index + 1 }));
    var value    = datum.value.split(':');

    var fieldGroup = AJS.$('<div />', {
        class: 'field-group',
        'data-object': datum.value
    }).appendTo(collectionHolder.closest('form'));

    fieldGroup.append(
            AJS.$('<input />', {
                class: 'hidden',
                id: 'OktolabRentBundle_Event_Form_' + index + '_type',
                name: 'OktolabRentBundle_Event_Form[objects][' + index + '][type]',
                value: value[0],
            })
    );

    fieldGroup.append(
            AJS.$('<input />', {
                class: 'hidden',
                id: 'OktolabRentBundle_Event_Form_objects_' + index + '_object',
                name: 'OktolabRentBundle_Event_Form[objects][' + index + '][object]',
                value: value[1],
            })
    );

    collectionHolder.data('index', index + 1);
    collectionHolder.append(output);
};

/**
 * Removes EventObject from Event Form
 *
 * @param jQueryEvent event
 */
oktolab.removeEventObjectFromEventForm = function(event) {
    var object = AJS.$(this).data('value');
    var form   = collectionHolder.closest('form');

    AJS.$(event.target).closest('tr').remove();
    form.find('div[data-object="' + object + '"]').remove();
};

AJS.$(document).ready(function() {
    AJS.$('#inventory-search-field').typeahead([{
        name: 'rent-items',
        valueKey: 'name',
        prefetch: { url: oktolab.typeahead.itemPrefetchUrl, ttl: 60000 },
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
        prefetch:   { url: oktolab.typeahead.setPrefetchUrl, ttl: 60000 },
        template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join(''),
        header: '<h3>Sets</h3>',
        engine: Hogan
    }]);

    collectionHolder.data('index', collectionHolder.find(':tr').length);

    jQuery('#inventory-search-field').on('typeahead:selected', function (e, datum) {
        var form = collectionHolder.closest('form');

        if (0 === form.find('div[data-object="' + datum.value + '"]').length) {
            oktolab.addTypeaheadObjectToEventForm(AJS.$('.event-objects'), datum);
        }

        jQuery(this).typeahead('setQuery', '');
    });

    collectionHolder.on('click', '.remove-object', oktolab.removeEventObjectFromEventForm);
});

Oktolab.EventForm.init({ container: '#rent-inventory-form > form' });