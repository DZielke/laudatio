<?php
    echo $this->Html->script('jquery-ui',array('inline' => false));
    echo $this->Html->css('/js/css/jquery-ui.css', null, array('inline' => false));
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>LAUDATIO - Description</title>
<script>

$(function() {
	$( "#accordion" ).accordion({
        heightStyle: "content",
        header: "h2",
        collapsible: true,
        active: false
    });
});

/*
* hoverIntent | Copyright 2011 Brian Cherne
* http://cherne.net/brian/resources/jquery.hoverIntent.html
* modified by the jQuery UI team
*/

$.event.special.hoverintent = {
    setup: function() {
        $( this ).bind( "mouseover", jQuery.event.special.hoverintent.handler );
    },
    teardown: function() {
        $( this ).unbind( "mouseover", jQuery.event.special.hoverintent.handler );
    },
    handler: function( event ) {
        var currentX, currentY, timeout,
        args = arguments,
        target = $( event.target ),
        previousX = event.pageX,
        previousY = event.pageY;

        function track(event) {
            currentX = event.pageX;
            currentY = event.pageY;
        };
        function clear() {
            target
            .unbind( "mousemove", track )
            .unbind( "mouseout", clear );
            clearTimeout( timeout );
        }
        function handler() {
            var prop,
            orig = event;
            if ( ( Math.abs( previousX - currentX ) +
            Math.abs( previousY - currentY ) ) < 7 ) {
                clear();
                event = $.Event( "hoverintent" );
                for ( prop in orig ) {
                    if ( !( prop in event ) ) {
                        event[ prop ] = orig[ prop ];
                    }
                }
                // Prevent accessing the original event since the new event
                // is fired asynchronously and the old event is no longer
                // usable (#6028)
                delete event.originalEvent;
                target.trigger( event );
            }
            else {
                previousX = currentX;
                previousY = currentY;
                timeout = setTimeout(handler, 200);
            }
        }
        timeout = setTimeout(handler, 200);
        target.bind({
            mousemove: track,
            mouseout: clear
        });
    }
};


function display(id) {
    var obj = document.getElementById(id)
    if (obj.style.display == "none") {
        obj.style.display = "block"
    }
    else {
        obj.style.display = "none"
    }
    return false
}


</script>
</head>

<!-- Piwik -->
<script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
    _paq.push(["setCookieDomain", "*.www.laudatio-repository.org"]);
    _paq.push(["trackPageView"]);
    _paq.push(["enableLinkTracking"]);

    (function() {
        var u=(("https:" == document.location.protocol) ? "https" : "http") + "://www.laudatio-repository.org/piwik/";
        _paq.push(["setTrackerUrl", u+"piwik.php"]);
        _paq.push(["setSiteId", "1"]);
        var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
        g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
    })();
</script>
<!-- End Piwik Code -->

<body>



<h3>LAUDATIO</h3>
<p><b>L</b>ong-term <b>A</b>ccess and <b>U</b>sage of <b>D</b>eeply <b>A</b>nnotated Informa<b>tio</b>n</p>
<!--input type="button" style="float:right;" value="Show/Hide important information to LAUDATIO-Repository" onclick="display('box1')"-->
<br><div id="box1" style="padding:2px;border:1px solid black;">Welcome to LAUDATIO-Repository!
    This is the first release of the repository which is still work in progress. Please contact us if you have any questions or feature or bug requests.<br>

    <?php
    $mail = 'laudatio-user@hu-berlin.de';
    $myMail = $this->Text->autoLinkEmails($mail);
    echo '('.$myMail.')</p>';
    ?>

</div><br>
<div id="accordion">
<h2><a href="#">Goals</a></h2>
<div>
<!--<p>What is the aim of the LAUDATIO project</p>-->
<p>The management and archiving of digital research data is an overlapping field for linguistics, library
and information science (LIS) and computer science. These disciplines are cooperating in the LAUDATIO project. 
The name LAUDATIO is an abbreviation for Long-term Access and Usage of Deeply Annotated Information. 
The project is funded by the German Research Foundation from 2011-2014. The departments of Corpus Linguistics 
as well as Historical Linguistics, and the Computer and Media Service (CMS) at Humboldt-Universität zu Berlin and 
The National Institute for Research in Computer Science and Control (INRIA France) are project partners cooperating 
with the Berlin School of Library and Information Science (BSLIS).</p>
<p>LAUDATIO aims to build an open access research data repository for historical linguistic data with respect to the
above mentioned requirements of historical corpus linguistics. For the access and (re-)use of historical linguistic data the 
LAUDATIO repository uses a flexible and appropriate documentation schema with a subset of TEI customized by 
TEI ODD. The extensive metadata schema contains information about the preparation and checking methods 
applied to the data, tools, formats and annotation guidelines used in the project, as well as bibliographic metadata, and 
information on the research context (e.g. the research project). To provide complex and comprehensive search in the 
linguistic annotation data, the linguistic search and visualization tool ANNIS will be integrated in the 
LAUDATIO repository infrastructure.</p>
<p>All corpora are available with open access Creative Commons License in LAUDATIO. All researchers from the academic disciplines of Linguistics
and Historical Linguistics can use and re-use the corpora e.g.:
</p>
<ul>
<li>look at all corpora,</li>
<li>search in all annotations,</li>
<li>download all corpora,</li>
<li>upload new annotations to an existing corpus</li>
<li>and upload new corpora.</li>
</ul>
</div>
<h2><a href="#">About Us</a></h2>
<div>
    <p>What is meant by research data in historical corpus linguistics?</p>

    <p>Historical corpus linguistic research data consists of digitized text material, transcriptions of historical texts and linguistic annotation
        &#45; a linguistic corpus. It is varied, often idiosyncratic depending on the research question and the language investigated, e.g.
        standard versus non-standard varieties of a language. Linguistic annotations of all kinds such as morpho-syntactic,
        syntactic, pragmatic and semantic annotation are seen as a crucial and essential part of a corpus in themselves.
        However, every single corpus built for a single research question may be used for another one as well. A collection of historical
        corpora from different periods may be even more useful, for example in order to trace diachronic language change phenomena
        over a long period. For this academic discipline, it would be a big advantage if such corpora, which are prepared in one way or another,
        are accessible and (re-)usable for all corpus linguists dealing with similar historical language data.
    </p>
</div>
<h2><a href="#">Documentation</a></h2>
<div>
    <p>Metadata and Research Data Documentation</p>
    <p>A detailed and full documentation of the preparation and annotation process including a bibliographic list of the digitized texts
        is an important step towards improving the quality and thus the usability of historical linguistic corpus data: Such a documentation includes
        a description of the corpus design, the proper references of authors and editors of the corpus and the annotation, including formats and methods
        of quality assurance such as measuring Inter-Coder Agreement. Thus, the documentation covers document description as well
        as object description. Additionally, for (re-)use of the linguistic digital research data the aforementioned information is essential in order to
        add annotations or new texts to already existing corpora, or in order to use them as a template for "best practices" in building a new corpus.
        The required information will be provided by the customization of a TEI Header with an ODD specification along with a RELAX NG schema.
        Metadata must capture the entire life cycle of historical linguistic data.
    </p>
</div>
</div>
</body>
</html>