oktolab.addTypeaheadObjectToEventForm = function(form, datum) {
    form.append('asdfasdf - ' + datum.value);
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



    AJS.$('#inventory-search-field').on('typeahead:selected', function (e, datum) {
        oktolab.addTypeaheadObjectToEventForm(AJS.$('.event-objects'), datum);
//        console.log(e, datum);
    });
});