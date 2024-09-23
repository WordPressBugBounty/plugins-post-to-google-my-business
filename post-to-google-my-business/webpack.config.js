// Require path.
const path = require( 'path' );

// Configuration object.
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

// Overrides the default config to have deterministic file names
// and also RTL stylesheets.
module.exports = {
    ...defaultConfig,
    // Create the entry points.
    // One for frontend and one for the admin area.
    entry: {
        // frontend and admin will replace the [name] portion of the output config below.
        //frontend: './js_src/front/front-index.js',
        settings: './js_src/admin/settings.js',
        metabox: './js_src/admin/metabox.js',
        block_editor: './js_src/admin/block-editor-index.js',
        notice: './js_src/admin/notice.js',
        dashboard: './js_src/admin/dashboard.js'
    },

    // Create the output files.
    // One for each of our entry points.
    output: {
        // [name] allows for the entry object keys to be used as file names.
        filename: '[name].js',
        // Specify the path to the JS files.
        path: path.resolve( __dirname, 'js' )
    },


    // optimization: {
    //     minimizer: [
    //         new UglifyJsPlugin({
    //             uglifyOptions: {
    //                 output: {
    //                     comments: /\<\/?fs_premium_only\>/i,
    //                 },
    //             },
    //             extractComments: true,
    //         }),
    //     ],
    // },
};
