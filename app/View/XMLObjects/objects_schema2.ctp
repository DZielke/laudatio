<?php
echo $this->Html->script('uploadscript',array('inline' => false));
echo $this->Html->script('jquery.treeTableVertical.js',array('inline' => false));
echo $this->Html->css('/js/css/jquery.treeTable.css', null, array('inline' => false));

$pids = preg_split('/:/', $pid);

$this->set('navbarArray',array(array('View','view'),array($pids[1])));
if(isset($noTEI) && $noTei ==true){
    echo 'No TEI-Header available.';
}
else {
    //$xml = simplexml_load_string(html_entity_decode($getObjectVersionXml)) or die("Error: Can not create object");

    $ann_link ='https://korpling.german.hu-berlin.de/annis3/#c='.$pid.'';
    echo "<div style=\"float:right;text-align:right;\">";
    echo $this->Html->image("annis_192.png",array(
        'width'=>'60px',
        "border" => "0",
        "alt" => "Link to ANNIS",
        "url" => $ann_link,
        "fullBase" => true
    ));
    echo "</div>";

    echo '<h3>'.$title.'</h3>';

    echo "<select id='versionDate' onchange='changeVersion(\"".urlencode($pid)."\")' name='dateObject'>";


    foreach($versions as $i => $vers) {
        $displayName =  substr($vers,strrpos($vers,'_')+1);
        $displayName =  substr($displayName,0,strrpos($displayName,':'));
        $displayName =  preg_replace("/T/"," ",$displayName);

        if ($vers == $thisVersion) {
            echo "<option value='".$vers."' selected>$displayName</option>";
        }
        else {
            echo "<option value='".$vers."'>$displayName</option>";
        }
    }

    echo "</select>";

    echo '<table id="tree">';
    echo '<tr><td>'.$title.', '.$authority.', '.$version.'';

    if ($extent !="" && $extent !="N/A"&& $extent !="?"){
        echo ', '.$extent.' '.$extent["type"];
    }

    echo '<tr><td>';
    $total_download_format = (int)count($download_format);
    echo "<b>Formats: </b> ";
    for($x=0;$x<$total_download_format;) {
        if(preg_match("/^[A-Z]/",$download_format[$x])) {
            $download = "http://141.20.4.72:8080/fedora/get/".$pid."/".strtoupper($download_format[$x])."";
            #$download = "depot1-7.cms.hu-berlin.de:8080/fedora/get/".$pid."/".strtoupper($download_format[$x])."";
        }
        else {
            $download = "http://141.20.4.72:8080/fedora/get/".$pid."/".$download_format[$x]."";
            #$download = "depot1-7.cms.hu-berlin.de:8080/fedora/get/".$pid."/".$download_format[$x]."";
        }
        if ($x!=0) {
            echo ", ";
        }
        echo $this->Html->link($download_format[$x], $download);
        $x++;
    }
    echo '</td></tr>';

    echo '</td></tr>';

    if (isset($handlePID)) {
        echo '<tr><td>';
        $handle = 'http://hdl.handle.net/11022/'.$handlePID;
        echo "<b>Persistent Identifier: </b> ".$this->Html->link($handle, $handle);
        echo '</td></tr>';
    }

    if (isset($homepage)) {
        echo '<tr><td><b>Project homepage:</b> <a href="'.$homepage.'">'.$homepage.'</a></td></tr>';
    }

    echo '<tr id="ex2-node-1" ><td><b title="Contains metadata for the object &lsquo;corpus&rsquo;">Corpus '.$title.'</b>';
    #&lsquo;corpus&rsquo;
    echo '<span class="helptooltip"><p>The section contains general information about the corpus itself. You get information about the corpus editors and annotators, the corpus project, the documents of the corpus and the applied annotations and formats.</p>';
    echo $this->Html->image("help.png",array(
        "border" => "0",
        'width'=>'20px',
        "alt" => "help",
    ));
    echo '</span></td></tr>';
    echo '<tr id="ex2-node-1-1" class="child-of-ex2-node-1"><td>Authorship</td></tr>';
    echo '<tr id="ex2-node-1-1-1" class="child-of-ex2-node-1-1"><td>Corpus Editor</td></tr>';
    if (isset($editor_surname_array)) {
        $total_editor_surname_array = (int)count($editor_surname_array);
        for($x=0;$x<$total_editor_surname_array;$x++) {
            echo '<tr id="ex2-node-1-1-1-'.$x.'" class="child-of-ex2-node-1-1-1">';
            echo '<td>'.$editor_forename_array[$x].' '.$editor_surname_array[$x].'</td>';
            echo ' ';

            echo '</tr>';

            if (isset($editor_institution_name_array[$x])) {
                echo '<tr id="ex2-node-1-1-1-1-'.$x.'" class="child-of-ex2-node-1-1-1-'.$x.'"><td>Affiliation: '.$editor_department_name_array[$x].', '.$editor_institution_name_array[$x].'</td></tr>';
            }
        }
    }

    echo '<tr id="ex2-node-1-1-2" class="child-of-ex2-node-1-1"><td>Corpus Annotator</td></tr>';
    if (isset($author_surname_array)) {
        $total_author_surname_array = (int)count($author_surname_array);
        for($y=0;$y<$total_author_surname_array;$y++) {
            if ($author_surname_array[$y] != 'NA') {
                echo '<tr id="ex2-node-1-1-2-'.$y.'" class="child-of-ex2-node-1-1-2">';
                echo '<td>'.$author_forename_array[$y].' '.$author_surname_array[$y].'</td>';
                echo ' ';

                echo '</tr>';

                if (isset($author_institution_name_array[$y])) {
                    echo '<tr id="ex2-node-1-1-1-2-'.$y.'" class="child-of-ex2-node-1-1-2-'.$y.'"><td>Affiliation: '.$author_department_name_array[$y].' '.$author_institution_name_array[$y].'</td></tr>';
                }
            }
            else {
                echo '<tr id="ex2-node-1-1-2-'.$y.'" class="child-of-ex2-node-1-1-2"><td>Not set</td>';
            }
        }
    }
    echo '<tr id="ex2-node-1-2" class="child-of-ex2-node-1"><td>Project</td></tr>';
    echo '<tr id="ex2-node-1-2-1" class="child-of-ex2-node-1-2"><td>Homepage '.($homepage != "NA" ? '<a href="'.$homepage.'">'.$homepage.'</a>' :"Not set").'</td></tr>';
    echo '<tr id="ex2-node-1-2-2" class="child-of-ex2-node-1-2"><td>Description</td></tr>';
    if (isset($description->p)) {
        echo '<tr id="ex2-node-1-2-2-1" class="child-of-ex2-node-1-2-2"><td>'.$description->p.'</td></tr>';
    }

    echo '<tr id="ex2-node-1-3" class="child-of-ex2-node-1"><td>Publication</td></tr>';
        echo '<tr id="ex2-node-1-3-1" class="child-of-ex2-node-1-3"><td>Authority: '.$authority.'</td></tr>';
        echo '<tr id="ex2-node-1-3-2" class="child-of-ex2-node-1-3"><td>Project Name: '.$idno.'</td></tr>';
        echo '<tr id="ex2-node-1-3-3" class="child-of-ex2-node-1-3"><td>Availability Status: '.$av_status['status'].'</td></tr>';
        echo '<tr id="ex2-node-1-3-4" class="child-of-ex2-node-1-3"><td>'.$project.'</td></tr>';
        echo '<tr id="ex2-node-1-3-5" class="child-of-ex2-node-1-3"><td>Authority: '.$authority.'</td></tr>';

        foreach ($date_array as $date) {
            echo '<tr id="ex2-node-1-3-6" class="child-of-ex2-node-1-3"><td>Corpus Release: '.($date!="" ? $date.', ':"").$date['when'].'</td></tr>';
        }

    if ($extent != "N/A") {
        echo '<tr id="ex2-node-1-4" class="child-of-ex2-node-1 collapsed"><td>Size: '.$extent.' '.$extent['type'].'</td></tr>';
    }


    echo '<tr id="ex2-node-1-5" class="child-of-ex2-node-1"><td>Documents</td></tr>';
    echo '<tr id="ex2-node-1-5-1" class="child-of-ex2-node-1-5"><td>';

    if (isset($items_array)) {
        $total_items_array = (int)count($items_array);
        $last = null;
        $sameCounter = 0;
        if($total_items_array > 0){
            echo '<ul>';
            for($u=0;$u<$total_items_array;$u++) {
                if(strcmp($items_array[$u],$last) == 0) {
                    $sameCounter++;
                    echo '<li rel="'.$sameCounter.'" class="link">'.$items_array[$u].'</li>';
                }
                else {
                    $sameCounter = 0;
                    echo '<li class="link">'.$items_array[$u].'</li>';
                }
                $last = $items_array[$u];
            }
            echo '</ul>';
        }
        echo '</td></tr>';
    }
    #echo '<tr id="ex2-node-1-5-2" class="child-of-ex2-node-1-5"><td>Language '.$language.'</td></tr>';

    foreach ($language_array as $language) {
        #echo '<tr id="ex2-node-1-5-2" class="child-of-ex2-node-1-5"><td>Language: '.($language!="" ? $language :" ").'</td></tr>';
        echo '<tr id="ex2-node-1-5-2" class="child-of-ex2-node-1-5">';
        if ($language['style'] == 'Language') {
            echo '<td>Language: '.$language;
        }
        if ($language['style'] == 'LanguageType') {
            echo '<td>Language Type: '.$language;
        }
        if ($language['style'] == 'LanguageArea') {
            echo '<td>Language Area: '.$language;
        }
        if ($language == '') {
            echo 'Not set';
        }
        elseif ($language != '' && $language['style'] =='') {
            echo '<td>Language: '.$language;
        }
        echo '</td></tr>';

        #.($language!="" ? $language :" ").'</td></tr>';
    }

    echo '<tr id="ex2-node-1-6" class="child-of-ex2-node-1"><td>Format</td></tr>';
        $total_namespace_xml_array = (int)count($namespace_xml_array);
        for($r=0;$r<$total_namespace_xml_array;$r++) {
            foreach ($namespace_xml_array[$r]as $key => $arr) {
                $arr_key = array_keys($arr);

                foreach(array_keys($arr) as $key) {
                    if ($arr[$key]->application['ident'] != '') {
                        echo '<tr id="ex2-node-1-6-'.$key.'" class="child-of-ex2-node-1-6">';
                        echo '<td>'.$arr[$key]->application['ident'].'</td>';
                            echo '<tr id="ex2-node-1-6-'.$key.'-1" class="child-of-ex2-node-1-6-'.$key.'"><td>Version: '.$arr[$key]->application['version'].'</td></tr>';
                    }

                    if ($arr[$key]->segmentation->p != '') {
                        echo '<tr id="ex2-node-1-6-'.$key.'-2" class="child-of-ex2-node-1-6-'.$key.'"><td>Segmentation: '.$arr[$key]->segmentation->p.'</td></tr>';
                    }

                    if ($arr[$key]->normalization->p != '') {
                        echo '<tr id="ex2-node-1-6-'.$key.'-3" class="child-of-ex2-node-1-6-'.$key.'"><td>Normalization: '.$arr[$key]->normalization->p.'</td></tr>';
                    }

                    if ($arr[$key]->namespace != '') {
                        echo '<tr id="ex2-node-1-6-'.$key.'-4" class="child-of-ex2-node-1-6-'.$key.'"><td>Annotation: <ul>';
                    //var_dump($arr);
                        foreach ($arr as $Desc_namespace) {
                            $count_arr=sizeof($Desc_namespace->namespace);
                            for ($w=0;$w<=sizeof($Desc_namespace->namespace);$w++) {
                                if ($Desc_namespace->namespace[$w]['name'] != '') {
                                    echo '<li  class="link annotation" rel="'.$Desc_namespace->namespace[$w]['rend'].'">'.$Desc_namespace->namespace[$w]['name'].'</li>';

                                   /* if ($w == $count_arr-1) {
                                        echo '';
                                    }
                                    else {
                                        echo ', ';
                                    }*/

                                }
                            }
                        }
                        echo '</ul>';
                        echo '</td>';
                    }

                }
                echo '</tr>';
            }
        }

    //TODO: sektion durch folgendes ersetzen
    $url = Router::url(array('controller'=>'Views','action'=>'objects_schema2_documents',$pid,$thisVersion),true);
    echo '<tr id="ex2-node-2" data-url="'.$url.'"><td><b title="Contains classical metadata for the object &lsquo;document&rsquo;">Documents</b>';

    echo '<span class="helptooltip"><p>The section contains bibliographic information about each document in the corpus, e.g. author, date and publication place. Each document has a list of annotation.</p>';
    echo $this->Html->image("help.png",array(
        "border" => "0",
        'width'=>'20px',
        "alt" => "help",
    ));
    echo '</span></td></tr>';



    echo '<tr id="ex2-node-3"><td><b title="Contains information about the applied annotations of the corpus">Annotation</b>';
    echo '<span class="helptooltip"><p>The section contains the whole tagset for each annotation with descriptions.</p>';
    echo $this->Html->image("help.png",array(
        "border" => "0",
        'width'=>'20px',
        "alt" => "help",
    ));
    echo '</span></td></tr>';

    if (isset($namespace_xml_rend_array)) {
        if (in_array('Transcription', $namespace_xml_rend_array)) {
            echo '<tr id="ex2-node-3-t" class="child-of-ex2-node-3"><td>Transcription</td></tr>';
            $namespace_xml_trans_array = array_values(array_unique($namespace_xml_trans_array));

            for($b=0;$b<sizeof($namespace_xml_trans_array);$b++) {
                echo '<tr id="ex2-node-3-t-'.$b.'" class="child-of-ex2-node-3-t"><td>'.$namespace_xml_trans_array[$b].'</td></tr>';
                foreach ($namespace_trans_array[$b] as $namespace_trans_array_value) {
                    echo '<tr id="ex2-node-3-t-'.$b.'-1" class="child-of-ex2-node-3-t-'.$b.'"><td><b>'.($namespace_trans_array_value['gi'] != 'NA' ? $namespace_trans_array_value['gi'] : "")."</b> ".$namespace_trans_array[$b].'</td></tr>';
                }
            }
        }

        if (in_array('Lexical', $namespace_xml_rend_array)) {
            echo '<tr id="ex2-node-3-lex" class="child-of-ex2-node-3"><td>Lexical</td></tr>';
            $namespace_xml_lexical_array = array_values(array_unique($namespace_xml_lexical_array));

            for($b=0;$b<sizeof($namespace_xml_lexical_array);$b++) {
                echo '<tr id="ex2-node-3-lex-'.$b.'" class="child-of-ex2-node-3-lex"><td>'.$namespace_xml_lexical_array[$b].'</td></tr>';
                foreach ($namespace_lexical_array[$b] as $namespace_lexical_array_value) {
                    echo '<tr id="ex2-node-3-lex-'.$b.'-1" class="child-of-ex2-node-3-lex-'.$b.'"><td><b>'.$namespace_lexical_array_value['gi']."</b> ".$namespace_lexical_array_value.'</td></tr>';
                }
            }
        }

        if (in_array('Graphical', $namespace_xml_rend_array)) {
            echo '<tr id="ex2-node-3-g" class="child-of-ex2-node-3"><td>Graphical</td></tr>';
            $namespace_xml_graphical_array = array_values(array_unique($namespace_xml_graphical_array));

            for($b=0;$b<sizeof($namespace_xml_graphical_array);$b++) {
                echo '<tr id="ex2-node-3-g-'.$b.'" class="child-of-ex2-node-3-g"><td>'.$namespace_xml_graphical_array[$b].'</td></tr>';
                foreach ($namespace_graphical_array[$b] as $namespace_graphical_array_value) {
                    echo '<tr id="ex2-node-3-g-'.$b.'-1" class="child-of-ex2-node-3-g-'.$b.'"><td><b>'.$namespace_graphical_array_value['gi']."</b> ".$namespace_graphical_array_value.'</td></tr>';
                }
            }
        }

        if (in_array('Syntactic', $namespace_xml_rend_array)) {
            echo '<tr id="ex2-node-3-s" class="child-of-ex2-node-3"><td>Syntactic</td></tr>';
            $namespace_xml_syntactic_array = array_values(array_unique($namespace_xml_syntactic_array));

            for($b=0;$b<sizeof($namespace_xml_syntactic_array);$b++) {
                echo '<tr id="ex2-node-3-s-'.$b.'" class="child-of-ex2-node-3-s"><td>'.$namespace_xml_syntactic_array[$b].'</td></tr>';
                foreach ($namespace_syntactic_array[$b] as $namespace_syntactic_array_value) {
                    echo '<tr id="ex2-node-3-s-'.$b.'-1" class="child-of-ex2-node-3-s-'.$b.'"><td><b>'.$namespace_syntactic_array_value['gi']."</b> ".$namespace_syntactic_array_value.'</td></tr>';
                }
            }
        }

        if (in_array('Other', $namespace_xml_rend_array)) {
            echo '<tr id="ex2-node-3-o" class="child-of-ex2-node-3"><td>Other</td></tr>';
            $namespace_xml_other_array = array_values(array_unique($namespace_xml_other_array));

            for($u=0;$u<sizeof($namespace_xml_other_array);$u++) {
                echo '<tr id="ex2-node-3-o-'.$u.'" class="child-of-ex2-node-3-o"><td>'.$namespace_xml_other_array[$u].'</td></tr>';
                foreach ($namespace_other_array[$u] as $namespace_other_array_value) {
                    echo '<tr id="ex2-node-3-o-'.$u.'-1" class="child-of-ex2-node-3-o-'.$u.'"><td><b>'.$namespace_other_array_value['gi']."</b> ".$namespace_other_array_value.'</td></tr>';
                }
            }
        }
    }
    $url = Router::url(array('controller'=>'Views','action'=>'objects_schema2_preparation',$pid,$thisVersion),true);
    //$url= Router::url('/View/XMLObjects/objects_schema2_preparations.ctp',true);
    echo '<tr id="ex2-node-4" data-url="'.$url.'"><td><b title="Contains information about every preparation step of every applied annotation">PreparationStep</b>';
    echo '<span class="helptooltip"><p>The section contains information about each step of preparation for each annotation which is listed in the section &lsquo;annotation&rsquo;. Here, the tools used for data preparation, checking methods and information about the overall corpus architecture are listed.
</p>';
    echo $this->Html->image("help.png",array(
        "border" => "0",
        'width'=>'20px',
        "alt" => "help",
    ));
    echo '</span></td></tr>';

    echo '</table>';
}

if (isset($reveal)) {
    //var_dump($reveal);
    if(isset($reveal_number)){
        echo '<span id="revealDocument" style="display:none;" rel="'.$reveal.'" ref="'.$reveal_number.'"></span>';
    }else{
        echo '<span id="revealDocument" style="display:none;" rel="'.$reveal.'"></span>';
    }
    /*echo '<script type="text/javascript">
                $(document).ready(function(){
                try {
                    $(\'td[rel="\'+decodeURIComponent("'.$reveal.'")+\'"]\').parent().reveal();
                    $(\'td[rel="\'+decodeURIComponent("'.$reveal.'")+\'"]\').parent().expand();
                }catch(error) {  
                }
            });</script>';*/
}else{
    echo '<span id="revealDocument" style="display:none;"></span>';
}
?>