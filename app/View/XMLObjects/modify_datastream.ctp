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

<?php
echo $this->Html->script('uploadscript',array('inline' => false));
	if(!isset($objectID)) $objectID = null;
	$this->set('navbarArray',
        array(
            array('Modify','modify'),
            array('Modify Object','modifyObject/'.urlencode($objectID)),
            array('Modify Datastream')
        )
    );
	if(isset($response)){
		$xml = simplexml_load_string($response) or die("Error: Can not create object"); 
		#var_dump($xml);
		$label = $xml->dsLabel;
		$controlGroup = $xml->dsControlGroup;
		$mimeType = $xml->dsMIME;
		$state = $xml->dsState;
		$created = $xml->dsCreateDate;
		$versionable = (string)$xml->dsVersionable;
		#$accessURL = 
		$checksum = $xml->dsChecksum;
		$checksumtype = $xml->dsChecksumType;
	}else{
		$label = '';
	
	}
	echo '<form id="modForm" method="POST" enctype="multipart/form-data">';
	echo '<h3>'.$dsid.'</h3>';
	echo '<table>';
	#echo "<tr><td>Label:</td>";


    echo '<tr><td title="Corpus Label">Corpus Label:<span class="helptooltip"><p>CorpusLabel Usually the full name of the corpus or corpus project, e.g. Register in German Science. If you´re not satisfied with the current label, you can change it.</p>';
        echo $this->Html->image("help.png",array(
            'border' => '0',
            'width'=>'20px',
            'alt' => 'help',
        ));
    echo '</span></td>';


	echo "<td><input name='label' type='text' value='$label' size='32' maxlength='64'></td></tr>";
	#echo "<tr><td>Control Group:</td><td>$controlGroup</td></tr>";

    echo '<tr><td>Control Group:<span class="helptooltip"><p>Objects (usually) obtain no data but only data streams that indicate where the data are to be found.<br><br> A new datastream can be defined as one of four types:<br>(I)nternal XML Metadata<br>(M)anaged Content<br>(E)xternal Referenced Content<br>(R)edirect Referenced Content</p>';
    echo $this->Html->image("help.png",array(
        'border' => '0',
        'width'=>'20px',
        'alt' => 'help',
    ));
    echo '</span></td>';
    echo "<td>$controlGroup</td></tr>";
/*
echo '<td>$controlGroup</td><td><span class="helptooltip"><p>a new datastream can be defined as one of four types:<br>(I)nternal XML Metadata<br>(M)anaged Content<br>(E)xternal Referenced Content<br>(R)edirect Referenced Content</p>';
echo $this->Html->image("help.png",array(
    'border' => '0',
    'width'=>'20px',
    'alt' => 'help',
));
echo '</span></td></tr>';
*/
	#echo "<tr><td>MIME Type:</td><td><input name='mimeType' type='text' value='$mimeType' size='32' maxlength='64'></td></tr>";

    echo '<tr></tr><td>MIME Type: <span class="helptooltip"><p>Multi-purpose Internet Mail Extensions specifies the file name of a data stream and indicate the used/given file types.</p>';
    echo $this->Html->image("help.png",array(
        'border' => '0',
        'width'=>'20px',
        'alt' => 'help',
    ));
    echo "</span></td><td><input name='mimeType' type='text' value='$mimeType' size='32' maxlength='64'></td></tr>";

	#echo "<tr><td>State:</td>";

    echo '<tr><td title="State">State:<span class="helptooltip"><p>Changing the state will set the current version active or inactive. An active state of a corpus means that it is available at the navigation bars &lsquo;view&rsquo; and &lsquo;search&rsquo;.</p>';
    echo $this->Html->image("help.png",array(
        'border' => '0',
        'width'=>'20px',
        'alt' => 'help',
    ));
    echo '</span></td>';
?>
	<td>
	<select name="state" size="1">
	<option value="A" <?php if($state=='A') echo 'selected';?>>Active (A)</option>
	<option value="I"<?php if($state=='I') echo 'selected';?>>Inactive (I)</option>
	<option value="D"<?php if($state=='D') echo 'selected';?>>Deleted (D)</option>
	</select>
	</td></tr>
<?php 
	echo "<tr><td>Created:</td><td>$created</td></tr>";
	echo "<tr><td>Set Version:</td>";
?>
	<td>
	  
	<select name="versionable" size="1">
	<option value="true" <?php if($versionable=="true") echo 'selected';?>>Updates will create a new version</option>
	<option value="false" <?php if($versionable=="false") echo 'selected';?>>Updates will replace most recent version</option>
	</select>
	</td>
    </tr>
   
<?php 
	#echo "<tr><td>Checksum:</td><td>$checksum</td></tr>";

    echo '<tr><td>Checksum:<span class="helptooltip"><p>Measure to ensure data integrity during data transmission or storage.</p>';
    echo $this->Html->image("help.png",array(
        'border' => '0',
        'width'=>'20px',
        'alt' => 'help',
    ));
    echo '</span></td>';
    echo "<td>$checksum</td></tr>";

	echo '</table>';
	echo '<input type="submit" name="modDs" value="Save Changes">';
	echo '</form></br>';

    /*if($versionable=="true"){
        echo '<button id="uploadButton" style="float:right" type="button" onclick="showVersionUpload()">Upload New Version</button>';
    }*/
?>

<?php 
    if(isset($error) && $error != ''){
        echo '<br /><div style="text-decoration:underline;font-weight:bold;">Antwort des Aufrufs</div><br />';
        echo '<div style="border:1px solid gray;">';
        $error = preg_replace('/.+<body>/','',$error);
        $error = preg_replace('#</body>.+#','',$error);
        echo $error;
        echo '</div>';
    }
?>
