
$(document).ready(function()  {
    $("#tree").treeTable(
        /*expandable: false*/
    );
    $('#ex2-node-2').expandAll();
    //$('#ex2-node-4').expandAll();
});

$(function () {
    //get documents via ajax
    $(document).delegate('#ex2-node-2',"click",loadDocuments);
    //get Preparations via ajax
    $(document).delegate('#ex2-node-4',"click",loadPreparations);

    //open document that was clicked on elasticsearch page
    if($('#revealDocument').attr('rel') != null){
        $('#ex2-node-2').trigger('click');
    }
    $(document).delegate('.expandAllFromHere b', 'click', loadAndExpand);




        //e.preventDefault();



    //bind click events on document list in corpus section as anchor to according document in documents section
    $('#ex2-node-1-5-1 td ul li').bind("click",jumpToDocument);

});

function loadAndExpand(e){
    e.stopPropagation();
    e.preventDefault();
    var target = $(e.target);
    var node = target.parent().parent().parent().parent();
    var nodeID = node.attr('id');
    if(node.siblings("tr.child-of-"+nodeID).length == 0){
        if(nodeID == 'ex2-node-2'){
            $.when(loadDocuments())
                .done(function() {
                    $('#'+nodeID).collapse();
                    $('#'+nodeID).expandAllDescendants();
                    //var array = $(node).siblings("tr[class^=child-of-" + node[0].id+ "]" ).toArray();
                    //    toggle(array);
                })
        }else if(nodeID == 'ex2-node-4'){
            $.when(loadPreparations())
                .done(function() {
                    $('#'+nodeID).collapse();
                    $('#'+nodeID).expandAllDescendants();
                })
        }
    }else{
        node.toggleAllDescendants();
    }
}

function jumpToAnnotation(node){
    var rend = node.attr('rel');
    var name =  node.html().replace(/[^a-zA-Z0-9]+/g, "");
    var annotations = $('.child-of-ex2-node-3 td');
    annotations.each(function(){
        var thisRend = $(this).justtext().toLowerCase();
        if(thisRend === rend.toLowerCase()){
            var rendNodeID = $(this).parent().attr('id');
            $('tr.child-of-'+rendNodeID+' td').each(function(){
                var thisName = $(this).justtext().replace(/[^a-zA-Z0-9]+/g, "")
                if(thisName === name){
                    expandThisAndParents($(this).parent());
                    $('html, body').animate({
                        scrollTop: $(this).parent().offset().top
                    }, 2000);
                    return false;
                }
            })
        }
    });

}

function jumpToDocAnnotation(node){
    var name =  node.html();
    var annotations = $('.child-of-ex2-node-3');
    var continueLoop = true;
    annotations.each(function(){
        var items = $('tr.child-of-'+$(this).attr('id')+ ' td')
        items.each(function(){
           if($(this).justtext().toLowerCase().replace(/[^a-zA-Z0-9]+/g, "") === name.toLowerCase().replace(/[^a-zA-Z0-9]+/g, "")){
               expandThisAndParents($(this).parent());
               $('html, body').animate({
                   scrollTop: $(this).parent().offset().top
               }, 2000);
               continueLoop = false;
           }
           return continueLoop;
        });
    });
}

jQuery.fn.justtext = function() {

    return $(this).clone()
        .children()
        .remove()
        .end()
        .text();

};

function expandThisAndParents(node){
    node.expand();
    var parentID = null;
    var classList = node.attr('class').split(/\s+/);
    $.each( classList, function(index, item){
        if (item.indexOf('child-of-') === 0) {
            parentID = item.substring(9);
            return false;
        }

    });
    while(parentID !== null){
        node = $('#'+parentID);
        parentID = null;

        if(node !== undefined){
            node.expand();
            var classList = node.attr('class').split(/\s+/);
            $.each( classList, function(index, item){
                if (item.indexOf('child-of-') === 0) {
                    parentID = item.substring(9);
                }
            });
        }
    }
}

function jumpToDocument(event){
    event.preventDefault();
    //documents already loaded ?
    if($('#ex2-node-2').siblings("tr.child-of-ex2-node-2").length > 0 && $('td[rel="'+$(this).html()+'"]').length > 0){
        var number = $(this).attr('rel');
        if(number == null)
            number = 0;
        if($('td[rel="'+$(this).html()+'"]').length > 0){
            $('td[rel="'+$(this).html()+'"]').eq(number).parent().expand();
            $('html, body').animate({
                scrollTop: $('td[rel="'+$(this).html()+'"]').eq(number).offset().top
            }, 2000);
        }
    }
    else {
        //set name of document to reveal
        $('#revealDocument').attr('rel',$(this).html());

        //if multiple docs with same name, set number as ref
        var number = $(this).attr('rel');
        if(number != null){
            $('#revealDocument').attr('ref',number);
        }
        $('#ex2-node-2').trigger('click');
    }
}

function revealDocument(){
    var reveal = null;
    reveal = $('#revealDocument').attr('rel');
    if(reveal != null && $('td[rel="'+decodeURIComponent(reveal)+'"]').length > 0 ){
        //$('td[rel="'+decodeURIComponent(reveal)+'"]').parent().reveal();
        //var items = $('td[rel="'+decodeURIComponent(reveal)+'"]');
        var number = 0;
        if($('#revealDocument').attr('ref')){
            number = $('#revealDocument').attr('ref')
        }
        $('td[rel="'+decodeURIComponent(reveal)+'"]').eq(number).parent().expand();

        $('html, body').animate({
            scrollTop: $('td[rel="'+decodeURIComponent(reveal)+'"]').eq(number).offset().top
        }, 2000);


    }
}

var loadDocuments = function(){
    $(document).undelegate('#ex2-node-2',"click",loadDocuments);
    //alert('test');
    //var dfd = $.Deferred();
    $('#ex2-node-2').children('td').addClass('loading');
    var url = $('#ex2-node-2').attr('data-url');
    return $.post(url,function(data) {
            var start = new Date().getTime();
            var final_start = start;
            var elapsed;
            $('#ex2-node-2').unbind("click");
            $('#ex2-node-2').unbind("mousedown");
            $('#ex2-node-2').unbind("keydown");
            var timeoutThreshold = 20;
            var documentRoot = $('#ex2-node-2');


            //append response to a div

            var documentAjaxDiv = document.createElement('div');
            $(documentAjaxDiv).append(documentRoot.clone());
            $(documentAjaxDiv).append(data);
            var divRoot = $($(documentAjaxDiv).children('#ex2-node-2')[0]);


            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('append ajax response: '+elapsed);

            //add ui-helper-hidden class to children
            $(documentAjaxDiv).children("tr").each(function(){
                $(this).addClass('ui-helper-hidden');
            });
            divRoot.removeClass('ui-helper-hidden');
            divRoot.removeClass('initialized');
            divRoot.children('td').each(function(){
                $(this).removeClass('loading');
                $(this).children('a, span').each(function(){
                    if(!$(this).hasClass('helptooltip')){
                        $(this).remove();
                    }
                });
            });
            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('children add class ui-helper: '+elapsed);




            //append response to a document fragment
            var frag = document.createDocumentFragment();
            //frag.append(documentAjaxDiv.childNodes);
            for(var i = 0;i<documentAjaxDiv.childNodes.length;i++){
                frag.appendChild(documentAjaxDiv.childNodes[i].cloneNode(true));
            };

            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('append children to frag: '+elapsed);

            divRoot = frag.querySelector('#ex2-node-2');
            $(divRoot).treeTablePreparation();

            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('init: '+elapsed);

            //takes most of the time .... TODO: increase performance
            $(divRoot).expandAjaxFragment();

            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('expand: '+elapsed);


            //append fragment after document table row
            documentRoot.after(frag);
            documentRoot.remove();


            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('append frag to treetable: '+elapsed);
            revealDocument();


            //DEBUG
            elapsed = new Date().getTime() - final_start;
            console.log('finished in: '+elapsed+'ms');
        }

    );
    /*var xhr = new XMLHttpRequest();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function() {
        if (this.readyState == 0 || this.status == 0) {
            // not really an error
            return;
        }
        else if (this.readyState==4 && this.status==200) {
            var start = new Date().getTime();
            var final_start = start;
            var elapsed;
            $('#ex2-node-2').unbind("click");
            $('#ex2-node-2').unbind("mousedown");
            $('#ex2-node-2').unbind("keydown");
            var timeoutThreshold = 20;
            var documentRoot = $('#ex2-node-2');


            //append response to a div

            var documentAjaxDiv = document.createElement('div');
            $(documentAjaxDiv).append(documentRoot.clone());
            $(documentAjaxDiv).append(xhr.responseText);
            var divRoot = $($(documentAjaxDiv).children('#ex2-node-2')[0]);


            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('append ajax response: '+elapsed);

            //add ui-helper-hidden class to children
            $(documentAjaxDiv).children("tr").each(function(){
                    $(this).addClass('ui-helper-hidden');
            });
            divRoot.removeClass('ui-helper-hidden');
            divRoot.removeClass('initialized');
            divRoot.children('td').each(function(){
                $(this).removeClass('loading');
                $(this).children('a, span').each(function(){
                    if(!$(this).hasClass('helptooltip')){
                        $(this).remove();
                    }
                });
            });
            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('children add class ui-helper: '+elapsed);




            //append response to a document fragment
            var frag = document.createDocumentFragment();
            //frag.append(documentAjaxDiv.childNodes);
            for(var i = 0;i<documentAjaxDiv.childNodes.length;i++){
              frag.appendChild(documentAjaxDiv.childNodes[i].cloneNode(true));
            };

            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('append children to frag: '+elapsed);

            divRoot = frag.querySelector('#ex2-node-2');
            $(divRoot).treeTablePreparation();

            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('init: '+elapsed);

            //takes most of the time .... TODO: increase performance
            $(divRoot).expandAjaxFragment();

            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('expand: '+elapsed);


            //append fragment after document table row
            documentRoot.after(frag);
            documentRoot.remove();


            //DEBUG
            elapsed = new Date().getTime() - start;
            start = new Date().getTime();
            console.log('append frag to treetable: '+elapsed);
            revealDocument();


            //DEBUG
            elapsed = new Date().getTime() - final_start;
            console.log('finished in: '+elapsed+'ms');
        }
        //console.log(xhr.responseText);
    };
    return xhr.send();*/
};

var loadPreparations = function(){
    $(document).undelegate('#ex2-node-4',"click",loadPreparations);
    $('#ex2-node-4').children('td').addClass('loading');
    var url = $('#ex2-node-4').attr('data-url');
    return $.post(url,function(data) {
            $('#ex2-node-4').unbind("click");
            $('#ex2-node-4').unbind("mousedown");
            $('#ex2-node-4').unbind("keydown");
            var timeoutThreshold = 20;
            var preparationRoot = $('#ex2-node-4');
            preparationRoot.after('<tr id="preparation-ajax" style="display:none"></tr>'); //style="display:none";
            var preparationAjaxDiv = $('#preparation-ajax');

            preparationAjaxDiv.append(data);

            var children = preparationAjaxDiv.children("tr");
            var index = 0;
            var timeoutFunction = function(){
                for(;index < children.length;index++){
                    $(children[index]).addClass('ui-helper-hidden');
                    if (index + 1 < children.length && index % timeoutThreshold == 0) {
                        setTimeout(timeoutFunction, 0);
                    }

                }

            };
            timeoutFunction();
            /*preparationAjaxDiv.children("tr").each(function() {
                $(this).addClass('ui-helper-hidden');
            });*/

            //preparationRoot.clone()
            preparationRoot.removeClass('initialized');
            preparationRoot.clone().prependTo('#preparation-ajax');

            var preparationAjaxDivRoot = preparationAjaxDiv.find('#ex2-node-4');

            children = preparationAjaxDivRoot.children('td');
            index = 0;
            timeoutFunction = function(){
                for(;index < children.length;index++){
                    $(children[index]).children('a, span').each(function(){
                        if(!$(this).hasClass('helptooltip')){
                            $(this).remove();
                        }
                    });
                    if (index + 1 < children.length && index % timeoutThreshold == 0) {
                        setTimeout(timeoutFunction, 0);
                    }

                }

            };
            timeoutFunction();
           /* preparationAjaxDivRoot.children('td').each(function(){
                $(this).children('a, span').each(function(){
                    if(!$(this).hasClass('helptooltip')){
                        $(this).remove();
                    }
                });
            });*/
            preparationAjaxDivRoot.treeTablePreparation();

            //$("#ex2-node-4").removeClass('initialized');
            //$("#ex2-node-4").treeTablePreparation();
            var next = preparationRoot;
            children = $("#preparation-ajax").children();
            index = 0;
            timeoutFunction = function(){
                for(;index < children.length;index++){
                    $(children[index]).children('td').removeClass('loading');
                    $(children[index]).insertAfter(next);
                    next = $(children[index]);
                }
                if (index + 1 < children.length && index % timeoutThreshold == 0) {
                    setTimeout(timeoutFunction, 0);
                }
            }
            timeoutFunction();
            /*$("#preparation-ajax").children().each(function(){
                $(this).children('td').removeClass('loading');
                $(this).insertAfter(next);
                next = $(this);
            });*/
            //bindingPreparation(preparationAjaxDivRoot);
            preparationRoot.remove();
            $('#preparation-ajax').remove();
            preparationAjaxDivRoot.expand();
        });


};



/*
 * jQuery treeTable Plugin VERSION
 * http://ludo.cubicphuse.nl/jquery-plugins/treeTable/doc/
 *
 * Copyright 2011, Ludo van den Boom
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */
(function($) {
    // Helps to make options available to all functions
    // TODO: This gives problems when there are both expandable and non-expandable
    // trees on a page. The options shouldn't be global to all these instances!
    var options;
    var defaultPaddingLeft;
    var persistStore;

    $.fn.treeTablePreparation = function(opts){
        options = $.extend({}, $.fn.treeTable.defaults, opts);

        if(options.persist) {
            persistStore = new Persist.Store(options.persistStoreName);
        }

        return this.each(function() {
            /* $(this).siblings().filter("tr[id^='ex2']").each(function() {
             // Skip initialized nodes.
             if (!$(this).hasClass('initialized')) {
             // Set child nodes to initial state if we're in expandable mode.
             if( options.expandable && options.initialState == "collapsed") {
             $(this).addClass('ui-helper-hidden');
             }

             }
             });*/

            defaultPaddingLeft = parseInt($($(this).children("td")[options.treeColumn]).css('padding-left'), 10);
            initialize($(this));
        });
    };

    $.fn.treeTable = function(opts) {
        options = $.extend({}, $.fn.treeTable.defaults, opts);

        if(options.persist) {
            persistStore = new Persist.Store(options.persistStoreName);
        }

        doBinding(this);

        return this.each(function() {
            $(this).addClass("treeTable").find("tbody tr").each(function() {
                // Skip initialized nodes.
                if (!$(this).hasClass('initialized')) {
                    var isRootNode = ($(this)[0].className.search(options.childPrefix) == -1);

                    // To optimize performance of indentation, I retrieve the padding-left
                    // value of the first root node. This way I only have to call +css+
                    // once.
                    if (isRootNode && isNaN(defaultPaddingLeft)) {
                        defaultPaddingLeft = parseInt($($(this).children("td")[options.treeColumn]).css('padding-left'), 10);
                    }

                    // Set child nodes to initial state if we're in expandable mode.
                    if(!isRootNode && options.expandable && options.initialState == "collapsed") {
                        $(this).addClass('ui-helper-hidden');
                    }

                    // If we're not in expandable mode, initialize all nodes.
                    // If we're in expandable mode, only initialize root nodes.
                    if(!options.expandable || isRootNode) {
                        if($(this).attr('id') == 'ex2-node-2' || $(this).attr('id') == 'ex2-node-4') {
                            initializeEmptyRoot($(this));
                        }
                        else {
                            initialize($(this));
                        }
                        $(this).addClass("collapsed");
                    }
                }
            });
        });
    };

    $.fn.treeTable.defaults = {
        childPrefix: "child-of-",
        clickableNodeNames: false,
        expandable: true,
        indent: 19,
        initialState: "collapsed",
        onNodeShow: null,
        onNodeHide: null,
        treeColumn: 0,
        persist: false,
        persistStoreName: 'treeTable',
        stringExpand: "Expand",
        stringCollapse: "Collapse"
    };

    //Expand all nodes
    $.fn.expandAll = function() {
        $(this).find("tr").each(function() {
            $(this).expand();
        });
    };

    $.fn.expandAllDescendants = function(){
        //expandAllFromHere -> collapse
        if($(this).hasClass('expanded')){
            return false;
        }
        $(this).expand();

        var array = allChildrenOf($(this)).toArray();
        chunkedExpand(array);
        /*
         allChildrenOf($(this)).each(function(){
         $(this).expand();
         });*/
    };

    function chunkedExpand(array){
        setTimeout(function(){
            var item = array.shift();
            $(item).expand();

            if (array.length > 0){
                expandTimout = setTimeout(arguments.callee, 0);
            }
        }, 10);
    }

    //Collapse all nodes
    $.fn.collapseAll = function() {
        $(this).find("tr").each(function() {
            $(this).collapse();
        });
    };

    // Recursively hide all node's children in a tree
    $.fn.collapse = function() {
        if (typeof expandTimout !== 'undefined') clearTimeout(expandTimout);
        return this.each(function() {
            $(this).removeClass("expanded").addClass("collapsed");

            if($(this).attr('id').length == 10) $(this).children('td').children('span').children('.expandAllFromHere').removeClass('hidden');
            $(this).children('td').children('span').children('.expandAllFromHere').children('b').text('All');

            if (options.persist) {
                persistNodeState($(this));
            }

            childrenOf($(this)).each(function() {
                if(!$(this).hasClass("collapsed")) {
                    $(this).collapse();
                }

                $(this).addClass('ui-helper-hidden');

                if($.isFunction(options.onNodeHide)) {
                    options.onNodeHide.call(this);
                }

            });
        });
    };

    $.fn.expandAjaxFragment = function(){
        return $(this).each(function() {
            $(this).removeClass("collapsed").addClass("expanded");

            //$(this).children('td').children('span').children('.expandAllFromHere').addClass('hidden');
            $(this).children('td').children('span').children('.expandAllFromHere').children('b').text('Close');

            if (options.persist) {
                persistNodeState($(this));
            }
            var children = childrenOf($(this))
            var index = 0;
            var timeoutFunction = function(){
                for(;index < children.length;index++){
                    initialize($(children[index]));

                    if($(children[index]).is(".expanded.parent")) {
                        $(children[index]).expand();
                    }

                    $(children[index]).removeClass('ui-helper-hidden');

                    if($.isFunction(options.onNodeShow)) {
                        options.onNodeShow.call(children[index]);
                    }
                    if (index + 1 < children.length ) {
                        setTimeout(timeoutFunction, 0);
                    }

                }

            };
            timeoutFunction();
            /*childrenOf($(this)).each(function() {
                initialize($(this));

                if($(this).is(".expanded.parent")) {
                    $(this).expand();
                }

                $(this).removeClass('ui-helper-hidden');

                if($.isFunction(options.onNodeShow)) {
                    options.onNodeShow.call(this);
                }
            });*/
        });
    };

    $.fn.expand = function() {
        return this.each(function() {
            $(this).removeClass("collapsed").addClass("expanded");

            //$(this).children('td').children('span').children('.expandAllFromHere').addClass('hidden');
            $(this).children('td').children('span').children('.expandAllFromHere').children('b').text('Close');

            if (options.persist) {
                persistNodeState($(this));
            }

            childrenOf($(this)).each(function() {
                initialize($(this));

                if($(this).is(".expanded.parent")) {
                    $(this).expand();
                }

                $(this).removeClass('ui-helper-hidden');

                if($.isFunction(options.onNodeShow)) {
                    options.onNodeShow.call(this);
                }
            });
        });
    };



    // Reveal a node by expanding all ancestors
    $.fn.reveal = function() {
        $(ancestorsOf($(this)).reverse()).each(function() {
            initialize($(this));
            $(this).expand().show();
        });

        return this;
    };



    // Add an entire branch to +destination+
    $.fn.appendBranchTo = function(destination) {
        var node = $(this);
        var parent = parentOf(node);

        var ancestorNames = $.map(ancestorsOf($(destination)), function(a) { return a.id; });

        // Conditions:
        // 1: +node+ should not be inserted in a location in a branch if this would
        //    result in +node+ being an ancestor of itself.
        // 2: +node+ should not have a parent OR the destination should not be the
        //    same as +node+'s current parent (this last condition prevents +node+
        //    from being moved to the same location where it already is).
        // 3: +node+ should not be inserted as a child of +node+ itself.
        if($.inArray(node[0].id, ancestorNames) == -1 && (!parent || (destination.id != parent[0].id)) && destination.id != node[0].id) {
            indent(node, ancestorsOf(node).length * options.indent * -1); // Remove indentation

            if(parent) { node.removeClass(options.childPrefix + parent[0].id); }

            node.addClass(options.childPrefix + destination.id);
            move(node, destination); // Recursively move nodes to new location
            indent(node, ancestorsOf(node).length * options.indent);
        }

        return this;
    };

    // Add reverse() function from JS Arrays
    $.fn.reverse = function() {
        return this.pushStack(this.get().reverse(), arguments);
    };

    $.fn.toggleAllDescendants = function(){
        if($(this).hasClass("collapsed")) {
            $(this).expandAllDescendants();
        }else{
            $(this).collapse();
        }
    };

    // Toggle an entire branch
    $.fn.toggleBranch = function() {
        if($(this).hasClass("collapsed")) {
            $(this).expand();
        } else {
            $(this).collapse();
        }

        return this;
    };

    // === Private functions

    function ancestorsOf(node) {
        var ancestors = [];
        while(node = parentOf(node)) {
            ancestors[ancestors.length] = node[0];
        }
        return ancestors;
    };

    function allChildrenOf(node){
        return $(node).siblings("tr[class^=" + options.childPrefix + node[0].id+ "]" );
    };

    function childrenOf(node) {
        return $(node).siblings("tr." + options.childPrefix + node[0].id);
    };

    function getPaddingLeft(node) {
        var paddingLeft = parseInt(node[0].style.paddingLeft, 10);
        return (isNaN(paddingLeft)) ? defaultPaddingLeft : paddingLeft;
    }

    function indent(node, value) {
        var cell = $(node.children("td")[options.treeColumn]);
        cell[0].style.paddingLeft = getPaddingLeft(cell) + value + "px";

        childrenOf(node).each(function() {
            indent($(this), value);
        });
    };

    function doBinding(treeTableRoot){
        $('body').bind('click',function(e){
            var target = $(e.target);
            if(target.is('tr.parent')){
                e.preventDefault();
                target.toggleBranch();
            }else if(target.is('td') && target.parent('tr.parent').length>0){
                e.preventDefault();
                target.parent().toggleBranch();
            }else if(target.is('a.expander') || (target.is('b') && target.parent().parent().is('tr.parent'))){
                e.preventDefault();
                target.parent().parent().toggleBranch();
            }else if(target.is('li.annotation')){
                e.preventDefault();
                jumpToAnnotation(target);
            }else if(target.is('li.docannotation')){
                e.preventDefault();
                jumpToDocAnnotation(target);
            }

        });

        $('body').bind('keydown',function(e){
            var target = $(e.target);
            if(e.keyCode == 13 && target.is('a.expander')){
                e.preventDefault();
                target.parent().parent().toggleBranch();
            }
        });
    }
    function initialize(node) {
        if(!node.hasClass("initialized")) {
            node.addClass("initialized");
            //node.addClass("initialized collapsed");

            var childNodes = $(node).siblings("tr." + options.childPrefix + node[0].id);
            //var isRootNode = (node.className.search(options.childPrefix) == -1);
            if((!node.hasClass("parent") && childNodes.length > 0 )) {
                node.addClass("parent");
            }

            if(node.hasClass("parent")) {
                var cell = $(node.children("td")[options.treeColumn]);
                var padding = getPaddingLeft(cell) + options.indent;

                childNodes.each(function() {
                    $(this).children("td")[options.treeColumn].style.paddingLeft = padding + "px";
                });

                if(options.expandable) {
                    var newLink = '<a href="#" title="' + options.stringExpand + '" style="margin-left: -' + options.indent + 'px; padding-left: ' + options.indent + 'px" class="expander"></a><span style="float:right;"><a href="#" title="Expand all from here" style="margin-right:5px;padding-left:15px;" class="expandAllFromHere hidden"><b>All</b></a></span>';

                    if(options.clickableNodeNames) {
                        cell.wrapInner(newLink);
                    } else {
                        cell.html(newLink+cell.html());
                    }
                    //expand all from node
                    //console.log(cell.children('span').children('.expandAllFromHere'));

                    if(node.attr('id').length == 10){
                        //cell.children('span').children('.expandAllFromHere').click(function() {
                            //$(this).addClass('loading');
                        //    node.toggleAllDescendants(); return false;}).mousedown(function() { return false; });
                        cell.children('span').children('.expandAllFromHere').keydown(function(e) { if(e.keyCode == 13) { node.toggleAllDescendants(); return false; }});
                        cell.children('span').children('.expandAllFromHere').removeClass('hidden');
                    }

                    if (options.persist && getPersistedNodeState(node)) {
                        node.addClass('expanded');
                    }

                    // Check for a class set explicitly by the user, otherwise set the default class
                    if(!(node.hasClass("expanded") || node.hasClass("collapsed"))) {
                        node.addClass(options.initialState);
                    }

                    if(node.hasClass("expanded")) {
                        node.expand();
                    }
                }
            }
        }
        //node.addClass("initialized");
    };

    function initializeEmptyRoot(node) {
        if(!node.hasClass("initialized")) {
            node.addClass("initialized");

            //node.addClass("parent");

            var childNodes = $(node).siblings("tr." + options.childPrefix + node[0].id);
            if((!node.hasClass("parent") && childNodes.length > 0 )) {
                node.addClass("parent");
            }

            var cell = $(node.children("td")[options.treeColumn]);
            var padding = getPaddingLeft(cell) + options.indent;

            childNodes.each(function() {
                $(this).children("td")[options.treeColumn].style.paddingLeft = padding + "px";
            });

            if(options.expandable) {
                if(node.attr('id').length == 10) {
                    var newLink = '<a href="#" title="' + options.stringExpand + '" style="margin-left: -' + options.indent + 'px; padding-left: ' + options.indent + 'px" class="expander"></a><span style="float:right;"><a href="#" title="Expand all from here" style="margin-right:5px;padding-left:15px;" class="expandAllFromHere"><b>All</b></a></span>';
                    //console.log(options.stringExpand);
                }
                else {
                    var newLink = '<a href="#" title="' + options.stringExpand + '" style="margin-left: -' + options.indent + 'px; padding-left: ' + options.indent + 'px" class="expander"></a>';
                }

                if(options.clickableNodeNames) {
                    //var docLink = '<a href="#" title="' + options.stringExpand + '" style="margin-left: -' + options.indent + 'px; padding-left: ' + options.indent + 'px" class="loading"></a>';
                    cell.wrapInner(newLink);
                } else {
                    //var docLink = '<a href="#" title="' + options.stringExpand + '" style="margin-left: -' + options.indent + 'px; padding-left: ' + options.indent + 'px" class="loading"></a>';
                    cell.html(newLink+cell.html());
                }
                //expand all from node
                //console.log(cell.children('span').children('.expandAllFromHere'));

                if(node.attr('id').length == 10){
                    //cell.children('span').children('.expandAllFromHere').addClass('loading');
                    //cell.children('span').children('.expandAllFromHere').click(function() {
                    //    node.toggleAllDescendants(); return false;}).mousedown(function() { return false; });
                    cell.children('span').children('.expandAllFromHere').keydown(function(e) { if(e.keyCode == 13) { node.toggleAllDescendants(); return false; }});
                }

                if (options.persist && getPersistedNodeState(node)) {
                    node.addClass('expanded');
                }

                // Check for a class set explicitly by the user, otherwise set the default class
                if(!(node.hasClass("expanded") || node.hasClass("collapsed"))) {
                    //node.addClass(options.initialState).addClass('loading');
                    node.addClass(options.initialState);
                }

                if(node.hasClass("expanded")) {
                    node.expand();
                }
            }

        }
    };

    function move(node, destination) {
        node.insertAfter(destination);
        childrenOf(node).reverse().each(function() { move($(this), node[0]); });
    };

    function parentOf(node) {
        var classNames = node[0].className.split(' ');

        for(var key=0; key<classNames.length; key++) {
            if(classNames[key].match(options.childPrefix)) {
                return $(node).siblings("#" + classNames[key].substring(options.childPrefix.length));
            }
        }

        return null;
    };

    //saving state functions, not critical, so will not generate alerts on error
    function persistNodeState(node) {
        if(node.hasClass('expanded')) {
            try {
                persistStore.set(node.attr('id'), '1');
            } catch (err) {

            }
        } else {
            try {
                persistStore.remove(node.attr('id'));
            } catch (err) {

            }
        }
    }

    function getPersistedNodeState(node) {
        try {
            return persistStore.get(node.attr('id')) == '1';
        } catch (err) {
            return false;
        }
    }
})(jQuery);