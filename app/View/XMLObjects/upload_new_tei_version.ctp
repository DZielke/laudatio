<?php #echo $this->Html->css('cake.my.changed');
$this->set('navbarArray',array(array('Modify','modify'),array('Modify Object','modifyObject/'.urlencode($id)),array('Update TEI-header')));
echo $this->Html->script('teiValidation',array('inline' => false));
echo $this->Html->css('validate',null,array('inline' => false));
if(!isset($response)){
    $response = '';
}
?>
<h3>TEI-header</h3>

<form id="ingestform" method="POST" onsubmit="return validateForm()" enctype="multipart/form-data" xmlns="http://www.w3.org/1999/html"
      xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">

    <table id="uploadform" border="0" cellpadding="0" cellspacing="4">
        <tr>
            <td>TEI-Header (Metadata):</td>
        </tr>
        <tr>
            <!-- <input type="hidden" name="MAX_FILE_SIZE" value="300000" /> -->
            <td>Label: <input id="label" name="label" value="<?php echo $dslabel?>" type="text" size="15" maxlength="30" style="background-color:lightgrey;" readonly="readonly"></td>
            <td>File: <input id="teiupload" name="teifile" type="file" data-url="<?php $url = Router::url(array('controller'=>'XMLObjects','action'=>'ajaxUpload'),true); echo $url;?>">
                <div style="display:none;" id="uploadResponse">
                    <span id="uploadPercentage"></span>
                    <span id="corpusResponse"> validating Corpus<?php echo $this->Html->image('ajax-loader.gif', array('id' => 'ajax-loader'));?></span><br/>
                    <span id="documentResponse"> validating Documents<?php echo $this->Html->image('ajax-loader.gif', array('id' => 'ajax-loader'));?></span><br/>
                    <span id="preparationResponse"> validating Preparations<?php echo $this->Html->image('ajax-loader.gif', array('id' => 'ajax-loader'));?></span>
                </div></td>
        </tr>
    </table>
    <br>
    <input type="submit" value="Update TEI-header!">
</form>


<?php
if($response != ''){
    echo '<br /><div style="text-decoration:underline;font-weight:bold;">Antwort des Aufrufs</div><br />';
    echo '<div style="border:1px solid gray;">';
    $response = preg_replace('/.+<body>/','',$response);
    $response = preg_replace('#</body>.+#','',$response);
    echo $response;
    echo '</div>';
}
?>