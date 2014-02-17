(function (window, document, $, Oktolab) {
   $('.category_sortable').sortable({
       handle: '.category-header',
       update: function (event, ui) {
           var json = new Object();
           $('.category_sortable').children().each(function(key, value) {
               json[$(value).data('value')] = $(value).index();
           });
           $.ajax({
                url: oktolab.jquery.calendarCategorySortUrl,
                type: 'post',
                data: JSON.stringify(json),
                contentType: 'application/json',
                dataType: 'json'
            });
       }
   });
   $('.item_sortable').sortable({
       update: function (event, ui) {
            var json = new Object();
            ui.item.parent().children().each(function (key, value) {
                json[$(value).data('value')] = $(value).index();
            });
            $.ajax({
                url: oktolab.jquery.calendarItemSortUrl,
                type: 'post',
                data: JSON.stringify(json),
                contentType: 'application/json',
                dataType: 'json'
            });
       }
   });

}(window, document, jQuery, Oktolab));
