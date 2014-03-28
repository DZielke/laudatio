/*
 * jquery.facetview.js
 *
 * displays faceted browse results by querying a specified index
 * can read config locally or can be passed in as variable when executed
 * or a config variable can point to a remote config
 * config options include specifying SOLR or ElasticSearch index
 * 
 * created by Mark MacGillivray - mark@cottagelabs.com
 *
 * http://facetview.cottagelabs.com
 *
 */

// first define the bind with delay function from (saves loading it separately) 
// https://github.com/bgrins/bindWithDelay/blob/master/bindWithDelay.js
(function ($) {
    $.fn.bindWithDelay = function (type, data, fn, timeout, throttle) {
        var wait = null;
        var that = this;

        if ($.isFunction(data)) {
            throttle = timeout;
            timeout = fn;
            fn = data;
            data = undefined;
        }

        function cb() {
            var e = $.extend(true, { }, arguments[0]);
            var throttler = function () {
                wait = null;
                fn.apply(that, [e]);
            };

            if (!throttle) {
                clearTimeout(wait);
            }
            if (!throttle || !wait) {
                wait = setTimeout(throttler, timeout);
            }
        }

        return this.bind(type, data, cb);
    }
})(jQuery);






// now the facetview function
(function ($) {


    $.fn.facetview = function (options) {
        //var facet_array = new Array();
        //var append;
        //var data_arr = new Array();

        // some big default values

        var resdisplay = [
            [
                {
                    "field":"author.name"
                },
                {
                    "pre":"(",
                    "field":"year",
                    "post":")"
                }
            ],
            [
                {
                    "pre":"<strong>",
                    "field":"title",
                    "post":"</strong>"
                }
            ],
            [
                {
                    "field":"howpublished"
                },
                {
                    "pre":"in <em>",
                    "field":"journal.name",
                    "post":"</em>,"
                },
                {
                    "pre":"<em>",
                    "field":"booktitle",
                    "post":"</em>,"
                },
                {
                    "pre":"vol. ",
                    "field":"volume",
                    "post":","
                },
                {
                    "field":"pages"
                },
                {
                    "field":"publisher"
                }
            ],
            [
                {
                    "field":"link.url"
                }
            ]
        ];


        // specify the defaults
        var defaults = {
            "config_file":false,
            //"default_filters":["collection","journal","author","year"],
            "facets":[],
            "result_display":resdisplay,
            "ignore_fields":["_id", "_rev"],
            "description":"",
            "search_url":"",
            "search_index":"elasticsearch",
            "default_url_params":{},
            "freetext_submit_delay":"700",
            "query_parameter":"q",
            "q":"*:*",
            "predefined_filters":{},
            "paging":{}
        };

        // and add in any overrides from the call
        var options = $.extend(defaults, options);
        !options.paging.size ? options.paging.size = 10 : "";
        !options.paging.from ? options.paging.from = 0 : "";






        // ===============================================
        // functions to do with filters
        // ===============================================

//----- show the filter values--------------------------------------------------

        // show the filter values
        var showfiltervals = function (event) {
            event.preventDefault();
            var tooltip_close = 'Close all layers of the section.'; //'<span class="helptooltip"><div id="facetview_tooltip"><img src="http://depot1-7.cms.hu-berlin.de/repository/app/webroot/img/help.png" width="20"/></div><p>Close all layers of the section.</p></span>';
            var tooltip_open = 'Open all layers of the section.';//'<span class="helptooltip"><div id="facetview_tooltip"><img src="http://depot1-7.cms.hu-berlin.de/repository/app/webroot/img/help.png" width="20"/></div><p>Open all layers of the section.</p></span>';

            if (!$(this).hasClass('filter_deactivated')) {
                if ($(this).hasClass('facetview_open')) {
                    $(this).children('i').removeClass('icon-minus');
                    $(this).children('i').children('p').html(tooltip_open);
                    $(this).children('i').addClass('icon-plus');
                    $(this).removeClass('facetview_open');
                    $('#facetview_' + $(this).attr('rel'), obj).children().find('.facetview_filtervalue').hide();
                    $(this).siblings('.facetview_filteroptions').hide();
                } else {
                    $(this).children('i').removeClass('icon-plus');
                    $(this).children('i').addClass('icon-minus');
                    $(this).children('i').children('p').html(tooltip_close);
                    //$(this).children('a').addClass('.facetview_filterchoice');
                    $(this).addClass('facetview_open');
                    //alert(JSON.stringify($(this).attr('rel')));
                    $('#facetview_' + $(this).attr('rel'), obj).children().find('.facetview_filtervalue').show();
                    $(this).siblings('.facetview_filteroptions').show();
                }
            }
        };

        /*
         // show the filter values
         var showfiltervals = function(event) {
         event.preventDefault();
         if ( $(this).hasClass('facetview_open') ) {
         $(this).children('i').replaceWith('<i class="icon-plus"></i>')
         $(this).removeClass('facetview_open');
         $('#facetview_' + $(this).attr('rel') ).children().hide();
         } else {
         $(this).children('i').replaceWith('<i class="icon-minus"></i>')
         $(this).addClass('facetview_open');
         $('#facetview_' + $(this).attr('rel') ).children().show();
         }
         }
         */


//----- function to perform for sorting of filters------------------------------
        var sortfilters = function (event) {
            event.preventDefault();
            var sortwhat = $(this).attr('href');
            var which = 0;
            for (var item in options.facets) {
                if ('field' in options.facets[item]) {
                    if (options.facets[item]['field'] == sortwhat) {
                        which = item;
                    }
                }
            }
            if ($(this).hasClass('facetview_count')) {
                options.facets[which]['order'] = 'count';
            } else if ($(this).hasClass('facetview_term')) {
                options.facets[which]['order'] = 'term';
            } else if ($(this).hasClass('facetview_rcount')) {
                options.facets[which]['order'] = 'reverse_count';
            } else if ($(this).hasClass('facetview_rterm')) {
                options.facets[which]['order'] = 'reverse_term';
            }
            dosearch();
            if (!$(this).parent().parent().siblings('.facetview_filtershow').hasClass('facetview_open')) {
                $(this).parent().parent().siblings('.facetview_filtershow').trigger('click');
            }
        };

//----- adjust how many results are shown---------------------------------------
        var morefacetvals = function (event) {
            event.preventDefault();
            var morewhat = options.facets[ $(this).attr('rel') ];
            if ('size' in morewhat) {
                var currentval = morewhat['size']
            } else {
                var currentval = 10
            }
            var newmore = prompt('Currently showing ' + currentval +
                '. How many would you like instead?');
            if (newmore) {
                options.facets[ $(this).attr('rel') ]['size'] = parseInt(newmore);
                $(this).html('show up to ' + newmore);
                dosearch();
                if (!$(this).parent().parent().siblings('.facetview_filtershow').hasClass('facetview_open')) {
                    $(this).parent().parent().siblings('.facetview_filtershow').trigger('click')
                }
            }
        };

//----- set the available filter values based on results------------------------
        var putvalsinfilters = function (data) {
            // for each filter setup, find the results for it and append them to the relevant filter
            //console.log('filterdata %o',data);
            for (var each in options.facets) {

                var facetname = options.facets[each]['display'].replace(/ /g, "");
                var ulID = 'facetview_' + options.facets[each]['field'].replace(/\./gi, '_') + options.facets[each]['display'].replace(/ /g, "");
                var list = $('ul[id="' + ulID + '"]');
                list.children().remove();
                list.siblings('div.btn-group').remove();
                list.siblings('div.facetrange').remove();
                list.siblings('div').children('a.facetitem_truncator').parent().remove();

                //options.facets[each]['field']+'-'+
                if (options.facets[each]['subfacets']) {
                    for (var i = 0; i < options.facets[each]['subfacets'].length; i++) {
                        var subfacetname = facetname + '-' + options.facets[each]['subfacets'][i][1];
                        var records = data["facets"][subfacetname];
                        putRecordsInSubfacet(options.facets[each], options.facets[each]['subfacets'][i][2], records);
                    }
                    //var records = data["facets"][facetname+'-other'];
                    //putRecordsInSubfacet(options.facets[each],'Other',records);
                } else {
                    //console.log('facet: %s data: %o',facetname,data["facets"][facetname])
                    var records = data["facets"][facetname];


                    putRecordsInFacet(options.facets[each], records);
                    if ( options.facets[each]['range'] != undefined) {
                        putRangeInFacet(options.facets[each]);
                    }
                }

                //if no filter options appended, deactivate
                if (list.children().length == 0) {
                    list.parent().siblings().each(function () {
                        if ($(this).hasClass('facetview_open')) {
                            $(this).children('i').removeClass('icon-minus');
                            $(this).children('i').addClass('icon-plus');
                            $(this).removeClass('facetview_open');
                            $('#facetview_' + $(this).attr('rel'), obj).children().find('.facetview_filtervalue').hide();
                            $(this).siblings('.facetview_filteroptions').hide();
                            $(this).addClass('filter_deactivated');
                            $(this).parent().addClass('filter_deactivated');
                        }
                    });
                }
                else {
                    list.parent().siblings().each(function () {
                        $(this).removeClass('filter_deactivated');
                        $(this).parent().removeClass('filter_deactivated');

                    });
                }

                if (!$('.facetview_filtershow[rel="' + ulID + '"]').hasClass('facetview_open')) {
                    //list.children().show();
                }
            }
            $('.facetview_filterchoice').bind('click', clickfilterchoice);
            $('.facetview_facetdaterange').bind('click', rangedatefilterchoice);
            $('.facetview_facetextentrange').bind('click', rangeextentfilterchoice);
            $('.toggle_range_input').bind('click', toggleRangeInput);
            $('.toggle_items_order').bind('click', toggleItemsOrder);

        };


        // ===============================================
        // functions to do with filter options
        // ===============================================
        var putRangeInFacet = function (facet) {
            var rangeClass;
            if(facet['range'] == 'extent'){
                rangeClass = 'facetextentrange';
            }else if(facet['range'] == 'date'){
                rangeClass = 'facetdaterange';
            }
            var ulID = 'facetview_' + facet['field'].replace(/\./gi, '_') + facet['display'].replace(/ /g, "");
            var list = $('ul[id="' + ulID + '"]').parent();
                var append = '<div class="facetrange hide_facetitem"><input ref="facetview_lowrangeval" type="text" maxlength="10"> - ' +
                    '<input ref="facetview_highrangeval" type="text" maxlength="10">' +
                    //<a class="facetview_rangechoice" rel="' + facet['field'] +'" href=#>go</a>'+
                    '<a class="rangebtn facetview_'+rangeClass+'" title="make a range selection on this filter" rel="' + facet['field'] + '" href="#" style="color:#aaa;"></a>' +
                    '</div>';

                list.prepend(append);
                append = '<div style="float:right;" class="btn-group">' +
                    '<a style="margin-left:10px;" class="btn dropdown-toggle" data-toggle="dropdown" href="#">' +
                    '<i class="icon-cog"></i> <span class="caret"></span></a>'+
                    '<ul class="dropdown-menu">' +
                    '<li><a class="toggle_range_input" href="">show range filter</a></li>'+
                    '<li><a class="toggle_items_order" href="">order ascending</a></li>'+
                    '</ul>' +
                    '</div>';
                list.prepend(append);
        };

        var putRecordsInSubfacet = function (facet, display, records) {
            var displaySet = false;
            var ulID = 'facetview_' + facet['field'].replace(/\./gi, '_') + facet['display'].replace(/ /g, "");
            var list = $('ul[id="' + ulID + '"]');
            for (var item in records) {//item-> jahres/monatszahlen; record[item]->anzahl
                var filterselected = false;
                if (records[item] == 0) {
                    filterselected = true;
                }
                else {
                    $('.facetview_filterselected').each(function () { //for each selected filter choice
                        var filteredHref = $(this).attr('href').trim();
                        //var filteredRel = $(this).attr('rel').trim();
                        if (filteredHref == item) {//filter choice selected == current filterchoice
                            filterselected = true;
                        }
                    });
                }


                if (!filterselected) {
                    if ((item != "N/A") && (item != "n")) {
                        if (!displaySet) {
                            displaySet = true;
                            var subname = '<li class="subfacet">' + display + '</li>';
                            list.append(subname);
                        }

                        var append = '<li style="display: list-item;"><a class="facetview_filterchoice' +
                            '" rel="' + facet['field'] + '" href="' + item + '">'
                            + item +
                            ' (' + records[item] + ')</a></li>';

                        list.append(append);
                    }
                }
            }
        };

        var putRecordsInFacet = function (facet, records) {
            var ulID = 'facetview_' + facet['field'].replace(/\./gi, '_') + facet['display'].replace(/ /g, "");
            var list = $('ul[id="' + ulID + '"]');
            var isDate = facet['range'] != undefined && facet['range'] == 'date'
            var recordsCount = 0;
            if(isDate){
                var  key;
                for (key in records) {
                    if (records.hasOwnProperty(key)) recordsCount++;
                }
            }
            var itemsInFacet = 0
            for (var item in records) {
                var filterselected = false;
                var date;
                if(isDate){
                    if(item.length > 12){
                        var date = new Date(+item);
                        var dateString = + date.getFullYear()/*+
                            "-"+ ('0' + (date.getMonth()+1)).slice(-2)+
                            "-"+('0' + date.getDate()).slice(-2);*/
                    }
                }


                if (records[item] == 0) {
                    filterselected = true;
                }else {
                    $('.facetview_filterselected').each(function () { //for each selected filter choice
                        var filteredHref = $(this).attr('href').trim();
                        //var filteredRel = $(this).attr('rel').trim();

                        if (filteredHref == item ||( date != undefined && dateString == filteredHref)) {//filter choice selected == current filterchoice
                            filterselected = true;
                        }
                    });
                }

                //nur 4stellige Jahrszahlen, Rest vermutlich Monate
                if (!filterselected) {
                    var trunc = '';
                    if(isDate){
                        if(itemsInFacet<(recordsCount-options.show_facetitems)){
                            trunc = 'class="trunc hide_facetitem"';
                        }else{
                            trunc = 'class="show_facetitem"';
                        }
                    }else{
                        if(itemsInFacet>=options.show_facetitems){
                            trunc = 'class="trunc hide_facetitem"';
                        }else{
                            trunc = 'class="show_facetitem"';
                        }
                    }
                   /* if (((facet['field'] == 'teiCorpus.teiHeader.fileDesc.publicationStmt.date.@when') && (item.length == 4)) || ((facet['field'] == 'teiCorpus.teiCorpus.teiHeader.teiHeader.fileDesc.publicationStmt.date.$') && (item.length > 1)) && (facet['field'] != 'teiCorpus.teiHeader.fileDesc.extent.@n')) {

                    }
                    else if ((facet['field'] != 'teiCorpus.teiHeader.fileDesc.publicationStmt.date.@when') && (facet['field'] != 'teiCorpus.teiCorpus.teiHeader.teiHeader.encodingDesc.schemaSpec.elementSpec.valList.valItem.@ident') && (facet['field'] != 'teiCorpus.teiHeader.encodingDesc.tagsDecl.namespace.@name') && (facet['field'] != 'teiCorpus.teiCorpus.teiHeader.teiHeader.fileDesc.publicationStmt.date.$') && (item.length > 1) && (facet['field'] != 'teiCorpus.teiHeader.fileDesc.extent.@n')) {
                        var append = '<li style="display: list-item;"><a class="facetview_filterchoice' +
                            '" rel="' + facet['field'] + '" href="' + item + '">'
                            //+ !item != 'N/A' ? item : '' +
                            + item +
                            ' (' + records[item] + ')</a></li>';
                        list.append(append);
                    }

                    else*/ if (facet['field'] == 'teiCorpus.teiHeader.encodingDesc.tagsDecl.namespace.@name') {
                        if ((item == "pb") || (item == "div") || (item == "lb") || (item == "p") || (item == "line") || (item == "pb_n")) {
                            var append = '<li '+trunc+'><a class="facetview_filterchoice' +
                                '" rel="' + facet['field'] + '" href="' + item + '">'
                                + item +
                                ' (' + records[item] + ')</a></li>';
                            list.append(append);
                            itemsInFacet++;
                        }
                    } else  if(date){
                        var yearOnly = '';
                        if((dateString+'').match(/^\d+$/) != null)
                            yearOnly = ' facetview_yearonlychoice';
                        var append = '<li '+trunc+'><a class="facetview_filterchoice' + yearOnly+
                            '" rel="' + facet['field'] + '" href="' + dateString + '">'
                             + dateString +
                            ' (' + records[item] + ')</a></li>';
                        list.prepend(append);
                        itemsInFacet++;
                    }else{
                        if (item != "N/A") {
                            var append = '<li '+trunc+'><a class="facetview_filterchoice' +
                                '" rel="' + facet['field'] + '" href="' + item + '">'
                                + item +
                                ' (' + records[item] + ')</a></li>';
                            list.append(append);
                            itemsInFacet++;
                        }
                    }





                    /*
                     else if (facet['field'] == 'teiCorpus.teiCorpus.teiHeader.teiHeader.encodingDesc.schemaSpec.elementSpec.valList.valItem.@ident') {
                     //alert(item);
                     if ((item == "text") || (item == "tok") || (item == "clean") || (item == "norm")) {
                     var append = '<li style="display: list-item;"><a class="facetview_filterchoice' +
                     '" rel="' + facet['field'] + '" href="' + item + '">'
                     + item +
                     ' ('+ records[item]+')</a></li>';
                     $('ul[id="facetview_' + facet['field'].replace(/\./gi,'_')+'"]').append(append);
                     }
                     }
                     */


                }
            }
            if(itemsInFacet>options.show_facetitems){
                list.parent().append('<div style="margin:-8px 0 0 25px;">(<a href="#" class="facetitem_truncator more">more</a>)</div>');
            }
        };

//----- show the advanced functions---------------------------------------------
        var showadvanced = function (event) {
            event.preventDefault();
            if ($(this).hasClass('facetview_open')) {
                $(this).removeClass('facetview_open').siblings().hide();
            }
            else {
                $(this).addClass('facetview_open').siblings().show();
            }
        };

//----- add a filter when a new one is provided---------------------------------
        var addfilters = function () {
            options.facets.push({'field':$(this).val()});
            // remove any current filters
            $('#facetview_filters').html("");
            buildfilters();
            dosearch();
        };

//----- set the user admin filters----------------------------------------------
        var advanced = function () {
            var advanceddiv = '<div id="facetview_advanced">' +
                '<a class="facetview_advancedshow" href="">ADVANCED ...</a>' +
                '<p>add filter:<br /><select id="facetview_addfilters"></select></p></div>';
            $('#facetview_filters').after(advanceddiv);
            $('.facetview_advancedshow').bind('click', showadvanced).siblings().hide();
        };

//----- populate the advanced options-------------------------------------------
        var populateadvanced = function (data) {
            // iterate through source keys
            var options = "";
            for (var item in data["records"][0]) {
                options += '<option>' + item + '</option>';
            }
            $('#facetview_addfilters').html("");
            $('#facetview_addfilters').append(options);
            $('#facetview_addfilters').change(addfilters);

        };

        // ===============================================
        // functions to do with building results
        // ===============================================

//----- read the result object and return useful vals depending on if ES or SOLR
//----- returns an object that contains things like ["data"] and ["facets"]-----
        var parseresults = function (dataobj) {
            resultobj = {};
            resultobj["records"] = [];
            resultobj["pid_id"] =[];
            resultobj["item"] = [];
            resultobj["start"] = "";
            resultobj["found"] = "";
            resultobj["facets"] = {};
            if (options.search_index == "elasticsearch") {
                for (var item in dataobj.hits.hits) {
                    resultobj["pid_id"].push(dataobj.hits.hits[item]._id); //220113
                    //alert(dataobj.hits.hits[item]._index);
                    if(dataobj.hits.hits[item].fields !== undefined || dataobj.hits.hits[item]._source){
                        if(dataobj.hits.hits[item]._source !== undefined){
                            resultobj["records"].push(dataobj.hits.hits[item]._source);
                        }else if(dataobj.hits.hits[item].fields !== undefined){
                            resultobj["records"].push(dataobj.hits.hits[item].fields.partial);
                        }
                        resultobj["start"] = "";
                        resultobj["found"] = dataobj.hits.total;
                        resultobj["pid_index"] = dataobj.hits.hits[item]._index;
                        if(resultobj["records"][resultobj["records"].length-1]['teiCorpus'] !== undefined && dataobj.hits.hits[item]['highlight'] !== undefined)
                                resultobj["records"][resultobj["records"].length-1]['teiCorpus']['highlight'] = dataobj.hits.hits[item].highlight;
                    }
                }
                for (item in dataobj.facets) {
                    var facetsobj = {};
                    if(dataobj.facets[item]['_type'] == 'date_histogram'){
                        for (var thing in dataobj.facets[item]["entries"]) {
                            facetsobj[ dataobj.facets[item]["entries"][thing]["time"] ] = dataobj.facets[item]["entries"][thing]["count"];
                        }
                    }else if(dataobj.facets[item]['_type'] == 'histogram'){
                        for (var thing in dataobj.facets[item]["entries"]) {
                            facetsobj[ dataobj.facets[item]["entries"][thing]["key"] ] = dataobj.facets[item]["entries"][thing]["count"];
                        }
                    }else{
                        for (var thing in dataobj.facets[item]["terms"]) {
                            facetsobj[ dataobj.facets[item]["terms"][thing]["term"] ] = dataobj.facets[item]["terms"][thing]["count"];
                        }
                    }
                    resultobj["facets"][item] = facetsobj;
                }
            }
            else {
                resultobj["start"] = "";
                //resultobj["pid_id"] = dataobj.hits.hits[item]._id;
                resultobj["found"] = dataobj.hits.total;
                resultobj["pid_index"] = dataobj.hits.hits[item]._index;

                if (dataobj.facet_counts) {
                    for (var item in dataobj.facet_counts.facet_fields) {
                        var facetsobj = {};
                        var count = 0;
                        for (var each in dataobj.facet_counts.facet_fields[item]) {
                            if (count % 2 == 0) {
                                facetsobj[ dataobj.facet_counts.facet_fields[item][each] ] = dataobj.facet_counts.facet_fields[item][count + 1];
                            }
                            count += 1;
                        }
                        resultobj["facets"][item] = facetsobj;
                    }
                }
            }
            return resultobj;
        };

//----- decrement result set----------------------------------------------------
        var decrement = function (event) {
            event.preventDefault();
            if ($(this).html() != '..') {
                options.paging.from = options.paging.from - options.paging.size;
                options.paging.from < 0 ? options.paging.from = 0 : "";
                dosearch();
            }
        };

        // increment result set
        var increment = function (event) {
            event.preventDefault();
            if ($(this).html() != '..') {
                options.paging.from = parseInt($(this).attr('href'));
                dosearch()
            }
        };

//----- write the metadata to the page------------------------------------------
        var putmetadata = function (data) {
            var metaTmpl = ' \
              <div class="pagination"> \
                <ul> \
                  <!--<li class="prev"><a id="facetview_decrement" href="{{from}}">&laquo; back</a></li>--> \
                  <li class="active"><a>{{from}} &ndash; {{to}} of {{total}}</a></li> \
                  <!--<li class="next"><a id="facetview_increment" href="{{to}}">next &raquo;</a></li>--> \
                </ul> \
              </div> \
              ';
            //$('#facetview_metadata_above').html("Not found...");
            $('#facetview_metadata').html("Not found...");
            if (data.found) {
                var from = options.paging.from + 1;
                var size = options.paging.size;
                !size ? size = 10 : "";
                var to = options.paging.from + size;
                data.found < to ? to = data.found : "";
                var meta = metaTmpl.replace(/{{from}}/g, from);
                meta = meta.replace(/{{to}}/g, to);
                meta = meta.replace(/{{total}}/g, data.found);
                //$('#facetview_metadata_above').html("").append(meta);
                $('#facetview_metadata').html("").append(meta);
                $('#facetview_decrement').bind('click', decrement);
                from < size ? $('#facetview_decrement').html('..') : "";
                $('#facetview_increment').bind('click', increment);
                data.found <= to ? $('#facetview_increment').html('..') : ""
            }

        };

//----- given a result record, build how it should look on the page-------------
        var buildrecord = function (record, index) {
            var project_part_item = {};
            var project_part_size = [];
            var project_part = [];
            var pid_split_index = resultobj['pid_index'];
            var pid_split_id = resultobj['pid_id'];
            var result = '<tr><td>';
            result += ' \
            <div style="float:right;" class="btn-group"> \
                <a style="margin-left:10px;" class="btn dropdown-toggle" data-toggle="dropdown" href="#"> \
                <i class="icon-cog"></i> <span class="caret"></span></a> \
                <ul class="dropdown-menu"> \
                <li><a href="">no options yet...</a></li> \
                </ul> \
               </div>';
            var display = options.result_display;
            var lines = '';
            for (lineitem in display) {
                line = "";
                for (object in display[lineitem]) {
                    var thekey = display[lineitem][object]['field'];
                    parts = thekey.split('.');

                    // TODO: this should perhaps recurse..
                    if (parts.length == 1) {
                        var res = record
                    } else if (parts.length == 2) {
                        var res = record[parts[0]]
                    }
                    var counter = parts.length - 1;

                    if (res && res.constructor.toString().indexOf("Array") == -1) {
                        var thevalue = res[parts[counter]];  // if this is a dict
                    } else {
                        var thevalue = [];
                        for (var row in res) {
                            thevalue.push(res[row][parts[counter]])
                        }
                    }
                    if (thevalue && thevalue.length) {
                        display[lineitem][object]['pre']
                            ? line += display[lineitem][object]['pre'] : false;
                        if (typeof(thevalue) == 'object') {
                            for (var val in thevalue) {
                                val != 0 ? line += ', ' : false;
                                line += thevalue[val]
                            }
                        }
                        else {
                            line += thevalue
                        }
                        display[lineitem][object]['post']
                            ? line += display[lineitem][object]['post'] : false;
                        line += ' '
                    }
                }
                if (line) {
                    lines += line.replace(/^\s/, '').replace(/\s$/, '').replace(/\,$/, '') + "<br />"
                }
            }


            lines ? result += lines : result += "<div>";
            lines ? result += lines : result += "<b>Title:</b> ";

            if (record['teiCorpus']) {
                lines ? result = lines : result += record['teiCorpus']['teiHeader']['fileDesc']['titleStmt']['title']['$'], "", "<div>";
                lines ? result += lines : result += ", ";
                lines ? result = lines : result += record['teiCorpus']['teiHeader']['fileDesc']['publicationStmt']['date']['@when'], "", "<div>";

                lines ? result += lines : result += "<div>";
                lines ? result += lines : result += "<b>Change:</b> Version ";
                lines ? result = lines : result += record['teiCorpus']['teiHeader']['revisionDesc']['change']['@n'], "", "<div>";
                lines ? result += lines : result += " ";

                if (record['teiCorpus']['teiHeader']['fileDesc']['extent'] != undefined && record['teiCorpus']['teiHeader']['fileDesc']['extent']['@type'] != "N/A" && record['teiCorpus']['teiHeader']['fileDesc']['extent']['@type'] == "NA") {
                    lines ? result += lines : result += "<b>Type:</b> ";
                    lines ? result = lines : result += record['teiCorpus']['teiHeader']['revisionDesc']['change']['@type'], "", "<div>";
                }
                lines ? result += lines : result += "<div>";

                if (record['teiCorpus']['teiHeader']['fileDesc']['extent'] != undefined && record['teiCorpus']['teiHeader']['fileDesc']['extent']['@type'] != "N/A" && record['teiCorpus']['teiHeader']['fileDesc']['extent']['@type'] != "NA" && record['teiCorpus']['teiHeader']['fileDesc']['extent']['$'] != "?") {
                    lines ? result += lines : result += "<b>Corpus Size:</b> ";
                    lines ? result = lines : result += record['teiCorpus']['teiHeader']['fileDesc']['extent']['$'], "", "<div>";
                    lines ? result += lines : result += " ";
                    lines ? result = lines : result += record['teiCorpus']['teiHeader']['fileDesc']['extent']['@type'], "", "<div>";
                }
                lines ? result += lines : result += "<div>";
                lines ? result += lines : result += "<b>Object URL: </b>";
                var str = "Direct Link to Corpus";
                lines ? result = lines : result += str.link('../../repository/corpus/' + encodeURIComponent(pid_split_id[index]));

                lines ? result += lines : result += "<div>";
                lines ? result += lines : result += "<b>Homepage:</b> ";

                if (record['teiCorpus']['teiHeader']['encodingDesc'].length > 1) {
                    var length = record['teiCorpus']['teiHeader']['encodingDesc'].length;
                    lines ? result = lines : result += record['teiCorpus']['teiHeader']['encodingDesc'][length - 1]['projectDesc']['p'][0]['ref']['@target'], "", "<div>";
                }
                else {
                    lines ? result = lines : result += record['teiCorpus']['teiHeader']['encodingDesc']['projectDesc']['p'][0]['ref']['@target'], "", "<div>";
                }
                lines ? result += lines : result += "<div class=\"truncate\">";
                //lines ? result += lines : result += "<b>Project Description:</b> ";
                lines ? result += lines : result += "&lt;b&gt;Project Description:&lt;/b&gt;";
                if(record['teiCorpus']['highlight']!== undefined && record['teiCorpus']['highlight']["teiCorpus.teiHeader.encodingDesc.projectDesc.p.$"] !== undefined ){
                    var last = record['teiCorpus']['highlight']['teiCorpus.teiHeader.encodingDesc.projectDesc.p.$'].length-1;
                    result += record['teiCorpus']['highlight']["teiCorpus.teiHeader.encodingDesc.projectDesc.p.$"][last];
                }else{
                    if (record['teiCorpus']['teiHeader']['encodingDesc'].length > 1) {
                        var length = record['teiCorpus']['teiHeader']['encodingDesc'].length;
                        lines ? result = lines : result += record['teiCorpus']['teiHeader']['encodingDesc'][length - 1]['projectDesc']['p'][1]['$'], "", "<div>";
                    }
                    else {
                        lines ? result = lines : result += record['teiCorpus']['teiHeader']['encodingDesc']['projectDesc']['p'][1]['$'], "", "<div>";
                    }
                }
                lines ? result += lines : result += '</div><b>Documents:</b><ul> ';
                /*
                 for (var i = 0; i < record['teiCorpus']['teiHeader']['fileDesc']['sourceDesc']['list']['item'].length;i++) {
                 project_part += record['teiCorpus']['teiHeader']['fileDesc']['sourceDesc']['list']['item'][i]['@corresp'] ;
                 project_part += " ";
                 }
                 */
                var isEmpty = true;
                if (typeof(record['teiCorpus']['teiCorpus']['teiHeader']['teiHeader']) != 'undefined') {
                    var previous = null;
                    var duplicateCount = 0;
                    for (var i = 0; i < record['teiCorpus']['teiCorpus']['teiHeader']['teiHeader'].length; i++) {
                        isEmpty = false;
                        try {
                            var doc = encodeURIComponent(record['teiCorpus']['teiCorpus']['teiHeader']['teiHeader'][i]['fileDesc']['titleStmt']['title']['$']);
                            var id = pid_split_id[index] + '_' + record['teiCorpus']['teiCorpus']['teiHeader']['teiHeader'][i]['fileDesc']['@id'];
                            project_part += '<li class="document_link" rel="' + record['teiCorpus']['teiCorpus']['teiHeader']['teiHeader'][i]['fileDesc']['@id'] + '">';
                            project_part += '<form class="form_link" method="post" action=' + objects_url + '/' + encodeURIComponent(pid_split_id[index]) + ' id="' + id + '">';
                            project_part += '<input type="hidden" name="reveal" value="' + doc + '" />';
                            if(previous === doc){
                                duplicateCount++;
                                project_part += '<input type="hidden" name="reveal_number" value="' + duplicateCount + '" />';
                            }else{
                                duplicateCount = 0;
                            }
                            project_part += '<a href="' + objects_url + '/' + encodeURIComponent(pid_split_id[index]) + '" onclick="document.getElementById(\'' + id + '\').submit(); return false;">' + record['teiCorpus']['teiCorpus']['teiHeader']['teiHeader'][i]['fileDesc']['titleStmt']['title']['$'] + '</a>';
                            project_part += '</form></li>';
                            previous = doc;
                        } catch (e) {
                            console.log('error "document title undefined" at corpus:%s, Document #%n', record['teiCorpus']['teiHeader']['fileDesc']['titleStmt']['title']['$'], i)
                        }
                    }
                    //new scheme ???
                } else if (typeof(record['teiCorpus']['teiCorpus']['teiHeader']) != 'undefined') {
                    isEmpty = false;
                    var previous = null;
                    var duplicateCount = 0;
                    var truncate = false;
                    if(Array.isArray(record['teiCorpus']['teiCorpus']['teiHeader'])){
                        for (var i = 0; i < record['teiCorpus']['teiCorpus']['teiHeader'].length; i++) {
                            try {
                                var doc = encodeURIComponent(record['teiCorpus']['teiCorpus']['teiHeader'][i]['fileDesc']['titleStmt']['title']['$']);
                                var id = pid_split_id[index] + '_' + record['teiCorpus']['teiCorpus']['teiHeader'][i]['fileDesc']['@id'];
                                project_part += '<li  rel="' + record['teiCorpus']['teiCorpus']['teiHeader'][i]['fileDesc']['@id'] + '"';
                                if(options.show_documents<i){
                                    project_part += ' style="display:none" class="document_link trunc"';
                                    truncate = true;
                                }else{
                                    project_part += 'class="document_link"';
                                }
                                project_part += ' ><form class="form_link" method="post" action=' + objects_url + '/' + encodeURIComponent(pid_split_id[index]) + ' id="' + id + '">';
                                project_part += '<input type="hidden" name="reveal" value="' + doc + '" />';
                                if(previous === doc){
                                    duplicateCount++;
                                    project_part += '<input type="hidden" name="reveal_number" value="' + duplicateCount + '" />';
                                }else{
                                    duplicateCount = 0;
                                }
                                previous = doc;
                                var doctitle = record['teiCorpus']['teiCorpus']['teiHeader'][i]['fileDesc']['titleStmt']['title']['$'];
                                if(record['teiCorpus']['highlight'] !== undefined && record['teiCorpus']['highlight']['teiCorpus.teiCorpus.teiHeader.fileDesc.titleStmt.title.$'] !== undefined){
                                    for(var title in record['teiCorpus']['highlight']['teiCorpus.teiCorpus.teiHeader.fileDesc.titleStmt.title.$']){
                                        var highlighttitle = record['teiCorpus']['highlight']['teiCorpus.teiCorpus.teiHeader.fileDesc.titleStmt.title.$'][title]
                                        var stripedtitle = highlighttitle.replace(/&lt;b&gt;/, '');
                                        stripedtitle = stripedtitle.replace(/&lt;\/b&gt;/, '');
                                        if(stripedtitle === doctitle){
                                            highlighttitle = $("<div/>").html(highlighttitle).text();//decodeEntities(highlighttitle);
                                            doctitle = highlighttitle;
                                            break;
                                        }
                                    }
                                }
                                project_part += '<a href="' + objects_url + '/' + encodeURIComponent(pid_split_id[index]) + '" onclick="document.getElementById(\'' + id + '\').submit(); return false;">' + doctitle + '</a>';
                                project_part += '</form></li>';

                            } catch (e) {
                                console.log('error "document title undefined" at corpus:%s, Document #%n', record['teiCorpus']['teiHeader']['fileDesc']['titleStmt']['title']['$'], i)
                            }
                        }
                    }else{
                        try {
                            var doc = encodeURIComponent(record['teiCorpus']['teiCorpus']['teiHeader']['fileDesc']['titleStmt']['title']['$']);
                            var id = pid_split_id[index] + '_' + record['teiCorpus']['teiCorpus']['teiHeader']['fileDesc']['@id'];
                            project_part += '<li  rel="' + record['teiCorpus']['teiCorpus']['teiHeader']['fileDesc']['@id'] + '"';

                            project_part += ' ><form class="form_link" method="post" action=' + objects_url + '/' + encodeURIComponent(pid_split_id[index]) + ' id="' + id + '">';
                            project_part += '<input type="hidden" name="reveal" value="' + doc + '" />';
                            var doctitle = record['teiCorpus']['teiCorpus']['teiHeader']['fileDesc']['titleStmt']['title']['$'];
                            if(record['teiCorpus']['highlight'] !== undefined && record['teiCorpus']['highlight']['teiCorpus.teiCorpus.teiHeader.fileDesc.titleStmt.title.$'] !== undefined){
                                for(var title in record['teiCorpus']['highlight']['teiCorpus.teiCorpus.teiHeader.fileDesc.titleStmt.title.$']){
                                    var highlighttitle = record['teiCorpus']['highlight']['teiCorpus.teiCorpus.teiHeader.fileDesc.titleStmt.title.$'][title]
                                    var stripedtitle = highlighttitle.replace(/&lt;b&gt;/, '');
                                    stripedtitle = stripedtitle.replace(/&lt;\/b&gt;/, '');
                                    if(stripedtitle === doctitle){
                                        highlighttitle = $("<div/>").html(highlighttitle).text();//decodeEntities(highlighttitle);
                                        doctitle = highlighttitle;
                                        break;
                                    }
                                }
                            }
                            project_part += '<a href="' + objects_url + '/' + encodeURIComponent(pid_split_id[index]) + '" onclick="document.getElementById(\'' + id + '\').submit(); return false;">' + doctitle + '</a>';
                            project_part += '</form></li>';

                        } catch (e) {
                            console.log('error "document title undefined" at corpus:%s, Document #1', record['teiCorpus']['teiHeader']['fileDesc']['titleStmt']['title']['$'])
                        }
                    }
                }
                if(truncate)
                    project_part += '<li style="list-style-type: none;">(<a href="#" class="list_truncator more">more</a>)</li>';
                if(isEmpty){
                    project_part += 'none'
                }
            } else {
                result += 'No TEI-Header available!';

                for (var field in record) {
                    result += '<div>' + field + '= ' + record[field] + '</div>';
                }
            }
            lines ? result += lines : result += '</ul>';
            result = result.concat(project_part);
            result += '</td></tr>';

            return result;
        };


//----- execute a search--------------------------------------------------------
        var dosearch = function () {
            if (options.search_index == "elasticsearch") {
                $.ajax({
                    type:"POST",
                    url:options.search_url,
                    data:elasticsearchquery(),
                    //processData: false,
                    dataType:"json",
                    success:showresults
                    // complete: showError
                });
                $.ajax({
                    type:"POST",
                    url:options.search_url,
                    //data:{source:elasticsearchfacets()},
                    data:elasticsearchfacets(),
                    dataType:"json",
                    success:showfacets
                });
            }
            else {
                $.ajax({
                    type:"get", url:solrsearchquery(),
                    dataType:"jsonp", jsonp:"json.wrf",
                    success:function (data) {
                        showresults(data)
                    }
                });
            }
        };

        var showError = function (jqXHR, error, errorThrown) {
            alert(error)
        };

//----- pass a list of filters to be displayed----------------------------------
        /*
         var buildfilters = function() {
         var filters = options.facets;
         var thefilters = "<h3>Filter by</h3>";
         for ( var idx in filters ) {
         var _filterTmpl = ' \
         <div class="btn-group"> \
         <a style="text-align:left; min-width:70%;" class="facetview_filtershow btn" \
         rel="{{FILTER_NAME}}" href=""> \
         <i class="icon-plus"></i> \
         {{FILTER_DISPLAY}} \
         </a> \
         <a class="btn dropdown-toggle" data-toggle="dropdown" \
         href="#"><span class="caret"></span></a> \
         <ul class="dropdown-menu"> \
         <li><a class="facetview_sort facetview_count" href="{{FILTER_EXACT}}">sort by count</a></li> \
         <li><a class="facetview_sort facetview_term" href="{{FILTER_EXACT}}">sort by term</a></li> \
         <li><a class="facetview_sort facetview_rcount" href="{{FILTER_EXACT}}">sort reverse count</a></li> \
         <li><a class="facetview_sort facetview_rterm" href="{{FILTER_EXACT}}">sort reverse term</a></li> \
         <li class="divider"></li> \
         <li><a class="facetview_facetrange" rel="{{FACET_IDX}}" href="{{FILTER_EXACT}}">select a filter range</a></li> \
         <li class="divider"></li> \
         <li><a class="facetview_morefacetvals" rel="{{FACET_IDX}}" href="{{FILTER_EXACT}}">show up to {{FILTER_HOWMANY}}</a></li> \
         </ul> \
         </div> \
         \
         \
         ';
         var url_param = '<ul id="facetview_{{FILTER_NAME}}" \
         class="facetview_filters"><li style="display: list-item;"><a class="facetview_filterchoice"></a></li></ul>';
         _filterTmpl = _filterTmpl.concat(url_param);

         thefilters += _filterTmpl.replace(/{{FILTER_NAME}}/g, filters[idx]['field'].replace(/\./gi,'_')).replace(/{{FILTER_EXACT}}/g, filters[idx]['field']);

         if ('size' in filters[idx] ) {
         thefilters = thefilters.replace(/{{FILTER_HOWMANY}}/gi, filters[idx]['size'])
         } else {
         thefilters = thefilters.replace(/{{FILTER_HOWMANY}}/gi, 10)
         }
         thefilters = thefilters.replace(/{{FACET_IDX}}/gi,idx)
         if ('display' in filters[idx]) {
         thefilters = thefilters.replace(/{{FILTER_DISPLAY}}/g, filters[idx]['display'])
         }
         else {
         thefilters = thefilters.replace(/{{FILTER_DISPLAY}}/g, filters[idx]['field'])
         }
         }
         $('#facetview_filters').append(thefilters)
         $('.facetview_morefacetvals').bind('click',morefacetvals)
         $('.facetview_facetrange').bind('click',facetrange)
         $('.facetview_sort').bind('click',sortfilters)
         $('.facetview_filtershow').bind('click',showfiltervals)
         }
         */
        var buildfilters = function () {
            var filters = options.facets;
            //alert(JSON.stringify(filters));
            var thesearch = "<h3>Full-Text Search</h3>";
            var thefilters = "<h3>Filter by</h3>";
            var theCorpusFilters = "<h4>Corpus</h4>";
            var theDocumentFilters = "<h4>Document</h4>";
            //var theCorpusFilters = "<h4>Corpus<span class='facetview_tooltip filtertype_tooltip'><img src='http://depot1-7.cms.hu-berlin.de/repository/app/webroot/img/info.png'/><p>The facet &lsquo;corpus&rsquo; shows a subset of the important metadata corresponding to the object &lsquo;corpus&rsquo;, e.g. the metadata &lsquo;date&rsquo; refers to the publication date of the corpus.</p></span></h4><span class=\"filtertyp\">";
            //var theDocumentFilters = "<h4>Document<span class='facetview_tooltip filtertype_tooltip'><img src='http://depot1-7.cms.hu-berlin.de/repository/app/webroot/img/info.png'/><p>The facet &lsquo;document&rsquo; shows a subset of the important metadata corresponding to the object &lsquo;document,&rsquo; e.g. the metadata &lsquo;date&rsquo; refers to the publication date of a single document.</p></span></h4><span class=\"filtertyp\">";

            for (var idx in filters) { //id="facetview_{{FILTER_NAME}}"
                var _filterTmpl = '<table id="facetview_filterbuttons_{{FILTER_NAME}}" class="facetview_filters table table-bordered table-condensed table-striped" style="display:item;"> \
			    <tr><td>\n\
			    <a class="facetview_filtershow" rel="{{FILTER_NAME}}" \
				href=""><i class="icon-plus icon_tooltip"><p>Open all layers of the section.</p></i> {{FILTER_DISPLAY}} \
			    </a> \
			    <div class="btn-group facetview_filteroptions" style="display:none; margin-top:5px;"> \
			    ';
                if (options.enable_rangeselect) {
                    _filterTmpl += '<a class="btn btn-small facetview_facetrange" title="make a range selection on this filter" rel="{{FACET_IDX}}" href="{{FILTER_EXACT}}" style="color:#aaa;">range</a>';
                }


                var url_param = '<ul id="facetview_{{FILTER_NAME}}" \
			    class="facetview_filters"></ul>';
                _filterTmpl = _filterTmpl.concat(url_param);
                _filterTmpl += '</div> \
			</td></tr>';

                if (filters[idx]['display'].match("Annotation - Graphical" || filters[idx]['display'].match("Annotation - Lexical") || filters[idx]['display'].match("Annotation - Transcription") || filters[idx]['display'].match("Annotation - Morphological") || filters[idx]['display'].match("Annotation - Syntactical") || filters[idx]['display'].match("Annotation - Meta") || filters[idx]['display'].match("Annotation - Other"))) {
                    _filterTmpl += '<span class="facetview_tooltip"><img src="img/help.png"><p>A rough division of annotations according to their functions.</p></span>';
                }

                /*
                if (filters[idx]['display'] == 'Annotation - Graphical') {
                    _filterTmpl += '<span class="facetview_tooltip"><img src="img/help.png"><p>A rough division of annotations according to their functions.</p></span>';
                }

                else if (filters[idx]['display'] == 'Annotation - Lexical') {
                    _filterTmpl += '<span class="facetview_tooltip"><img src="img/help.png"><p>A rough division of annotations according to their functions.</p></span>';
                }
                */

                _filterTmpl += '</table>';

                if ('display' in filters[idx] && (filters[idx]['display'].match("Annotation") || filters[idx]['display'].match("Document"))) {
                    //alert(filters[idx]['field']);

                    theDocumentFilters += _filterTmpl.replace(/{{FILTER_NAME}}/g, filters[idx]['field'].replace(/\./gi, '_') + filters[idx]['display'].replace(/ /g, "")).replace(/{{FILTER_EXACT}}/g, filters[idx]['field']);
                    if ('size' in filters[idx]) {
                        theDocumentFilters = theDocumentFilters.replace(/{{FILTER_HOWMANY}}/gi, filters[idx]['size'])
                    }
                    else {
                        theDocumentFilters = theDocumentFilters.replace(/{{FILTER_HOWMANY}}/gi, 10)
                    }
                    theDocumentFilters = theDocumentFilters.replace(/{{FACET_IDX}}/gi, idx);
                    if ('display' in filters[idx]) {
                        //alert(filters[idx]['display']);
                        theDocumentFilters = theDocumentFilters.replace(/{{FILTER_DISPLAY}}/g, filters[idx]['display']);
                        //alert(theDocumentFilters);
                    }
                    else {
                        theDocumentFilters = theDocumentFilters.replace(/{{FILTER_DISPLAY}}/g, filters[idx]['field'])
                    }

                }
                else {
                    //alert(filters[idx]['teiCorpus']['teiCorpus']['teiHeader']['teiHeader']['fileDesc']['extent']['@type']);
                    theCorpusFilters += _filterTmpl.replace(/{{FILTER_NAME}}/g, filters[idx]['field'].replace(/\./gi, '_') + filters[idx]['display'].replace(/ /g, "")).replace(/{{FILTER_EXACT}}/g, filters[idx]['field']);

                    if ('size' in filters[idx]) {
                        theCorpusFilters = theCorpusFilters.replace(/{{FILTER_HOWMANY}}/gi, filters[idx]['size'])
                    }
                    else {
                        theCorpusFilters = theCorpusFilters.replace(/{{FILTER_HOWMANY}}/gi, 10)
                    }
                    theCorpusFilters = theCorpusFilters.replace(/{{FACET_IDX}}/gi, idx);
                    if ('display' in filters[idx]) {
                        theCorpusFilters = theCorpusFilters.replace(/{{FILTER_DISPLAY}}/g, filters[idx]['display'])
                    }
                    else {
                        theCorpusFilters = theCorpusFilters.replace(/{{FILTER_DISPLAY}}/g, filters[idx]['field'])
                    }
                }


                /*var _filterTmpl = '<table id="facetview_filterbuttons_{{FILTER_NAME}}" class="facetview_filters table table-bordered table-condensed table-striped" style="display:item;"> \
                 <tr><td>\n\
                 <a class="facetview_filtershow" title="filter by {{FILTER_DISPLAY}}" rel="{{FILTER_NAME}}" \
                 href=""><i class="icon-plus"></i> {{FILTER_DISPLAY}} \
                 </a> \
                 <div class="btn-group facetview_filteroptions" style="display:none; margin-top:5px;"> \
                 ';
                 if ( options.enable_rangeselect ) {
                 _filterTmpl += '<a class="btn btn-small facetview_facetrange" title="make a range selection on this filter" rel="{{FACET_IDX}}" href="{{FILTER_EXACT}}" style="color:#aaa;">range</a>';
                 }
                 var url_param = '<ul id="facetview_{{FILTER_NAME}}" \
                 class="facetview_filters"></ul>';
                 _filterTmpl = _filterTmpl.concat(url_param);
                 _filterTmpl +='</div> \
                 </td></tr> \
                 </table>';

                 thefilters += _filterTmpl.replace(/{{FILTER_NAME}}/g, filters[idx]['field'].replace(/\./gi,'_')).replace(/{{FILTER_EXACT}}/g, filters[idx]['field']);

                 if ('size' in filters[idx] ) {
                 thefilters = thefilters.replace(/{{FILTER_HOWMANY}}/gi, filters[idx]['size'])
                 }
                 else {
                 thefilters = thefilters.replace(/{{FILTER_HOWMANY}}/gi, 10)
                 }
                 thefilters = thefilters.replace(/{{FACET_IDX}}/gi,idx)
                 if ('display' in filters[idx]) {
                 thefilters = thefilters.replace(/{{FILTER_DISPLAY}}/g, filters[idx]['display'])
                 //alert(thefilters);
                 }
                 else {
                 thefilters = thefilters.replace(/{{FILTER_DISPLAY}}/g, filters[idx]['field'])
                 }*/
            }
            theCorpusFilters += "</span>";
            theDocumentFilters += "</span>";
            $('#facetview_filters').append(thesearch);
            $('#facetview_filters').append(thefilters);
            $('#facetview_filters').append(theCorpusFilters);
            $('#facetview_filters').append(theDocumentFilters);
            $('.facetview_morefacetvals').bind('click', morefacetvals);
            //$('.facetview_facetrange').bind('click', facetrange)
            $('.facetview_sort').bind('click', sortfilters);
            $('.facetview_filtershow').bind('click', showfiltervals);

            //$('.facetview_teiCorpus_teiHeader_fileDesc_publicationStmt_date_@when').append(putvalsinfilters(data));
            //putvalsinfilters(data);
        };

        var showfacets = function (sdata) {
            console.log('facets results %o', sdata);
            var data = parseresults(sdata);
            putvalsinfilters(data);
            $('.facetitem_truncator').bind('click',facetitemstruncate);
        };

        var decodeEntities = function() {
            // this prevents any overhead from creating the object each time
            var element = document.createElement('div');

            function decodeHTMLEntities (str) {
                if(str && typeof str === 'string') {
                    // strip script/html tags
                    str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
                    str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
                    element.innerHTML = str;
                    str = element.textContent;
                    element.textContent = '';
                }

                return str;
            }

            return decodeHTMLEntities;
        };

//----- put the results on the page---------------------------------------------
        var showresults = function (sdata) {
            console.log('query results %o', sdata);

            // get the data and parse from the solr / es layout
            var data = parseresults(sdata);
            // change filter options
            //putvalsinfilters(data);           
            // put result metadata on the page
            putmetadata(data);
            // populate the advanced options
            populateadvanced(data);
            // put the filtered results on the page
            $('#facetview_results').html("");
            var infofiltervals = {};
            $.each(data.records, function (index, value) {
                // write them out to the results div
                //alert(JSON.stringify(value));
                $('#facetview_results').append(buildrecord(value, index));
                $('#facetview_results tr:last-child').linkify()
            });
            $('.truncate').truncate({max_length:400, fade:500});
            $('.list_truncator').bind('click', listtruncate);
            // bind the more action to show the hidden details
            $('.facetview_more').bind('click', showmore);
            if (options.highlight_documents == true && ($('#facetview_freetext').val() != "" || $('a[rel^="teiCorpus.teiCorpus.teiHeader"].facetview_filterselected').length>0)) {
                $.ajax({
                    type:"POST",
                    url:options.search_url,
                    data:buildHighlightQuery(),

                    dataType:"json",
                    success:setHighlights
                });
            }
        };


        var listtruncate = function(event){
            event.preventDefault();
            if($(this).hasClass('more')){
                $(this).parent().siblings('li').each(function(){
                   $(this).removeAttr('style');
                });
                $(this).removeClass('more');
                $(this).text('less');
            }else{
                $(this).parent().siblings('li.trunc').each(function(){
                    $(this).attr('style','display:none');
                });
                $(this).addClass('more');
                $(this).text('more');
            }
        };

        var facetitemstruncate = function(event){
            event.preventDefault();
            if($(this).hasClass('more')){
                $(this).parent().siblings('ul').children('li').each(function(){
                    $(this).removeClass('hide_facetitem');
                    $(this).addClass('show_facetitem');
                });
                $(this).removeClass('more');
                $(this).text('less');
            }else{
                $(this).parent().siblings('ul').children('li.trunc').each(function(){
                    $(this).removeClass('show_facetitem');
                    $(this).addClass('hide_facetitem');
                });
                $(this).addClass('more');
                $(this).text('more');
            }
        };

//------ show more details of an event, and trigger the book search-------------
        var showmore = function (event) {
            event.preventDefault();
            alert("show record view options")
        };

        // ===============================================
        // functions to do with searching
        // ===============================================

//----- build the search query URL based on current params----------------------
        var solrsearchquery = function () {
            // set default URL params
            var urlparams = "";
            for (var item in options.default_url_params) {
                urlparams += item + "=" + options.default_url_params[item] + "&";
            }
            // do paging params
            var pageparams = "";
            for (item in options.paging) {
                pageparams += item + "=" + options.paging[item] + "&";
            }
            // set facet params
            var urlfilters = "";
            for (item in options.facets) {
                urlfilters += "facet.field=" + options.facets[item]['field'] + "&";
            }
            // build starting URL
            var theurl = options.search_url + urlparams + pageparams + urlfilters + options.query_parameter + "=";
            // add default query values
            // build the query, starting with default values
            var query = "";
            for (item in options.predefined_filters) {
                query += item + ":" + options.predefined_filters[item] + " AND ";
            }
            $('.facetview_filterselected', obj).each(function () {
                query += $(this).attr('rel') + ':"' +
                    $(this).attr('href') + '" AND ';
            });
            // add any freetext filter
            if ($('#facetview_freetext').val() != "") {
                query += $('#facetview_freetext').val() + '*';
            }
            query = query.replace(/ AND $/, "");
            // set a default for blank search
            if (query == "") {
                query = options.q;
            }
            theurl += query;
            return theurl;
        };

//----- build the search query URL based on current params----------------------

        //build query from freetext terms, apply filter selections and highlight filterhits
        var elasticsearchquery = function () {
            var qs = {};
            //build query for freetext terms
            qs = buildQuery(qs);
            //collect filter selections
            var terms = {};
            var ranges = {};
            $('.facetview_filterselected', obj).each(function () {
                var fieldname = $(this).attr('rel');
                if ($(this).hasClass('facetview_facetrangeselected')) {
                    if(ranges[fieldname]){
                        ranges[fieldname].push({
                            'from':$('.facetview_lowrangeval', this).html(),
                            'to':$('.facetview_highrangeval', this).html()
                        });
                    }else{
                        ranges[fieldname] = [{
                            'from':$('.facetview_lowrangeval', this).html(),
                            'to':$('.facetview_highrangeval', this).html()
                        }];

                    }
                }
                else {
                    //convert to int
                    var term  = $(this).attr('href');
                    if(!isNaN(parseFloat(term)) && isFinite(term)){
                        term = +$(this).attr('href');
                    }
                    if (terms[fieldname]) {
                        terms[fieldname].push(term);
                    } else {
                        terms[fieldname] = [term];
                    }
                }

            });
            //apply terms for filter (facets) selections
            qs = buildQuerysFacetChoice(qs, terms, ranges);
            //qs = buildFilters(qs,terms); 
            //qs = buildFacets(qs,terms);
            qs['partial_fields'] = options.searchfields;
            console.log('query_log:'+JSON.stringify(qs));
            return JSON.stringify(qs)
        };

        //build facetselections based on freetext seach result
        var elasticsearchfacets = function () {
            var qs = {};
            //build query for freetext terms
            qs = buildQuery(qs);
            //collect filter selections
            var terms = {};
            var ranges = {};
            $('.facetview_filterselected', obj).each(function () {
                var fieldname = $(this).attr('rel');
                if ($(this).hasClass('facetview_facetrangeselected')) {
                    if(ranges[fieldname]){
                        ranges[fieldname].push({
                            'from':$('.facetview_lowrangeval', this).html(),
                            'to':$('.facetview_highrangeval', this).html()
                        });
                    }else{
                        ranges[fieldname] = [{
                            'from':$('.facetview_lowrangeval', this).html(),
                            'to':$('.facetview_highrangeval', this).html()
                        }];

                    }
                }else if($(this).hasClass('facetview_yearonlychoice')){
                    var year  = $(this).attr('href')+'';
                    if(year.match(/^\d+$/) != null){
                        if(ranges[fieldname]){
                            ranges[fieldname].push({
                                'from':year+'-01-01',
                                'to':year+'-12-31'
                            });
                        }else{
                            ranges[fieldname] = [{
                                'from':year+'-01-01',
                                'to':year+'-12-31'
                            }];

                        }
                    }else{console.log('error: date filter choice of type facetview_yearonlychoice is not a number')}
                }else{
                    //convert to int
                    var term  = $(this).attr('href');
                    if(!isNaN(parseFloat(term)) && isFinite(term)){
                        term = +$(this).attr('href');
                    }
                    if (terms[fieldname]) {
                        terms[fieldname].push(term);
                    } else {
                        terms[fieldname] = [term];
                    }
                }

            });
            qs = buildFacets(qs, terms,ranges);
            qs['fields'] = [];
            console.log('facets:%s',JSON.stringify(qs));
            return JSON.stringify(qs)
        };

        //apply filterselections to query (and add highlights for them .... highlights now via seperate nested facets: buildHighlightQuery())
        var buildQuerysFacetChoice = function (qs, terms, ranges) {
            //var qs = {};
            if (!$.isEmptyObject(terms) || !$.isEmptyObject(ranges)){

                var qsFiltered = {'query':{'bool':{'must':[]}}};
                if(qs['query']['match_all'] == undefined){
                    qsFiltered['query']['bool']['must'].push(qs['query']);
                }

                if (!$.isEmptyObject(terms)) {
                    var facets = options.facets;
                    var qsFiltered = {'query':{'bool':{'must':[]}}};
                    if(qs['query']['match_all'] == undefined){
                        qsFiltered['query']['bool']['must'].push(qs['query']);
                    }
                    //qsFiltered['highlight'] = {'fields':{}};
                    for (var i in terms) {
                        if(i != undefined && terms.hasOwnProperty(i)){
                            var rangeTerm = false;
                            var dateTerm = false;
                            var andClause = {};
                            andClause = {'terms':{}};
                            andClause['terms'][i] = terms[i];
                            for(var j = 0;j<facets.length && !rangeTerm ;j++){
                                if(facets[j]['field'] == i && facets[j]['range']){
                                    rangeTerm = true;
                                    if(facets[j]['range'] == 'date'){
                                        dateTerm = true;
                                    }
                                }
                            }
                            //push range terms into should[] because there is no 'ranges'-filter in elasticsearch
                            if(rangeTerm){
                                if(dateTerm){
                                    var dateTerms = {'terms':{}};
                                    for(var term in andClause['terms'][i]){
                                        var year = andClause['terms'][i][term]+'';
                                        if(year.match(/^\d+$/) != null){
                                            if(ranges[i]){
                                                ranges[i].push({
                                                    'from':year+'-01-01',
                                                    'to':year+'-12-31'
                                                });
                                            }else{
                                                ranges[i] = [{
                                                    'from':year+'-01-01',
                                                    'to':year+'-12-31'
                                                }];

                                            }
                                        }else{
                                            if(!dateTerms['terms'][i])
                                                dateTerms['terms'][i] = [];
                                            dateTerms['terms'][i].push(andClause['terms'][i][term]);
                                        }

                                    }
                                    if(dateTerms['terms'].length >0){
                                        if(!qsFiltered['query']['bool']['should'])
                                            qsFiltered['query']['bool']['should'] = [];
                                        qsFiltered['query']['bool']['should'].push(dateTerms);
                                    }
                                }
                            }else{
                                qsFiltered['query']['bool']['must'].push(andClause);
                            }
                        }
                    }

                }
                var size = function(obj) {
                    var size = 0, key;
                    for (key in obj) {
                        if (obj.hasOwnProperty(key)) size++;
                    }
                    return size;
                };
                //console.log('ranges %o, size:%i', ranges,size(ranges) );
                if (!$.isEmptyObject(ranges)) {

                    for (var field in ranges) {
                        if(field != undefined && ranges.hasOwnProperty(field)){
                            //add fields to seperate should clauses and insert them as bool querys into new must clause
                           if(size(ranges) > 1){
                               var shouldClause ={'bool':{'should':[],'minimum_number_should_match':1}};
                               for(var range in ranges[field]){
                                   if(range != undefined && ranges[field].hasOwnProperty(range)){
                                       var rangeClause = {};
                                       rangeClause = {'range':{}};
                                       rangeClause['range'][field] = ranges[field][range];
                                       shouldClause['bool']['should'].push(rangeClause);
                                   }
                               }
                               qsFiltered['query']['bool']['must'].push(shouldClause);
                            }else{
                               for(var range in ranges[field]){
                                   if(range != undefined && ranges[field].hasOwnProperty(range)){
                                       var andClause = {};
                                       andClause = {'range':{}};
                                       andClause['range'][field] = ranges[field][range];
                                       if(qsFiltered['query']['bool']['should'] == undefined){
                                           qsFiltered['query']['bool']['should']=[];
                                       }
                                       qsFiltered['query']['bool']['should'].push(andClause);
                                   }
                               }
                            }
                        }
                    }

                }
                qsFiltered['query']['bool']['minimum_number_should_match'] = 1;

                qs = qsFiltered;
            }

            //qs['highlight']= {'fields':{'teiCorpus.teiHeader.fileDesc.titleStmt.title.$':{}}};
            //console.log('highlights: %o', qs);
            return qs;
        };

        var buildFacets = function (qs, terms, ranges) {
            //var qs = {};
            qs['facets'] = {};
            for (var item in options.facets) {
              if(item != undefined && options.facets.hasOwnProperty(item)){
                //console.log(options.facets[item])
                var obj = options.facets[item];
                var facetname = obj['display'].replace(/ /g, ""); //obj['field']+'-'+
                //delete obj['display']
                //clone facet for subfacets with appropriate facet filtersme
                if (obj['subfacets']) {
                    var allsubterms = {};
                    for (var sub = 0; sub < obj['subfacets'].length; sub++) {
                        var subfacetname = facetname + '-' + obj['subfacets'][sub][1];
                        var filterfield = obj['subfacets'][sub][0];
                        var filtervalue = obj['subfacets'][sub][1];




                        //prepare facet
                        if (obj['nested'] != undefined) {
                            qs['facets'][subfacetname] = {
                                'nested':obj['nested'],
                                'terms':{'field':obj['field']},
                                'facet_filter':{'bool':{'must':[] }}
                            };

                        } else {
                            qs['facets'][subfacetname] = {
                                'terms':{'field':obj['field']},
                                'facet_filter':{'bool':{'must':[] }}
                            };
                        }

                        for (var term in terms) {
                            //dont include filterterms of same field into facet filter
                            if (terms.hasOwnProperty(term) &&  term != obj['field']) {
                                if(!qs['facets'][subfacetname]['facet_filter'])
                                    qs['facets'][subfacetname]['facet_filter'] = {'bool':{'must':[]}};
                                var x = {'terms':{}};
                                x['terms'][term] = terms[term];
                                qs['facets'][subfacetname]['facet_filter']['bool']['must'].push(x);
                                //facetfilter_terms[term] = terms[term];
                            }
                        }

                        var x = {'terms':{}};
                        x['terms'][filterfield] = [filtervalue];
                        qs['facets'][subfacetname]['facet_filter']['bool']['must'].push(x);

                        //apply facetfilter
                       /* var facetfilter_terms = {};
                        for (var term in terms) {
                            //dont include filterterms of same field into facet filter
                            if (term != obj['field']) {
                                facetfilter_terms[term] = terms[term];
                            }
                        }
                        facetfilter_terms[filterfield] = [filtervalue];

                        qs['facets'][subfacetname]['facet_filter']['bool']['must'].push({'terms':facetfilter_terms});*/


                        //to exclude in 'other'-facet
                        if (allsubterms[filterfield]) {
                            allsubterms[filterfield].push(filtervalue);
                        } else {
                            allsubterms[filterfield] = [filtervalue];
                        }


                        //apply facet filter ranges
                        if (!$.isEmptyObject(ranges)) {
                            var facetfilter_ranges = {};
                            var own_ranges = [];
                            var fieldname = obj['field'];
                            for (r in ranges){
                                if (ranges.hasOwnProperty(r) && r != undefined) {
                                    if(r != obj['field']){
                                        facetfilter_ranges[r] = ranges[r];
                                    }else{
                                        own_ranges = ranges[r];
                                    }
                                }
                            }
                            for (var i = 0; i < own_ranges.length; i++) {
                                var range = {};
                                range['numeric_range'] = {};
                                range['numeric_range'][obj['field']]= own_ranges[i];
                                if(!qs['facets'][subfacetname]['facet_filter']){
                                    qs['facets'][subfacetname]['facet_filter'] = {'bool':{'must_not':[]}};
                                }else if(!qs['facets'][subfacetname]['facet_filter']['bool']){
                                    qs['facets'][subfacetname]['facet_filter']['bool'] = {'must_not':[]};
                                }else if(!qs['facets'][subfacetname]['facet_filter']['bool']['must_not']){
                                    qs['facets'][subfacetname]['facet_filter']['must_not'] = [];
                                }
                                var idxOr = indexOfObject(qs['facets'][subfacetname]['facet_filter']['bool']['must_not'],'or');
                                var idxRange = indexOfObject(qs['facets'][subfacetname]['facet_filter']['bool']['must_not'],'numeric_range');
                                if(idxRange != -1){
                                    var x = qs['facets'][subfacetname]['facet_filter']['bool']['must_not'][idxRange];
                                    qs['facets'][subfacetname]['facet_filter']['bool']['must_not'][idxRange] = {'or':[x]};
                                    qs['facets'][subfacetname]['facet_filter']['bool']['must_not'][idxRange]['or'].push(range);
                                }else if(idxOr != -1){
                                    qs['facets'][subfacetname]['facet_filter']['bool']['must_not'][idxOr]['or'].push(range);
                                }else{
                                    qs['facets'][subfacetname]['facet_filter']['bool']['must_not'].push(range);
                                }
                            }

                            if(!$.isEmptyObject(facetfilter_ranges)){
                                if(!qs['facets'][subfacetname]['facet_filter']){
                                    qs['facets'][subfacetname]['facet_filter'] = {'bool':{'must_not':[]}};
                                }else if(!qs['facets'][subfacetname]['facet_filter']['bool']){
                                    qs['facets'][subfacetname]['facet_filter']['bool'] = {'must_not':[]};
                                }else if(!qs['facets'][subfacetname]['facet_filter']['bool']['must_not']){
                                    qs['facets'][subfacetname]['facet_filter']['must_not'] = [];
                                }
                                for(var field in facetfilter_ranges){
                                    if (facetfilter_ranges.hasOwnProperty(field)) {
                                        for(r in facetfilter_ranges[field]){
                                            if (facetfilter_ranges[field].hasOwnProperty(r) && r != undefined) {
                                                var rangefilter = {};
                                                rangefilter [field] = facetfilter_ranges[field][r];
                                                var idxRange = indexOfObject(qs['facets'][subfacetname]['facet_filter']['bool']['must'],"numeric_range");
                                                var idxOr = indexOfObject(qs['facets'][subfacetname]['facet_filter']['bool']['must'],"or");
                                                if(idxRange != -1){
                                                    var or = qs['facets'][subfacetname]['facet_filter']['bool']['must'][idxRange];
                                                    qs['facets'][subfacetname]['facet_filter']['bool']['must'][idxRange] = {'or':[or]};
                                                    qs['facets'][subfacetname]['facet_filter']['bool']['must'][idxRange]['or'].push({
                                                        'numeric_range': rangefilter
                                                    });
                                                }else if(idxOr != -1){
                                                    qs['facets'][subfacetname]['facet_filter']['bool']['must'][idxOr]['or'].push({
                                                        'numeric_range': rangefilter
                                                    });
                                                }else{
                                                    qs['facets'][subfacetname]['facet_filter']['bool']['must'].push({
                                                        'numeric_range': rangefilter
                                                    });
                                                }
                                            }
                                        }
                                    }

                                }
                            }
                        }

                        if (obj['size']) qs['facets'][subfacetname]['terms']['size'] = obj['size'] + '';
                        if (obj['order']) qs['facets'][subfacetname]['terms']['order'] = obj['order'];

                        //change to histogram facet if defined in index.js
                        if(obj['histogram'] != undefined){
                            if(obj['range'] && obj['range'] == 'date'){
                                qs['facets'][subfacetname]['date_histogram'] = qs['facets'][subfacetname]['terms'];
                                qs['facets'][subfacetname]['date_histogram']['interval'] = obj['histogram'];
                            }else{
                                qs['facets'][subfacetname]['histogram'] = qs['facets'][subfacetname]['terms'];
                                qs['facets'][subfacetname]['histogram']['interval'] = obj['histogram'];
                            }
                            delete qs['facets'][subfacetname]['terms'];
                        }
                    }



                    qs['facets'][facetname + '-other'] = {
                        'terms':{'field':obj['field']},
                        'facet_filter':{'bool':{}}
                    };

                    for (var term in terms) {
                        //dont include filterterms of same field into facet filter
                        if (terms.hasOwnProperty(term) &&  term != obj['field']) {
                            if(!qs['facets'][facetname + '-other']['facet_filter']['bool']['must'])
                                qs['facets'][facetname + '-other']['facet_filter']['bool']['must']=[];
                            var x = {'terms':{}};
                            x['terms'][term] = terms[term];
                            qs['facets'][facetname + '-other']['facet_filter']['bool']['must'].push(x);
                            //facetfilter_terms[term] = terms[term];
                        }
                    }

                    if (obj['nested'] != undefined) {
                        qs['facets'][facetname + '-other']['nested'] = obj['nested'];
                    }



                    qs['facets'][facetname + '-other']['facet_filter']['bool']['must_not'] = {'terms':allsubterms};

                    if (obj['size']) qs['facets'][facetname + '-other']['terms']['size'] = obj['size'] + '';
                    if (obj['order']) qs['facets'][facetname + '-other']['terms']['order'] = obj['order'];

                    //change to histogram facet if defined in index.js
                    if(obj['histogram'] != undefined){
                        if(obj['range'] && obj['range'] == 'date'){
                            qs['facets'][facetname + '-other']['date_histogram'] = qs['facets'][facetname + '-other']['terms'];
                            qs['facets'][facetname + '-other']['date_histogram']['interval'] = obj['histogram'];
                        }else{
                            qs['facets'][facetname + '-other']['histogram'] = qs['facets'][facetname + '-other']['terms'];
                            qs['facets'][facetname + '-other']['histogram']['interval'] = obj['histogram'];
                        }
                        delete qs['facets'][facetname + '-other']['terms'];
                    }
                    //no subfacets
                } else {

                    qs['facets'][facetname] = {
                        'terms':{'field':obj['field']}
                    };
                    if (obj['size']) qs['facets'][facetname]['terms']['size'] = obj['size'] + '';
                    if (obj['order']) qs['facets'][facetname]['terms']['order'] = obj['order'];



                    //apply facet filter
                    if (!$.isEmptyObject(terms)) {
                        var facetfilter_terms = {};

                        for (var term in terms) {
                            //dont include filterterms of same field into facet filter
                            if (terms.hasOwnProperty(term) &&  term != obj['field']) {
                                if(!qs['facets'][facetname]['facet_filter'])
                                    qs['facets'][facetname]['facet_filter'] = {'bool':{'must':[]}};
                                var x = {'terms':{}};
                                x['terms'][term] = terms[term];
                                qs['facets'][facetname]['facet_filter']['bool']['must'].push(x);
                                //facetfilter_terms[term] = terms[term];
                            }
                        }


                       /* if (!$.isEmptyObject(facetfilter_terms)) {
                            qs['facets'][facetname]['facet_filter'] = {'bool':{'must':[]}};

                            qs['facets'][facetname]['facet_filter']['bool']['must'].push({'terms':facetfilter_terms});
                        }//qs['facets'][obj['field']]['terms']["all_terms"]=true;
                        */
                    }
                    //same for range
                    if (!$.isEmptyObject(ranges)) {
                        var facetfilter_ranges = {};
                        var own_ranges = [];
                        for (r in ranges){
                            if (ranges.hasOwnProperty(r) && r != undefined) {
                               if(r != obj['field']){
                                    facetfilter_ranges[r] = ranges[r];
                                }else{
                                    own_ranges = ranges[r];
                               }
                            }
                        }
                        for (var i = 0; i < own_ranges.length; i++) {
                            var range = {};
                            range['numeric_range'] = {};
                            range['numeric_range'][obj['field']]= own_ranges[i];
                            if(!qs['facets'][facetname]['facet_filter']){
                                qs['facets'][facetname]['facet_filter'] = {'bool':{'must_not':[]}};
                            }else if(!qs['facets'][facetname]['facet_filter']['bool']){
                                qs['facets'][facetname]['facet_filter']['bool'] = {'must_not':[]};
                            }else if(!qs['facets'][facetname]['facet_filter']['bool']['must_not']){
                                qs['facets'][facetname]['facet_filter']['must_not'] = [];
                            }
                            var idxOr = indexOfObject(qs['facets'][facetname]['facet_filter']['bool']['must_not'],'or');
                            var idxRange = indexOfObject(qs['facets'][facetname]['facet_filter']['bool']['must_not'],'numeric_range');
                            if(idxRange != -1){
                                var x = qs['facets'][facetname]['facet_filter']['bool']['must_not'][idxRange];
                                qs['facets'][facetname]['facet_filter']['bool']['must_not'][idxRange] = {'or':[x]};
                                qs['facets'][facetname]['facet_filter']['bool']['must_not'][idxRange]['or'].push(range);
                            }else if(idxOr != -1){
                                qs['facets'][facetname]['facet_filter']['bool']['must_not'][idxOr]['or'].push(range);
                            }else{
                                qs['facets'][facetname]['facet_filter']['bool']['must_not'].push(range);
                            }
                        }

                        if(!$.isEmptyObject(facetfilter_ranges)){
                            if(!qs['facets'][facetname]['facet_filter']){
                                qs['facets'][facetname]['facet_filter'] = {'bool':{'must':[]}};
                            }else if(!qs['facets'][facetname]['facet_filter']['bool']){
                                qs['facets'][facetname]['facet_filter']['bool'] ={'must':[]};
                            }else if(!qs['facets'][facetname]['facet_filter']['bool']['must']){
                                qs['facets'][facetname]['facet_filter']['bool']['must']=[];
                            }
                            for(var field in facetfilter_ranges){
                                if (facetfilter_ranges.hasOwnProperty(field)) {
                                    for(r in facetfilter_ranges[field]){
                                        if (facetfilter_ranges[field].hasOwnProperty(r) && r != undefined) {
                                            var rangefilter = {};
                                            rangefilter [field] = facetfilter_ranges[field][r];
                                            var idxRange = indexOfObject(qs['facets'][facetname]['facet_filter']['bool']['must'],"numeric_range");
                                            var idxOr = indexOfObject(qs['facets'][facetname]['facet_filter']['bool']['must'],"or");
                                            if(idxRange != -1){
                                                var or = qs['facets'][facetname]['facet_filter']['bool']['must'][idxRange];
                                                qs['facets'][facetname]['facet_filter']['bool']['must'][idxRange] = {'or':[or]};
                                                qs['facets'][facetname]['facet_filter']['bool']['must'][idxRange]['or'].push({
                                                    'numeric_range': rangefilter
                                                });
                                            }else if(idxOr != -1){
                                                qs['facets'][facetname]['facet_filter']['bool']['must'][idxOr]['or'].push({
                                                    'numeric_range': rangefilter
                                                });
                                            }else{
                                                qs['facets'][facetname]['facet_filter']['bool']['must'].push({
                                                    'numeric_range': rangefilter
                                                });
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }

                    if (obj['nested']) {
                        qs['facets'][facetname]['nested'] = obj['nested']
                    }
                    if (obj['filter']) {
                        if (!qs['facets'][facetname]['facet_filter']) {
                            qs['facets'][facetname]['facet_filter'] = {'bool':{'must':[]}};
                        }
                        var filter = {'term':{}};
                        filter['term'][obj['filter'][0]] = obj['filter'][1];
                        qs['facets'][facetname]['facet_filter']['bool']['must'].push(filter)
                    }

                    //change to histogram facet if defined in index.js
                    if(obj['histogram'] != undefined){
                        if(obj['range'] && obj['range'] == 'date'){
                            qs['facets'][facetname]['date_histogram'] = qs['facets'][facetname]['terms'];
                            qs['facets'][facetname]['date_histogram']['interval'] = obj['histogram'];
                        }else{
                            qs['facets'][facetname]['histogram'] = qs['facets'][facetname]['terms'];
                            qs['facets'][facetname]['histogram']['interval'] = obj['histogram'];
                        }
                        delete qs['facets'][facetname]['terms'];
                    }
                }


              }
            }
            return qs;
        };

        var indexOfObject = function (array, searchElement /*, fromIndex */ ) {

            for (var k=0; k < array.length; k++) {
                if (k in array && array[k][searchElement] != undefined) {
                    return k;
                }
            }
            return -1;
        };

        var buildQuery = function (qs) {
            var bool = false;
            //var qs = {};
            /*          $('.facetview_filterselected',obj).each(function() {
             //console.log($(this))
             //!bool ? bool = {'must': [] } : ""
             !bool ? bool = {'should': [] } : ""
             if ( $(this).hasClass('facetview_facetrange') ) {
             var rel = options.facets[ $(this).attr('rel') ]['field']
             var rngs = {
             'from': $('.facetview_lowrangeval', this).html(),
             'to': $('.facetview_highrangeval', this).html()
             }
             var obj = {
             'range': {
             }
             }
             obj['range'][ rel ] = rngs
             bool['should'].push(obj)

             }
             else {
             var obj = {
             'term': {
             }
             }
             obj['term'][ $(this).attr('rel') ] = $(this).attr('href')
             bool['should'].push(obj)
             }
             });

             if(bool['should']){
             bool['minimum_number_should_match'] = 1;
             }
             */
            for (var item in options.predefined_filters) {
                !bool ? bool = {'must':[] } : "";
                var obj = {
                    'term':{}
                };
                obj['term'][ item ] = options.predefined_filters[item];
                bool['must'].push(obj)
            }
            if (bool) {
                $('#facetview_freetext').val() != "" ? bool['must'].push({
                    'query_string':{
                        'query':$('#facetview_freetext').val()
                    }
                }) : "";
                qs['query'] = {
                    'bool':bool
                }
            } else {
                $('#facetview_freetext').val() != "" ? qs['query'] = {
                    'query_string':{
                        'query':$('#facetview_freetext').val()
                    }
                } : qs['query'] = {
                    'match_all':{
                    }
                }
            }

            if(qs['query']['match_all'] === undefined){
                qs['highlight'] = {
                    'fields':{'teiCorpus.teiHeader.encodingDesc.projectDesc.p.$':{"number_of_fragments" : 0},
                        'teiCorpus.teiCorpus.teiHeader.fileDesc.titleStmt.title.$':{"number_of_fragments" : 0}},
                    'pre_tags' : ['&lt;b&gt;'],
                    'post_tags' : ['&lt;/b&gt;']

                }
            }
            // set any paging
            options.paging.from != 0 ? qs['from'] = options.paging.from : "";
            options.paging.size != 10 ? qs['size'] = options.paging.size : "";
            return qs;
        };

        var buildHighlightQuery = function () {
            var qs = {};
            var terms = {};

            var terms = {};
            var ranges = {};
            $('a[rel^="teiCorpus.teiCorpus.teiHeader"].facetview_filterselected', obj).each(function () {
                var fieldname = $(this).attr('rel');
                if ($(this).hasClass('facetview_facetrangeselected')) {
                    if(ranges[fieldname]){
                        ranges[fieldname].push({
                            'from':$('.facetview_lowrangeval', this).html(),
                            'to':$('.facetview_highrangeval', this).html()
                        });
                    }else{
                        ranges[fieldname] = [{
                            'from':$('.facetview_lowrangeval', this).html(),
                            'to':$('.facetview_highrangeval', this).html()
                        }];

                    }
                }
                else {
                    //convert to int
                    var term  = $(this).attr('href');
                    if(!isNaN(parseFloat(term)) && isFinite(term)){
                        term = +$(this).attr('href');
                    }
                    if (terms[fieldname]) {
                        terms[fieldname].push(term);
                    } else {
                        terms[fieldname] = [term];
                    }
                }

            });

            var qsFacetChoices =  {'query':{'match_all':{}}};
            qsFacetChoices = buildQuerysFacetChoice(qsFacetChoices,terms,ranges);
            //console.log("highlight facet choices: %s",JSON.stringify(qsFacetChoices));
            if(qsFacetChoices['query']['bool'] !== undefined){
                qs = {'query':{'match_all':{}},
                    'facets':{
                        'doctitles':{
                            "nested":"teiCorpus.teiCorpus.teiHeader",
                            "terms":{
                                "field":"teiCorpus.teiCorpus.teiHeader.fileDesc.@id.untouched"
                            },
                            "facet_filter":{
                                'bool':{}
                            }
                        }
                    }
                };
                delete qsFacetChoices['query']['bool']['minimum_number_should_match'];
                qs['facets']['doctitles']['facet_filter']['bool'] = qsFacetChoices['query']['bool']
            }
            /*if (!$.isEmptyObject(terms)) {
                qs = {'query':{'match_all':{}},
                    'facets':{
                        'doctitles':{
                            "nested":"teiCorpus.teiCorpus.teiHeader",
                            "terms":{
                                "field":"teiCorpus.teiCorpus.teiHeader.fileDesc.@id.untouched"
                            },
                            "facet_filter":{
                                'bool':{'must':[]}
                            }
                        }
                    }
                };

                for (var i in terms) {
                    if(i != undefined && terms.hasOwnProperty(i)){
                        var andClause = {};
                        andClause = {'terms':{}};
                        andClause['terms'][i] = terms[i];
                        qs['facets']['doctitles']['facet_filter']['bool']['must'].push(andClause);
                    }
                }
            }*/

            //console.log("highlightquery: %s",JSON.stringify(qs));

            qs['fields'] = [];
            return JSON.stringify(qs);
        };

        var setHighlights = function (sdata) {
            console.log('highlightQueryResults: %o',sdata );
            var highlightDocs = [];
            if (sdata['facets'] && sdata['facets']['doctitles'] && sdata['facets']['doctitles']['terms'] && sdata['facets']['doctitles']['terms'].length > 0) {
                for (var i in sdata['facets']['doctitles']['terms']) {
                    highlightDocs.push(sdata['facets']['doctitles']['terms'][i]['term'])
                }
            }
            $('li.document_link').each(function () {
                if ($.inArray($(this).attr('rel'), highlightDocs) != -1) {
                    $(this).removeAttr('style');
                    $(this).removeClass('trunc');
                    $(this).addClass('document_selected');
                }
            });
            //console.log('highlightData: %o',highlightDocs );
        };

//----- trigger a search when a filter choice is clicked------------------------
        var clickfilterchoice = function (event) {
            event.preventDefault();
            //alert($(this).html()) 
            //console.log()
            var yearOnly = '';
            if($(this).hasClass('facetview_yearonlychoice'))
                yearOnly = ' facetview_yearonlychoice ';
            var newobj = '<a class="facetview_filterselected facetview_clear ' + yearOnly +
                'btn btn-info" rel="' + $(this).attr("rel") +
                '" alt="remove" title="remove"' +
                ' href="' + $(this).attr("href") + '">' +
                //Filter: choice
                //!!!
                //alert(JSON.stringify($(this).parents().filter('.facetview_filteroptions').siblings().filter('.facetview_filtershow').text().trim()));

                $(this).parents().filter('.facetview_filteroptions').siblings().filter('.facetview_filtershow')
                    .clone()//clone the element
                    .children()//select all the children
                    .remove()//remove all the children
                    .end()//again go back to selected element
                    .text().trim() + ': ' +
                $(this).html().replace(/\(.*\)/, '') + ' <i class="icon-remove"></i></a>';
            $(this).parent().hide();
            //alert(newobj);

            $('#facetview_selectedfilters').append(newobj);
            $('.facetview_filterselected').unbind('click', clearfilter);
            $('.facetview_filterselected').bind('click', clearfilter);
            options.paging.from = 0;
            dosearch();
        };

        var rangedatefilterchoice = function (event) {
            event.preventDefault();
            // TODO: VALIDATION + MIN / MAX values
            var rel = $(this).attr('rel');
            var rangeLow = $(this).siblings().filter('input[ref="facetview_lowrangeval"]').val();
            var rangeHigh = $(this).siblings().filter('input[ref="facetview_highrangeval"]').val();
            var yearLow, monthLow, dayLow, yearHigh, monthHigh, dayHigh;
            var highValidFormat = false;
            var lowValid = false;
            var highValid = false;

            if(rangeLow.match(/\d+/) !== null && rangeLow.match(/\d+/)[0] == rangeLow){
                lowValid = true;

            }else if(rangeLow.match(/^\d+-[0-1]\d-[0-3]\d$/) !== null && rangeLow.match(/^\d+-[0-1]\d-[0-3]\d$/)[0] == rangeLow){
                yearLow = rangeLow.match(/^\d+/)[0];
                dayLow = rangeLow.match(/\d\d$/)[0];
                monthLow = rangeLow.match(/-\d\d-/)[0].substr(1,rangeLow.match(/-\d\d-/)[0].length-2);
                if(0<+dayLow<=31 && 0<+monthLow<=12){
                    lowValid = true;
                }
            }

            if(rangeHigh.match(/\d+/) !== null && rangeHigh.match(/\d+/)[0] == rangeHigh){
                highValidFormat= true;
            }else if(rangeHigh.match(/^\d+-[0-1]\d-[0-3]\d$/) !== null && rangeHigh.match(/^\d+-[0-1]\d-[0-3]\d$/)[0]== rangeHigh){
                yearHigh = rangeHigh.match(/^\d+/)[0];
                dayHigh = rangeHigh.match(/\d\d$/)[0];
                monthHigh = rangeHigh.match(/-\d\d-/)[0].substr(1,rangeHigh.match(/-\d\d-/)[0].length-2);
                if(0<+dayLow<=31 && 0<+monthLow<=12){
                    highValidFormat = true;
                }
            }

            if(lowValid && highValidFormat){
                //yyyy < yyyy
                if(yearLow === undefined && yearHigh === undefined && +rangeHigh>+rangeLow){
                    lowValid = true;
                    highValid = true;
                //yyyy < yyyy-mm-dd
                }else if(yearLow === undefined && yearHigh !== undefined && +rangeLow<+yearHigh){
                    highValid = true;
                //yyyy-mm-dd < yyyy
                }else if(yearLow !== undefined && yearHigh === undefined && +yearLow<+rangeHigh){
                    highValid = true;
                }else if(yearLow !== undefined && yearHigh !== undefined){
                    if(yearLow < yearHigh){
                        highValid = true;
                    }else if(yearLow == yearHigh && monthLow < monthHigh){
                        highValid = true;
                    }else if(yearLow == yearHigh && monthLow == monthHigh && dayLow < dayHigh){
                        highValid = true;
                    }
                }
            }

            if(!lowValid){
                $(this).siblings().filter('input[ref="facetview_lowrangeval"]').addClass("error");
            }else if( $(this).siblings().filter('input[ref="facetview_lowrangeval"]').hasClass("error")){
                $(this).siblings().filter('input[ref="facetview_lowrangeval"]').removeClass("error");
            }
            if(!highValid){
                $(this).siblings().filter('input[ref="facetview_highrangeval"]').addClass("error");
            }else if($(this).siblings().filter('input[ref="facetview_highrangeval"]').hasClass("error")){
                $(this).siblings().filter('input[ref="facetview_highrangeval"]').removeClass("error");
            }
            if(!highValid || !lowValid){
                return;
            }

            var range = '<span class="facetview_lowrangeval">'+rangeLow + '</span>' +
                        " - " +
                        '<span class="facetview_highrangeval">'+rangeHigh + '</span>';

            var newobj = '<a class="facetview_filterselected facetview_facetrangeselected facetview_clear ' +
                'btn btn-info" rel="' + rel +
                '" alt="remove" title="remove"' +
                ' href="#">' +
                $(this).parents().filter('.facetview_filteroptions').siblings().filter('.facetview_filtershow')
                    .clone()//clone the element
                    .children()//select all the children
                    .remove()//remove all the children
                    .end()//again go back to selected element
                    .text().trim() + ': ' +
                range + ' <i class="icon-remove"></i></a>';


            $('#facetview_selectedfilters').append(newobj);
            $(".facetview_facetrangeselected").unbind('click', clearrangefilter);
            $('.facetview_facetrangeselected').bind('click', clearrangefilter);
            options.paging.from = 0;
            dosearch();
        };

        var toggleRangeInput = function (event) {
            event.preventDefault();
            var text = '';
            $(this).parents('div.facetview_filteroptions').children('div.facetrange').each(function(){
                if($(this).hasClass('hide_facetitem')){
                    $(this).removeClass('hide_facetitem');
                    text = 'hide range filter';

                }else{
                    $(this).addClass('hide_facetitem');
                    text = 'show range filter';
                }
            });
            $(this).html(text);

        }

        var toggleItemsOrder = function (event) {
            event.preventDefault();
            var list = $(this).parents('div.facetview_filteroptions').children('ul.facetview_filters');
            list.children('li').each(function(){
                var item = $(this).detach();
                list.prepend(item);

            });
            console.log($(this).html());
            if($(this).html() === "order ascending"){
                $(this).html("order descending");
            }else{
                $(this).html("order ascending");
            }
        }

        var rangeextentfilterchoice = function (event) {
            event.preventDefault();
            // TODO: VALIDATION + MIN / MAX values
            var rel = $(this).attr('rel');
            var rangeLow = $(this).siblings().filter('input[ref="facetview_lowrangeval"]').val();
            var rangeHigh = $(this).siblings().filter('input[ref="facetview_highrangeval"]').val();

            var lowValid = false;
            var highValid = false;
            var highValidFormat= false;

            if(rangeLow.match(/\d+/) !== null && rangeLow.match(/\d+/)[0] == rangeLow){
                lowValid = true;
            }

            if(rangeHigh.match(/\d+/) !== null && rangeHigh.match(/\d+/)[0] == rangeHigh){
                highValidFormat= true;
            }


            if(lowValid && highValidFormat && +rangeLow<+rangeHigh){
                highValid = true;
            }

            if(!lowValid){
                $(this).siblings().filter('input[ref="facetview_lowrangeval"]').addClass("error");
            }else if( $(this).siblings().filter('input[ref="facetview_lowrangeval"]').hasClass("error")){
                $(this).siblings().filter('input[ref="facetview_lowrangeval"]').removeClass("error");
            }
            if(!highValid){
                $(this).siblings().filter('input[ref="facetview_highrangeval"]').addClass("error");
            }else if($(this).siblings().filter('input[ref="facetview_highrangeval"]').hasClass("error")){
                $(this).siblings().filter('input[ref="facetview_highrangeval"]').removeClass("error");
            }
            if(!highValid || !lowValid){
                return;
            }

            var range = '<span class="facetview_lowrangeval">'+rangeLow + '</span>' +
                " - " +
                '<span class="facetview_highrangeval">'+rangeHigh + '</span>';

            var newobj = '<a class="facetview_filterselected facetview_facetrangeselected facetview_clear ' +
                'btn btn-info" rel="' + rel +
                '" alt="remove" title="remove"' +
                ' href="#">' +
                $(this).parents().filter('.facetview_filteroptions').siblings().filter('.facetview_filtershow')
                    .clone()//clone the element
                    .children()//select all the children
                    .remove()//remove all the children
                    .end()//again go back to selected element
                    .text().trim() + ': ' +
                range + ' <i class="icon-remove"></i></a>';


            $('#facetview_selectedfilters').append(newobj);
            $(".facetview_facetrangeselected").unbind('click', clearrangefilter);
            $('.facetview_facetrangeselected').bind('click', clearrangefilter);
            options.paging.from = 0;
            dosearch();
        };

//----- clear a filter when clear button is pressed, and re-do the search-------
        var clearfilter = function (event) {
            event.preventDefault();
            //alert($(event.currentTarget).attr('rel'));
            //console.log(event.currentTarget);
            //alert(JSON.stringify($(this).attr('rel')));
            $('.' + $(event.currentTarget).attr('class') + '[href="' + $(event.currentTarget).attr('href') + '"]').parent().show();
            $(this).remove();
            dosearch();
        };

        var clearrangefilter = function (event) {
            event.preventDefault();
            //alert($(event.currentTarget).attr('rel'));
            //console.log(event.currentTarget);
            //alert(JSON.stringify($(this).attr('rel')));
            //$('.' + $(event.currentTarget).attr('class') + '[href="' + $(event.currentTarget).attr('href') + '"]').parent().show();
            $(this).remove();
            dosearch();
        };

//----- do search options-------------------------------------------------------
        var fixmatch = function (event) {
            event.preventDefault();
            var freetext = $('#facetview_freetext');
            if ($(this).attr('id') == "facetview_partial_match") {
                //alert($(this).attr('id'));
                var newvals = freetext.val().replace(/"/gi, '').replace(/\*/gi, '').replace(/\~/gi, '').split(' ');
                //alert(newvals); //searchstring
                var newstring = "";
                for (item in newvals) {
                    if (newvals[item].length > 0 && newvals[item] != ' ') {
                        if (newvals[item] == 'OR' || newvals[item] == 'AND') {
                            newstring += newvals[item] + ' ';
                        }
                        else {
                            newstring += '*' + newvals[item] + '* ';
                        }
                    }
                }
                freetext.val(newstring);
            }
            else if ($(this).attr('id') == "facetview_fuzzy_match") {
                newvals = freetext.val().replace(/"/gi, '').replace(/\*/gi, '').replace(/\~/gi, '').split(' ');
                newstring = "";
                for (item in newvals) {
                    if (newvals[item].length > 0 && newvals[item] != ' ') {
                        if (newvals[item] == 'OR' || newvals[item] == 'AND') {
                            newstring += newvals[item] + ' ';
                        } else {
                            newstring += newvals[item] + '~ ';
                        }
                    }
                }
                freetext.val(newstring);
            }
            else if ($(this).attr('id') == "facetview_exact_match") {
                newvals = freetext.val().replace(/"/gi, '').replace(/\*/gi, '').replace(/\~/gi, '').split(' ');
                newstring = "";
                for (item in newvals) {
                    if (newvals[item].length > 0 && newvals[item] != ' ') {
                        if (newvals[item] == 'OR' || newvals[item] == 'AND') {
                            newstring += newvals[item] + ' ';
                        } else {
                            newstring += '"' + newvals[item] + '" ';
                        }
                    }
                }
                $.trim(newstring, ' ');
                freetext.val(newstring);
            }
            else if ($(this).attr('id') == "facetview_match_all") {
                freetext.val($.trim(freetext.val().replace(/ OR /gi, ' ')));
                freetext.val(freetext.val().replace(/ /gi, ' AND '));
            }
            else if ($(this).attr('id') == "facetview_match_any") {
                freetext.val($.trim(freetext.val().replace(/ AND /gi, ' ')));
                freetext.val(freetext.val().replace(/ /gi, ' OR '));
            }
            dosearch();
            /*freetext.focus().trigger('keyup');*/
        };


//----- adjust how many results are shown---------------------------------------
        var howmany = function (event) {
            event.preventDefault();
            var newhowmany = prompt('Currently displaying ' + options.paging.size +
                ' results per page. How many would you like instead?');
            if (newhowmany) {
                options.paging.size = parseInt(newhowmany);
                options.paging.from = 0;
                $('#facetview_howmany').html('results per page (' + options.paging.size + ')');
                dosearch()
            }
        };

//----- the facet view object to be appended to the page------------------------
        /*                    <li><a id="facetview_partial_match" href="">partial match</a></li> \
         <li><a id="facetview_exact_match" href="">exact match</a></li> \
         <li><a id="facetview_fuzzy_match" href="">fuzzy match</a></li> \
         <li><a id="facetview_match_all" href="">match all</a></li> \
         <li><a id="facetview_match_any" href="">match any</a></li> \
         <li><a href="#">clear all</a></li> \
         <li><a target="_blank" \
         href="http://lucene.apache.org/java/2_9_1/queryparsersyntax.html"> \
         learn more</a></li> \
         <li class="divider"></li> \*/
        //var tooltip_open_fuzzy = 'Lucene supports fuzzy searches based on the Levenshtein Distance, or Edit Distance algorithm.';

        var thefacetview = ' \
           <div id="facetview"> \
             <div class="row-fluid"> \
               <div class="span3"> \
                 <div id="facetview_filters"></div> \
               </div> \
               <div class="span9"> \
                 <!--<form method="GET" action="#search">--> \
                   <div id="facetview_searchbar" style="display:inline; float:left;" class="input-prepend"> \
                   <input class="span4" id="facetview_freetext" name="q" value="" placeholder="search term" autofocus /> \
                    <span class="add-on"><i class="icon-search"></i></span> \
                   </div> \
                   <div style="display:inline; float:left;margin-left:-2px;" class="btn-group"> \
                    <a style="-moz-border-radius:0px 3px 3px 0px; \
                    -webkit-border-radius:0px 3px 3px 0px; border-radius:0px 3px 3px 0px;" \
                    class="btn dropdown-toggle" data-toggle="dropdown" href="#"> \
                    <i class="icon-cog"></i> <span class="caret"></span></a> \
                    <ul class="dropdown-menu"> \
                    <li><a id="facetview_howmany" href="#">results per page ({{HOW_MANY}})</a></li> \
                    </ul> \
                   </div> \
                   <div class="match_options">\
                       <ul>\
                            <li><a class="btn" id="facetview_partial_match" title="The lucene-based Full-Text Search supports partial matches e.g. without mutated vowel: \'Maerchen\' The result from search-request is for e.g.\'Maerchen\' or \'Märchen\'."  href="">partial match</a></li> \
                            <!--<li><a class="btn" style="color:#544532" id="facetview_partial_match" href="">partial match</a></li>--> \
                            <li ><a class="btn" id="facetview_exact_match" title="The lucene-based Full-Text Search supports exact matches e.g. with mutated vowel: \'Märchen\'." href="">exact match</a></li> \
                            <li><a class="btn" id="facetview_fuzzy_match" title="The lucene-based Full-Text Search supports fuzzy searches based on the Levenshtein Distance, or Edit Distance algorithm." href="">fuzzy match</a></li> \
                            <li><a class="btn" id="facetview_match_all" href="">match all</a></li> \
                            <li><a class="btn" id="facetview_match_any" href="">match any</a></li> \
                            <!--<li><a class="btn" id="facetview_clear_all" href="">clear_all</a></li> --> \
                            <li style="float:right;"><a class="btn" target="_blank" \
                            href="http://lucene.apache.org/java/2_9_1/queryparsersyntax.html"> \
                            learn more</a></li> \
                        </ul>\
                    </div>\
                   <div style="clear:both;" id="facetview_selectedfilters"></div> \
                 <!--</form>--> \
                 <div id="facetview_metadata_above"></div> \
                 <table class="table table-striped" id="facetview_results"></table> \
                 <div id="facetview_metadata"></div> \
               </div> \
             </div> \
           </div> \
           ';


        // ===============================================
        // now create the plugin on the page
        return this.each(function () {
            // get this object
            obj = $(this);
            // append the facetview object to this object
            thefacetview = thefacetview.replace(/{{HOW_MANY}}/gi, options.paging.size);
            //append( "<p>Test</p>" );
            //$(obj).append('<b>' + thefacetview + '</b>');
            $(obj).append(thefacetview);

            // setup search option triggers
            $('#facetview_partial_match').bind('click', fixmatch).toggleClass("active");
            $('#facetview_exact_match').bind('click', fixmatch);
            $('#facetview_fuzzy_match').bind('click', fixmatch);
            $('#facetview_match_any').bind('click', fixmatch);
            //$('#facetview_clear_all').bind('click', fixmatch);
            $('#facetview_match_all').bind('click', fixmatch);
            $('#facetview_howmany').bind('click', howmany);

            //$('#facetview_partial_match').click(function () {
            //    $(this).toggleClass("active");
            //});

            $(function() {
                $('#facetview_partial_match').on('click', function(event) {
                    if (event.type == 'click') {
                        // do stuff
                        $(this).toggleClass("clicked");
                    }
                    $('#facetview_fuzzy_match').removeClass("active");
                    $('#facetview_exact_match').removeClass("active");
                    $('#facetview_match_any').removeClass("active");
                    $('#facetview_match_all').removeClass("active");
                    $(this).addClass("active");
                });
            });

            $(function() {
                $('#facetview_exact_match').on('click', function(event) {
                    if (event.type == 'click') {
                        // do stuff
                        $(this).toggleClass("clicked");
                    }
                    $('#facetview_fuzzy_match').removeClass("active");
                    $('#facetview_partial_match').removeClass("active");
                    $('#facetview_match_any').removeClass("active");
                    $('#facetview_match_all').removeClass("active");
                    $(this).addClass("active");
                });
            });

            $(function() {
                $('#facetview_fuzzy_match').on('click', function(event) {
                    if (event.type == 'click') {
                        // do stuff
                        $(this).toggleClass("clicked");
                    }
                    $('#facetview_exact_match').removeClass("active");
                    $('#facetview_partial_match').removeClass("active");
                    $('#facetview_match_any').removeClass("active");
                    $('#facetview_match_all').removeClass("active");
                    $(this).addClass("active");
                });
            });

            $(function() {
                $('#facetview_match_all').on('click', function(event) {
                    if (event.type == 'click') {
                        // do stuff
                        $(this).toggleClass("clicked");
                    }
                    $('#facetview_exact_match').removeClass("active");
                    $('#facetview_fuzzy_match').removeClass("active");
                    $('#facetview_partial_match').removeClass("active");
                    $('#facetview_match_any').removeClass("active");
                    $(this).addClass("active");
                });
            });

            $(function() {
                $('#facetview_match_any').on('click', function(event) {
                    if (event.type == 'click') {
                        // do stuff
                        $(this).toggleClass("clicked");
                    }
                    $('#facetview_exact_match').removeClass("active");
                    $('#facetview_fuzzy_match').removeClass("active");
                    $('#facetview_partial_match').removeClass("active");
                    $('#facetview_match_all').removeClass("active");
                    $(this).addClass("active");
                });
            });

            // resize the searchbar
            var searchbar = $('#facetview_searchbar');
            var thewidth = searchbar.parent().parent().width();
            searchbar.css('width', thewidth - 248 + 'px');
            $('#facetview_freetext').css('width', thewidth - 288 + 'px');

            // append the filters to the facetview object
            //in use
            buildfilters();
            if (options.description) {
                $('#facetview_filters').append('<div><h3>Meta</h3>' + options.description + '</div>');
            }
            //$('#facetview_freetext', obj).bindWithDelay('keyup', dosearch, options.freetext_submit_delay);
            $('input#facetview_freetext').keyup(function(event){
                if (event.keyCode == 13 || event.which == 13){
                    event.preventDefault();
                    dosearch();
                }});
            $('.icon-search').parent().click(dosearch);

            // trigger the search once on load, to get all results
            dosearch();
        }); // end of the function


    };
})(jQuery);


