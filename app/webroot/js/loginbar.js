$(document).ready(function(){
	$('#loginlink').bind('click',showLogin);
});

function showLogin(event){
	event.preventDefault();
    $('#loginlink').unbind('click');
    $('#loginlink').bind('click',hideLogin);
    $('#loginlink').parent().siblings('.loginform').removeAttr('style');
}

function hideLogin(event){
    event.preventDefault();
    $('#loginlink').unbind('click');
    $('#loginlink').bind('click',showLogin);
    $('#loginlink').parent().siblings('.loginform').attr('style','display:none');
}
