import {select, subscribe} from "@wordpress/data";
import {AutoPostCheckBoxValue, updateGMBAutoPostCheckBox} from "./components/autopost-toggle";
import {doAction} from "@wordpress/hooks";

// hack for refreshing the metabox after saving the gutenberg post

let isAlreadyUpdating = false;
subscribe(function () {

    let isSavingPost = select('core/editor').isSavingPost();
    let isAutosavingPost = select('core/editor').isAutosavingPost();


    if (isSavingPost && !isAutosavingPost) {
        if(AutoPostCheckBoxValue() && !isAlreadyUpdating) {
            isAlreadyUpdating = true;
            setTimeout(function () {

                doAction('pgmb_saving_post');
                let isEditedPostBeingScheduled = select('core/editor').isEditedPostBeingScheduled();
                if(!isEditedPostBeingScheduled){
                    updateGMBAutoPostCheckBox(false);
                }

                isAlreadyUpdating = false;
            }, 5000); //Some ugly delay to make sure the post was created before reloading the list
        }

    }
});
