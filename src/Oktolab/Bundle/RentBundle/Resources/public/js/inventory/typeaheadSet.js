AJS.$(document).ready(function (){

    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').typeahead({
        name:       'set-items',
        valueKey:   'name',
        prefetch:  { url: oktolab.typeahead.itemPrefetchUrl, ttl: 60000 },
        limit:      10,
        template: [
            '<span class="aui-icon aui-icon-small aui-iconfont-devtools-file">Object</span>',
            '<p class="tt-object-name">{{name}}</p>',
            '<p class="tt-object-addon">{{barcode}}</p>'
        ].join(''),
        engine: Hogan
    });

    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').on('typeahead:selected', function (e, datum) {
        AJS.$('.appendable').append(AJS.$('<tr id=row_'+datum.name+'>').load(loadItemRowUrl+datum.name));
        jQuery(this).typeahead('setQuery', '');
        AJS.$('fieldset').append('<input class="addableitem" type="hidden" id="oktolab_bundle_rentbundle_inventory_settype_itemsToAdd_'+datum.name+'_id" name="oktolab_bundle_rentbundle_inventory_settype[itemsToAdd]['+datum.name+'][id]" required="required" />');
    });

    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').on('click', '.inventory_set_remove_item', function(event){
        event.preventDefault();
        AJS.$('#row_'+this.id).remove();
        AJS.$('#oktolab_bundle_rentbundle_inventory_settype_itemsToAdd_'+this.id+'_id').remove();
    });
});
