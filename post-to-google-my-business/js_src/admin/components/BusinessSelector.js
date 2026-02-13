/**
 * @property {string} ajaxurl URL for ajax request set by WordPress
 */

import * as $ from 'jquery';
import {__, sprintf} from "@wordpress/i18n";

let accountsLoading = false;
let accountCache = {};
let groupCache = {};
let groupsLoading = false;
let locationsLoading = false;
let locationCache = {};

let isLoading = false;

let refreshLockout = 5;

/**
 * Class to make the business selector work
 *
 * @param container Parent container selector
 * @param {string} ajax_prefix Prefix for ajax calls made to WordPress
 * @param es6container
 * @param load_callback
 * @param multiple
 * @param account_controls
 * @param nonce
 * @constructor
 */
let BusinessSelector = function(container, ajax_prefix, es6container, load_callback, multiple, account_controls, nonce){
    let instance = this;
    let fieldContainer = $('.mbp-business-selector', container);
    let locationBlockedInfo = $('.mbp-location-blocked-info', container);
    let refreshApiCacheButton = $('.refresh-api-cache', container);
    let filterTextareaControl = $('.mbp-filter-locations', container);
    let businessSelectorSelectedLocation = $('input:checked', fieldContainer);

    let selectedLocations;

    let loadSuccess;

    const businessSelector = es6container.querySelector('.mbp-business-selector');

    const loadListeners = [];

    if(typeof load_callback === 'function'){
        loadListeners.push(load_callback);
    }

    this.isLoading = function(){
        return isLoading;
    }

    this.registerLoadListener = function(listener){
        loadListeners.push(listener);
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    this.AjaxCall = async function(nonce, action, data){
        const formData  = new FormData();

        formData.append('action', ajax_prefix + "_" + action);
        formData.append('nonce', nonce);
        if(data){
            formData.append('data', JSON.stringify(data));
        }

        return await fetch(ajaxurl, {
            method: 'POST',
            body: formData,
        });
    }

    function getSpinner(){
        const spinner  = document.createElement('span');
        spinner.className = 'spinner is-active';
        spinner.style.float = 'none';
        return spinner;
    }

    this.populate = async function (purgeCache = false){
        loadListeners.forEach(listener => listener(true));
        isLoading = true;
        // refreshApiCacheButton.add(filterTextareaControl).attr('disabled', true);


        while(businessSelector.firstChild) {
            businessSelector.removeChild(businessSelector.firstChild);
        }

        const table = document.createElement('table');

        const spinner = getSpinner();
        businessSelector.appendChild(spinner);
        businessSelector.appendChild(table);

        try {
            await this.getAccounts(table, purgeCache);
            loadSuccess = true;
        }catch(e){
            /* translators: %s is error message */
            const errorRow = table.appendChild(instance.errorRow(sprintf(__('Failed to load locations: %s', 'post-to-google-my-business'), e.message)));
            errorRow.scrollIntoView();
            accountsLoading = groupsLoading = locationsLoading = false;
            loadSuccess = false;

            //Add a hidden field so if the user submits we can check if the field data is invalid and prevent it from being saved
            const errorField = document.createElement('input');
            errorField.name = businessSelector.dataset.field_name + "[load_success]";
            errorField.type = "hidden";
            errorField.value = "no";
            businessSelector.appendChild(errorField);
        }

        spinner.remove();
        isLoading = false;
        filterTextareaControl.attr('disabled', false);

        loadListeners.forEach(listener => listener(false, loadSuccess));
        return loadSuccess;
    }

    this.getAccounts = async function(container, purgeCache){
        let accounts;
        //lockout and caching so two components on the same page won't cause double requests
        while(accountsLoading){
            await sleep(100);
        }
        if(accountCache.accounts && !purgeCache){
            accounts = accountCache.accounts;
        }else{
            accountsLoading = true;
            groupCache = accountCache = locationCache = {};
            const accountsResponse = await instance.AjaxCall(nonce, 'get_accounts',
                {
                    refresh: purgeCache,
                });
            accounts = accountCache.accounts = await accountsResponse.json();
            accountsLoading = false;
        }

        if(accounts && accounts.success){
            for(const account_id in accounts.data){
                const tbody = document.createElement('tbody');
                const tr = document.createElement('tr');
                const th = document.createElement('th');
                th.colSpan = 2;
                th.textContent = accounts.data[account_id].email;
                if(account_controls && accounts.data[account_id].controls){
                    th.innerHTML = th.innerHTML + " " + accounts.data[account_id].controls;
                }

                tbody.dataset.account_id = account_id;

                tr.appendChild(th);
                tbody.appendChild(tr);

                container.appendChild(tbody);
                // const loaderTR = document.createElement('tr');
                // const loaderTD = document.createElement('td');
                // loaderTR.appendChild(loaderTD);
                // loaderTD.appendChild(getSpinner());
                // container.appendChild(loaderTR);
                await instance.getGroups(account_id, tbody);
                // loaderTR.remove();
            }
        }else{
            if(accounts && accounts.loading){
                container.appendChild(instance.noticeRow(__('The background process is currently synchronizing your locations, please wait.', 'post-to-google-my-business')));
                await sleep(5000);

                accountCache.accounts = null;
                await instance.populate();

            }else if(accounts && typeof accounts.data === "string"){
                throw new Error(accounts.data);
            }else{
                console.log(accounts);
                throw new Error(__('Unknown error occurred trying to load accounts', 'post-to-google-my-business'));
            }
        }


    }

    this.getGroups = async function(accountID, accountElement, offset = 0){
        let groups;

            while(groupsLoading){
                await new Promise((resolve) => setTimeout(resolve, 100));
            }
            let groupcachkey = accountID + offset;
            if(groupCache[groupcachkey]){
                groups = groupCache[groupcachkey];
            }else{
                groupsLoading = true;
                const groupsResponse = await instance.AjaxCall(nonce, 'get_groups', {
                   account_id: accountID,
                   offset,
                });

                groups = groupCache[groupcachkey] = await groupsResponse.json();
                groupsLoading = false;
            }


        if(groups && groups.success && groups.data.accounts){
            for (const group of groups.data.accounts){

                const groupTR = document.createElement('tr');
                const groupTD = document.createElement('td');
                groupTD.colSpan = 2;
                const groupLabel = document.createElement('strong');
                groupLabel.textContent = group.accountName;

                groupTD.appendChild(groupLabel);
                groupTR.appendChild(groupTD);
                accountElement.appendChild(groupTR);

                await instance.getLocations(accountID, group.name, accountElement, 0);
            }
        }else if(groups && groups.success && typeof groups.data === "string") {
            //e.g. when there are no groups in the account
            accountElement.appendChild(instance.noticeRow(groups.data));
        }else if(groups && !groups.success && typeof groups.data === "string"){
            throw new Error(groups.data);
        }else{
            console.log(groups);
            throw new Error(__('An unknown error occurred trying to load the groups', 'post-to-google-my-business'));
        }

        if(groups.data.count === 100){
            return await instance.getGroups(accountID, accountElement, offset + 100);
        }
    }

    this.getLocations = async function(account_id, group_id, groupElement, offset = 0){
        let locations;

            while(locationsLoading){
                await new Promise((resolve) => setTimeout(resolve, 100));
            }
            let cachekey = group_id + offset;
            if(locationCache[cachekey]){
                locations = locationCache[cachekey];
            }else{
                locationsLoading = true;

                const locationsResponse = await instance.AjaxCall(nonce, 'get_group_locations', {
                    group_id: group_id,
                    account_id: account_id,
                    offset,
                });
                locations = locationCache[cachekey] = await locationsResponse.json();
                locationsLoading = false;
            }


        if(locations && locations.success && locations.data.rows) {
            for (const row of locations.data.rows) {
                const checkboxContainer = document.createElement('td');

                const normalizedLocationName = group_id + "/" + row.location_name;

                const checked = selectedLocations[account_id] && ((typeof selectedLocations[account_id] === "object" && Object.values(selectedLocations[account_id]).includes(normalizedLocationName)) || normalizedLocationName === selectedLocations[account_id]);

                const checkboxInput = instance.getCheckboxInput(account_id, group_id, row.location_name, checked);

                checkboxContainer.appendChild(checkboxInput);

                const locationContainer = document.createElement('tr');
                locationContainer.className = 'mbp-business-item';

                locationContainer.appendChild(checkboxContainer);

                checkboxContainer.className = 'mbp-checkbox-container';

                locationContainer.insertAdjacentHTML('beforeend', row.column);
                groupElement.appendChild(locationContainer);
            }
        }else if(locations && locations.success && typeof locations.data === 'string'){
            groupElement.appendChild(instance.noticeRow(locations.data));
        }else if(locations && !locations.success && typeof locations.data === 'string'){
            throw new Error(locations.data);
        }else{
            console.log(locations);
            throw new Error(__('Failed to load locations, unknown error', 'post-to-google-my-business'));
        }


        if(locations.data.count === 500){
            return await instance.getLocations(account_id, group_id, groupElement, offset + 500, refresh);
        }

    }

    this.getCheckboxInput = function(account_key, account_name, location_name, checked, disabled){
        const checkboxElement = document.createElement('input');
        checkboxElement.type = multiple ? 'checkbox' : 'radio';
        checkboxElement.name = businessSelector.dataset.field_name + "[" + account_key + "]" + (multiple ? "[]" : "");
        checkboxElement.id = 'cb-'+ businessSelector.dataset.field_name + "-" + location_name.replace('/', '-');
        checkboxElement.value = account_name + "/" + location_name;
        checkboxElement.disabled = disabled;
        checkboxElement.checked = checked;
        checkboxElement.onchange = () => {
            if(multiple){
                if(typeof selectedLocations[account_key] !== 'object' || !Array.isArray(selectedLocations[account_key])){
                    selectedLocations[account_key] = Array.of(selectedLocations[account_key]);
                }
                selectedLocations[account_key].push(checkboxElement.value);
            }else{
                selectedLocations = {};
                selectedLocations[account_key] = checkboxElement.value;
            }
        }
        return checkboxElement;
    }

    this.setSelection = function (selection) {
        selectedLocations = selection;

        const inputtype = multiple ? "checkbox" : "radio";

        const checkboxes  = businessSelector.querySelectorAll(`input[type="${inputtype}"]`);
        for(const checkbox of checkboxes){
            checkbox.checked = false;
        }
        for (const account_id in selectedLocations){
            const data = selectedLocations[account_id];
            if(typeof data === "object"){
                data.forEach((location) => {
                    const checkbox = businessSelector.querySelector(`input[type="${inputtype}"][value="${location}"]`);
                    if(checkbox){
                        checkbox.checked = true;
                    }
                });
            }else{
                const checkbox = businessSelector.querySelector(`input[type="${inputtype}"][value="${data}"]`);
                if(checkbox){
                    checkbox.checked = true;
                }
            }
        }

        instance.populate().then();

    }

    this.noticeRow = function(message){
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 2;
        td.textContent = message;
        tr.appendChild(td);
        return tr;
    }

    this.errorRow = function(message){
        const noticeRow = this.noticeRow(message);
        noticeRow.className = 'pgmb-business-selector-error-row';
        return noticeRow;
    }

    /**
     * Case insentive filter function for locations
     */
    $.extend($.expr[":"], {
        "containsi": function(elem, i, match, array) {
            return (elem.textContent || elem.innerText || "").toLowerCase()
                .indexOf((match[3] || "").toLowerCase()) >= 0;
        }
    });

    /**
     * Filter the location list and keep only items that match the text
     */
    $(".mbp-filter-locations", container).keyup(function(){
        let search = $(this).val();

        $( ".mbp-business-selector tr.mbp-business-item", container).hide()
        .filter(":containsi(" + search + ")")
        .show();
    });

    /**
     * Hook function to select all locations to the appropriate button
     */
    $(".mbp-select-all-locations", container).click(function(event){
        event.preventDefault();
        $(".mbp-checkbox-container input:checkbox:visible", container).prop("checked", true);
    });

    /**
     * Hook function to select no locations to its' button
     */
    $(".mbp-select-no-locations", container).click(function(event){
        event.preventDefault();
        $(".mbp-checkbox-container input:checkbox:visible", container).prop("checked", false);
    });

    /**
     *
     * @param accountbody
     * @param revoke Whether to revoke the access tokens
     */
    this.deleteAccount = function(accountbody){
        let data = {
            'action': ajax_prefix + '_delete_account',
            'account_id': accountbody.data("account_id")
        };
        accountbody.remove();
        $.post(ajaxurl, data);
    };

    /**
     * Hook function to delete account buttons
     */
    $(container).on('click', '.mbp-disconnect-account', function(event){
        event.preventDefault();
        let shouldDelete = confirm(__('Disconnect the Google account from this website?', 'post-to-google-my-business'));
        if(!shouldDelete){
            return;
        }
        const accountbody = $(this).closest("tbody");
        instance.deleteAccount(accountbody);
    });


    let currentAccount;

    $(container).on('click', '.mbp-set-cookie-control', function(event){
        $("#pgmb-cookie-fieldset input", container).val('');
        const accountbody = $(this).closest("tbody");
        currentAccount = accountbody.data("account_id");
        tb_show(__('Set account cookies', 'post-to-google-my-business'), "#TB_inline?width=600&height=300&inlineId=mbp-set-cookies-dialog");
    });

    let saveButton = $("#mbp-set-cookies-dialog-container button", container);
    saveButton.click(function(event){
        saveButton.attr('disabled', true);
        let cookie_data = $("#pgmb-cookie-fieldset").serialize();
        let data = {
            'action': ajax_prefix + '_save_account_cookies',
            'cookie_data': cookie_data,
            'account_id': currentAccount
        };
        $.post(ajaxurl, data, function(response){
            saveButton.attr('disabled', false);
            if(!response.success){
                $('#mbp-cookie-error').show().html(response.data);
            }else{
                tb_remove();
                instance.refreshBusinesses(true, instance.getBusinessSelectorSelection());
            }
        });
    });


    /**
     * Hook function to toggle the selection of groups
     */
    $(".pgmb-toggle-account", container).click(function(event){
        event.preventDefault();

        let checkboxes = $(this).closest('tbody').find('.mbp-checkbox-container input:checkbox:visible');

        checkboxes.prop("checked", !checkboxes.prop("checked"));
    });

    /**
     * Checks if any of the businesses are not allowed to use the localPostAPI and show an informational message if one is
     */
    this.checkForDisabledLocations = function(){
        if($('input:disabled', fieldContainer).length){
            locationBlockedInfo.show();
            return;
        }
        locationBlockedInfo.hide();
    };
    this.checkForDisabledLocations();

    // this.scrollToSelectedLocation = function(){
    //     let selectedItem = $(".mbp-checkbox-container input[type='radio']:checked", container);
    //     console.log(selectedItem);
    //     fieldContainer.scrollTop(fieldContainer.scrollTop() + selectedItem.position().top
    //         - fieldContainer.height()/2 + selectedItem.height()/2);
    // }
    // this.scrollToSelectedLocation();

    /**
     * Refreshes the location listing
     *
     * @param {boolean} refresh When set to true - Forces a call to the Google API instead of relying on the local cache
     * @param {object} selected Array of selected locations
     */
    this.refreshBusinesses = function(refresh, selected){
        refresh = refresh || false;

        fieldContainer.empty();
        instance.populate(refresh);
        // $.post(ajaxurl, data, function(response) {
        //     fieldContainer.replaceWith(response);
        //     //Refresh our reference to the field container
        //     fieldContainer = $('.mbp-business-selector', container);
        //     refreshApiCacheButton.html(mbp_localize_script.refresh_locations).attr('disabled', false);
        //     instance.checkForDisabledLocations();
        // });
    };

    if(businessSelectorSelectedLocation.val() === '0'){
        instance.refreshBusinesses(false);
    }

    this.getBusinessSelectorSelection = function(){
        let selectedBusinesses = {};

        $.each($('input:checked', fieldContainer), function(){
            let name = $(this).attr('name');
            let user_id = name.match(/([0-9]+)/);

            if(user_id[1]){
                //selectedBusinesses.push($(this).val());
                selectedBusinesses[user_id[1]] = $(this).val();
            }

        });
        return selectedBusinesses;
    };




    /**
     * Obtain refreshed list of locations from the Google API
     */
    refreshApiCacheButton.click(function(event){
        event.preventDefault();
        // instance.refreshBusinesses(true, instance.getBusinessSelectorSelection());
        refreshApiCacheButton.html(__('Please wait...', 'post-to-google-my-business')).attr('disabled', true);
        fieldContainer.empty();
        instance.populate(true).then(function(result){
            if(result){
                refreshApiCacheButton.html(__('Refresh locations', 'post-to-google-my-business')).attr('disabled', false);
                refreshLockout = 5;
            }else{
                let count = refreshLockout;
                const lockoutTimer = setInterval(() => {
                    count--;
                    /* translators: %d is the amount of seconds remaining before the locations can be refreshed again */
                    refreshApiCacheButton.html(sprintf(__("Retry in %d", 'post-to-google-my-business'), count));
                    if(count <= 0){
                        refreshApiCacheButton.html(__('Refresh locations', 'post-to-google-my-business')).attr('disabled', false);
                        clearInterval(lockoutTimer);
                    }
                }, 1000);
                refreshLockout = refreshLockout * 2;
            }

        });


    });
};


export default BusinessSelector;
