var wasEmpty = false;

$(document).ready(function(){
    if($('#UserPassword').val() == '')
        wasEmpty = true;
	$('.nav-parent-corpora').hover(showCorpora);
    $('.nav-parent-corpora').bind('click',showCorpora);

    $('.nav-parent-admin').hover(showAdmin);
    $('.nav-parent-admin').bind('click',showAdmin);


    $('#loginlink').bind('click',showLogin);
    $('#UserUsernameLabel').bind('click',showUsername);
    $('#UserUsernameLabel').focus(showUsername);
    $('#UserPasswordLabel').bind('click',showPassword);
    $('#UserPasswordLabel').focus(showPassword);
    $('#UserUsername').bind('input',showPasswordAutoComplete);
    $('#UserUsername').blur(showDefaultUsername);
    $('#UserPassword').blur(showDefaultPassword);
    //$('#UserPassword').bind('input',showPasswordAutoComplete);
    //$('#UserPassword').bind('input',showPassword);
});

/* sub menus */
function showCorpora(event){
	event.preventDefault();
    hideSubs(null);
    var corpora = $('div.nav-sub-corpora');
    $(event.target).addClass('hovered');
    corpora.removeClass('hidden');
    $('header.section').mouseleave(hideSubs);
}

function showAdmin(event){
    event.preventDefault();
    hideSubs(null);
    var admin = $('div.nav-sub-admin');
    $(event.target).addClass('hovered');
    admin.removeClass('hidden');
    $('header.section').mouseleave(hideSubs);
}


function hideSubs(event){
    $('a.nav-parent').each(function(){
       $(this).removeClass('hovered');
    });
    $('div.nav-sub').each(function(){
        $(this).addClass('hidden');
    });
}



/* login form */
function showLogin(event){
    event.preventDefault();
    $('#loginlink').unbind('click');
    $('#loginlink').bind('click',hideLogin);
    $('#loginlink').siblings('.loginform').removeAttr('style');
    showDefaultUsername(null);
    showDefaultPassword(null);
    if($('#UserPassword').val() != ''){
        $('#UserPassword').blur(showDefaultPassword);
    }
    if($('#UserUsername').val() != ''){
        $('#UserUsername').blur(showDefaultUsername);
    }
}

function hideLogin(event){
    event.preventDefault();
    $('#loginlink').unbind('click');
    $('#loginlink').bind('click',showLogin);
    $('#loginlink').siblings('.loginform').attr('style','display:none');
}


function showUsername(event){
    $('#UserUsernameLabel').addClass('hidden');//.attr('style','display:none');
    $('#UserUsername').removeClass('hidden');

    $('#UserUsername').focus();
}

function showDefaultUsername(event){
    if($('#UserUsername').val() == ''){
        $('#UserUsername').addClass('hidden');
        $('#UserUsernameLabel').removeClass('hidden'); //attr('style','display:inline-block');
    }
}

function showPassword(event){
        $('#UserPasswordLabel').addClass('hidden');
        $('#UserPassword').removeClass('hidden')
        $('#UserPassword').focus();
}

function showPasswordAutoComplete(event){
        setTimeout(function(){
            if(wasEmpty && $('#UserPassword').val() != ''){
                console.log($('#UserPassword').val());
                showPassword(null);
                wasEmpty = false;
            }
        }, 100);
}

function showDefaultPassword(event){
    if($('#UserPassword').val() == ''){
        wasEmpty = true;
        $('#UserPassword').bind('input',showPassword);
        $('#UserPassword').addClass('hidden');
        $('#UserPasswordLabel').removeClass('hidden');
        // $('#UserPasswordLabel').attr('style','display:inline-block');
    }else{
        wasEmpty = false;
    }
}