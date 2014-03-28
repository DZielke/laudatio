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
		#for($x=0;$x<sizeof($xml);$x++) {
		
		
	    $x=0;
		foreach ($xml->objectChangeDate as $objectChangeDate) {
		    if($x>0){
			    if($versionDate != null){
			        if($objectChangeDate == $versionDate){
			             echo "<option selected>".$objectChangeDate."</option>";
                    }else{
                         echo "<option>".$objectChangeDate."</option>";
                    }
			    }else{
			        if($x==count($xml->objectChangeDate)-1){
			            var_dump($objectChangeDate);
			             echo "<option selected>".$objectChangeDate."</option>";  
			        }else{
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
		
		#($extent != "N/A" ? $extent : "")
		
			
		foreach ($download_format as $value) {
			if ($value == 'TEIXML') {
				$download='http://depot1-7.cms.hu-berlin.de:8080/fedora/objects/'.$pid.'/datastreams/TEI_header/content';
			}
			elseif ($value == 'EXMARaLDA') {
				$download = "http://141.20.4.72:8080/fedora/get/".$pid."/".ucfirst($value)."";
			}
			
			#elseif ($value == 'relANNIS') {
			#	$download = "http://141.20.4.72:8080/fedora/get/".$pid."/".$value."";
			#}
			
			else {
				$download = "http://141.20.4.72:8080/fedora/get/".$pid."/".$value."";
			}
			

		}
		
		for ($x=0;$x<sizeof($label_format);) {
			$germanc_link = "relANNIS";
			$TEI_germanc_link = "TEI";
			
			
			if ($title == 'GerManC') {
				array_push($download_format, $germanc_link);
				array_push($label_format, $germanc_link);
				
				array_push($download_format, $TEI_germanc_link);
				array_push($label_format, $TEI_germanc_link);
			}
			
		
			echo ", ".$this->Html->link($label_format[$x], $download);
			$x++;
			
			
			$germanc_link = array_pop($download_format);
			$germanc_link = array_pop($label_format);
			
			$TEI_germanc_link = array_pop($download_format);
			$TEI_germanc_link = array_pop($label_format);
			
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
						echo '<tr id="ex2-node-1-1-2-'.$y.'" class="child-of-ex2-node-1-1-2">';
							echo '<td>'.$author_forename_array[$y].' '.$author_surname_array[$y].'</td>';
							echo ' ';
							
							echo '</tr>';

							if (isset($author_institution_name_array[$y])) {
								echo '<tr id="ex2-node-1-1-1-2-'.$y.'" class="child-of-ex2-node-1-1-2-'.$y.'"><td>Affiliation: '.$author_department_name_array[$y].' '.$author_institution_name_array[$y].'</td></tr>';
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
				echo '<tr id="ex2-node-1-4-1" class="child-of-ex2-node-1-4"><td>'.$extent.'</td></tr>';
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
				
						#foreach ($download_format as $download_value) {
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
								elseif ($download_format[$p] == 'Xml') { 
									if (isset($namespace_xml_array)) {
										foreach ($namespace_xml_array as $namespace_value) {
											echo $namespace_value.', ';
										}
									}
									else {
										echo "Not set";
									}
								}
								elseif ($download_format[$p] == 'relANNIS') { 
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
		
								#.$namespace_array[$p].'</td></tr>';
								echo '</td></tr>';
							}
						#}
					}
			
		echo '<tr id="ex2-node-2"><td><b>Documents</b>';
		echo '<span class="helptooltip"><p>This section contains classical metadata for the object &lsquo;document&rsquo;. The author, the editor, publication place, bibliographic scope and other bibliographic metadata are presented here.</p>';
		echo $this->Html->image("help.png",array(
			    "border" => "0",
			    'width'=>'20px',
			    "alt" => "help",
			));
		 echo '</span></td></tr>';
			#echo '<tr id="ex2-node-2-1" class="child-of-ex2-node-2"><td>Title</td></tr>';
				#$q=1;
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
								echo '<tr id="ex2-node-2-'.$q.'-2-1-1" class="child-of-ex2-node-2-'.$q.'-2-1"><td>'.$item_surname_array[$q].', '.$item_forename_array[$q].'</td></tr>';
							echo '<tr id="ex2-node-2-'.$q.'-2-2" class="child-of-ex2-node-2-'.$q.'-2"><td>Editor</td></tr>';
								echo '<tr id="ex2-node-2-'.$q.'-2-2-1" class="child-of-ex2-node-2-'.$q.'-2-2"><td>'.$editor_surname_doc_array[$q].', '.$editor_forename_doc_array[$q].'</td></tr>';
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
								echo '<tr id="ex2-node-2-'.$q.'-3-6-1" class="child-of-ex2-node-2-'.$q.'-3-6"><td>'.$item_extent_array[$q].'</td></tr>';
						
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
		
			echo '<tr id="ex2-node-3-1" class="child-of-ex2-node-3"><td>Transcription</td></tr>';
				echo '<tr id="ex2-node-3-1-1" class="child-of-ex2-node-3-1"><td>tok</td></tr>';
					if (isset($tok_text)) {
						echo '<tr id="ex2-node-3-1-1-1" class="child-of-ex2-node-3-1-1"><td>'.(($tok_text != '' || NULL) ? $tok_text : 'Not Set').'</td></tr>';
					}
					#else {
						
					#}
				echo '<tr id="ex2-node-3-1-2" class="child-of-ex2-node-3-1"><td>clean</td></tr>';
					if (isset($clean_text)) {
						echo '<tr id="ex2-node-3-1-1-2" class="child-of-ex2-node-3-1-2"><td>'.(($clean_text != '' ||NULL) ? $clean_text : 'Not Set').'</td></tr>';
					}
				echo '<tr id="ex2-node-3-1-3" class="child-of-ex2-node-3-1"><td>norm</td></tr>';
					if (isset($norm_text)) {
						echo '<tr id="ex2-node-3-1-1-3" class="child-of-ex2-node-3-1-3"><td>'.(($norm_text != '' || NULL) ? $norm_text : 'Not Set').'</td></tr>';
					}

			echo '<tr id="ex2-node-3-2" class="child-of-ex2-node-3"><td>Lexical</td></tr>';
				echo '<tr id="ex2-node-3-2-1" class="child-of-ex2-node-3-2"><td>pos</td></tr>';
					echo '<tr id="ex2-node-3-2-1-1" class="child-of-ex2-node-3-2-1"><td>';
					
					if (isset($item_tagUsage_array)) {					
						foreach ($item_tagUsage_array as $attrib => $item_tagUsage_array_value) {
							echo '<b>'.$item_tagUsage_array_value["gi"].'</b> '.$item_tagUsage_array_value.'<br>';
						}
					}
					else {
						echo "Not set";
					}
					
					echo '</td></tr>';
				echo '<tr id="ex2-node-3-2-2" class="child-of-ex2-node-3-2"><td>foreign</td></tr>';
					if (isset($foreign_text)) {
						echo '<tr id="ex2-node-3-2-2-1" class="child-of-ex2-node-3-2-2"><td>'.(($foreign_text != '' || NULL) ? $foreign_text : 'Not Set').'</td></tr>';
					}
				echo '<tr id="ex2-node-3-2-3" class="child-of-ex2-node-3-2"><td>lang</td></tr>';
					echo '<tr id="ex2-node-3-2-3-1" class="child-of-ex2-node-3-2-3"><td>';

					if (isset($item_lang_tagUsage_array)) {
						foreach ($item_lang_tagUsage_array as $attrib => $item_lang_tagUsage_array_value) {
							echo '<b>'.$item_lang_tagUsage_array_value["gi"].'</b> '.$item_lang_tagUsage_array_value.'<br>';
						}
					}
					else {
						echo "Not set";
					}
					
					echo '</td></tr>';
					
			echo '<tr id="ex2-node-3-3" class="child-of-ex2-node-3"><td>Syntactic</td></tr>';
			echo '<tr id="ex2-node-3-4" class="child-of-ex2-node-3"><td>Graphic</td></tr>';
				if (isset($hi_text)) {
					echo '<tr id="ex2-node-3-4-1" class="child-of-ex2-node-3-4"><td>'.(($hi_text != '' || NULL) ? $hi_text : 'Not Set').'</td></tr>';
				}
		
		echo '<tr id="ex2-node-4"><td><b>PreparationStep</b>';
		echo '<span class="helptooltip"><p>This section contains information about every preparation step of every applied annotation. All steps of annotation/transcription including references for annotators and applied tools for annotation and quality assurance are described here.</p>';
		echo $this->Html->image("help.png",array(
			    "border" => "0",
			    'width'=>'20px',
			    "alt" => "help",
			));
		 echo '</span></td></tr>';
		

			
		#layer_title_array
		foreach ($layer_title_array as $attrib => $layer_title_array_value) {
			echo '<tr id="ex2-node-4-'.$attrib.'" class="child-of-ex2-node-4">';
				echo '<td>'.$layer_title_array_value.'</td>';
			echo '</tr>';
			
			
			//--------------
			echo '<tr id="ex2-node-4-'.$attrib.'-1" class="child-of-ex2-node-4-'.$attrib.'"><td>Authorship</td></tr>';
				echo '<tr id="ex2-node-4-'.$attrib.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Corpus Editor</td></tr>';
					for ($x=0;$x<sizeof($editor_surname_array);$x++) {
						#var_dump($attrib);
						echo '<tr id="ex2-node-4-'.$attrib.'-1-1-'.$x.'" class="child-of-ex2-node-4-'.$attrib.'-1-1">';
							echo '<td>'.$prep_editor_forename_array[$x].' '.$prep_editor_surname_array[$x].'</td>';
							echo ' ';
							
							echo '</tr>';

							if (isset($prep_editor_institution_name_array[$x])) {
								echo '<tr id="ex2-node-4-'.$attrib.'-1-1-1-'.$x.'" class="child-of-ex2-node-4-'.$attrib.'-1-1-'.$x.'"><td>Affiliation: '.$prep_editor_department_name_array[$x].', '.$prep_editor_institution_name_array[$x].'</td></tr>';
							}
					}
				echo '<tr id="ex2-node-4-'.$attrib.'-1-0" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Annotator</td></tr>';
					for ($y=0;$y<sizeof($prep_author_surname_array);$y++) {
						#var_dump($attrib);
						echo '<tr id="ex2-node-4-'.$attrib.'-1-0-'.$y.'" class="child-of-ex2-node-4-'.$attrib.'-1-0">';
							echo '<td>'.$prep_author_forename_array[$y].' '.$prep_author_surname_array[$y].'</td>';
							echo ' ';
							
							echo '</tr>';

							if (isset($prep_author_institution_name_array[$y])) {
								echo '<tr id="ex2-node-4-'.$attrib.'-1-0-1-'.$y.'" class="child-of-ex2-node-4-'.$attrib.'-1-0-'.$y.'"><td>Affiliation: '.$prep_author_department_name_array[$y].' '.$prep_author_institution_name_array[$y].'</td></tr>';
							}
					}
			
				
			echo '<tr id="ex2-node-4-'.($attrib+2).'-0" class="child-of-ex2-node-4-'.$attrib.'"><td>Availability</td></tr>';
			
				echo '<tr id="ex2-node-4-'.($attrib+2).'-0-1" class="child-of-ex2-node-4-'.($attrib+2).'-0"><td>Release</td></tr>';
					echo '<tr id="ex2-node-4-'.($attrib+2).'-0-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-0-1"><td>'.$prep_release.'</td></tr>';
					echo '<tr id="ex2-node-4-'.($attrib+2).'-0-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-0-1"><td>Description</td></tr>';
						echo '<tr id="ex2-node-4-'.($attrib+2).'-0-2-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-0-2-1"><td>'.$prep_desc.'</td></tr>';
				echo '<tr id="ex2-node-4-'.($attrib+2).'-0-2" class="child-of-ex2-node-4-'.($attrib+2).'-0"><td>Source</td></tr>';
					#($prep_tok_change_when_array[$x] != '' ? $prep_tok_change_when_array[$x] : 'Not Set')
					echo '<tr id="ex2-node-4-'.($attrib+2).'-0-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-0-2"><td>'.($prep_source_ref != "N/A" ? 'PDF - <a href='.$prep_source_ref.'>Hyperlink</a>' : "Not set").'</td></tr>';
					echo '<tr id="ex2-node-4-'.($attrib+2).'-0-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-0-2"><td>Description</td></tr>';
						echo '<tr id="ex2-node-4-'.($attrib+2).'-0-3-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-0-4-1"><td>'.(($prep_source_desc != "" && $prep_source_desc != "N/A") ? $prep_source_desc : "Not set").'</td></tr>';
			
			
			
			echo '<tr id="ex2-node-4-'.($attrib+2).'-1" class="child-of-ex2-node-4-'.$attrib.'"><td>Encoding</td></tr>';
				
				for ($x=0;$x<sizeof($prep_encodingDesc_array);$x++) {
					if (($layer_title_array_value == 'pos') && (($prep_encodingDesc_array[$x] == "Annotation") || ($prep_encodingDesc_array[$x] == "TokenAnnotation"))) {
						echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'" class="child-of-ex2-node-4-'.($attrib+2).'-1"><td>'.$prep_encodingDesc_array[$x];
							echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Tool</td></tr>';

								if (($prep_encodingDesc_array[$x] == "Annotation") || ($prep_encodingDesc_array[$x] == "TokenAnnotation")) {
									foreach ($appInfo_ann_array as $attribs => $appInfo_ann_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_ann_type_array[$attribs]."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1"><td>'.($prep_ann_application_subtype_array[$attribs] != '' ? $prep_ann_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2"><td>'.$prep_ann_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3"><td>'.$appInfo_ann_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4"><td>'.$prep_ann_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}
																
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Segmentation</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-2"><td>'.$prep_segmentation_array[$x].'</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-2-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-2"><td>Description</td></tr>';
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-2-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-2-2"><td>'.$prep_segmentation_desc_array[$x].'</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Normalization</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-3"><td>'.$prep_normalization_array[$x].'</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-3-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-3"><td>Description</td></tr>';
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-3-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-3-2"><td>'.$prep_normalization_desc_array[$x].'</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Correction</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4"><td>Method</td></tr>';
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-1"><td>'.$correction_method_array[$x].'</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4"><td>Status</td></tr>';
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-2"><td>'.$correction_status_array[$x].'</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4"><td>Description</td></tr>';
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-3"><td>'.$prep_correction_desc_array[$x].'</td></tr>';
									
								echo '<tr id="ex2-node-4-'.($attrib+2).'-2" class="child-of-ex2-node-4-'.$attrib.'"><td>Revision</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-2"><td>Date</td></tr>';
										#(($prep_source_desc != "" && $prep_source_desc != "N/A") ? $prep_source_desc : "Not set")
										###echo '<tr id="ex2-node-4-'.($attrib+2).'-2-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-2-1"><td>'.(($prep_ann_change_when_array[$x] != "" && $prep_ann_change_when_array[$x] != "N/A") ? $prep_ann_change_when_array[$x] : "Not set").'</td></tr>';
									###echo '<tr id="ex2-node-4-'.($attrib+2).'-2-2" class="child-of-ex2-node-4-'.($attrib+2).'-2"><td>Who</td></tr>';
										###echo '<tr id="ex2-node-4-'.($attrib+2).'-2-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-2-2"><td>'.$prep_ann_change_who_array[$x].'</td></tr>';
									###echo '<tr id="ex2-node-4-'.($attrib+2).'-2-3" class="child-of-ex2-node-4-'.($attrib+2).'-2"><td>Description</td></tr>';
										###echo '<tr id="ex2-node-4-'.($attrib+2).'-2-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-2-3"><td>'.$prep_ann_change_desc_array[$x].'</td></tr>';
								
					}
					
					elseif (($layer_title_array_value == 'tok') && (($prep_encodingDesc_array[$x] != "Annotation") && ($prep_encodingDesc_array[$x] != "TokenAnnotation") && ($prep_encodingDesc_array[$x] != "Normalization"))) {
						echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'" class="child-of-ex2-node-4-'.($attrib+2).'-1"><td>'.$prep_encodingDesc_array[$x];
							echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Tool</td></tr>';
								if ($prep_encodingDesc_array[$x] == "Transcription") {
									foreach ($appInfo_trans_array as $attribs => $appInfo_trans_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_type_array[$attribs]."<br>";
											#echo $appInfo_trans_array_value."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1"><td>'.($prep_application_subtype_array[$attribs] != '' ? $prep_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2"><td>'.$prep_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3"><td>'.$appInfo_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4"><td>'.$prep_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}


								if ($prep_encodingDesc_array[$x] == "Markup in TEI") {
									foreach ($appInfo_markup_array as $attribs => $appInfo_markup_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_markup_type_array[$attribs]."<br>";
											#echo $appInfo_markup_array_value."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-1"><td>'.($prep_markup_application_subtype_array[$attribs] != '' ? $prep_markup_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-2"><td>'.$prep_markup_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-3"><td>'.$appInfo_markup_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-4"><td>'.$prep_markup_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}
								
								elseif ($prep_encodingDesc_array[$x] == "Tokenization") {
									foreach ($appInfo_tok_array as $attribs => $appInfo_tok_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_tok_type_array[$attribs]."<br>";
											#echo $appInfo_tok_array_value."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-1"><td>'.($prep_tok_application_subtype_array[$attribs] != '' ? $prep_tok_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-2"><td>'.$prep_tok_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-3"><td>'.$appInfo_tok_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-3-4"><td>'.$prep_tok_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}

								elseif ($prep_encodingDesc_array[$x] == "Mapping TEI xml mit Treetaggeroutput") {
									foreach ($appInfo_mapping_array as $attribs => $appInfo_mapping_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_mapping_type_array[$attribs]."<br>";
											#echo $appInfo_mapping_array_value."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-1"><td>'.($prep_mapping_application_subtype_array[$attribs] != '' ? $prep_mapping_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-2"><td>'.$prep_mapping_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-3"><td>'.$appInfo_mapping_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-4-4"><td>'.$prep_mapping_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}

							echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Segmentation</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-2"><td>'.$prep_segmentation_array[$x].'</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-2-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-2"><td>Description</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-2-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-2-2"><td>'.$prep_segmentation_desc_array[$x].'</td></tr>';
							echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Normalization</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-3"><td>'.$prep_normalization_array[$x].'</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-3-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-3"><td>Description</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-3-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-3-2"><td>'.$prep_normalization_desc_array[$x].'</td></tr>';
							echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Correction</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4"><td>Method</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-1"><td>'.$correction_method_array[$x].'</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4"><td>Status</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-2"><td>'.$correction_status_array[$x].'</td></tr>';
								echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4"><td>Description</td></tr>';
									echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-4-3"><td>'.$prep_correction_desc_array[$x].'</td></tr>';
				

					}
					elseif (($layer_title_array_value == 'N/A') && ($prep_encodingDesc_array[$x] == "Transcription")) {
						#var_dump($layer_title_array_value);
						$temp = array_unique($prep_encodingDesc_array);
						$prep_encodingDesc_array = $temp;
						echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'" class="child-of-ex2-node-4-'.($attrib+2).'-1"><td>'.$prep_encodingDesc_array[$x];
							echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Tool</td></tr>';
								if ($prep_encodingDesc_array[$x] == "Transcription") {
									#var_dump($appInfo_trans_array);
									#$appInfo_trans_array
									$temp = array_unique($appInfo_trans_array);
									$appInfo_trans_array = $temp;
									
									foreach ($appInfo_trans_array as $attribs => $appInfo_trans_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_type_array[$attribs]."<br>";
											#echo $appInfo_trans_array_value."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1"><td>'.($prep_application_subtype_array[$attribs] != '' ? $prep_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2"><td>'.$prep_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3"><td>'.$appInfo_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4"><td>'.$prep_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}
					}
					elseif (($layer_title_array_value == 'norm') && ($prep_encodingDesc_array[$x] == "Normalization")) {
						echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'" class="child-of-ex2-node-4-'.($attrib+2).'-1"><td>'.$prep_encodingDesc_array[$x];
							echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Tool</td></tr>';
								if ($prep_encodingDesc_array[$x] == "Normalization") {
									foreach ($appInfo_trans_array as $attribs => $appInfo_trans_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_type_array[$attribs]."<br>";
											#echo $appInfo_trans_array_value."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1"><td>'.($prep_application_subtype_array[$attribs] != '' ? $prep_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2"><td>'.$prep_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3"><td>'.$appInfo_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4"><td>'.$prep_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}
					}
					elseif (($layer_title_array_value == 'text') && (($prep_encodingDesc_array[$x] == "Transcription") || ($prep_encodingDesc_array[$x] == "Markup in TEI"))) {
						echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'" class="child-of-ex2-node-4-'.($attrib+2).'-1"><td>'.$prep_encodingDesc_array[$x];
							echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$x.'-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'"><td>Tool</td></tr>';
								if ($prep_encodingDesc_array[$x] == "Transcription") {
									foreach ($appInfo_trans_array as $attribs => $appInfo_trans_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_type_array[$attribs]."<br>";
											#echo $appInfo_trans_array_value."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-1"><td>'.($prep_application_subtype_array[$attribs] != '' ? $prep_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-2"><td>'.$prep_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-3"><td>'.$appInfo_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-1-4"><td>'.$prep_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}
								
								
								if ($prep_encodingDesc_array[$x] == "Markup in TEI") {
									foreach ($appInfo_markup_array as $attribs => $appInfo_markup_array_value) {
										echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$x.'-1"><td>';
											echo $prep_application_markup_type_array[$attribs]."<br>";
											#echo $appInfo_markup_array_value."<br>";
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2"><td>Version</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-1"><td>'.($prep_markup_application_subtype_array[$attribs] != '' ? $prep_markup_application_subtype_array[$attribs] : 'Not Set').'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-2" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2"><td>Label</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-2"><td>'.$prep_markup_app_label_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-3" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2"><td>Style</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-3"><td>'.$appInfo_markup_style_array[$attribs].'</td></tr>';
												echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-4" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2"><td>Format</td></tr>';
													echo '<tr id="ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-4-1" class="child-of-ex2-node-4-'.($attrib+2).'-1-'.$attribs.'-1-2-4"><td>'.$prep_markup_application_array[$attribs].'</td></tr>';
										echo '</td></tr>';
									}
								}
					
					}

				}
				
				if ($layer_title_array_value == 'tok') {
					echo '<tr id="ex2-node-4-'.($attrib+2).'-2" class="child-of-ex2-node-4-'.$attrib.'"><td>Revision</td></tr>';
						echo '<tr id="ex2-node-4-'.($attrib+2).'-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-2"><td>Date</td></tr>';
							echo '<tr id="ex2-node-4-'.($attrib+2).'-2-1-1" class="child-of-ex2-node-4-'.($attrib+2).'-2-1"><td>'.($prep_tok_change_when_array[$x] != '' ? $prep_tok_change_when_array[$x] : 'Not Set').'</td></tr>';
						echo '<tr id="ex2-node-4-'.($attrib+2).'-2-2" class="child-of-ex2-node-4-'.($attrib+2).'-2"><td>Who</td></tr>';
							echo '<tr id="ex2-node-4-'.($attrib+2).'-2-2-1" class="child-of-ex2-node-4-'.($attrib+2).'-2-2"><td>'.($prep_tok_change_who_array[$x] != '' ? $prep_tok_change_who_array[$x] : 'Not Set').'</td></tr>';
						echo '<tr id="ex2-node-4-'.($attrib+2).'-2-3" class="child-of-ex2-node-4-'.($attrib+2).'-2"><td>Description</td></tr>';
							echo '<tr id="ex2-node-4-'.($attrib+2).'-2-3-1" class="child-of-ex2-node-4-'.($attrib+2).'-2-3"><td>'.($prep_tok_change_desc_array[$x] != '' ? $prep_tok_change_desc_array[$x] : 'Not Set').'</td></tr>';
				}	
				echo '</td></tr>';
			
			
			
			/*
			echo '<tr id="ex2-node-4-3-2" class="child-of-ex2-node-4-1"><td>Revision</td></tr>';
				echo '<tr id="ex2-node-4-3-2-1" class="child-of-ex2-node-4-3-2"><td>Date</td></tr>';
				echo '<tr id="ex2-node-4-3-2-2" class="child-of-ex2-node-4-3-2"><td>Who</td></tr>';
				echo '<tr id="ex2-node-4-3-2-3" class="child-of-ex2-node-4-3-2"><td>Description</td></tr>';
				//------------
			*/
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

/* show document 
 * alert("error: '.$reveal.' parentId:"+ $(\'td[rel="\'+decodeURIComponent("'.$reveal.'")+\'"]\').parent().attr("id"));
 * $(\'td[rel="\'+decodeURIComponent("'.$reveal.'")+\'"]\').parent().attr("id")
 * 
$("form#reveal").submit(function() {
var nodeId = $("#revealNodeId").val()
try {
$("#example-advanced").treetable("reveal", nodeId);
}
catch(error) {
alert(error.message);
}
return false;
}); 
 * 
 * 
 * <form id="reveal">
<input type="text" name="nodeId" placeholder="nodeId" id="revealNodeId"/>
<input type="submit" value="Reveal"/><br/>
<small>Examples: <tt>2-1-1</tt>, <tt>3-1-1-2-2</tt> and <tt>3-2-1-2-3-1-2-2-1-1-1-1-2-5</tt></small>
</form> 
 */
?>