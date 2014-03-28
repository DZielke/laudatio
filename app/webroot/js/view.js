jQuery(document).ready(function() {
    $('#tree').on('click', 'tr.duplicateRow a',function(event){
        event.preventDefault();
        $('#tree tr.'+$(this).attr('class')).removeClass('hidden');
        $(this).parent().parent().remove();
    });


    /* Close section button */
    var offset = 220;
    var duration = 500;
    jQuery(window).scroll(function() {
        var windowHeight = $(window).height();
        var section1 = $('#ex2-node-1').offset().top - $(window).scrollTop();
        var section2 = $('#ex2-node-2').offset().top - $(window).scrollTop();
        var section3 = $('#ex2-node-3').offset().top - $(window).scrollTop();
        var section4 = $('#ex2-node-4').offset().top - $(window).scrollTop();
        var close1 = $('#close1');
        var close2 = $('#close2');
        var close3 = $('#close3');
        var close4 = $('#close4');
        if((section2-section1)>windowHeight && section1 < 0){
            if(section2 > windowHeight){
                close1.css({position: 'fixed',bottom: '1em',top: 'auto'});
            }else{
                close1.css({position: 'absolute',bottom:'auto',top:($('#ex2-node-2').offset().top-close1.outerHeight())});
            }
            close1.fadeIn(duration);
        }else{
            close1.fadeOut(duration);
        }

        if((section3-section2)>windowHeight && section2 < 0){
            if(section3 > windowHeight){
                close2.css({position: 'fixed',bottom: '1em',top: 'auto'});
            }else{
                close2.css({position: 'absolute',bottom:'auto',top:($('#ex2-node-3').offset().top-close2.outerHeight())});
            }
            close2.fadeIn(duration);
        }else{
            close2.fadeOut(duration);
        }

        if((section4-section3)>windowHeight && section3 < 0){
            if(section4 > windowHeight){
                close3.css({position: 'fixed',bottom: '1em',top: 'auto'});
            }else{
                close3.css({position: 'absolute',bottom:'auto',top:($('#ex2-node-4').offset().top-close3.outerHeight())});
            }
            close3.fadeIn(duration);
        }else{
            close3.fadeOut(duration);
        }


        if(section4 < 0){
            var lastRow = $('#tree').offset().top - $(window).scrollTop() +$('#tree').height();

            //console.log(lastRow);
            if(lastRow > windowHeight){
                close4.css({position: 'fixed',bottom: '1em',top: 'auto'});
            }else{
                close4.css({position: 'absolute',top:($('#tree').offset().top+$('#tree').height()-close4.outerHeight()),bottom:'auto'});
            }
            close4.fadeIn(duration);
        }else{
            close4.fadeOut(duration);
        }




        /* console.log($(window).height());
        console.log("Section 1: "+ ($('#ex2-node-1').offset().top - $(window).scrollTop()));
        console.log("Section 2: "+ ($('#ex2-node-2').offset().top - $(window).scrollTop()));
        console.log("Section 3: "+ ($('#ex2-node-3').offset().top - $(window).scrollTop()));
        console.log("Section 4: "+ ($('#ex2-node-4').offset().top - $(window).scrollTop()));
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.close-section').fadeIn(duration);
        } else {
            jQuery('.close-section').fadeOut(duration);
        }*/
        //console.log($('#ex2-node-3').offset().top);
       // console.log(document.body.scrollHeight);
    });

    jQuery('.close-section').click(function(event) {
        event.preventDefault();
        $(event.target).fadeOut(0);
        var id = $(event.target).attr('id');
        var node = $('#ex2-node-'+id.substr(id.length-1,1));
        node.click();
        $(window).scrollTop( node.offset().top );
        return false;
    })
});