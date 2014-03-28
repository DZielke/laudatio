<?php
    App::uses('AppController', 'Controller','Xml');
    /**
     * Users Controller
     *
     * @property User $User
     */
class ConfigsController extends AppController {

    function beforeFilter() {
        parent::beforeFilter();
    }
    public function index(){
        if(isset($_POST) && !empty($_POST)){
            if(ctype_alnum($_POST['indexName'])){
                Configure::write('IndexConfig.index_name', $_POST['indexName']);
                Configure::dump('site_config.php', 'default',array('IndexConfig','SchemeConfig'));
                $this->set('indexNameChanged','Index set to: '.$_POST['indexName']);
            }else{
                $this->set('indexNameChanged','alphanumeric characters only');
            }
        }else{
            $this->set('indexNameChanged','');
        }
        $conf = Configure::read('SchemeConfig');
        $this->set('activeScheme',$conf['current_scheme']);
        $conf = Configure::read('IndexConfig');
        $this->set('indexName',$conf['index_name']);

    }

    public function uploadScheme(){
        if(isset($_POST) && !empty($_POST) ){
            if(isset($_POST['delete'])){
                $schemeName = $_POST['delete'];
                $this->Config->deleteFedoraObject("scheme:$schemeName");
            }elseif(isset($_POST['activate'])){
                Configure::write('SchemeConfig.current_scheme', $_POST['activate']);
                Configure::dump('site_config.php', 'default',array('IndexConfig','SchemeConfig','FedoraConfig','PiwikConfig','HandleConfig'));
            }elseif(isset($_FILES) && !empty($_FILES)){
                $error = ConfigsController::validateUploadScheme($_POST,$_FILES);
                if($error != ''){
                    $this->Session->setFlash($error);
                    return;
                }
                $schemeName = $_POST['schemeName'];
                $this->Config->createFedoraObject("scheme:$schemeName","Scheme Configuration");


                //if(!mkdir(WWW_ROOT.'schemes/'.$schemeName)) $error .= 'Could not create folder for scheme.</br>';

                $format = ConfigsController::getSchemeFormat($_FILES['corpusScheme']["name"]);
                if($format){
                    $this->Config->addDatastream("scheme:$schemeName", $_FILES['corpusScheme']["tmp_name"], "teiODD_Corpus", "teiODD_Corpus.".$format,null,false);
                }else{
                    $error .= "Unsupported corpus scheme format. (supported: rng)</br>";
                }

                $format = ConfigsController::getSchemeFormat($_FILES['documentScheme']["name"]);
                if($format){
                    $this->Config->addDatastream("scheme:$schemeName", $_FILES['documentScheme']["tmp_name"], "teiODD_Document", "teiODD_Document.".$format,null,false);
                }else{
                    $error .= "Unsupported documents scheme format. (supported: rng)</br>";
                }

                $format = ConfigsController::getSchemeFormat($_FILES['prepScheme']["name"]);
                if($format){
                    $this->Config->addDatastream("scheme:$schemeName", $_FILES['prepScheme']["tmp_name"], "teiODD_Preparation", "teiODD_Preparation.".$format,null,false);
                }else{
                    $error .= "Unsupported preparation scheme format. (supported: rng)</br>";
                }

                if($error == ''){
                    //Configure::write('SchemeConfig.current_scheme', $schemeName);
                    //Configure::dump('site_config.php', 'default',array('IndexConfig','SchemeConfig','FedoraConfig','PiwikConfig','HandleConfig'));

                    //clone default mappings
                    try{
                        $conf = Configure::read('SchemeConfig');
                        $default_scheme = $conf['current_scheme'];
                        $defaults_index_mapping = $this->Config->getDatastream("scheme:$default_scheme","indexMapping");
                        $defaults_index_mapping = preg_replace('/"[A-Za-z0-9]+"/', '"'.$schemeName.'"', $defaults_index_mapping, 1);
                        $defaults_view_mapping = $this->Config->getDatastream("scheme:$default_scheme","viewMapping");
                        try{
                            $this->Config->putIndexMapping($schemeName,$defaults_index_mapping);
                        }catch(InternalErrorException $e){
                            $this->Session->setFlash("Mapping not accepted, please configure mapping before importing coprora. Error: ".$e->getMessage());
                        }
                        $this->Config->addDatastreamText("scheme:$schemeName","indexMapping","Index Mapping",$defaults_index_mapping);
                        $this->Config->addDatastreamText("scheme:$schemeName","viewMapping","View Mapping",$defaults_view_mapping);
                    }catch (NotFoundException $e){
                        //mappings not found, do nothing
                    }
                    $this->Session->setFlash('New scheme added.', 'default', array('class' => 'success'));
                }else{
                    $this->Config->deleteFedoraObject("scheme:$schemeName");
                    $this->Session->setFlash('New scheme not added. Error: '.$error);
                }
            }
        }
        $schemeList = $this->Config->getSchemeList();
        //view variables
        //$schemeList = glob(WWW_ROOT.'schemes/*',GLOB_ONLYDIR);
        //$schemeList = str_replace(WWW_ROOT.'schemes/','',$schemeList);
        if(isset($schemeList) && !empty($schemeList)){
            $this->set('schemes',$schemeList);
        }else{
            $this->set('schemes','');
        }
        $max = 0;
        foreach($schemeList as $i => $scheme){
            preg_match("/\d+$/",$scheme,$matches);
            if(isset($matches[0]))
                $max = max($max,$matches[0]);
        }
        $this->set('newSchemeNumber',$max+1);
        $conf = Configure::read('SchemeConfig');
        $this->set('activeScheme',$conf['current_scheme']);
    }

    private function getSchemeFormat($name){
        $extension = substr($name,strrpos($name,'.')+1);
        if($extension === 'rng'){
            return $extension;
        }else{
            return false;
        }
    }

    private function validateUploadScheme($post,$files){
        $error = '';
        if(!ctype_alnum($_POST['schemeName'])) $error .= 'Scheme name should contain alphanumeric characters only</br>';
        if(!isset($_FILES['corpusScheme'])) $error .= 'Corpus Scheme required</br>';
        if(!isset($_FILES['documentScheme'])) $error .= 'Document Scheme required</br>';
        if(!isset($_FILES['prepScheme'])) $error .= 'Preparation Scheme required</br>';

        $schemeName = $_POST['schemeName'];
        if($this->Config->isFedoraObject("scheme:$schemeName")) $error.= 'Scheme already exists.';
        return $error;
    }

    public function reindex(){
        $this->loadModel('XMLObject');
        $this->autoRender = false;
        $response = $this->XMLObject->showAllObjectsXML();
        $xml = Xml::build($response);
        $errormsg = '';
        foreach($xml->resultList->objectFields as $object){
            if(strpos($object->pid,'fedora-system') === false && strpos($object->pid,'islandora') === false && strpos($object->pid,'test:') === false && strpos($object->pid,'scheme:') === false){
                if($this->XMLObject->doIndexing($object->pid)){
                    $return_array = $this->XMLObject->indexLatestVersion($object->pid);
                    if(isset($return_array['http_code'])){
                        if($return_array['http_code']!= '201'){
                            $errormsg .= ' '.$object->pid.':'.$return_array['response'];
                        }
                    }else{
                        var_dump($return_array);
                    }
                }
            }
        }

        $this->Session->setFlash('Documents reindexed'.$errormsg, 'default', array('class' => 'success'));
        $this->redirect("/configs");
    }


    public function updateHandle(){
        $this->loadModel('XMLObject');
        $this->autoRender = false;
        $response = $this->XMLObject->showAllObjectsXML();
        $xml = Xml::build($response);
        $errors = null;
        foreach($xml->resultList->objectFields as $object){
            if(strpos($object->pid,'fedora-system') === false && strpos($object->pid,'islandora') === false && strpos($object->pid,'test:') === false && strpos($object->pid,'scheme:') === false){
                $responses = $this->XMLObject->updateHandle($object->pid);
                for($x = 0;$x<count($responses['http_code']);$x++){
                    if($responses['http_code'][$x] != "204"){
                        if(!isset($errors[(string)$object->pid]))
                            $errors[(string)$object->pid] = array();
                        array_push($errors[(string)$object->pid],$responses['response'][$x]);
                    }
                }
            }
        }

        if($errors == null)
            $this->Session->setFlash('Handle addresses updated.', 'default', array('class' => 'success'));
        else{
            $errormsg = "";
            foreach($errors as $pid => $error_array){
                $errormsg .= $pid.": ";
                foreach($error_array as $error)
                    $errormsg .= $error."</br>";
                $errormsg .="</br>";
            }
            $this->Session->setFlash('Error while updating handle addresses:'.$errormsg);
        }
        $this->redirect("/configs");

    }

    /********************************************************************************
     *
     *                              Index Mapping
     *
     *******************************************************************************/

    public function editIndexMapping($scheme){
        $this->set("scheme",$scheme);
        if($this->Config->isFedoraObject("scheme:$scheme") && $this->Config->isFedoraDatastream("scheme:$scheme","indexMapping")){
            $this->set("versions", $this->Config->getDatastreamVersions("scheme:$scheme","indexMapping"));
        }
    }

    public function loadIndexMapping(){
        $this->autoRender = false;
        if(isset($_POST['scheme'])){
            $scheme = $_POST['scheme'];
            if($this->Config->isFedoraObject("scheme:$scheme") && $this->Config->isFedoraDatastream("scheme:$scheme","indexMapping")){
                if(isset($_POST['version']))
                    echo $this->Config->getDatastream("scheme:$scheme","indexMapping",$_POST['version']);
                else
                    echo $this->Config->getDatastream("scheme:$scheme","indexMapping");
            }else{
                echo "no mapping configured";
            }
        }else{
            echo "no mapping configured";
        }
    }

    public function deleteIndexMapping(){
        $this->autoRender = false;
        if(isset($_POST['scheme'])){
            $scheme = $_POST['scheme'];
            if($this->Config->isFedoraObject("scheme:$scheme") && $this->Config->isFedoraDatastream("scheme:$scheme","indexMapping")){
                if(isset($_POST['version'])){
                    $this->Config->deleteDatastream("scheme:$scheme","indexMapping",$_POST['version']);
                }else{
                    $this->Config->deleteDatastream("scheme:$scheme","indexMapping");
                }
                echo 'success';
            }else{
                echo "no configuration found";
            }
        }else{
            echo "no scheme specified";
        }
    }

    public function saveIndexMapping(){
        $this->autoRender = false;
        if(isset($_POST['json']) && isset($_POST['scheme']) && $this->Config->isFedoraObject("scheme:".$_POST['scheme'])){
            if(!$this->Config->isFedoraDatastream("scheme:".$_POST['scheme'],"indexMapping")){
                $this->Config->addDatastreamText("scheme:".$_POST['scheme'],"indexMapping","Index Mapping",$_POST['json'],"text/json");
            }else{
                $this->Config->modifyDatastreamText("scheme:".$_POST['scheme'],"indexMapping",$_POST['json'],"text/json");
            }
            echo json_encode($this->Config->getDatastreamVersions("scheme:".$_POST['scheme'], "indexMapping"));
        }else{
            echo "error";
        }
    }

    public function reindexScheme(){
        $this->autoRender = false;

        //load mapping from fedora
        if(!isset($_POST['scheme'])){
            echo 'no scheme specified';
            return;
        }
        $scheme = $_POST['scheme'];
        if($this->Config->isFedoraObject("scheme:$scheme") && $this->Config->isFedoraDatastream("scheme:$scheme","indexMapping")){
            if(isset($_POST['version']))
                $json = $this->Config->getDatastream("scheme:$scheme","indexMapping",$_POST['version']);
            else
                $json = $this->Config->getDatastream("scheme:$scheme","indexMapping");
        }else{
            echo 'no index mapping configured';
            return;
        }

        //delete mapping and put new mapping
        $this->Config->deleteIndexMapping($scheme);
        try{
            $this->Config->putIndexMapping($scheme,$json);
        }catch(InternalErrorException $e){
            echo "Mapping not accepted: ".$e->getMessage();
            return;
        }

        $this->loadModel('XMLObject');
        $response = $this->XMLObject->showAllObjectsXML();
        $xml = Xml::build($response);
        $errormsg = '';
        foreach($xml->resultList->objectFields as $object){
            if(strpos($object->pid,'fedora-system') === false && strpos($object->pid,'islandora') === false && strpos($object->pid,'test:') === false && strpos($object->pid,'scheme:') === false){
                $teiID = $this->XMLObject->getFirstTeiDSID($object->pid);
                if(substr( $teiID, -strlen( $scheme ) ) == $scheme){
                    $return_array = $this->XMLObject->indexLatestVersion($object->pid);
                    if(isset($return_array['http_code']) && $return_array['http_code']!= '201'){
                        echo $object->pid.':'.$return_array['response'];
                        return;
                    }
                }
            }
        }

        echo 'success';
    }

    /********************************************************************************
     *
     *                              View Mapping
     *
     *******************************************************************************/

    public function editView($scheme){
        $this->loadModel('XMLObject');
        $response = $this->XMLObject->showAllObjectsXML();
        $xml = Xml::build($response);
        $corpora = array();
        foreach($xml->resultList->objectFields as $object){
            if(strpos($object->pid,'fedora-system') === false && strpos($object->pid,'islandora') === false && strpos($object->pid,'test:') === false && strpos($object->pid,'scheme:') === false){
                array_push($corpora,$object->pid);
            }
        }
        $this->set("scheme",$scheme);
        $this->set("corpora",$corpora);

        if($this->Config->isFedoraObject("scheme:$scheme") && $this->Config->isFedoraDatastream("scheme:$scheme","viewMapping")){
            $this->set("versions", $this->Config->getDatastreamVersions("scheme:$scheme","viewMapping"));
        }
    }

    public function saveView(){
        $this->autoRender = false;
        if(isset($_POST['json']) && isset($_POST['scheme']) && $this->Config->isFedoraObject("scheme:".$_POST['scheme'])){
            if(!$this->Config->isFedoraDatastream("scheme:".$_POST['scheme'],"viewMapping")){
                $this->Config->addDatastreamText("scheme:".$_POST['scheme'],"viewMapping","View Mapping",$_POST['json'],"text/json");
            }else{
                $this->Config->modifyDatastreamText("scheme:".$_POST['scheme'],"viewMapping",$_POST['json'],"text/json");
            }
            echo json_encode($this->Config->getDatastreamVersions("scheme:".$_POST['scheme'], "viewMapping"));
        }else{
            echo "error";
        }
    }

    public function deleteView(){
        $this->autoRender = false;
        if(isset($_POST['scheme'])){
            $scheme = $_POST['scheme'];
            if($this->Config->isFedoraObject("scheme:$scheme") && $this->Config->isFedoraDatastream("scheme:$scheme","viewMapping")){
                if(isset($_POST['version'])){
                    $this->Config->deleteDatastream("scheme:$scheme","viewMapping",$_POST['version']);
                }else{
                    $this->Config->deleteDatastream("scheme:$scheme","viewMapping");
                }
                echo 'success';
            }else{
                echo "no configuration found";
            }
        }else{
            echo "no scheme specified";
        }
    }

    public function help(){}

    public function loadView(){
        $this->autoRender = false;
        if(isset($_POST['scheme'])){
            $scheme = $_POST['scheme'];
            if($this->Config->isFedoraObject("scheme:$scheme") && $this->Config->isFedoraDatastream("scheme:$scheme","viewMapping")){
                if(isset($_POST['version']))
                    echo $this->Config->getDatastream("scheme:$scheme","viewMapping",$_POST['version']);
                else
                    echo $this->Config->getDatastream("scheme:$scheme","viewMapping");
            }else{
                $view_conf = "No view mapping configured for this scheme. Example:\n";
                $view_conf .= "{\n\t\"ex2-node-1\":{\n\t\t\"1\":{\n\t\t\t\"value\":\"Authorship\"\n\t\t}\n\t}";
                $view_conf .= ",\n\t\"ex2-node-2\":{\n\t\t\"-\":{\n\t\t\t\"value\":\"\$teiCorpus->teiHeader[]->fileDesc->titleStmt->title\"\n\t\t}\n\t}";
                $view_conf .= ",\n\t\"ex2-node-3\":{\n\t\t\"-\":{\n\t\t\t\"value\":\"\$teiHeader->encodingDesc[]->appInfo->application->label\"\n\t\t}\n\t}";
                $view_conf .= ",\n\t\"ex2-node-4\":{\n\t\t\"-\":{\n\t\t\t\"value\":\"\$teiCorpus->teiCorpus->teiHeader[]->fileDesc->titleStmt->title\"\n\t\t}\n\t}";
                $view_conf .= "\n}";
                echo $view_conf;
            }
        }else{
            echo "Error: no scheme specified";
        }
    }
}