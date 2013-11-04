(function() {
    // oktolab.baseUrl - Base URL to application (incl. host).

    oktolab.typeahead = {
        itemPrefetchUrl: oktolab.baseUrl + '/api/item/typeahead.json',
        itemRemoteUrl:   oktolab.baseUrl + '/api/item/typeahead.json/%QUERY',
        setPrefetchUrl:  oktolab.baseUrl + '/api/set/typeahead.json',
        setRemoteUrl:    oktolab.baseUrl + '/api/set/typeahead.json/%QUERY',
        contactPrefetchUrl: oktolab.baseUrl + '/api/contact/typeahead.json',
        contactRemoteUrl:   oktolab.baseUrl + '/api/contact/typeahead.json/%QUERY',
        costunitPrefetchUrl: oktolab.baseUrl + '/api/costunit/typeahead.json',
        costunitcontactRemoteUrl: oktolab.baseUrl + '/api/costunit/__id__/typeahead.json'
    };

    oktolab.plupload = {
        uploadUrl: oktolab.baseUrl + '/_uploader/gallery/upload',
    };

})();
