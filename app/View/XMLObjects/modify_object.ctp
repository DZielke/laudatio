<?php #echo $this->Html->css('cake.my.changed');
$this->set('navbarArray',array(array('Modify','modify'),array('Modify Object','modifyObject')));
echo $this->Html->script('uploadscript',array('inline' => false));
echo $this->Html->css('jquery-ui.min.css', null, array('inline' => false));
echo $this->Html->script('jquery-ui.min.js',array('inline' => false));
echo $this->Html->script('modify',array('inline' => false));


if(!isset($label)){
	$label = '';
}
if(!isset($id)){
	$id = '';
}
if(!isset($owner)){
	$owner = '';
}
if(!isset($state)){
	$state = '';
}
if(!isset($response)){
	$response = '';
}
if(!isset($datastreams)){
	$datastreams = '';
}
?>

<!--<a href="http://jquery.com/">jQuery</a>--> 
                              
			

<form  method="POST" enctype="multipart/form-data">
<h3>Properties</h3>
<table border="0" cellpadding="0" cellspacing="4">
<!--<tr>	<td>Corpus Name:</td>-->

        <?php
            echo '<tr><td title="Corpus Name">Corpus Name:<span class="helptooltip"><p>ID:CorpusName Usually an acronym with an own ID, e.g. laudatio:RIDGES-Herbology. The ID cannot be changed.</p>';
                echo $this->Html->image("help.png",array(
                    'border' => '0',
                    'width'=>'20px',
                    'alt' => 'help',
                ));
            echo '</span></td>';
            echo "<td id='corpusID'>$id</td>";
        ?>
		
</tr>
<!--<tr>	<td>Corpus Label:</td>-->
    <?php
        echo '<tr><td title="Corpus Label">Data stream Label:<span class="helptooltip"><p>CorpusLabel Usually the full name of the corpus or corpus project, e.g. Register in German Science. If you´re not satisfied with the current label, you can change it.</p>';
            echo $this->Html->image("help.png",array(
                'border' => '0',
                'width'=>'20px',
                'alt' => 'help',
            ));
        echo '</span></td>';
	    echo "<td><input name='label' type='text' value='$label' size='32' maxlength='64'></td>";
    ?>
</tr>
<!--<tr>	<td>Owner:</td>-->
    <?php
        echo '<tr><td title="Owner">Owner:<span class="helptooltip"><p>You can chose other accounts to get the same status as corpus administrator for the current corpus.</p>';
            echo $this->Html->image("help.png",array(
                'border' => '0',
                'width'=>'20px',
                'alt' => 'help',
            ));
        echo '</span></td>';
	    echo "<td><input name='owner' type='text' value='$owner' size='32' maxlength='64'></td>";
    ?>
</tr>
<!--<tr>	<td>State</td>-->
    <?php
        #echo '<tr><td title="State">State:<span class="helptooltip"><p>Changing the state will set the current version active or inactive. An active state of a corpus means that it is available at the navigation bars &lsquo;view&rsquo; and &lsquo;search&rsquo;.</p>';
    #echo '<tr><td title="State">State:<span class="helptooltip"><p>Changing the state will set the current version active or inactive. An active state of a corpus means that it is available at the navigation bars &lsquo;view&rsquo; and &lsquo;search&rsquo;.</p>';
    echo '<tr><td title="State">State:<span class="helptooltip"><p>Shows the state of the corpus in Laudatios database.</p>';
            echo $this->Html->image("help.png",array(
                'border' => '0',
                'width'=>'20px',
                'alt' => 'help',
            ));
        echo '</span></td>';
    ?>

	<td>
	<select name="state" size="1" disabled>
	<option value="A" <?php if($state=='A') echo 'selected';?>>Active (A)</option>
	<option value="I"<?php if($state=='I') echo 'selected';?>>Inactive (I)</option>
	<option value="D"<?php if($state=='D') echo 'selected';?>>Deleted (D)</option>
	</select>
	</td>
</tr>
</table>
<input type="submit" value="Save Changes">
</form>

</br>
<h3>Data streams</h3>
<table id="uploadform">
<tr><th>Data stream-ID</th><th>Label</th><th>mimeType</th></tr>
<?php
foreach($datastreams as $ds){
    if($ds['dsid'] == 'DC' || $ds['dsid'] == 'teiVersion' || $ds['dsid'] == 'license' || $ds['dsid'] == 'handlePIDs') continue;
	echo '<tr><td>'.$ds['dsid'].'</td>';
	echo '<td>'.$ds['label'].'</td>';
	echo '<td>'.$ds['mimeType'].'</td>';

	echo '<td><button type="button" onclick="window.location.href=\'../modifyDatastream/'.urlencode(trim($id)).'/'.urlencode(trim($ds['dsid'])).'\'">Modify</button></td>';
    echo '<td><button type="button" onclick="window.location.href=\'../downloadDatastream/'.urlencode(trim($id)).'/'.urlencode(trim($ds['dsid'])).'\'">Download</button></td>';
    if(strpos($ds['dsid'],"TEI-header") ===0 ){
        echo '<td><button type="button" onclick="window.location.href=\'../uploadNewTeiVersion/'.urlencode(trim($id)).'/'.urlencode(trim($ds['dsid'])).'\'">Upload new Version</button></td>';
    }else{
        echo '<td><button type="button" onclick="window.location.href=\'../uploadNewVersion/'.urlencode(trim($id)).'/'.urlencode(trim($ds['dsid'])).'\'">Upload new Version</button></td>';
    }
    echo '<td><button type="button" onclick="window.location.href=\'../dsVersionHistory/'.urlencode(trim($id)).'/'.urlencode(trim($ds['dsid'])).'\'">History</button></td>';
    echo '<td><button type="button" onclick="return confirmDelete(\''.$ds['dsid'].'\',\''.urlencode(trim($id)).'\')">Delete</button></td>';
   	echo '</tr>';
}
?>
<tr>	
	 <?php
        echo '<td title="Data streams"><button type="button" onclick="window.location.href=\'../addDatastream/'.urlencode(trim($id)).'\'">Add New Datastreams</button>';
     /*
        echo '<span class="helptooltip"><p>Upload/modify/download a zipped file for each format of the corpus and give the file a display name, e.g. upload exb-files and names them &lsquo;EXMARaLDA&rsquo;. <br><br>The display name must be exactly the same name as the given name under application ident in TEI-Header Scheme.</p>';
        echo $this->Html->image("help.png",array(
             'border' => '0',
             'width'=>'20px',
             'alt' => 'help',
        ));
     */
        echo '</span></td>';

        echo '<td title="Data streams (Metadata)"><button type="button" onclick="window.location.href=\'../addTeiheader/'.urlencode(trim($id)).'\'">Add New TEI-header</button>';
     /*
        echo '<span class="helptooltip"><p>Upload/modify/download the merged TEI XML file for your corpus.</p>';
        echo $this->Html->image("help.png",array(
             'border' => '0',
             'width'=>'20px',
             'alt' => 'help',
        ));
     */
        echo '</span></td>';
     ?>
</tr>
</table>
<br>
<?php
echo $this->Html->image("delete.ico",array(
    "border" => "0",
    'id' => 'deleteIcon',
    'width'=>'15px',
    'class' => 'clickable hidden',
    'style'=>'float:right; margin-left:10px;',
    'title' => 'remove user',
    "alt" => "remove"
));
?>
<h3>Users</h3>
<table>
    <tr><th>Creator Account:</th></tr><tr><td><?php echo $creator ?></td></tr>
    <!--<tr><th>Open Access:</th></tr><tr><td>-->
    <?php
        echo '<tr><th id="openAccessTitle">Open Access:<span class="helptooltip"><p>In order to add the corpus to the view and search functions allow open access publication of the corpus in the LAUDATIO-Repository.</p>';
            echo $this->Html->image("help.png",array(
                'border' => '0',
                'width'=>'20px',
                'alt' => 'help',
            ));
        echo '</span></th></tr><tr><td>';


    $openAccessUrl = Router::url(array('controller'=>'XMLObjects','action'=>'toggleOpenAccess'),true);
    $indexingUrl = Router::url(array('controller'=>'XMLObjects','action'=>'toggleIndexing'),true);

    if($openAccess) {
        echo '<input data-url="'.$openAccessUrl.'" type="radio" id="openAccessTrue" name="openAccess" checked="checked">All users are allowed to view this Corpus</br>';
        echo '<input data-url="'.$openAccessUrl.'" type="radio" name="openAccess">Only allowed users can view this Corpus</td></tr>';
    }
    else {
        echo '<input data-url="'.$openAccessUrl.'" type="radio" id="openAccessTrue" name="openAccess">All users are allowed to view this Corpus</br>';
        echo '<input data-url="'.$openAccessUrl.'" type="radio" name="openAccess"  checked="checked">Only allowed users can view this Corpus</td></tr>';
    }

    echo '<tr><th id="indexingTitle">Index Corpus for Search:<span class="helptooltip"><p>Allow/Prohibit to add the corpus to the search functions in the LAUDATIO-Repository.</p>';
    echo $this->Html->image("help.png",array(
        'border' => '0',
        'width'=>'20px',
        'alt' => 'help',
    ));
    echo '</span></th></tr><tr><td>';

    if($indexing) {
        echo '<input data-url="'.$indexingUrl.'" type="radio" id="indexingTrue" name="indexing" checked="checked">Allow to add the corpus to the search functions</br>';
        echo '<input data-url="'.$indexingUrl.'" type="radio" name="indexing">Don\'t allow to add the corpus to the search functions</td></tr>';
    }
    else {
        echo '<input data-url="'.$indexingUrl.'" type="radio" id="indexingTrue" name="indexing">Allow to add the corpus to the search functions</br>';
        echo '<input data-url="'.$indexingUrl.'" type="radio" name="indexing"  checked="checked">Don\'t allow to add the corpus to the search functions</td></tr>';
    }

    //radio
/*
    if($openAccess) {
        echo '<input data-url="'.$url.'" type="radio" id="openAccess" name="openAccess"><span>All users are allowed to view this Corpus</span></td></tr>';
    }
    else {
        echo '<input data-url="'.$url.'" type="radio" id="openAccess" name="openAccess"><span>Only allowed users can view this Corpus</span></td></tr>';
    }
*/
?>
    <tr class="userlist" id="userlist" data-url="<?php $url = Router::url(array('controller'=>'XMLObjects','action'=>'removeCorpusUser'),true); echo $url;?>"><th id="allowedUsersTitle">Allowed Users</th>
    <?php
    foreach($users as $user) {
            echo '<tr class="userlist"><td><span>'.$user.'</span>';
            echo $this->Html->image("delete.ico",array(
                "border" => "0",
                'width'=>'15px',
                'class' => 'clickable removeUser',
                'style'=>'float:right; margin-left:10px;',
                'title' => 'remove user',
                "alt" => "remove"
            ));
            echo '</td></tr>';
    }
    ?>
    <tr><td>New User: <input type="text"><button id="addUserButton" data-url="<?php $url = Router::url(array('controller'=>'XMLObjects','action'=>'addCorpusUser'),true); echo $url;?>" type="button">Add User</button>
    <span id="CorpusUserResponse"></span>
    </td></tr>

    <!--Allowed Groups for later release------>
    <!--<tr class="grouplist" id="grouplist" data-url="<?php #$url = Router::url(array('controller'=>'XMLObjects','action'=>'removeCorpusGroup'),true); echo $url;?>"><th>Allowed Groups</th>-->

    <?php
    /*
        foreach($groups as $group) {
            echo '<tr class="grouplist"><td><span>'.$group.'</span>';
            echo $this->Html->image("delete.ico",array(
                "border" => "0",
                'width'=>'15px',
                'class' => 'clickable removeGroup',
                'style'=>'float:right; margin-left:10px;',
                'title' => 'remove group',
                "alt" => "remove"
            ));
            echo '</td></tr>';
        }
    */
    ?>

    <!--<tr><td>New Group: <input type="text"><button id="addGroupButton" data-url="<?php #$url = Router::url(array('controller'=>'XMLObjects','action'=>'addCorpusGroup'),true); echo $url;?>" type="button">Add Group</button>-->
    <!--<span id="CorpusGroupResponse"></span>-->
<!--</td></tr>-->
</table>


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
	#$this->Js->codeBlock("write('hello')");jQuery("tr#format1").hide();
	#TODO: ausgabe formatieren, object zurückgeben wenn erfolgreich
	if($response != ''){
		echo '<br /><div style="text-decoration:underline;font-weight:bold;">Antwort des Aufrufs</div><br />';
		echo '<div style="border:1px solid gray;">';
		$response = preg_replace('/.+<body>/','',$response);
		$response = preg_replace('#</body>.+#','',$response);
		echo $response;
		echo '</div>';
	}
?>