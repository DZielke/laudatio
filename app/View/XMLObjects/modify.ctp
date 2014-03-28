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
	$this->set('navbarArray',array(array('Modify','modify')));
	echo '<table>';
	
    echo '<th title="Object">Object<span class="helptooltip"><p>Click an object to display the corpus.</p>';
    echo $this->Html->image("help.png",array(
        'border' => '0',
        'width'=>'20px',
        'alt' => 'help',
    ));
    echo '</span></th>';
	
	echo '<th title="Label">Label</th><tr>';

    foreach ($corpora as $corpus) {
        $pid = $corpus['pid'];
        $label = $corpus['label'];
        $pids = preg_split('/:/', $pid);
        echo '<td title ='.$pids[1].'>';
        echo $this->Html->link($pids[1],array('controller' => 'XMLObjects', 'action' => 'modifyObject/'.urlencode(trim($pid))));

        #echo $this->Html->link('modifyObject/'.$pids[1], array('controller' => 'XMLObjects', 'action' => 'modifyObject/'.urlencode(trim($pid)), 'id' => 3, 'slug' => 'CakePHP_Rocks'));

        echo '</td>';
        echo '<td title="'.$label.'">';
        echo $label;
        echo "</td>";

        echo '<td><button type="button" onclick="location.href=\''.Router::url(array('controller' => 'XMLObjects','action' => 'modifyObject',$pid)).'\'" value="Modify Object">Modify Object</button></td>';
        echo '</tr>';
    }
	echo '</table>';
if(count($corpora) == 0)
    echo '<p>No corpora available</p>';
?>