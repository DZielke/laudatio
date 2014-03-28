<?php
var_dump($xml);
foreach($xml->resultList->objectFields as $object){
echo '</br>'.$object->pid;
}

?>