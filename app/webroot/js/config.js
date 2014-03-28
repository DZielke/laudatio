/*****************************************************
 *
 *                  Config
 *
 *****************************************************/


function activateScheme(scheme){
    var myForm = document.createElement("form");
    myForm.method="post";
    var myInput = document.createElement("input");
    myInput.setAttribute("name","activate");
    myInput.setAttribute("value",scheme);
    myForm.appendChild(myInput);
    document.body.appendChild(myForm);
    myForm.submit();
    document.body.removeChild(myForm);
}

function deleteScheme(scheme){
    if(confirm('Are you sure you want to delete scheme '+scheme+' ?')){
        var myForm = document.createElement("form");
        myForm.method="post";
        var myInput = document.createElement("input");
        myInput.setAttribute("name","delete");
        myInput.setAttribute("value",scheme);
        myForm.appendChild(myInput);
        document.body.appendChild(myForm);
        myForm.submit();
        document.body.removeChild(myForm);
    }else{
        return false;
    }
}

var toast=function(msg,selector,color){
    if($(selector).children('div').length > 0 ){
        $(selector).children('div').each(function(){
            $(this).remove();
        });
    }
    var delay = 2000;
    if(color = '#ff6e6e')
       delay = 15000;
    $("<div class='ui-loader ui-overlay-shadow ui-body-e ui-corner-all'>"+msg+"</div>")
        .css({ display: "block",
            opacity: 0.90,
            position: "relative",
            padding: "1px",
            "font-size": "100%",
            "text-align": "center",
            "width": "200px",
            "margin-top":"6px",
            "margin-left":"0",
            "background-color":color,
            "border-radius": "4px",
            "border": "1px solid #ccc",
            "box-shadow": "rgba(0, 0, 0, 0.3) 1px 1px 2px 0px"
        })
        .appendTo( selector ).delay( delay )
        .fadeOut( 400, function(){
            $(this).remove();
        });
}

/*****************************************************
 *
 *                  View Mapping
 *
 *****************************************************/


function deleteView(scheme){
    if(confirm('Are you sure you want to delete the view for scheme '+scheme+' ?')){
        location.href=$('#trashButton').attr('data-url')+'/'+scheme;
    }else{
        return false;
    }
}

function saveView(scheme){
    var editor = ace.edit("editor");
    try{
        $.parseJSON(editor.getValue());
    }
    catch(e){
        toast("syntax error, file not saved",'#msg','#ff6e6e');
        console.log(e)
        $('#editor textarea').focus();
        return;
    }

    var url = $("#msg").attr('data-url');
    var xhr = new XMLHttpRequest();
    //var user = $(this).siblings('input').val();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200){
                if(xhr.responseText === "saved"){
                    toast("saved",'#msg','#2bd42b');
                }else{
                    toast(xhr.responseText,'#msg','#ff6e6e');
                }
            }else{
                if(xhr.status==302){
                    toast("Your Session timed out, please login again.",'#msg','#ff6e6e');
                }else{
                    toast("connection error",'#msg','#ff6e6e');//$('#CorpusUserResponse').text('upload failed');
                }
            }
        }
    };
    var formData = new FormData();
    formData.append('scheme',scheme);

    formData.append('json',editor.getValue());
    xhr.send(formData);
    $('#editor textarea').focus();
}

function preview(button){
    $('#editor textarea').focus();
    var corpus = $('#corpus').val();
    if(typeof previewWindow !== 'undefined' && previewWindow != null){
        previewWindow.close();
    }
    var previewWindow = window.open($(button).attr('data-url')+"/"+corpus, 'preview');
        var myForm = document.createElement("form");
        myForm.setAttribute("target", "preview");
        myForm.setAttribute("action", $(button).attr('data-url')+"/"+corpus);

        myForm.method="post";
        var myInput = document.createElement("input");
        myInput.setAttribute("name","json");
        var editor = ace.edit("editor");
        myInput.setAttribute("value",editor.getValue());
        myForm.appendChild(myInput);
        document.body.appendChild(myForm);
        myForm.submit();

        document.body.removeChild(myForm);
        //previewWindow = window.open($(button).attr('data-url')+"/"+corpus, 'preview');
}

function help(){
    window.open($('#helpButton').attr('data-url'), 'view configuration help');
}

/*****************************************************
 *
 *                  Index Mapping
 *
 *****************************************************/

function deleteMapping(scheme){
    if(confirm('Are you sure you want to delete index mapping for '+scheme+' ?')){
        var url = $("#trashButton").attr('data-url');
        var xhr = new XMLHttpRequest();
        //var user = $(this).siblings('input').val();
        xhr.open('post', url, true);
        xhr.onreadystatechange=function(){
            if (xhr.readyState==4){
                if(xhr.status==200){
                    if(xhr.responseText === "success"){
                        window.location.reload();
                    }else{
                        toast(xhr.responseText,'#msg','#ff6e6e');
                    }
                }else{
                    if(xhr.status==302){
                        toast("Your Session timed out, please login again.",'#msg','#ff6e6e');
                    }else{
                        toast("connection error",'#msg','#ff6e6e');//$('#CorpusUserResponse').text('upload failed');
                    }
                }
            }
        };
        var formData = new FormData();
        formData.append('scheme',scheme);
        xhr.send(formData);
        $('#editor textarea').focus();
    }else{
        return false;
    }
}

function deleteMappingVersion(scheme){
    if(confirm('Are you sure you want to delete the current index mapping version for '+scheme+' ?')){
        var url = $("#trashButton").attr('data-url');
        var xhr = new XMLHttpRequest();
        //var user = $(this).siblings('input').val();
        xhr.open('post', url, true);
        xhr.onreadystatechange=function(){
            if (xhr.readyState==4){
                if(xhr.status==200){
                    if(xhr.responseText === "success"){
                        toast("version deleted",'#msg','#2bd42b');
                        var selectBox = document.getElementById("version");
                        var length = selectBox.options.length;
                        for(var i = 0; i <= length; i++){
                            if(selectBox.options[i].text.substring(0,2) == "* "){
                                selectBox.remove(i);
                                break;
                            }
                        }
                        if(selectBox.options.length < 2)
                            selectBox.disabled = true;
                        if(selectBox.options.length < 1){
                            selectBox.options[0] = new Option('no versions','null');
                            editor.setValue("no mapping configured",-1);
                        }else{
                            selectBox.options[0].text = '* '+ selectBox.options[0].text;
                            selectBox.selectedIndex = 0
                            loadMappingVersion(scheme,selectBox.options[0].value);
                        }
                        $('#editor textarea').focus();
                    }else{
                        if(xhr.status==302){
                            toast("Your Session timed out, please login again.",'#msg','#ff6e6e');
                        }else{
                            toast(xhr.responseText,'#msg','#ff6e6e');
                        }
                    }
                }else{
                    toast("connection error",'#msg','#ff6e6e');//$('#CorpusUserResponse').text('upload failed');
                }
            }
        };
        var formData = new FormData();
        formData.append('scheme',scheme);
        var selectBox = document.getElementById("version");
        if(!selectBox.disabled){
            var length = selectBox.options.length;
            var version = null;
            for(var i = 0;i<length;i++){
                if(selectBox.options[i].text.substring(0,2) == "* "){
                    var version = selectBox.options[i].value;
                    break;
                }
            }
            if(version === null){
                toast("no version available",'#msg','#ff6e6e');
                return false;
            }
            formData.append('version',version);
        }
        xhr.send(formData);
    }else{
        return false;
    }
}

function putIndexMapping(scheme){
    var url = $("#uploadButton").attr('data-url');
    var xhr = new XMLHttpRequest();
    //var user = $(this).siblings('input').val();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            document.getElementById("uploadButton").src = document.getElementById("uploadButton").src.replace("ajax-loader.gif","upload.ico");
            if(xhr.status==200){
                if(xhr.responseText === "success"){
                    toast("reindexing successful",'#msg','#2bd42b');
                }else{
                    toast("<p>"+xhr.responseText+"</p>",'#msg','#ff6e6e');
                }
            }else{
                if(xhr.status==302){
                    toast("Your Session timed out, please login again.",'#msg','#ff6e6e');
                }else{
                    toast("<p>"+xhr.responseText+"</p>",'#msg','#ff6e6e');
                }
            }
        }
    }
    var formData = new FormData();
    formData.append('scheme',scheme);
    var selectBox = document.getElementById("version");
    var length = selectBox.options.length;
    var version = null;
    for(var i = 0;i<length;i++){
        if(selectBox.options[i].text.substring(0,2) == "* "){
            var version = selectBox.options[i].value;
            break;
        }
    }
    if(version === null){
        toast("no version available",'#msg','#ff6e6e');
        return false;
    }
    formData.append('version',version);
    document.getElementById("uploadButton").src = document.getElementById("uploadButton").src.replace("upload.ico","ajax-loader.gif");
    xhr.send(formData);


}

function loadMappingSelectedVersion(scheme){
    var version = $('#version').val();
    if(version == "null")
        return false;
    var text = $('#version option:selected').text();
    if(version == "null")
        alert("No version selected");
    if(text.substring(0,2) == "* ")
      alert("Selected version already loaded")
    loadMappingVersion(scheme,version);
}

function loadMappingVersion(scheme,version){
    if(version == "null")
        alert("No version selected");
    var url = $("#editor").attr('data-url');
    var xhr = new XMLHttpRequest();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200 && xhr.responseText != "error"){
                var selectBox = document.getElementById("version");
                var length = selectBox.options.length;
                for(var i = 0;i<length;i++){
                    if(selectBox.options[i].text.substring(0,2) == "* "){
                        selectBox.options[i].text = selectBox.options[i].text.substring(2);
                    }
                    if(selectBox.options[i].value == version){
                        selectBox.options[i].text = "* "+selectBox.options[i].text
                        selectBox.selectedIndex = i;
                    }
                }
                editor.setValue(xhr.responseText,-1);
            }else{
                toast("connection error",'#msg','#ff6e6e');
            }
        }
    };
    var formData = new FormData();
    formData.append('scheme',scheme);
    formData.append('version',version);
    xhr.send(formData);
}

function saveMapping(scheme){
    var editor = ace.edit("editor");
    try{
        $.parseJSON(editor.getValue());
    }
    catch(e){
        toast("syntax error, mapping not saved",'#msg','#ff6e6e');
        console.log(e)
        $('#editor textarea').focus();
        return;
    }

    var url = $("#saveButton").attr('data-url');
    var xhr = new XMLHttpRequest();
    //var user = $(this).siblings('input').val();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200){
                if(xhr.responseText !== "error"){
                    toast("saved",'#msg','#2bd42b');
                    console.log(xhr.responseText);
                    var version = $.parseJSON(xhr.responseText);
                    console.log(version);
                    var selectBox = document.getElementById("version");
                    var length = selectBox.options.length;
                    var prev = selectBox.options[0];
                    var current;
                    selectBox.options[0] = new Option('* '+version[0][0],version[0][0]);
                    if(prev.value !== "null"){
                        for(var i = 1; i <= length; i++){
                            current = selectBox.options[i];
                            if(prev.text.substring(0,2) == "* ")
                                prev.text = prev.text.substring(2);
                            selectBox.options[i] = prev;
                            prev = current;
                        }
                    }
                    if(selectBox.options.length > 1)
                        selectBox.disabled = false;
                    selectBox.selectedIndex = 0
                }else{
                    if(xhr.status==302){
                        toast("Your Session timed out, please login again.",'#msg','#ff6e6e');
                    }else{
                        toast(xhr.responseText,'#msg','#ff6e6e');
                    }
                }
            }else{
                toast("connection error",'#msg','#ff6e6e');//$('#CorpusUserResponse').text('upload failed');
            }
        }
    };
    var formData = new FormData();
    formData.append('scheme',scheme);

    formData.append('json',editor.getValue());
    xhr.send(formData);
    $('#editor textarea').focus();
}
