/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
AJS.$(document).ready(function (){

    AJS.$.getJSON('http://localhost/rent3/web/app_dev.php/inventory/set/search.json', function(data) {
        console.log(data);
    });

    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').on('typeahead:selected', function (e, datum) {
        AJS.$('.appendable').append(AJS.$('<tr id='+datum.name+'>').load("../item/getItem/"+datum.name));
        this.value = '';
        AJS.$('form').append('<input class="text" type="hidden" id="oktolab_bundle_rentbundle_inventory_settype_itemsToAdd_'+datum.name+'_id" name="oktolab_bundle_rentbundle_inventory_settype[itemsToAdd]['+datum.name+'][id]" required="required" />')

    });

    AJS.$('#oktolab_bundle_rentbundle_inventory_settype_searchItems').typeahead({
        name: "items",
        prefetch:{ url: "search.json", ttl: 300000 },
        limit: 10
    });

});
