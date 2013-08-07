(function(Oktolab) {
    Oktolab.addTypeaheadRow = function(collection, entity) {
        var index    = collection.data('index');
        var template = Hogan.compile(collection.data('prototype'));
        var output   = template.render(jQuery.extend(entity, { index: index + 1 }));

        collection.append(output);
        collection.data('index', index + 1);

        return collection;
    }
})(window.Oktolab);

//oktolab.addTypeaheadHiddenInput()

//(function() {
//
//
//});

jQuery(document).ready(function ($) {
    var searchItemsField = $('#oktolab_rentbundle_inventory_set_searchItems');
    var form             = searchItemsField.closest('form');
    var collectionHolder = $('.set-items', form);

    collectionHolder.data('index', 0);
//    console.log(Oktolab);

    searchItemsField.typeahead({
        name:       'set-items',
        valueKey:   'name',
        prefetch:  { url: oktolab.typeahead.itemPrefetchUrl, ttl: 60000 },
        template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join(''),
        engine: Hogan
    });

    searchItemsField.on('typeahead:selected', function (e, datum) {
        collectionHolder = Oktolab.addTypeaheadRow(collectionHolder, datum);
//        oktolab.addTypeaheadHiddenInput(form, datum);
//        oktolab.addTypeaheadRow(e, datum);
//        oktolab.addTypeaheadHiddenInput(datum, );
    });

//    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').on('typeahead:selected', function (e, datum) {
//        AJS.$('.appendable').append(AJS.$('<tr id=row_'+datum.name+'>').load(loadItemRowUrl+datum.name));
//        jQuery(this).typeahead('setQuery', '');
//        AJS.$('fieldset').append('<input class="addableitem" type="hidden" id="oktolab_bundle_rentbundle_inventory_settype_itemsToAdd_'+datum.name+'_id" name="oktolab_bundle_rentbundle_inventory_settype[itemsToAdd]['+datum.name+'][id]" required="required" />');
//    });
//
//    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').on('click', '.inventory_set_remove_item', function(event){
//        event.preventDefault();
//        AJS.$('#row_'+this.id).remove();
//        AJS.$('#oktolab_bundle_rentbundle_inventory_settype_itemsToAdd_'+this.id+'_id').remove();
//    });
})(AJS.$);
