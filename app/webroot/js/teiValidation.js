$(function () {
    $('#teiupload').bind('change', upload);
});

function upload(){
    File = $('input[type="file"]')[0].files[0]
    /*var files = $('input[type="file"]')[0].files;
     for (var i = 0; i < files.length; i++)
     {
     alert(files[i].name);
     }*/
    formData = new FormData();
    if ('getAsBinary' in File){
        formData.append('teifile', File.getAsBinary());
    }else{
        formData.append('teifile' || File.fileName, File);
    }
    formData.append('filename',File.name || File.fileName);
    formData.append('id',$('#id').val());
    formData.append('scheme',$('#scheme').val());
    xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", onUploadProgress);

    var url = $(this).attr('data-url');
    xhr.open('post', url, true);

    //xhr.setRequestHeader("X-File-Name", File.name || File.fileName);
    //xhr.setRequestHeader("X-File-Size", File.fileSize);
    //xhr.setRequestHeader("X-File-Type", File.type);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            $('#ajax-loader').attr('style','display:none;');
            if(xhr.status==200){
                //var file = getFileName(xhr.responseText);
                //var text = getText(xhr.responseText);
                $('#corpusResponse').html(xhr.responseText);
                $('#uploadResponse').removeAttr('style');

            }else{
                $('#uploadResponse').text('upload failed');
            }
            validateDocuments(url);
        }
    };

    /*if ('getAsBinary' in File)
     xhr.sendAsBinary(File.getAsBinary());
     else
     xhr.send(File);
     */
    $('#uploadResponse').removeAttr('style');
    xhr.send(formData);
}

function deleteFolder(url){
    return $.ajax({
        type: "post",
        url: url+'/delete'
    });
}

function validateDocuments(url){
    $.ajax({
        type: "post",
        //contentType: "application/json; charset=utf-8",
        url: url+'/docs',
        //data: {'tempFileName':fileName },
        success: function (result) {
            $('#documentResponse').html(result);
        },
        error: function(){
            $('#documentResponse').html('<b>upload error</b>');
        }
    });
    validatePreparations(url);
}

function validatePreparations( url){
    $.ajax({
        type: "post",
        //contentType: "application/json; charset=utf-8",
        url: url+'/preps',
        // data: {'tempFileName': fileName },
        success: function (result) {
            $('#preparationResponse').html(result);
        },
        error: function(){
            $('#documentResponse').html('<b>upload error</b>');
        }
    });
}

function getText(responseText) {
    if(responseText.indexOf('!temp')!= -1){
        //console.log(responseText.substr(responseText.indexOf('!temp')+5,responseText.length-responseText.indexOf('!temp')+5))
        return responseText.substr(responseText.indexOf('!temp')+5,responseText.length-responseText.indexOf('!temp')+5)
    }
    return null;
}

function getFileName(responseText) {
    if(responseText.indexOf('!temp')!= -1){
        //console.log(responseText.substr(0,responseText.indexOf('!temp')));
        return responseText.substr(0,responseText.indexOf('!temp'))
    }
    return null;
}

function onUploadProgress(event) {
    if(event.lengthComuptable) {
        var percentage = log(Math.round((event.loaded * 100) / event.total) + '%');
        if(percentage = 100){
            $('#uploadPercentage').html('');
        }else{
            $('#uploadPercentage').html('upload: '+percentage+'<br/>');
        }

    }
}

function validateForm(){
    if(!$('#label').val()){
        $('#label').addClass('valerror');
        return false;
    }else{
        return true;
    }
}