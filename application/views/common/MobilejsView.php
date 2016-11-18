<!--not tho change js-->
<script type="text/javascript" src="<?php echo base_url(); ?>asset/js/jquery-2.2.4.min.js"></script>
<!-- Framework 7 script -->
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/framework7.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/js/material.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/jquery.timeago.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/jssocials.min.js"></script>
<!--<script type="text/javascript" src="<?php /*echo base_url(); */?>asset/mobile/js/jquery-clockpicker.min.js"></script>-->
<script type="text/javascript" src="<?php echo base_url();?>asset/mobile/js/jquery.timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/jquery.swipebox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/jquery.geolocation.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/welcomescreen.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/cropper.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/fullcalendar.min.js"></script>
<!--<script type="text/javascript" src="<?php /*echo base_url(); */?>asset/mobile/js/dialog-polyfill.js"></script>-->
<!--<script type="text/javascript" src="<?php /*echo base_url(); */?>asset/js/jquery-ui.js"></script>-->
<script type="text/javascript" src="<?php echo base_url(); ?>asset/mobile/js/vex.combined.min.js"></script>

<!--<script type="text/javascript" src="<?php /*echo base_url(); */?>asset/mobile/js/hammer.min.js"></script>-->

<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-86757534-1', 'auto');
    ga('send', 'pageview');

</script>

<script>
    window.jukeLat = 0;
    window.jukeLong = 0;
    window.base_url = '<?php echo base_url(); ?>';
    var isAndroid = Framework7.prototype.device.android === true;
    var isIos = Framework7.prototype.device.ios === true;

    /*Template7.global = {
        android: isAndroid,
        ios: isIos
    };*/

    // Export selectors engine
    var $$ = Dom7;

    var welcomescreen_slides = [
        {
            id: 'slide0',
            picture: '<div class="tutorialicon"><img src="<?php echo base_url();?>asset/images/splashLogo.png"/>'+
            '<span class="load-txt">Loading</span><div class="progress-bar"><div class="progressbar-infinite"></div></div></div>'
        }
    ];
    var options = {
        'bgcolor': '#fff',
        'fontcolor': '#000',
        closeButton:false,
        pagination:false
    };
    vex.defaultOptions.className = 'vex-theme-plain';
   /* <div class="windows8">'+
    '<div class="wBall" id="wBall_1">'+
    '<div class="wInnerBall"></div>'+
    '</div>'+
    '<div class="wBall" id="wBall_2">'+
    '<div class="wInnerBall"></div>'+
    '</div>'+
    '<div class="wBall" id="wBall_3">'+
    '<div class="wInnerBall"></div>'+
    '</div>'+
    '<div class="wBall" id="wBall_4">'+
    '<div class="wInnerBall"></div>'+
    '</div>'+
    '<div class="wBall" id="wBall_5">'+
    '<div class="wInnerBall"></div>'+
    '</div>'+
    '</div>*/
    var MS_IN_MINUTES = 60 * 1000;
    var formatTime = function(date) {
        return date.toISOString().replace(/-|:|\.\d+/g, '');
    };

    var calculateEndTime = function(event) {
        return event.end ?
            formatTime(event.end) :
            formatTime(new Date(event.start.getTime() + (event.duration * MS_IN_MINUTES)));
    };

    var calendarGenerators = {
        google: function(event) {
            var startTime = formatTime(event.start);
            var endTime = calculateEndTime(event);

            var href = encodeURI([
                'https://www.google.com/calendar/render',
                '?action=TEMPLATE',
                '&text=' + (event.title || ''),
                '&dates=' + (startTime || ''),
                '/' + (endTime || ''),
                '&details=' + (event.description || ''),
                '&location=' + (event.address || ''),
                '&sprop=&sprop=name:'
            ].join(''));
            return '<a class="icon-google external item-link list-button" target="_blank" href="' +
                href + '">Google Calendar</a>';
        },

        yahoo: function(event) {
            var eventDuration = event.end ?
                ((event.end.getTime() - event.start.getTime())/ MS_IN_MINUTES) :
                event.duration;

            // Yahoo dates are crazy, we need to convert the duration from minutes to hh:mm
            var yahooHourDuration = eventDuration < 600 ?
            '0' + Math.floor((eventDuration / 60)) :
            Math.floor((eventDuration / 60)) + '';

            var yahooMinuteDuration = eventDuration % 60 < 10 ?
            '0' + eventDuration % 60 :
            eventDuration % 60 + '';

            var yahooEventDuration = yahooHourDuration + yahooMinuteDuration;

            // Remove timezone from event time
            var st = formatTime(new Date(event.start - (event.start.getTimezoneOffset() *
                    MS_IN_MINUTES))) || '';

            var href = encodeURI([
                'http://calendar.yahoo.com/?v=60&view=d&type=20',
                '&title=' + (event.title || ''),
                '&st=' + st,
                '&dur=' + (yahooEventDuration || ''),
                '&desc=' + (event.description || ''),
                '&in_loc=' + (event.address || '')
            ].join(''));

            return '<a class="icon-yahoo external item-link list-button" target="_blank" href="' +
                href + '">Yahoo! Calendar</a>';
        },

        ics: function(event, eClass, calendarName) {
            var startTime = formatTime(event.start);
            var endTime = calculateEndTime(event);

            var href = encodeURI(
                'data:text/calendar;charset=utf8,' + [
                    'BEGIN:VCALENDAR',
                    'VERSION:2.0',
                    'BEGIN:VEVENT',
                    'URL:' + document.URL,
                    'DTSTART:' + (startTime || ''),
                    'DTEND:' + (endTime || ''),
                    'SUMMARY:' + (event.title || ''),
                    'DESCRIPTION:' + (event.description || ''),
                    'LOCATION:' + (event.address || ''),
                    'END:VEVENT',
                    'END:VCALENDAR'].join('\n'));

            return '<a class="' + eClass + ' external item-link list-button" target="_blank" href="' +
                href + '">' + calendarName + ' Calendar</a>';
        },

        ical: function(event) {
            return this.ics(event, 'icon-ical', 'iCal');
        },

        outlook: function(event) {
            return this.ics(event, 'icon-outlook', 'Outlook');
        }
    };

    var generateCalendars = function(event) {
        return {
            google: calendarGenerators.google(event),
            yahoo: calendarGenerators.yahoo(event),
            ical: calendarGenerators.ical(event),
            outlook: calendarGenerators.outlook(event)
        };
    };
    $$(document).on("focus",".kbdfix", function(e)
    {
        if(isAndroid)
        {
            var el = $$(e.target);
            var page = el.closest(".page-content");
            var elTop = el.offset().top; //do correction if input at near or below middle of screen
            if(elTop > (page.height() / 2) )
            {
                var delta = page.offset().top + elTop - $$(".statusbar-overlay").height() * (myApp.device.ios?1:2) - $$(".navbar").height();
                //minus navbar height?&quest;? 56 fot MD
                var kbdfix = page.find("#keyboard-fix");
                if(kbdfix.length == 0)
                {
                    //create kbdfix element
                    page.append("<div id='keyboard-fix'></div>");
                }
                $$("#keyboard-fix").css("height", delta * 2 + "px");
                page.scrollTop( delta, 300);
            }
        }
    }, true);

    $$(document).on("blur",".kbdfix", function(e)
    { //reduce all fixes
        if(isAndroid)
        {
            $$("#keyboard-fix").css("height", "0px");
        }
    }, true);
    var dialogClose = false;
    var confirmClose = false;
    function alertDialog(title, msg, callbak)
    {
        dialogClose = callbak;
        var dialog = document.querySelector('dialog#alertDialog');
        $(dialog).find('.mdl-dialog__title').html(title);
        $(dialog).find('p').html(msg);
        if (! dialog.showModal) {
            dialogPolyfill.registerDialog(dialog);
        }
        dialog.showModal();
        dialog.querySelector('.close').addEventListener('click', function() {
            $('dialog.mdl-dialog').effect( "drop", "slow", function(){
                if(dialog.open)
                {
                    dialog.close();
                }
            });
        });
    }
    document.querySelector('dialog#alertDialog').addEventListener('close', function() {
        if(dialogClose)
        {
            setTimeout(function(){
                mainView.router.back({
                    ignoreCache: true
                });
            },500);
        }
        var diaClose = setInterval(function(){
            if($('dialog#alertDialog').attr('style') != '')
            {
                $('dialog#alertDialog').attr('style','');
                clearInterval(diaClose);
            }
        },2000);
    });
    var dialog1;
    function confirmDialog(title, msg, otherBtn, impStuff, callbak)
    {
        confirmClose = callbak;
        dialog1 = document.querySelector('dialog#confirmDialog');
        $(dialog1).find('.mdl-dialog__title').html(title);
        $(dialog1).find('p').html(msg);
        $(dialog1).find('#imp-stuff').val(impStuff);
        $(dialog1).find('.confirm-option').html(otherBtn);
        if (! dialog1.showModal) {
            dialogPolyfill.registerDialog(dialog1);
        }
        dialog1.showModal();
        dialog1.querySelector('.close').addEventListener('click', function() {
            $('dialog.mdl-dialog#confirmDialog').effect( "drop", "slow", function(){
                if(dialog1.open)
                {
                    dialog1.close();
                }
            });
        });
    }
    document.querySelector('dialog#confirmDialog').addEventListener('close', function() {
        if(confirmClose)
        {
            setTimeout(function(){
                mainView.router.back({
                    ignoreCache: true
                });
            },500);
        }
        var diaClose1 = setInterval(function(){
            if($('dialog#confirmDialog').attr('style') != '')
            {
                $('dialog#confirmDialog').attr('style','');
                clearInterval(diaClose1);
            }
        },2000);
    });
    document.querySelector('.confirm-option').addEventListener('click', function(){
        cancelEvent($('#confirmDialog #imp-stuff').val());
        if(dialog1.open)
        {
            dialog1.close();
        }
    });

    function ConvertTimeformat(format, str)
    {
        if(str != '')
        {
            var time = str;
            var hours = Number(time.match(/^(\d+)/)[1]);
            var minutes = Number(time.match(/:(\d+)/)[1]);
            //var AMPM = time.substr(-2);
            var AMPM = time.match(/\s(.*)$/)[1];
            if (AMPM == "PM" && hours < 12) hours = hours + 12;
            if (AMPM == "AM" && hours == 12) hours = hours - 12;
            var sHours = hours.toString();
            var sMinutes = minutes.toString();
            if (hours < 10) sHours = "0" + sHours;
            if (minutes < 10) sMinutes = "0" + sMinutes;
            return sHours+":"+sMinutes;
        }
        else
        {
            return '';
        }
    }

    function getGeoError(code)
    {
        switch(code)
        {
            case 0:
                return 'Some Unknown Error Occurred!';
                break;
            case 1:
                return 'User Permission Denied Or Location Unknown';
                break;
            case 2:
                return 'position unavailable (error response from location provider)';
                break;
            case 3:
                return 'Location Fetching Timed out!';
                break;
            default:
                return 'Try again after sometime';
        }
    }
    function showCustomLoader()
    {
        $('body').addClass('custom-loader-body');
        $('.custom-loader-overlay').css('top',$(window).scrollTop()).addClass('show');
    }

    function hideCustomLoader()
    {
        $('body').removeClass('custom-loader-body');
        $('.custom-loader-overlay').removeClass('show');
    }
</script>