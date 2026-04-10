import MediaUploader from "./MediaUploader";

import * as $ from 'jquery';

//require("jquery-ui-timepicker-addon");
import "jquery-ui-timepicker-addon";
import "jquery-ui-timepicker-addon/src/jquery-ui-timepicker-addon.css";
import {addFilter, applyFilters} from "@wordpress/hooks";
import apiFetch from "@wordpress/api-fetch";
import {__, _x, sprintf} from "@wordpress/i18n";
import {addQueryArgs} from "@wordpress/url";
import {formatGoogleEventDate} from "../google-date-helper";

let PostEditor = function(ajax, ajax_prefix, default_fields, nonce, timepicker_disabled, loadingCallback, localize_vars, lock_to_post_id){
    let ajaxEnabled = ajax || false;
    const postEditorInstance = this;
    const counterFields = $('.pgmb-field-with-counter');

    const postFormContainer = $(".mbp-post-form-container");

    let fieldPrefix = "mbp_form_fields";

    let templateDefaultFields;
    let isEditingTemplate;

    this.mediaUploader = new MediaUploader($('.mediaupload_selector'));
    if(!ajaxEnabled){
        let staticImage = $('.mbp-post-attachment');
        if(staticImage.val()){
            this.mediaUploader.loadItem($('.mbp-attachment-type').val(), staticImage.val(),staticImage.val());
        }
    }


    function debounce(func, timeout = 300){
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }

    this.setFieldPrefix = function(prefix){
        this.mediaUploader.setFieldName(prefix);
        fieldPrefix = prefix;
    };

    let eventStartDate = $('#event_start_date');
    let eventEndDate = $('#event_end_date');

    if(!timepicker_disabled){
        $.timepicker.datetimeRange(
            eventStartDate,
            eventEndDate,
            {
                showOn: "button",
                //buttonImageOnly: true,
                buttonText: "",
                minInterval: (1000*60*60), // 1hr
                dateFormat : 'yy-mm-dd',
                timeFormat: 'HH:mm',
                minDate : 0,
                constrainInput: false,
                start: {}, // start picker options
                end: {} // end picker options
            }
        );
    }


    $('.mbp-validate-date').on("keyup change", debounce(function (event) {
        let closestDateDisplay = $(event.currentTarget).closest('td').find('.mbp-validated-date-display');
        if ($(event.currentTarget).val() === "") {
            //$('#event_start_date_validator').html('');
            $(closestDateDisplay).html('');
            return false;
        }
        const data = {
            'action': ajax_prefix + '_check_date',
            'mbp_post_nonce': nonce,
            'timestring': $(event.currentTarget).val()
        };
        $.post(ajaxurl, data, function (response) {
            if (response.success) {
                $(closestDateDisplay).html(response.data);
                return true;
            } else {
                $(closestDateDisplay).html('Invalid date');
                return false;
            }
        });
    }));

    /**
     * Switch tabs by providing a selector for a valid tab
     *
     * @param clicked Selector for the clicked tab
     */
    this.switch_tab = function(clicked){
        $('.nav-tab', postFormContainer).removeClass("nav-tab-active");
        $(clicked).addClass("nav-tab-active");
        $('.mbp-fields > tbody > tr').hide();
        $('.mbp-fields > tbody > tr.' + $(clicked).data('fields')).show();
        $('input.mbp-topic-type').val($(clicked).data("topic")).trigger('change');

    };

    /**
     * Hook switch tab function to tabs
     */
    $('.nav-tab', postFormContainer).click(function(event) {
        event.preventDefault();
        postEditorInstance.switch_tab(this);
    });

    /**
     * Open the advanced post settings
     */
    $('.mbp-toggle-advanced').click(function(event) {
        event.preventDefault();
        const advanced_settings = $(".mbp-advanced-post-settings");
        if(advanced_settings.is(":hidden")){
            localStorage.openAdvanced = JSON.stringify(true);
        }else{
            localStorage.openAdvanced = JSON.stringify(false);
        }
        advanced_settings.slideToggle("slow");
    });

    /**
     * Reload the state of the advanced post settings dialog
     */
    if(localStorage.openAdvanced && JSON.parse(localStorage.openAdvanced) === true){
        const advanced_settings = $(".mbp-advanced-post-settings");
        advanced_settings.show();
    }

    /**
     * Trigger change on the post text field when it is changed externally, to update the character counter
     */
    counterFields.change(function () {
        $(this).trigger("keyup");
    });

    /**
     * Update text and word counter for the text field
     */
    counterFields.keyup(function () {
        let counter = $(this).parents('tr').find('.mbp-character-count');
        let count = $(this).val().length;
        let words = $(this).val().split(' ').length - 1;
        counter.text(count);
        if(count > $(this).data('maxchars')){
            counter.css('color', 'red');
        }else{
            counter.css('color', 'inherit');
        }
        $('.mbp-word-count').text(words);
    });

    // /**
    //  * Keep track of the state of the button option
    //  * @type {boolean} Button options are opened
    //  */
    // let ButtonOptionsOpened = false;
    //
    // /**
    //  * Show/hide Call to Action settings when checking/unchecking the CTA checkbox
    //  */
    // $('#mbp_button').change(function() {
    //     if(this.checked) {
    //         $(".mbp-button-settings").fadeIn("slow");
    //         ButtonOptionsOpened = true;
    //     }else{
    //         $(".mbp-button-settings").fadeOut("slow");
    //         ButtonOptionsOpened = false;
    //     }
    // });



    /**
     * Hide the "alternative URL" field if the CTA is set to "CALL"
     */
    $('.mbp-button-type').change(function() {
        const alternativeURL = $(".mbp-button-url");
        if(!$(this).val() || $(this).val() === 'CALL'){
            alternativeURL.fadeOut("slow");
            return;
        }

        alternativeURL.fadeIn("slow");

    });

    this.recurseMultiDimensionalFields = function(name, value, depth){
        if(!depth){ depth = '[' + name + ']'; }
        if ($.isArray(value) || $.isPlainObject(value)) {

            $.each(value, function (key, checkboxVal) {
                let newDepth = '';
                if($.isArray(value)){
                    newDepth = depth + '[]';
                }else{
                    newDepth = depth + '[' + key + ']';
                }
                postEditorInstance.recurseMultiDimensionalFields(key, checkboxVal, newDepth);
            });
            return;
        }

        if(value === "1" || value === "on"){
            value = true;
        }
        if(typeof value === 'boolean'){
            $('[name="' + fieldPrefix + depth + '"]').prop('checked', value);
        }else{
            $('[name^="' + fieldPrefix + depth + '"][value="' + value + '"]').prop('checked', true);
        }

    }

    /**
     * Repopulate the form fields from data object
     *
     * @param form_fields - object containing field names and values
     */
    this.loadFormFields = function(form_fields){
        $.each(form_fields, function(name, value){
            //let field = $('[name="' + fieldPrefix + '[' + name + ']"], [name="' + fieldPrefix + '[' + name + '][]"]');
            let field = $('[name^="' + fieldPrefix + '[' + name + ']"]');

            field = applyFilters('pgmb-load-posteditor-field', field, name, value);
            if(!field){
                return;
            }

            if(field.is(':checkbox') || field.is(':radio')) {
                //Uncheck everything first
                field.prop('checked', false);

                postEditorInstance.recurseMultiDimensionalFields(name, value);

            }else if(field.is('select') && !value) {
                field.val("");
            }else{
                field.val(value);
            }
            field.change();
        });

        if(form_fields.mbp_post_attachment && form_fields.mbp_attachment_type){
            //mediaupload.loadItem(form_fields.mbp_attachment_type, form_fields.mbp_post_attachment, form_fields.mbp_post_attachment);
            this.mediaUploader.loadItem(form_fields.mbp_attachment_type, form_fields.mbp_post_attachment, form_fields.mbp_post_attachment);
        }

        const tab = $('a[data-topic="'+ form_fields.mbp_topic_type +'"]');
        this.switch_tab(tab);
    };

    this.resetForm = function(){
        this.mediaUploader.clearItems();
    };

    this.loadDefaultFormFields = function (){
        this.loadFormFields(default_fields);
    };

    this.templateDefaultFields = function(fields){
        templateDefaultFields = fields;
    }

    this.setEditingTemplate = function(editing_template){
        isEditingTemplate = editing_template;
    }

    /**
     * Trigger dynamic changes on the form when the form is loaded statically
     */
    if(!ajaxEnabled){
        //trigger changes when the form is not loaded through ajax
        $('.mbp-validate-date').trigger("change");
        $('#mbp_button').trigger("change");
        $('.mbp-button-type').trigger("change");
        $(counterFields).trigger("keyup");

        //Switch to the appropriate tab
        let topicType = $("input.mbp-topic-type").val();
        let tab = $('a[data-topic="'+ topicType +'"]');
        this.switch_tab(tab);
    }

    $('#pgmb-restore-default-template').on('click', function(event){
        event.preventDefault();
        const restore = confirm(__('Are you sure you want to restore the default template?', 'post-to-google-my-business'));
        if(restore){
            postEditorInstance.resetForm();
            if(!isEditingTemplate){
                postEditorInstance.loadDefaultFormFields();
            }else if(isEditingTemplate && templateDefaultFields){
                postEditorInstance.loadFormFields(templateDefaultFields);
            }
        }
    });

    /* ----------------------------------
     * Variable selector
     * ---------------------------------- */



    function recurseVariableTree(data, parent, path = '' ){

        Object.entries(data).forEach(([key, node]) => {
            const li = $('<li></li>');
            const fullPath = path ? `${path}.${key}` : key;

            if(node.children){
                /* Root contexts don't have the .type parameter */
                const header = $(`<span class="pgmb-variable-group">${node.type ? key : node.label}</span>`);
                li.append(header);

                if(node.is_premium && !localize_vars.can_use_premium_code){
                    const premium = $(`<a href="${localize_vars.upgrade_url}" target="_blank"><span class="pgmb-premium-badge">${__('Premium', 'post-to-google-my-business')}</span></a>`);
                    li.append(premium);
                }

                const ul = $('<ul class="pgmb-variable-tree-nested"></ul>');
                recurseVariableTree(node.children, ul, fullPath);
                li.append(ul);
            }else{
                const row = $(`<span class="pgmb-variable-row">${key}</span>`);
                row.data({
                    token: fullPath,
                    label: node.label,
                    type: node.type
                });
                li.append(row);
            }

            parent.append(li);

        });

    }

    $(document).on('click', '.pgmb-variable-group', function() {
        $(this).toggleClass('pgmb-variable-group-open');
        $(this).parent().children('.pgmb-variable-tree-nested').toggle();
    });

    /* ----------------------------------
     * Variable click
     * ---------------------------------- */

    $(document).on('click', '.pgmb-variable-row', function () {

        const token = $(this).data('token');
        const label = $(this).data('label');
        const type = $(this).data('type');



        const container = $('#pgmb-variable-details').empty();

        const details = $(`
            <strong>${token}</strong> (${type})<br />
            <p>${label || ''}</p>
        `);

        container.append(details);

        const insertButton = $(`<button type="button" class="button button-primary">${__('Insert', 'post-to-google-my-business')}</button>`);
        const copyButton = $(`<button type="button" class="button button-secondary">${__('Copy to clipboard', 'post-to-google-my-business')}</button>`);

        function buildToken() {
            switch (type) {

                case 'boolean':
                    return `{{#${token}}}\n\n{{/${token}}}`;

                case 'array':
                case 'list':
                    return `{{#${token}}}\n{{.}}\n{{/${token}}}`;

                case 'object':
                    return `{{#${token}}}\n{{.}}\n{{/${token}}}`;

                default:
                    return `{{${token}}}`;
            }
        }

        insertButton.on('click', function () {
            const current_topic_type = $('input.mbp-topic-type').val();
            if(current_topic_type === 'PRODUCT'){
                insertIntoTextarea('#product_details', buildToken(type));
                return;
            }
            insertIntoTextarea('#post_text', buildToken(type));
        });

        copyButton.on('click', function () {
            navigator.clipboard.writeText(buildToken(type));
        });

        container.append(insertButton, copyButton);

    });


    /* ----------------------------------
     * Selected preview post
     * ---------------------------------- */

    function setSelectedPost(data) {

        const imageSrc = data.thumbnail ? data.thumbnail : localize_vars.placeholders.image;

        return $(`
        <div class="pgmb-selected-post">
            <div class="pgmb-image-container">
                <img src="${imageSrc}" alt="${data.title}" />
            </div>
        
            <strong>${data.title}</strong>
            <p>${data.excerpt}</p>
        </div>
    `);

    }


    /* ----------------------------------
     * Insert into textarea
     * ---------------------------------- */

    function insertIntoTextarea(selector, text) {

        const textarea = document.querySelector(selector);

        if (!textarea) return;

        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;

        textarea.value =
            textarea.value.substring(0, start) +
            text +
            textarea.value.substring(end);

        textarea.selectionStart = textarea.selectionEnd = start + text.length;
        textarea.focus();
    }

    /* ----------------------------------
    * Open variable panel
    * ---------------------------------- */

    let current_post_id;
    let warn = false;

    function fetchVariables(post_type, offset = 0){
        const selectedPostContainer = $('#pgmb-selected-post');
        const spinner = $('<span class="spinner is-active"></span>');
        $('#pgmb-variable-tree-container').empty().append(spinner);
        apiFetch({
            path: addQueryArgs('/pgmb/v1/variables', {
                post_id: lock_to_post_id || null,
                post_type,
                offset
            })
        }).then((data) => {
            const tree = $('#pgmb-variable-tree-container');
            tree.empty();
            recurseVariableTree(data.descriptions, tree);

            if(lock_to_post_id){
                $('#pgmb-post-browser').hide();
                if(!warn){
                    const unavailable_data_notice = $(`<div class="notice notice-info alt"><p>${__('Dynamic data from the WordPress post currently being edited might be outdated or not yet available. Save/Update the post to get current data.', 'post-to-google-my-business')}</p></div>`);
                    $('.pgmb-panel-content').prepend(unavailable_data_notice);
                    warn = true;
                }
            }

            selectedPostContainer
                .empty()
                .append(setSelectedPost(data.for_post));

            current_post_id = data.for_post.id;
            loadPreview();

        }).catch((error) => {
            const errorMessage = error.message;
            selectedPostContainer
                .empty()
                .append(errorMessage);
        });
    }

    /* ----------------------------------
     * Open variable panel
     * ---------------------------------- */

    $('.pgmb-insert-variable-button').on('click', function (event) {

        event.preventDefault();

        const panel = $('#pgmb-variable-panel');

        panel.toggleClass('open');

        if (!panel.hasClass('open')) return;

        fetchVariables('post');

    });


    /* ----------------------------------
     * Close panel
     * ---------------------------------- */

    $('.pgmb-variable-panel-close-button').on('click', function (event) {

        event.preventDefault();
        $('#pgmb-variable-panel').removeClass('open');

    });

    /* ----------------------------------
     * Post type selector
     * ---------------------------------- */
    let offset = 0;
    const prevButton = $('#pgmb-selected-post-controls .button-previous');
    const nextButton = $('#pgmb-selected-post-controls .button-next');
    const postTypeSelector = $('#pgmb-vp-post-type');
    postTypeSelector.on('change', function(event){
        offset = 0;
        fetchVariables($(this).val(), offset);
        nextButton.attr('disabled', true);
    });

    /* ----------------------------------
     * Post controls
     * ---------------------------------- */

    prevButton.on('click', function(event){
        offset--;
       event.preventDefault();
       fetchVariables(postTypeSelector.val(), offset);
       if(offset < 0){
           nextButton.attr('disabled', false);
       }
    });

    nextButton.on('click', function(event){
        offset++;
        event.preventDefault();
        fetchVariables(postTypeSelector.val(), offset);
        if(offset >= 0){
            nextButton.attr('disabled', true);
        }
    });

    $('.pgmb-var-tab:first').fadeIn();

    $('#pgmb-variable-panel .nav-tab-wrapper a').click(function(event){
        event.preventDefault();
        $('#pgmb-variable-panel .nav-tab-wrapper a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active').blur();
        const tab = $(this).attr('href');
        $('.pgmb-var-tab').hide();
        $(tab).fadeIn();
    });

    $('#pgmb-refresh-preview').click(function(event){
        event.preventDefault();
        loadPreview();
    });

    function loadPreview(){
        const spinner = $('<span class="spinner is-active"></span>');
        $('.pgmb-post-preview-container').empty().append(spinner);
        apiFetch({
            path: addQueryArgs('/pgmb/v1/preview', {
                post_id: current_post_id,
                field_prefix: fieldPrefix,
                form_fields: $('fieldset#mbp-post-data').serialize()
            })
        }).then((data) => {
            const preview = generatePreview(data);
            $('.pgmb-post-preview-container').empty().append(preview);
        }).catch((error) => {
            $('.pgmb-post-preview-container').empty().append(error.message);
        });
    }

    function truncateWords(str, maxLength = 120) {
        if (str.length <= maxLength) return str;

        const shortened = str.slice(0, maxLength);
        return shortened.slice(0, shortened.lastIndexOf(' ')) + '…';
    }



    function generatePreview(data) {
        const imageSrc = data.media?.[0]?.sourceUrl ?? '';

        const imageMarkup = imageSrc
            ? `<div class="pgmb-post-preview-image-container">
                    <div role="img" style="background-image: url(${imageSrc});"></div>
                </div>`
            : '';


        const offerLabelMarkup = data.topicType === 'OFFER'
            ? `<span class="pgmb-post-preview-offer-label-wrapper" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" focusable="false" class="pgmb-post-preview-offer-label"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"></path></svg></span>`
            : '';

        const ctaText = (actionType) => {
            switch(actionType){
                case "SHOP":
                    return __('Buy', 'post-to-google-my-business');
                case "BOOK":
                    return __('Book', 'post-to-google-my-business');
                case "ORDER":
                    return __('Order online', 'post-to-google-my-business');
                case "LEARN_MORE":
                    return __('Learn more', 'post-to-google-my-business');
                case "SIGN_UP":
                    return __('Sign up', 'post-to-google-my-business');
                case "CALL":
                    return __('Call Now', 'post-to-google-my-business');
                default:
                    return __('More information', 'post-to-google-my-business');
            }
        }

        const ctaMarkup = !!data.callToAction
            ? `<a href="${data.callToAction.url}" target="_blank" class="pgmb-post-preview-cta">${ctaText(data.callToAction.actionType)}</a>`
            : '';

        const dateText = formatGoogleEventDate(data);

        const eventMarkup = !!data.event
            ? `<div class="pgmb-post-preview-event-title-wrapper"> <!-- nvO4I -->
                   <div class="pgmb-post-preview-event-title"> <!-- zatzn -->
                        ${data.event.title}
                   </div>
                   ${offerLabelMarkup}
                </div>
                ${dateText ? `
                <div class="pgmb-post-preview-event-date">
                    ${dateText}
                </div>` : ''}
            `
            : '';

        return $(`
            <div class="pgmb-post-preview ${imageSrc ? 'pgmb-post-preview-has-image' : ''}">
                ${imageMarkup}
                <!-- DLAM2 -->
                <div class="pgmb-post-preview-content-container">
                    <!-- iT7n -->
                    <div class="pgmb-post-preview-content-text-wrapper ${!imageSrc ? 'pgmb-post-preview-content-no-pic' : ''}">
                        ${eventMarkup}
                        <!-- gdQxwd -->
                        <div class="pgmb-post-preview-content-text">${truncateWords(data.summary)}</div>
                    </div>
                    <!-- Ufkx2c -->
                    <div class="pgmb-post-preview-content-date">${_x('Just now', 'Sample publishing date in the post preview', 'post-to-google-my-business')}</div>
                </div>
                ${ctaMarkup}
            </div>
        `);

    }

};

export default PostEditor;
