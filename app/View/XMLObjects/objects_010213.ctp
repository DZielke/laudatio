<?php
#echo $this->Html->css('jquery.treeTable.css',array('inline' => false));
echo $this->Html->script('uploadscript',array('inline' => false));
echo $this->Html->script('jquery.treeTable.js',array('inline' => false));

$this->set('navbarArray',array(array('Object')));
if(isset($noTEI) && $noTei ==true){
	echo 'No TEI-Header available.';
}else{
	#header('Content-type: text/html; charset=utf-8');
	
	$xml = simplexml_load_string(html_entity_decode($getObjectVersionXml)) or die("Error: Can not create object");
	#var_dump((utf8_encode($newestTeiDate)));
    #foreach ($xml->objectChangeDate as $objectChangeDate) {
    #    var_dump(utf8_encode($objectChangeDate->asXML()));
    #}
		#ausgabe
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
		#echo "<option value='0'><p class='bold'><b>date</b></p></option>";
        # echo "<option selected value=".$x."><p class='bold'>".$objectChangeDate[$x]."</p></option>";
		echo "</select></td></tr>";
		#echo '<button type="button" onclick="createTreeTable()">tree</button>';
	
		echo '<table id="tree">';
		#echo '<table>';
		echo '<tr><td>'.$title.', '.$authority.', '.$version.', '.$extent.' Token';
		
		for ($x=0;$x<sizeof($label_format);) {
			#echo ', ';
		
			foreach ($download_format as $value) {

			
				if ($value == 'TEIxml') {
					$download='http://depot1-7.cms.hu-berlin.de:8080/fedora/objects/'.$pid.'/datastreams/TEI_header/content';
				}
				else {
					$download = "http://141.20.4.72:8080/fedora/get/".$pid."/".$value."";
				}
				echo ", ".$this->Html->link($label_format[$x], $download);
				$x++;
			}
		}
		echo '</td></tr>';
		echo '<tr><td><a href="'.$homepage.'">'.$homepage.'</a></td></tr>';
		#echo '</table>';
		
		#$u=1;


		#echo '<th>Name</th><th>Value</th>';
		
		#echo '<table id="tree">';
		echo '<tr id="ex2-node-1"><td>Corpus '.$title.'</td></tr>';
			echo '<tr id="ex2-node-1-1" class="child-of-ex2-node-1"><td>Authorship</td></tr>';
				echo '<tr id="ex2-node-1-1-1" class="child-of-ex2-node-1-1"><td>Corpus Editor</td></tr>';
					
		
					for ($x=0;$x<sizeof($editor_surname_array);$x++) {
						echo '<tr id="ex2-node-1-1-1-1" class="child-of-ex2-node-1-1-1">';
							echo '<td>'.$editor_forename_array[$x].' '.$editor_surname_array[$x].'</td>';
							echo ' ';
						#echo '</td></tr>';
						
						#echo '<tr id="ex2-node-1-1-1-1-1" class="child-of-ex2-node-1-1-1-1">';
							if (isset($editor_institution_name_array)) {
								echo '<td>('.$editor_department_name_array[$x].' '.$editor_institution_name_array[$x].')</td>';
								echo '<br>';
							}
						echo '</tr>';
						
					}
					
					
					#echo 'Node 1.1.1.1</td></tr>';
				echo '<tr id="ex2-node-1-1-2" class="child-of-ex2-node-1-1"><td>Corpus Annotator</td></tr>';
				
					for ($y=0;$y<sizeof($author_surname_array);$y++) {
						echo '<tr id="ex2-node-1-1-1-2" class="child-of-ex2-node-1-1-2"><td>';
							echo $author_forename_array[$y].' '.$author_surname_array[$y];
							echo ' ';
						#echo '</td></tr>';
						
						#echo '<tr id="ex2-node-1-1-1-1-2" class="child-of-ex2-node-1-1-1-2">';
							if (isset($editor_institution_name_array)) {
								echo '('.$author_department_name_array[$y].' '.$author_institution_name_array[$y].')';
								echo '<br>';
							}
						echo '</tr>';
						
					}
				
					#echo '<tr id="ex2-node-1-1-1-2" class="child-of-ex2-node-1-1-2"><td>Node 1.1.1.2</td></tr>';
			echo '<tr id="ex2-node-1-2" class="child-of-ex2-node-1"><td>Project</td></tr>';
				echo '<tr id="ex2-node-1-2-1" class="child-of-ex2-node-1-2"><td>Homepage</td></tr>';
					echo '<tr id="ex2-node-1-2-1-1" class="child-of-ex2-node-1-2-1"><td><a href="'.$homepage.'">'.$homepage.'</a></td></tr>';
				echo '<tr id="ex2-node-1-2-2" class="child-of-ex2-node-1-2"><td>Description</td></tr>';
					echo '<tr id="ex2-node-1-2-2-1" class="child-of-ex2-node-1-2-2"><td>'.$description.'</td></tr>';
			echo '<tr id="ex2-node-1-3" class="child-of-ex2-node-1"><td>Availability (Status: '.$av_status['status'].', '.$project.')</td></tr>';
				echo '<tr id="ex2-node-1-3-1" class="child-of-ex2-node-1-3"><td>Authority</td></tr>';
					echo '<tr id="ex2-node-1-3-1-1" class="child-of-ex2-node-1-3-1"><td>'.$authority.'</td></tr>';
				echo '<tr id="ex2-node-1-3-2" class="child-of-ex2-node-1-3"><td>Corpus Release</td></tr>';
					echo '<tr id="ex2-node-1-3-2-1" class="child-of-ex2-node-1-3-2"><td>'.$corpusrelease.'</td></tr>';
			echo '<tr id="ex2-node-1-4" class="child-of-ex2-node-1"><td>Token</td></tr>';
				echo '<tr id="ex2-node-1-4-1" class="child-of-ex2-node-1-4"><td>'.$extent.'</td></tr>';
			
			
			#echo '<td>Document\'s name</td><td>'.$item_title.'</td>';
			echo '<tr id="ex2-node-1-5" class="child-of-ex2-node-1"><td>Documents</td></tr>';
				echo '<tr id="ex2-node-1-5-1" class="child-of-ex2-node-1-5"><td>';
				
				#for($u=0;$u<sizeof($items_array);) {
				#	foreach ($item_title_array as $item_title) {
				#		echo $item_title;
				#	}
				#}
				
				
				echo '</td></tr>';
			echo '<tr id="ex2-node-1-6" class="child-of-ex2-node-1"><td>Format</td></tr>';
			
		echo '<tr id="ex2-node-2"><td>Documents of the Corpus</td></tr>';
			echo '<tr id="ex2-node-2-1" class="child-of-ex2-node-2"><td>Node 2.1</td></tr>';
				echo '<tr id="ex2-node-2-1-1" class="child-of-ex2-node-2-1"><td>Node 2.1.1</td></tr>';
			echo '<tr id="ex2-node-2-2" class="child-of-ex2-node-2"><td>Node 2.2</td></tr>';
				echo '<tr id="ex2-node-2-2-1" class="child-of-ex2-node-2-2"><td>Node 2.2.1</td></tr>';
					echo '<tr id="ex2-node-2-2-1-1" class="child-of-ex2-node-2-2-1"><td>Node 2.2.1.1</td></tr>';
				echo '<tr id="ex2-node-2-2-2" class="child-of-ex2-node-2-2"><td>Node 2.2.2</td></tr>';
			
		echo '<tr id="ex2-node-3"><td>Annotation</td></tr>';
		
		echo '<tr id="ex2-node-4"><td>PreparationStep</td></tr>';
		echo '</table>';
	}
?>