<?php
echo $this->Html->script('dshistory',array('inline' => false));
$this->set('navbarArray',array(array('Modify','modify'),array('Modify Object','modifyObject/'.urlencode($objectID)),array('Object History')));

    echo "<table>";
    echo "<tr><td>Object-PID:</td><td>".$xml->attributes()->pid."</td></tr>";
    echo "<tr><td>Datastream-PID:</td><td>".$xml->attributes()->dsID."</td></tr>";
    echo "</table>";

    foreach($xml->datastreamProfile as $version){
        echo '</br><table>';
        echo "<tr><td>Label:</td><td>".$version->dsLabel."</td></tr>";
        echo "<tr><td>VersionID:</td><td>".$version->dsVersionID."</td></tr>";
        echo "<tr><td>Create Date:</td><td>".$version->dsCreateDate."</td></tr>";
        echo "<tr><td>MIME-Type:</td><td>".$version->dsMIME."</td></tr>";
        echo "<tr><td>Size:</td><td>".$version->dsSize."</td></tr>";
        echo "<tr><td>Versionable:</td><td>".$version->dsVersionable."</td></tr>";
        echo "<tr><td>";
        echo "<a class='clickable' style='float:right;' onclick='deleteVersion(\"".$xml->attributes()->pid."\",\"".$xml->attributes()->dsID."\",\"".$version->dsCreateDate."\")'>delete</a>";
        echo "<a href='../../downloadDatastream/".urlencode(trim($xml->attributes()->pid))."/".urlencode(trim($xml->attributes()->dsID))."/".urlencode(trim($version->dsCreateDate))."'>download</a></td></tr>";
        echo '</table>';
    }

?>