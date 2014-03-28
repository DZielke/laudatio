
$(document).ready(function(){
	$('input[name="id"]').focusout(function(){
		validateID();
	});
	$('input[name="id"]').focusin(function(){
		$('input[name="id"]').removeClass('valerror');
	});
	
	$('form:first').submit(function(){
		return validateForm();
	});
	
});

function validateForm(){
	var correct = true;
	if(!validateID()) correct = false;
	//if(!validateLabel()) correct = false;
	if(!correct) alert('Please enter correct values')
	return correct;
}

function validateID(){
	if($('input[name="id"]').val() &&  $('input[name="id"]').val().match(/^([A-Za-z0-9]|-|\.)+:(([A-Za-z0-9])|-|\.|~|_|(%[0-9A-F]{2}))+$/i)!=null){
		return true;
	}else{
		showError('input[name="id"]');
		return false;
	}
}

function showError(element){
	$(element).addClass('valerror');
	$(element).after('<span class="errortooltip"> Name must have the form namespace:id</span>');
}

function hideError(element){
	$(".errortooltip").remove();
	$(element).removeClass('valerror');
}
