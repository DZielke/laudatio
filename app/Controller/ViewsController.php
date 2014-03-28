<?php
App::uses('XMLObject', 'AppController', 'Controller', 'Utility', 'Xml');
App::uses('HtmlHelper', 'View/Helper');


class ViewsController extends AppController {

    function beforeFilter() {
        parent::beforeFilter();
        #$this->Auth->allow('*');
        $this->Auth->allow();#'index','view'
    }

    private function viewPermission($corpus_id){
        App::uses('Xml','Utility');
        $this->loadModel('XMLObject');
        if($this->Session->read('Auth.User.group_id') == '1') return true;
        if($this->XMLObject->isOpenAccess($corpus_id)){
            return true;
        }
        //if($pid == null) return false;
        //$user = $this->Session->read('Auth.User.username');
        $user_id = $this->Session->read('Auth.User.id');
        $user_name = $this->Session->read('Auth.User.username');
        $user_group = $this->Session->read('Auth.User.Group.id');
        if( $this->XMLObject->isCorpusGroup($user_group,$corpus_id) || $this->XMLObject->isCorpusUser($user_id,$user_name,$corpus_id) || $this->XMLObject->isCorpusCreator($user_id,$user_name,$corpus_id)){
            return true;
        }else{
            return false;
        }
    }

    public function objects_schema2_documents($value,$version){
        $this->autoRender = false;
        $this->loadModel('XMLObject');
        if(!$this->viewPermission($value)){
            $this->Session->setFlash(__('No permission to view Corpus.'));
            $this->redirect(array('controller' => 'XMLObjects','action' => 'view'));
        }
        $date = substr($version,strrpos($version,'_')+1);
        $version = substr($version,0,strrpos($version,'_'));
        //$scheme = substr($version,strrpos($version,'_')+1);

        $result_array = $this->XMLObject->getObjectTEIXmlVersion($value,$version,$date);
        $xmlObject = Xml::build($result_array['response']);// Here will throw a Exception


        if (isset($xmlObject->teiHeader)) {
            // TODO: arrays fÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¼r documents aus XMLObjects::objectsSchema2() aufbauen
            $items_array = array();
            $register_array = array();
            $short_array = array();

            $item_surname_array = array();
            $item_forename_array = array();
            $editor_surname_doc_array = array();
            $editor_forename_doc_array = array();

            $item_publisher_array = array();
            $item_pubPlace_array = array();
            $item_publDate_array = array();

            $item_series_array = array();
            $item_biblScope_array = array();

            $item_extent_array = array();

            $annotListItem_array = array();
            $sourceDesc_array = array();

            $langUsageItem_array = array();
            $item_revision_array = array();

            $title_pid_array = array();

            foreach ($xmlObject->teiHeader->fileDesc->titleStmt->title as $title_pid) {
                array_push($title_pid_array, $title_pid);
                $this->set('title_pid_array', $title_pid_array);
            }

            for($a=0;$a<sizeof($xmlObject->teiCorpus->teiHeader);$a++) {
                foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->attributes('xml', TRUE) as $short) {
                    array_push($short_array, $short);
                    $this->set('short_array', $short_array);
                }

                foreach ($xmlObject->teiCorpus->teiHeader[$a]->attributes() as $attrib => $style) {
                    if ($attrib == 'style') {
                        array_push($register_array, $style);
                        $this->set('register_array', $register_array);
                    }
                }

                foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->title as $item) {
                    array_push($items_array, $item);
                    $this->set('items_array', $items_array);
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->forename)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->forename as $item_forename) {
                        array_push($item_forename_array, $item_forename);
                        $this->set('item_forename_array', $item_forename_array);
                    }
                }
                else if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->persName->author->forename)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->persName->author->forename as $item_forename) {
                        array_push($item_forename_array, $item_forename);
                        $this->set('item_forename_array', $item_forename_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->surname)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->surname as $item_surname) {
                        array_push($item_surname_array, $item_surname);
                        $this->set('item_surname_array', $item_surname_array);
                    }
                }
                else if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->persName->surname)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->persName->surname as $item_surname) {
                        #print_r($item_surname);
                        array_push($item_surname_array, $item_surname);
                        $this->set('item_surname_array', $item_surname_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->forename)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->forename as $editor_forename_doc) {
                        array_push($editor_forename_doc_array, $editor_forename_doc);
                        $this->set('editor_forename_doc_array', $editor_forename_doc_array);
                    }
                }
                else if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->persName->forename)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->persName->forename as $editor_forename_doc) {
                        array_push($editor_forename_doc_array, $editor_forename_doc);
                        $this->set('editor_forename_doc_array', $editor_forename_doc_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->surname)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->surname as $editor_surname_doc) {
                        array_push($editor_surname_doc_array, $editor_surname_doc);
                        $this->set('editor_surname_doc_array', $editor_surname_doc_array);
                    }
                }
                else if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->persName->surname)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->persName->surname as $editor_surname_doc) {
                        array_push($editor_surname_doc_array, $editor_surname_doc);
                        $this->set('editor_surname_doc_array', $editor_surname_doc_array);
                    }
                }

                #publisher
                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->publisher)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->publisher as $item_publisher) {
                        array_push($item_publisher_array, $item_publisher);
                        $this->set('item_publisher_array', $item_publisher_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->pubPlace)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->pubPlace as $item_pubPlace) {
                        array_push($item_pubPlace_array, $item_pubPlace);
                        $this->set('item_pubPlace_array', $item_pubPlace_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->date)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->date as $item_publDate) {
                        array_push($item_publDate_array, $item_publDate);
                        $this->set('item_publDate_array', $item_publDate_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->biblScope)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->biblScope as $item_biblScope) {
                        array_push($item_biblScope_array, $item_biblScope);
                        $this->set('item_biblScope_array', $item_biblScope_array);
                    }
                }

                #sourceDesc
                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->sourceDesc)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->sourceDesc as $sourceDesc) {
                        array_push($sourceDesc_array, $sourceDesc);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->extent)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->extent as $item_extent) {
                        array_push($item_extent_array, $item_extent);
                        $this->set('item_extent_array', $item_extent_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->seriesStmt->title)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->seriesStmt->title as $item_series) {
                        array_push($item_series_array, $item_series);
                        $this->set('item_series_array', $item_series_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->revisionDesc)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->revisionDesc as $item_revision) {
                        array_push($item_revision_array, $item_revision);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->profileDesc->langUsage)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->profileDesc->langUsage as $langUsageItem) {
                        array_push($langUsageItem_array, $langUsageItem);
                    }
                }

                if ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec->attributes() == 'AnnotationList') {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec as $annotListItem) {
                        #foreach ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec->valList->valItem as $annotListItem) {
                        array_push($annotListItem_array, $annotListItem['corresp']);
                        $this->set('annotListItem_array', $annotListItem_array);
                    }
                }
            }

            // html fuer documents aus objects_schema6.ctp
            #if (isset($items_array)) {
            $total_items_array = (int)count($items_array);
            for($q=0;$q<$total_items_array;$q++) {
                echo '<tr id="ex2-node-2-'.$q.'" class="child-of-ex2-node-2" title="Document '.$items_array[$q].'">';
                $title_pid = str_replace(" ", "-", $title_pid_array[0]);
                #$pdf_link = 'http://depot1-7.cms.hu-berlin.de/repository/pdfjs/web/pdf/'.$title_pid.'/'.$short_array[$q].'.pdf';
                $pdf_link = 'http://www.laudatio-repository.org/repository/pdfjs/web/pdf/'.$title_pid.'/'.$short_array[$q].'.pdf';

                if(XMLObject::file_alive($pdf_link)) {
                    echo '<td rel="'.$items_array[$q].'">'.$items_array[$q];
                    echo '<span class="pdf">';

                    echo '<a href="'.$pdf_link.'">
                                        <img src="../../../app/webroot/img/pdf_icon_10.png" title="'.$short_array[$q].'.pdf" />
                                        </a>';
                    #echo 'FileSize: ' .filesize($pdf_link);
                    echo '</span>';
                }

                else {
                    echo '<td rel="'.$items_array[$q].'">'.$items_array[$q];
                    #echo '<td class="pdf">';
                    #echo '<img src="../../../app/webroot/img/no_pdf_icon_8.png" title="No PDF exists" />';
                    #echo "</td>";
                }

                echo '</td></tr>';

                echo '<tr id="ex2-node-2-'.$q.'-1" class="child-of-ex2-node-2-'.$q.'"><td>Title</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-1-1" class="child-of-ex2-node-2-'.$q.'-1"><td>'.$items_array[$q].'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-1-2" class="child-of-ex2-node-2-'.$q.'-1"><td>Register: '.($register_array[$q] != '' ? $register_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-1-3" class="child-of-ex2-node-2-'.$q.'-1"><td>Short: '.$short_array[$q].'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2" class="child-of-ex2-node-2-'.$q.'"><td>Authorship</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2-1" class="child-of-ex2-node-2-'.$q.'-2"><td>Author</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2-1-1" class="child-of-ex2-node-2-'.$q.'-2-1"><td>'.($item_surname_array[$q] != 'NA' ? $item_surname_array[$q].', '.$item_forename_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2-2" class="child-of-ex2-node-2-'.$q.'-2"><td>Editor</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2-2-1" class="child-of-ex2-node-2-'.$q.'-2-2"><td>'.($editor_surname_doc_array[$q] != 'NA' ? $editor_surname_doc_array[$q].', '.$editor_forename_doc_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3" class="child-of-ex2-node-2-'.$q.'"><td>Publication</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-1" class="child-of-ex2-node-2-'.$q.'-3"><td>Publisher: '.($item_publisher_array[$q] != 'NA' ? $item_publisher_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-2" class="child-of-ex2-node-2-'.$q.'-3"><td>Place: '.$item_pubPlace_array[$q].'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-3" class="child-of-ex2-node-2-'.$q.'-3"><td>Date: '.$item_publDate_array[$q].'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-4" class="child-of-ex2-node-2-'.$q.'-3"><td>Series: '.($item_series_array[$q] != 'NA' && $item_series_array[$q] != '' ? $item_series_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-5" class="child-of-ex2-node-2-'.$q.'-3"><td>Scope: '.($item_biblScope_array[$q] != 'NA' ? $item_biblScope_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-6" class="child-of-ex2-node-2-'.$q.'-3"><td>Size: '.((isset($item_extent_array[$q]) && $item_extent_array[$q] != 'NA') ? $item_extent_array[$q].' '.$item_extent_array[$q]['type'] : 'Not set').'</td></tr>';

                echo '<tr id="ex2-node-2-'.$q.'-4" class="child-of-ex2-node-2-'.$q.'"><td>Annotation</td></tr>';

                if (isset($annotListItem_array)) {
                    echo '<tr id="ex2-node-2-'.$q.'-4-1" class="child-of-ex2-node-2-'.$q.'-4"><td>AnnotationList</td></tr>';
                    echo '<tr id="ex2-node-2-'.$q.'-4-1-1" class="child-of-ex2-node-2-'.$q.'-4-1"><td>';


                    if ($annotListItem_array > 0) {
                        echo '<ul>';

                        foreach ($annotListItem_array[$q] as $annotListItem_array_value) {
                            echo '<li class="docannotation link">'.$annotListItem_array_value['corresp'].'</li>';
                        }
                        echo '</ul>';
                    }
                }

                echo '<tr id="ex2-node-2-'.$q.'-5" class="child-of-ex2-node-2-'.$q.'"><td>Revision</td></tr>';
                $x=1;
                foreach ($item_revision_array[$q]->change as $changeDesc) {
                    if ($changeDesc['n'] !='') {
                        echo '<tr id="ex2-node-2-'.$q.'-5-'.$x.'" class="child-of-ex2-node-2-'.$q.'-5"><td>Version: '.$changeDesc['n'].'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-5-'.$x.'-1" class="child-of-ex2-node-2-'.$q.'-5-'.$x.'"><td>Date: '.($changeDesc['when'] != 'NA' ? $changeDesc['when'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-5-'.$x.'-2" class="child-of-ex2-node-2-'.$q.'-5-'.$x.'"><td>Who: '.($changeDesc['who'] != 'NA' ? $changeDesc['who'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-5-'.$x.'-3" class="child-of-ex2-node-2-'.$q.'-5-'.$x.'"><td>Description: '.($changeDesc != 'NA' ? $changeDesc : 'Not set').'</td></tr>';
                        $x++;
                    }
                }

                if (isset($sourceDesc_array[$q])) {
                    echo '<tr id="ex2-node-2-'.$q.'-6" class="child-of-ex2-node-2-'.$q.'"><td>Source Description</td></tr>';
                    $y=1;
                    foreach ($sourceDesc_array[$q]->msDesc as $msDesc) {
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Alternative Name: '.$msDesc->msIdentifier->msName.'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Repository: '.$msDesc->msIdentifier->altIdentifier->repository.'</td></tr>';

                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Collection: '.$msDesc->msIdentifier->altIdentifier->collection.'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Object Type: '.$msDesc->history->origin->objectType.'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Original Date: '.$msDesc->history->origin->origDate.'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Not before: '.$msDesc->history->origin->origDate['notBefore-custom'].' Not after: '.$msDesc->history->origin->origDate['notAfter-custom'].'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Original Place: '.$msDesc->history->origin->origPlace.'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Title: '.$msDesc->history->origin->title.'</td></tr>';
                        $y++;
                    }

                    $w=1;
                    foreach ($sourceDesc_array[$q]->recordHist as $recordHist) {
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$w.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Source: <a href="'.$recordHist->source['facs'].'">'.$recordHist->source['facs'].'</a></td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$w.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Repertorium: <a href="'.$recordHist->source->ref['target'].'">'.$recordHist->source->ref.'</a></td></tr>';
                        $w++;
                    }
                }

                if (isset($langUsageItem_array[$q])) {
                    #echo '<tr id="ex2-node-2-'.$q.'-7" class="child-of-ex2-node-2-'.$q.'"><td>Language</td></tr>';
                    $p=1;
                    foreach ($langUsageItem_array[$q]->language as $language) {
                        if ($language['style'] == 'Language') {
                            echo '<tr id="ex2-node-2-'.$q.'-7-'.$p.'" class="child-of-ex2-node-2-'.$q.'"><td>Language: '.$language.'</td></tr>';
                        }
                        elseif ($language['style'] == '' && $language != '') {
                            echo '<tr id="ex2-node-2-'.$q.'-7-'.$p.'" class="child-of-ex2-node-2-'.$q.'"><td>Language: '.$language.'</td></tr>';
                        }
                        if ($language['style'] == 'LanguageType') {
                            echo '<tr id="ex2-node-2-'.$q.'-7-'.$p.'" class="child-of-ex2-node-2-'.$q.'"><td>Language Type: '.$language.'</td></tr>';
                        }
                        if ($language['style'] == 'LanguageArea') {
                            echo '<tr id="ex2-node-2-'.$q.'-7-'.$p.'" class="child-of-ex2-node-2-'.$q.'"><td>Language Area: '.$language.'</td></tr>';
                        }
                        $p++;
                    }
                }
            }
        }
    }


    public function objects_schema6_documents($value,$version){
        $this->autoRender = false;
        $this->loadModel('XMLObject');
        if(!$this->viewPermission($value)){
            $this->Session->setFlash(__('No permission to view Corpus.'));
            $this->redirect(array('controller' => 'XMLObjects','action' => 'view'));
        }
        $date = substr($version,strrpos($version,'_')+1);
        $version = substr($version,0,strrpos($version,'_'));
        //$scheme = substr($version,strrpos($version,'_')+1);

        $result_array = $this->XMLObject->getObjectTEIXmlVersion($value,$version,$date);
        $xmlObject = Xml::build($result_array['response']);// Here will throw a Exception


        if (isset($xmlObject->teiHeader)) {
            // TODO: arrays fÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¼r documents aus XMLObjects::objectsSchema2() aufbauen
            $items_array = array();
            $register_array = array();
            $short_array = array();

            $item_surname_array = array();
            $item_forename_array = array();
            $editor_surname_doc_array = array();
            $editor_forename_doc_array = array();

            $item_publisher_array = array();
            $item_pubPlace_array = array();
            $item_publDate_array = array();

            $item_series_array = array();
            $item_biblScope_array = array();

            $item_extent_array = array();

            $annotListItem_array = array();
            $sourceDesc_array = array();

            $langUsageItem_array = array();
            $item_revision_array = array();

            $title_pid_array = array();

            $transcriptionItem_array = array();
            $graphicalItem_array = array();
            $lexicalItem_array = array();
            $syntacticalItem_array = array();
            $metaItem_array = array();
            $otherItem_array = array();


            foreach ($xmlObject->teiHeader->fileDesc->titleStmt->title as $title_pid) {
                array_push($title_pid_array, $title_pid);
                $this->set('title_pid_array', $title_pid_array);
            }

            for($a=0;$a<sizeof($xmlObject->teiCorpus->teiHeader);$a++) {
                foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->attributes('xml', TRUE) as $short) {
                    array_push($short_array, $short);
                    $this->set('short_array', $short_array);
                }

                foreach ($xmlObject->teiCorpus->teiHeader[$a]->attributes() as $attrib => $style) {
                    if ($attrib == 'style') {
                        array_push($register_array, $style);
                        $this->set('register_array', $register_array);
                    }
                }

                foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->title as $item) {
                    array_push($items_array, $item);
                    $this->set('items_array', $items_array);
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->forename)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->forename as $item_forename) {
                        array_push($item_forename_array, $item_forename);
                        $this->set('item_forename_array', $item_forename_array);
                    }
                }
                else if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->persName->author->forename)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->persName->author->forename as $item_forename) {
                        array_push($item_forename_array, $item_forename);
                        $this->set('item_forename_array', $item_forename_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->surname)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->surname as $item_surname) {
                        array_push($item_surname_array, $item_surname);
                        $this->set('item_surname_array', $item_surname_array);
                    }
                }
                else if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->persName->surname)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->author->persName->surname as $item_surname) {
                        #print_r($item_surname);
                        array_push($item_surname_array, $item_surname);
                        $this->set('item_surname_array', $item_surname_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->forename)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->forename as $editor_forename_doc) {
                        array_push($editor_forename_doc_array, $editor_forename_doc);
                        $this->set('editor_forename_doc_array', $editor_forename_doc_array);
                    }
                }
                else if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->persName->forename)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->persName->forename as $editor_forename_doc) {
                        array_push($editor_forename_doc_array, $editor_forename_doc);
                        $this->set('editor_forename_doc_array', $editor_forename_doc_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->surname)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->surname as $editor_surname_doc) {
                        array_push($editor_surname_doc_array, $editor_surname_doc);
                        $this->set('editor_surname_doc_array', $editor_surname_doc_array);
                    }
                }
                else if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->persName->surname)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->titleStmt->editor->persName->surname as $editor_surname_doc) {
                        array_push($editor_surname_doc_array, $editor_surname_doc);
                        $this->set('editor_surname_doc_array', $editor_surname_doc_array);
                    }
                }

                #publisher
                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->publisher)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->publisher as $item_publisher) {
                        array_push($item_publisher_array, $item_publisher);
                        $this->set('item_publisher_array', $item_publisher_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->pubPlace)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->pubPlace as $item_pubPlace) {
                        array_push($item_pubPlace_array, $item_pubPlace);
                        $this->set('item_pubPlace_array', $item_pubPlace_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->date)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->date as $item_publDate) {
                        array_push($item_publDate_array, $item_publDate);
                        $this->set('item_publDate_array', $item_publDate_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->biblScope)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->publicationStmt->biblScope as $item_biblScope) {
                        array_push($item_biblScope_array, $item_biblScope);
                        $this->set('item_biblScope_array', $item_biblScope_array);
                    }
                }

                #sourceDesc
                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->sourceDesc)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->sourceDesc as $sourceDesc) {
                        array_push($sourceDesc_array, $sourceDesc);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->extent)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->extent as $item_extent) {
                        array_push($item_extent_array, $item_extent);
                        $this->set('item_extent_array', $item_extent_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->seriesStmt->title)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->fileDesc->seriesStmt->title as $item_series) {
                        array_push($item_series_array, $item_series);
                        $this->set('item_series_array', $item_series_array);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->revisionDesc)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->revisionDesc as $item_revision) {
                        array_push($item_revision_array, $item_revision);
                    }
                }

                if (isset($xmlObject->teiCorpus->teiHeader[$a]->profileDesc->langUsage)) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->profileDesc->langUsage as $langUsageItem) {
                        array_push($langUsageItem_array, $langUsageItem);
                    }
                }


                $total = (int)count($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec);
                for ($i=0;$i<$total;$i++) {
                    foreach ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec[$i] as $annotListItem) {
                        #$elementSpecs = array();
                        #$elementSpecs[][$i] = $annotListItem;
                        if ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec[$i]->attributes() == 'Transcription') {
                            #array_push($transcriptionItem_array, $elementSpecs);
                            array_push($transcriptionItem_array, $annotListItem);
                        }
                        elseif ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec[$i]->attributes() == 'Lexical') {
                            array_push($lexicalItem_array, $annotListItem);
                        }
                        elseif ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec[$i]->attributes() == 'Graphical') {
                            array_push($graphicalItem_array, $annotListItem);
                        }
                        elseif ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec[$i]->attributes() == 'Syntactical') {
                            array_push($syntacticalItem_array, $annotListItem);
                        }
                        elseif ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec[$i]->attributes() == 'Meta') {
                            array_push($metaItem_array, $annotListItem);
                        }
                        elseif ($xmlObject->teiCorpus->teiHeader[$a]->encodingDesc->schemaSpec->elementSpec[$i]->attributes() == 'Other') {
                            array_push($otherItem_array, $annotListItem);
                        }
                    }
                }
            }

            // html fuer documents aus objects_schema6.ctp
            #if (isset($items_array)) {
            $total_items_array = (int)count($items_array);
            for($q=0;$q<$total_items_array;$q++) {
                echo '<tr id="ex2-node-2-'.$q.'" class="child-of-ex2-node-2" title="Document '.$items_array[$q].'">';
                $title_pid = str_replace(" ", "-", $title_pid_array[0]);
                #$pdf_link = 'http://depot1-7.cms.hu-berlin.de/pdfjs/web/pdf/'.$title_pid.'/'.$short_array[$q].'.pdf';
                #$pdf_link = 'http://depot1-7.cms.hu-berlin.de/pdfjs/web/pdf/'.$title_pid.'/'.$short_array[$q].'.pdf';
                #$pdf_link = $this->webroot.'/repository/pdfjs/web/pdf/'.$title_pid.'/'.$short_array[$q].'.pdf';
                #$pdf_link = $this->WWW_ROOT.'/pdfjs/web/pdf/'.$title_pid.'/'.$short_array[$q].'.pdf';
                #WWW_ROOT.'

                #$pdf_link_folder = 'http://www.laudatio-repository.org/repository/pdfjs/web/pdf/'.$title_pid.'/';
                $pdf_link = 'http://www.laudatio-repository.org/repository/pdfjs/web/pdf/'.$title_pid.'/'.$short_array[$q].'.pdf';
                #var_dump($pdf_link);
                if(XMLObject::file_alive($pdf_link)) {
                    echo '<td rel="'.$items_array[$q].'">'.$items_array[$q];
                    #$filename = $pdf_link;
                    #if (file_exists($filename)) {
                    echo '<span class="pdf">';


                    echo '<div>';
                    $result = XMLObject::retrieve_remote_file_size($pdf_link);
                    $filesize = $result/(1024*1024);
                    if ($result >=0) {
                        echo '<a href="'.$pdf_link.'" download>
							<img src="../../../app/webroot/img/pdf_icon_10.png" title=" Download '.$short_array[$q].'.pdf" />
						</a>';

                        echo "Size: ".round($result/(1024*1024), 2)." MB";
                    }
                    echo '</div>';
                    echo '</span>';
                    #}
                }

                else {
                    echo '<td rel="'.$items_array[$q].'">'.$items_array[$q];
                    #echo '<td class="pdf">';
                    #echo '<img src="../../../app/webroot/img/no_pdf_icon_8.png" title="No PDF exists" />';
                    #echo "</td>";
                }

                echo '</td></tr>';
/*
                if(XMLObject::file_alive($pdf_link) && $result >=0) {
                    echo '<tr id="ex2-node-2-'.$q.'-0" class="child-of-ex2-node-2-'.$q.'"><td>Preview PDF</td></tr>';
                    echo '<tr id="ex2-node-2-'.$q.'-0-1" class="child-of-ex2-node-2-'.$q.'-0"><td>';

                    if ($filesize >=20) {
                        echo "This PDF is too big for Viewer to Preview. Please Download this File.";
                    }
                    else {
                        echo '<iframe src="http://docs.google.com/gview?url='.$pdf_link.'&embedded=true" style="margin-left: 20%; width:600px; height:500px;" frameborder="0"></iframe>';
                    }
                }
*/
                echo '<tr id="ex2-node-2-'.$q.'-1" class="child-of-ex2-node-2-'.$q.'"><td>Title</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-1-1" class="child-of-ex2-node-2-'.$q.'-1"><td>'.$items_array[$q].'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-1-2" class="child-of-ex2-node-2-'.$q.'-1"><td>Register: '.($register_array[$q] != '' ? $register_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-1-3" class="child-of-ex2-node-2-'.$q.'-1"><td>Short: '.$short_array[$q].'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2" class="child-of-ex2-node-2-'.$q.'"><td>Authorship</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2-1" class="child-of-ex2-node-2-'.$q.'-2"><td>Author</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2-1-1" class="child-of-ex2-node-2-'.$q.'-2-1"><td>'.($item_surname_array[$q] != 'NA' ? $item_surname_array[$q].', '.$item_forename_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2-2" class="child-of-ex2-node-2-'.$q.'-2"><td>Editor</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-2-2-1" class="child-of-ex2-node-2-'.$q.'-2-2"><td>'.($editor_surname_doc_array[$q] != 'NA' ? $editor_surname_doc_array[$q].', '.$editor_forename_doc_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3" class="child-of-ex2-node-2-'.$q.'"><td>Publication</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-1" class="child-of-ex2-node-2-'.$q.'-3"><td>Publisher: '.($item_publisher_array[$q] != 'NA' ? $item_publisher_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-2" class="child-of-ex2-node-2-'.$q.'-3"><td>Place: '.$item_pubPlace_array[$q].'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-3" class="child-of-ex2-node-2-'.$q.'-3"><td>Date: '.$item_publDate_array[$q].'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-4" class="child-of-ex2-node-2-'.$q.'-3"><td>Series: '.($item_series_array[$q] != 'NA' && $item_series_array[$q] != '' ? $item_series_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-5" class="child-of-ex2-node-2-'.$q.'-3"><td>Scope: '.($item_biblScope_array[$q] != 'NA' ? $item_biblScope_array[$q] : 'Not set').'</td></tr>';
                echo '<tr id="ex2-node-2-'.$q.'-3-6" class="child-of-ex2-node-2-'.$q.'-3"><td>Size: '.((isset($item_extent_array[$q]) && $item_extent_array[$q] != 'NA') ? $item_extent_array[$q].' '.$item_extent_array[$q]['type'] : 'Not set').'</td></tr>';

                echo '<tr id="ex2-node-2-'.$q.'-4" class="child-of-ex2-node-2-'.$q.'"><td>Annotation</td></tr>';
                if (isset($transcriptionItem_array[$q])) {
                    echo '<tr id="ex2-node-2-'.$q.'-4-t" class="child-of-ex2-node-2-'.$q.'-4"><td>Transcription:';
                    echo '<tr id="ex2-node-2-'.$q.'-4-t-1" class="child-of-ex2-node-2-'.$q.'-4-t"><td>';
                    if ($transcriptionItem_array > 0) {
                        echo '<ul>';
                        foreach ($transcriptionItem_array[$q] as $transcriptionItem_array_value) {
                            echo '<li class="docannotation link">'.$transcriptionItem_array_value['corresp'].'</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</td></tr>';
                    echo '</td></tr>';
                }

                if (isset($graphicalItem_array[$q])) {
                    echo '<tr id="ex2-node-2-'.$q.'-4-g" class="child-of-ex2-node-2-'.$q.'-4"><td> Graphical: ';
                    echo '<tr id="ex2-node-2-'.$q.'-4-g-1" class="child-of-ex2-node-2-'.$q.'-4-g"><td>';
                    if ($graphicalItem_array > 0) {
                        echo '<ul>';
                        foreach ($graphicalItem_array[$q] as $graphicalItem_array_value) {
                            echo '<li class="docannotation link">'.$graphicalItem_array_value['corresp'].'</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</td></tr>';
                    echo '</td></tr>';
                }

                if (isset($lexicalItem_array[$q])) {
                    echo '<tr id="ex2-node-2-'.$q.'-4-l" class="child-of-ex2-node-2-'.$q.'-4"><td> Lexical: ';
                    echo '<tr id="ex2-node-2-'.$q.'-4-l-1" class="child-of-ex2-node-2-'.$q.'-4-l"><td>';
                    if ($lexicalItem_array > 0) {
                        echo '<ul>';
                        foreach ($lexicalItem_array[$q] as $lexicalItem_array_value) {
                            echo '<li class="docannotation link">'.$lexicalItem_array_value['corresp'].'</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</td></tr>';
                    echo '</td></tr>';
                }

                if (isset($metaItem_array[$q])) {
                    echo '<tr id="ex2-node-2-'.$q.'-4-m" class="child-of-ex2-node-2-'.$q.'-4"><td> Meta: ';
                    echo '<tr id="ex2-node-2-'.$q.'-4-m-1" class="child-of-ex2-node-2-'.$q.'-4-m"><td>';
                    if ($metaItem_array > 0) {
                        echo '<ul>';
                        foreach ($metaItem_array[$q] as $metaItem_array_value) {
                            echo '<li class="docannotation link">'.$metaItem_array_value['corresp'].'</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</td></tr>';
                    echo '</td></tr>';
                }

                if (isset($otherItem_array[$q])) {
                    echo '<tr id="ex2-node-2-'.$q.'-4-o" class="child-of-ex2-node-2-'.$q.'-4"><td> Other: ';
                    echo '<tr id="ex2-node-2-'.$q.'-4-o-1" class="child-of-ex2-node-2-'.$q.'-4-o"><td>';
                    if ($otherItem_array > 0) {
                        echo '<ul>';
                        foreach ($otherItem_array[$q] as $otherItem_array_value) {
                            echo '<li class="docannotation link">'.$otherItem_array_value['corresp'].'</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</td></tr>';
                    echo '</td></tr>';
                }


                if (isset($syntacticalItem_array[$q])) {
                    echo '<tr id="ex2-node-2-'.$q.'-4-1-1" class="child-of-ex2-node-2-'.$q.'-4-1"><td> Syntactical: ';
                    echo '<tr id="ex2-node-2-'.$q.'-4-s-1" class="child-of-ex2-node-2-'.$q.'-4-s"><td>';
                    if ($syntacticalItem_array > 0) {
                        echo '<ul>';
                        foreach ($syntacticalItem_array[$q] as $syntacticalItem_array_value) {
                            echo '<li class="docannotation link">'.$syntacticalItem_array_value['corresp'].'</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</td></tr>';
                    echo '</td></tr>';
                }
                /*
                                if (isset($annotListItem_array)) {
                                    echo '<tr id="ex2-node-2-'.$q.'-4-1" class="child-of-ex2-node-2-'.$q.'-4"><td>AnnotationList</td></tr>';
                                    echo '<tr id="ex2-node-2-'.$q.'-4-1-1" class="child-of-ex2-node-2-'.$q.'-4-1"><td>';

                                    if ($annotListItem_array > 0) {
                                        echo '<ul>';
                                        foreach ($annotListItem_array[$q] as $annotListItem_array_value) {


                                                echo '<li class="docannotation link">'.$annotListItem_array_value['corresp'].'</li>';
                                        }
                                        echo '</ul>';
                                    }
                                    echo '</td></tr>';
                                }
                */
                echo '<tr id="ex2-node-2-'.$q.'-5" class="child-of-ex2-node-2-'.$q.'"><td>Revision</td></tr>';
                $x=1;
                foreach ($item_revision_array[$q]->change as $changeDesc) {
                    if ($changeDesc['n'] !='') {
                        echo '<tr id="ex2-node-2-'.$q.'-5-'.$x.'" class="child-of-ex2-node-2-'.$q.'-5"><td>Version: '.$changeDesc['n'].'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-5-'.$x.'-1" class="child-of-ex2-node-2-'.$q.'-5-'.$x.'"><td>Date: '.($changeDesc['when'] != 'NA' ? $changeDesc['when'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-5-'.$x.'-2" class="child-of-ex2-node-2-'.$q.'-5-'.$x.'"><td>Who: '.($changeDesc['who'] != 'NA' ? $changeDesc['who'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-5-'.$x.'-3" class="child-of-ex2-node-2-'.$q.'-5-'.$x.'"><td>Description: '.($changeDesc != 'NA' ? $changeDesc : 'Not set').'</td></tr>';
                        $x++;
                    }
                }

                if (isset($sourceDesc_array[$q])) {
                    echo '<tr id="ex2-node-2-'.$q.'-6" class="child-of-ex2-node-2-'.$q.'"><td>Source Description</td></tr>';
                    $y=1;
                    foreach ($sourceDesc_array[$q]->msDesc as $msDesc) {
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Alternative Name: '.(($msDesc->msIdentifier->msName != "NA") ? $msDesc->msIdentifier->msName : "Not set").'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Repository: '.(($msDesc->msIdentifier->altIdentifier->repository != "NA") ? $msDesc->msIdentifier->altIdentifier->repository : "Not set").'</td></tr>';

                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Collection: '.(($msDesc->msIdentifier->altIdentifier->collection != "NA") ? $msDesc->msIdentifier->altIdentifier->collection : "Not set").'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Object Type: '.(($msDesc->history->origin->objectType != "NA") ? $msDesc->history->origin->objectType : "Not set").'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Original Date: '.(($msDesc->history->origin->origDate != "NA") ? $msDesc->history->origin->origDate : "Not set").'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Not before: '.(($msDesc->history->origin->origDate['notBefore-custom'] != "NA") ? $msDesc->history->origin->origDate['notBefore-custom'] : "Not set").', Not after: '.(($msDesc->history->origin->origDate['notAfter-custom'] != "NA") ? $msDesc->history->origin->origDate['notAfter-custom'] : "Not set").'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Original Place: '.(($msDesc->history->origin->origPlace != "NA") ? $msDesc->history->origin->origPlace : "Not set").'</td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$y.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Title: '.(($msDesc->history->origin->title != "NA") ? $msDesc->history->origin->title : "Not set").'</td></tr>';
                        $y++;
                    }

                    $w=1;
                    foreach ($sourceDesc_array[$q]->recordHist as $recordHist) {
                        #'.(($appInfo_value->application['subtype'] != 'NA') ? $appInfo_value->application['subtype'] : 'Not set').'
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$w.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Source: '.(($recordHist->source['facs'] !="NA") ? '<a href="'.$recordHist->source['facs'].'">'.$recordHist->source['facs'] : "Not set").'</a></td></tr>';
                        echo '<tr id="ex2-node-2-'.$q.'-6-'.$w.'" class="child-of-ex2-node-2-'.$q.'-6"><td>Repertorium:'.(($recordHist->source->ref != "NA") ? '<a href="'.$recordHist->source->ref['target'].'">'.$recordHist->source->ref : "Not set").'</a></td></tr>';
                        $w++;
                    }
                }

                if (isset($langUsageItem_array[$q])) {
                    $p=1;
                    foreach ($langUsageItem_array[$q]->language as $language) {
                        if ($language['style'] == 'Language') {
                            echo '<tr id="ex2-node-2-'.$q.'-7-'.$p.'" class="child-of-ex2-node-2-'.$q.'"><td>Language: '.(($language != "NA") ? $language : "Not set").'</td></tr>';
                        }
                        elseif ($language['style'] == '' && $language != '') {
                            echo '<tr id="ex2-node-2-'.$q.'-7-'.$p.'" class="child-of-ex2-node-2-'.$q.'"><td>Language: '.(($language != "NA") ? $language : "Not set").'</td></tr>';
                        }
                        if ($language['style'] == 'LanguageType') {
                            echo '<tr id="ex2-node-2-'.$q.'-7-'.$p.'" class="child-of-ex2-node-2-'.$q.'"><td>Language Type: '.(($language != "NA") ? $language : "Not set").'</td></tr>';
                        }

                        if ($language['style'] == 'LanguageArea') {
                            echo '<tr id="ex2-node-2-'.$q.'-7-'.$p.'" class="child-of-ex2-node-2-'.$q.'"><td>Language Area: '.(($language != "NA") ? $language : "Not set").'</td></tr>';
                        }
                        $p++;
                    }
                }
            }
        }
    }

    public function objects_schema2_preparation($value,$version){
        #echo "<meta charset='utf-8' />";
        #$this->Html->charset('ISO-8859-1');
        $this->autoRender = false;
        $this->loadModel('XMLObject');

        $date = substr($version,strrpos($version,'_')+1);
        $version = substr($version,0,strrpos($version,'_'));
        //$scheme = substr($version,strrpos($version,'_')+1);

        $result_array = $this->XMLObject->getObjectTEIXmlVersion($value,$version,$date);
        $xmlObject = Xml::build($result_array['response']);// Here will throw a Exception
        if (isset($xmlObject->teiHeader)) {

            $prep_editor_forename_array = array();
            $prep_editor_surname_array = array();
            $prep_editor_department_name_array = array();
            $prep_editor_institution_name_array = array();
            $prep_author_forename_array = array();
            $prep_author_surname_array = array();
            $prep_author_department_name_array = array();
            $prep_author_institution_name_array = array();
            $prep_encodingDesc_array = array();
            $layer_title_array = array();
            $prep_publicationStmt_array = array();
            $prep_ref_target_array = array();
            $prep_extent_array = array();
            $preparation_header_array = array();
            $revisionDesc_array = array();
            $prep_author_orgname_array = array();


            $editor_surname_array = array();
            $author_surname_array = array();

            //Corpus
            for($x=0;$x<sizeof($xmlObject->teiHeader->fileDesc->titleStmt->editor);$x++) {
                if (isset($xmlObject->teiHeader->fileDesc->titleStmt->editor[$x]->persName->surname)) {
                    foreach ($xmlObject->teiHeader->fileDesc->titleStmt->editor[$x]->persName->surname as $editor_surname) {
                        array_push($editor_surname_array, $editor_surname);
                    }
                }
            }

            for($y=0;$y<sizeof($xmlObject->teiHeader->fileDesc->titleStmt->author);$y++) {
                if (isset($xmlObject->teiHeader->fileDesc->titleStmt->author[$y]->persName->surname)) {
                    foreach ($xmlObject->teiHeader->fileDesc->titleStmt->author[$y]->persName->surname as $author_surname) {
                        array_push($author_surname_array, $author_surname);
                    }
                }
            }

            //PreparationStep
            $total = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader);
            for($g=0;$g<$total;$g++) {
                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->encodingDesc as $preparation_header) {
                    if (isset($preparation_header)) {
                        $encodingDescs = array();

                        $encodingDescs[][$g] = $preparation_header;

                        array_push($preparation_header_array, $encodingDescs);
                    }
                }

                // revisionDesc
                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->revisionDesc as $revisionDesc) {
                    array_push($revisionDesc_array, $revisionDesc);
                }


                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->title as $layer_title) {
                    array_push($layer_title_array, $layer_title);
                }

                $total2 = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor);
                for($x=0;$x<$total2;$x++) {
                    foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->persName->forename as $prep_editor_forename) {
                        array_push($prep_editor_forename_array, $prep_editor_forename);
                    }

                    foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->persName->surname as $prep_editor_surname) {
                        array_push($prep_editor_surname_array, $prep_editor_surname);
                    }

                    $total3 = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor->affiliation->orgName);
                    for($q =0; $q < $total3;$q++){
                        if ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q]->attributes() == 'Department') {
                            $prep_editor_department_name = $xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q];

                            array_push($prep_editor_department_name_array, $prep_editor_department_name);
                        }
                        elseif ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q]->attributes() == 'Institution') {
                            $prep_editor_institution_name = $xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q];

                            array_push($prep_editor_institution_name_array, $prep_editor_institution_name);
                        }

                        foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q] as $prep_editor_orgname) {
                            array_push($prep_editor_orgname_array, $prep_editor_orgname);
                        }
                    }
                }

                $total4 = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author);
                for($y =0; $y < $total4;$y++){
                    foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->persName->forename as $prep_author_forename) {
                        array_push($prep_author_forename_array, $prep_author_forename);
                    }

                    foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->persName->surname as $prep_author_surname) {
                        array_push($prep_author_surname_array, $prep_author_surname);
                    }

                    $total5 = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author->affiliation->orgName);
                    for($q =0; $q < $total5;$q++) {
                        if ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q]->attributes() == 'Department') {
                            $prep_author_department_name = $xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q];

                            array_push($prep_author_department_name_array, $prep_author_department_name);
                        }
                        elseif ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q]->attributes() == 'Institution') {
                            $prep_author_institution_name = $xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q];

                            array_push($prep_author_institution_name_array, $prep_author_institution_name);
                        }

                        foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q] as $prep_author_orgname) {
                            array_push($prep_author_orgname_array, $prep_author_orgname);
                        }
                    }
                }

                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->extent as $prep_extent) {
                    array_push($prep_extent_array, $prep_extent);
                }

                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->publicationStmt->date as $prep_publicationStmt) {
                    array_push($prep_publicationStmt_array, $prep_publicationStmt);
                }

                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->sourceDesc->p->ref as $prep_ref_target) {
                    array_push($prep_ref_target_array, $prep_ref_target['target']);
                }

                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->encodingDesc as $prep_encodingDesc) {
                    array_push($prep_encodingDesc_array, $prep_encodingDesc);
                }
            }

            foreach ($layer_title_array as $attrib => $layer_title_array_value) {
                echo '<tr id="ex2-node-4-'.$attrib.'" class="child-of-ex2-node-4" title="PreparationStep '.$layer_title_array_value.'">';
                echo '<td>'.$layer_title_array_value.'</td>';
                echo '</tr>';

                echo '<tr id="ex2-node-4-'.$attrib.'-0" class="child-of-ex2-node-4-'.$attrib.'"><td>Authorship</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-0-1" class="child-of-ex2-node-4-'.$attrib.'-0"><td>Corpus Editor</td></tr>';
                $total_editor_surname_array = (int)count($editor_surname_array);
                for($x=0;$x<$total_editor_surname_array;$x++) {
                    echo '<tr id="ex2-node-4-'.$attrib.'-0-1-'.$x.'" class="child-of-ex2-node-4-'.$attrib.'-0-1">';
                    echo '<td>'.$prep_editor_forename_array[$x].' '.$prep_editor_surname_array[$x].'</td>';
                    echo ' ';

                    echo '</tr>';

                    if (isset($prep_editor_institution_name_array[$x])) {
                        echo '<tr id="ex2-node-4-'.$attrib.'-0-1-1-'.$x.'" class="child-of-ex2-node-4-'.$attrib.'-0-1-'.$x.'"><td>Affiliation: '.$prep_editor_department_name_array[$x].', '.$prep_editor_institution_name_array[$x].'</td></tr>';
                    }
                }

                echo '<tr id="ex2-node-4-'.$attrib.'-0-2" class="child-of-ex2-node-4-'.$attrib.'-0"><td>Annotator</td></tr>';
                $total_author_surname_array = (int)count($author_surname_array);
                for($y=0;$y<$total_author_surname_array;$y++) {
                    if ($prep_author_forename_array[$y] != 'NA') {
                        echo '<tr id="ex2-node-4-'.$attrib.'-0-2-'.$y.'" class="child-of-ex2-node-4-'.$attrib.'-0-2">';
                        echo '<td>'.$prep_author_forename_array[$y].' '.$prep_author_surname_array[$y].'</td>';
                        echo ' ';

                        echo '</tr>';

                        if (isset($prep_author_institution_name_array[$y])) {
                            echo '<tr id="ex2-node-4-'.$attrib.'-0-2-1-'.$y.'" class="child-of-ex2-node-4-'.$attrib.'-0-2-'.$y.'"><td>Affiliation: '.$prep_author_department_name_array[$y].' '.$prep_author_institution_name_array[$y].'</td></tr>';
                        }
                    }
                    else {
                        echo '<tr id="ex2-node-4-'.$attrib.'-0-2-'.$y.'" class="child-of-ex2-node-4-'.$attrib.'-0-2"><td>Not set</td>';
                    }
                }
                echo '</tr>';

                echo '<tr id="ex2-node-4-'.$attrib.'-1" class="child-of-ex2-node-4-'.$attrib.'"><td>Availability</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Release: '.((@$prep_publicationStmt_array[$attrib]['when'] != "" && $prep_publicationStmt_array[$attrib]['when'] != "N/A") ? $prep_publicationStmt_array[$attrib]['when'].', '.$prep_publicationStmt_array[$attrib] : "Not set").'</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-1-2" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Source: '.((@$prep_ref_target_array[$attrib] != "" && $prep_ref_target_array[$attrib] != "N/A" && $prep_ref_target_array[$attrib] != "NA") ? 'PDF - <a href='.$prep_ref_target_array[$attrib].'>Hyperlink</a>' : "Not set").'</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-1-3" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Size: '.((@$prep_extent_array[$attrib] != "" && $prep_extent_array[$attrib] != "N/A" && $prep_extent_array[$attrib] != "NA") ? $prep_extent_array[$attrib].' '.$prep_extent_array[$attrib]['type'] : "Not set").'</td></tr>';

                echo '<tr id="ex2-node-4-'.$attrib.'-2" class="child-of-ex2-node-4-'.$attrib.'"><td>Encoding</td></tr>';

                $total_preparation_header_array = (int)count($preparation_header_array);
                for($r=0;$r<$total_preparation_header_array;$r++) {
                    foreach ($preparation_header_array[$r] as $key => $arr) {

                        foreach(array_keys($arr) as $key) {
                            if ($key===$attrib) {
                                $key_gen = $arr[$key]["n"];
                                echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2"><td>'.@$arr[$key]["style"].'</td></tr>';

                                foreach ($arr[$key]->appInfo as $appInfo_value) {
                                    $appInfo_n_value = $appInfo_value['n'];

                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Tool: '.$appInfo_value->application->label.'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Version: '.$appInfo_value->application['version'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-2" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Label: '.$appInfo_value->application->label.'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-3" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Format: '.$appInfo_value['style'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-4" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Annotation Class: '.$appInfo_value->application['type'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-5" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Subtype: '.(($appInfo_value->application['subtype'] != 'NA') ? $appInfo_value->application['subtype'] : 'Not set').'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-6" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Description: '.(($appInfo_value->application->p != 'NA') ? $appInfo_value->application->p : 'Not set').'</td></tr>';
                                }
                                foreach ($arr[$key]->editorialDecl as $editorialDecl_value) {
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Normalization</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$key_gen.'"><td>Method: '.$editorialDecl_value->normalization['method'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$key_gen.'"><td>Description: '.(($editorialDecl_value->normalization->p != 'NA') ? $editorialDecl_value->normalization->p : 'Not set').'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Segmentation</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Corresp: '.(($editorialDecl_value->segmentation['corresp'] != '' && $editorialDecl_value->segmentation['corresp'] != 'NA' && $editorialDecl_value->segmentation['corresp'] != NULL) ? $editorialDecl_value->segmentation['corresp'] : 'Not set').'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Style: '.$editorialDecl_value->segmentation['style'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-3" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Description: '.(($editorialDecl_value->segmentation->p != 'NA') ? $editorialDecl_value->segmentation->p : 'Not set').'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Correction</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Method: '.$editorialDecl_value->correction['method'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Status: '.$editorialDecl_value->correction['status'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-3" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Description: '.$editorialDecl_value->correction->p.'</td></tr>';
                                }
                            }
                        }
                    }
                }

                echo '<tr id="ex2-node-4-'.$attrib.'-3" class="child-of-ex2-node-4-'.$attrib.'"><td>Revision</td></tr>';
                foreach ($revisionDesc_array[$attrib]->change as $changeDesc) {
                    if ($changeDesc['type'] != 'QualityCheck') {
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-1" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Version: '.$changeDesc['n'].'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-1-1" class="child-of-ex2-node-4-'.$attrib.'-3-1"><td>Date: '.($changeDesc['when'] != 'NA' ? $changeDesc['when'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-1-2" class="child-of-ex2-node-4-'.$attrib.'-3-1"><td>Who: '.($changeDesc['who'] != 'NA' ? $changeDesc['who'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-1-3" class="child-of-ex2-node-4-'.$attrib.'-3-1"><td>Description: '.($changeDesc != 'NA' ? $changeDesc : 'Not set').'</td></tr>';
                    }
                    else if($changeDesc['type'] == 'QualityCheck') {
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-2" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Version: '.$changeDesc['n'].'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-2-1" class="child-of-ex2-node-4-'.$attrib.'-3-2"><td>Date: '.($changeDesc['when'] != 'NA' ? $changeDesc['when'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-2-2" class="child-of-ex2-node-4-'.$attrib.'-3-2"><td>Who: '.($changeDesc['who'] != 'NA' ? $changeDesc['who'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-2-3" class="child-of-ex2-node-4-'.$attrib.'-3-2"><td>Description: '.($changeDesc != 'NA' ? $changeDesc : 'Not set').'</td></tr>';
                    }
                }
            }
        }
    }

    public function objects_schema6_preparation($value,$version){
        #echo "<meta charset='utf-8' />";
        #$this->Html->charset('ISO-8859-1');
        $this->autoRender = false;
        $this->loadModel('XMLObject');

        $date = substr($version,strrpos($version,'_')+1);
        $version = substr($version,0,strrpos($version,'_'));
        //$scheme = substr($version,strrpos($version,'_')+1);

        $result_array = $this->XMLObject->getObjectTEIXmlVersion($value,$version,$date);
        $xmlObject = Xml::build($result_array['response']);// Here will throw a Exception
        if (isset($xmlObject->teiHeader)) {

            $prep_editor_forename_array = array();
            $prep_editor_surname_array = array();
            $prep_editor_department_name_array = array();
            $prep_editor_institution_name_array = array();
            $prep_author_forename_array = array();
            $prep_author_surname_array = array();
            $prep_author_department_name_array = array();
            $prep_author_institution_name_array = array();
            $prep_encodingDesc_array = array();
            $layer_title_array = array();
            $prep_publicationStmt_array = array();
            $prep_ref_target_array = array();
            $prep_extent_array = array();
            $preparation_header_array = array();
            $revisionDesc_array = array();
            $prep_author_orgname_array = array();


            $editor_surname_array = array();
            $author_surname_array = array();

            //Corpus
            for($x=0;$x<sizeof($xmlObject->teiHeader->fileDesc->titleStmt->editor);$x++) {
                if (isset($xmlObject->teiHeader->fileDesc->titleStmt->editor[$x]->persName->surname)) {
                    foreach ($xmlObject->teiHeader->fileDesc->titleStmt->editor[$x]->persName->surname as $editor_surname) {
                        array_push($editor_surname_array, $editor_surname);
                    }
                }
            }

            for($y=0;$y<sizeof($xmlObject->teiHeader->fileDesc->titleStmt->author);$y++) {
                if (isset($xmlObject->teiHeader->fileDesc->titleStmt->author[$y]->persName->surname)) {
                    foreach ($xmlObject->teiHeader->fileDesc->titleStmt->author[$y]->persName->surname as $author_surname) {
                        array_push($author_surname_array, $author_surname);
                    }
                }
            }

            //PreparationStep
            $total = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader);
            for($g=0;$g<$total;$g++) {
                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->encodingDesc as $preparation_header) {
                    if (isset($preparation_header)) {
                        $encodingDescs = array();

                        $encodingDescs[][$g] = $preparation_header;

                        array_push($preparation_header_array, $encodingDescs);
                    }
                }

                // revisionDesc
                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->revisionDesc as $revisionDesc) {
                    array_push($revisionDesc_array, $revisionDesc);
                }


                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->title as $layer_title) {
                    array_push($layer_title_array, $layer_title);
                }

                $total2 = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor);
                for($x=0;$x<$total2;$x++) {
                    foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->persName->forename as $prep_editor_forename) {
                        array_push($prep_editor_forename_array, $prep_editor_forename);
                    }

                    foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->persName->surname as $prep_editor_surname) {
                        array_push($prep_editor_surname_array, $prep_editor_surname);
                    }

                    $total3 = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor->affiliation->orgName);
                    for($q =0; $q < $total3;$q++){
                        if ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q]->attributes() == 'Department') {
                            $prep_editor_department_name = $xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q];

                            array_push($prep_editor_department_name_array, $prep_editor_department_name);
                        }
                        elseif ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q]->attributes() == 'Institution') {
                            $prep_editor_institution_name = $xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q];

                            array_push($prep_editor_institution_name_array, $prep_editor_institution_name);
                        }

                        foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->editor[$x]->affiliation->orgName[$q] as $prep_editor_orgname) {
                            array_push($prep_editor_orgname_array, $prep_editor_orgname);
                        }
                    }
                }

                $total4 = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author);
                for($y =0; $y < $total4;$y++){
                    foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->persName->forename as $prep_author_forename) {
                        array_push($prep_author_forename_array, $prep_author_forename);
                    }

                    foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->persName->surname as $prep_author_surname) {
                        array_push($prep_author_surname_array, $prep_author_surname);
                    }

                    $total5 = (int)count($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author->affiliation->orgName);
                    for($q =0; $q < $total5;$q++) {
                        if ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q]->attributes() == 'Department') {
                            $prep_author_department_name = $xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q];

                            array_push($prep_author_department_name_array, $prep_author_department_name);
                        }
                        elseif ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q]->attributes() == 'Institution') {
                            $prep_author_institution_name = $xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q];

                            array_push($prep_author_institution_name_array, $prep_author_institution_name);
                        }

                        foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->titleStmt->author[$y]->affiliation->orgName[$q] as $prep_author_orgname) {
                            array_push($prep_author_orgname_array, $prep_author_orgname);
                        }
                    }
                }

                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->extent as $prep_extent) {
                    array_push($prep_extent_array, $prep_extent);
                }

                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->publicationStmt->date as $prep_publicationStmt) {
                    array_push($prep_publicationStmt_array, $prep_publicationStmt);
                }

                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->fileDesc->sourceDesc->p->ref as $prep_ref_target) {
                    array_push($prep_ref_target_array, $prep_ref_target['target']);
                }

                foreach ($xmlObject->teiCorpus->teiCorpus->teiHeader[$g]->encodingDesc as $prep_encodingDesc) {
                    array_push($prep_encodingDesc_array, $prep_encodingDesc);
                }
            }

            foreach ($layer_title_array as $attrib => $layer_title_array_value) {
                echo '<tr id="ex2-node-4-'.$attrib.'" class="child-of-ex2-node-4" title="PreparationStep '.$layer_title_array_value.'">';
                echo '<td>'.$layer_title_array_value.'</td>';
                echo '</tr>';

                echo '<tr id="ex2-node-4-'.$attrib.'-0" class="child-of-ex2-node-4-'.$attrib.'"><td>Authorship</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-0-1" class="child-of-ex2-node-4-'.$attrib.'-0"><td>Corpus Editor</td></tr>';
                $total_editor_surname_array = (int)count($editor_surname_array);
                for($x=0;$x<$total_editor_surname_array;$x++) {
                    echo '<tr id="ex2-node-4-'.$attrib.'-0-1-'.$x.'" class="child-of-ex2-node-4-'.$attrib.'-0-1">';
                    echo '<td>'.$prep_editor_forename_array[$x].' '.$prep_editor_surname_array[$x].'</td>';
                    echo ' ';

                    echo '</tr>';

                    if (isset($prep_editor_institution_name_array[$x])) {
                        echo '<tr id="ex2-node-4-'.$attrib.'-0-1-1-'.$x.'" class="child-of-ex2-node-4-'.$attrib.'-0-1-'.$x.'"><td>Affiliation: '.$prep_editor_department_name_array[$x].', '.$prep_editor_institution_name_array[$x].'</td></tr>';
                    }
                }

                echo '<tr id="ex2-node-4-'.$attrib.'-0-2" class="child-of-ex2-node-4-'.$attrib.'-0"><td>Annotator</td></tr>';
                $total_author_surname_array = (int)count($author_surname_array);
                for($y=0;$y<$total_author_surname_array;$y++) {
                    if ($prep_author_forename_array[$y] != 'NA') {
                        echo '<tr id="ex2-node-4-'.$attrib.'-0-2-'.$y.'" class="child-of-ex2-node-4-'.$attrib.'-0-2">';
                        echo '<td>'.$prep_author_forename_array[$y].' '.$prep_author_surname_array[$y].'</td>';
                        echo ' ';

                        echo '</tr>';

                        if (isset($prep_author_institution_name_array[$y])) {
                            echo '<tr id="ex2-node-4-'.$attrib.'-0-2-1-'.$y.'" class="child-of-ex2-node-4-'.$attrib.'-0-2-'.$y.'"><td>Affiliation: '.$prep_author_department_name_array[$y].' '.$prep_author_institution_name_array[$y].'</td></tr>';
                        }
                    }
                    else {
                        echo '<tr id="ex2-node-4-'.$attrib.'-0-2-'.$y.'" class="child-of-ex2-node-4-'.$attrib.'-0-2"><td>Not set</td>';
                    }
                }
                echo '</tr>';

                echo '<tr id="ex2-node-4-'.$attrib.'-1" class="child-of-ex2-node-4-'.$attrib.'"><td>Availability</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Release: '.((@$prep_publicationStmt_array[$attrib]['when'] != "" && $prep_publicationStmt_array[$attrib]['when'] != "N/A") ? $prep_publicationStmt_array[$attrib]['when'].', '.$prep_publicationStmt_array[$attrib] : "Not set").'</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-1-2" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Source: ';
                if(@$prep_ref_target_array[$attrib] != "" && @$prep_ref_target_array[$attrib] != "URL" &&$prep_ref_target_array[$attrib] != "N/A" && $prep_ref_target_array[$attrib] != "NA"){
                    echo 'PDF - <a href='.$prep_ref_target_array[$attrib].'>Hyperlink</a>';
                }else{
                    echo "Not set";
                }
                echo '</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-1-3" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Size: '.((@$prep_extent_array[$attrib] != "" && $prep_extent_array[$attrib] != "N/A" && $prep_extent_array[$attrib] != "NA") ? $prep_extent_array[$attrib].' '.$prep_extent_array[$attrib]['type'] : "Not set").'</td></tr>';

                echo '<tr id="ex2-node-4-'.$attrib.'-2" class="child-of-ex2-node-4-'.$attrib.'"><td>Encoding</td></tr>';

                $total_preparation_header_array = (int)count($preparation_header_array);
                for($r=0;$r<$total_preparation_header_array;$r++) {
                    foreach ($preparation_header_array[$r] as $key => $arr) {

                        foreach(array_keys($arr) as $key) {
                            if ($key===$attrib) {
                                $key_gen = $arr[$key]["n"];
                                echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2"><td>'.@$arr[$key]["style"].'</td></tr>';

                                foreach ($arr[$key]->appInfo as $appInfo_value) {
                                    $appInfo_n_value = $appInfo_value['n'];

                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Tool: '.$appInfo_value->application->label.'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Version: '.$appInfo_value->application['version'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-2" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Label: '.$appInfo_value->application->label.'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-3" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Format: '.$appInfo_value['style'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-4" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Annotation Class: '.$appInfo_value->application['type'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-5" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Subtype: '.(($appInfo_value->application['subtype'] != 'NA') ? $appInfo_value->application['subtype'] : 'Not set').'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-6" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Description: '.(($appInfo_value->application->p != 'NA') ? $appInfo_value->application->p : 'Not set').'</td></tr>';
                                }
                                foreach ($arr[$key]->editorialDecl as $editorialDecl_value) {
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Normalization</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$key_gen.'"><td>Method: '.$editorialDecl_value->normalization['method'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$key_gen.'"><td>Description: '.(($editorialDecl_value->normalization->p != 'NA') ? $editorialDecl_value->normalization->p : 'Not set').'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Segmentation</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Corresp: '.(($editorialDecl_value->segmentation['corresp'] != '' && $editorialDecl_value->segmentation['corresp'] != 'NA' && $editorialDecl_value->segmentation['corresp'] != NULL) ? $editorialDecl_value->segmentation['corresp'] : 'Not set').'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Style: '.$editorialDecl_value->segmentation['style'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-3" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Description: '.(($editorialDecl_value->segmentation->p != 'NA') ? $editorialDecl_value->segmentation->p : 'Not set').'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Correction</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Method: '.$editorialDecl_value->correction['method'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Status: '.$editorialDecl_value->correction['status'].'</td></tr>';
                                    echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-3" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Description: '.$editorialDecl_value->correction->p.'</td></tr>';
                                }
                            }
                        }
                    }
                }

                echo '<tr id="ex2-node-4-'.$attrib.'-3" class="child-of-ex2-node-4-'.$attrib.'"><td>Revision</td></tr>';
                foreach ($revisionDesc_array[$attrib]->change as $changeDesc) {
                    if ($changeDesc['type'] != 'QualityCheck') {
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-1" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Version: '.$changeDesc['n'].'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-1-1" class="child-of-ex2-node-4-'.$attrib.'-3-1"><td>Date: '.($changeDesc['when'] != 'NA' ? $changeDesc['when'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-1-2" class="child-of-ex2-node-4-'.$attrib.'-3-1"><td>Who: '.($changeDesc['who'] != 'NA' ? $changeDesc['who'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-1-3" class="child-of-ex2-node-4-'.$attrib.'-3-1"><td>Description: '.($changeDesc != 'NA' ? $changeDesc : 'Not set').'</td></tr>';
                    }
                    else if($changeDesc['type'] == 'QualityCheck') {
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-2" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Version: '.$changeDesc['n'].'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-2-1" class="child-of-ex2-node-4-'.$attrib.'-3-2"><td>Date: '.($changeDesc['when'] != 'NA' ? $changeDesc['when'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-2-2" class="child-of-ex2-node-4-'.$attrib.'-3-2"><td>Who: '.($changeDesc['who'] != 'NA' ? $changeDesc['who'] : 'Not set').'</td></tr>';
                        echo '<tr id="ex2-node-4-'.$attrib.'-3-2-3" class="child-of-ex2-node-4-'.$attrib.'-3-2"><td>Description: '.($changeDesc != 'NA' ? $changeDesc : 'Not set').'</td></tr>';
                    }
                }
            }
        }
    }
}