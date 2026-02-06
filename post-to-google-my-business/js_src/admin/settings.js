/**
 * @property {string} ajaxurl URL for ajax request set by WordPress
 *
 * Translations
 * @property {Array} mbp_localize_script[] Array containing translations
 * @property {string} mbp_localize_script.refresh_locations "Refresh Locations"
 * @property {string} mbp_localize_script.please_wait "Please wait..."
 */

import * as $ from "jquery";
import PostEditor from "./components/PostEditor";
import BusinessSelector from "./components/BusinessSelector";
import {__} from "@wordpress/i18n";


const BUSINESSSELECTOR_CALLBACK_PREFIX = mbp_localize_script.BUSINESSSELECTOR_CALLBACK_PREFIX;
const POST_EDITOR_CALLBACK_PREFIX = mbp_localize_script.POST_EDITOR_CALLBACK_PREFIX;
const FIELD_PREFIX = mbp_localize_script.FIELD_PREFIX;

const { disable_event_dateselector, setting_selected_location, nonce } = mbp_localize_script;

const submitButton = document.querySelector('#mbp_google_settings #submit');
const oldtext = submitButton.value;
const listener = function(loading){
    if(loading){
        submitButton.value = __('Please wait for all locations to load', 'post-to-google-my-business');
        submitButton.disabled = true;
    }else{
        submitButton.value = oldtext;
        submitButton.disabled = false;
    }
}

let postEditor = new PostEditor(false, POST_EDITOR_CALLBACK_PREFIX, null, null, disable_event_dateselector, listener);
postEditor.setFieldPrefix(FIELD_PREFIX);


const SettingsBusinessSelector = new BusinessSelector($('.mbp-google-settings-business-selector'), BUSINESSSELECTOR_CALLBACK_PREFIX, document.querySelector('.mbp-google-settings-business-selector'), listener, false, true, nonce);

SettingsBusinessSelector.setSelection(setting_selected_location);

$('.pgmb-disconnect-website').click(function(event){
    if(!confirm(__('Disconnect the Google account from this website?', 'post-to-google-my-business'))){
        event.preventDefault();
    }
});


export { postEditor, FIELD_PREFIX, POST_EDITOR_CALLBACK_PREFIX };
