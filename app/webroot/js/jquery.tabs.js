/*
 * jQuery Timetabs v1
 * http://www.tn34.de
 * Authors: Mario Alves (Javascript), David Hestler (Markup)
 *
 * Copyright 2010, TN34.DE
 * Released under the GPL license.
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Date: Tue Aug 17 12:19:511 2010
 */

(function(jQuery){

    jQuery.fn.timetabs = function(options) {

        var opt = jQuery.extend({}, jQuery.timetabs.defaults, options);

        // main function
        return this.each(function() {

            var gDLIndex = jQuery.timetabs.gDLI.length++;

            var delayedStartInterval = null;


            var continueFlag = false;

            // create object
            jQuery.timetabs.gDLI[gDLIndex] = {
                selfObj: jQuery(this),
                self: this,
                t: '',
                c: '',
                cIndex: 0,
                stTime: 0,
                timeOut: 0,
                timeLeft: 0
            };

            // children elements
            jQuery.timetabs.gDLI[gDLIndex].t = gDLI().selfObj.children('dt');
            jQuery.timetabs.gDLI[gDLIndex].c = gDLI().selfObj.children('dd');

            // check if dt.length and dd.length is equal
            if(gDLI().t.length !== gDLI().c.length) return;

            // validate defaultIndex
            jQuery.timetabs.gDLI[gDLIndex].cIndex = ((opt.defaultIndex+1) > gDLI().t.length || opt.defaultIndex < 0) ? 0 : opt.defaultIndex;

            switch(opt.animated) {
                case 'slide':
                case 'curtain':
                case 'fade':
                    break;

                default:
                case false:
                    opt.animated = false;
                    break;
            }


            if(opt.animated !== false) {
                gDLI().selfObj.find('dd:gt('+gDLI().cIndex+')').hide();
            }


            // activate defaultIndex
            _setActiveTab(gDLI().cIndex);

            jQuery(gDLI().t).click(function(e) {
                _setActiveTab(this, e);
                e.stopPropagation();
            });


            if(!opt.static){
                if((isNaN(opt.interval)) !== true || parseInt(opt.interval) > 0) {
                    jQuery(gDLI().t).mouseover(_pause).mouseenter(_pause).mouseleave(_unpause);
                    jQuery(gDLI().c).mouseover(_pause).mouseenter(_pause).mouseleave(_unpause);
                }
            }

            function _startInterval() {
                if(!opt.static){
                    clearTimeout(jQuery.timetabs.gDLI[gDLIndex].timeOut);
                    if(delayedStartInterval) clearTimeout(delayedStartInterval);

                    jQuery.timetabs.gDLI[gDLIndex].stTime = new Date().valueOf();
                    jQuery.timetabs.gDLI[gDLIndex].timeOut = setTimeout(function() {
                        _setActiveTab();
                    }, opt.interval);

                    jQuery.timetabs.gDLI[gDLIndex].timeLeft = opt.interval;
                }
            }

            function _pause() {
                clearTimeout(jQuery.timetabs.gDLI[gDLIndex].timeOut);
                if(delayedStartInterval) clearTimeout(delayedStartInterval);
                continueFlag = true;
                var timeRan = new Date().valueOf() - gDLI().stTime;
                jQuery.timetabs.gDLI[gDLIndex].timeLeft -= timeRan;
            }

            function _unpause() {
                if(!opt.static){
                    if(opt.continueOnMouseLeave === true) {
                        delayedStartInterval = setTimeout(function() {
                            jQuery.timetabs.gDLI[gDLIndex].timeout = setTimeout(function() {
                                _setActiveTab();
                            },  gDLI().timeLeft);
                        }, 350);
                    } else {
                        jQuery(gDLI().t).unbind('mouseover', _pause).unbind('mouseenter', _pause).unbind('mouseleave', _unpause);
                        jQuery(gDLI().c).unbind('mouseover', _pause).unbind('mouseenter', _pause).unbind('mouseleave', _unpause);
                    }
                }
            }

            function _setActiveTab(el, e) {

                clearTimeout(jQuery.timetabs.gDLI[gDLIndex].timeOut);
                if(delayedStartInterval) clearTimeout(delayedStartInterval);

                if(typeof el == 'number') {
                    var index = el;

                } else if(typeof el == 'undefined') {
                    var index = gDLI().cIndex + 1;

                } else if(typeof el == 'object') {
                    var index = gDLI().selfObj.children('dt').index(el);


                } else {
                    var index = 0;
                }

                if(index == gDLI().t.length) {
                    index = 0;
                }


                if(typeof e !== 'undefined' && jQuery.timetabs.gDLI[gDLIndex].cIndex == index) {
                    _startIntervalAftersetActiveTab(e);
                    return;
                }

                opt.beforeChange(gDLI());

                function tabAnimation(i) {
                    switch(opt.animated) {
                        //case false:break;
                        case 'slide':

                        case 'curtain':
                        case 'fade':
                            gDLI().selfObj.children('dt.active').removeClass('active');
                            jQuery(gDLI().t.get(i)).addClass('active');
                            break;

                    }
                }

                switch(opt.animated) {

                    case false:
                        tabAnimation(index);
                        gDLI().selfObj.children('.active').removeClass('active');
                        jQuery(gDLI().t.get(index)).addClass('active').next('dd').addClass('active');
                        _startIntervalAftersetActiveTab(e);
                        break;

                    case 'slide':
                        if(gDLI().selfObj.children('dd.active').length == 0) {
                            tabAnimation(index);
                            jQuery(gDLI().c.get(index)).addClass('active');


                            _startIntervalAftersetActiveTab(e);
                        } else {
                            gDLI().selfObj.children('dd.active').slideUp(opt.animationSpeed, function() {
                                tabAnimation(index);
                                jQuery(gDLI().c.get(index)).slideDown(opt.animationSpeed, function() {
                                    _startIntervalAftersetActiveTab(e);
                                    jQuery(this).addClass('active');
                                });
                                jQuery(this).removeClass('active').hide();
                            });
                        }
                        break;


                    case 'curtain':
                        tabAnimation(index);
                        jQuery(gDLI().c.get(index)).css('z-index', '10000').slideDown(opt.animationSpeed, function() {
                            gDLI().selfObj.children('dd.active').removeClass('active').hide();
                            _startIntervalAftersetActiveTab(e);
                            jQuery(this).css('z-index', '1').addClass('active');
                        });
                        break;

                    default:
                    case 'fade':
                        tabAnimation(index);
                        jQuery(gDLI().c.get(index)).css('z-index', '10000').fadeIn(opt.animationSpeed, function() {
                            gDLI().selfObj.children('dd.active').removeClass('active').hide();
                            _startIntervalAftersetActiveTab(e);
                            jQuery(this).css('z-index', '1').addClass('active');
                        });
                        break;
                }

                jQuery.timetabs.gDLI[gDLIndex].cIndex = index;

                opt.afterChange(gDLI());
            }

            function _startIntervalAftersetActiveTab(e) {
                if(opt.continueOnMouseLeave === false && continueFlag === true) {
                    return;
                }
                if(typeof e !== 'undefined' && ((isNaN(opt.interval)) !== true || parseInt(opt.interval) > 0)) {
                    gDLI().selfObj.one('mouseleave', function() {
                        _startInterval();
                    });
                } else _startInterval();
            }

            function gDLI() {
                return jQuery.timetabs.gDLI[gDLIndex];
            }
        });
    };

    jQuery.timetabs = {

        defaults: {
            static: true,
            defaultIndex: 0,
            interval: 2500,
            continueOnMouseLeave: true,
            animated: false,
            animationSpeed: 500,
            beforeChange: function(){},  // before load-function
            afterChange: function(){}	 // after load-function
        },

        gDLI: [] // global "dl" index
    }

})(jQuery);