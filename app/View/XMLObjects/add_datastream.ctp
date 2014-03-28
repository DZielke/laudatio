<?php #echo $this->Html->css('cake.my.changed');
$this->set('navbarArray',array(array('Modify','modify'),array('Modify Object','modifyObject/'.urlencode($id)),array('Add Datastream')));
echo $this->Html->script('uploadscript',array('inline' => false));

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
                              
<h3>Properties</h3>
<table border="0" cellpadding="0" cellspacing="4">
<tr>	<td>ID:</td>
		<?php echo "<td>$id</td>";?>
		
</tr>
<tr>	<td>Label:</td>
	<?php echo "<td>$label</td>";?>
</tr>
</table>
</br>

<h3>Datastreams</h3>

<form method="POST" enctype="multipart/form-data">

<table id="uploadform" border="0" cellpadding="0" cellspacing="4">
<tr></tr>
<tr>
  <td>Datastream-ID:</td>
  <td><input name="formatname[]" type="text" size="15" maxlength="30"></td>
  <td> <input name="userfile[]" type="file"></td>
  <td><button type="button" onclick="deleteFormat($(this))">delete</button></td>
</tr>
<tr>
	<td><button type="button" onclick="addFormat()">more</button></td>
</tr>
</table>
<br>
<input type="submit" value="Upload Datastreams!">
</form>


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