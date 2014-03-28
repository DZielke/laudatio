

$(function () {
	$('#id').bind("keyup",allowTeiUpload);
	$('#id').bind("focusout",allowTeiUpload);
	$('#teiupload').bind('change', upload);
	if(!$('#id').val()){
		$('#teiupload').attr('style','display:none;');
		$('#teiupload').after('<div id="insertFirst">Specify Corpus Name</div>');
	}
});

function allowTeiUpload(){
	if(!$('#id').val()||$('#id').hasClass('valerror')){
		$('#teiupload').attr('style','display:none;');
		if(!$('#insertFirst').length) $('#teiupload').after('<span id="insertFirst">specify name</span>');
	}else{
		$('#teiupload').removeAttr('style');
		$('#insertFirst').remove();
	}
}

function upload(){
    reset();
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
	xhr = new XMLHttpRequest();
	xhr.upload.addEventListener("progress", onUploadProgress);
	 
	var url = $(this).attr('data-url');
	xhr.open('post', url, true);
    //$('#uploadResponse').removeAttr('style');
	//xhr.setRequestHeader("X-File-Name", File.name || File.fileName);
	//xhr.setRequestHeader("X-File-Size", File.fileSize);
	//xhr.setRequestHeader("X-File-Type", File.type);
	xhr.onreadystatechange=function(){
	  if (xhr.readyState==4){
          $('#corpusResponse').after('<span id="corpusResponseDone"></span> ');
          $('#corpusResponse').attr('style','display:none;');
          //$('#ajax-loader').attr('style','display:none;');
    	if(xhr.status==200){
            //var file = getFileName(xhr.responseText);
            //var text = getText(xhr.responseText);

            $('#corpusResponseDone').html(xhr.responseText);
	    }else{
    		$('#corpusResponseDone').text('upload failed');
    	}
        validateDocuments(url);
	  }
   };
	  
	/*if ('getAsBinary' in File)
	    xhr.sendAsBinary(File.getAsBinary());
	else
	    xhr.send(File);
	 */
	//$('#uploadResponse').removeAttr('style');
	xhr.send(formData);
}

function reset(){
    //$('#ajax-loader').removeAttr('style');
    if($('#uploadResponse').attr('style') != undefined){
        $('#uploadResponse').removeAttr('style');
        return;
    }
    //$('#uploadResponse').text('validating Corpus');
    $('#documentResponseDone').remove();
    $('#corpusResponseDone').remove();
    $('#preparationResponseDone').remove();
    $('#corpusResponse').removeAttr('style');
    $('#preparationResponse').removeAttr('style');
    $('#documentResponse').removeAttr('style');

}

function validateDocuments(url){
    $.ajax({
        type: "post",
        //contentType: "application/json; charset=utf-8",
        url: url+'/docs',
        //data: {'tempFileName':fileName },
        success: function (result) {
            $('#documentResponse').after('<span id="documentResponseDone"></span> ');
            $('#documentResponse').attr('style','display:none;');
            $('#documentResponseDone').html(result);
        },
        error: function(){
            $('#documentResponse').after('<span id="documentResponseDone"></span> ');
            $('#documentResponse').attr('style','display:none;');
            $('#documentResponseDone').html('<b>upload error</b>');
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
            $('#preparationResponse').after('<span id="preparationResponseDone"></span> ');
            $('#preparationResponse').attr('style','display:none;');
            $('#preparationResponseDone').html(result);
        },
        error: function(){
            $('#preparationResponse').after('<span id="preparationResponseDone"></span> ');
            $('#preparationResponse').attr('style','display:none;');
            $('#preparationResponseDone').html('<b>upload error</b>');
        }
    });
}

function getText(responseText) {
    if(responseText.indexOf('!temp')!= -1){
        //console.log(responseText.substr(responseText.indexOf('!temp')+5,responseText.length-responseText.indexOf('!temp')+5))
        return responseText.substr(responseText.indexOf('!temp')+5,responseText.length-responseText.indexOf('!temp')+5)
    }
}

function getFileName(responseText) {
    if(responseText.indexOf('!temp')!= -1){
        //console.log(responseText.substr(0,responseText.indexOf('!temp')));
        return responseText.substr(0,responseText.indexOf('!temp'))
    }
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