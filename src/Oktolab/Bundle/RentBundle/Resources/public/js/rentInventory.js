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
    var form     = collectionHolder.closest('form');
    var value    = datum.value.split(':');

    var fieldGroup = AJS.$('<div />', {
        class: 'field-group',
        'data-object': datum.value
    }).appendTo(form);

    fieldGroup.append(
            AJS.$('<input />', {
                class: 'hidden',
                id: 'oktolabrentbundle_event_objects_' + index + '_type',
                name: 'oktolabrentbundle_event[objects][' + index + '][type]',
                value: value[0],
            })
    );

    fieldGroup.append(
            AJS.$('<input />', {
                class: 'hidden',
                id: 'oktolabrentbundle_event_objects_' + index + '_object',
                name: 'oktolabrentbundle_event[objects][' + index + '][object]',
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
    var index  = collectionHolder.data('index');

    AJS.$(event.target).closest('tr').remove();
    form.find('div[data-object="' + object + '"]').remove();
    collectionHolder.data('index', index - 1);
};


AJS.$(document).ready(function() {
    AJS.$('#inventory-search-field').typeahead({
        name: 'items',
        valueKey: 'name',
        local: [
            {
                'name': 'JVC Kamera',
                'description': 'Kamera zum filmen',
                'barcode': 'A5DF1',
                'value': 'item:1',
                'tokens': [ 'A5DF1', 'JVC Kamera', 'Kamera' ]
            },
            {
                'name': 'JVC Kamera22',
                'description': 'Kamera zum filmen',
                'barcode': 'A5DF122',
                'value': 'item:3',
                'tokens': [ 'A5DF122', 'JVC Kamera', 'Kamera' ]
            },
            {
                'name': 'Blackmagic Kamera',
                'description': 'Kamera auch zum filmen',
                'barcode': 'BCDEF',
                'value': 'item:2',
                'tokens': [ 'BCDEF', 'Blackmagic Kamera', 'Kamera' ]
            },
        ],
         template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join(''),
        engine: Hogan
    });

    collectionHolder.data('index', collectionHolder.find(':tr').length);
    collectionHolder.data('objects', []);

    AJS.$('#inventory-search-field').on('typeahead:selected', function (e, datum) {
        var form = collectionHolder.closest('form');

        if (0 === form.find('div[data-object="' + datum.value + '"]').length) {
            oktolab.addTypeaheadObjectToEventForm(AJS.$('.event-objects'), datum);
        }

        jQuery(this).typeahead('setQuery', '');
    });

    collectionHolder.on('click', '.remove-object', oktolab.removeEventObjectFromEventForm);

createRentForm.gotoPanel(0);
    createRentForm.show();
});