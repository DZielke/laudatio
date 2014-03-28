<?php
echo $this->Html->script('/jquery-ui/jquery-1.9.1.js',array('inline' => false));
echo $this->Html->script('/jquery-ui/jquery-ui.js',array('inline' => false));
echo $this->Html->css('/css/jquery-ui-1.8.18.custom.css', null, array('inline' => false));

$this->set('navbarArray',array(array('Impressum')));
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>LAUDATIO - Description</title>
    <!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />-->
    <!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
    <!--<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>-->
    <!--<link rel="stylesheet" href="/resources/demos/style.css" />-->
    <script>


        $(function() {
            $( "#accordion" ).accordion({
                event: "click hoverintent"
            });
        });

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
                function track( event ) {
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
                    } else {
                        previousX = currentX;
                        previousY = currentY;
                        timeout = setTimeout( handler, 50 );
                    }
                }
                timeout = setTimeout( handler, 50 );
                target.bind({
                    mousemove: track,
                    mouseout: clear
                });
            }
        };
    </script>
</head>
<body>

<h3>Impressum</h3>
<!--<p>Impressum Hier werden noch Ansprechpartner benannt. </p>-->

<div id="accordion">
    <h5>Anbieter</h5>
    <div>
        <p>
        </p>
    </div>
    <h5>Vertreter</h5>
    <div>
        <p>
        </p>
    </div>
    <h5>Redaktionsverantwortliche</h5>
    <div>
        <p>
        </p>
    </div>
    <h5>Layout&Programmierung</h5>
    <div>
        <p>
        </p>
    </div>
    <h5>Haftungsausschluss</h5>
    <div>
        <p>
        </p>
    </div>
</div>
</body>
</html>

<?php
    if($this->Session->read('Auth.User.group_id') == '1'){
        // require_once("/var/www/cakephp-2.2.3/lib/Java.inc");
        //echo java("java.lang.System")->getProperties();
        //echo phpinfo();
    }
?>
