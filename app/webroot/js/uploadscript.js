 /*$(document).ready(function(){
   $("a").click(function(event){
     alert("Thanks for visiting!");
   });
 });
 */
 /*$(document).ready(function(){
   	window.onload = function(){ alert("jquery says welcome"); }*/
   /*	alert("bla");
 });*/
function deleteObject(pid){
		var current = window.location.href.toString();
		window.location.href = current.substring(0,current.indexOf('delete'))+'delete/'+pid;
}
function changeDemo(){
	
	document.getElementById("demo").innerHTML="getElementById works";
}


function changeVersion(pid){
	var date;
	
	date = document.getElementById('versionDate').value;
	var current = window.location.href.toString();
	//window.location.href = window.location.href.toString()+"/"+date);
    //depricated
	//window.location.href = current.substring(0,current.indexOf(pid)+pid.length)+"/"+encodeURIComponent(date).replace(".","/");
    window.location.href = current.substring(0,current.indexOf(pid)+pid.length)+"/"+encodeURIComponent(date);
}

function showVersionUpload(){
	$('</br><h3>Upload New Version</h3><form id="uploadForm" method="POST" enctype="multipart/form-data">'+
      '<table border="0" cellpadding="0" cellspacing="4">'+
      '<td><input name="userfile" type="file"></td></tr></table><br>'+
      '<input type="submit" name="uploadNewVersion" value="Upload"></form>').insertAfter($('#modForm'));
      $('#uploadButton').remove();
}
	
function addFormat(){
	
$('<tr><td>Corpus Format:</td>'+
  '<td><input name="formatname[]" type="text" size="15" maxlength="30"></td>'+
  '<td>File: '+
  '<input name="userfile[]" type="file"></td>'+
  '<td><button type="button" onclick="deleteFormat($(this))">Delete</button></td>'+
'</tr>').insertAfter($('#uploadform').find('tr').eq(-3));
	/*$('form table #id').val("Gesetzter Wert")*/;
}

function deleteFormat(format){
	format.parent().parent().remove();
}

function confirmDelete(id,trim)
{
    if(confirm('Are you sure you want to delete Datastream '+id+' ?')){
    	{window.location.href="../deleteDatastream/"+trim+'/'+id;}
    }else{
    	return false;
    }
         
}

