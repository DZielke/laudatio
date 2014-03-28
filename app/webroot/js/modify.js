$(document).ready(function()  {
    $('#addUserButton').click(addUser);
    $('#addGroupButton').click(addGroup);
    $('.removeUser').click(removeUser);
    $('.removeGroup').click(removeGroup);
    $("input[name=openAccess]:radio").change(toggleAccess);
    $("input[name=indexing]:radio").change(toggleIndexing);
});

var toast=function(msg,selector){
    $("<div class='ui-loader ui-overlay-shadow ui-body-e ui-corner-all'>"+msg+"</div>")
        .css({ display: "block",
            opacity: 0.90,
            position: "absolute",
            padding: "1px",
            "font-size": "100%",
            "text-align": "center",
            width: "200px",
            "margin-top":"-22px",
            "margin-left":"350px",
            "background-color":"white"

            //left: ($(window).width() - 284)/2,
            //top: $(window).height()/2
            })
        .appendTo( selector ).delay( 1500 )
        .fadeOut( 400, function(){
           $(this).remove();
        });
}

function toggleAccess(event){
    var url = $(this).attr('data-url');
    var xhr = new XMLHttpRequest();
    //var user = $(this).siblings('input').val();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200){
                //$('#CorpusUserResponse').text(xhr.responseText);
                if($('#openAccessTrue').prop('checked')){
                    toast("Changed to: Open Access",'#openAccessTitle');
                }else{
                    toast("Changed to: Closed Access",'#openAccessTitle');
                }
                //var checkbox = $('#openAccess');
                //if(checkbox.prop('checked')){
                //    //$(this).removeAttr('checked');
                //    checkbox.siblings('span').html('All users are allowed to view this Corpus')
                //}else{
                //    checkbox.siblings('span').html('Only allowed users can view this Corpus')
                //}
            }else{
                toast("Connection error",'#openAccessTitle');//$('#CorpusUserResponse').text('upload failed');
            }
        }
    };
    var formData = new FormData();
    formData.append('id',$('#corpusID').html());
    xhr.send(formData);
}

function toggleIndexing(event){
    var url = $(this).attr('data-url');
    var xhr = new XMLHttpRequest();
    //var user = $(this).siblings('input').val();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200){
                if(xhr.responseText != null){
                    console.log(xhr.responseText);
                }
                if($('#indexingTrue').prop('checked')){
                    toast("Changed to: indexing",'#indexingTitle');
                }else{
                    toast("Changed to: no indexing",'#indexingTitle');
                }
                //var checkbox = $('#openAccess');
                //if(checkbox.prop('checked')){
                //    //$(this).removeAttr('checked');
                //    checkbox.siblings('span').html('All users are allowed to view this Corpus')
                //}else{
                //    checkbox.siblings('span').html('Only allowed users can view this Corpus')
                //}
            }else{
                toast("Connection error",'#indexingTitle');
            }
        }
    };
    var formData = new FormData();
    formData.append('id',$('#corpusID').html());
    xhr.send(formData);
}

function addUser(event){

    var url = $(this).attr('data-url');
    var xhr = new XMLHttpRequest();
    var user = $(this).siblings('input').val();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200){

                if(xhr.responseText === 'User added.'){
                    var icon = $('#deleteIcon').clone();
                    icon.removeClass('hidden');
                    icon.addClass('removeUser');
                    $('tr.userlist:last').after('<tr class="userlist"><td><span>'+user+'</span></td></tr>');
                    $('tr.userlist:last td').append(icon);
                    $('.removeUser').click(removeUser);

                    toast(xhr.responseText,'#allowedUsersTitle');
                    //$('#CorpusUserResponse').text(xhr.responseText);
                }else if( xhr.responseText.indexOf('/') >= 0){
                    window.location.href=xhr.responseText;
                }else{
                    toast(xhr.responseText,'#allowedUsersTitle');
                    //$('#CorpusUserResponse').text(xhr.responseText);
                }
            }else{
                toast('Connection error.','#allowedUsersTitle');
                //$('#CorpusUserResponse').text('upload failed');
            }
        }
    };
    var formData = new FormData();
    formData.append('user',user);
    formData.append('id',$('#corpusID').html());
    xhr.send(formData);
}

function addGroup(event){
    var group = $(this).siblings('input').val();
    var url = $(this).attr('data-url');
    var xhr = new XMLHttpRequest();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200){
                $('#CorpusGroupResponse').text(xhr.responseText);
                if(xhr.responseText == 'Group added.'){
                    var icon = $('#deleteIcon').clone();
                    icon.removeClass('hidden');
                    icon.addClass('removeGroup');
                    $('tr.grouplist:last').after('<tr class="grouplist"><td><span>'+group+'</span></td></tr>');
                    $('tr.grouplist:last td').append(icon);
                    $('.removeGroup').click(removeGroup);
                }else if( xhr.responseText.indexOf('/') >= 0){
                    window.location.href=xhr.responseText;
                }else{
                    $('#CorpusGroupResponse').text(xhr.responseText);
                }
            }else{
                $('#CorpusGroupResponse').text('upload failed');
            }
        }
    };
    var formData = new FormData();
    formData.append('group',$(this).siblings('input').val());
    formData.append('id',$('#corpusID').html());
    xhr.send(formData);
}

function removeUser(event){
    var row = $(this).parents('tr');
    var user = $(this).siblings('span').html();

    var url = $('#userlist').attr('data-url');
    var xhr = new XMLHttpRequest();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200){
                if(xhr.responseText === 'success'){
                    console.log('success')
                    row.remove();
                    toast('User removed.','#allowedUsersTitle');
                    //$('#CorpusUserResponse').text('User removed.');
                }else if( xhr.responseText.indexOf('/') >= 0){
                    window.location.href=xhr.responseText;
                }else{
                    toast(xhr.responseText,'#allowedUsersTitle');//$('#CorpusUserResponse').text(xhr.responseText);
                }
            }else{
                toast('Connection error.','#allowedUsersTitle');
                //$('#CorpusUserResponse').text('Error: could not remove user');
            }
        }
    };
    var formData = new FormData();
    formData.append('user',user);
    formData.append('id',$('#corpusID').html());
    xhr.send(formData);

}

function removeGroup(event){
    var row = $(this).parents('tr');
    var group = $(this).siblings('span').html();

    var url = $('#grouplist').attr('data-url');
    var xhr = new XMLHttpRequest();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200){
                if(xhr.responseText === 'success'){
                    console.log('success')
                    row.remove();
                    $('#CorpusGroupResponse').text('Group removed.');
                }else if( xhr.responseText.indexOf('/') >= 0){
                    window.location.href=xhr.responseText;
                }else{
                    $('#CorpusGroupResponse').text(xhr.responseText);
                }
            }else{
                $('#CorpusGroupResponse').text('Error: could not remove group');
            }
        }
    };
    var formData = new FormData();
    formData.append('group',group);
    formData.append('id',$('#corpusID').html());
    xhr.send(formData);

}

