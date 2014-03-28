<?php
echo $this->Html->script('uploadscript',array('inline' => false));
echo $this->Html->script('jquery.treeTable.js',array('inline' => false));
echo $this->Html->css('/js/css/jquery.treeTable.css', null, array('inline' => false));

$this->set('navbarArray',array(array('Object')));
if(isset($noTEI) && $noTei ==true){
	echo 'No TEI-Header available.';
}
else {
	$xml = simplexml_load_string(html_entity_decode($getObjectVersionXml)) or die("Error: Can not create object");

		$ann_link ='https://korpling.german.hu-berlin.de/annis3-snapshot/#c='.$pid.'';
        echo "<div style=\"float:right;text-align:right;\">";
        echo $this->Html->image("annis_192.png",array( //annis.ico
            'width'=>'40px',
            "border" => "0",
            "alt" => "Link to ANNIS",
            "url" => $ann_link,
            "fullBase" => true
        ));
        echo "</div>";
        
	echo '<h3>'.utf8_decode($title).'</h3>';
      
	echo "<tr><td><select id='versionDate' onchange='changeVersion(\"".urlencode($pid)."\")' name='dateObject'>";
	$x=0;
	
	foreach ($xml->objectChangeDate as $objectChangeDate) {
		if ($x > 0) {
			if ($versionDate != null) {
				if ($objectChangeDate == $versionDate) {
					echo "<option selected>".$objectChangeDate."</option>";
				} 
				else {
					echo "<option>".$objectChangeDate."</option>";
				}
			}
			else {
				if ($x==count($xml->objectChangeDate)-1) {
					var_dump($objectChangeDate);
					echo "<option selected>".$objectChangeDate."</option>";  
				}
				else {
					echo "<option>".$objectChangeDate."</option>";
				}			        
			}
		}
		$x++;		
	}

	echo "</select>";
		echo '<table id="tree">';
		echo '<tr><td>'.$title.', '.$authority.', '.$version.'';
		
		if ($extent !="N/A"){
			echo ', '.$extent.' Token';
		}
			
			for ($x=0;$x<sizeof($download_format);) {
				#if ($download_format[$x] == 'TEIXML' || $download_format[$x] == 'XML') {
				#	$download='http://depot1-7.cms.hu-berlin.de:8080/fedora/objects/'.$pid.'/datastreams/TEI_header/content';
				#}
				#elseif ($download_format[$x] == 'EXMARaLDA') {
					$download = "http://141.20.4.72:8080/fedora/get/".$pid."/".ucfirst($download_format[$x])."";
				#}
				
				#elseif ($download_format[$x] == 'relANNIS') {
				#	$download = "http://141.20.4.72:8080/fedora/get/".$pid."/".$download_format[$x]."";
				#}
				
				#else {
					$download = "http://141.20.4.72:8080/fedora/get/".$pid."/".$download_format[$x]."";
				#}
				echo ", ".$this->Html->link($download_format[$x], $download);
				$x++;
			}



		
		if (isset($homepage)) {
			echo '<tr><td><a href="'.$homepage.'">'.$homepage.'</a></td></tr>';
		}
		
		echo '<tr id="ex2-node-1"><td><b>Corpus '.$title.'</b>';
		echo '<span class="helptooltip"><p>This section contains metadata for the object &lsquo;corpus&rsquo;. The publisher including references for the research project the annotators and the formats of the corpus which are available are presented in this section.</p>';
		echo $this->Html->image("help.png",array(
                    "border" => "0",
                    'width'=>'20px',
                    "alt" => "help",
                ));
		echo '</span></td></tr>';
			echo '<tr id="ex2-node-1-1" class="child-of-ex2-node-1"><td>Authorship</td></tr>';
				echo '<tr id="ex2-node-1-1-1" class="child-of-ex2-node-1-1"><td>Corpus Editor</td></tr>';
					for ($x=0;$x<sizeof($editor_surname_array);$x++) {
						echo '<tr id="ex2-node-1-1-1-'.$x.'" class="child-of-ex2-node-1-1-1">';
							echo '<td>'.$editor_forename_array[$x].' '.$editor_surname_array[$x].'</td>';
							echo ' ';
							
							echo '</tr>';

							if (isset($editor_institution_name_array[$x])) {
								echo '<tr id="ex2-node-1-1-1-1-'.$x.'" class="child-of-ex2-node-1-1-1-'.$x.'"><td>Affiliation: '.$editor_department_name_array[$x].', '.$editor_institution_name_array[$x].'</td></tr>';
							}
					}
					
				echo '<tr id="ex2-node-1-1-2" class="child-of-ex2-node-1-1"><td>Corpus Annotator</td></tr>';
					for ($y=0;$y<sizeof($author_surname_array);$y++) {
						if ($author_surname_array[$y] != 'NA' || NULL) {
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
			echo '<tr id="ex2-node-1-2" class="child-of-ex2-node-1"><td>Project</td></tr>';
				echo '<tr id="ex2-node-1-2-1" class="child-of-ex2-node-1-2"><td>Homepage</td></tr>';
					if (isset($homepage)) {
						echo '<tr id="ex2-node-1-2-1-1" class="child-of-ex2-node-1-2-1"><td><a href="'.$homepage.'">'.$homepage.'</a></td></tr>';
					}
				echo '<tr id="ex2-node-1-2-2" class="child-of-ex2-node-1-2"><td>Description</td></tr>';
					if (isset($description->p)) {
						echo '<tr id="ex2-node-1-2-2-1" class="child-of-ex2-node-1-2-2"><td>'.$description->p.'</td></tr>';
					}
			echo '<tr id="ex2-node-1-3" class="child-of-ex2-node-1"><td>Availability Status: '.$av_status['status'].'</td></tr>';
				echo '<tr id="ex2-node-1-3-1" class="child-of-ex2-node-1-3"><td>'.$project.'</td></tr>';
				echo '<tr id="ex2-node-1-3-2" class="child-of-ex2-node-1-3"><td>Authority</td></tr>';
					echo '<tr id="ex2-node-1-3-2-1" class="child-of-ex2-node-1-3-2"><td>'.$authority.'</td></tr>';
				echo '<tr id="ex2-node-1-3-3" class="child-of-ex2-node-1-3"><td>Corpus Release</td></tr>';
					echo '<tr id="ex2-node-1-3-3-1" class="child-of-ex2-node-1-3-3"><td>'.$corpusrelease.'</td></tr>';
			if ($extent != "N/A") {
			echo '<tr id="ex2-node-1-4" class="child-of-ex2-node-1"><td>Extent</td></tr>';
				echo '<tr id="ex2-node-1-4-1" class="child-of-ex2-node-1-4"><td>'.$extent.' '.$extent['type'].'</td></tr>';
			}
			
			echo '<tr id="ex2-node-1-5" class="child-of-ex2-node-1"><td>Documents</td></tr>';
				echo '<tr id="ex2-node-1-5-1" class="child-of-ex2-node-1-5"><td>';
				
				
				for($u=0;$u<sizeof($items_array);$u++) {
					echo $items_array[$u].'<br>';
				}
				echo '</td></tr>';
				echo '<tr id="ex2-node-1-5-2" class="child-of-ex2-node-1-5"><td>Language</td></tr>';
					echo '<tr id="ex2-node-1-5-2-1" class="child-of-ex2-node-1-5-2"><td>'.$language.'</td></tr>';
				
			echo '<tr id="ex2-node-1-6" class="child-of-ex2-node-1"><td>Format</td></tr>';
				
					for($p=0;$p<sizeof($download_format);$p++) {
						echo '<tr id="ex2-node-1-6-'.$p.'" class="child-of-ex2-node-1-6">';
						echo '<td>'.$download_format[$p].'</td>';
						
						echo '</tr>';
						
						if (isset($download_format[$p])) {
							echo '<tr id="ex2-node-1-6-'.$p.'-1" class="child-of-ex2-node-1-6-'.$p.'"><td>Version: '.$version_format[$p].'</td></tr>';
							echo '<tr id="ex2-node-1-6-'.$p.'-1" class="child-of-ex2-node-1-6-'.$p.'"><td>Segmentation: '.$segmentation_array[$p].'</td></tr>';
							echo '<tr id="ex2-node-1-6-'.$p.'-1" class="child-of-ex2-node-1-6-'.$p.'"><td>Normalization: '.$normalization_array[$p].'</td></tr>';
							echo '<tr id="ex2-node-1-6-'.$p.'-1" class="child-of-ex2-node-1-6-'.$p.'"><td>Annotation: ';
							
							if ($download_format[$p] == 'TEIXML') {
								if (isset($namespace_tei_array)) {
									foreach ($namespace_tei_array as $namespace_value) {
										echo $namespace_value.', ';
									}
								}
								else {
									echo "No set";
								}
							
							}
							elseif ($download_format[$p] == 'EXMARaLDA') {
								if (isset($namespace_exb_array)) {
									foreach ($namespace_exb_array as $namespace_value) {
										echo $namespace_value.', ';
									}
								}
								else {
									echo "Not set";
								}
							}
							elseif ($download_format[$p] == 'XML') { 
								if (isset($namespace_xml_array)) {
									foreach ($namespace_xml_array as $namespace_value) {
										echo $namespace_value.', ';
									}
								}
								else {
									echo "Not set";
								}
							}
							elseif (($download_format[$p] == 'relANNIS') || $download_format[$p] == 'ANNIS') { 
								if (isset($namespace_relANNIS_array)) {
									foreach ($namespace_relANNIS_array as $namespace_value) {
										echo $namespace_value.', ';
									}
								}
								else {
									echo "Not set";
								}
							}
							elseif ($download_format[$p] == 'PDF') { 
								if (isset($namespace_pdf_array)) {
									foreach ($namespace_pdf_array as $namespace_value) {
										echo $namespace_value.', ';
									}
								}
								else {
									echo "Not set";
								}
							}
							
							echo '</td></tr>';
						}
					}
		echo '<tr id="ex2-node-2"><td><b>Documents</b>';
		echo '<span class="helptooltip"><p>This section contains classical metadata for the object &lsquo;document&rsquo;. The author, the editor, publication place, bibliographic scope and other bibliographic metadata are presented here.</p>';
		echo $this->Html->image("help.png",array(
			    "border" => "0",
			    'width'=>'20px',
			    "alt" => "help",
			));
		 echo '</span></td></tr>';
				for($q=0;$q<sizeof($items_array);$q++) {
					echo '<tr id="ex2-node-2-'.$q.'" class="child-of-ex2-node-2"><td rel="'.$items_array[$q].'">'.$items_array[$q].'</td></tr>';
						echo '<tr id="ex2-node-2-'.$q.'-1" class="child-of-ex2-node-2-'.$q.'"><td>Title</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-1-1" class="child-of-ex2-node-2-'.$q.'-1"><td>Title</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-1-1-1" class="child-of-ex2-node-2-'.$q.'-1-1"><td>'.$items_array[$q].'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-1-2" class="child-of-ex2-node-2-'.$q.'-1"><td>Register</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-1-2-1" class="child-of-ex2-node-2-'.$q.'-1-2"><td>'.$register_array[$q].'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-1-3" class="child-of-ex2-node-2-'.$q.'-1"><td>Short</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-1-3-1" class="child-of-ex2-node-2-'.$q.'-1-3"><td>'.$short_array[$q].'</td></tr>';
						echo '<tr id="ex2-node-2-'.$q.'-2" class="child-of-ex2-node-2-'.$q.'"><td>Authorship</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-2-1" class="child-of-ex2-node-2-'.$q.'-2"><td>Author</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-2-1-1" class="child-of-ex2-node-2-'.$q.'-2-1"><td>'.(($item_surname_array[$q] != 'NA' || NULL) ? $item_surname_array[$q].', '.$item_forename_array[$q] : 'Not set').'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-2-2" class="child-of-ex2-node-2-'.$q.'-2"><td>Editor</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-2-2-1" class="child-of-ex2-node-2-'.$q.'-2-2"><td>'.(($editor_surname_doc_array[$q] != 'NA' || NULL) ? $editor_surname_doc_array[$q].', '.$editor_forename_doc_array[$q] : 'Not set').'</td></tr>';
						echo '<tr id="ex2-node-2-'.$q.'-3" class="child-of-ex2-node-2-'.$q.'"><td>Publication</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-3-1" class="child-of-ex2-node-2-'.$q.'-3"><td>Publisher</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-3-1-1" class="child-of-ex2-node-2-'.$q.'-3-1"><td>'.(($item_publisher_array[$q] != 'NA' || NULL) ? $item_publisher_array[$q] : 'Not set').'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-3-2" class="child-of-ex2-node-2-'.$q.'-3"><td>Place</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-3-2-1" class="child-of-ex2-node-2-'.$q.'-3-2"><td>'.$item_pubPlace_array[$q].'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-3-3" class="child-of-ex2-node-2-'.$q.'-3"><td>Date</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-3-3-1" class="child-of-ex2-node-2-'.$q.'-3-3"><td>'.$item_publDate_array[$q].'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-3-4" class="child-of-ex2-node-2-'.$q.'-3"><td>Series</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-3-4-1" class="child-of-ex2-node-2-'.$q.'-3-4"><td>'.(($item_series_array[$q] != 'NA' || NULL) ? $item_series_array[$q] : 'Not set').'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-3-5" class="child-of-ex2-node-2-'.$q.'-3"><td>Scope</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-3-5-1" class="child-of-ex2-node-2-'.$q.'-3-5"><td>'.$item_biblScope_array[$q].'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-3-6" class="child-of-ex2-node-2-'.$q.'-3"><td>Extent</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-3-6-1" class="child-of-ex2-node-2-'.$q.'-3-6"><td>'.(($item_extent_array[$q] != 'NA' || NULL) ? $item_extent_array[$q] : 'Not set').'</td></tr>';
						
						echo '<tr id="ex2-node-2-'.$q.'-4" class="child-of-ex2-node-2-'.$q.'"><td>Annotation</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-4-1" class="child-of-ex2-node-2-'.$q.'-4"><td>Transkription</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-4-1-1" class="child-of-ex2-node-2-'.$q.'-4-1"><td>';
									$new = array();
									
									if (isset($valItem_array)) {
										for($d=0;$d<sizeof($valItem_array[$q]->valItem);$d++) {
											foreach ($valItem_array[$q]->valItem[$d]->attributes() as $valItem_array_value) {
												echo $valItem_array_value.'<br>';
											}
										}
									}
									else {
										echo "Not set";
									}
								echo '</td></tr>';
								
								
							echo '<tr id="ex2-node-2-'.$q.'-4-2" class="child-of-ex2-node-2-'.$q.'-4"><td>Annotation</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-4-2-1" class="child-of-ex2-node-2-'.$q.'-4-2"><td>';
									if (isset($annotItem_array)) {	
										for($s=0;$s<sizeof($annotItem_array[$q]->valItem);$s++) {
											foreach ($annotItem_array[$q]->valItem[$s]->attributes() as $attribute => $annotItem_array_value) {
												if ($attribute == 'corresp') {
													echo $annotItem_array_value.'<br>';
												}
											}
										}
									}
									else {
										echo "Not set";
									}
								echo '</td></tr>';
								
						echo '<tr id="ex2-node-2-'.$q.'-5" class="child-of-ex2-node-2-'.$q.'"><td>Revision</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-5-1" class="child-of-ex2-node-2-'.$q.'-5"><td>Date</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-5-1-1" class="child-of-ex2-node-2-'.$q.'-5-1"><td>'.$date_revision_array[$q].'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-5-2" class="child-of-ex2-node-2-'.$q.'-5"><td>Type</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-5-2-1" class="child-of-ex2-node-2-'.$q.'-5-2"><td>'.$type_revision_array[$q].'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-5-3" class="child-of-ex2-node-2-'.$q.'-5"><td>Who</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-5-3-1" class="child-of-ex2-node-2-'.$q.'-5-3"><td>'.(($who_revision_array[$q] != 'NA' || NULL) ? $who_revision_array[$q] : 'Not set').'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-5-4" class="child-of-ex2-node-2-'.$q.'-5"><td>Description</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-5-4-1" class="child-of-ex2-node-2-'.$q.'-5-4"><td>'.(($item_revision_array[$q] != 'NA' || NULL) ? $item_revision_array[$q] : 'Not set').'</td></tr>';
				}
			
		echo '<tr id="ex2-node-3"><td><b>Annotation</b>';
		echo '<span class="helptooltip"><p>This section contains information about the applied annotations of the corpus. Every single annotation with its annotation keys and values are listed here.</p>';
		echo $this->Html->image("help.png",array(
			    "border" => "0",
			    'width'=>'20px',
			    "alt" => "help",
			));
		echo '</span></td></tr>';

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

		echo '<tr id="ex2-node-4"><td><b>PreparationStep</b>';
		echo '<span class="helptooltip"><p>This section contains information about every preparation step of every applied annotation. All steps of annotation/transcription including references for annotators and applied tools for annotation and quality assurance are described here.</p>';
		echo $this->Html->image("help.png",array(
			    "border" => "0",
			    'width'=>'20px',
			    "alt" => "help",
			));
		 echo '</span></td></tr>';
		
		foreach ($layer_title_array as $attrib => $layer_title_array_value) {
			echo '<tr id="ex2-node-4-'.$attrib.'" class="child-of-ex2-node-4">';
				echo '<td>'.$layer_title_array_value.'</td>';
			echo '</tr>';
			
			echo '<tr id="ex2-node-4-'.$attrib.'-0" class="child-of-ex2-node-4-'.$attrib.'"><td>Authorship</td></tr>';
				echo '<tr id="ex2-node-4-'.$attrib.'-0-1" class="child-of-ex2-node-4-'.$attrib.'-0"><td>Corpus Editor</td></tr>';
					for ($x=0;$x<sizeof($editor_surname_array);$x++) {
						echo '<tr id="ex2-node-4-'.$attrib.'-0-1-'.$x.'" class="child-of-ex2-node-4-'.$attrib.'-0-1">';
							echo '<td>'.$prep_editor_forename_array[$x].' '.$prep_editor_surname_array[$x].'</td>';
							echo ' ';
							
							echo '</tr>';

							if (isset($prep_editor_institution_name_array[$x])) {
								echo '<tr id="ex2-node-4-'.$attrib.'-0-1-1-'.$x.'" class="child-of-ex2-node-4-'.$attrib.'-0-1-'.$x.'"><td>Affiliation: '.$prep_editor_department_name_array[$x].', '.$prep_editor_institution_name_array[$x].'</td></tr>';
							}
					}
			
				echo '<tr id="ex2-node-4-'.$attrib.'-0-2" class="child-of-ex2-node-4-'.$attrib.'-0"><td>Annotator</td></tr>';
					for ($y=0;$y<sizeof($author_surname_array);$y++) {
						if ($prep_author_forename_array[$y] != 'NA' || NULL) {
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
				echo '<tr id="ex2-node-4-'.$attrib.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Release</td></tr>';
					echo '<tr id="ex2-node-4-'.$attrib.'-1-1-'.$attrib.'" class="child-of-ex2-node-4-'.$attrib.'-1-1"><td>'.((@$prep_publicationStmt_array[$attrib]['when'] != "" && $prep_publicationStmt_array[$attrib]['when'] != "N/A") ? $prep_publicationStmt_array[$attrib]['when'].' '.$prep_publicationStmt_array[$attrib] : "Not set").'</td></tr>';
				echo '<tr id="ex2-node-4-'.$attrib.'-1-2" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Source</td></tr>';
					echo '<tr id="ex2-node-4-'.$attrib.'-1-2-1" class="child-of-ex2-node-4-'.$attrib.'-1-2"><td>'.((@$prep_ref_target_array[$attrib] != "" && $prep_ref_target_array[$attrib] != "N/A" && $prep_ref_target_array[$attrib] != "NA") ? 'PDF - <a href='.$prep_ref_target_array[$attrib].'>Hyperlink</a>' : "Not set").'</td></tr>';
				echo '<tr id="ex2-node-4-'.$attrib.'-1-3" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Extent</td></tr>';
					echo '<tr id="ex2-node-4-'.$attrib.'-1-3-1" class="child-of-ex2-node-4-'.$attrib.'-1-3"><td>'.((@$prep_extent_array[$attrib] != "" && $prep_extent_array[$attrib] != "N/A" && $prep_extent_array[$attrib] != "NA") ? $prep_extent_array[$attrib].' '.$prep_extent_array[$attrib]['type'] : "Not set").'</td></tr>';
			
			#if (isset($preparation_header_array)) {	
				echo '<tr id="ex2-node-4-'.$attrib.'-2" class="child-of-ex2-node-4-'.$attrib.'"><td>Encoding</td></tr>';
					//for gibt performance-probleme
					for($o=0;$o<sizeof($preparation_header_array[$attrib]->encodingDesc);$o++) {
						foreach ($preparation_header_array[$attrib]->encodingDesc[$o]->attributes() as $attribute => $preparation_header_array_value) {
							
							if ($attribute == 'style') {
								echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'" class="child-of-ex2-node-4-'.$attrib.'-2"><td>'.$preparation_header_array_value.'</td></tr>';
							}
						}
						
						foreach ($preparation_header_array[$attrib]->encodingDesc[$o]->appInfo as $appInfo_value) {
							echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'"><td>Tool: '.$appInfo_value->application->label.'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'"><td>Version</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-1-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-1"><td>'.$appInfo_value->application['version'].'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-2" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'"><td>Label</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-2-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-2"><td>'.$appInfo_value->application->label.'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-3" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'"><td>Format</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-3-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-3"><td>'.$appInfo_value['style'].'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-4" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'"><td>Annotation Class</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-4-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-4"><td>'.$appInfo_value->application['type'].'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-5" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'"><td>Subtype</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-5-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-5"><td>'.(($appInfo_value->application['subtype'] != 'NA' || NULL) ? $appInfo_value->application['subtype'] : 'Not set').'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-6" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'"><td>Description</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-6-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'-'.$appInfo_value['n'].'-6"><td>'.(($appInfo_value->application->p != 'NA' || NULL) ? $appInfo_value->application->p : 'Not set').'</td></tr>';
						}
						
						foreach ($preparation_header_array[$attrib]->encodingDesc[$o]->editorialDecl as $editorialDecl_value) {
							echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$o.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'"><td>Normalization</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$o.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$o.'"><td>Method</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$o.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$o.'-1"><td>'.$editorialDecl_value->normalization['method'].'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$o.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$o.'"><td>Description</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$o.'-1-2" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$o.'-2"><td>'.(($editorialDecl_value->normalization->p != 'NA' || NULL) ? $editorialDecl_value->normalization->p : 'Not set').'</td></tr>';
							echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$o.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'"><td>Segmentation</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$o.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$o.'"><td>Corresp</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$o.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$o.'-1"><td>'.(($editorialDecl_value->segmentation['corresp'] != '' && $editorialDecl_value->segmentation['corresp'] != 'NA' && $editorialDecl_value->segmentation['corresp'] != NULL) ? $editorialDecl_value->segmentation['corresp'] : 'Not set').'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$o.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$o.'"><td>Style</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$o.'-2-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$o.'-2"><td>'.$editorialDecl_value->segmentation['style'].'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$o.'-3" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$o.'"><td>Description</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$o.'-3-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$o.'-3"><td>'.(($editorialDecl_value->segmentation->p != 'NA' || NULL) ? $editorialDecl_value->segmentation->p : 'Not set').'</td></tr>';
							echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$o.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$o.'"><td>Correction</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$o.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$o.'"><td>Method</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$o.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$o.'-1"><td>'.$editorialDecl_value->correction['method'].'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$o.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$o.'"><td>Status</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$o.'-2-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$o.'-2"><td>'.$editorialDecl_value->correction['status'].'</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$o.'-3" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$o.'"><td>Description</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$o.'-3-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$o.'-3"><td>'.$editorialDecl_value->correction->p.'</td></tr>';
						}
					}
					
					
					for($c=0;$c<sizeof($preparation_header_array[$attrib]->revisionDesc);$c++) {
						echo '<tr id="ex2-node-4-'.$attrib.'-3" class="child-of-ex2-node-4-'.$attrib.'"><td>Revision</td></tr>';
					
						foreach ($preparation_header_array[$attrib]->revisionDesc[$c]->change->attributes() as $attribute => $revisionDesc_value) {
							if ($attribute == 'when') {
								echo '<tr id="ex2-node-4-'.$attrib.'-3-1" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Date</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-3-1-1" class="child-of-ex2-node-4-'.$attrib.'-3-1"><td>'.$revisionDesc_value.'</td></tr>';
							}
							elseif ($attribute == 'who') {
								echo '<tr id="ex2-node-4-'.$attrib.'-3-2" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Who</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-3-2-1" class="child-of-ex2-node-4-'.$attrib.'-3-2"><td>'.(($revisionDesc_value != 'NA' && $revisionDesc_value != NULL) ? $revisionDesc_value : 'Not set').'</td></tr>';
							}
							elseif ($attribute == 'n') {
								echo '<tr id="ex2-node-4-'.$attrib.'-3-3" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Version</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-3-3-1" class="child-of-ex2-node-4-'.$attrib.'-3-3"><td>'.$revisionDesc_value.'</td></tr>';
							}
						}
						foreach ($preparation_header_array[$attrib]->revisionDesc[$c] as $desc_revisionDesc_value) {
							echo '<tr id="ex2-node-4-'.$attrib.'-3-4" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Description</td></tr>';
								echo '<tr id="ex2-node-4-'.$attrib.'-3-4-1" class="child-of-ex2-node-4-'.$attrib.'-3-4"><td>'.(($desc_revisionDesc_value != 'NA' && $desc_revisionDesc_value != NULL) ? $desc_revisionDesc_value : 'Not set').'</td></tr>';
						}
					}
					
			#}
			

		}
		echo '</table>';
	}
   if (isset($reveal)) {
        echo '<script type="text/javascript">
                $(document).ready(function(){
                try {
                    $(\'td[rel="\'+decodeURIComponent("'.$reveal.'")+\'"]\').parent().reveal();
                    $(\'td[rel="\'+decodeURIComponent("'.$reveal.'")+\'"]\').parent().expand();
                }catch(error) {  
                }
            });</script>';
    }
?>