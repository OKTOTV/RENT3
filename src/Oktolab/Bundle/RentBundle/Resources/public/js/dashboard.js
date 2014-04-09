jQuery(document).ready(function ($) {

    $('#dashboard-show-date').on('click', function(e){
        var url = oktolab.baseUrl+'/dashboard';
        var begin = null;
        if ($('#calendar-input-date').val()) {
            begin = new Date($('#calendar-input-date').val());
            url = url+'/'+ begin.getFullYear() + "-" + Oktolab.leadingZero(begin.getMonth()+1) +'-'+Oktolab.leadingZero(begin.getDate().toString());
            begin.setDate(begin.getDate()+3);
            url = url+'/'+ begin.getFullYear() + "-" + Oktolab.leadingZero(begin.getMonth()+1) +'-'+Oktolab.leadingZero(begin.getDate().toString());

            window.location.href = url;
        }
    });
});

