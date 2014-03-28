<?php
    echo $this->Html->script('ace/ace',array('inline' => false));
    $this->Html->css('ace', null, array('inline' => false));
?>
<h2>Hilfe - Konfiguration der View</h2>
<p>Die Konfiguration der Ansicht erfolgt über eine <a href="http://www.json.org/">JSON</a>-Datei. In dieser Datei wird jedem Pfad in der Ansicht eine Pfad im TeiHeader eines Corpus zugeordnet. Auf diese Weise kann die Ansicht ohne Programmierkenntnisse konfiguriert werden. Gleichzeitig ermöglicht die Konfigurationsdatei das dynamische Nachladen der aufzuklappenden Elemente der Ansicht.
Sind weitere Funktionalitäten gewünscht oder gibt es Frage, schreibt bitte eine Mail an Dennis oder Tino.</p>
<h3>Struktur</h3>
<p>Die Grundstruktur wird beim Erstellen einer neuen Konfigurationsdatei vorgegeben. Die 4 Wurzelknoten der Ansicht (Corpus, Documents,Annotation,PreparationStep) werden durch die Objekte "ex2-node-X" repräsentiert und sind nicht veränderbar.
    Alle Kindknoten, die durch das Aufklappen eines Knotens sichtbar werden, werden als in den Elternknoten geschachtelte Objekte angegeben.
Der Titel des Knotens wird, ebenfalls als im Elternbjekt geschachteltes Objekt, mit dem Schlüssel "value" angegeben.</p>
Beispiel:
<div id="editor1" style="position:relative;height:130px;width:400px;clear:both">{
    "elternknoten":{
        "value":"Titel Elternknoten",
        "kindknoten":{
            #Inhalt Kindknoten
        }
    }
}</div>
<p>Als Schlüssel der Kindknoten ist immer eine Zahl oder ein Minus in doppelten Anführungszeichen zu wählen. Die Zahlen geben die Reihenfolge der Knoten in der Ansicht vor. Der Schlüssel "-" bedeutet, dass es sich nicht um ein Kindknoten, sondern um eine Menge von Kindknoten handelt, die alle aus demselben Knoten des TeiHeaders stammen. Mehr dazu im Abschnitt <i>Arrays</i>.</p>
        <p>Objekte ("Schlüssel":"Wert") werden durch Komma voneinander getrennt.</p>

<h3>Pfade</h3>
<p>Soll der Titel eines Knotens kein fest definierter Text sein, sondern Inhalt aus dem TeiHeader des Corpus enthalten, werden Pfade verwendet. Ein Pfad gibt den Ort im TeiHeader an, an dem der gewünschte Inhalt zu finden ist. Pfade beginnen immer mit einem "$".</p>
Beispiel:
<div id="editor2" style="position:relative;height:115px;width:600px;clear:both">{
    "ex2-node-1":{
        "1":{
            "value":"Corpusname: $teiHeader->fileDesc->titleStmt->title"
        }
    }
}</div>
</br>
<p>Pfade zu Attributen werden mit Hilfe von eckigen Klammern angegeben. "CorpusHeader" wird im folgenden TeiHeader-Ausschnitt über den Pfad $teiCorpus->teiHeader['type'] ereicht.</p>
&ltteiCorpus&gt</br>
&nbsp;&nbsp;&nbsp;&ltteiHeader type="CorpusHeader"&gt</br>
&nbsp;&nbsp;&nbsp;&lt/teiHeader&gt</br>
&lt/teiCorpus&gt</br>
<h3>Arrays</h3>
<p>Arrays erzeugen beim Aufklappen des Elternknotens eine Menge von Kindknoten. Arrays werden durch "[]" angezeigt.</p>
<p>Beispiel: Ein Corpus enthält eine Menge von Dokumenten. Die folgende Konfiguration listet die Titel aller Dokumente eines Corpus, beim Aufklappen des <i>Documents</i>-Knotens, auf.
Dokumente erscheinen im TeiHeader als ein Array (deutsch: Feld oder Reihe) von &ltteiHeader&gt-Knoten. Dieses Array wird durch den Pfad $teiCorpus->teiHeader[] aufgelöst.</p>
<div id="editor3" style="position:relative;height:115px;width:600px;clear:both">{
    "ex2-node-2":{
        "-":{
            "value":"$teiCorpus->teiHeader[]->fileDesc->titleStmt->title"
        }
    }
}</div>
<h3>Bedingungen</h3>
<p>Die Elemente eines Arrays können durch Bedingungen gefiltert werden. Elemente, die diese Bedigngung nicht erfüllen, werden in der Ansicht nicht angezeigt.
Die Bedingung wird in einem Objekt mit dem Schlüssel "condition" angegeben. Der Pfad des zu überprüfenden Wertes wird ausgehend vom Array angegeben und beginnt mit einem "$".</p>
<p>Beispiel: Die folgende Konfiguration listet nur Dokumente auf, deren Größe über 100 liegt.</p>
<div id="editor4" style="position:relative;height:130px;width:600px;clear:both">{
    "ex2-node-2":{
        "-":{
            "value":"$teiCorpus->teiHeader[]->fileDesc->titleStmt->title",
            "condition":"$->fileDesc->extent > 100"
        }
    }
}</div>
<p>Bedingungen für mehrere Pfade im Titel werden durch Komma von einander getrennt. Siehe Beispiel im Abschnitt <i>Default-Werte</i></p>
<h3>Default-Werte</h3>
<p>Default-Werte sind alternative Titel des Elements, falls sich unter dem angegebenen Pfad im TeiHeader kein Wert finden lässt.</p>
Beispiel:
<div id="editor5" style="position:relative;height:130px;width:600px;clear:both">{
    "ex2-node-2":{
    "-":{
        "value":"$teiCorpus->teiHeader[]->fileDesc->titleStmt->title",
            "default":"document title unknown"
        }
    }
}</div>
<p>Durch das Schlüsselwort "omit" wird der Eintrag nicht angezeigt. Werden mehrere Pfade im Titel verwendet, können einzelne default-Werte durch das Objekt "default_values" gesetzt werden. Die Alternativen werden druch Komma voneinander getrennt. Kann einer der Pfade des Titels nicht aufgelöst werden, wird der Wert aus "default_values" verwendet. Kann keiner der Pfade aufgelöst werden, wird der Wert aus "default" eingesetzt.</p>

<p>Beispiel: Die folgende Konfiguration bewirkt, dass der Eintrag nicht angezeigt wird, falls keine Größe unter dem Pfad ->fileDesc->extent zu finden ist. Ist die Größeneinheit unter ->fileDesc->extent['type'] nicht angegeben, wird diese durch "" ersetzt und somit weggelassen.</p>

<div id="editor6" style="position:relative;height:180px;clear:both">{
    "ex2-node-2":{
        "-":{
            "value":"$teiCorpus->teiHeader[]->fileDesc->titleStmt->title",
            "1":{
                "value":"Size: $teiCorpus->teiHeader[]->fileDesc->extent $teiCorpus->teiHeader[]->fileDesc->extent['type']",
                "default_values":"omit,"
            }
        }
    }
}</div>

<h3>Weitere Optionen</h3>
<p>Es existieren weitere Optionen, die das Verhalten und die Darstellung der Knoten verändern können. Die im Folgenden aufgeführten Optionen
sind für Spezialfälle entworfen und mit Vorsicht einzusetzen.</p>
<h4>Class</h4>
Mit Hilfe der Option "class" kann dem Knoten eine Klasse zugewiesen werden. Klassen werden zur Selektion von Elementen verwendet, um z.B. deren Aussehen per CSS zu verändern.
Zur Zeit wird die Klasse "link" verwendet, um Knotentitel wie Links aussehen zu lassen und per JavaScript auf Klicks zu reagieren. So wird z.B. beim klick auf ein Dokument unter dem Wurzelknoten Corpus, der entsprechende Kindknoten unter dem Wurzelknoten Documents geöffnet. Solche speziellen Verhalten müssen auf Anfrage entwickelt werden.
<h4>Type</h4>
<p>Verändert den Anzeigetyp von Arrayknoten.</p>
Bisher implementiert: </br>"type":"list" - Stellt die Knoten in einer Liste dar statt als eigene Knoten. Funktioniert nur mit Knoten, die keine Kindknoten besitzen.

<h4>Tooltip</h4>
<p>Tooltips werden am rechten Rand der Knoten mit einem ?-Symbol angezeigt. Wird die Maus über dieses Symbol bewegt, wird der konfigurierte Text angezeigt.</p>
    </br> "tooltip":"text" ->
<?php echo '<span class="helptooltip"><p>text</p>';
echo $this->Html->image("help.png",array(
"border" => "0",
'width'=>'20px',
"alt" => "help",
));
echo '</span>'?>
<h4>PDF</h4>
in Arbeit


<h3>Preview</h3>
<p>Per Klick auf den Preview-Button wird ein neues Fenster geöffnet, in dem die Voransicht für die aktuelle (ungespeicherte) Konfiguration und den TeiHeader des ausgewählten Corpus angezeigt wird. Die Konfiguration wird dabei nicht gespeichert.</p>
Achtung: Die Preview kann nicht mit F5 aktualisiert werden. Wird die Seite neu geladen, wird die gespeicherte Konfiguration verwendet. Um die Voransicht zu erneuern muss der Preview-Button erneut betätigt werden.
<script>

var editors = new Array();
editors.push(ace.edit("editor1"));
editors.push(ace.edit("editor2"));
editors.push(ace.edit("editor3"));
editors.push(ace.edit("editor4"));
editors.push(ace.edit("editor5"));
editors.push(ace.edit("editor6"));
for (var i = 0; i < editors.length; i++){
    editors[i].setTheme("ace/theme/clouds");
    editors[i].getSession().setMode("ace/mode/json");
    editors[i].getSession().setUseWrapMode(false);
    editors[i].setReadOnly(true);
    editors[i].setHighlightActiveLine(false);
    editors[i].setShowPrintMargin(false);
}
</script>