(function (window, document, $, Oktolab) {

    var ajaxRequest = function (url, json) {
        $.ajax({
            url: url,
                 type: 'post',
                 data: JSON.stringify(json),
                 contentType: 'application/json',
                 dataType: 'json'
        });
    };

    $('.category_sortable').sortable({
        handle: '.category-header',
        update: function (event, ui) {
            var json = new Object();
            $('.category_sortable').children().each(function(key, value) {
                json[$(value).data('value')] = $(value).index();
            });
            ajaxRequest(oktolab.jquery.calendarCategorySortUrl, json);
        }
    });

    $('.item_sortable').sortable({
        update: function (event, ui) {
             var json = new Object();
             ui.item.parent().children().each(function (key, value) {
                 json[$(value).data('value')] = $(value).index();
             });
             ajaxRequest(oktolab.jquery.calendarItemSortUrl, json);
        }
    });

    $('.set_sortable').sortable({
       update: function (event, ui) {
            var json = new Object();
            ui.item.parent().children().each(function (key, value) {
                json[$(value).data('value')] = $(value).index();
            });
            ajaxRequest(oktolab.jquery.calendarSetSortUrl, json);
       }
   });

}(window, document, jQuery, Oktolab));
