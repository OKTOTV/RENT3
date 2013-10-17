(function() {
    // oktolab.baseUrl - Base URL to application (incl. host).

    oktolab.typeahead = {
        itemPrefetchUrl:    oktolab.baseUrl + '/api/item/typeahead.json',
        setPrefetchUrl:     oktolab.baseUrl + '/api/set/typeahead.json',
        contactPrefetchUrl: oktolab.baseUrl + '/api/contact/typeahead.json'
    };

    oktolab.plupload = {
        uploadUrl: oktolab.baseUrl + '/_uploader/gallery/upload',
    };

})();
