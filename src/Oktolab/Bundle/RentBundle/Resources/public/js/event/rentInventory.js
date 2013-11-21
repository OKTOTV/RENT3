var collectionHolder = AJS.$('.event-objects');

/**
 * Adds EventObjects to an event form
 *
 * @param jQueryObject   collectionHolder
 * @param TypeaheadDatum datum
 */
oktolab.addTypeaheadObjectToEventForm = function(collectionHolder, datum) {
    console.log('add item');
    var index    = collectionHolder.data('index');
    var template = Hogan.compile(collectionHolder.data('prototype'));
    var output   = template.render(AJS.$.extend(datum, { index: index + 1 }));

    collectionHolder.data('index', index + 1);
    collectionHolder.append(output);
};

/**
 * Removes EventObject from Event Form
 *
 * @param jQueryEvent event
 */
oktolab.removeEventObjectFromEventForm = function(object) {
//    var object = AJS.$(this).data('value');
//    var form   = collectionHolder.closest('form');

    AJS.$(object).closest('tr').remove();
//    form.find('div[data-object="' + object + '"]').remove();
};

AJS.$(document).ready(function() {
    AJS.$('#inventory-search-field').typeahead([{
        name: 'rent-items',
        valueKey: 'name',
        prefetch: { url: oktolab.typeahead.itemPrefetchUrl, ttl: 60000 },
        remote: { url: oktolab.typeahead.itemRemoteUrl },
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
        remote: { url: oktolab.typeahead.setRemoteUrl },
        template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join(''),
        header: '<h3>Sets</h3>',
        engine: Hogan
    }]);

    jQuery('#inventory-search-field').on('typeahead:selected', function (e, datum) {
        var form = collectionHolder.closest('form');

        if (0 === form.find('div[data-object="' + datum.value + '"]').length) {
            oktolab.addTypeaheadObjectToEventForm(AJS.$('.event-objects'), datum);
        }

        jQuery(this).typeahead('setQuery', '');
    });

    collectionHolder.on('click', 'a.remove', function (e) {
        e.preventDefault();
        oktolab.removeEventObjectFromEventForm(e.currentTarget);
    });
});

Oktolab.EventForm.init({ container: '#rent-inventory-form > form', hideButtons: true });
Oktolab.EventForm.init({ container: '#event-form' });