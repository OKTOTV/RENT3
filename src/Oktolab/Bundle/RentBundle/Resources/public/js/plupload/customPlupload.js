//<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
//<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
//<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
//<script type="text/javascript" src="http://www.plupload.com/plupload/js/plupload.full.js"></script>
//<script type="text/javascript" src="http://www.plupload.com/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>
//
//<script type="text/javascript">
AJS.$(document).ready(function()
{
//    AJS.$("#fileupload").plupload(
//    {
//        runtimes: "html5",
//        url: uploadAttachmentUrl
//    });

// Custom example logic
    AJS.$(function() {
        var uploader = new plupload.Uploader({
            runtimes : 'html5',
            browse_button : 'pickfiles',
            container : 'container',
            max_file_size : '10mb',
            url : uploadAttachmentUrl,
            filters : [
                {title : "Image files", extensions : "jpg,gif,png"},
            ],
            resize : {width : 320, height : 240, quality : 90}
        });

        uploader.bind('Init', function(up, params) {
            AJS.$('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
        });

        AJS.$('#uploadfiles').click(function(e) {
            if (uploader.files.length > 0) {
                uploader.start();
                e.preventDefault();
            }
        });

        uploader.init();

        uploader.bind('FilesAdded', function(up, files) {
            $.each(files, function(i, file) {
                $('#filelist').append(
                    '<div id="' + file.id + '">' +
                    file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                '</div>');
            });

            up.refresh(); // Reposition Flash/Silverlight
        });

        uploader.bind('UploadProgress', function(up, file) {
            $('#' + file.id + " b").html(file.percent + "%");
        });

        uploader.bind('Error', function(up, err) {
            $('#filelist').append("<div>Error: " + err.code +
                ", Message: " + err.message +
                (err.file ? ", File: " + err.file.name : "") +
                "</div>"
            );

            up.refresh(); // Reposition Flash/Silverlight
        });

        uploader.bind('FileUploaded', function(up, file) {
            $('#' + file.id + " b").html("100%");
        });

        uploader.bind('UploadComplete', function(uploader, file) {
            console.log(uploader);
            $('form').submit();
        });
    });
});