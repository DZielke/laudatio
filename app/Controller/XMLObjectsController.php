<?php
App::uses('AppController', 'Controller', 'Utility', 'Xml');

class XMLObjectsController extends AppController {

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(array('index','view','addCorpusGroup','removeCorpusGroup','addCorpusUser','removeCorpusUser','login','impressum','contact','documentation','objects','corpus','corpus_load','downloadDatastream','plugin' => 'search'));#'index','view'
    }

    public function index() {}

    public function blog() {}

    public function contact() {}

    public function documentation() {}

    public function view() {
        $corpora = array();
        $xml_answer = $this->XMLObject->showAllObjectsXML();
        if(!$xml_answer){
            $this->set('corpora', $corpora);
            return;
        }
        $xml = simplexml_load_string($xml_answer) or die("Error: Can not create object");
        for($i=0;$i<sizeof($xml->resultList->objectFields);$i++) {
            if (!XMLObjectsController::ignoreFedoraObject($xml->resultList->objectFields[$i]->pid)) {
                if($this->viewPermission($xml->resultList->objectFields[$i]->pid[0]))
                    array_push($corpora,array("pid" => $xml->resultList->objectFields[$i]->pid[0], "label" => $xml->resultList->objectFields[$i]->label[0]));
            }
        }
        $this->set('corpora', $corpora);
    }

    public function modify(){
        $corpora = array();
        $xml_answer = $this->XMLObject->showAllObjectsXML();
        if(!$xml_answer){
            $this->set('corpora', $corpora);
            return;
        }
        $xml = simplexml_load_string($xml_answer) or die("Error: Can not create object");
        for($i=0;$i<sizeof($xml->resultList->objectFields);$i++) {
             if (!XMLObjectsController::ignoreFedoraObject($xml->resultList->objectFields[$i]->pid)) {
                if($this->modifyPermission($xml->resultList->objectFields[$i]->pid[0]))
                    array_push($corpora,array("pid" => $xml->resultList->objectFields[$i]->pid[0], "label" => $xml->resultList->objectFields[$i]->label[0]));
            }
        }
        $this->set('corpora', $corpora);
    }

    public function delete($pid = null, $label = null) {
        if($pid){
            if(!$this->modifyPermission($pid)){
                $this->Session->setFlash(__('No permission to delete Corpus.'));
            }else{
                $result_array = $this->XMLObject->deleteFedora($pid);
                $this->XMLObject->deleteFromIndex($pid);
                if($result_array['http_code'] == '200'){
                    $this->XMLObject->deleteCorpus($pid);
                    $this->Session->setFlash('Corpus '.$pid.' purged.', 'default', array('class' => 'success'));
                }else{
                    if($result_array['http_code'] == '404'){
                        $this->Session->setFlash('Corpus '.$pid.' not found.');
                    }
                    $this->set('response', $result_array['response']);
                }
            }
        }

        $corpora = array();
        $xml_answer = $this->XMLObject->showAllObjectsXML();
        if(!$xml_answer){
            $this->set('corpora', $corpora);
            return;
        }
        $xml = simplexml_load_string($xml_answer) or die("Error: Can not create xml object");

        for($i=0;$i<sizeof($xml->resultList->objectFields);$i++) {
            if (!XMLObjectsController::ignoreFedoraObject($xml->resultList->objectFields[$i]->pid)) {
                if($this->modifyPermission($xml->resultList->objectFields[$i]->pid[0]))
                    array_push($corpora,array("pid" => $xml->resultList->objectFields[$i]->pid[0], "label" => $xml->resultList->objectFields[$i]->label[0]));
            }
        }
        $this->set('corpora', $corpora);
    }

    private function ignoreFedoraObject($pid){
        if ((!(strstr($pid, 'fedora-system:'))) && (!(strstr($pid, 'ignore:'))) && (!(strstr($pid, 'scheme:'))) && (!(strstr($pid, 'test:'))) && (!(strstr($pid, 'islandora:')))) {
            return false;
        }else{
            return true;
        }
    }

    public function corpus($pid = null,$version = null){
        if($pid === null){
            $this->Session->setFlash(__("Select a corpus."));
            $this->redirect(array('action' => 'view'));
        }
        if(!$this->viewPermission($pid)){
            $this->Session->setFlash(__('No permission to view Corpus.'));
            $this->redirect(array('action' => 'view'));
        }
        $preview = false;
        if(isset($_POST['json'])){
            $json = $_POST['json'];
            Cache::write("preview".$this->Session->read('Auth.User.id'),$json, 'short');
            $preview = true;
        }
        elseif ($this->Session->check('preview')) {
            $preview = $this->Session->read('preview');
            $this->Session->delete('preview');
        }
        $revealDocument = null;
        $revealDocumentNumber = null;
        if (isset($_POST['reveal'])) {
            $revealDocument = $_POST['reveal'];
        }
        elseif ($this->Session->check('last_reveal')) {
            $revealDocument = $this->Session->read('last_reveal');
            $this->Session->delete('last_reveal');
        }
        if (isset($_POST['reveal_number'])) {
            $revealDocumentNumber = $_POST['reveal_number'];
        }
        elseif ($this->Session->check('last_reveal_number')) {
            $revealDocumentNumber = $this->Session->read('last_reveal_number');
            $this->Session->delete('last_reveal_number');
        }

        $count = substr_count($version,'_');
        $date = null;
        $scheme = null;
        if($count === 3){
            $date = substr($version,strrpos($version,'_')+1);
            $version = substr($version,0,strrpos($version,'_'));
            $scheme = substr($version,strrpos($version,'_')+1);
        }elseif($count === 2){
            $scheme = substr($version,strrpos($version,'_')+1);
        }

        if($scheme !== null && !$preview && !$this->XMLObject->isFedoraDatastream("scheme:$scheme","viewMapping")){
            $this->Session->setFlash(__('Error: No view mapping configured. Please contact the administrator.'));
            $this->redirect(array('action' => 'view'));
        }

        if($version != null){
            if(!$this->XMLObject->isTeiVersion($pid,$version)){
                $this->Session->setFlash(__("Selected corpus version '$version' not available."));
                $version = null;
            }elseif($date === null || !$this->XMLObject->isTeiDate($pid,$version,$date)){
                if($date !== null)
                    $this->Session->setFlash(__("Selected corpus date '$date' not available."));
                $date = $this->XMLObject->getFirstTeiDate($pid,$version);
                if($revealDocument !== null){
                    if($this->Session->check('last_reveal'))
                        $this->Session->delete('last_reveal');
                    $this->Session->write('last_reveal', $revealDocument);
                }
                if($revealDocumentNumber !== null){
                    if($this->Session->check('last_reveal_number'))
                        $this->Session->delete('last_reveal_number');
                    $this->Session->write('last_reveal_number', $revealDocumentNumber);
                }
                $this->Session->write('preview', $preview);
                $this->redirect(array('controller' => 'XMLObjects','action'=>'corpus',$pid,$version.'_'.$date));
            }
        }else{
        //set url to first version
            if($revealDocument !== null){
                if($this->Session->check('last_reveal'))
                    $this->Session->delete('last_reveal');
                $this->Session->write('last_reveal', $revealDocument);
            }
            if($revealDocumentNumber !== null){
                if($this->Session->check('last_reveal_number'))
                    $this->Session->delete('last_reveal_number');
                $this->Session->write('last_reveal_number', $revealDocumentNumber);
            }
            $version = $this->XMLObject->getFirstTeiVersion($pid);
            if(!$version){
                $this->Session->setFlash(__('Internal Corpus Version Error. Please contact the administrator.'));
                $this->redirect(array('action' => 'view'));
            }
            $this->Session->write('preview', $preview);
            $this->redirect(array('controller' => 'XMLObjects','action'=>'corpus',$pid,$version));
        }
        //reveal Document if set by search link
        if($revealDocument !== null){
            $this->set('reveal',$revealDocument);
        }
        if($revealDocumentNumber !== null){
            $this->set('reveal_number',$revealDocumentNumber);
        }

        $result_array = $this->XMLObject->getObjectTEIXmlVersion($pid,$version,$date);

        #Error handling
        if($result_array['http_code'] != '200'){
            if($result_array['http_code'] == '401'){
                $this->Session->setFlash(__('Access denied, Object state may be inactive'));
            }else{
                if($result_array['http_code'] == '404'){
                    $msgdate = '';
                    if($version) $msgdate = ' Version: '.$version;
                    $this->Session->setFlash('No TEI_Header available for object '. urldecode($pid) .$msgdate);
                }else{
                    $this->Session->setFlash('Error Code:'.$result_array['http_code']);
                }
            }
            $this->redirect($this->referer());
        }
        #set view variables
        $xmlObject = Xml::build($result_array['response']);
        if (!isset($xmlObject->teiHeader)) {
            $this->Session->setFlash('No TEI-Header available for corpus '.$pid);
            $this->set('pid', $pid);
            $this->set('title', $xmlObject->objLabel);
            $this->set('editor', $xmlObject->objOwnerId);
            $this->set('extent', '');
            $this->set('publDate', '');
            $this->set('status', $xmlObject->objState);
        }else{
            $this->set('preview',$preview);
            $versions = $this->XMLObject->getTeiVersions($pid);
            $this->set('versions',$versions);
            $this->set('thisVersion',$version.'_'.$date);
            $this->set('handlePID',$this->XMLObject->getHandlePID($pid,$version.'_'.$date));
            $this->set('license',$this->XMLObject->getLicense($pid));
            $this->set('pid', $pid);
            $this->set('title', $xmlObject->teiHeader->fileDesc->titleStmt->title);
            $this->set('authority', $xmlObject->teiHeader->fileDesc->publicationStmt->authority);
            $this->set('version', $xmlObject->teiHeader->revisionDesc->change["n"]);
            $this->set('extent', $xmlObject->teiHeader->fileDesc->extent);

            $editor_forename_array = array();
            $editor_surname_array = array();
            $download_format = array();
            $date_array = array();
            foreach ($xmlObject->teiHeader->fileDesc->publicationStmt->date as $date) {
                array_push($date_array, $date);
            }
            $this->set('date_array', $date_array);

            for($x=0;$x<sizeof($xmlObject->teiHeader->fileDesc->titleStmt->editor);$x++) {
                if (isset($xmlObject->teiHeader->fileDesc->titleStmt->editor[$x]->persName->forename)) {
                    foreach ($xmlObject->teiHeader->fileDesc->titleStmt->editor[$x]->persName->forename as $editor_forename) {
                        array_push($editor_forename_array, $editor_forename);
                    }
                }
                if (isset($xmlObject->teiHeader->fileDesc->titleStmt->editor[$x]->persName->surname)) {
                    foreach ($xmlObject->teiHeader->fileDesc->titleStmt->editor[$x]->persName->surname as $editor_surname) {
                        array_push($editor_surname_array, $editor_surname);
                    }
                }
            }
            for($i=0;$i<sizeof($xmlObject->teiHeader->encodingDesc);$i++) {
                if ($xmlObject->teiHeader->encodingDesc[$i]) {
                    foreach ($xmlObject->teiHeader->encodingDesc[$i]->appInfo->application->attributes() as $attrib => $format) {
                        if ($attrib == 'ident') {
                            array_push($download_format, $format);
                            $this->set('download_format', $download_format);
                        }
                    }
                }
            }
            $this->set('editor_forename_array', $editor_forename_array);
            $this->set('editor_surname_array', $editor_surname_array);

            if (isset($xmlObject->teiHeader->encodingDesc->projectDesc->p->ref['target'])) {
                $this->set('homepage', $xmlObject->teiHeader->encodingDesc->projectDesc->p->ref['target']);
            }
        }
    }

    /**
     * Method contacted via ajax to load requested parts of the view. Uses the view mapping to get the respective values from the corpus tei-header.
     * @return string
     */
    public function corpus_load(){
        $this->autoRender=false;
        if(!isset($_POST['node']) || !isset($_POST['pid']) || !isset($_POST['version'])){
            echo 'TRANSMISSION_ERROR';
            return;
        }
        $node = $_POST['node'];
        $version = $_POST['version'];
        $pid = $_POST['pid'];
        $padding = $_POST['padding'];
        if(!$this->viewPermission($pid)){
            echo 'PERMISSION_ERROR';
        }
        $date = substr($version,strrpos($version,'_')+1);
        $version = substr($version,0,strrpos($version,'_'));
        $scheme = substr($version,strrpos($version,'_')+1);

        if($_POST['preview']=='true'){
            $view_conf = Cache::read("preview".$this->Session->read('Auth.User.id'), 'short');
            if($view_conf == null)
                return 'preview expired, please reopen preview from configuration';
            $json = json_decode($view_conf,true);
            if($json == null)
                return 'Syntax error in configuration file';
        }else{
            //use cache to load teiHeader and view.json to avoid frequent reads from disk
            $json = null;//Cache::read($scheme."_view", 'short');
            if(!$json){
                $view_conf = $this->XMLObject->getViewMapping($scheme);
                if($view_conf === null)
                    return 'Error: No view configuration for this scheme available !';
                $json = json_decode($view_conf,true);
    //            Cache::write($scheme."_view",$json, 'short');
            }
            if($json == null)
                return 'Syntax error in configuration file';
        }

        $corpus = null; //Cache::read(urlencode($pid.$version), 'short');
        if(!$corpus){
            $result_array = $this->XMLObject->getObjectTEIXmlVersion($pid,$version,$date);
            $corpus = $result_array['response'];
//            Cache::write(urlencode($pid.$version),$corpus, 'short'); //TODO: handle error if cache full or something
        }
        $xmlObject = Xml::build($corpus);
        /******************************************************
            use node id to find clicked json node in view mapping
        ******************************************************/
        $nodes = explode("-",$node);
        $first = array_shift($nodes);
        $first .= "-".array_shift($nodes);
        $first .= "-".array_shift($nodes);
        $json = $json[$first];
        //$outputRows = array();
        $array_route = array();
        while(count($nodes) > 0){
            $index = array_shift($nodes);
            if(isset($json[$index])){
                $json = $json[$index];
            }
            else if(isset($json["-"])){
                if(isset($json["-"]["from"])){
                    array_push($array_route,($index-$json["-"]["from"]));
                }else{
                    array_push($array_route,$index);
                }
                $json = $json["-"];
            }else{
                return "PATH_ERROR";
            }
        }
        /******************************************************
                get content form tei header
         ******************************************************/
        $append = "";
        $array_index = 0;
        foreach ($json as $key => $child){

            /*** get content for single row ***/
            if($key != "value"){
                if($key != "-" && is_numeric($key)){
                    if(isset($child['value'])){
                        $value = $this->getNodeFromTei($child,$array_route,$xmlObject);
                    }else{
                        $value = "not configured";
                    }
                    if($value === null)
                        continue;
                    //add offset for arrays
                    $array_index++;

                    //append treetable row to result
                    $append .= "<tr id=\"".$node."-".$key."\" class=\"child-of-".$node;
                    //check if is parent of nodes
                    if(isset($json[$key]["1"]) || isset($json[$key]["-"])){
                        $append .= " parent load initialized collapsed\">";
                        $append .= "<td style=\"padding-left:".$padding."px;\"><a class=\"expander\" style=\"margin-left: -19px; padding-left: 19px\" title=\"Expand\" href=\"#\"></a>";
                        $append .= $value;
                    }else{
                        $append .= "\"><td style=\"padding-left:".$padding."px;\">$value";
                    }
                    //append tooltip if configured
                    if(isset($child['tooltip'])){
                        $view = new View($this);
                        $html = $view->loadHelper('Html');
                        $append .= "<span class=\"helptooltip\"><p>".$child['tooltip']."</p>".$html->image("help.png",array(
                            "border" => "0",
                            'width'=>'20px',
                            "alt" => "help",
                        ))."</span>";
                    }
                    $append .= "</td></tr>";

                /*** get content for multiple rows from array ***/
                }elseif ($key === "-") {
                    if(isset($child['value'])){
                        $values = $this->getNodesFromTei($child,$array_route,$xmlObject);
                        if(count($values) === 0){
                            $append .= "<tr id=\"".$node."-1\" class=\"child-of-".$node."\">";
                            $append .= "<td style=\"padding-left:".$padding."px;\">Not set</td></tr>";
                        }
                        //append treetable rows to result

                        //add list if configured
                        if(isset($child['type']) && $child['type'] == "list"){
                            if(isset($json_node["1"]) || isset($json_node["-"])){
                                $append .= "<tr id=\"".$node."-".$array_index."\" class=\"child-of-".$node;
                                $append .= "\"><td style=\"padding-left:".$padding."px;\">configuration error: type list not available for parent nodes</td></tr>";
                                continue;
                            }else{
                                $append .= "<tr id=\"".$node."-".$array_index."\" class=\"child-of-".$node." initialized\"><td style=\"padding-left:".$padding."px;\"><ul style='margin:0;'>";
                            }
                        }

                        foreach($values as $key => $value){
                            if($value !== null)
                                $append .= $this->append_array_rows($value,$node,$array_index,$child,$padding,count($values));
                            $array_index++;
                        }
                        if(isset($child['type']) && $child['type'] == "list"){
                            //$append ends with ul -> no values added
                            if(substr($append, -strlen("<ul style='margin:0;'>")) === "<ul style='margin:0;'>")
                                $append .= '<li>none</li>';
                            $append .= "</ul></td></tr>";
                        }


                    }else{
                        $append .= "<tr id=\"".$node."-1\" class=\"child-of-".$node."\">";
                        $append .= "<td style=\"padding-left:".$padding."px;\">not configured</td></tr>";
                    }
                }
            }
        }
        if($append == ""){
            $append .= "<tr id=\"".$node."-1\" class=\"child-of-".$node."\">";
            $append .= "<td style=\"padding-left:".$padding."px;\">Not set</td></tr>";
        }
        echo $append;
    }
 	/**
     * Creates table rows containing the values for arrays from the tei-header.
     * @param $value
     * @param $node
     * @param $i
     * @param $json_node
     * @param $padding
     * @param $rows_count
     * @return string
     */
    private function append_array_rows($value,$node,$array_index,$json_node,$padding,$rows_count){
        $append = "";
        if(isset($json_node['type']) && $json_node['type'] == "list"){
            $append .= "<li";
            if(isset($json_node["class"]))
                $append .= " class=\"".$json_node["class"]."\"";
            $append .= ">$value</li>";
        }else{
            $append .= "<tr id=\"".$node."-".$array_index."\" class=\"child-of-".$node;

            if(isset($json_node["1"]) || isset($json_node["-"])){
                $append .= " parent initialized load collapsed\">";
                $append .= "<td style=\"padding-left:".$padding."px;\"><a class=\"expander\" style=\"margin-left: -19px; padding-left: 19px\" title=\"Expand\" href=\"#\"></a>";
                $append .= "$value";
            }else{
                $append .= "\"><td style=\"padding-left:".$padding."px;\">$value";
            }
            if(isset($json_node['tooltip'])){
                $view = new View($this);
                $html = $view->loadHelper('Html');
                $append .= "<span class=\"helptooltip\"><p>".$json_node['tooltip']."</p>".$html->image("help.png",array(
                    "border" => "0",
                    'width'=>'20px',
                    "alt" => "help",
                ))."</span>";
            }
            $append .= "</td></tr>";
        }
        return $append;
    }

    //TODO: refactor !!
    private function getNodesFromTei($node,$array_route,$tei){
        $text = $node['value'];
        $condition = @$node['condition']?:null;
        $result = array();
        if(strpos($text,"$") === false){
            $result[0] = $text;
            return $result;
        }
        //TODO: check if more [] than count(array_route)
        if(strpos($text,"[") === false){
            $result[0] = "ERROR: not an array";
            return $result;
        }

        preg_match_all('/\$(->|\w|\(.*\)|\[(\'|\S|\")*\])+/',$text,$paths);
        $paths = $paths[0];
        $values = array();
        $elementCount = array();
        $pathCount = 0;

        //resolve 2 arrays at once
        $resolveDouble = false;
        foreach ($paths as $path){

            //provide [] with array_route infos
            $path_parts = explode("[]",$path);
            $routeIndex = 0;
            while(count($path_parts) > 2 && count($array_route) > $routeIndex){
                $x = array_shift($path_parts);
                $path_parts[0] = $x."[".$array_route[$routeIndex++]."]".$path_parts[0];
            }

            if(count($path_parts) > 2 || count($array_route)>$routeIndex){
                if(count($path_parts) === 3 && !isset($node["1"]) && !isset($node["-"])){
                    $resolveDouble = true;
                }else{
                    $result[0] = "Error: unreselved array or wrong path configured";
                    return $result;
                }
            }

            if(count($path_parts) < 2 || count($array_route)>$routeIndex){
                $result[0] = "Error: no array to resolve";
                return $result;
            }
            //get values from teiHeader for each array element at prefix[]->suffix
            if(!isset($elementCount[$pathCount]))
                $elementCount[$pathCount] = 0;
            $i = 0;
            $prefix = eval('return $tei->'.substr($path_parts[0],1).'['.$i++.'];');
            while($prefix !== null){
                //Check condition, do not add value to $values if condition is wrong
                if($condition === null){
                    //merge all children of an array node
                    if($resolveDouble){
                        $j = 0;
                        $prefix2 = eval('return $prefix'.$path_parts[1].'['.$j++.'];');
                        while($prefix2!==null){
                            array_push($values,eval('return (string) $prefix2'.$path_parts[2].';'));
                            $elementCount[$pathCount]++;
                            $prefix2 = eval('return $prefix'.$path_parts[1].'['.$j++.'];');
                        }
                    }else{
                        $elementCount[$pathCount]++;
                        array_push($values, $this->defaultValue(eval('return (string) $prefix'.$path_parts[1].';'),$node,$pathCount));
                    }
                }else{
                    $condition_result = $this->checkCondition($condition,$pathCount,$prefix,$tei,$path_parts[0],$i-1);
                    if($condition_result === true){
                        //merge all children of an array node
                        if($resolveDouble){
                            $j = 0;
                            $prefix2 = eval('return $prefix'.$path_parts[1].'['.$j++.'];');
                            while($prefix2!==null){
                                array_push($values,eval('return (string) $prefix2'.$path_parts[2].';'));
                                $elementCount[$pathCount]++;
                                $prefix2 = eval('return $prefix'.$path_parts[1].'['.$j++.'];');
                            }
                        }else{
                            $elementCount[$pathCount]++;
                            array_push($values, $this->defaultValue(eval('return (string) $prefix'.$path_parts[1].';'),$node,$pathCount));
                        }
                    //return condition error message
                    }elseif($condition_result !== false){
                        $elementCount[$pathCount]++;
                        array_push($values, $condition_result );
                    //push null to conserve route for children
                    }else{
                        $elementCount[$pathCount]++;
                        array_push($values, null);
                    }
                }
                $prefix = eval('return $tei->'.substr($path_parts[0],1).'['.$i++.'];');
            }
            $pathCount++;
        }
        //check if element counts equal for each path
        $previous = null;
        foreach($elementCount as  $value){
            if($previous !== null && $previous !== $value){
                $result[0] = "ERROR: paths must resolve same array";
                return $result;
            }
            $previous = $value;
        }
        $elements = $elementCount[0];
        for($x = 0; $x<$elements;$x++ ){
            $row_values = array();
            $defaultCount = 0;
            $omitRow = false;
            for($y= 0; $y <$pathCount;$y++){
                if(strpos($values[$y*$elements],'$defaultomit') !== false)
                    $omitRow = true;
                if(strpos($values[$y*$elements],'$default') !== false){
                    $values[$y*$elements] = str_replace('$default', "",$values[$y*$elements]);
                    $defaultCount++;
                }
                array_push($row_values,$values[$y*$elements]);
            }
            if(!$omitRow){
                if(($defaultCount == $pathCount) && isset($node['default'])){
                    if($node['default'] !== "omit")
                        array_push($result,$node['default']);
                }else{
                    if($row_values[0] === null){
                        array_push($result,array_shift($row_values));
                    }else{
                        array_push($result,preg_replace_callback('/\$(->|\w|\(.*\)|\[(\'|\S|\")*\])+/',function($matches) use (&$row_values){
                            return array_shift($row_values);
                        },$text));
                    }
                }
            }
            array_shift($values);
        }
        return $result;
    }

    private function defaultValue($value, $node, $index = null){
        if($value == "" || $value == "NA" || $value == "N/A"){
            if(isset($node['default_values']) && $index !==null){
                $defaults = explode(",",$node['default_values']);
                $value = '$default'.$defaults[$index];
            }elseif(isset($node['default']) && $index ===null){
                $value = '$default'.$node['default'];
            }else{
                $value = '$defaultNot set';
            }
        }
        return $value;
    }

    /**
     * Checks if $condition #$pathCount === true in $prefix
     * @param $condition
     * @param $pathCount number of condition to be evaluated
     * @param $prefix part of the tei-header xml-object that contains the array
     * @param $tei tei-header xml-object
     * @param $prefixString part of the xml-path leading to the array
     * @param $array_index index of this item in the array of the tei-header node
     * @return bool
     */
    private function checkCondition($condition,$pathCount,$prefix,$tei,$prefixString,$array_index){
        $conditions = explode(",",$condition);
        $condition = trim($conditions[$pathCount]);
        if($condition === "")
            return true;
        if(strpos($condition,"$") !== false){
            //attention: path "$" is allowed in condition
            preg_match_all('/\$(->|\w|\(.*\)|\[(\'|\S|\")*\])*/',$condition,$variables);
            $variables = $variables[0];
            $values = array();
            foreach($variables as $variable){
                array_push($values,eval('return (string) $prefix'.substr($variable,1).';'));
            }
            $condition = preg_replace_callback('/\$(->|\w|\(.*\)|\[(\'|\S|\")*\])*/',function($matches) use (&$values){
                return "'".array_shift($values)."'";
            },$condition);

        }elseif($condition === "last"){
            $test = eval('return $tei->'.substr($prefixString,1).'['.($array_index+1).'];');
            if(eval('return $tei->'.substr($prefixString,1).'['.($array_index+1).'];') === null)
                return true;
            return false;
        }elseif($condition === "first"){
            if($array_index === 0)
                return true;
            return false;
        }
        return eval('return '.$condition.';');
    }

    private function getNodeFromTei($node,$array_route,$tei){
        $text = $node['value'];
        $condition = @$node['condition']?:null;
        if(strpos($text,"$") === false)
            return $text;
        preg_match_all('/\$(->|\w|\(.*\)|\[(\'|\S|\")*\])+/',$text,$paths);
        $paths = $paths[0];
        $values = array();
        foreach ($paths as $index => $path){
            //provide [] with array_route infos
            if(strpos($path,"[]") !== false){
                $path_parts = explode("[]",$path);
                $routeIndex = 0;
                while(count($path_parts) > 1 && count($array_route)>$routeIndex){
                    $x = array_shift($path_parts);
                    $path_parts[0] = $x."[".$array_route[$routeIndex++]."]".$path_parts[0];
                }
                $path = $path_parts[0];
                //check if all arrays resolved
                if(count($path_parts) > 1 || count($array_route)>$routeIndex)
                    //if one array not resolved and condition provided: get first element for which condition == true
                    if(isset($node['condition']) && count($path_parts) == 2){
                        $i = 0;
                        $prefix = eval('return $tei->'.substr($path_parts[0],1).'['.$i++.'];');
                        while($prefix !== null){
                            $check = $this->checkCondition($node['condition'],$index,$prefix,$tei,$path_parts[0],$i-1);
                                if($check === true){
                                    array_push($values,$this->defaultValue(eval('return (string) $prefix'.$path_parts[1].';'),$node));
                                    break;
                                }elseif($check !== false){
                                    array_push($values,$check);
                                }
                            $prefix = eval('return $tei->'.substr($path_parts[0],1).'['.$i++.'];');
                        }
                    }else{
                        return "ERROR: unresolved array";
                    }
            }
            //get value at $path from teiHeader
            array_push($values, $this->defaultValue(eval('return (string) $tei->'.substr($path,1).';'),$node,$index));
        }
        //replace variables in $text with $values
        $omitResult = false;
        $defaultCount = 0;
        $result = preg_replace_callback('/\$(->|\w|\(.*\)|\[(\'|\S|\")*\])+/',function($matches) use (&$values,&$defaultCount,&$omitResult){
            $value = array_shift($values);
            if(strpos($value, '$defaultomit') !== false)
                $omitResult = true;
            if(strpos($value, '$default') !== false){
                $value = str_replace('$default', "",$value);
                $defaultCount++;
            }
            return $value;
        },$text);
        //if all results are empty, use default value
        if($defaultCount === count($paths) && isset($node['default'])){
            if($node['default'] == "omit"){
                $result = null;
            }else{
                $result = $node['default'];
            }
        }
        if($omitResult)
            return null;

        return $result;

    }

    public function modifyObject($pid = null){
        if(!$this->modifyPermission($pid)){
            $this->Session->setFlash(__('No permission to modify Corpus.'));
            $this->redirect(array('action' => 'modify'));
        }
        #check id
        if($pid){
            $pid = urldecode($pid);
        }else{
            if(isset($_POST) && !empty($_POST))
                $pid = $_POST['id'];
        }
        if(!$pid){
            $this->Session->setFlash(__('No valid ID'));
            $this->redirect(array('action' => 'modify'));
        }

        #check post-data
        if(isset($_POST) && !empty($_POST)){
            try{
                #validate + check if data was modified
                $result_array = $this->XMLObject->getObjectXML($pid);
                if($result_array['http_code'] != '200'){
                    if($result_array['http_code'] == '401') $this->Session->setFlash(__('Access denied, Object state may be inactive'));
                    $this->redirect(array('action' => 'modify'));
                }
                $xmlObject = Xml::build($result_array['response']);
                $validate_array['label']=$xmlObject->objLabel;
                $validate_array['owner']=$xmlObject->objOwnerId;
                $validate_array['state']=$xmlObject->objState;
                $flashmsg = $this->XMLObject->validateModifyObject($_POST,$validate_array);
                if($flashmsg != ''){
                    $this->Session->setFlash($flashmsg);
                }else{
                    #set modify data
                    if($validate_array['owner']!=$_POST['owner']) $data['owner']=$_POST['owner'];
                    if($validate_array['label']!=$_POST['label']) $data['label']=$_POST['label'];
                    if($validate_array['state']!=$_POST['state']) $data['state']=$_POST['state'];
                    $data['pid'] = urldecode($pid);
                    $result_array = $this->XMLObject->modifyObjectFedora($data);
                    if($result_array['http_code'] == '200'){
                        $this->Session->setFlash('Object '.$pid.' modified.', 'default', array('class' => 'success'));
                    }else{
                        $this->Session->setFlash('Object '.$pid.' not modified.');
                        $this->set('response', $result_array['response']);
                    }
                }
            }catch (XmlException $e) {
                throw new InternalErrorException();
            }
        }

        #set view variables
        try{
            $result_objects = $this->XMLObject->getObjectXML($pid);
            $result_datastreams = $this->XMLObject->getDatastreamsXML($pid);
            if($result_objects['http_code'] != '200' || $result_datastreams['http_code'] != '200'){
                if($result_objects['http_code'] == '401'){
                    $this->Session->setFlash(__('Access denied, Object state may be inactive'));
                }else{
                    $this->Session->setFlash(__('Error requesting object, Code:'.$result_objects['http_code'].'/'.$result_datastreams['http_code']));
                    $this->set('response', $result_objects['response'].'</br>'.$result_datastreams['response']);
                }
                $this->redirect(array('action' => 'modify'));
            }

            $xmlObject = Xml::build($result_objects['response']);
            $this->set('id', $pid);
            $this->set('label', $xmlObject->objLabel);
            $this->set('owner', $xmlObject->objOwnerId);
            $this->set('state', $xmlObject->objState);

            $xmlDatastreams = Xml::build($result_datastreams['response']);
            $this->set('datastreams', $xmlDatastreams[0]->datastream);
            #print_r($xmlDatastreams);
        }
        catch (XmlException $e) {
            throw new InternalErrorException();
        }

        $this->set('creator',$this->XMLObject->getCreator($pid));

        $this->set('users',array_merge($this->XMLObject->getUsers($pid),$this->XMLObject->getLdapUsers($pid)));
        $this->set('groups',$this->XMLObject->getGroups($pid));
        if($this->XMLObject->isOpenAccess($pid)){
            $this->set('openAccess',true);
        }else{
            $this->set('openAccess',false);
        }

        if($this->XMLObject->doIndexing($pid)){
            $this->set('indexing',true);
        }else{
            $this->set('indexing',false);
        }
    }

    public function toggleOpenAccess(){
        $this->autoRender = false;
        $id = @$_POST['id']?: null;

        if($id !== null){
            $this->XMLObject->toggleOpenAccess($id);
        }
    }

    public function toggleIndexing(){
        $this->autoRender = false;
        $id = @$_POST['id']?: null;

        if($id !== null){
            if($this->XMLObject->doIndexing($id)){
                $return_array = $this->XMLObject->indexLatestVersion($id);
                if($return_array['http_code'] == "201"){
                    $this->XMLObject->toggleIndexing($id);
                }else{
                    echo $return_array['response'];
                }
            }else{
                $this->XMLObject->deleteFromIndex($id);
                $this->XMLObject->toggleIndexing($id);
            }
        }
    }

    public function deleteDatastream($pid = null, $dsid = null){
        if($dsid != null && $pid != null){
            $answer = $this->XMLObject->purgeDatastream($pid,$dsid);
            if($answer['http_code']=='200'){
                if(strpos($pid,'TEI-header' ===0))
                    $this->XMLObject->indexLatestVersion($pid);
                $this->XMLObject->deleteAllHandlePID($pid,$dsid);
                $this->Session->setFlash('Datastream "'.$dsid.'" purged.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'modifyObject',$pid));
            }else{
                $this->Session->setFlash('Datastream not purged, Code: '.$answer['http_code']);
                $this->redirect(array('action' => 'modifyObject',$pid));
            }
        }else{
            $this->Session->setFlash('No valid object/datastream');
            $this->redirect(array('action' => 'modifyObject',$pid));
        }
    }

    public function ajaxUpload($mode = null){
        $this->autoRender = false;

        $folder = 'tmp/'.$this->Session->read('Auth.User.username') . $this->Session->read('Auth.User.id');
        $view = new View($this);
        $html = $view->loadHelper('Html');

        if(isset($_POST['id']) && isset($_FILES['teifile']) && $mode == null){
            $sXmlFile = file_get_contents($_FILES['teifile']['tmp_name']);
            $teiXML = Xml::build($sXmlFile);
            $response = '';
            $response = $this->XMLObject->makeValidationFiles($teiXML,$folder);
            $response .= $this->XMLObject->validateTeiCorpusHeader($folder);
            if($response == 'valid'){
                echo  "<b>Corpus  </b>". $html->image('validUpload.png', array('width' => '17px','alt' => 'valid'));
            }else{
                echo "<b>Corpus  </b>".$html->image('invalidUpload.png', array('width' => '17px','alt' => 'invalid')).$response."<br/>";
            }

        }elseif($mode != null){

            if($mode == 'delete'){
                if(is_dir($folder)) $this->XMLObject->deleteValidationFolder($folder);

            }elseif($mode == 'preps'){
                $response = $this->XMLObject->validateTeiPreparationsHeader($folder);
                if($response == 'valid'){
                    echo  "<b>Preparations  </b>". $html->image('validUpload.png', array('width' => '17px','alt' => 'valid'));
                }else{
                    echo "<b>Preparations  </b>".$html->image('invalidUpload.png', array('width' => '17px','alt' => 'invalid'))."<br/>";
                    foreach($response as $key => $value){
                        echo 'Error in preparation '.$key.':'.$value.'<br/>';
                    }
                }

            }elseif($mode == 'docs'){
                $response = $this->XMLObject->validateTeiDocumentHeader($folder);
                if($response == 'valid'){
                    echo  "<b>Documents  </b>". $html->image('validUpload.png', array('width' => '17px','alt' => 'valid'));
                }else{
                    echo "<b>Documents </b>".$html->image('invalidUpload.png', array('width' => '17px','alt' => 'invalid'))."<br/>";
                    foreach($response as $key => $value){
                        echo 'Error in document '.$key.':'.$value.'<br/>';
                    }
                }
            }
        }
    }

    public function import() {
        if(!empty($_POST)){
            $successmsg = "";
            $files = @$_FILES ?: null;
            $errormsg = $this->XMLObject->validateIngest($_POST,$files);
            if($errormsg != ''){
                $this->Session->setFlash($errormsg);
            }else{
                if(!isset($files['teifile']) || !isset($files['teifile']['name']) || $files['teifile']['name'] == "" || $files['teifile']['size'] == 0){
                    $this->Session->setFlash("Please select a TEI-Header to import a corpus.");
                    return;
                }
                $formatname = @$_POST['formatname']?: null;
                //validate tei header
                $valid = false;
                $folder = 'tmp/'.$this->Session->read('Auth.User.username') . $this->Session->read('Auth.User.id');
                $sXmlFile = file_get_contents($files['teifile']['tmp_name']);
                $teiXML = Xml::build($sXmlFile);
                $this->XMLObject->makeValidationFiles($teiXML,$folder);
                $response = $this->XMLObject->validateTeiCorpusHeader($folder);
                if($response == 'valid'){
                    $response = $this->XMLObject->validateTeiDocumentHeader($folder);
                    if($response == 'valid'){
                        $response = $this->XMLObject->validateTeiPreparationsHeader($folder);
                        if($response == 'valid'){
                            $valid = true;
                        }
                    }
                }
                $this->XMLObject->deleteValidationFolder($folder);
                if(!$valid){
                    $this->Session->setFlash('Error: Tei_header not valid');
                    return;
                }
                $returnmsg = $this->XMLObject->ingestFedora($_POST['id'],$_POST['label'],$_FILES,$formatname,$this->Session->read('Auth.User.username'));
                #set flashmessage
                $error = false;
                $response = '';
                for($i=0;$i<count($returnmsg['http_code']);$i++){
                    if($returnmsg['http_code'][$i] != '201'){

                        if($i == 0){
                            $error = true;
                            if($returnmsg['http_code'][$i] == '500'){
                                $errormsg .= 'Object not created, PID already exists</br>';
                            }else{
                                if(isset($returnmsg['response'][$i]))$response .= '</br>'.$returnmsg['response'][$i];
                                $errormsg .= 'Object not created</br>';
                            }
                        }elseif($i==1){
                            $error = true;
                            $errormsg .= 'TEI-header upload failed</br>';
                            if(isset($returnmsg['response'][$i]))$response .= '</br>'.$returnmsg['response'][$i];
                        }elseif($i == 2){
                            $error = true;
                            $errormsg .= "Can not update tei version file";
                            if(isset($returnmsg['response'][$i]))$response .= '</br>'.$returnmsg['response'][$i];
                        }else{
                            $errormsg .= 'Upload '.($i-2).' failed</br>';
                            if(isset($returnmsg['response'][$i]))$response .= '</br>'.$returnmsg['response'][$i];
                        }
                    }else{
                        //corpus created -> create database entry for rightsmanagement
                        if($i == 0){
                            if($this->Session->read('Auth.User.Group.name') === 'LdapGroup'){
                                $this->XMLObject->createLdap($_POST['id'],$this->Session->read('Auth.User.username'),isset($_POST['openAccess']),isset($_POST['doIndex']));
                            }else{
                                $this->XMLObject->create($_POST['id'],$this->Session->read('Auth.User.id'),isset($_POST['openAccess']),isset($_POST['doIndex']));
                            }

                        }
                    }
                }
                if(!$error) {
                    if(isset($_FILES['teifile']) && isset($_POST['doIndex'])){
                        $sXmlFile = '/tmp/'.basename($_FILES['teifile']['tmp_name']);
                        $json = $this->XMLObject->javaXSLT($sXmlFile);

                        $return_array = $this->XMLObject->handle($_POST['id']);
                        $this->XMLObject->saveLicense($_POST);
                        $return_array = $this->XMLObject->indexJson($json,$_POST['id']);
                        if($return_array['http_code']== 201){
                            $successmsg .= "</br>Corpus indexed.";
                            #$return_array = $this->XMLObject->handle($_POST['id']);
                        }else{
                            $errormsg .= "</br>Corpus not indexed. Error:".$return_array['response'];
                            $error = true;
                        };
                    }
                }
                if($error){
                    $this->Session->setFlash($errormsg);
                }else{
                    $this->Session->setFlash('Corpus imported. '.$successmsg, 'default', array('class' => 'success'));
                    $this->set('ingestedObject',urlencode($_POST['id']));
                }
                if($response != '') $this->set('response',$response);
            }
        }
    }

    public function modifyDatastream($pid = null, $dsid = null){
        if(!$this->modifyPermission($pid)){
            $this->Session->setFlash(__('No permission to modify Corpus.'));
            $this->redirect(array('action' => 'modify'));
        }
        if($pid != null && $pid != null){
            #check post data
            if(isset($_POST) && !empty($_POST)){
                #get current properties
                try{
                    $result = $this->XMLObject->getDatastream($pid,$dsid);
                    if($result['http_code'] != '200'){
                        $this->Session->setFlash(__('Error, Code:'.$result['http_code']));
                        return;
                    }
                    $xmlObject = Xml::build($result['response']); // Here will throw a Exception
                    $validate_array['dsLabel']=(string)$xmlObject->dsLabel;
                    $validate_array['dsMIME']=(string)$xmlObject->dsMIME;
                    $validate_array['dsState']=(string)$xmlObject->dsState;
                    $validate_array['dsVersionable']=(string)$xmlObject->dsVersionable;
                }catch (XmlException $e) {
                    throw new InternalErrorException();
                }
                #change datastream properties
                if(isset($_POST['modDs'])){
                    #validate post data
                    $flashmsg = $this->XMLObject->validateModifyDatastream($_POST,$validate_array);
                    if($flashmsg != ''){
                        $this->Session->setFlash($flashmsg);
                    }else{
                        #check for changes
                        if($validate_array['dsLabel']!= $_POST['label']) $data['label']=$_POST['label'];
                        if($validate_array['dsMIME']!= $_POST['mimeType']) $data['mimeType']=$_POST['mimeType'];
                        if($validate_array['dsState']!= $_POST['state']) $data['state']=$_POST['state'];
                        if($validate_array['dsVersionable']!= $_POST['versionable']) $data['versionable']=$_POST['versionable'];
                        if(isset($data)){
                            $data['dsid'] = $dsid;
                            $data['pid'] = $pid;
                            #set modify data
                            $result_array = $this->XMLObject->modifyDatastreamFedora($data);
                            if($result_array['http_code'] == '200'){
                                $this->Session->setFlash('Datastream modified.', 'default', array('class' => 'success'));
                            }else{
                                $this->Session->setFlash(__('Error, Code:'.$result['http_code']));
                            }
                        }
                    }
                    #upload new version of datastream
                }elseif(isset($_POST['uploadNewVersion']) && isset($_FILES) && $_FILES != ''){
                    $parameter[0] = array(
                        'dsLabel' => urlencode($validate_array['dsLabel']),
                        'mimeType' => urlencode($validate_array['dsMIME']),
                        'dsState' => $validate_array['dsState'],
                        'versionable' => $validate_array['dsVersionable']
                    );
                    #var_dump($parameter);
                    #upload
                    $result_array = $this->XMLObject->addDatastreams($pid,$_FILES,array($dsid),$parameter);
                    #auswertung
                    if($result_array['http_code'] == '201'){
                        $this->Session->setFlash('Datastream updated.', 'default', array('class' => 'success'));
                    }else{
                        $this->Session->setFlash('Error: Streams not added. Code:'.$result_array['http_code']);
                        $this->set('error', $result_array['response']);
                    }
                }
            }
            #view variables
            try{
                $result = $this->XMLObject->getDatastream($pid,$dsid);
                if($result['http_code'] == '200'){
                    $this->set('dsid',$dsid);
                    $this->set('response',$result['response']);
                }else{
                    $this->Session->setFlash(__('Error, Code:'.$result['http_code']));
                    $this->redirect($this->referer());
                }
            }catch (XmlException $e) {
                throw new InternalErrorException();
            }
            $this->set('objectID',$pid);

        }
    }
    public function addDatastream($pid = null){
        if($pid != null) $pid = urldecode($pid);
        if($pid == null && isset($_POST) && !empty($_POST)) $pid = $_POST['id'];
        if($pid == null){
            $this->Session->setFlash(__('No valid ID'));
            $this->redirect($this->referer());
        }else{
            #check post
            if(isset($_POST) && !empty($_POST) && isset($_FILES) && !empty($_FILES)){
                #validierung
                $flashmsg = $this->XMLObject->validateAddDatastream($_POST,$_FILES['userfile']);
                if($flashmsg != ''){
                    $this->Session->setFlash($flashmsg); #
                }else{
                    #upload
                    $result_array = $this->XMLObject->addDatastreams($pid,$_FILES,$_POST['formatname']);
                    #auswertung
                    if($result_array['http_code'] == '201'){
                        $this->Session->setFlash('Datastream(s) added.', 'default', array('class' => 'success'));
                        $this->redirect(array('action' => 'modifyObject',$pid));
                    }else{
                        $this->Session->setFlash('Error: Streams not added. Code:'.$result_array['http_code']);
                        $this->set('response', $result_array['response']);
                    }
                }
            }

            #view variables
            try{
                $result_objects = $this->XMLObject->getObjectXML($pid);
                if($result_objects['http_code'] == '200'){
                    $xmlObject = Xml::build($result_objects['response']);
                    $this->set('id', $pid);
                    $this->set('label', $xmlObject->objLabel);
                }else{
                    $this->Session->setFlash(__('Error, Code:'.$result_objects['http_code']));
                    $this->redirect($this->referer());
                }
            }catch (XmlException $e) {
                throw new InternalErrorException();
            }
        }
    }

    public function addCorpusUser(){
        $this->autoRender = false;
        if(!$this->Auth->loggedIn()) exit(Router::url(array('controller' => 'Users','action' => 'login')));
        $user = @$_POST['user']?: null;
        $id = @$_POST['id']?: null;
        if($user !== null && $id !== null){
            $this->loadModel('User');
            $user_array = $this->User->findUser($user);
            if($user_array){
                echo $this->XMLObject->addCorpusUser($id,$user_array['User']['id']);
            }else{
                $this->loadModel('Ldap');
                if($this->Ldap->isUser($user)){
                    echo $this->XMLObject->addLdapCorpusUser($id,$user);
                }else{
                    echo 'User not found.';
                }
            }
        }else{
            echo 'Error: No User specified.';
        }
    }

    public function removeCorpusUser(){
        $this->autoRender = false;
        if(!$this->Auth->loggedIn()) exit(Router::url(array('controller' => 'Users','action' => 'login')));
        $user = @$_POST['user']?: null;
        $id = @$_POST['id']?: null;
        if($user !== null && $id !== null){
            $this->loadModel('User');
            $user_array = $this->User->findUser($user);
            if($user_array){
                echo $this->XMLObject->removeCorpusUser($id,$user_array['User']['id']);
            }else{
                $this->loadModel('Ldap');
                    if($this->Ldap->isUser($user)){
                        echo $this->XMLObject->removeLdapCorpusUser($id,$user);
                    }else{
                        echo 'User not found.';
                    }
            }
        }else{
            echo 'Error: No User specified.';
        }
    }

    public function addCorpusGroup(){
        $this->autoRender = false;
        if(!$this->Auth->loggedIn()) exit(Router::url(array('controller' => 'Users','action' => 'login')));
        $group = @$_POST['group']?: null;
        $id = @$_POST['id']?: null;
        if($group !== null && $id !== null){
            $this->loadModel('Group');
            $group_array = $this->Group->findGroup($group);
            if($group_array){
                echo $this->XMLObject->addCorpusGroup($id,$group_array['Group']['id']);
            }else{
                echo 'Group not found.';
            }
        }else{
            echo 'Error: No Group specified.';
        }
    }

    public function removeCorpusGroup(){
        $this->autoRender = false;
        if(!$this->Auth->loggedIn()) exit(Router::url(array('controller' => 'Users','action' => 'login')));
        $group = @$_POST['group']?: null;
        $id = @$_POST['id']?: null;
        if($group !== null && $id !== null){
            $this->loadModel('Group');
            $group_array = $this->Group->findGroup($group);
            if($group_array){
                echo $this->XMLObject->removeCorpusGroup($id,$group_array['Group']['id']);
            }else{
                echo 'Group not found.';
            }
        }else{
            echo 'Error: No Group specified.';
        }
    }

    public function addTeiheader($id){
        if(isset($_POST) && !empty($_POST) && isset($_POST['label']) && !empty($_POST['label'])){
            $valid = false;
            //validation
            $folder = 'tmp/'.$this->Session->read('Auth.User.username') . $this->Session->read('Auth.User.id');
            if(isset($_FILES['teifile'])){
                $sXmlFile = file_get_contents($_FILES['teifile']['tmp_name']);
                try{
                    $teiXML = Xml::build($sXmlFile);
                }catch (XmlException $e){
                    $this->Session->setFlash(__("Fehler".$e));
                    return;
                }
                $this->XMLObject->makeValidationFiles($teiXML,$folder);
                if(isset($_POST['scheme'])){
                    $scheme = $_POST['scheme'];
                }else{
                    $scheme = null;
                }
                $response = $this->XMLObject->validateTeiCorpusHeader($folder,$scheme);
                if($response == 'valid'){
                    $response = $this->XMLObject->validateTeiDocumentHeader($folder,$scheme);
                    if($response == 'valid'){
                        $response = $this->XMLObject->validateTeiPreparationsHeader($folder,$scheme);
                        if($response == 'valid'){
                            $valid = true;
                        }
                    }
                }
                $this->XMLObject->deleteValidationFolder($folder);
            }
            if($valid){
                $error = '';
                $returnmsg = $this->XMLObject->addTei($id,$_POST['label'],$_FILES['teifile']['tmp_name'],$scheme);
                if($returnmsg['http_code'] != '201' && $returnmsg['http_code'] != '200'){
                    $error.=$returnmsg['response'].'</br>';
                }else{
                    if($this->XMLObject->doIndexing($id)){
                        $return_array = $this->XMLObject->indexLatestVersion($id);
                        if($return_array['http_code']!= 201){
                            $error .= "</br>Object not indexed. Error:".$return_array['response'];
                        }
                    }
                    $return_array = $this->XMLObject->handle($id);
                    if($return_array['http_code']!= 200){
                        $error .= "</br>Could not create handle pid. Error:".$return_array['response'];
                    }
                }
                if($error == ''){
                    $this->Session->setFlash('TEI-header added.', 'default', array('class' => 'success'));
                    // $this->redirect(array('action' => 'modifyObject',$id));
                }else{
                    $this->Session->setFlash(__($error));
                }
                //$this->redirect($this->referer());
            }else{
                $this->Session->setFlash(__('TEI-header invalid.'));
            }
        }
        $schemeList = glob(WWW_ROOT.'schemes/*',GLOB_ONLYDIR);
        $schemeList = str_replace(WWW_ROOT.'schemes/','',$schemeList);
        if(isset($schemeList) && !empty($schemeList)){
            $this->set('schemes',$schemeList);
        }else{
            $this->set('schemes','');
        }
        $conf = Configure::read('SchemeConfig');
        $this->set('activeScheme',$conf['current_scheme']);

        $this->set('id',$id);
    }


    public function downloadTei($pid){
        #$this->autoRender = false;
        $path='http://depot1-7.cms.hu-berlin.de:8080/fedora/objects/'.$pid.'/datastreams/TEI_header/content';

        $resonse_array = $this->XMLObjects->getObjectTEIXml($pid);
        if($resonse_array['http_code'] == '200'){
            $this->viewClass = 'Media';
            $params = array(
                #'id'        => 'tei-header-'.urldecode($pid).'.xml',
                'name'      => 'tei-header-'.urldecode($pid),
                'extension' => 'xml',
                'path'      => APP . $path, # <---------------------
                'download'  => true

            );
            $this->set($params);
        }
        #ab cakephp 2.3 -> $this->response->file($file["path"], array('download' => true, 'name' => 'tei-header-'.urldecode($pid)));
    }

    public function downloadDatastream($pid,$dsid,$asOfDateTime = null){
        $this->autoRender=false;
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $this->redirect("$fedoraAdress/fedora/objects/$pid/datastreams/$dsid/content?download=true");
        try{
            $result = $this->XMLObject->getDatastream($pid,$dsid);
            if($result['http_code'] != '200'){
                $this->Session->setFlash(__('Error, Code:'.$result['http_code']));
                return;
            }
            $xmlObject = Xml::build($result['response']);
            $file_name=(string)$xmlObject->dsLabel;
            if(strpos($dsid,"TEI-header") === 0){
                $file_name = $dsid.".xml";
            }
            $mime =(string)$xmlObject->dsMIME;
        }catch (XmlException $e) {
            throw new InternalErrorException();
        }
        $this->response->type($mime);
        if($asOfDateTime){
            $url[] = "http://141.20.4.72:8080/fedora/objects/".$pid."/datastreams/".$dsid."/content?asOfDateTime=$asOfDateTime";
        }else{
            $url[] = "http://141.20.4.72:8080/fedora/objects/".$pid."/datastreams/".$dsid."/content";
        }
        $response = @call_user_func_array('file_get_contents', $url);
        $file = tempnam("/tmp", "download");
        file_put_contents($file,$response);

        $this->response->header(array(
            "Content-Disposition: attachment; filename=\"$file_name\"",
            "Content-Type: application/octet-stream",
            "X-Sendfile: /tmp/$file"
        ));
        return $response;
    }

    public function uploadNewTeiVersion($pid,$dsid){
        try{
            $result = $this->XMLObject->getDatastream($pid,$dsid);
            if($result['http_code'] != '200'){
                $this->Session->setFlash(__('Error, Code:'.$result['http_code']));
                return;
            }
            $xmlObject = Xml::build($result['response']); // Here will throw a Exception
            $dslabel=(string)$xmlObject->dsLabel;
        }catch (XmlException $e) {
            throw new InternalErrorException();
        }
        $this->set("id",$pid);
        $this->set("dsid",$dsid);
        $this->set("dslabel",$dslabel);
        $schemeList = glob(WWW_ROOT.'schemes/*',GLOB_ONLYDIR);
        $schemeList = str_replace(WWW_ROOT.'schemes/','',$schemeList);
        if(isset($schemeList) && !empty($schemeList)){
            $this->set('schemes',$schemeList);
        }else{
            $this->set('schemes','');
        }

        if(!empty($_POST)){
            if(isset($_FILES) && $_FILES['teifile']['size'] != 0){
                $valid = false;
                //validation
                $folder = 'tmp/'.$this->Session->read('Auth.User.username') . $this->Session->read('Auth.User.id');
                if(isset($_FILES['teifile'])){
                    $sXmlFile = file_get_contents($_FILES['teifile']['tmp_name']);
                    $teiXML = Xml::build($sXmlFile);
                    $this->XMLObject->makeValidationFiles($teiXML,$folder);
                    $response = $this->XMLObject->validateTeiCorpusHeader($folder);
                    if($response == 'valid'){
                        $response = $this->XMLObject->validateTeiDocumentHeader($folder);
                        if($response == 'valid'){
                            $response = $this->XMLObject->validateTeiPreparationsHeader($folder);
                            if($response == 'valid'){
                                $valid = true;
                            }
                        }
                    }
                    $this->XMLObject->deleteValidationFolder($folder);
                }
                if($valid){
                    $response = $this->XMLObject->addDatastream($pid,$_FILES['teifile']['tmp_name'],urlencode($dsid),urlencode($dslabel));
                    if($response['http_code'] == 201){
                        $this->Session->setFlash('TEI-header updated.', 'default', array('class' => 'success'));
                        if($this->XMLObject->doIndexing($pid))
                            $this->XMLObject->indexLatestVersion($pid);
                        $this->XMLObject->handle($pid);
                    }else{
                        $this->Session->setFlash('TEI-header not updated. Error:'.$response['http_code']);
                    }
                }else{
                    $this->Session->setFlash('TEI-header invalid.');
                }
            }else{
                $this->Session->setFlash('No file selected.');
            }
        }
    }

    public function uploadNewVersion($pid,$dsid){
        try{
            $result = $this->XMLObject->getDatastream($pid,$dsid);
            if($result['http_code'] != '200'){
                $this->Session->setFlash(__('Error, Code:'.$result['http_code']));
                return;
            }
            $xmlObject = Xml::build($result['response']); // Here will throw a Exception
            $dslabel=(string)$xmlObject->dsLabel;
        }catch (XmlException $e) {
            throw new InternalErrorException();
        }
        $this->set("id",$pid);
        $this->set("dsid",$dsid);
        $this->set("dslabel",$dslabel);
        if(!empty($_POST)){
            if(isset($_FILES)){
                $response = $this->XMLObject->addDatastream($pid,$_FILES['uploadfile']['tmp_name'],urlencode($dsid),urlencode($_FILES['uploadfile']['name']));
                if($response['http_code'] == 201){
                    $this->Session->setFlash('Datastream updated.', 'default', array('class' => 'success'));
                }else{
                    $this->Session->setFlash('Datastream not updated.');
                }
            }else{
                $this->Session->setFlash('No file selected.');
            }
        }
    }

    public function dsVersionHistory($pid, $dsid){
        if(isset($_POST) && !empty($_POST) ){
            if(strpos($_POST['pid'],'TEI-header') === 0){
                $result_array = $this->XMLObject->purgeDatastreamVersionFedora($_POST['pid'],$_POST['dsID'],$_POST['date']);
            }else{
                $result_array = $this->XMLObject->deleteTeiVersion($_POST['pid'],$_POST['dsID'],$_POST['date']);
            }
            if($result_array['http_code'] == 200){
                $this->Session->setFlash('Datastream purged.', 'default', array('class' => 'success'));
            }else{
                $this->Session->setFlash('Datastream not purged. Error: '.$result_array['response']);
            }
        }
        try{
            $result = $this->XMLObject->getDatastreamHistory($pid,$dsid);
            if($result['http_code'] != '200'){
                if($result['http_code'] == '404'){
                    $this->Session->setFlash(__('Datastream no longer available.'));
                }else{
                    $this->Session->setFlash(__('Error: '.$result['response'].', Code:'.$result['http_code']));
                }


                $this->redirect(array('action' => 'modifyObject',$pid));
            }
            $xmlObject = Xml::build($result['response']);
        }catch (XmlException $e) {
            throw new InternalErrorException();
        }
        $this->set('objectID',$pid);
        $this->set('xml',$xmlObject);
    }



    private function viewPermission($corpus_id){
        if($this->Session->read('Auth.User.group_id') == '1') return true;
        if($this->XMLObject->isOpenAccess($corpus_id)){
            return true;
        }

        $user_id = $this->Session->read('Auth.User.id');
        $user_name = $this->Session->read('Auth.User.username');
        $user_group = $this->Session->read('Auth.User.Group.id');
        if( $this->XMLObject->isCorpusGroup($user_group,$corpus_id) || $this->XMLObject->isCorpusUser($user_id,$user_name,$corpus_id) || $this->XMLObject->isCorpusCreator($user_id,$user_name,$corpus_id)){
            return true;
        }else{
            return false;
        }
    }

    private function modifyPermission($corpus_id){
        if($this->Session->read('Auth.User.group_id') == '1') return true;
        $user_id = $this->Session->read('Auth.User.id');
        $user_name = $this->Session->read('Auth.User.username');
        if($this->XMLObject->isCorpusCreator($user_id,$user_name,$corpus_id)){
            return true;
        }else{
            return false;
        }
    }

}