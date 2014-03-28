<?php
        foreach ($layer_title_array as $attrib => $layer_title_array_value) {
            echo '<tr id="ex2-node-4-'.$attrib.'" class="child-of-ex2-node-4">';
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
                echo '<tr id="ex2-node-4-'.$attrib.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Release</td></tr>';
                    echo '<tr id="ex2-node-4-'.$attrib.'-1-1-'.$attrib.'" class="child-of-ex2-node-4-'.$attrib.'-1-1"><td>'.((@$prep_publicationStmt_array[$attrib]['when'] != "" && $prep_publicationStmt_array[$attrib]['when'] != "N/A") ? $prep_publicationStmt_array[$attrib]['when'].' '.$prep_publicationStmt_array[$attrib] : "Not set").'</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-1-2" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Source</td></tr>';
                    echo '<tr id="ex2-node-4-'.$attrib.'-1-2-1" class="child-of-ex2-node-4-'.$attrib.'-1-2"><td>'.((@$prep_ref_target_array[$attrib] != "" && $prep_ref_target_array[$attrib] != "N/A" && $prep_ref_target_array[$attrib] != "NA") ? 'PDF - <a href='.$prep_ref_target_array[$attrib].'>Hyperlink</a>' : "Not set").'</td></tr>';
                echo '<tr id="ex2-node-4-'.$attrib.'-1-3" class="child-of-ex2-node-4-'.$attrib.'-1"><td>Extent</td></tr>';
                    echo '<tr id="ex2-node-4-'.$attrib.'-1-3-1" class="child-of-ex2-node-4-'.$attrib.'-1-3"><td>'.((@$prep_extent_array[$attrib] != "" && $prep_extent_array[$attrib] != "N/A" && $prep_extent_array[$attrib] != "NA") ? $prep_extent_array[$attrib].' '.$prep_extent_array[$attrib]['type'] : "Not set").'</td></tr>';
            
                echo '<tr id="ex2-node-4-'.$attrib.'-2" class="child-of-ex2-node-4-'.$attrib.'"><td>Encoding</td></tr>';
                    //for gibt performance-probleme
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
										echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Version</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-1-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'-1"><td>'.$appInfo_value->application['version'].'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-2" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Label</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-2-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'-2"><td>'.$appInfo_value->application->label.'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-3" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Format</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-3-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'-3"><td>'.$appInfo_value['style'].'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-4" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Annotation Class</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-4-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'-4"><td>'.$appInfo_value->application['type'].'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-5" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Subtype</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-5-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'-5"><td>'.(($appInfo_value->application['subtype'] != 'NA') ? $appInfo_value->application['subtype'] : 'Not set').'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-6" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'"><td>Description</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_value['n'].'-6-1" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'-'.$appInfo_n_value.'-6"><td>'.(($appInfo_value->application->p != 'NA') ? $appInfo_value->application->p : 'Not set').'</td></tr>';
								}
								foreach ($arr[$key]->editorialDecl as $editorialDecl_value) {
									echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Normalization</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$key_gen.'"><td>Method</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-1"><td>'.$editorialDecl_value->normalization['method'].'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$key_gen.'"><td>Description</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-1-2" class="child-of-ex2-node-4-'.$attrib.'-2-n'.$key_gen.'-2"><td>'.(($editorialDecl_value->normalization->p != 'NA') ? $editorialDecl_value->normalization->p : 'Not set').'</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Segmentation</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Corresp</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-1"><td>'.(($editorialDecl_value->segmentation['corresp'] != '' && $editorialDecl_value->segmentation['corresp'] != 'NA' && $editorialDecl_value->segmentation['corresp'] != NULL) ? $editorialDecl_value->segmentation['corresp'] : 'Not set').'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Style</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-2-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-2"><td>'.$editorialDecl_value->segmentation['style'].'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-3" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'"><td>Description</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-3-1" class="child-of-ex2-node-4-'.$attrib.'-2-s'.$key_gen.'-3"><td>'.(($editorialDecl_value->segmentation->p != 'NA') ? $editorialDecl_value->segmentation->p : 'Not set').'</td></tr>';
									echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'" class="child-of-ex2-node-4-'.$attrib.'-2-'.$key_gen.'"><td>Correction</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Method</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-1-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-1"><td>'.$editorialDecl_value->correction['method'].'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-2" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Status</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-2-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-2"><td>'.$editorialDecl_value->correction['status'].'</td></tr>';
										echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-3" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'"><td>Description</td></tr>';
											echo '<tr id="ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-3-1" class="child-of-ex2-node-4-'.$attrib.'-2-c'.$key_gen.'-3"><td>'.$editorialDecl_value->correction->p.'</td></tr>';
								}
							}
						}
					}
				}
                    
                    
				echo '<tr id="ex2-node-4-'.$attrib.'-3" class="child-of-ex2-node-4-'.$attrib.'"><td>Revision</td></tr>';
				
				foreach ($revisionDesc_array[$attrib]->change->attributes() as $attribute => $revisionDesc_value) {
					if ($attribute == 'when') {
						echo '<tr id="ex2-node-4-'.$attrib.'-3-1" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Date</td></tr>';
							echo '<tr id="ex2-node-4-'.$attrib.'-3-1-1" class="child-of-ex2-node-4-'.$attrib.'-3-1"><td>'.$revisionDesc_value.'</td></tr>';
					}
					elseif ($attribute == 'who') {
						echo '<tr id="ex2-node-4-'.$attrib.'-3-2" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Who</td></tr>';
							echo '<tr id="ex2-node-4-'.$attrib.'-3-2-1" class="child-of-ex2-node-4-'.$attrib.'-3-2"><td>'.($revisionDesc_value != 'NA' ? $revisionDesc_value : 'Not set').'</td></tr>';
					}
					elseif ($attribute == 'n') {
						echo '<tr id="ex2-node-4-'.$attrib.'-3-3" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Version</td></tr>';
							echo '<tr id="ex2-node-4-'.$attrib.'-3-3-1" class="child-of-ex2-node-4-'.$attrib.'-3-3"><td>'.$revisionDesc_value.'</td></tr>';
					}
				}
				foreach ($revisionDesc_array[$attrib] as $desc_revisionDesc_value) {
					echo '<tr id="ex2-node-4-'.$attrib.'-3-4" class="child-of-ex2-node-4-'.$attrib.'-3"><td>Description</td></tr>';
						echo '<tr id="ex2-node-4-'.$attrib.'-3-4-1" class="child-of-ex2-node-4-'.$attrib.'-3-4"><td>'.($desc_revisionDesc_value != 'NA' ? $desc_revisionDesc_value : 'Not set').'</td></tr>';
				}
        }
?>