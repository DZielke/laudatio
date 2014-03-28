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
echo $this->Html->script('uploadscript',array('inline' => false));
$this->set('navbarArray',array(array('Delete')));

if(!isset($response)){
	$response = '';
}
	echo '<table>';


    echo '<th title="Object">Object<span class="helptooltip"><p>Click on an object for a display of the corresponding corpus.</p>';
    echo $this->Html->image("help.png",array(
            "border" => "0",
            'width'=>'20px',
            "alt" => "help",
    ));

        echo '</span></th>';
	
	echo '<th title="Label">Label</th><tr>';

    foreach ($corpora as $corpus) {
        $pid = $corpus['pid'];
        $label = $corpus['label'];
        $pids = preg_split('/:/', $pid);
        echo '<td title ='.$pids[1].'>';
        echo $this->Html->link($pids[1],array('controller' => 'XMLObjects', 'action' => 'delete/'.urlencode(trim($pid))),array(),"Are you sure you wish to delete this corpus?");
        echo '</td>';
        echo '<td title="'.$label.'">';
        echo $label;
        echo "</td>";

        echo '<td><button type="button" onclick="if(confirm(\'Are you sure you wish to delete this corpus?\')) deleteObject(\''.urlencode(trim($pid)).'\'); else return false;">Delete Object</button></td>';
        echo '</tr>';
	}
	echo '</table>';
if(count($corpora) == 0)
    echo '<p>No corpora available</p>';
	/*if($response != '') {
        echo '<br /><div style="text-decoration:underline;font-weight:bold;">Antwort des Aufrufs</div><br />';
        echo '<div style="border:1px solid gray;">';
        $response = preg_replace('/.+<body>/','',$response);
        $response = preg_replace('#</body>.+#','',$response);
        echo $response;
        echo '</div>';
	}*/
?>