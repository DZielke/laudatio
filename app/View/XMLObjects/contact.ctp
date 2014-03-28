<?php
    echo $this->Html->css('/js/css/jquery-ui.css', null, array('inline' => false));
    echo $this->Html->script('jquery-ui',array('inline' => false));

    $this->set('navbarArray',array(array('Contact','contact')));
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>LAUDATIO - Description</title>
    <script>

        $(function() {
            $( "#accordion" ).accordion({
                heightStyle: "content",
                header: "h2",
                collapsible: true,
                active: false
            });
        });
        /*
         $("#accordion").accordion({
         header: "h3",
         collapsible: true,
         active: false
         });
         */

        /*
         * hoverIntent | Copyright 2011 Brian Cherne
         * http://cherne.net/brian/resources/jquery.hoverIntent.html
         * modified by the jQuery UI team
         */

        $.event.special.hoverintent = {
            setup: function() {
                $( this ).bind( "mouseover", jQuery.event.special.hoverintent.handler );
            },
            teardown: function() {
                $( this ).unbind( "mouseover", jQuery.event.special.hoverintent.handler );
            },
            handler: function( event ) {
                var currentX, currentY, timeout,
                    args = arguments,
                    target = $( event.target ),
                    previousX = event.pageX,
                    previousY = event.pageY;

                function track(event) {
                    currentX = event.pageX;
                    currentY = event.pageY;
                };
                function clear() {
                    target
                        .unbind( "mousemove", track )
                        .unbind( "mouseout", clear );
                    clearTimeout( timeout );
                }
                function handler() {
                    var prop,
                        orig = event;
                    if ( ( Math.abs( previousX - currentX ) +
                        Math.abs( previousY - currentY ) ) < 7 ) {
                        clear();
                        event = $.Event( "hoverintent" );
                        for ( prop in orig ) {
                            if ( !( prop in event ) ) {
                                event[ prop ] = orig[ prop ];
                            }
                        }
                        // Prevent accessing the original event since the new event
                        // is fired asynchronously and the old event is no longer
                        // usable (#6028)
                        delete event.originalEvent;
                        target.trigger( event );
                    }
                    else {
                        previousX = currentX;
                        previousY = currentY;
                        timeout = setTimeout(handler, 200);
                    }
                }
                timeout = setTimeout(handler, 200);
                target.bind({
                    mousemove: track,
                    mouseout: clear
                });
            }
        };
    </script>
</head>

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

<body>

<h3>Contact</h3>
<!--<p>Long-term Access and Usage of Deeply Annotated Information</p>-->

<div id="accordion">
    <h2><a href="#">Project Management</a></h2>
    <div>
        <table>
        <tr>
        <td>
            <b>Prof. Dr. phil. Karin Donhauser</b><br>
            <u>Visiting Address:</u>
            <p>Dorotheenstraße 24, Raum 3.215</p>
            <p>10117</br>Berlin - Mitte</p>

            <u>Postal Address:</u>
            <p>Institut für deutsche Sprache und Linguistik<br>
            Philosophische Fakultät II<br>
            Humboldt-Universität zu Berlin<br>
            Unter den Linden 6<br>
            D-10099 Berlin</p>

            <u>Telephone:</u>
            <p>+49 (0)30 2093-9635</p>

            <u>E-Mail:</u>
            <?php
                $mail = 'karin.donhauser@staff.hu-berlin.de';
                $myMail = $this->Text->autoLinkEmails($mail);
                echo '<p>'.$myMail.'</p>';
            ?>
        </td>
        <td>
            <b>Prof. Dr. phil. Anke Lüdeling</b><br>
            <u>Visiting Address:</u>
            <p>Dorotheenstraße 24, Raum 3.342</p>
            <p>10117</br>Berlin - Mitte</p>

            <u>Postal Address:</u>
            <p>Institut für deutsche Sprache und Linguistik<br>
            Philosophische Fakultät II<br>
            Humboldt-Universität zu Berlin<br>
            Unter den Linden 6<br>
            D-10099 Berlin</p>

            <u>Telephone:</u>
            <p>+49 (0)30 2093–9799</p>

            <u>Fax:</u>
            <p>+49 (0)30 2093–9729</p>

            <u>E-Mail:</u>
            <?php
                $mail = 'anke.luedeling@rz.hu-berlin.de';
                $myMail = $this->Text->autoLinkEmails($mail);
                echo '<p>'.$myMail.'</p>';
            ?>
        </td>
        </tr>
        <tr>
        <td>
            <b>Laurent Romary, PhD Habil.</b><br>
            <u>Visiting Address:</u>
            <p>Dorotheenstraße 24, Raum 3.403</p>
            <p>10117</br>Berlin - Mitte</p>

            <u>Postal Address:</u>
            <p>Institut für deutsche Sprache und Linguistik<br>
            Philosophische Fakultät II<br>
            Humboldt-Universität zu Berlin<br>
            Unter den Linden 6<br>
            D-10099 Berlin</p>

            <u>E-Mail:</u>
            <?php
                $mail = 'laurent.romary@inria.fr';
                $myMail = $this->Text->autoLinkEmails($mail);
                echo '<p>'.$myMail.'</p>';
            ?>
        </td>
        <td>
            <b>Prof. Dr. Peter Schirmbacher</b><br>
            <u>Visiting Address:</u>
            <p>Rudower Chaussee 26, Raum 2'116</p>
            <p>12489</br>Berlin - Adlershof</p>

            <u>Postal Address:</u>
            <p>Humboldt-Universität zu Berlin<br>
            Computer- und Medienservice<br>
            Unter den Linden 6<br>
            D-10099 Berlin</p>

            <u>Telephone:</u>
            <p>+49 (0)30 2093-70010</p>

            <u>Fax:</u>
            <p>+49 (0)30 2093-2959</p>

            <u>E-Mail:</u>
            <?php
                $mail = 'schirmbacher@cms.hu-berlin.de';
                $myMail = $this->Text->autoLinkEmails($mail);
                echo '<p>'.$myMail.'</p>';
            ?>
        </td>
        </tr>
        </table>
    </div>
    <h2><a href="#">Substantive Responsibility</a></h2>
    <div>
        <table>
            <tr>
                <td>
                    <b>Carolin Odebrecht</b><br>
                    <u>Visiting Address:</u>
                    <p>Dorotheenstraße 24, Raum 3.408</p>
                    <p>10117</br>Berlin - Mitte</p>

                    <u>Postal Address:</u>
                    <p>Institut für deutsche Sprache und Linguistik<br>
                        Philosophische Fakultät II<br>
                        Humboldt-Universität zu Berlin<br>
                        Unter den Linden 6<br>
                        D-10099 Berlin</p>

                    <u>Telephone:</u>
                    <p>+49 (0)30 2093-9618</p>

                    <u>E-Mail:</u>
                    <?php
                        $mail = 'carolin.odebrecht@hu-berlin.de';
                        $myMail = $this->Text->autoLinkEmails($mail);
                        echo '<p>'.$myMail.'</p>';
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <h2><a href="#">Technical Responsibility</a></h2>
    <div>
        <table>
            <tr>
                <td>
                    <b>Thomas Krause</b><br>
                    <u>Visiting Address:</u>
                    <p>Dorotheenstraße 24, Raum 3.333</p>
                    <p>10117</br>Berlin - Mitte</p>

                    <u>Postal Address:</u>
                    <p>Institut für deutsche Sprache und Linguistik<br>
                        Philosophische Fakultät II<br>
                        Humboldt-Universität zu Berlin<br>
                        Unter den Linden 6<br>
                        D-10099 Berlin</p>

                    <u>Telephone:</u>
                    <p>+49 (0)30 2093 9720</p>

                    <u>E-Mail:</u>
                    <?php
                        $mail = 'thomas.krause@alumni.hu-berlin.de';
                        $myMail = $this->Text->autoLinkEmails($mail);
                        echo '<p>'.$myMail.'</p>';
                    ?>
                </td>
                <td>
                    <b>Dennis Zielke</b><br>
                    <u>Visiting Address:</u>
                    <p>Rudower Chaussee 26 , Raum 3'333</p>
                    <p>12489</br>Berlin - Adlershof</p>

                    <u>Postal Address:</u>
                    <p>Humboldt-Universität zu Berlin<br>
                        Computer- und Medienservice<br>
                        Unter den Linden 6<br>
                        D-10099 Berlin</p>

                    <u>Telephone:</u>
                    <p>+49 (0)30 2093-70004</p>

                    <u>E-Mail:</u>
                    <?php
                        $mail = 'dennis.zielke@cms.hu-berlin.de';
                        $myMail = $this->Text->autoLinkEmails($mail);
                        echo '<p>'.$myMail.'</p>';
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <h2><a href="#">Disclaimer</a></h2>
    <div>
        <table>
            <tr>
                <td>
                    <p>The LAUDATIO-Team is not responsible for any contents linked or referred to from its pages. The published links are researched and compiled with best possible accurateness. The LAUDATIO-Team does not have any influence on the current or future design and contents of the pages linked to. The LAUDATIO-Team is not responsible for the contents of the linked pages nor does it embrace it. Only the author of the respective pages might be liable for illegal, incorrect or incomplete contents as well as for any damage caused by the use of information presented there. The liability of the one who has only linked to these pages is excluded. </p>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>