AJS.$(document).ready(function() {
    if (jQuery('.plupload').data('plupload') === 'single') {
        var pictureloader = new plupload.Uploader({
            runtimes:           'html5',
            browse_button:      'pickfile',
            container:          'containerfile',
            max_file_size:      '10mb',
            multi_selection:    false,
            max_file_count:     1,
            url:                oktolab.plupload.uploadUrl,
            filters: [
                { title : "Image files", extensions : "jpg,jpeg,gif,png" }
            ]
         });

         pictureloader.bind('Init', function(up, params) {
             AJS.$('#file').html("<div>Bereit: " + params.runtime + "</div>");
         });

         AJS.$('#uploadfile').click(function(e) {
             if (pictureloader.files.length > 0) {
                 pictureloader.start();
                 e.preventDefault();
             }
         });

         pictureloader.bind('FilesAdded', function(up, files) {
             $.each(files, function(i, file) {
                 $('#file').append(
                     '<div id="' + file.id + '">' +
                     file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                 '</div>');
             });

             up.refresh(); // Reposition Flash/Silverlight
         });

         pictureloader.bind('UploadProgress', function(up, file) {
             $('#' + file.id + " b").html(file.percent + "%");
         });

         pictureloader.bind('Error', function(up, err) {
             $('#file').append("<div>Error: " + err.code +
                 ", Message: " + err.message +
                 (err.file ? ", File: " + err.file.name : "") +
                 "</div>"
             );

             up.refresh(); // Reposition Flash/Silverlight
         });

         pictureloader.bind('FileUploaded', function(up, file) {
             $('#' + file.id + " b").html("100%");
         });

         pictureloader.bind('UploadComplete', function(uploader, file) {
             $('form').submit();
         });

         pictureloader.init();
    }

    if (jQuery('.plupload').data('plupload') === 'multiple') {
        var uploader = new plupload.Uploader({
            runtimes :          'html5',
            browse_button :     'pickfiles',
            container :         'container',
            max_file_size :     '10mb',
            url :               oktolab.plupload.uploadUrl,
            filters : [
                {title : "Image files", extensions : "jpg,jpeg,gif,png"}
            ]
        });

        uploader.bind('Init', function(up, params) {
            AJS.$('#filelist').html("<div>Bereit: " + params.runtime + "</div>");
        });

        AJS.$('#uploadfiles').click(function(e) {
            if (uploader.files.length > 0) {
                uploader.start();
                e.preventDefault();
            }
        });

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
            $('.oktolab-plupload-form').submit();
        });

        uploader.init();
    }
});