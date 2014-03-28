<?php
$this->set('navbarArray',array(array('Import','import')));
#echo $this->Html->css('cake.my.changed');
echo $this->Html->script('uploadscript',array('inline' => false));
echo $this->Html->script('validate',array('inline' => false));


/*echo $this->Html->script('fileUpload/jquery.iframe-transport.js',array('inline' => false));
echo $this->Html->script('fileUpload/jquery.ui.widget.js',array('inline' => false));
echo $this->Html->css('fileUpload/jquery.fileupload-ui.css',null,array('inline' => false));
echo $this->Html->script('fileUpload/jquery.fileupload.js',array('inline' => false));
echo $this->Html->script('fileUpload/jquery.fileupload-ui.js',array('inline' => false));

echo $this->Html->css('fileUpload/style.css',null,array('inline' => false));*/
echo $this->Html->script('ajaxupload',array('inline' => false));
//echo $this->Html->script('registerpid',array('inline' => false));
//echo $this->Html->script('registerpid',array('inline' => false));
echo $this->Html->css('validate',null,array('inline' => false));
echo $this->Html->css('creativecommons',null,array('inline' => false));
/*
echo '<span class="helptooltip"><p>Here you can upload your own corpus. For a step by step guide see the navigation bar &lsquo;Documentation&rsquo;.</p>';
echo $this->Html->image("help.png",array(
    'border' => '0',
    'width'=>'20px',
    'alt' => 'help',
));
echo '</span>';
*/

if(isset($ingestedObject)){
    echo $this->html->link('View imported Corpus',array('controller' => 'XMLObjects', 'action' => 'objects/'.$ingestedObject));
    echo '</br>';
    echo '<p></p>';
    echo '</br>';
}


echo '<h3>Import a New Corpus</h3>';

if(!isset($label)){
	$label = '';
}
if(!isset($id)){
	$id = '';
}

if(!isset($response)){
	$response = '';
}

?>
<!--<a href="http://jquery.com/">jQuery</a>-->


<form id="ingestform" action="import" method="POST" enctype="multipart/form-data" xmlns="http://www.w3.org/1999/html"
      xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">

<table id="uploadform" border="0" cellpadding="0" cellspacing="4">
<!--
<tr>
    <?php
        /*
        echo '<td>Authorization form:<span class="helptooltip"><p>Please fill out the document and send it to the stated postal address</p>';
        echo $this->Html->image("help.png",array(
            'border' => '0',
            'width'=>'20px',
            'alt' => 'help',
        ));
        echo '</span></td>';

        $authorization_link = '../../../repository/auth/Authentifizierung_HU_vorlage.docx';
        echo '<td><a href="'.$authorization_link.'">Access auth form</a></td><td></td><td></td>';
        */
    ?>
</tr>
-->
<tr>
    <?php
        echo '<td>Corpus Name:<span class="helptooltip"><p>An acronym with an own ID, e.g. laudatio:RIDGES-Herbology.</p>';
            echo $this->Html->image("help.png",array(
                'border' => '0',
                'width'=>'20px',
                'alt' => 'help',
            ));
        echo '</span></td>';

		 echo "<td><input id='id' name='id' type='text' value='$id' size='16' maxlength='64'></td><td></td><td></td>";?>
</tr>
<tr>
    <?php
        echo '<td>Corpus Label:<span class="helptooltip"><p>The full name of the corpus or corpus project, e.g. Register in German Science.</p>';
            echo $this->Html->image("help.png",array(
                'border' => '0',
                'width'=>'20px',
                'alt' => 'help',
            ));
        echo '</span></td>';

     echo "<td><input name='label' type='text' value='$label' size='16' maxlength='64'></td><td></td><td></td>";?>
</tr>
<tr>
	<!--<td>TEI-Header (Metadata):</td>-->

    <?php
        echo '<td>TEI-Header (Metadata):<span class="helptooltip"><p>Upload the merged TEI XML file of your corpus.</p>';
            echo $this->Html->image("help.png",array(
                'border' => '0',
                'width'=>'20px',
                'alt' => 'help',
            ));
        echo '</span></td>';
    ?>

	<!-- <input type="hidden" name="MAX_FILE_SIZE" value="300000" /> -->
	<td><input id="teiupload" name="teifile" type="file" data-url="<?php echo Router::url(array('controller'=>'XMLObjects','action'=>'ajaxUpload'),true);?>">
    <div style="display:none;" id="uploadResponse">
        <span id="uploadPercentage"></span>
        <span id="corpusResponse"> validating Corpus<?php echo $this->Html->image('ajax-loader.gif', array('id' => 'ajax-loader'));?></span><br/>
        <span id="documentResponse"> validating Documents<?php echo $this->Html->image('ajax-loader.gif', array('id' => 'ajax-loader'));?></span><br/>
        <span id="preparationResponse"> validating Preparations<?php echo $this->Html->image('ajax-loader.gif', array('id' => 'ajax-loader'));?></span>
    </div></td><td></td><td></td>

	<!--<td><button type="button" id="indexAjax">indexTest</button></td>-->
</tr>
<tr>
  <!--<td>Corpus Format:</td>-->
  <?php
    echo '<td>Corpus Format:<span class="helptooltip"><p>Upload a zipped file for each format of the corpus and chose the display name as given in your TEI XML file, e.g. EXMARaLDA as in &lt;application ident="EXMARaLDA" version="1.5.1"&gt;.</p>';
        echo $this->Html->image("help.png",array(
            'border' => '0',
            'width'=>'20px',
            'alt' => 'help',
        ));
    echo '</span></td>';
  ?>

  <td><input name="formatname[]" type="text" size="15" maxlength="30"></td>
  <td>File: <input name="userfile[]" type="file"></td>
  <td><button type="button" onclick="deleteFormat($(this))">Delete</button></td>
</tr>
<tr>
	<td><button type="button" onclick="addFormat()">Add Format</button></td><td></td><td></td><td></td>
</tr>
<tr><th id="openAccessTitle">Open Access:<span class="helptooltip"><p>In order to add the corpus to the view and search functions allow open access publication of the corpus in the LAUDATIO-Repository.</p>
     <?php
        echo $this->Html->image("help.png",array(
            'border' => '0',
            'width'=>'20px',
            'alt' => 'help',
        ));
     ?>
</span></th></tr>
<tr><td>
    <input type="checkbox" name="doIndex" value="1" checked="checked">Open access publication of the corpus for the search function of the LAUDATIO-Repository.<br>
    <input type="checkbox" name="openAccess" value="1" checked="checked">Open access publication of the corpus for the view function of the LAUDATIO-Repository.


</td><td></td><td></td><td></td></tr>

<tr><th>Creative Commons Licences:</th></tr>

    <tr><td id="creativecommonsField">
    <input type="hidden" id="cc_js_seed_uri" value="http://creativecommons.org/licenses/by-sa/3.0/de/" />
    <script type="text/javascript" src="http://api.creativecommons.org/jswidget/tags/0.97/complete.js?locale=en_US&amp;jurisdictions=disabled"></script>
    </td><td></td><td></td><td></td></tr>
</table>
<!--<input type="checkbox" name="test" value="true">Index TeiFile-->
<br>
<input type="submit"  value="Upload Corpus!">
</form>


<!-- Piwik
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

<?php

    
	#$this->Js->codeBlock("write('hello')");jQuery("tr#format1").hide();
	#TODO: ausgabe formatieren, object zurückgeben wenn erfolgreich
	/*if($response != ''){
		echo '<br /><div style="text-decoration:underline;font-weight:bold;">Antwort des Aufrufs</div><br />';
		echo '<div style="border:1px solid gray;">';
		$response = preg_replace('/.+<body>/','',$response);
		$response = preg_replace('#</body>.+#','',$response);
		echo $response;
		echo '</div>';
	}*/
?>