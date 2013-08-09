(function() {
    // oktolab.baseUrl - Base URL to application (incl. host).

    oktolab.typeahead = {
        itemPrefetchUrl: oktolab.baseUrl + '/api/item/typeahead.json',
    //    itemSearchUrl: oktolab.baseUrl + '/inventory/item/search/%QUERY',
    };

    oktolab.plupload = {
        uploadUrl: oktolab.baseUrl + '/_uploader/gallery/upload',
    };

})();
