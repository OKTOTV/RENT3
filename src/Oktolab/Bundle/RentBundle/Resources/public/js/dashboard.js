jQuery(document).ready(function ($) {
    $(document).tooltip({ 
        track: true,
        content: function(){return $(this).attr('title');}
    });

    $('#dashboard-date').appendDtpicker({
        "firstDayOfWeek": 1,
        "dateOnly"      : true,
        "dateFormat"    : "YYYY-MM-DD",
        "locale"        : "de",
        "calendarMouseScroll": false,
        "closeOnSelected": true,
        "onHide": function(handler){
            var url = oktolab.baseUrl+'/dashboard';
            var begin = null;
            begin = new Date($('#dashboard-date').val());
            url = url+'/'+ begin.getFullYear() + "-" + Oktolab.leadingZero(begin.getMonth()+1) +'-'+Oktolab.leadingZero(begin.getDate().toString());
            begin.setDate(begin.getDate()+3);
            url = url+'/'+ begin.getFullYear() + "-" + Oktolab.leadingZero(begin.getMonth()+1) +'-'+Oktolab.leadingZero(begin.getDate().toString());

            window.location.href = url;
        }
    }); 
});

