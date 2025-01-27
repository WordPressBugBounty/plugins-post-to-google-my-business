/**
 * @property {string} ajaxurl URL for ajax request set by WordPress
 */

import * as $ from 'jquery';

let accountsLoading = false;
let accountCache = {};
let groupCache = {};
let groupsLoading = false;
let locationsLoading = false;
let locationCache = {};

let isLoading = false;



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
    let businessSelectorSelectedLocation = $('input:checked', fieldContainer);

    let selectedLocations;

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

    this.getAccounts = async function(refresh = false){
        loadListeners.forEach(listener => listener(true));
        isLoading = true;

        const spinner  = document.createElement('span');
        spinner.className = 'spinner is-active';
        spinner.style.float = 'none';

        while(businessSelector.firstChild) {
            businessSelector.removeChild(businessSelector.firstChild);
        }

        const table = document.createElement('table');

        businessSelector.appendChild(spinner);
        businessSelector.appendChild(table);

        let accounts;
        try{
            while(accountsLoading){
                await new Promise((resolve) => setTimeout(resolve, 100));
            }
            if(accountCache.accounts && !refresh){
                accounts = accountCache.accounts;
            }else{
                accountsLoading = true;

                const accountsResponse = await instance.AjaxCall(nonce, 'get_accounts');
                accounts = accountCache.accounts = await accountsResponse.json();
                accountsLoading = false;
            }

        }catch(e){
            table.appendChild(e.message);
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

                table.appendChild(tbody);
                const loaderTR = document.createElement('tr');
                const loaderTD = document.createElement('td');
                loaderTR.appendChild(loaderTD);
                loaderTD.appendChild(spinner.cloneNode(true));
                table.appendChild(loaderTR);
                await instance.getGroups(account_id, tbody, null, refresh);
                loaderTR.remove();
            }
        }else{
            if(accounts.data.loading){
                businessSelector.appendChild(spinner.cloneNode(true));
                table.appendChild(instance.noticeRow("The background process is currently synchronizing your locations, please wait."));
                setTimeout(()=> {

                    accountCache.accounts = null;
                    instance.getAccounts();

                }, 5000);
            }else if(typeof accounts.data === "string"){
                table.appendChild(instance.noticeRow(accounts.data));
            }else{
                table.appendChild(instance.noticeRow("Unknown error occurred trying to load accounts"));
            }
        }

        spinner.remove();
        isLoading = false;
        loadListeners.forEach(listener => listener(false));
    }

    this.getGroups = async function(accountID, accountElement, offset = 0, refresh = false){
        let groups;
        try {
            while(groupsLoading){
                await new Promise((resolve) => setTimeout(resolve, 100));
            }
            let groupcachkey = accountID + offset;
            if(groupCache[groupcachkey] && !refresh){
                groups = groupCache[groupcachkey];
            }else{
                groupsLoading = true;
                const groupsResponse = await instance.AjaxCall(nonce, 'get_groups', {
                   account_id: accountID,
                   offset,
                    refresh: refresh,
                });

                groups = groupCache[groupcachkey] = await groupsResponse.json();
                groupsLoading = false;
            }
        }catch(e){
            accountElement.appendChild(instance.noticeRow(e.message));
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

                await instance.getLocations(accountID, group.name, accountElement, 0, refresh);
            }
        }else{
            if(typeof groups.data === "string"){
                accountElement.appendChild(instance.noticeRow(groups.data));
            }else{
                accountElement.appendChild(instance.noticeRow('An unknown error occurred trying to load the groups'));
            }
        }

        if(groups.data.count === 100){
            await instance.getGroups(accountID, accountElement, offset + 100, refresh);
        }
    }

    this.getLocations = async function(account_id, group_id, groupElement, offset = 0, refresh = false){
        let locations;
        try{
            while(locationsLoading){
                await new Promise((resolve) => setTimeout(resolve, 100));
            }
            let cachekey = group_id + offset;
            if(locationCache[cachekey] && !refresh){
                locations = locationCache[cachekey];
            }else{
                locationsLoading = true;

                const locationsResponse = await instance.AjaxCall(nonce, 'get_group_locations', {
                    group_id: group_id,
                    account_id: account_id,
                    offset,
                    refresh: refresh,
                });
                locations = locationCache[cachekey] = await locationsResponse.json();
                locationsLoading = false;
            }
        }catch(e){
            groupElement.appendChild(instance.noticeRow(e.message));
        }

        if(locations && locations.success && locations.data.rows){
            for(const row of locations.data.rows){
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
        }else{
            if(locations.data){
                groupElement.appendChild(instance.noticeRow(locations.data));
            }else{
                groupElement.appendChild(instance.noticeRow('Failed to load locations, unknown error'));
            }
        }


        if(locations.data.count === 500){
            await instance.getLocations(account_id, group_id, groupElement, offset + 500, refresh);
        }

    }

    this.getCheckboxInput = function(account_key, account_name, location_name, checked, disabled){
        const checkboxElement = document.createElement('input');
        checkboxElement.type = multiple ? 'checkbox' : 'radio';
        checkboxElement.name = businessSelector.dataset.field_name + "[" + account_key + "]" + (multiple ? "[]" : "");
        checkboxElement.id = 'cb-'+ businessSelector.dataset.field_name+ "-" + location_name.replace('/', '-');
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

        instance.getAccounts().then();

    }



    this.noticeRow = function(message){
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 2;
        td.textContent = message;
        tr.appendChild(td);
        return tr;
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
        let shouldDelete = confirm(mbp_localize_script.delete_account_confirmation);
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
        tb_show("Set Account Cookies", "#TB_inline?width=600&height=300&inlineId=mbp-set-cookies-dialog");
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
        instance.getAccounts(refresh);
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
        refreshApiCacheButton.html(mbp_localize_script.please_wait).attr('disabled', true);
        fieldContainer.empty();
        instance.getAccounts(true).then(function(result){
            refreshApiCacheButton.html(mbp_localize_script.refresh_locations).attr('disabled', false);
        });


    });
};


export default BusinessSelector;
