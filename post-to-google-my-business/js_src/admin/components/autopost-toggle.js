/**
 * @property {Array} pgmb_block_editor_data[] script localization
 */
import { __ } from '@wordpress/i18n';


import { ToggleControl } from "@wordpress/components";
import {withSelect, withDispatch, dispatch, select, subscribe} from "@wordpress/data";


const { checkedByDefault } = pgmb_block_editor_data;

let defaultLoaded = false;
const unsubscribe = subscribe(() => {
	let isCleanNewPost = select('core/editor').isCleanNewPost();
	if(isCleanNewPost && checkedByDefault && !defaultLoaded){
		defaultLoaded = true;
		updateGMBAutoPostCheckBox(true);
		unsubscribe();
	}
});

let AutoPostCheckBoxValue = () => {
    return select('core/editor').getEditedPostAttribute('meta')['_mbp_gutenberg_autopost'];
}


let updateGMBAutoPostCheckBox = (value) => {
    dispatch('core/editor').editPost({meta: {_mbp_gutenberg_autopost: value}})
}

let AutoPostToggle = (props) => {
    return (
        <ToggleControl
            label={__("Auto-post to GMB", "post-to-google-my-business")}
            checked={ props.gmb_checkbox }
            onChange={(value) => props.onMetaFieldChange(value)}
        />
    );
}

AutoPostToggle = withSelect(
    (select) => {
        return {
            gmb_checkbox: AutoPostCheckBoxValue()
        }
    }
)(AutoPostToggle);

AutoPostToggle = withDispatch(
    (dispatch) => {
        return {
            onMetaFieldChange: updateGMBAutoPostCheckBox
        }
    }
)(AutoPostToggle);

export default AutoPostToggle;
export { AutoPostCheckBoxValue, updateGMBAutoPostCheckBox };
