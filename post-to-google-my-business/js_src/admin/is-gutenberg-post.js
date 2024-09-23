// The array/object key that will be sent with the REST request
import {useEffect} from "@wordpress/element";
import {withDispatch, withSelect} from "@wordpress/data";

const key = 'isGutenbergPost';

let PluginIsGutenbergPost = ( { setIsGutenbergPost, isDirty } ) => {
    useEffect( () => {
        setIsGutenbergPost();
    }, [ isDirty ] );
    return (
        <>
            { null }
        </>
    );
};


PluginIsGutenbergPost = withSelect( ( select ) => {
    return {
        isDirty: select( 'core/editor' ).isEditedPostDirty(),
    };
} )(PluginIsGutenbergPost);

PluginIsGutenbergPost = withDispatch( ( dispatch, _, { select } ) => {
    return {
        setIsGutenbergPost: () => {
            const isDirty = select( 'core/editor' ).isEditedPostDirty();
            const isGBPost = select( 'core/editor' ).getEditedPostAttribute( key ) || false;
            if ( ! isGBPost && isDirty ) {
                dispatch( 'core/editor' ).editPost( { [ key ]: true }, { undoIgnore: true } );
            }
        },
    };
} )(PluginIsGutenbergPost);

export default PluginIsGutenbergPost;