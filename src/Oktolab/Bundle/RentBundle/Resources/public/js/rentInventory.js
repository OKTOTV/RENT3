var collectionHolder = AJS.$('.event-objects');


/**
 * Adds EventObjects to an event form
 *
 * @param jQueryObject   collectionHolder
 * @param TypeaheadDatum datum
 * @returns {undefined}
 */
oktolab.addTypeaheadObjectToEventForm = function(collectionHolder, datum) {
    var index     = collectionHolder.data('index');

    var template = Hogan.compile(collectionHolder.data('prototype'));
    var output = template.render(AJS.$.extend(datum, { index: index +1 }));

    var form = collectionHolder.closest('form');
    var value = datum.value.split(':');

    form.append(
            AJS.$('<input />', {
                class: 'hidden',
                id: 'oktolabrentbundle_event_objects_' + index + '_type',
                name: 'oktolabrentbundle_event[objects][' + index + '][type]',
                value: value[0],
            })
    );

    form.append(
            AJS.$('<input />', {
                class: 'hidden',
                id: 'oktolabrentbundle_event_objects_' + index + '_object',
                name: 'oktolabrentbundle_event[objects][' + index + '][object]',
                value: value[1],
            })
    );

    collectionHolder.data('index', index + 1);
    collectionHolder.data('objects').push(datum.value);
    collectionHolder.append(output);
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
                'name': 'Blackmagic Kamera',
                'description': 'Kamera auch zum filmen',
                'barcode': 'BCDEF',
                'value': 'item:2',
                'tokens': [ 'BCDEF', 'Blackmagic Kamera', 'Kamera' ]
            },
        ],
         template: [
            '<p class="repo-language">{{barcode}}</p>',
            '<p class="repo-name">{{name}}</p>',
            '<p class="repo-description">{{description}}</p>'
        ].join(''),
        engine: Hogan
    });

    collectionHolder.data('index', collectionHolder.find(':input').length);
    collectionHolder.data('objects', []);

    AJS.$('#inventory-search-field').on('typeahead:selected', function (e, datum) {
        var objects = collectionHolder.data('objects');

        if (-1 === AJS.$.inArray(datum.value, objects)) {
            oktolab.addTypeaheadObjectToEventForm(AJS.$('.event-objects'), datum);
        } else {
            console.log(AJS.format('Already added element "{0}"!', datum.value));
        }
    });
});