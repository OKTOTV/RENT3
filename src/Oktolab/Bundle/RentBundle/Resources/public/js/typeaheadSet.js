/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
AJS.$(document).ready(function (){

    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').typeahead({
        name: "items",
        prefetch:{ url: prefetchUrl, ttl: 60000 },
        limit: 10
    });

    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').on('typeahead:selected', function (e, datum) {
        AJS.$('.appendable').append(AJS.$('<tr id=row_'+datum.name+'>').load(loadItemRowUrl+datum.name));
        this.value = '';
        AJS.$('fieldset').append('<input class="addableitem" type="hidden" id="oktolab_bundle_rentbundle_inventory_settype_itemsToAdd_'+datum.name+'_id" name="oktolab_bundle_rentbundle_inventory_settype[itemsToAdd]['+datum.name+'][id]" required="required" />');
    });

    AJS.$(document).on("click", '.inventory_set_remove_item', function(event){
        event.preventDefault();
        AJS.$('#row_'+this.id).remove();
        AJS.$('#oktolab_bundle_rentbundle_inventory_settype_itemsToAdd_'+this.id+'_id').remove();
    });
//
//    AJS.$(document).on("click", '.inventory_set_remove_attached_item', function(event) {
//        event.preventDefault();
//        AJS.$('#row_'+this.id).remove();
//    });
});
