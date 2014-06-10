<?php
class XMLObject extends AppModel {

    /************************************************************************
     *
     *              database methods for user rights management
     *
     *************************************************************************/

    public $hasMany = array(
        'CorpusUser' => array(
            'className' => 'CorpusUser',
            'dependent' => true
        ),
        'LdapCorpusUser' => array(
            'className' => 'LdapCorpusUser',
            'dependent' => true
        ),
        'CorpusGroup' => array(
            'className' => 'CorpusGroup',
            'dependent' => true
        )
    );

    public $hasOne = array(
        'Creator' => array(
            'className' => 'CorpusCreator',
            'foreignKey' => 'x_m_l_object_id',
            'dependent' => true
        ),
        'LdapCreator' => array(
            'className' => 'LdapCorpusCreator',
            'foreignKey' => 'x_m_l_object_id',
            'dependent' => true
        )
    );

    /**
     * Creates a new coprus entry in the database and associates it with the users id
     * @param string $name corpus pid
     * @param integer $creator user id
     * @param bool $open open access
     * @param bool $indexing index in elasticsearch
     * @return void
     */
    public function create($name,$creator,$open,$indexing){
        $this->set(array(
            'name' => $name,
            'openaccess' => $open,
            'indexing' => $indexing
        ));
        $this->save();
        $this->Creator->set(array(
            'x_m_l_object_id' => $this->id,
            'user_id' => $creator
        ));
        $this->Creator->save();
    }

    /**
     * Creates a new coprus entry in the database and associates it with the ldap user account
     * @param $name
     * @param $creator_name
     * @param $open
     * @param $indexing
     */
    public function createLdap($name,$creator_name,$open,$indexing){
        $this->set(array(
            'name' => $name,
            'openaccess' => $open,
            'indexing' => $indexing
        ));
        $this->save();

        $this->LdapCreator->set(array(
            'x_m_l_object_id' => $this->id,
            'user_name' => $creator_name
        ));
        $this->LdapCreator->save();
    }

    public function deleteCorpus($name){
        $this->deleteAll(array(
            'XMLObject.name' => $name
        ));
    }

    public function getCreator($corpus_name){
        $corpus = $this->find('first', array(
            'conditions' => array('XMLObject.name' => $corpus_name)
        ));
        $corpus_creator = $this->Creator->find('first',array(
            'conditions' => array(
                'x_m_l_object_id' => $corpus['XMLObject']['id']
        )));
        if($corpus_creator !== false){
            return $corpus_creator['User']['username'];
        }else{
              $corpus_creator = $this->LdapCreator->find('first',array(
                'conditions' => array(
                    'x_m_l_object_id' => $corpus['XMLObject']['id']
                )));
            return $corpus_creator['LdapCreator']['user_name'];
        }
    }

    public function isCorpusCreator($user_id,$user_name,$corpus_name){
        $corpus = $this->find('first', array(
            'conditions' => array('XMLObject.name' => $corpus_name)
        ));
        $corpus_creator = $this->Creator->find('first',array(
            'conditions' =>array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'user_id' => $user_id
        )));
        if($corpus_creator){
            return true;
        }else{
            $corpus_creator = $this->LdapCreator->find('first',array(
                'conditions' =>array(
                    'x_m_l_object_id' => $corpus['XMLObject']['id'],
                    'user_name' => $user_name
                )));
            if($corpus_creator){
                return true;
            }else{
                return false;
            }
        }
    }

    public function isCreator($user_id){
        $corpus_creator = $this->Creator->find('first',array(
            'conditions' =>array(
                'user_id' => $user_id
            )));
        if($corpus_creator){
            return true;
        }else{
            return false;
        }
    }

    public function setGroup($name,$group_id){
        $corpus = $this->find('first', array(
            'conditions' => array('XMLObject.name' => $name)
        ));
        $this->CorpusGroup->set(array(
            'x_m_l_object_id' => $corpus['XMLObject']['id'],
            'group_id' => $group_id
        ));
        $this->CorpusGroup->save();
    }

    public function unsetGroup($name,$group_id){
        $this->CorpusGroup->deleteAll(array(
            'XMLObject.name' => $name,
            'CorpusGroup.group_id' => $group_id
        ));
    }

    public function getGroups($corpus_name){
        $corpus_groups = $this->find('all',array(
            'conditions' => array('XMLObject.name' => $corpus_name),
            'recursive' => 2
        ));
        $grouplist = array();
        if($corpus_groups && $corpus_groups[0]['CorpusGroup']){
            foreach($corpus_groups[0]['CorpusGroup'] as $group){
                if(isset($group['GroupInCorpusGroup']['name']))
                array_push($grouplist,$group['GroupInCorpusGroup']['name']);
            }
        }
        return $grouplist;
    }

    public function addLdapCorpusUser($corpus_name, $user_name){
        $corpus = $this->find('first',array('conditions' => array( 'name' => $corpus_name)));
        $corpusUser = $this->LdapCorpusUser->find('first',array(
            'conditions' => array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'user_name' => $user_name
            )));
        if($corpusUser){
            return 'User already allowed.';
        }else{
            $this->LdapCorpusUser->set(array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'user_name' => $user_name
            ));
            $this->LdapCorpusUser->save();
            return 'User added.';
        }
    }

    public function removeLdapCorpusUser($corpus_name, $user_name){
        $corpus = $this->find('first',array('conditions' => array( 'name' => $corpus_name)));
        $removed = $this->LdapCorpusUser->deleteAll( array(
            'x_m_l_object_id' => $corpus['XMLObject']['id'],
            'user_name' => $user_name
        ));
        if($removed){
            return 'success';
        }else{
            return 'User not found.';
        }
    }

    public function getLdapUsers($corpus_name){
        $corpus_users = $this->find('all',array(
            'conditions' => array('XMLObject.name' => $corpus_name),
            'recursive' => 2
        ));
        $userlist = array();
        if($corpus_users && $corpus_users[0]['LdapCorpusUser']){
            foreach($corpus_users[0]['LdapCorpusUser'] as $user){
                if(isset($user['user_name']))
                    array_push($userlist,$user['user_name']);
            }
        }
        return $userlist;
    }

    public function getUsers($corpus_name){
        $corpus_users = $this->find('all',array(
            'conditions' => array('XMLObject.name' => $corpus_name),
            'recursive' => 2
        ));
        $userlist = array();
        if($corpus_users && $corpus_users[0]['CorpusUser']){
            foreach($corpus_users[0]['CorpusUser'] as $user){
                if(isset($user['UserInCorpusUser']['username']))
                    array_push($userlist,$user['UserInCorpusUser']['username']);
            }
        }
        return $userlist;
    }

    public function addCorpusUser($corpus_name, $user_id){
        $corpus = $this->find('first',array('conditions' => array( 'name' => $corpus_name)));
        $corpusUser = $this->CorpusUser->find('first',array(
            'conditions' => array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'user_id' => $user_id
            )));
        if($corpusUser){
            return 'User already allowed.';
        }else{
            $this->CorpusUser->set(array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'user_id' => $user_id
            ));
            $this->CorpusUser->save();
            return 'User added.';
        }
    }

    public function removeCorpusUser($corpus_name, $user_id){
        $corpus = $this->find('first',array('conditions' => array( 'name' => $corpus_name)));
        $removed = $this->CorpusUser->deleteAll( array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'user_id' => $user_id
            ));
        if($removed){
            return 'success';
        }else{
            return 'User not found.';
        }
    }

    public function isCorpusUser($user_id,$user_name,$corpus_name){
        $corpus = $this->find('first',array('conditions' => array( 'name' => $corpus_name)));
        $corpusUser = $this->CorpusUser->find('first',array(
            'conditions' => array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'user_id' => $user_id
            )));
        if($corpusUser){
            return true;
        }else{
            $corpusUser = $this->LdapCorpusUser->find('first',array(
                'conditions' => array(
                    'x_m_l_object_id' => $corpus['XMLObject']['id'],
                    'user_name' => $user_name
                )));
            if($corpusUser){
                return true;
            }else{
                return false;
            }
        }

    }

    public function addCorpusGroup($corpus_name, $group_id){
        $corpus = $this->find('first',array('conditions' => array( 'name' => $corpus_name)));
        $corpusGroup = $this->CorpusGroup->find('first',array(
            'conditions' => array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'group_id' => $group_id
            )));
        if($corpusGroup){
            return 'Group already allowed.';
        }else{
            $this->CorpusGroup->set(array(
                'x_m_l_object_id' => $corpus['XMLObject']['id'],
                'group_id' => $group_id
            ));
            $this->CorpusGroup->save();
            return 'Group added.';
        }
    }

    public function removeCorpusGroup($corpus_name, $group_id){
        $corpus = $this->find('first',array('conditions' => array( 'name' => $corpus_name)));
        $removed = $this->CorpusGroup->deleteAll( array(
            'x_m_l_object_id' => $corpus['XMLObject']['id'],
            'group_id' => $group_id
        ));
        if($removed){
            return 'success';
        }else{
            return 'Group not found.';
        }
    }

    public function isCorpusGroup($group_id,$corpus_name){
        $corpus = $this->find('first',array('conditions' => array( 'name' => $corpus_name)));
        $corpusGroup = $this->CorpusGroup->find('first',array(
            'conditions' => array(
            'x_m_l_object_id' => $corpus['XMLObject']['id'],
            'group_id' => $group_id
        )));
        if($corpusGroup){
            return true;
        }else{
            return false;
        }
    }

    public function isOpenAccess($corpus_name){
        $corpus = $this->find('first',array('conditions' => array(
            'name' => $corpus_name,
            'openaccess' => 1
        )));
        if($corpus){
            return true;
        }else{
            return false;
        }
    }

    public function toggleOpenAccess($corpus_name){
        $corpus = $this->find('first',array('conditions' => array(
            'name' => $corpus_name
        )));
        if($corpus){
            $id = $corpus['XMLObject']['id'];
            if($corpus['XMLObject']['openaccess'] === true){
                $this->unbindModel(array('hasOne' => array('Creator')));
                $this->updateAll(array('openaccess' => false, 'name' => '\''.$corpus_name.'\''),array('XMLObject.id' => $id));
            }else{
                $this->unbindModel(array('hasOne' => array('Creator')));
                $this->updateAll(array('openaccess' => true,'name' => '\''.$corpus_name.'\'' ),array('XMLObject.id' => $id));
            }
        }
    }

    public function toggleIndexing($corpus_name){
        $corpus = $this->find('first',array('conditions' => array(
            'name' => $corpus_name
        )));
        if($corpus){
            $id = $corpus['XMLObject']['id'];
            if($corpus['XMLObject']['indexing'] === true){
                $this->unbindModel(array('hasOne' => array('Creator')));
                $this->updateAll(array('indexing' => false, 'name' => '\''.$corpus_name.'\''),array('XMLObject.id' => $id));
            }else{
                $this->unbindModel(array('hasOne' => array('Creator')));
                $this->updateAll(array('indexing' => true,'name' => '\''.$corpus_name.'\'' ),array('XMLObject.id' => $id));
            }
        }
    }

    public function doIndexing($corpus_name){
        $corpus = $this->find('first',array('conditions' => array(
            'name' => $corpus_name,
            'indexing' => 1
        )));
        if($corpus){
            return true;
        }else{
            return false;
        }
    }

    public function setUser($name,$user_id){
        $corpus = $this->find('first', array(
            'conditions' => array('XMLObject.name' => $name)
        ));
        $this->CorpusUser->set(array(
            'x_m_l_object_id' => $corpus['XMLObject']['id'],
            'user_id' => $user_id
        ));
        $this->CorpusUser->save();
    }

    public function unsetUser($name,$user_id){
        $this->CorpusUser->deleteAll(array(
            'XMLObject.name' => $name,
            'CorpusUser.user_id' => $user_id
        ));
    }

    /************************************************************************
     *
     *              TEI-header validation
     *
     *************************************************************************/

    private function libxml_display_errors() { 
        $errors = libxml_get_errors();
        $errortext = "";
        foreach ($errors as $error) {
            $errortext .= XMLObject::libxml_display_error($error);
        }
        libxml_clear_errors();
        return $errortext;
        }
    
    private function libxml_display_error($error) { 
        $return = "<br/>\n";
        switch ($error->level) { 
            case LIBXML_ERR_WARNING: 
                $return .= "<b>Warning $error->code</b>: "; 
                break; 
            case LIBXML_ERR_ERROR: 
                $return .= "<b>Error $error->code</b>: "; 
                break; 
            case LIBXML_ERR_FATAL: 
                $return .= "<b>Fatal Error $error->code</b>: "; 
                break; 
        } 
        $return .= trim($error->message); 
    
        /*if ($error->file) {
            $return .= " in <b>$error->file</b>"; 
        } */
        $return .= " on line <b>$error->line</b>\n"; 
    
        return $return; 
    }

    private function HandleXmlError($errno, $errstr, $errfile, $errline)
    {
        if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0))
        {
            throw new DOMException($errstr);
        }
        else
            return false;
    }

    public function makeValidationFiles($teiAll,$folder){
        if($teiAll->teiCorpus->teiCorpus){
            $teiCorpus = $teiAll->teiHeader;
            /*set_error_handler(function($number, $error){
                if (preg_match('/^DOMDocument::loadXML\(\): (.+)$/', $error, $m) === 1) {
                    throw new Exception($m[1]);
                }
            });*/
            //TODO: error handler
            //set_error_handler('XMLObject::HandleXmlError');
            $teiCorpusDom =  new DOMDocument('1.0', 'utf-8');
            $teiCorpusDom->loadXML($teiCorpus->asXML());
            /*}catch(Exception $e){
                echo 'Could not load XML. XML  may be not well-formed. Error:'.$e;
            }*/
            //restore_error_handler();
            $coprusHeaderNode = $teiCorpusDom->getElementsByTagName('teiHeader')->item(0);
            $teiDOM = new DOMDocument('1.0', 'UTF-8');
            $imported = $teiDOM->importNode($coprusHeaderNode,true);
            $node = $teiDOM->createElement('TEI');
            $node->setAttribute('xmlns','http://www.tei-c.org/ns/1.0');
            $node->appendChild($imported);
            $textNode = $teiDOM->createElement('text');
            $node->appendChild($textNode);
            $teiDOM->appendChild($node);

            XMLObject::rrmdir('./'.$folder);
            mkdir('./'.$folder.'/Corpus',0765,true);
            $tempFile = time() . '-' . rand() . '-teiCorpus.tmp';
            $teiDOM->save('./'.$folder.'/Corpus/'.$tempFile);

            //documents
            foreach($teiAll->teiCorpus->teiHeader as $docHeader){
                //load document xml
                $teiDocumentsDom =  new DOMDocument('1.0', 'utf-8');
                $teiDocumentsDom->loadXML($docHeader->asXML());
                //get teiheader node
                $documentHeaderNode = $teiDocumentsDom->getElementsByTagName('teiHeader')->item(0);
                //create dom for validation
                $teiDOM = new DOMDocument('1.0', 'UTF-8');
                //import teiheader node from document xml into new dom document
                $imported = $teiDOM->importNode($documentHeaderNode,true);
                //create TEI root node, append xml document + textnode
                $node = $teiDOM->createElement('TEI');
                $node->setAttribute('xmlns','http://www.tei-c.org/ns/1.0');
                $node->appendChild($imported);
                $textNode = $teiDOM->createElement('text');
                $node->appendChild($textNode);
                $teiDOM->appendChild($node);
                if(!is_dir('./'.$folder.'/Documents')) mkdir('./'.$folder.'/Documents',0765,true);
                $tempFile = time() . '-' . rand() . '-teiDocument.tmp';
                $teiDOM->save('./'.$folder.'/Documents/'.$tempFile);
            }

            //preparations
            foreach($teiAll->teiCorpus->teiCorpus->teiHeader as $prepHeader){
                //load document xml
                $teiPrepsDom =  new DOMDocument('1.0', 'utf-8');
                $teiPrepsDom->loadXML($prepHeader->asXML());
                //get teiheader node
                $prepHeaderNode = $teiPrepsDom->getElementsByTagName('teiHeader')->item(0);
                //create dom for validation
                $teiDOM = new DOMDocument('1.0', 'UTF-8');
                //import teiheader node from document xml into new dom document
                $imported = $teiDOM->importNode($prepHeaderNode,true);
                //create TEI root node, append xml document + textnode
                $node = $teiDOM->createElement('TEI');
                $node->setAttribute('xmlns','http://www.tei-c.org/ns/1.0');
                $node->appendChild($imported);
                $textNode = $teiDOM->createElement('text');
                $node->appendChild($textNode);
                $teiDOM->appendChild($node);
                if(!is_dir('./'.$folder.'/Preparations')) mkdir('./'.$folder.'/Preparations',0765,true);
                $tempFile = time() . '-' . rand() . '-teiPreparation.tmp';
                $teiDOM->save('./'.$folder.'/Preparations/'.$tempFile);
            }
        }
        return '';

    }

    public function deleteValidationFolder($folder){
        XMLObject::rrmdir($folder);
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") XMLObject::rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
    
    public function validateTeiCorpusHeader($folder,$current_scheme = null){
        if($current_scheme == null){
            $conf = Configure::read('SchemeConfig');
            $current_scheme = $conf['current_scheme'];
        }
        $tempTeiDom = new DOMDocument();
        $dp = opendir($folder.'/Corpus');
        $tempFile = '';
        while ($file = readdir($dp)) {
            if ($file == '.'){
                continue;
            }elseif($file == '..'){
                continue;
            }elseif(is_dir($file)){
                continue;
            }else{
                $tempFile = $file;
                $tempTeiDom->load($folder.'/Corpus/'.$file);
            }
        }
        if (is_file($folder.'/Corpus/'.$tempFile))
        {
            unlink($folder.'/Corpus/'.$tempFile);
        }
        closedir($dp);
        libxml_use_internal_errors(true);

        if(XMLObject::isFedoraDatastream("scheme:$current_scheme","teiODD_Corpus")){
            $scheme = XMLObject::getDatastreamContent("scheme:$current_scheme","teiODD_Corpus");
            if($tempTeiDom->relaxNGValidateSource($scheme)){
                $response = 'valid';
            }else{
                $response = XMLObject::libxml_display_errors();
            }
        }else{
            $response = "no corpus scheme available";
        }
        if(!isset($response)) $response = array('error' => 'unknown');
        return $response;
    }

    public function validateTeiDocumentHeader($folder,$current_scheme = null){
        if($current_scheme == null){
            $conf = Configure::read('SchemeConfig');
            $current_scheme = $conf['current_scheme'];
        }
        if(!XMLObject::isFedoraDatastream("scheme:$current_scheme","teiODD_Document"))
            return "no corpus scheme available";
        $scheme = XMLObject::getDatastreamContent("scheme:$current_scheme","teiODD_Document");
        $tempTeiDom = new DOMDocument();
        if(!is_dir($folder.'/Documents')) return array('directory' => 'error');
        $dp = opendir($folder.'/Documents');
        $valid = true;
        $response = array();

        while ($file = readdir($dp)) {
            if ($file == '.'){
                continue;
            }elseif($file == '..'){
                continue;
            }elseif(is_dir($file)){
                continue;
            }else{
                $tempFile = $file;
                $tempTeiDom->load($folder.'/Documents/'.$file);
                if (is_file($folder.'/Documents/'.$tempFile))
                {
                    unlink($folder.'/Documents/'.$tempFile);
                }
                libxml_use_internal_errors(true);
                $docname = $tempTeiDom->getElementsByTagName('teiHeader')->item(0)->getElementsByTagName('fileDesc')->item(0)->getAttribute('xml:id');

                if(!$tempTeiDom->relaxNGValidateSource($scheme)){
                    $response[$docname] = XMLObject::libxml_display_errors();
                    $valid = false;
                }
            }
        }
        closedir($dp);
        if($valid){
            return 'valid';
        }else{
            return $response;
        }
 }

    public function validateTeiPreparationsHeader($folder,$current_scheme = null){
        if($current_scheme == null){
            $conf = Configure::read('SchemeConfig');
            $current_scheme = $conf['current_scheme'];
        }
        if(!XMLObject::isFedoraDatastream("scheme:$current_scheme","teiODD_Preparation"))
            return "no corpus scheme available";
        $scheme = XMLObject::getDatastreamContent("scheme:$current_scheme","teiODD_Preparation");

        $tempTeiDom = new DOMDocument();
        if(!is_dir($folder.'/Preparations')) return array('directory' => 'error');
        $dp = opendir($folder.'/Preparations');
        $valid = true;
        $response = array();

     while ($file = readdir($dp)) {
         if ($file == '.'){
             continue;
         }elseif($file == '..'){
             continue;
         }elseif(is_dir($file)){
             continue;
         }else{
             $tempFile = $file;
             $tempTeiDom->load($folder.'/Preparations/'.$file);
             if (is_file($folder.'/Preparations/'.$tempFile))
             {
                 unlink($folder.'/Preparations/'.$tempFile);
             }
             libxml_use_internal_errors(true);
             $prepname = $tempTeiDom->getElementsByTagName('teiHeader')->item(0)->getElementsByTagName('fileDesc')->item(0)->getElementsByTagName('titleStmt')->item(0)->getElementsByTagName('title')->item(0)->getAttribute('corresp');

             if(!$tempTeiDom->relaxNGValidateSource($scheme)){
                 $response[$prepname] = XMLObject::libxml_display_errors();
                 $valid = false;
             }
         }
     }
     closedir($dp);
     if($valid){
         return 'valid';
     }else{
         return $response;
     }
 }










 /**********************************************************
  *
  *                 Fedora REST API
  *
 ***********************************************************/


    public function ingestFedora($pid,$label,$files,$formatname,$ownerId){ #header , formatarray, filearray
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        if($pid != '' && $label != ''){
            //create fedora object
            if(!XMLObject::pidvalidate($pid)){
                $returnmsg['response'][] = 'Pid der Form namespace-id:object-id angeben';
                $returnmsg['http_code'][] = 500;
                return $returnmsg;
            }
            $pid = urlencode($pid);
            $label = urlencode($label);
            $ownerId = urlencode($ownerId);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid?label=$label&ownerId=$ownerId" );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $returnmsg['response'][]=$response;
            $returnmsg['http_code'][]=$http_code;
            curl_close($ch);
            if($http_code != 201) return $returnmsg;
            if(!XMLObject::is_array_empty($files)){
                if(isset($files['teifile']) && !XMLObject::is_array_empty($files['teifile']['tmp_name'])){
                    $conf = Configure::read('SchemeConfig');
                    $current_scheme = $conf['current_scheme'];
                    $fileName = '/tmp/'.basename($files['teifile']['tmp_name']);
                    //upload tei header
                    $dslabel = urlencode("TEI Header");
                    $dsformatname = urlencode('TEI-header_version1_'.$current_scheme);
                    $response = XMLObject::addDatastream($pid,$fileName,$dsformatname,$dslabel);
                    $returnmsg['response'][]=$response['response'];
                    $returnmsg['http_code'][]=$response['http_code'];
                    if($response['http_code'] != 201) return $returnmsg;
                    //set tei header version/scheme
                    $response = XMLObject::updateTeiVersionFile($pid,$dsformatname);
                    $returnmsg['response'][]=$response['response'];
                    $returnmsg['http_code'][]=$response['http_code'];
                    if($response['http_code'] != 201) return $returnmsg;
                }
                if(isset($files['userfile']) && !XMLObject::is_array_empty($files['userfile']['tmp_name'])){
                    //upload datastreams
                    for($i = 0; $i<count($files['userfile']['tmp_name']); $i++ ){
                        if($files['userfile']['tmp_name'][$i]!=''){
                            $fileName = '/tmp/'.basename($files['userfile']['tmp_name'][$i]);
                            $dslabel = urlencode($files['userfile']['name'][$i]);
                            $dsformatname = $formatname[$i];
                            $response = XMLObject::addDatastream($pid,$fileName,$dsformatname,$dslabel);
                            $returnmsg['response'][]=$response['response'];
                            $returnmsg['http_code'][]=$response['http_code'];
                            if($response['http_code'] != 201) return $returnmsg;
                        }
                    }
                }
            }
            return $returnmsg;
        }else{
            $returnmsg['http_code'][] = 500;
            $returnmsg['response'][]='pid / label empty';
            return $returnmsg;
        }
    }

    /**
     * Deletes the Fedora object of $pid
     * @param $pid
     * @return mixed
     */
    public function deleteFedora($pid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }

    /**
     * Modifies the meta-data of a fedora object. Use hash keys 'label', 'owner' and 'state'.
     * @param $data
     * @return mixed
     */
    public function modifyObjectFedora($data){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $param = '';
        if(isset($data['label'])){
            if($param != '') $param .= '&';
            $param .= 'label='.urlencode($data['label']);
        }
        if(isset($data['owner'])){
            if($param != '') $param .= '&';
            $param .= 'ownerId='.urlencode($data['owner']);
        }
        if(isset($data['state'])){
            if($param != '') $param .= '&';
            $param .= 'state='.$data['state'];
        }
        if($param != '') $param = '?'.$param;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/".$data['pid'].$param);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }

    /**
     * Modifies the meta-data of a fedora datastream. Use hash keys 'label', 'mimeType' ,'versionable' and 'state'.
     * @param $data
     * @return mixed
     */
    public function modifyDatastreamFedora($data){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $param = '';
        if(isset($data['label'])) $param .= 'dsLabel='.urlencode($data['label']);
        if(isset($data['mimeType'])){
            if($param != '') $param .= '&';
            $param .= 'mimeType='.urlencode($data['mimeType']);
        }
        if(isset($data['state'])){
            if($param != '') $param .= '&';
            $param .= 'dsState='.urlencode($data['state']);
        }
        if(isset($data['versionable'])){
            if($param != '') $param .= '&';
            $param .= 'versionable='.urlencode($data['versionable']);
        }
        if($param != '') $param = '?'.$param;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        #curl_setopt($ch, CURLOPT_URL, "http://depot1-7.cms.hu-berlin.de:8080/fedora/objects/".$data['pid']."/datastreams/".$data['dsid'].$param);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/".$data['pid']."/datastreams/".$data['dsid'].$param);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT");
        #curl_setopt($ch, CURLOPT_USERPWD, 'fedoraAdmin:fedoraAdmin');
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;

    }

    public function modifyDatastream($pid,$filename,$dsformatname,$dslabel){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        if($filename == ''){
            return XMLObject::resultMessage("Can not modify Datastream. Filename empty.",500);
        }
        $handle = fopen($filename, "r");
        if(filesize($filename)>0){
            $XPost = fread($handle, filesize($filename));
        }else{
            return XMLObject::resultMessage("Can not modify Datastream. File empty.",500);
        }
        $mime_type = mime_content_type($filename);
        fclose($handle);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        #curl_setopt($ch, CURLOPT_URL, "depot1-7.cms.hu-berlin.de:8080/fedora/objects/$pid/datastreams/$dsformatname?dsLabel=$dslabel"); // set url to post to
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsformatname?dsLabel=$dslabel"); // set url to post to

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT");
        #curl_setopt($ch, CURLOPT_USERPWD, 'fedoraAdmin:fedoraAdmin');
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: $mime_type"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40); // times out after 4s
        curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost); // add POST fields
        curl_setopt($ch, CURLOPT_POST, true);
        #curl_setopt($ch, CURLOPT_NOBODY, true);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }


    /**
     * returns datastreams metadata
     * @param $pid
     * @param $dsid
     * @param null $asOfDateTime
     * @return mixed
     */
    public function getDatastream($pid, $dsid, $asOfDateTime = null){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        if($asOfDateTime){
            $url = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid?format=xml&asOfDateTime=".$asOfDateTime;
        }
        else {
            $url = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid?format=xml";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }

    /**
     * Returns the content of a fedora datastream
     * @param $pid
     * @param $dsid
     * @param null $asOfDateTime
     * @return mixed
     * @throws NotFoundException
     * @throws InternalErrorException
     */
    public function getDatastreamContent($pid, $dsid, $asOfDateTime = null){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $pid = urlencode($pid);
        $dsid = urlencode($dsid);
        if($asOfDateTime){
            $url = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid/content?download=true&asOfDateTime=".$asOfDateTime;
        }else {
            $url = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid/content?download=true";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($http_code !== 200){
            if($http_code === 404){
                throw new NotFoundException();
            }else{
                if(Configure::read('debug') > 0)
                    throw new InternalErrorException('Internal Error: '.$response);
                else
                    throw new InternalErrorException();
            }
        }
        return $response;
    }

    /**
     * Returns the history of a fedora datastream in xml
     * @param $pid
     * @param $dsid
     * @return mixed
     */
    public function getDatastreamHistory($pid, $dsid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $ch = curl_init();
        $pid = $this->formatPID($pid);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid/history?format=xml");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }

    /**
     * Returns the meta-data of the datatreams of an object in fedora
     * @param $pid
     * @return mixed
     */
    public function getDatastreamsXML($pid)
    {
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $pid = $this->formatPID($pid);
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams?format=xml";
        $response = call_user_func_array('file_get_contents', $url);
        return XMLObject::resultMessage($response,substr($http_response_header[0], 9, 3));
    }


    public function addDatastream($pid,$filename,$dsformatname,$dslabel,$mime_type = null){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $handle = fopen($filename, "r");
        $XPost = fread($handle, filesize($filename));
        fclose($handle);
        if($mime_type == null){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo,$filename);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsformatname?dsLabel=$dslabel&controlGroup=M"); // set url to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:$mime_type"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost);
        curl_setopt($ch, CURLOPT_POST, true);
        $result_array = array(
            'response' => curl_exec($ch),
            'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE)
        );
        curl_close($ch);
        return $result_array;
    }

    public function addDatastreams($pid, $files, $formatname, $parameters = null){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        if($parameters){
            $i=0;
            foreach($parameters as $parameter){
                $param[$i] = '';
                foreach($parameter as $key => $value){
                    if($key != 'dsLabel'){ #dsLabel is set separately; if not set in parameters, taken from filename
                        $param[$i] .= '&'.$key.'='.$value;
                    }
                }
                $i++;
            }
        }
        $result_array['response'] = '';
        $result_array['http_code'] = '201';
        if(!XMLObject::is_array_empty($files)){
            for($i = 0; $i<count($files['userfile']['tmp_name']); $i++ ){
                if($files['userfile']['tmp_name'][$i]!=''){
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo,$files['userfile']['tmp_name'][$i]);
                    $filename = '/tmp/'.basename($files['userfile']['tmp_name'][$i]);
                    $handle = fopen($filename, "r");
                    $XPost = fread($handle, filesize($filename));
                    fclose($handle);
                    $dslabel = @$parameters[$i]['dsLabel']?:urlencode($files['userfile']['name'][$i]);
                    //TODO: validieren
                    $dsformatname = $formatname[$i];
                    if(!isset($param[$i]))$param[$i] = '';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                    curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsformatname?dsLabel=$dslabel".$param[$i].'&controlGroup=M'); // set url to post to
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:".$mime_type));
                    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost);
                    curl_setopt($ch, CURLOPT_POST, true);
                    $result_array['response'] .= curl_exec($ch);
                    if(curl_getinfo($ch, CURLINFO_HTTP_CODE)!='201')$result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                }
            }
        }
        return $result_array;
    }

    /**
     * Deletes a datatream of a fedora object
     * @param $pid
     * @param $dsid
     * @return mixed
     */
    public function purgeDatastream($pid,$dsid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        if(strpos($dsid,'TEI-header') === 0) XMLObject::updateTeiVersionFile($pid,$dsid,true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }

    /**
     * Deletes a version of a fedora datastream specified by $date.
     * @param $pid
     * @param $dsID
     * @param $date
     * @return mixed
     */
    public function purgeDatastreamVersionFedora($pid,$dsID,$date){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsID?startDT=".urlencode($date)."&endDT=".urlencode($date));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }

    //TODO: configure max results per page
    public function showAllObjectsXML() {
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $url = "$fedoraAdress/fedora/objects?terms=*&pid=true&subject=true&label=true&resultFormat=xml&maxResults=80";
        $response = file_get_contents($url);
        return $response;
    }

    /**
     * Checks if the datastream is present in fedora.
     * @param string $pid pid of the fedora object
     * @param string $dsid id of the datastream
     * @return bool
     */
    public function isFedoraDatastream($pid,$dsid){
        $pid = urlencode($pid);
        $dsid = urlencode($dsid);
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $url = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid?format=xml";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code !== 200){
            return false;
        }else {
            return true;
        }
    }

    /**
     * Checks if an object of $pid exists in fedora.
     * @param $pid pid of the fedora object
     * @return bool
     */
    public function isFedoraObject($pid){
        $pid = urlencode($pid);
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];

        $pid = urlencode($pid);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/objectXML");
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code === 200)
            return true;
        else
            return false;
    }

 /*Warning
 Since PHP 5.2.12, the max_file_uploads configuration setting acts as a limit on the number of files that can be uploaded in one request. You will need to ensure that your form does not try to upload more files in one request than this limit.
  */








    /*****************************************************************************
     *
     *              Index TEI-header in elasticsearch
     *
     ******************************************************************************/


    /**
     * indexes latest tei-header version from teiVersion-File, removes other
     * @param $pid
     * @return array (response => string, http_code => integer)
     */
    public function indexLatestVersion($pid){
        $teiname = XMLObject::getFirstTeiVersion($pid);
        //remove date
        $version = substr($teiname,0,strrpos($teiname,'_'));
        //get scheme
        $scheme = substr($version,strrpos($version,'_')+1);
        //remove old versions
        XMLObject::deleteFromIndex($pid);
        if($teiname != null){
            $response=XMLObject::getObjectTEIXmlVersion($pid,$version);
            $file = tempnam("/tmp", "teiVersion");
            file_put_contents($file,$response['response']);
            $json = XMLObject::javaXSLT($file);
            unlink($file);
            $return_array = XMLObject::indexJson($json,$pid,$scheme);
            return $return_array;
        }else{
            $return_array['response']= 'No TEI-header left for indexing';
            $return_array['http_code'] = 500;
        }
        return $return_array;
    }

    public function indexJson($json, $id, $current_scheme = null){
        if($current_scheme == null){
            $confScheme = Configure::read('SchemeConfig');
            $current_scheme = $confScheme['current_scheme'];
        }
        $confIndex = Configure::read('IndexConfig');
        $index_name = $confIndex['index_name'];
        $index_address = $confIndex['index_address'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$index_address/$index_name/$current_scheme/".urlencode($id));
        curl_setopt($ch, CURLOPT_PORT, "9200");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, true);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }

    public function deleteFromIndex($id,$index_name = null, $type = null){
        if($index_name == null){
            $conf = Configure::read('IndexConfig');
            $index_name = $conf['index_name'];
            $index_address = $conf['index_address'];
        }
        if($type == null){
            $url = "$index_address/$index_name/_query";
        }else{
            $url = "$index_address/$index_name/$type/_query";
        }
        $json = '{"term":{"_id" : "'.$id.'"}}';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, "9200");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        $result_array['response'] = curl_exec($ch);
        $result_array['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result_array;
    }
	












    /***************************************************************************
     *
     *                  TEI-HEADER
     *
     ****************************************************************************/
    /**
     * Returns the first Tei-Header-pid + date
     * @param string $pid
     * @return string Tei-header_@version_@scheme_@date
     */
    public function getFirstTeiVersion($pid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/teiVersion/content";
        $versionFileContent = @call_user_func_array('file_get_contents', $url);
        if($versionFileContent === false || $versionFileContent == '') return null;
        if(stripos($versionFileContent,';') !==false){
            $versionFileContent = substr($versionFileContent,0,stripos($versionFileContent,';'));
        }
        return $versionFileContent."_".XMLObject::getFirstTeiDate($pid,$versionFileContent);
    }

    public function getFirstTeiDSID($pid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/teiVersion/content";
        $versionFileContent = @call_user_func_array('file_get_contents', $url);
        if($versionFileContent === false || $versionFileContent == '') return null;
        if(stripos($versionFileContent,';') !==false){
            $versionFileContent = substr($versionFileContent,0,stripos($versionFileContent,';'));
        }
        return $versionFileContent;
    }

    public function getFirstTeiDate($pid,$teiVersion){
        $response = XMLObject::getDatastreamHistory($pid,$teiVersion);
        $xml = Xml::build($response['response']);
        //dot not valid in url -> replace by :
        return str_replace('.',':',$xml->datastreamProfile[0]->dsCreateDate);
    }

    public function isTeiDate($pid,$teiVersion,$date){
        $response = XMLObject::getDatastreamHistory($pid,$teiVersion);
        $xml = Xml::build($response['response']);
        foreach($xml->datastreamProfile as $dataStream){
            //dot not valid in url -> replace by :
            if(str_replace('.',':',$dataStream->dsCreateDate) == $date)
                return true;
        }
        return false;
    }

    public function getObjectTEIXml($pid,$version){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $pid = $this->formatPID($pid);
        if($version == null){
            $teiname = XMLObject::getFirstTeiVersion($pid);
        }else{
            $teiname = $version;
        }
        var_dump($teiname);
        $url[] = "$fedoraAdress/fedora/objects/".$pid."/datastreams/".$teiname."/content";
        $response = @call_user_func_array('file_get_contents', $url);
        $result_array['http_code'] = substr($http_response_header[0], 9, 3);
        $result_array['response'] = $response;
        return $result_array;
    }

    /**
     * @param $pid pid of the object
     * @param null $version datastream id of the tei file
     * @param null $date optional
     * @return mixed
     */

    public function getObjectTEIXmlVersion($pid,$version = null,$date = null){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $pid = $this->formatPID($pid);

        if($version==null){
            $version = XMLObject::getFirstTeiVersion($pid);
            $date = substr($version,strrpos($version,'_')+1);
            $version = substr($version,0,strrpos($version,'_'));
        }
        if($date === null){
            $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$version/content";
        }else{
            //set dot for milliseconds to comply with fedora date scheme
            $date = substr($date,0,strrpos($date,':')).'.'.substr($date,strrpos($date,':')+1);
            $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$version/content?asOfDateTime=$date";
        }


        $response = @call_user_func_array('file_get_contents', $url);
        $result_array['http_code'] = substr($http_response_header[0], 9, 3);
        $result_array['response'] = $response;
        return $result_array;
    }


    /**
     * @return array( Tei-header_@version_@scheme_@date, ....)
     */
    public function getTeiVersions($pid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/teiVersion/content";
        $versionFileContent = @call_user_func_array('file_get_contents', $url);
        while(stripos($versionFileContent,';')){
            $versions[] = substr($versionFileContent,0,stripos($versionFileContent,';'));
            $versionFileContent = substr($versionFileContent,stripos($versionFileContent,';')+1);
        }
        $versions[] = $versionFileContent;
        foreach($versions as $version){
            $response = XMLObject::getDatastreamHistory($pid,$version);
            $xml = Xml::build($response['response']);
            foreach($xml->datastreamProfile as $profile){
                $versionsHistory[] = $version."_".str_replace('.',':',$profile->dsCreateDate);
            }
        }
        return $versionsHistory;
    }

    public function getObjectXML($pid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        if($this->pidvalidate($pid)){
            $url[] = "$fedoraAdress/fedora/objects/$pid?format=xml";
            $response = @call_user_func_array('file_get_contents', $url);
            $result_array['http_code'] = substr($http_response_header[0], 9, 3);
            $result_array['response'] = $response;
            return $result_array;
        }else{
            $result_array['http_code'] = 'novalidpid';
            $result_array['response'] = null;
            return $result_array;
        }
    }

    public function addTei($pid,$dslabel = null,$fileName,$current_scheme = null){
        if($current_scheme == null){
            $conf = Configure::read('SchemeConfig');
            $current_scheme = $conf['current_scheme'];
        }

        //upload tei header
        if($dslabel == null){
            $dslabel = urlencode("TEI Header");
        }else{
            $dslabel = urlencode($dslabel);
        }
        $response = XMLObject::getDatastream($pid,'teiVersion');
        if(isset($response['http_code']) && $response['http_code'] == 200){
            $version = XMLObject::getFirstTeiVersion($pid);
            //nur versionsnummer
            $version =  preg_replace("/^TEI-header_version/","",$version);
            $version =  substr($version,0,strpos($version,'_'));
            $version++;
            if(!$version){
                $response['http_code'] = 400;
                $response['response'] = 'TeiVersionFile corrupt';
            }
            $dsformatname = urlencode('TEI-header_version'.$version.'_'.$current_scheme);
        }else{
            $dsformatname = urlencode('TEI-header_version1_'.$current_scheme);
        }

        $response = XMLObject::addDatastream($pid,$fileName,$dsformatname,$dslabel,'text/xml');
        if($response['http_code'] != 201) return $response;
        //set tei header version/scheme
        $response = XMLObject::updateTeiVersionFile($pid,$dsformatname);
        return $response;
    }

    public function isTeiVersion($pid,$requested_version){
        if(strrpos($requested_version,'Z') == strlen($requested_version)-1)
            $requested_version = substr($requested_version,0,strrpos($requested_version,'_'));
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $dsId = urlencode('teiVersion');
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsId/content";
        $versionFileContent = @call_user_func_array('file_get_contents', $url);
        while(stripos($versionFileContent,';')){
            $versions[] = substr($versionFileContent,0,stripos($versionFileContent,';'));
            $versionFileContent = substr($versionFileContent,stripos($versionFileContent,';')+1);
        }
        $versions[] = $versionFileContent;
        foreach($versions as $version){
               if($version === $requested_version){
                   return true;
               }
        }
        return false;
    }


    private function updateTeiVersionFile($pid,$teiname,$delete = false){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $dsId = urlencode('teiVersion');
        $dsLabel = urlencode('Tei Header Versions');
        if($delete){
            $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsId/content";
            $versionFileContent = @call_user_func_array('file_get_contents', $url);
            $file = tempnam("/tmp", "teiVersion");
            $text = str_replace($teiname.';','',$versionFileContent);
            $text = str_replace($teiname,'',$text);
            //$text = str_replace(';;',';',$text);
            //if(strpos($text,';') ===0) $text = substr($text,1);
            if($text === ''){
                $result = XMLObject::purgeDatastream($pid,"teiVersion");
            }else{
                file_put_contents($file,$text);
                $result = XMLObject::modifyDatastream($pid,$file,$dsId,$dsLabel);
            }
            unlink($file);
        }else{
            $response = XMLObject::getDatastream($pid,'teiVersion');
            if($response['http_code'] != 200){
                $file = tempnam("/tmp", "teiVersion");
                file_put_contents($file,$teiname);
                $result = XMLObject::addDatastream($pid,$file,$dsId,$dsLabel);
                unlink($file);
            }else{
                $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsId/content";
                $versionFileContent = @call_user_func_array('file_get_contents', $url);
                $versionFileContent = $teiname.';'.$versionFileContent;
                $file = tempnam("/tmp", "teiVersion");
                file_put_contents($file,$versionFileContent);
                $result = XMLObject::modifyDatastream($pid,$file,$dsId,$dsLabel);
                unlink($file);
            }
        }
        return $result;
    }

    /**
     * Deletes a TEI-header version from handle file, teiVersion file, index and fedora
     * @param $pid
     * @param $dsID
     * @param $date
     * @return array|mixed
     */
    public function deleteTEIVersion($pid,$dsID,$date){
        $version = $dsID."_".str_replace('.',':',$date);


        //deletion not allowed by gwdg
        //$handlePID = XMLObject::getHandlePID($pid,$version);
        //$response = XMLObject::unregisterHandlePID($handlePID);

        XMLObject::deleteHandlePID($pid,$version);

        //get first teiVersion that is indexed
        $firstTEI = XMLObject::getFirstTeiVersion($pid);

        //delete version from fedora
        XMLObject::purgeDatastreamVersionFedora($pid,$dsID,$date);

        //check if datastream still exists
        $result_array = XMLObject::getDatastream($pid,$dsID);

        if($result_array['http_code'] != 200){

            //delete from teiVersion file
            $result_array = XMLObject::updateTeiVersionFile($pid,$dsID,true);
            //if was first, index new first version
            if($version === $firstTEI){
                XMLObject::indexLatestVersion($pid);
            }
        }
        return $result_array;

    }


    public function getViewMapping($scheme){
        if(XMLObject::isFedoraObject("scheme:$scheme") && XMLObject::isFedoraDatastream("scheme:$scheme","viewMapping")){
            return XMLObject::getDatastreamContent("scheme:$scheme","viewMapping");
        }else{
            return null;
        }
    }














    /***************************************************************
     *
     *                      HANDLE
     *
     ******************************************************************/

    /**
     * Creates a new handle pid for corpus $pid
     * @param $pid
     * @return array
     */
    public function handle($pid){
        $version = XMLObject::getFirstTeiVersion($pid);
        $handlePID = XMLObject::registerHandlePID($pid,$version);
        return XMLObject::appendHandlePID($handlePID,$pid,$version);
    }

    /**
     * Registers a corpus url at gwdg handle
     * @param $pid opbject pid
     * @param $version if another then first version should be registered
     */
    public function registerHandlePID($pid,$version = null){
        if($version==null){
            $version = XMLObject::getFirstTeiVersion($pid);
        }
        $objectURL = Router::url(array('controller' => 'XMLObjects','action' => 'objects',$pid,$version), true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "http://pid.gwdg.de/handles/11022" );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, 'xxx:xxx');
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_ENCODING, 'xml');
        curl_setopt($ch,CURLOPT_HTTPHEADER,array (
            "Accept: application/json",
            "Content-Type:application/json"
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, '[{"type":"URL","parsed_data":"'.$objectURL.'","encoding":"xml"}]');
        $response = curl_exec($ch);
        $response2 = substr($response,strpos($response,"Location:")+10);
        $response2 = substr($response2,0,strpos($response2,"\n"));
        if(strpos($response2,"\r")!==false) $response2 = substr($response2,0,strpos($response2,"\r"));
        $response2  = substr($response2,strrpos($response2,"/")+1);
        return $response2;
    }

    //command line: curl -u '$user:$pw' -H "Accept:application/json" -H "Content-Type:application/json" -X PUT --data '[{"type":"URL","parsed_data":"http://www.laudatio-repository.org/repository/corpus/laudatio%3AMaerchenkorpus/TEI-header_version1_Schema6_2013-08-30T14%3A56%3A07%3A867Z"}]' http://pid.gwdg.de/handles/11022/0000-0000-1CDC-A
    public function updateHandle($pid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $confHandle = Configure::read('HandleConfig');
        $handleUserpwd = $confHandle['handle_userpwd'];
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/handlePIDs/content";
        $pidFileContent = @call_user_func_array('file_get_contents', $url);
        if($pidFileContent === false || $pidFileContent == '' || $pidFileContent == '{}') return null;
        $pids = json_decode($pidFileContent);
        $result_array['response'] = array();
        $result_array['http_code'] = array();
        foreach($pids as $header => $handle){
            $objectURL = Router::url(array('controller' => 'XMLObjects','action' => 'corpus'), true);
            $objectURL = $objectURL."/".urlencode($pid)."/".urlencode($header);
            $handleAddress= "http://pid.gwdg.de/handles/11022"."/".$handle;//"http://hdl.handle.net/11022/$handle?no-redirect";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_URL, $handleAddress );
            curl_setopt($ch, CURLOPT_USERPWD, $handleUserpwd);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT");
            curl_setopt($ch, CURLOPT_HTTPHEADER,array (
                "Accept: application/json",
                "Content-Type:application/json"
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                '[
                    {"type":"URL","parsed_data":"'.$objectURL.'"}
                ]'
            );
            $response = curl_exec($ch);
            array_push($result_array['response'],$response);
            array_push($result_array['http_code'],curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }
        //return code 204 -> successfull
        return $result_array;

    }

    /**
     * Appends a handle PID and object PID to a json file in fedora
     * @param $handlepid
     * @param $pid
     * @param $version
     * @return array
     */
    public function appendHandlePID($handlepid,$pid,$version){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $dsId = urlencode('handlePIDs');
        $dsLabel = urlencode('Handle PIDs');
        $response = XMLObject::getDatastream($pid,'handlePIDs');
        if($response['http_code'] != '200'){
            $file = tempnam("/tmp", "handlePIDs");
            $content = '{"'.$version.'":"'.$handlepid.'"}';
            file_put_contents($file,$content);
            $result = XMLObject::addDatastream($pid,$file,$dsId,$dsLabel);
            unlink($file);
        }else{
            $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsId/content";
            $handleFileContent = @call_user_func_array('file_get_contents', $url);
            $array = json_decode($handleFileContent,true);
            $array[$version] = $handlepid;
            $handleFileContent = json_encode($array,JSON_FORCE_OBJECT);

            $file = tempnam("/tmp", "handlePIDs");
            file_put_contents($file,$handleFileContent);
            $result = XMLObject::modifyDatastream($pid,$file,$dsId,$dsLabel);
            unlink($file);
        }

        return $result;
    }

    /**
     * Returns a handle PID for a @$version of a tei-header from a json file in fedora
     * @param $pid
     * @param $version
     * @return null
     */
    public function getHandlePID($pid,$version){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $dsId = urlencode('handlePIDs');
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsId/content";
        $handleFileContent = @call_user_func_array('file_get_contents', $url);
        $array = json_decode($handleFileContent,true);
        if(!isset($array[$version])) return null;
        return $array[$version];
    }

    /**
     * Deletes a handle pid from the handle json file in fedora
     * @param $pid
     * @param $version
     * @return mixed
     */
    public function deleteHandlePID($pid,$version){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $dsId = urlencode('handlePIDs');
        $dsLabel = urlencode('Handle PIDs');
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsId/content";
        $handleFileContent = @call_user_func_array('file_get_contents', $url);
        $array = json_decode($handleFileContent,true);
        //$handlePID = $array[$version];
        if(!isset($array[$version])) return;
        unset($array[$version]);
        $handleFileContent = json_encode($array,JSON_FORCE_OBJECT);
        $file = tempnam("/tmp", "handlePIDs");
        file_put_contents($file,$handleFileContent);
        $result = XMLObject::modifyDatastream($pid,$file,$dsId,$dsLabel);
        unlink($file);
        return $result;
    }

    /**
     * Deletes a handle pid from the handle json file in fedora
     * @param $pid
     * @param $dsid
     * @return mixed
     */
    public function deleteAllHandlePID($pid,$dsid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $dsId = urlencode('handlePIDs');
        $dsLabel = urlencode('Handle PIDs');
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsId/content";
        $handleFileContent = @call_user_func_array('file_get_contents', $url);
        $array = json_decode($handleFileContent,true);
        foreach($array as $version => $key){
            if(strpos($version,$dsid)===0)
                unset($array[$version]);
        }
        $handleFileContent = json_encode($array,JSON_FORCE_OBJECT);
        $file = tempnam("/tmp", "handlePIDs");
        file_put_contents($file,$handleFileContent);
        $result = XMLObject::modifyDatastream($pid,$file,$dsId,$dsLabel);
        unlink($file);
        return $result;
    }
    /**
     * DOES NOT WORK
     *
     * Enforcing nodelete-Profile. Deletion of handles is deactivated for the Prefix 11022.
     *
     * unregisters handle pids
     * @param $handlePID
     * @return mixed
     */
    public function unregisterHandlePID($handlePID){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_URL, "http://pid.gwdg.de/handles/11022/".$handlePID );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_USERPWD, '1019-01:XaihahP5');
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,CURLOPT_HTTPHEADER,array (
                "Accept: application/json",
                "Content-Type:application/json"
            ));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
            //curl_setopt($ch, CURLOPT_POSTFIELDS, '[{"type":"URL","parsed_data":"'.$objectURL.'","encoding":"xml"}]');
            $response = curl_exec($ch);
            return $response;

    }

    /*************************************************************************
     *
     *                      Creative Common License
     *
     ************************************************************************/

    public function saveLicense($post){
        $license_array = array();
        if(isset($post['cc_js_want_cc_license'])){
            $license_array['cc_js_want_cc_license'] = $post['cc_js_want_cc_license'];
        }
        if(isset($post['cc_js_result_uri'])){
            $license_array['cc_js_result_uri'] = $post['cc_js_result_uri'];
        }
        if(isset($post['cc_js_result_img'])){
            $license_array['cc_js_result_img'] = $post['cc_js_result_img'];
        }
        if(isset($post['cc_js_result_name'])){
            $license_array['cc_js_result_name'] = $post['cc_js_result_name'];
        }

        $file = tempnam("/tmp", "license");
        $content = json_encode($license_array);
        file_put_contents($file,$content);
        XMLObject::addDatastream($post['id'],$file,urlencode("license"),urlencode("Creative Commons License"));
        unlink($file);
    }

    public function getLicense($pid){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $dsId = urlencode('license');
        $url[] = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsId/content";
        $licenseFileContent = @call_user_func_array('file_get_contents', $url);
        //if($licenseFileContent) return null;
        $array = json_decode($licenseFileContent,true);
        return $array;
    }


    /************************************************************************
     *
     *                        Utility & Validation
     *
     ***********************************************************************/

    /**
     * converts xml-file into json
     * @param $sXmlFile
     * @return mixed
     */
    public function javaXSLT($sXmlFile){
        require_once("/var/www/repository/lib/Java.inc");
        chmod($sXmlFile, 0755);
        $sXslFile = $_SERVER['DOCUMENT_ROOT']."/xsltjson/conf/xml-to-json-modified.xsl";
        try{
            $oXslSource = new java("javax.xml.transform.stream.StreamSource", "file://".$sXslFile);
            $oXmlSource = new java("javax.xml.transform.stream.StreamSource", "file://".$sXmlFile);

            $oFeatureKeys = new JavaClass("net.sf.saxon.FeatureKeys");

            //Create the Factory
            $oTransformerFactory = new java("net.sf.saxon.TransformerFactoryImpl");

            //Disable source document validation
            $oTransformerFactory->setAttribute($oFeatureKeys->SCHEMA_VALIDATION, 4);

            //Create a new Transformer
            $oTransFormer = $oTransformerFactory->newTransformer($oXslSource);

            //Create a StreamResult to store the output
            $oResultStringWriter = new java("java.io.StringWriter");
            $oResultStream = new java("javax.xml.transform.stream.StreamResult", $oResultStringWriter);

            //Transform
            $oTransFormer->transform($oXmlSource, $oResultStream);
            //unbind($sXmlFile)
            //Echo the output from the transformation
            //echo java_cast($oResultStringWriter->toString(), "string");
            return java_cast($oResultStringWriter->toString(), "string");

        }catch(JavaException $e){
            echo java_cast($e->getCause()->toString(), "string");
            exit;
        }
    }

    private function resultMessage($message,$code){
        $result_array['response'] = $message;
        $result_array['http_code'] = $code;
        return $result_array;
    }


    private function formatPID($pid){
        $pid = urldecode($pid);
        $result = "";
        $pid_array = explode(":",$pid);
        $namespace = $pid_array[0];
        $id = $pid_array[1];

        $namespace_array = str_split($namespace);
        for($i=0; $i<count($namespace_array); $i++){
            if(preg_match('/[a-zA-Z0-9]|\.|\-/',$namespace_array[$i])){
                $result .= $namespace_array[$i];
            }
        }
        $result .= ':';
        $id_array = str_split($id);
        for($i=0; $i<count($id_array); $i++){
            if(preg_match('/[a-zA-Z0-9]|\.|\-|~|_|/',$id_array[$i])){
                $result .= $id_array[$i];
            }
        }
        #var_dump(urlencode($result));
        return urlencode($result);
    }

    function formatXmlString($xml) {
        // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
        $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

        // now indent the tags
        $token      = strtok($xml, "\n");
        $result     = ''; // holds formatted version as it is built
        $pad        = 0; // initial indent
        $matches    = array(); // returns from preg_matches()
        $indent=0;
        // scan each line and adjust indent based on opening/closing tags
        while ($token !== false) :

            // test for the various tag states

            // 1. open and closing tags on same line - no change
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
                $indent=0;
            // 2. closing tag - outdent now
            elseif (preg_match('/^<\/\w/', $token, $matches) &&($indent!=0)) :
                $pad--;

            // 3. opening tag - don't pad this one, only subsequent tags
            elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
                $indent=1;
            // 4. no indentation needed
            else :
                $indent = 0;
            endif;

            // pad the line with the required number of leading spaces
            $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
            $result .= $line . "\n"; // add to the cumulative result, with linefeed
            $token   = strtok("\n"); // get the next token
            $pad    += $indent; // update the pad size for subsequent lines
        endwhile;

        return $result;
    }

    public function validateModifyObject($post,$validate_array){
        $out = '';
        if(empty($post)){
            $out = 'Internal Failure: validateModifyObject: empty postdata</br>';
        }
        $change = false;
        if($validate_array['label']!=$post['label']) $change = true;
        if($validate_array['owner']!=$post['owner']) $change = true;
        if($validate_array['state']!=$post['state']) $change = true;
        if(!$change) $out = 'No changes made.</br>';

        #validate label/owner
        #TODO: validation
        #validate state
        if($post['state']!= 'A' && $post['state']!= 'I' && $post['state']!= 'D') $out = 'Not a valid state';

        return $out;
    }

    public function validateModifyDatastream($post,$validate_array){
        return '';
    }
    public function validateIngest($post, $files){
        $out = '';
        $ds = @$files['userfile']?:null;
        $tei = @$files['teifile']?:null;
        #check id
        if($post['id'] == ''){
            $out .= 'Please enter ID</br>';
            #check id format
        }else{
            if(!XMLObject::pidvalidate($post['id'])){
                $out .= 'ID not valid, correct form namespace:id </br>';

            }
        }
        #check label
        if($post['label'] == ''){
            $out .= 'Please enter Label</br>';
        }
        #check files

        #check label for every uploaded format
        if( count($ds['name']) > 1 ){
            for($i=1;$i<(count($ds['name']));$i++){
                if($post['formatname'][$i-1] == '' && $ds['name'][$i]!= ''){
                    $out .= 'Please enter Format-Name for Datastream '.($i).'</br>';
                }
            }
        }
        #check TEI


        return $out;
    }

    public function validateAddDatastream($post, $files){
        $out = '';
        //TODO:check if id already in use
        if( count($files['name']) > 1 ){
            for($i=1;$i<(count($files['name']));$i++){
                if(strpos($post['formatname'][$i-1],'TEI-header')){
                    $out .= 'Format-Name must not begin with "TEI-header". Please use the TEI-header upload form.';
                }
                if($post['formatname'][$i-1] == '' && $files['name'][$i]!= ''){
                    $out .= 'Please enter Format-Name for Datastream '.($i).'</br>';
                }
            }
        }
        return $out;
    }

    public function pidvalidate($pid){
        return preg_match('/^([A-Za-z0-9]|-|\.)+:(([A-Za-z0-9])|-|\.|~|_|(%[0-9A-F]{2}))+$/',$pid);
    }

    public function is_array_empty($InputVariable){
        $Result = true;
        if (is_array($InputVariable) && count($InputVariable) > 0){
            foreach ($InputVariable as $Value){
                $Result = $Result && XMLObject::is_array_empty($Value);
            }
        }else{
            $Result = empty($InputVariable);
        }
        return $Result;
    }

}
