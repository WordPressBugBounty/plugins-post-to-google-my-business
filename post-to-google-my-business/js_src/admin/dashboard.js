import {Calendar} from "@fullcalendar/core";
import timeGridPlugin from "@fullcalendar/timegrid";
import allLocales from '@fullcalendar/core/locales-all';
import * as $ from "jquery";



const { nonce, calendar_timezone, calendar_nonce, locale, delete_nonce } = pgmb_dashboard_data;


let eventToDelete;

document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('pgmb-calendar');

    let adjustedLocale = locale.toLowerCase().replace('_', '-');
    let availableLocales = [];
    allLocales.forEach((element) => {
        availableLocales.push(element.code);
    });

    let calendar_locale;

    if(availableLocales.includes(adjustedLocale)){
        calendar_locale = adjustedLocale;
    }

    if(!calendar_locale){
        adjustedLocale = adjustedLocale.split('-')[0];
        if(availableLocales.includes(adjustedLocale)){
            calendar_locale = adjustedLocale;
        }
    }


    let calendar = new Calendar(calendarEl, {
        locales: allLocales,
        locale: calendar_locale ? calendar_locale : 'en',
        plugins: [ timeGridPlugin  ],
        timeZone: calendar_timezone,
        initialView: 'timeGridWeek',
        allDaySlot: false,
        height: "auto",
        events: {
            url: ajaxurl,
            method: 'POST',
            extraParams: {
                nonce: calendar_nonce,
                action: 'mbp_get_timegrid_feed'
            },
            error: function(){
                //handle error
            }
        },
        loading: function(isLoading){
            if(isLoading){
                $('#pgmb-calender-loading').show();
            }else{
                $('#pgmb-calender-loading').hide();
            }
        },
        eventClick: function(info){
            info.jsEvent.preventDefault();
            let post_id = info.event.extendedProps.post_id;
            tb_show("Post info", "#TB_inline?width=600&height=300&inlineId=pgmb-calendar-post-popup");
            eventToDelete = info.event;
            const container = $('#pgmb-calendar-post-popup-inner');
            container.html('<span class="spinner is-active"></span>');
            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: {
                    nonce: calendar_nonce,
                    post_id: post_id,
                    action: 'pgmb_calendar_post_data'
                },
                success: function (response) {
                    container.html(response.data.post);
                }
            });
        },
        eventDidMount: function (info) {
            let title = $(info.el).find('.fc-event-title');
            let topicDashicon;
            switch(info.event.extendedProps.topictype){
                case "STANDARD":
                    topicDashicon = 'dashicons-megaphone';
                    break;
                case "EVENT":
                    topicDashicon ='dashicons-calendar';
                    break;
                case "OFFER":
                    topicDashicon = 'dashicons-tag';
                    break;
                case "PRODUCT":
                    topicDashicon = 'dashicons-cart'
                    break;
                case "ALERT":
                    topicDashicon = 'dashicons-sos'
                    break;
            }
            $("<span class=\"dashicons " + topicDashicon + "\"></span> &nbsp;").prependTo(title);

            if (info.event.extendedProps.live && !info.event.extendedProps.hasError) {
                $("<span class=\"dashicons dashicons-admin-site\"></span> &nbsp;").prependTo(title);
            }

            if (info.event.extendedProps.hasError) {
                $("<span class=\"dashicons dashicons-warning\"></span> &nbsp;").prependTo(title);
            }

            if (info.event.extendedProps.repost) {
                $("<span class=\"dashicons dashicons-controls-repeat\"></span> &nbsp;").prependTo(title);
            }

        }
    });


    $(document).on("click", '.pgmb-delete-post', function(event) {
        let post_id = parseInt($(this).data('post_id'));
        const data = {
            'action': 'mbp_delete_post',
            'mbp_post_id': post_id,
            'mbp_post_nonce': delete_nonce
        };
        tb_remove();
        if(eventToDelete){
            eventToDelete.remove();
        }
        eventToDelete = null;
        $.post(ajaxurl, data);
    });

    $(".pgmb-message .mbp-notice-dismiss").click(function(event){
        event.preventDefault();
        let theNotification = $(this).closest('.pgmb-message');

        let data = {
            'action': 'mbp_delete_notification',
            'nonce': nonce,
            'identifier': theNotification.data('identifier'),
            'section': theNotification.data('section'),
            'ignore': $(this).data('ignore')
        };
        let notificationsContainer = $(this).closest('.pgmb-notifications-container');
        let notificationCounter = $('.mbp-notification-count', notificationsContainer);

        theNotification.fadeOut();

        let notificationCount = parseInt(notificationCounter.text()) - 1;

        notificationCounter.text(notificationCount);

        let isMainNotification = theNotification.hasClass("pgmb-notification");

        let pluginMenu = $('li.toplevel_page_post_to_google_my_business');
        if(isMainNotification){
            $('.update-count', pluginMenu).text(notificationCount);
        }

        if(notificationCount <= 0){
            if(isMainNotification) {
                $('.update-plugins', pluginMenu).remove();
            }
            notificationsContainer.fadeOut('slow');
        }
        $.post(ajaxurl, data);
    });

    calendar.render();
});
