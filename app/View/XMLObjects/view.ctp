<!-- Piwik -->
<script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
    _paq.push(["setCookieDomain", "*.www.laudatio-repository.org"]);
    _paq.push(["trackPageView"]);
    _paq.push(["enableLinkTracking"]);

    (function() {
        var u=(("https:" == document.location.protocol) ? "https" : "http") + "://www.laudatio-repository.org/piwik/";
        _paq.push(["setTrackerUrl", u+"piwik.php"]);
        _paq.push(["setSiteId", "1"]);
        var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
        g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
    })();
</script>
<!-- End Piwik Code -->

<?php
	$this->set('navbarArray',array(array('View','view')));
	echo '<table>';
	
	echo '<th>Object<span class="helptooltip"><p>Click on an object to display the corpus.</p>';
	echo $this->Html->image("help.png",array(
		'border' => '0',
        'width'=>'20px',
        'alt' => 'help',
	));
	echo '</span></th>';

	echo '<th>Label</th><tr>';
	//$xml = simplexml_load_string(html_entity_decode($xmloutput)) or die("Error: Can not create object");

		foreach ($corpora as $corpus) {
                $pid = $corpus['pid'];
                $label = $corpus['label'];
				$pids = preg_split('/:/', $pid);
				echo '<td title ='.$pids[1].'>';
				echo $this->Html->link($pids[1],array('controller' => 'XMLObjects', 'action' => 'corpus',$pid));
                #echo $this->Html->link($pids[1],('view/:objects', array('controller' => 'XMLObjects', 'action' => 'objects',$pid), array('pass'=>array('objects'))));
                #Router::connect('view/:objects', array('controller' => 'XMLObjects', 'action' => 'view'), array('pass'=>array('objects')));
				echo '</td>';
				echo '<td title="'.$label.'">';
				echo $label;
				echo "</td>";

				//echo '<td><button type="button" onclick="location.href=\''.Router::url(array('controller' => 'XMLObjects','action' => 'objects',$pid)).'\'" value="View Object">View Object</button></td>';
                echo '<td><button type="button" onclick="location.href=\''.Router::url(array('controller' => 'XMLObjects','action' => 'corpus',$pid)).'\'" value="View Corpus">View Corpus</button></td>';
            echo '</tr>';
		}


	echo '</table>';
    if(count($corpora) == 0)
        echo '<p>No corpora available</p>';
?>