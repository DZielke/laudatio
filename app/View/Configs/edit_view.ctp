<?php $this->set('navbarArray',array(array('Configuration','index','Configs'),array('Schemes','uploadScheme','Configs'),array('View')));
echo $this->Html->script('config',array('inline' => false));
echo $this->Html->script('ace/ace',array('inline' => false));
$this->Html->css('ace', null, array('inline' => false));


echo $this->Html->image("save.png",array(
'id' => 'saveButton',
'data-url' => Router::url(array('controller'=>'Configs','action'=>'saveView'),true),
"border" => "0",
'width'=>'15px',
'class' => 'clickable button',
'style'=>'margin:5px; float:left;',
'onclick'=>'saveMapping("'.$scheme.'")',
'title' => 'save view (Ctrl+S)',
"alt" => "save"
));

echo $this->Html->image("trash.ico",array(
    'id' => 'trashButton',
    'data-url'=>Router::url(array('controller'=>'Configs','action'=>'deleteView'),true),
    "border" => "0",
    'width'=>'15px',
    'class' => 'clickable button',
    'style'=>'margin:5px; float:left;',
    'onclick'=>'deleteMapping("'.$scheme.'")',
    'title' => 'delete mapping',
    "alt" => "delete"
));

echo $this->Html->image("delete.ico",array(
    'id' => 'deleteButton',
    "border" => "0",
    'width'=>'15px',
    'class' => 'clickable button',
    'style'=>'margin:5px; float:left;',
    'onclick'=>'deleteMappingVersion("'.$scheme.'")',
    'title' => 'delete mapping version',
    "alt" => "delete version"
));

echo $this->Html->image("questionMark.ico",array(
    'id' => 'helpButton',
    'data-url'=>Router::url(array('controller'=>'Configs','action'=>'help'),true),
    "border" => "0",
    'width'=>'15px',
    'class' => 'clickable button',
    'style'=>'margin:5px; float:left;',
    'onclick'=>'help()',
    'title' => 'help',
    "alt" => "help"
));

echo '<div style="float:left;" data-url="'.Router::url(array('controller'=>'Configs','action'=>'saveView'),true).'" id="msg"></div>';




echo '<button  data-url="'.Router::url(array('controller'=>'XMLObjects','action'=>'corpus'),true).'" style="float:right;clear:none;margin-top:6px;" type="button" onclick="preview(this)" >preview</button>';
echo '<select id="corpus" style="font-size:100%;margin-top:6px;clear:none;float:right;">';
foreach($corpora as $corpus){
    echo '<option value="'.urlencode($corpus).'">'.$corpus.'</option>';
}
echo '</select>';

//Version selection
echo '<button  data-url="'.Router::url(array('controller'=>'XMLObjects','action'=>'loadView'),true).'" style="float:right;clear:none;margin-top:6px;margin-right:20px;" type="button" onclick="loadMappingSelectedVersion(\''.$scheme.'\')" >load</button>';

if(!isset($versions) || count($versions) < 2){
    echo '<select id="version" style="font-size:100%;margin-top:6px;clear:none;float:right;" disabled>';
}else{
    echo '<select id="version" style="font-size:100%;margin-top:6px;clear:none;float:right;">';
}
if(isset($versions)){
    $count = 0;
    foreach($versions as $version){
        if($count++ == 0)
            echo '<option value="'.urlencode($version).'">* '.$version.'</option>';
        else
            echo '<option value="'.urlencode($version).'">'.$version.'</option>';
    }
}else{
     echo '<option value="null">no versions</option>';
}
echo '</select>';
echo '<label  style="float:right;clear:none;margin-top:6px;">choose version: </label>';



echo '<div id="editor" data-url="'.Router::url(array('controller'=>'Configs','action'=>'loadView'),true).'" style="position:relative;height:600px;clear:both"></div>';
?>

<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/clouds");
    editor.getSession().setMode("ace/mode/json");
    editor.getSession().setUseWrapMode(false);
    editor.setHighlightActiveLine(true);
    editor.setShowPrintMargin(false);
    editor.commands.addCommand({
        name: 'save',
        bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
        exec: function(){
            $('#saveButton').click();
        },
        readOnly: false // false if this command should not apply in readOnly mode
    });
    var url = $("#editor").attr('data-url');
    var xhr = new XMLHttpRequest();
    xhr.open('post', url, true);
    xhr.onreadystatechange=function(){
        if (xhr.readyState==4){
            if(xhr.status==200 && xhr.responseText != "error"){
                editor.setValue(xhr.responseText,-1);
            }else{
                toast("connection error",'#msg','#ff6e6e');
            }
        }
    };
    var formData = new FormData();
    formData.append('scheme','<?php echo $scheme; ?>');
    xhr.send(formData);
</script>