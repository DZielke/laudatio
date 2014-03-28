<?php #echo $this->Html->css('cake.my.changed');
$this->set('navbarArray',array(array('Modify','modify'),array('Modify Object','modifyObject/'.urlencode($id)),array('Update Datastream')));
if(!isset($response)){
    $response = '';
}
?>
<h3>Datastream</h3>

<form id="ingestform" method="POST"  enctype="multipart/form-data" xmlns="http://www.w3.org/1999/html"
      xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">

    <table id="uploadform" border="0" cellpadding="0" cellspacing="4">
        <tr>
            <td>Datastream</td>
        </tr>
        <tr>
            <!-- <input type="hidden" name="MAX_FILE_SIZE" value="300000" /> -->
            <td>Label: <input id="label" name="label" value="<?php echo $dslabel?>" type="text" size="15" maxlength="30" style="background-color:lightgrey;" readonly="readonly"></td>
            <td>File: <input  name="uploadfile" type="file"></td>
        </tr>
    </table>
    <br>
    <input type="submit" value="Update Datastream!">
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