<?php
App::uses('Config','Model');

class ConfigTest extends CakeTestCase{
    public $fixtures = array('app.config');

    private $deleteObject = null;
    private $deleteFile = null;
    private $deleteMapping = null;

    public function setUp() {
        parent::setUp();
        $this->Config = ClassRegistry::init('Config');
    }

    public function testCreateFedoraObject()
    {
        $pid = "ignore:testobject";
        $this->deleteObject = $pid;
        $this->Config->createFedoraObject($pid,"testlabel");
        $this->assertEqual($this->Config->isFedoraObject($pid),true);

    }

    public function testAddDatastream(){
        $pid = "ignore:testobject";
        $this->deleteObject = $pid;
        $this->Config->createFedoraObject($pid,"testlabel");
        $this->deleteFile = 'tmp/testDatastream';
        file_put_contents($this->deleteFile,"test");
        $this->Config->addDatastream($pid,$this->deleteFile,"testID","testLabel");
        $this->assertEqual($this->Config->isFedoraDatastream($pid,"testID"),true);
    }

    public function testAddDatastreamText(){
        $pid = "ignore:testobject";
        $this->deleteObject = $pid;
        $this->Config->createFedoraObject($pid,"testlabel");
        $this->Config->addDatastreamText($pid,"testID","testLabel","foo");
        $this->assertEqual($this->Config->isFedoraDatastream($pid,"testID"),true);
    }

    public function testModifyDatastream(){
        $pid = "ignore:testobject";
        $this->deleteObject = $pid;
        $this->Config->createFedoraObject($pid,"testlabel");
        $this->deleteFile = 'tmp/testDatastream';
        file_put_contents($this->deleteFile,"foo");
        $this->Config->addDatastream($pid,$this->deleteFile,"testID","testLabel");
        file_put_contents($this->deleteFile,"bar");
        $this->Config->modifyDatastream($pid,$this->deleteFile,"testID");
        $content = $this->Config->getDatastream($pid,"testID");
        $this->assertEqual($content,"bar");
    }

    public function testDatastreamVersioning(){
        $pid = "ignore:testobject";
        $this->deleteObject = $pid;
        $this->Config->createFedoraObject($pid,"testlabel");
        $this->deleteFile = 'tmp/testDatastream';
        file_put_contents($this->deleteFile,"foo");
        $this->Config->addDatastream($pid,$this->deleteFile,"testID","testLabel");
        file_put_contents($this->deleteFile,"bar");
        $this->Config->modifyDatastream($pid,$this->deleteFile,"testID");
        $versions = $this->Config->getDatastreamVersions($pid,"testID");
        $content = $this->Config->getDatastream($pid,"testID",$versions[0]);
        $this->assertEqual($content,"bar");
        $content = $this->Config->getDatastream($pid,"testID",$versions[1]);
        $this->assertEqual($content,"foo");
    }

    public function testDatastreamPurge(){
        $pid = "ignore:testobject";
        $this->deleteObject = $pid;
        $this->Config->createFedoraObject($pid,"testlabel");
        $this->deleteFile = 'tmp/testDatastream';

        file_put_contents($this->deleteFile,"foo");
        $this->Config->addDatastream($pid,$this->deleteFile,"testID","testLabel");
        file_put_contents($this->deleteFile,"bar");
        $this->Config->modifyDatastream($pid,$this->deleteFile,"testID");
        //file_put_contents($this->deleteFile,"baz");
        $this->Config->modifyDatastreamText($pid,"testID","baz","text/json");
        //delete "bar"
        $versions = $this->Config->getDatastreamVersions($pid,"testID");
        $this->Config->deleteDatastream($pid,"testID",$versions[1]);

        //check if "foo" and "baz" still there
        $versions = $this->Config->getDatastreamVersions($pid,"testID");
        $content = $this->Config->getDatastream($pid,"testID",$versions[0]);
        $this->assertEqual($content,"baz");
        $content = $this->Config->getDatastream($pid,"testID",$versions[1]);
        $this->assertEqual($content,"foo");

        $this->Config->deleteDatastream($pid,"testID");
        $this->assertEqual(false,$this->Config->isFedoraDatastream($pid,"testID"));
    }

    public function testObjectOverwrite(){
        $pid = "ignore:testobject";
        $this->deleteObject = $pid;
        $this->Config->createFedoraObject($pid,"testlabel");
        $this->deleteFile = 'tmp/testDatastream';
        file_put_contents($this->deleteFile,"foo");
        $this->Config->addDatastream($pid,$this->deleteFile,"testID","testLabel");
        $this->Config->createFedoraObject($pid,"testlabel");
        $this->assertEqual(false,$this->Config->isFedoraDatastream($pid, "testID"));
    }


    public function testIndexMapping(){
        $this->deleteMapping = $scheme = "asdfgh";
        $this->assertEqual(false,$this->Config->indexTypeExists($scheme));
        $this->Config->putIndexMapping($scheme,'{
	    "'.$scheme.'" : {
		"dynamic_templates" : [{
				"date_corpus_revision" : {
					"mapping" : {
						"type" : "date",
						"index" : "not_analyzed",
						"format" : "yyyy||YYYY-MM||YYYY-MM-dd"
					},
					"path_match" : "teiCorpus.teiHeader.revisionDesc.change.@when"
				}
			}]}}');

        $this->assertEqual(true,$this->Config->indexTypeExists($scheme));
        $this->Config->deleteIndexMapping($scheme);
        $this->assertEqual(false,$this->Config->indexTypeExists($scheme));
    }




    public function tearDown(){
        parent::tearDown();
        if($this->deleteMapping !== null){
            $this->Config->deleteIndexMapping($this->deleteMapping);
        }
        if($this->deleteObject !== null){
            $this->Config->deleteFedoraObject($this->deleteObject);
        }
        if($this->deleteFile !== null){
            unlink($this->deleteFile);
        }
    }
}
