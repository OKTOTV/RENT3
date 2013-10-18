(function() {
    // oktolab.baseUrl - Base URL to application (incl. host).

    oktolab.typeahead = {
        itemPrefetchUrl: oktolab.baseUrl + '/api/item/typeahead.json',
    //    itemSearchUrl: oktolab.baseUrl + '/inventory/item/search/%QUERY',
        contactPrefetchUrl: oktolab.baseUrl + '/api/contact/typeahead.json',
        costunitPrefetchUrl: oktolab.baseUrl + '/api/costunit/typeahead.json',
        costunitcontactRemoteUrl: oktolab.baseUrl + '/api/costunit/__id__/typeahead.json'
    };

    oktolab.plupload = {
        uploadUrl: oktolab.baseUrl + '/_uploader/gallery/upload',
    };

})();
