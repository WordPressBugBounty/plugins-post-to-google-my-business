import {Calendar} from "@fullcalendar/core";
import timeGridPlugin from "@fullcalendar/timegrid";
import allLocales from '@fullcalendar/core/locales-all';
import * as $ from "jquery";
import {__, _x} from "@wordpress/i18n";



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
            tb_show(_x('Post info', 'Title header for the dialog that opens when you click a post in the calendar', 'post-to-google-my-business'), "#TB_inline?width=600&height=300&inlineId=pgmb-calendar-post-popup");
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
                    topicDashicon = '<svg class="pgmb-calendar-svg" width="20" height="20" viewBox="0 0 24 24" focusable="false"><path d="M23 12l-2.44-2.78.34-3.68-3.61-.82-1.89-3.18L12 3 8.6 1.54 6.71 4.72l-3.61.81.34 3.68L1 12l2.44 2.78-.34 3.69 3.61.82 1.89 3.18L12 21l3.4 1.46 1.89-3.18 3.61-.82-.34-3.68L23 12zm-4.51 2.11l.26 2.79-2.74.62-1.43 2.41L12 18.82l-2.58 1.11-1.43-2.41-2.74-.62.26-2.8L3.66 12l1.85-2.12-.26-2.78 2.74-.61 1.43-2.41L12 5.18l2.58-1.11 1.43 2.41 2.74.62-.26 2.79L20.34 12l-1.85 2.11z"></path><path d="M11 15h2v2h-2zm0-8h2v6h-2z"></path></svg>';
                    break;
                case "EVENT":
                    topicDashicon ='<svg class="pgmb-calendar-svg" width="20" height="20" viewBox="0 0 24 24" focusable="false"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"></path><path d="M14.5 13a2.5 2.5 0 0 0 0 5 2.5 2.5 0 0 0 0-5z"></path></svg>';
                    break;
                case "OFFER":
                    topicDashicon = '<svg class="pgmb-calendar-svg" width="20" height="20" viewBox="0 0 24 24" focusable="false"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58s1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41s-.23-1.06-.59-1.42zM13 20.01L4 11V4h7v-.01l9 9-7 7.02zM8 6.5C8 7.33 7.33 8 6.5 8S5 7.33 5 6.5 5.67 5 6.5 5 8 5.67 8 6.5z"></path></svg>';
                    break;
                case "PRODUCT":
                    topicDashicon = '<svg class="pgmb-calendar-svg" height="20" viewBox="0 0 24 24" width="20" focusable="false"><path d="M18,6h-2c0-2.21-1.79-4-4-4S8,3.79,8,6H6C4.9,6,4,6.9,4,8v12c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V8C20,6.9,19.1,6,18,6z M12,4c1.1,0,2,0.9,2,2h-4C10,4.9,10.9,4,12,4z M18,20H6V8h2v2c0,0.55,0.45,1,1,1s1-0.45,1-1V8h4v2c0,0.55,0.45,1,1,1s1-0.45,1-1V8 h2V20z"></path></svg>'
                    break;
                case "ALERT":
                    topicDashicon = '<span class="dashicons dashicons-sos"></span>'
                    break;
            }
            $(topicDashicon).prependTo(title);

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
