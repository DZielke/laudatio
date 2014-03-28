<?php
App::uses('AppModel', 'Model');

class Config extends AppModel {

    /**
     * Creates a new fedora object. Overwrites object if it already exists.
     * @param $pid pid of the fedora object
     * @param $label label of the fedora object
     * @throws InternalErrorException
     */
    public function createFedoraObject($pid,$label){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];

        $pid_encoded = urlencode($pid);
        $label = urlencode($label);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid_encoded?label=$label" );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code != 201){
            if(strpos($response,"org.fcrepo.server.errors.ObjectExistsException") !== false){
                Config::deleteFedoraObject($pid);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid_encoded?label=$label" );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($http_code != 201){
                    if(Configure::read('debug') > 0)
                        throw new InternalErrorException('Could not create fedora object. '.$response);
                    else
                        throw new InternalErrorException('Could not create fedora object.');
                }
            }else{
                if(Configure::read('debug') > 0)
                    throw new InternalErrorException('Could not create fedora object. '.$response);
                else
                    throw new InternalErrorException('Could not create fedora object.');

            }
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

    /**
     * Deletes an object of $pid from fedora.
     * @param $pid pid of the fedora object
     * @throws InternalErrorException throws no exception if object is not present in fedora
     */
    public function deleteFedoraObject($pid){
        $pid = urlencode($pid);
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code !== 200){
            if(Configure::read('debug') > 0)
                throw new InternalErrorException('Could not delete fedora object: '.$response);
            else
                throw new InternalErrorException('Could not delete fedora object.');
        }

    }

    /**
     * Adds a file as datastream to fedora.
     * @param $pid pid of the fedora object
     * @param $filename name of the file to be imported as datastream
     * @param $dsid id of the datastream
     * @param $dslabel label of the datatream
     * @param null $mime_type mime type of the file. If not provided the mime type is retrieved by php's finfo()
     * @param bool $versionable default: true, every import of the a datastream with the same dsid results in a new version of the datastream accessible via history. False overwrites the existing datastream.
     * @throws InternalErrorException
     */
    public function addDatastream($pid, $filename, $dsid, $dslabel, $mime_type = null, $versionable = true){
        $pid = urlencode($pid);
        $dslabel = urlencode($dslabel);
        $dsid = urlencode($dsid);
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
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
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid?dsLabel=$dslabel&controlGroup=M&versionable=$versionable");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:$mime_type"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code !== 201){
            if(Configure::read('debug') > 0)
                throw new InternalErrorException('Could not add datastream '.$response);
            else
                throw new InternalErrorException('Could not add datastream.');
        }
    }

    /**
     * Adds a string as datastream to fedora. (versionable is always set to true)
     * @param $pid pid of the fedora object
     * @param $dsid id of the datastream
     * @param $dslabel label of the datatream
     * @param $text content of the resulting datastream
     * @param null $mime_type default is text/plain
     * @throws InternalErrorException
     */
    public function addDatastreamText($pid,$dsid,$dslabel,$text,$mime_type = null){
        $pid = urlencode($pid);
        $dslabel = urlencode($dslabel);
        $dsid = urlencode($dsid);
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        if($mime_type === null)
            $mime_type = "text/plain";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid?dsLabel=$dslabel&controlGroup=M");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:$mime_type"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $text);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code !== 201){
            if(Configure::read('debug') > 0)
                throw new InternalErrorException('Could not add datastream '.$response);
            else
                throw new InternalErrorException('Could not add datastream.');
        }
    }

    /**
     * Checks if the datastream is present in fedora.
     * @param $pid
     * @param $dsid
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
     * returns the content of the datastream
     * @param $pid
     * @param $dsid
     * @param null $asOfDateTime
     * @return mixed
     * @throws NotFoundException
     * @throws InternalErrorException
     */
    public function getDatastream($pid, $dsid, $asOfDateTime = null){
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
     * Puts a new version of a datastream to fedora (if datastream is versionable, else the datastream will be overwritten)
     * @param $pid
     * @param $filename
     * @param $dsid
     * @throws InternalErrorException
     */
    public function modifyDatastream($pid,$filename,$dsid){
        if($filename === null || empty($pid) || empty($dsid) )
            throw new InternalErrorException('argument(s) empty');
        $pid = urlencode($pid);
        $dsid = urlencode($dsid);
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $handle = fopen($filename, "r");
        if(filesize($filename)>0){
            $XPost = fread($handle, filesize($filename));
        }else{
            throw new InternalErrorException('file empty'); // empty files not allowed by fedora ?
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo,$filename);
        fclose($handle);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: $mime_type"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code !== 200){
                if(Configure::read('debug') > 0)
                    throw new InternalErrorException('Internal Error: '.$response);
                else
                    throw new InternalErrorException();
        }
    }

    /**
     * Puts a new version of a datastream to fedora (if datastream is versionable, else the datastream will be overwritten)
     * @param $pid
     * @param $dsid
     * @param $text
     * @param null $mime
     * @throws InternalErrorException
     */
    public function modifyDatastreamText($pid,$dsid,$text,$mime=null){
        if($text === null || empty($pid) || empty($dsid) )
            throw new InternalErrorException('argument(s) empty');
        $pid = urlencode($pid);
        $dsid = urlencode($dsid);
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $XPost = $text;
            if($mime !== null)
                $mime_type = $mime;
            else
                $mime_type = "text/plain";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: $mime_type"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code !== 200){
            if(Configure::read('debug') > 0)
                throw new InternalErrorException('Internal Error: '.$response);
            else
                throw new InternalErrorException();
        }
    }


    /**
     * Returns the dates of datastream versions.
     * Returned dates can be used as 3rd parameter $asOfDateTime in getDatastream()
     * @param $pid
     * @param $dsid
     * @return array
     * @throws InternalErrorException
     */
    public function getDatastreamVersions($pid, $dsid){
        $pid = urlencode($pid);
        $dsid = urlencode($dsid);
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid/history?format=xml");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $fedora_userpwd);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code !== 200)
            throw new InternalErrorException;

        $xml = Xml::build($response);
        $dates = array();
        foreach($xml->datastreamProfile as $version){
            array_push($dates,$version->dsCreateDate);
        }
        return $dates;
    }


    /**
     * Deletes a datastream, optional just a version of the datastream specified by date/time.
     * @param $pid
     * @param $dsid
     * @param $asOfDateTime
     * @return mixed
     * @throws NotFoundException
     * @throws InternalErrorException
     */
    public function deleteDatastream($pid,$dsid,$asOfDateTime = null){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $pid = urlencode($pid);
        $dsid = urlencode($dsid);
        if($asOfDateTime){
            $url = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid?startDT=$asOfDateTime&endDT=$asOfDateTime";
        }
        else {
            $url = "$fedoraAdress/fedora/objects/$pid/datastreams/$dsid";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
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
     * returns all fedora objects pids that start with 'scheme:'
     * @return array
     * @throws NotFoundException
     * @throws InternalErrorException
     */
    public function getSchemeList(){
        $confFedora = Configure::read('FedoraConfig');
        $fedoraAdress = $confFedora['fedora_address_port'];
        $fedora_userpwd = $confFedora['fedora_userpwd'];
        $url = "$fedoraAdress/fedora/objects?query=pid~scheme:*&pid=true&resultFormat=xml";
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
        $schemeList  = array();
        $xml = Xml::build($response);
        foreach($xml->resultList->objectFields as $object){
            array_push($schemeList, substr((string)$object->pid,7));
        }
        return $schemeList;

    }

    /************************************************************************
     *
     *                              Index
     *
     ************************************************************************/
    /**
     * Puts the provided JSON-string as mapping to elasticsearch. Will merge with existing mappings if possible.
     * @param $scheme
     * @param $json
     * @throws InternalErrorException
     */
    public function putIndexMapping($scheme,$json){
        $confIndex = Configure::read('IndexConfig');
        $index_name = $confIndex['index_name'];
        $index_address = $confIndex['index_address'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$index_address/$index_name/$scheme/_mapping");
        curl_setopt($ch, CURLOPT_PORT, "9200");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code !== 200){
            throw new InternalErrorException($response);
        }
    }

    /**
     * Deletes the index mapping of the provided scheme from elasticsearch. All indexed objects of type $scheme will be deleted in elasticsearch.
     * @param $scheme
     */
    public function deleteIndexMapping($scheme){
        $confIndex = Configure::read('IndexConfig');
        $index_name = $confIndex['index_name'];
        $index_address = $confIndex['index_address'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$index_address/$index_name/$scheme/_mapping");
        curl_setopt($ch, CURLOPT_PORT, "9200");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }

    /**
     * Checks if a type for $scheme exists in elasticsearch.
     * @param $scheme
     * @return bool
     */
    public function indexTypeExists($scheme){
        $confIndex = Configure::read('IndexConfig');
        $index_name = $confIndex['index_name'];
        $index_address = $confIndex['index_address'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, "$index_address/$index_name/$scheme");
        curl_setopt($ch, CURLOPT_PORT, "9200");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"HEAD");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code === 200){
            return true;
        }else{
            return false;
        }
    }
}