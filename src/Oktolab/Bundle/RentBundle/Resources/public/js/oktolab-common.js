(function(window) {

    /**
     * Appends a data-prototype (from collection) to the collection by using Hogan templating engine
     * entity will be extended with index (collection data-index)
     *
     * @param {jQuery} collection     use a jQuery wrapped set
     * @param {object} entity         typically a typeahead datum
     *
     * @returns {collection|unresolved}
     */
    Oktolab.appendPrototypeTemplate = function(collection, entity) {
        var index = collection.data('index');
        var prototype = collection.data('prototype');

        // Abort if data-prototype is not set
        if (undefined === prototype) {
            console.log('No data-prototype found!');
            return;
        }

        // TODO: prepend adding already added objects!

        // compile Hogan template
        var template = Hogan.compile(prototype); // TODO: performance-hint: use an array of compiled templates
        var output = template.render(jQuery.extend(entity, { index: index + 1 }));

        collection.append(output);
        collection.data('index', index + 1);

        return collection;
    };

})(window);