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


const BUSINESSSELECTOR_CALLBACK_PREFIX = mbp_localize_script.BUSINESSSELECTOR_CALLBACK_PREFIX;
const POST_EDITOR_CALLBACK_PREFIX = mbp_localize_script.POST_EDITOR_CALLBACK_PREFIX;
const FIELD_PREFIX = mbp_localize_script.FIELD_PREFIX;

const { disable_event_dateselector, setting_selected_location, nonce } = mbp_localize_script;

const buttons = document.querySelectorAll('.mbp-settings .submit .button');
const oldtext = buttons[0].value;

const listener = function(loading){
    if(loading){
        buttons.forEach((button) => {
            button.value = mbp_localize_script.wait_for_locations_to_load;
            button.disabled = true;
        });

        return;
    }
    buttons.forEach((button) => {
        button.value = oldtext;
        button.disabled = false;
    });
}

let postEditor = new PostEditor(false, POST_EDITOR_CALLBACK_PREFIX, null, null, disable_event_dateselector, listener);
postEditor.setFieldPrefix(FIELD_PREFIX);


const SettingsBusinessSelector = new BusinessSelector($('.mbp-google-settings-business-selector'), BUSINESSSELECTOR_CALLBACK_PREFIX, document.querySelector('.mbp-google-settings-business-selector'), listener, false, true, nonce);

SettingsBusinessSelector.setSelection(setting_selected_location);

$('.pgmb-disconnect-website').click(function(event){
    if(!confirm(mbp_localize_script.delete_account_confirmation)){
        event.preventDefault();
    }
});


export { postEditor, FIELD_PREFIX, POST_EDITOR_CALLBACK_PREFIX };
