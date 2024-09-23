/**
 * @property {Array} pgmb_block_editor_data[] script localization
 */

import icons from "../admin/icons";
import {registerPlugin} from "@wordpress/plugins";
import {__} from "@wordpress/i18n";
import {PluginPostStatusInfo, PluginPrePublishPanel} from "@wordpress/edit-post";
import AutoPostToggle from "../admin/components/autopost-toggle";
import "../admin/metabox-fix";
import {applyFilters} from "@wordpress/hooks";
import PluginIsGutenbergPost from "./is-gutenberg-post";



let AutoPostToggles = (props) => {
    return (
        <>
            <PluginPostStatusInfo>
                <AutoPostToggle />
                <PluginIsGutenbergPost/>
            </PluginPostStatusInfo>
            <PluginPrePublishPanel title={__('Post to GMB', 'post-to-google-my-business')} initialOpen='true'>
                <AutoPostToggle />
                { applyFilters('pgmb_pre_publish_panel') }
            </PluginPrePublishPanel>
        </>
    );
}

registerPlugin('pgmb-autopost-plugin', { render: AutoPostToggles, icon: icons.pgmb });

