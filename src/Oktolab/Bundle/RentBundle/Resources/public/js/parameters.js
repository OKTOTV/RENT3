(function() {
    // oktolab.baseUrl - Base URL to application (incl. host).

    oktolab.typeahead = {
        itemPrefetchUrl: oktolab.baseUrl + '/api/item/typeahead.json',
        itemRemoteUrl:   oktolab.baseUrl + '/api/item/typeahead.json/%QUERY',

        setPrefetchUrl:  oktolab.baseUrl + '/api/set/typeahead.json',
        setRemoteUrl:    oktolab.baseUrl + '/api/set/typeahead.json/%QUERY',

        roomPrefetchUrl: oktolab.baseUrl + '/api/roomt/typeahead.json',
        roomRemoteUrl: oktolab.baseUrl + '/api/roomt/typeahead.json/%QUERY',

        contactPrefetchUrl: oktolab.baseUrl + '/api/contact/typeahead.json',
        contactRemoteUrl:   oktolab.baseUrl + '/api/contact/typeahead.json/%QUERY',

        costunitPrefetchUrl: oktolab.baseUrl + '/api/costunit/typeahead.json',
        costunitRemoteUrl:  oktolab.baseUrl + '/api/costunit/typeahead.json/%QUERY',
        costunitcontactRemoteUrl: oktolab.baseUrl + '/api/costunit/__id__/typeahead.json',

        eventRemoteUrl:     oktolab.baseUrl + '/api/event/typeahead.json/%QUERY',
        //TODO: eventPrefetchUrl ? Why not?

        eventItemRemoteUrl: oktolab.baseUrl + '/api/event/items/typeahead.json/%QUERY', //Add /begin/end as YYYY-MM-DD and you get all available items
        eventSetRemoteUrl:  oktolab.baseUrl + '/api/event/sets/typeahead.json/%QUERY',  //Add /begin/end as YYYY-MM-DD and you get all available sets

        eventRoomRemoteUrl: oktolab.baseUrl + '/api/event/rooms/typeahead.json/%QUERY', //Add /begin/end as YYYY-MM-DD and you get all available rooms
    };

    oktolab.plupload = {
        uploadUrl: oktolab.baseUrl + '/_uploader/gallery/upload',
    };

})();
