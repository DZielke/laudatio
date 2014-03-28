<?php $this->set('navbarArray',array(array('Configuration','index','Configs'),array('Schemes')));
echo $this->Html->script('config',array('inline' => false));
/*if($schemes != ''){
    $schemeCount = count($schemes)+1;
}else{
    $schemeCount = 1;
} */?>
<h1>upload new scheme</h1>
<form method="POST" enctype="multipart/form-data">
<table>
    <tr><td>Scheme name</td><td><input name="schemeName" type="text" size="15" maxlength="15" value="<?php echo('Schema'.$newSchemeNumber);?>"></td></tr>
    <tr><td>Corpus Scheme</td><td><input name="corpusScheme" type="file"></td></tr>
    <tr><td>Document Scheme</td><td><input name="documentScheme" type="file"></td></tr>
    <tr><td>Preparation Scheme</td><td><input name="prepScheme" type="file"></td></tr>
    <tr><td><input type="submit"  value="Upload Schemes"></td></tr>
</table>
</form>
<h1>Schemes</h1>
<?php
    if($schemes == ''){
        echo 'No previous schemes.';
    }else{
            foreach($schemes as $i => $scheme ){
                //echo "<form class='button' method='POST'><input type='hidden' name='delete' value='".$scheme."'>";
                echo "<table><tr>".$scheme; //<input type='submit' class='delete' value=''>
                if($activeScheme == $scheme){
                    echo $this->Html->image("fav.ico",array(
                        "border" => "0",
                        'width'=>'15px',
                        "alt" => "active",
                        'title' => 'default scheme'
                     ));
                }else{
                    echo $this->Html->image("delete.ico",array(
                        "border" => "0",
                        'width'=>'15px',
                        'class' => 'clickable',
                        'style'=>'float:right; margin-left:10px;',
                        'onclick'=>'deleteScheme("'.$scheme.'")',
                        'title' => 'delete scheme',
                        "alt" => "delete"
                    ));
                    echo "<a class='clickable' style='float:right;' onclick='activateScheme(\"".$scheme."\")'>set to default</a>";

                }
                echo "<a class='clickable' style='float:right;margin-right:5px;' href='".Router::url(array('controller'=>'Configs','action'=>'editView',$scheme),true)."'>edit view</a> ";
                echo "<a class='clickable' style='float:right;margin-right:5px;' href='".Router::url(array('controller'=>'Configs','action'=>'editIndexMapping',$scheme),true)."'>edit index mapping</a> ";
                echo "</tr></table>"; //</form>
            }

    }
