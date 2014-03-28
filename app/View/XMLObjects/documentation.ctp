<?php
App::uses('XMLObject', 'AppController', 'Controller', 'Utility', 'Xml');
App::uses('HtmlHelper', 'View/Helper');

echo $this->Html->css('/js/css/tabs.css', null, array('inline' => false));
echo $this->Html->css('/js/css/jquery-ui.css', null, array('inline' => false));
echo $this->Html->script('/js/jquery.tabs.js',array('inline' => false));

$this->set('navbarArray',array(array('Documentation','documentation')));
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>LAUDATIO - Description</title>
    <script type="text/javascript">
        $(document).ready(function()  {
            jQuery('dl#tabs2').addClass('enabled').timetabs({
                defaultIndex: 0,
                animated: 'slide',
                interval: 2500
            });

            /* animation preview
            jQuery('input[name=animation]').click(function() {
                $this = jQuery(this);
                jQuery.fn.timetabs.switchanimation($this.val());
            });*/
        });
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
<div class="itemdiv">
<div class="centeredContent">
<h3>Documentation</h3>
<dl class="tabs" id="tabs2">

<dt>View
    <?php
    echo $this->Html->link(
        $this->Html->image('linkTo_smallerq.png', array('width'=>'12px', 'title' => 'Link to View', 'border' => '0')),
        '../../../repository/view',
        array('target' => '_blank', 'escape' => false)
    );
    ?>
</dt>
<dd>
    <ul>
        <li><b>No Registration </b> is needed to use the view function of LAUDATIO-Repository.</li>
        <li>The <b>TEI Header</b> provides the information for the view.</li>
        <li>Every corpus has four sections: 'Corpus', 'Document', 'Annotation' and 'Preparation'. Click on the section of interest and navigate through the information.</li>

        <b>Corpus</b><br>
        This section contains general information about the corpus itself. You get information about the corpus editors and annotators, the corpus project, the documents of the corpus and the applied annotations and formats.<br>

        <b>Document</b><br>
        This section contains bibliographic information about each document in the corpus, e.g. author, date and publication place. Each document has a list of annotation.<br>

        <b>Annotation</b><br>
        This section contains the whole tagset for each annotation with descriptions.<br>

        <b>Preparation</b><br>
        This section contains information about each step of preparation for each annotation which is listed in the section 'annotation'. Here, the tools used for data preparation, checking methods and information about the overall corpus architecture are listed.<br>
    </ul>
</dd>
<dt>Search
    <?php
    echo $this->Html->link(
        $this->Html->image('linkTo_smallerq.png', array('width'=>'12px', 'title' => 'Link to Search', 'border' => '0')),
        '../../../repository/search',
        array('target' => '_blank', 'escape' => false)
    );
    ?>
</dt>
<dd>
    <ul>
        <li><b>No Registration </b> is needed to use the search function of LAUDATIO-Repository.</li>
        <li>You can use the <b>faceted search and/or the full-text search</b> to retrieve information about all corpora in the LAUDATIO-Repository.</li>
        <li>The faceted search will filter all hits (of all available corpora in the LAUDATIO-Repository). For example: If you select the facet 'Formats' and check on 'exmaralda', you will only get the corpora which have EXMARaLDA as a format. The activated facet will be displayed above the hits. You can use more than one facet for your search.</li>
        <li>The full-text search has several options.</li>
        <ul>
            <li>Partial match - The lucene-based Full-Text Search supports partial matches e.g. without mutated vowel: 'Maerchen' The result from search-request is for e.g. 'Maerchen' or 'MÃ¤rchen'.</li>
            <li>Exact match - The lucene-based Full-Text Search supports exact matches e.g. with mutated vowel: 'MÃ¤rchen'.</li>
            <li>Fuzzy match - The lucene-based Full-Text Search supports fuzzy searches based on the Levenshtein Distance, or Edit Distance algorithm.</li>
            <li>Match all</li>
            <li>Match any</li>
            <!--<li>Learn more</li>-->
            <li>You can choose between these options to get the best results for your search.</li>
        </ul>
        <li>You can combine faceted search and full-text search. For example, choose the facet 'Formats' and a value, e.g. 'EXMARaLDA' and add a text in the input box, e.g. 'Herbology' and will only get hits where all criteria are true.</li>
    </ul>
</dd>

<dt>Import
    <?php
    echo $this->Html->link(
        $this->Html->image('linkTo_smallerq.png', array('width'=>'12px', 'title' => 'Link to Import', 'border' => '0')),
        '../../../repository/import',
        array('target' => '_blank', 'escape' => false)
    );
    ?>
</dt>
<dd>
    <ul>
        <li><b>Registration:</b> You need to be registered (create an
            <?php
            echo $this->Html->link('account)', '../../../repository/Users/requestAccount');
            ?>
            to upload corpora. If you already have a HU-Account you need not to create a new account. You can use your HU-account instead.</li>
        <li><b>Import</b> a corpus and fill in the 'Corpus Name' with an ID which might be an acronym, e.g. 'laudatio:RIDGES' for 'Register in German Science' and a 'Corpus Label' which should be the full name of the corpus, e.g. 'Register in German Science'. </li>
        <li><b>Upload</b> the metadata as a TEI XML file. You find the scheme (RNG or DTD) and documentation (TEI ODD) for the merged TEI-Header and the full workflow description for compiling the metadata in the 'Documentation' navigation bar. You only need to upload the merged TEI XML file for the whole corpus. The metadata will be validated against the scheme. You will get feedback whether the TEI XML file is valid or not.</li>
        <li><b>Upload</b> the corpus formats. A corpus may have several formats. You can upload a zipped file for each format. Add the display name of the format.</li>
    </ul>
</dd>

<dt>Modify
    <?php
    echo $this->Html->link(
        $this->Html->image('linkTo_smallerq.png', array('width'=>'12px', 'title' => 'Link to Modify', 'border' => '0')),
        '../../../repository/modify',
        array('target' => '_blank', 'escape' => false)
    );
    ?>
</dt>
<dd>
    <ul>
        <li><b>Registered corpus owner</b> (the account who uploaded the corpus) is allowed to modify the corpus.</li>
        <li>
            <b>Properties</b><br>
            <span>Corpus label: Changing the label will change the display name in the navigation bar 'view' for the corpus.</span><br>
            <span>Corpus name: Can't be modified.</span><br>
            <span>Corpus state: Changing the state will set the current version active or inactive. An active state of a corpus means that it is available at the navigation bars 'view' and 'search'.</span><br>

            <b>Data streams</b><br>
            <span>Data streams: You can download, add, change and delete single data streams of your corpus.</span><br>
            <span>Data labels: Changing the label will change the display name in the navigation bar 'view' for the corpus.</span><br>
            <span>Data MIME types: Multi-purpose Internet Mail Extensions specifies the file name of a data stream and indicate the used/given file types.</span><br>
            <span>Data state: Changing the state will set the current version active or inactive. An active state of a corpus means that it is available at the navigation bars 'view' and 'search'.</span><br>
            <span>Set version: Defines whether the applied changes will create a new version of the corpus or replace the current version.</span>
        </li>
    </ul>
</dd>

<dt>TEI ODD and Scheme</dt>
<!--<dt><a name="odd" href="#odd">TEI ODD and Scheme</a></dt>-->

<dd>
    <b>Scheme 7</b><br>
    <p>The document is the customization of the TEI p5 scheme for the LAUDATIO-Repository. The metadata which will be uploaded for each corpus as a TEI XML file which will be validated against the scheme produced by the ODD. Note, that there are several versions of the LAUDATIO-ODD, we start here with scheme 7. We customize the ODD according to the needs for the repository and the user's perspectives. If you have feature request please contact us
        <?php
        $mail = 'laudatio-user@hu-berlin.de';
        $myMail = $this->Text->autoLinkEmails($mail);
        echo '('.$myMail.').</p>';
        ?>

        <b>TEI Scheme</b><br>

        <?php
        $corpus_rng_link = '../../../repository/files/documentation/corpus/version7/teiODD_LAUDATIOCorpus_Scheme7.rng';
        $corpus_rnc_link = '../../../repository/files/documentation/corpus/version7/teiODD_LAUDATIOCorpus_Scheme7.rnc';

        $corpus_doc_presentation_link = '../../../repository/files/documentation/corpus/version7/teiODD_LAUDATIOCorpus_S7/document.html';
        //$corpus_doc_link = '../../../repository/files/documentation/corpus/version7/teiODD_LAUDATIODocumentation_S7.zip';
        $corpus_odd_link = '../../../repository/files/documentation/corpus/version7/teiODD_LAUDATIOCorpus_S7.zip';

        echo '<table><tr>';
        echo '<td><div class="download">';
        echo "<u>Corpus</u>";
        echo "<br>";

        echo 'Download <a href="'.$corpus_odd_link.'">ODD</a><br>';
        echo 'Download <a href="'.$corpus_rng_link.'">RNG Scheme</a><br>';
        echo 'Download <a href="'.$corpus_rnc_link.'">RNC Scheme</a><br>';
        //echo 'Download <a href="'.$corpus_doc_link.'">Documentation</a><br>';
        echo 'Here is the presentation from the <a href="'.$corpus_doc_presentation_link.'">documentation</a><br>';
        echo '</div></td>';


        $document_rnc_link = '../../../repository/files/documentation/document/version7/teiODD_LAUDATIODocument_Scheme7_RNC.zip';
        $document_rng_link = '../../../repository/files/documentation/document/version7/teiODD_LAUDATIODocument_Scheme7_RNG.zip';
        //$document_doc_link = '../../../repository/files/documentation/document/version7/teiODD_LAUDATIODocumentation_S7.zip';
        $document_odd_link = '../../../repository/files/documentation/document/version7/teiODD_LAUDATIODocument_Scheme7.zip';
        $document_doc_presentation_link = '../../../repository/files/documentation/document/version7/teiODD_LAUDATIODocument_Scheme7/document.html';


        echo '<td><div class="download">';
        echo "<u>Document</u>";
        echo "<br>";

        echo 'Download <a href="'.$document_odd_link.'">ODD</a><br>';
        echo 'Download <a href="'.$document_rng_link.'">RNG Scheme</a><br>';
        echo 'Download <a href="'.$document_rnc_link.'">RNC Scheme</a><br>';
        //echo 'Download <a href="'.$document_doc_link.'">Documentation</a><br>';
        echo 'Here is the presentation from the <a href="'.$document_doc_presentation_link.'">documentation</a><br>';
        echo '</div></td>';

        $preparation_rnc_link = '../../../repository/files/documentation/preparation/version7/teiODD_LAUDATIOPreparation_Scheme7_RNC.zip';
        $preparation_rng_link = '../../../repository/files/documentation/preparation/version7/teiODD_LAUDATIOPreparation_Scheme7_RNG.zip';
        $preparation_doc_presentation_link = '../../../repository/files/documentation/preparation/version7/teiODD_LAUDATIOPreparation_S7/document.html';
        //$preparation_doc_link = '../../../repository/files/documentation/preparation/version7/teiODD_LAUDATIODocumentation_S7.zip';
        $preparation_odd_link = '../../../repository/files/documentation/preparation/version7/teiODD_LAUDATIOPreparation_S7.zip';

        echo '<td><div class="download">';
        echo "<u>Preparation</u>";
        echo "<br>";


        echo 'Download <a href="'.$preparation_odd_link.'">ODD</a><br>';
        echo 'Download <a href="'.$preparation_rng_link.'">RNG Scheme</a><br>';
        echo 'Download <a href="'.$preparation_rnc_link.'">RNC Scheme</a><br>';
        //echo 'Download <a href="'.$preparation_doc_link.'">Documentation</a><br>';
        echo 'Here is the presentation from the <a href="'.$preparation_doc_presentation_link.'">documentation</a><br>';
        echo '</div></td>';
        echo '</tr></table>';

        ?>
</dd>

<dt>TEI Templates and Header (Metadata)</dt>
<dd>
    <ul>
        <b>Scheme 7</b><br>
        <?php
        #<dt>TEI ODD and Scheme</dt>
        #<a href="#anfang">Seitenanfang</a>
        #<a href="../projektintern.htm#anker">Anker definieren und Verweise zu Ankern</a>
        #echo '<a href="#odd">Here</a> you can find the Downloads for Scheme 6.<br>';
        ?>
        <script>
            //$('#odd').trigger('click');
            /*
             $( document ).ready(function() {
             $("odd" ).click(function() {
             alert( "Blubb..." );
             });
             });
             */
        </script>

        <br>

        <b> Creating your own metadata</b>
        <li>Download the current version of the templates for each section of a corpus ('Corpus', 'Document' and 'Preparation').</li>
        <li>Fill in the required information:</li>
        <ul>
            <b>'Corpus' - Template</b><br>
            <li>For one corpus you only need one TEI XML file.</li>
            <li>Fill in the corpus creators, annotators, project descriptions, list of documents (with a short name) and the encodings for each format of your corpus.</li>
            <li>For every format there is one encodingDesc where you list all annotation keys and values of each format (there may be differences between the formats).</li>
        </ul>
        <p>For help, see other TEI XML files which are already uploaded in the LAUDATIO-Repository or the TEI ODD for the section of interest.</p>
        <ul>
            <b>'Document' - Template</b><br>
            <li>For each document you need one TEI XML file.</li>
            <li>Fill in all required information, e.g. author of the document, place and date.</li>
            <li>Every document has its own list of annotation (there may be differences between the documents).</li>
        </ul>
        <p>For help, see other TEI XML files which are already uploaded in the LAUDATIO-Repository or the TEI ODD for the section of interest.</p>
        <ul>
            <b>'Preparation' - Template</b><br>
            <li>For each annotation layer you need one TEI XML file independent on how the annotation was made.
                Note, that if one annotation layer, e.g. a part of speech annotation (pos) is applied in all formats of the corpus, one TEI XML file is sufficient for this annotation layer.</li>
            <li>Every TEI XML file contains the whole preparation history for one annotation layer, including the first annotation, converting processes and checking methods. For each preparation step and format there is one encodingDesc. For example: A first annotation is applied with the EXMARaLDA Editor and afterwards converted to relANNIS format. For the EXMARaLDA format there is one encodingDesc and for the relANNIS format another one where the tools, the checking methods and corpus architecture information such as segmentation is listed, too.</li>
        </ul>
        <p>For help, see other TEI XML files which are already uploaded in the LAUDATIO-Repository or the TEI ODD for the section of interest.</p>
        <li>Merge all TEI-Header with the teitool</li>
        <ul>
            <li>Build a folder structure as the following:</li>
            <span>First order folder 'CorpusName'</span><br>
            <span>Second order folder 'CorpusHeader' = contains one TEI XML file for the corpus which is validated with the scheme for the section 'Corpus'.</span><br>
            <span>Second order folder 'DocumentHeader' = contains all TEI XML files for the documents which are validated with the scheme for the section 'Document'.</span><br>
            <span>Second order folder 'PreparationHeader' = contains all TEI XML files for the preparationSteps for each annotation which are validated with the scheme for the section 'Preparation'.</span><br>
            <span>Second order folder 'AllHeader' = contains nothing.</span><br>
            <li>Run the teitool in you command line.</li>

            <?php
            $teitool_link = '../../../repository/validation/teitool-1.0.jar';
            echo 'Download <a href="'.$teitool_link.'">teitool 0.1</a><br>';
            ?>
        </ul>
        <li>Upload the merged TEI XML file into LAUDATIO-Repository.</li>
    </ul>
</dd>
</dl>
</div>
</div>
</body>
</html>