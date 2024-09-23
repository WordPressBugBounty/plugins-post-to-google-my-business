/*
Based on WP List table ajax by debba

https://github.com/debba/wp-list-table-ajax-sample
 */

import * as $ from 'jquery';


let AjaxListTable = function(container, nonce, ajax_prefix){

    const instance = this;

    let parent_id;

    /** added method display
     * for getting first sets of data
     **/

    this.set_parent_id = function(id){
        parent_id = id;
    }

    this.display = function(){
        $(container).html('<span class="spinner is-active"></span>');
        $.ajax({

            url: ajaxurl,
            dataType: 'json',
            data: {
                ajax_list_table_nonce: nonce.val(),
                parent_id: parent_id,
                action: ajax_prefix + '_list_display'
            },
            success: function (response) {
                $(container).html(response.display);

                $('.button.action', container).on('click', function(e){
                    e.preventDefault();
                    instance.bulk_action(this);
                });

                $("tbody", container).on("click", ".toggle-row", function(e) {
                    e.preventDefault();
                    $(this).closest("tr").toggleClass("is-expanded")
                });

                instance.init();
            }
        });

    };

    this.init = function(){

        var timer;
        var delay = 500;

        $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a', container).on('click', function (e) {
            e.preventDefault();
            var query = this.search.substring(1);

            var data = {
                paged: instance.__query( query, 'paged' ) || '1',
                order: instance.__query( query, 'order' ) || 'desc',
                orderby: instance.__query( query, 'orderby' ) || 'date_created'
            };
            instance.update(data);
        });

        $('input[name=paged]',container).on('keyup', function (e) {

            if (13 == e.which)
                e.preventDefault();

            var data = {
                paged: parseInt($('input[name=paged]', container).val()) || '1',
                order: $('input[name=order]', container).val() || 'desc',
                orderby: $('input[name=orderby]', container).val() || 'date_created'
            };

            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                instance.update(data);
            }, delay);
        });

        $('#email-sent-list').on('submit', function(e){

            e.preventDefault();

        });



    };

    /** AJAX call
     *
     * Send the call and replace table parts with updated version!
     *
     * @param    object    data The data to pass through AJAX
     */
    this.update = function(data){

        $.ajax({

            url: ajaxurl,
            data: $.extend(
                {
                    ajax_list_table_nonce: nonce.val(),
                    parent_id: parent_id,
                    action: ajax_prefix + '_list_update',
                },
                data
            ),
            success: function (response) {

                var response = $.parseJSON(response);

                if (response.rows.length)
                    $('tbody', container).html(response.rows);
                if (response.column_headers.length)
                    $('thead tr, tfoot tr', container).html(response.column_headers);
                if (response.pagination.bottom.length)
                    $('.tablenav.top .tablenav-pages', container).html($(response.pagination.top).html());
                if (response.pagination.top.length)
                    $('.tablenav.bottom-pgmb-subposts .tablenav-pages', container).html($(response.pagination.bottom).html());

                instance.init();
            }
        });
    };

    this.bulk_action = function(triggerbutton){
        let bulk_action = $(triggerbutton).siblings('select').val();
        let post_ids = [];

        $(".check-column input:checkbox:checked", container).each(function(){
            //We dont want to send the value of the "select all" checkboxes
            if($(this).val() === 'on'){
                return;
            }
            post_ids.push($(this).val());
        });

        $.ajax({

            url: ajaxurl,
            dataType: 'json',
            data: {
                ajax_list_table_nonce: nonce.val(),
                parent_id: parent_id,
                action: ajax_prefix + '_bulk_action',
                bulk_action: bulk_action,
                post_ids: post_ids
            },
            success: function (response) {
                if(bulk_action === "refresh_status"){
                    $(container).html('Refreshing post statuses... <span class="spinner is-active"></span>');
                    instance.poll_status();
                }else{
                    instance.display();
                }
            }
        });

    };

    this.poll_status = function(){
        $.ajax({
            url: ajaxurl,
            dataType: 'json',
            data: {
                ajax_list_table_nonce: nonce.val(),
                parent_id: parent_id,
                action: ajax_prefix + '_check_status',
            },
            success: function (response) {
                if(!response.busy){
                    instance.display();
                }else{
                    setTimeout(instance.poll_status, 5000);
                }
            }
        });
    }

    /**
     * Filter the URL Query to extract variables
     *
     * @see http://css-tricks.com/snippets/javascript/get-url-variables/
     *
     * @param    string    query The URL query part containing the variables
     * @param    string    variable Name of the variable we want to get
     *
     * @return   string|boolean The variable value if available, false else.
     */
    this.__query = function (query, variable) {

        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if (pair[0] == variable)
                return pair[1];
        }
        return false;
    };
}


// list.display();
export default AjaxListTable;
