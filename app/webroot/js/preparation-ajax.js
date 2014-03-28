$(function () {
    $('#ex2-node-4').bind("click",loadPreparation);
});

function loadPreparation(){
    //alert('test');
    var url = $(this).attr('data-url');
    xhr = new XMLHttpRequest();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            //$('#corpusResponse').attr('style','display:none;');
            //$('#ajax-loader').attr('style','display:none;');
            if(xhr.status==200){
                $('#ex2-node-4').unbind("click");
                $('#ex2-node-4').unbind("mousedown");
                $('#ex2-node-4').unbind("keydown");
                var preparationRoot = $('#ex2-node-4');
                preparationRoot.after('<tr id="preparation-ajax" style="display:none"></tr>'); //style="display:none";
                var preparationAjaxDiv = $('#preparation-ajax');

                preparationAjaxDiv.append(xhr.responseText);
                //preparationAjaxDiv.children("tr").each(function() {
                //    $(this).addClass('ui-helper-hidden');
                //});

                //preparationRoot.clone()
                preparationRoot.removeClass('initialized');
                preparationRoot.clone().prependTo('#preparation-ajax');

                var preparationAjaxDivRoot = preparationAjaxDiv.find('#ex2-node-4');
                preparationAjaxDivRoot.children('a|span').each(function(){
                   $(this).remove();
                });
                $.getScript("../../../app/webroot/js/jquery.treeTableVertical.js", function(){
                    preparationAjaxDivRoot.treeTablePreparation();

                    //$("#ex2-node-4").removeClass('initialized');
                    //$("#ex2-node-4").treeTablePreparation();
                    var next = preparationRoot;
                   $("#preparation-ajax").children().each(function(){
                       $(this).insertAfter(next);
                       next = $(this);
                   });
                    //bindingPreparation(preparationAjaxDivRoot);
                    preparationRoot.remove();
                    $('#preparation-ajax').remove();
                    preparationAjaxDivRoot.expand();
                });


            }else{
                //$('#corpusResponseDone').text('upload failed');
            }
        }
    };
    xhr.send();


}