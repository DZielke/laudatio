jQuery(document).ready(function($) {
    $('.facet-view-simple').facetview({
        search_url: '//www.laudatio-repository.org/laudatio5/_search?--allow-running-insecure-content',

        search_index: 'elasticsearch',
        freetext_submit_delay: "200", // in ms (deactivated, use the search button)
        highlight_documents: true,
        show_documents: 5,  //defines how much documents will be shown in a result before truncating
        show_facetitems:10,
        facets: [
            {'field': 'teiCorpus.teiHeader.fileDesc.titleStmt.title.$.untouched', 'display': 'Corpora'},
            {'field': 'teiCorpus.teiHeader.fileDesc.publicationStmt.idno.$.untouched', 'display': 'Projects'},
            {'field': 'teiCorpus.teiHeader.encodingDesc.appInfo.application.@ident', 'size': 100, 'order':'term', 'display': 'Formats'},
            {'field': 'teiCorpus.teiHeader.fileDesc.publicationStmt.date.@when', 'range':'date', 'histogram':'year', 'display': 'Date - Corpus'},

            //with subfacets
            //{'field': 'teiCorpus.teiHeader.fileDesc.extent.$', 'range':'extent', 'histogram' : 1000, 'display': 'Size - Corpus','subfacets':[['teiCorpus.teiHeader.fileDesc.extent.@type','words','Words'],['teiCorpus.teiHeader.fileDesc.extent.@type','tokens','Tokens']]},
            {'field': 'teiCorpus.teiHeader.fileDesc.extent.$', 'display': 'Size - Corpus','subfacets':[['teiCorpus.teiHeader.fileDesc.extent.@type','words','Words'],['teiCorpus.teiHeader.fileDesc.extent.@type','tokens','Tokens']]},
            {'field': 'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.valList.valItem.@ident', 'nested':'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec','size': 100, 'order':'term', 'display': 'Annotation - Graphical','filter':['teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.@ident','graphical']},
            {'field': 'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.valList.valItem.@ident', 'nested':'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec','size': 100, 'order':'term', 'display': 'Annotation - Lexical', 'filter':['teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.@ident','lexical']},
            {'field': 'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.valList.valItem.@ident', 'nested':'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec','size': 100, 'order':'term', 'display': 'Annotation - Transcription','filter':['teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.@ident','transcription']},
            {'field': 'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.valList.valItem.@ident', 'nested':'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec','size': 100, 'order':'term', 'display': 'Annotation - Syntactical','filter':['teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.@ident','syntactical']},
            {'field': 'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.valList.valItem.@ident', 'nested':'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec','size': 100, 'order':'term', 'display': 'Annotation - Meta','filter':['teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.@ident','meta']},
            {'field': 'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.valList.valItem.@ident', 'nested':'teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec','size': 100, 'order':'term', 'display': 'Annotation - Other', 'filter':['teiCorpus.teiCorpus.teiHeader.encodingDesc.schemaSpec.elementSpec.@ident','other']},
            //{'field': 'teiCorpus.teiHeader.encodingDesc.tagsDecl.namespace.@name', 'size': 100, 'order':'term', 'display': 'Annotation - Mark up'},
            {'field': 'teiCorpus.teiCorpus.teiHeader.fileDesc.publicationStmt.date.@when', 'histogram':'year','range':'date','nested':'teiCorpus.teiCorpus.teiHeader','order':'count', 'display': 'Date - Document'},
            {'field': 'teiCorpus.teiCorpus.teiHeader.fileDesc.extent.$', 'display': 'Size - Document','subfacets':[['teiCorpus.teiCorpus.teiHeader.fileDesc.extent.@type','words','Words'],['teiCorpus.teiCorpus.teiHeader.fileDesc.extent.@type','tokens','Tokens']]}
        ],
        //restricts the response from server to the specified fields and reduces traffic
        "searchfields":{
            "partial":{
                "include":[
                    "teiCorpus.teiHeader.fileDesc.titleStmt.title.$.untouched",
                    "teiCorpus.teiHeader.fileDesc.publicationStmt.date.@when",
                    "teiCorpus.teiHeader.revisionDesc.change.@n",
                    "teiCorpus.teiHeader.encodingDesc.projectDesc.p.$",
                    "teiCorpus.teiHeader.encodingDesc.projectDesc.p.ref.@target",
                    "teiCorpus.teiCorpus.teiHeader.fileDesc.titleStmt.title.$",
                    "teiCorpus.teiCorpus.teiHeader.fileDesc.@id",
                    "teiCorpus.teiHeader.fileDesc.extent.$",
                    "teiCorpus.teiHeader.fileDesc.extent.@type"
                ]
            }
        },
        paging: {
            from: 0,
            size: 10
        }
    });

    // set up form
    $('.demo-form').submit(function(e) {
        e.preventDefault();
        var $form = $(e.target);
        var _data = {};
        $.each($form.serializeArray(), function(idx, item) {
            _data[item.name] = item.value;
        });
        $('.facet-view-here').facetview(_data);
    });
});
