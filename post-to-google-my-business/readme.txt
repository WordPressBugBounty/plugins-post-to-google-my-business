=== Post to Google My Business (Google Business Profile) ===
Contributors: koen12344, freemius
Donate link: https://tycoonmedia.net/?utm_source=repository&utm_medium=link&utm_campaign=donate
Tags: google my business, gmb, auto publish, posts, post, local search, google my business posts, google places, google plus, google+
Requires at least: 4.9.0
Tested up to: 6.6.1
Stable tag: 3.1.27
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

(Auto-)Publish and manage Google My Business (GMB) Posts from the WordPress Dashboard!

== Description ==

The posts feature in Google Business Profile (formerly: Google My Business) is a great way to improve the presence of your, or your clients' business on Google. It can be a hassle however to have to log in to Google My Business every time you want to create a new post, likewise it can be an easy thing to forget.

Don't miss out on the SEO benefit, and save time by creating posts on Google My Business directly from the WordPress Dashboard!

Use the Auto-post feature to instantly publish your latest WordPress post to your Google Business Profile, based on a preset template and the posts' featured image.

The Post to Google My Business plugin utilizes the official Google My Business API with secure oAuth authentication to ensure your Google account is safe.

= Features =
* Create, edit or delete posts without having to visit your Google My Business dashboard
* Automatically publish your latest WordPress posts to GMB using the Auto-post feature
* Generates beautiful auto-posts from any content. HTML, shortcodes and clutter from visual editors such as Divi or Visual Composer are automatically stripped
* Network- and site-level Multisite support
* Supports publishing to GMB from external apps (such as Zapier, Integromat, IFTTT, ManageWP, InfiniteWP, MainWP and more)
* Uses official Google My Business API
* Developer friendly. Uses the latest built-in WordPress functions and has various actions/filters to hook into.
* Translatable. Uses built-in WordPress functions for easy translation.
* Compatible with Gutenberg/Block Editor
* Supports modern image formats such as WebP and AVIF

> **Time-saving features available in the Premium versions:**
>
> * Product support, create "real" Products in GBP based on your WooCommerce Products or other content
> * Schedule Google My Business posts for automatic publishing in the future
> * Publish posts to multiple locations, across multiple Google accounts at once
> * Automatic re-posting (post recycling) - Automatically recycle your GMB posts at preset intervals and x amount of times
> * Auto publish posts with specific tags or categories
> * Make posts unique using Spintax
> * Post Campaigns - Create posts on GMB that aren't tied to any specific WordPress post or page.
> * Evergreen content - randomly publish items from a selection of your content
> * Multiple Auto-post templates
> * Manage GMB post campaigns for your agency clients
> * Much more!
>
> **[Learn more about Post to Google My Business Premium](https://tycoonmedia.net/?utm_source=repository&utm_medium=link&utm_campaign=learn_more&utm_content=description)**

= Great support! =
We're here to help in case you're having trouble using Post to Google My Business. Just ask in the support forum and we'll get back to you ASAP. Feedback and ideas to improve the plugin are always welcome.

== Installation ==

Installing and configuring Post to Google My Business is easy!

1. Upload the plugin files to the `/wp-content/plugins/post-to-google-my-business` directory, or install it through the plugins page within the WordPress Dashboard.
2. Activate the plugin through the **Plugins** page in WordPress
3. Go to the **Post to GMB** > **Settings** page to configure the plugin
4. To allow your website to post to Google My Business on your behalf, click **Connect to Google My Business**. Confirm the authorization using the Google account that holds your business location.
5. You will be redirected back to the settings page. Select your business location and press **Save Changes**.
6. All set! When creating a new WordPress **Post** there will a new metabox that allows you to create posts on Google My Business.


== Frequently Asked Questions ==

= Can I use this plugin on a localhost installation? =

Yes, but you may run into errors if you add a link or image to your post. Google will try to fetch your image/video, or resolve the link to your website, but if your localhost installation can't be reached from the outside world, it won't be able to do so.
The quick post feature will not work at all in that case, because it uses the URL and Featured Image of your post.

= Why is/are my location(s) grayed out? =

Not every Google My Business listing is allowed to create posts on Google My Business (localPostAPI is disabled). This means the plugin can't create posts on those locations. First, make sure your location is fully verified & live. Business chains (10+ locations) are normally exempt from creating posts, but are temporarily allowed to create them to share updates about the corona virus.

= Why are my scheduled posts being published too late/not at all? =

Post to Google My Business relies on the WP Cron system to send out scheduled posts. By default, it is only triggered when someone visits your website. If your site doesn't get a lot of visitors, your posts may be sent out too late. To make the WP Cron system more dependable, you can [hook it into the system task scheduler](https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/)

= Why can't I create posts with a video? =

While GBP itself supports creating posts with video, [it's (currently) not possible](https://i.imgur.com/UjFmYrC.png) to create them through the GBP API. So the plugin can not do it either.

= Why can't I create posts with multiple images? =

While GBP itself lets you create posts with up to 10 images, the GBP API (currently) only allows [a single image](https://i.imgur.com/RzlfTOB.png) to be uploaded. That's why you can only upload a single image in the plugin.

= Why does the plugin require such extensive permissions on my GBP locations? =

While ideally we'd ask for as little permissions as possible to make the plugin work, the Google Business Profile API
oddly enough only has a single permission level. It's either all or nothing. The idea of giving the plugin permission to edit or delete
your GBP location(s) might sound scary, but rest assured, your access tokens are **only** stored (securely) within your own website. Nor
are the endpoints to make any bad stuff happen implemented in the plugin backend, or the plugin itself. So even if your site were
to be compromised, the access tokens would be useless.

= I'm getting a "Fetching image failed" error when creating posts =

When creating a post with an image, Google will try to download the post image directly from your website. Hence, it must be directly
accessible when opening the image URL. When you get a "Fetching image failed" error, it means Google is unable to download the image from your website
Services like CloudFlare may have an impact on this. In CloudFlare, turn off any image hotlinking protection or other features that may interfere
with image accessibility. To further debug the issue, you can use a service like [httpstatus](https://httpstatus.io/). Enter the URL of your image
(it is shown along with the error) into the "URLs to check" field. If the returned status is not 200 - OK, there is something wrong!

== Screenshots ==

1. Customizing and posting GMB post
2. Using the Auto-post feature
3. Creating a "What's new" post
4. Creating an event
5. Creating an offer post
6. Auto-post template settings

== Changelog ==

= 3.1.27 =
* Added: AVIF image conversion, fall back on original uploaded image if it matches GMB requirements

= 3.1.26 =
* Clean up JS dependencies
* Test on WordPress 6.6
* Update Freemius SDK

= 3.1.24 =
* Update: Freemius SDK 2.7.1

> **Premium**
>
> * Fix: Creating products not working (due to Google changes)

= 3.1.23 =
> **Premium**
>
> * Fix: Delete event date of previous post not updated when editing auto-repost schedule
> * Added: Indication when previous auto-repost item will be deleted

= 3.1.22 =
* Tested on WordPress 6.5
* Fix: return original image if failing to create intermediate
* Fix: Incorrect loading of admin styles
* Improved: Authentication logic
* Improved: (Developers) LocalPost object now has getters
* Update: Freemius SDK 2.6.2


= 3.1.21 =
* Fix: Potential issue with images smaller than 1200x900
* Update: Freemius SDK 2.6.1

= 3.1.18 =
* Add option to bypass WP-Cron for debugging purposes

= 3.1.17 =
* Tested on WordPress 6.4
* Update: Add additional 20 second lockout to prevent double posts when auto-posting
* Update: Freemius SDK 2.5.12

= 3.1.16 =
* Update: WP Background processing 1.1.1
* Update: Freemius SDK 2.5.11

> **Premium**
>
> * Fix: Change to GBP causing products to stop working

= 3.1.15 =
* Fix: Add nonce to ajax endpoint to dismiss notifications

= 3.1.14 =
* Update: Cleaned up Javascript dependencies

> **Premium**
>
> * Fix: Google accounts being duplicated in the business selector when creating/editing multiple posts
> * Fix: Publish button saying "Publish Now" instead of "Update" when editing posts

= 3.1.13 =
* Fix: Posts longer than 1500 characters containing emoji causing Google API error

= 3.1.12 =
> **Premium**
>
> * Fix: Not selecting any location in the auto-post template location selector caused JS/PHP error

= 3.1.11 =
* Fix: Freemius SDK updated to v2.5.5 to fix "Call to undefined method Freemius_Api_WordPress::RemoteRequest()" error
* Update: Pt_BR translations by @valdermirmaran
* Update: "Product" tab now aligned to the right to better separate it from regular posts

> **Premium**
>
> * Update: Removed call-to-action for Products (in accordance with GMB itself)

= 3.1.10 =
* Added extra check for mbstring when creating posts
* Updated Freemius SDK

> **Premium**
>
> * Products are now allowed to have no description

= 3.1.9 =
* Added "Refresh Post Status" bulk option to post list

> **Agency**
>
> * Added CSV export to post list

= 3.1.8 =
* fix issue blocking site health screen

= 3.1.7 =
* Make product price field no longer required
* Add check for mbstring PHP module

= 3.1.6 =
* Tested on 6.2
* Added notification about new Product Sync for GBP plugin
* Included new translations

= 3.1.5 =
* Add extra content-type header check for WebP images

= 3.1.4 =
* Fix "Refresh locations" button not actually refreshing locations from GMB API

= 3.1.3 =
* Fix business selector not loading all locations

= 3.1.2 =
* Fix issues with account/business selector

> **Premium**
>
> * Convert Campaign tags/categories from built-in to custom taxonomy

= 3.1.1 =
> **Premium**
>
> * Fix missing account controls

= 3.1.0 =
* Prevent duplicate posts when using auto-post
* Improve auto-posting logic
* Add option to enable/disable specific request types (Editor, Internal, XML-RPC, REST)
* Lowered event & offer title length from 80 to 58 characters
* Prevent items in the trash from creating (scheduled) GPB posts
* Improve the business selector to load large amounts of locations in chunks
* Refactoring

> **Premium**
>
> * Fix posting products (effective_id error)
> * Fix products showing incorrect summary in post list

= 3.0.28 =
* Add fix for duplicate posts in same session

= 3.0.27 =
* Add check for sites that have relative image URLs for some reason

= 3.0.26 =
> **Premium**
>
> * Add better detection of product creation errors
> * Fix cookie check when submitting cookie details
> * Various small improvements to product API code


= 3.0.25 =
> **Premium**
>
> * Improve product JSON decoding logic

= 3.0.24 =
* Updated pt translation

> **Premium**
>
> * Fix product custom category field not saving

= 3.0.23 =
> **Premium**
>
> * Fix image upload error for products

= 3.0.22 =
* Add clearer errors to dialog when clicking post in calendar
* Change layout of store/shop code in created posts dialog

> **Premium**
>
> * Fix option to disable date & time selector for auto-post templates

= 3.0.21 =
* Add store code to business selector and created posts dialog
* Add debounce to date & time selector to reduce Ajax calls
* Add option to disable date & time selector on event/offer dates
* Add delay to posts to avoid excessive API calls
* Fix delete API call being triggered multiple times
* Fix auto-post on scheduled WordPress posts

> **Premium**
>
> * Added "loop" function to evergreen content
> * Evergreen content now only posts unique content
> * Auto-repost will delete the previous post

= 3.0.20 =

* Fix image not updating when updating post
* Fix image not being deleted from GMB when deleted from post
* Fix CTA not being deleted from GMB when deleted from post
* Allow placeholder variables in date fields

> **Premium**
>
> * Fix product price range field not loading in auto-post template
> * Fix duplicate call when deleting multiple posts at once
> * Refresh location list after updating product cookies

= 3.0.19 =
* Remove hasVoiceOfMerchant check

> **Premium**
>
> * Fix cookies not saving

= 3.0.18 =
* Move access token from URL to Auth header
* Add check for IP address in call-to-action URL
* Fix label for locations in business selector
* Adjust max post length to 1499 characters to avoid length error

= 3.0.17 =
* Change button selection checkboxes to dropdown
* Route API requests through custom backend
* Fix Undefined property: stdClass::$languageCode notice/error
* Fix Undefined index: order/orderby notices/errors
* Fix Undefined index: hook_suffix notice/error
* Update locales (Brazilian Portuguese thanks to Valdemir Maran)

> **Premium**
>
> * Disable call-to-action button types that aren't available for products
> * Clarify how to publish products
> * Fix Undefined index: mbp_form_fields notice/error when creating new auto-post template
> * Allow wildcard variables in product max pricerange field
> * Add new WooCommerce variables for variation products
> * Add setting check to cookie dialog
> * Fix saving cookie settings being triggered twice

= 3.0.14 =
* Fix incorrect location selection within groups
* Fix support for Google accounts with more than 20 location groups
* Remove shortcodes from WooCommerce product description

> **Premium**
>
> * Fix undefined index errors on product form when WP_DEBUG was enabled
> * Fix intermittent issue with location(s) not being selected in the "Advanced post settings"
> * Restore disappeared "Toggle Selection" function on Google Accounts

= 3.0.13 =
* Fix auto-post toggle not being turned on by default in block editor
* Improve API code to be compatible with new version of GMB API

> **Premium**
>
> * Improve product publishing api

= 3.0.12 =

> **Premium**
>
> * Improve code for product publishing

= 3.0.11 =
* Fix incorrect token revocation request
* Fix pre-php 7.3 error composer message
* Clarify "Refresh token" error message
* Fix a few locale mistakes
* Bump minimum PHP version to 7.0
* Fix for Notice: Trying to access array offset on line 163
* Add account key to mbp_business_selector_locations filter
* Fix display of service area businesses in business selector
* Add placeholder parsing to event/offer title and trim it to 80 characters

> **Premium**
>
> * Show error when evergreen content schedule does not exist in cron
> * Fix edit and duplicate functions in Starter version
> * Add debug data download for products

= 3.0.10 =
* Add graceful error for when the Google authorization is cancelled
* Fix advanced post settings spacing
* Update Freemius SDK
* Clear access token cache when account is deleted
* Fix a few permission issues

= 3.0.9 =

> **Premium**
>
>* Improvements to product publishing code

= 3.0.8 =
* Fix text domain on update notification
* Prevent api error when trying to create product

= 3.0.7 =
* Fix location list not showing all locations in groups with more than 100 locations
* Add upgrade & new feature notifications
* Improve upgrade process

= 3.0.6 =

* Fix controls on dynamically loaded accounts in business selector

> **Premium**
>
> * Fix woocommerce product category sync

= 3.0.5 =

* Update locales (Portuguese thanks to Valdemir Maran)
* Post editor: Various layout and logic fixes
* WP 5.9: Fix calendar icon

> **Premium**
>
> * Add product support
> * Fix evergreen not selecting the correct posts
> * Fix evergreen page empty in Starter version
> * Fix evergreen date timezone issue

= 3.0.2 =
* Updated Brazilian Portuguese translations (Thanks to @valdemirmaran)
* Fix error when no post types are selected in the settings
* Added evergreen content promotional page
* Fix not showing welcome message on new site within multisite

> **Premium**
>
> * Fix private backend post types being indexed by Yoast causing 404s

= 3.0.1 =
* Remove deprecated "Get Offer" call to action
* Fix link parsing mode not working
* Updated pot file (now automatically included in build process)
* Updated Dutch translations
* Fixed localization for calendar
* Fixed datetime check in auto-post template editor

> **Premium**
>
> * Added check for invalid evergreen post schedule
> * Fixed localization for cron selector

= 3.0.0 =
* Refactor & improve a lot of code, mainly improved the way the plugin connects to Google
* Paginated post & created post list, added bulk actions
* Fix Featured Image checkbox state not saving
* Remove pointless debug page
* Moved post scheduling calendar to its own dashboard page, added popup with options
* Added WebP support (GMB does not support it, so plugin will convert image to PNG)
* Added WooCommerce support by default

> **Premium**
>
> * (Starter) Moved ability to select a location per post to Starter
> * (Pro) Added ability to create multiple post templates
> * (Pro) Added "evergreen content" feature to automatically publish posts old posts
> * (Pro) Moved ability to publish posts to multiple locations at once to Pro
> * (Agency) Added ability to connect multiple Google accounts


= 2.2.49 =
* Add option to advanced post options to change link parsing mode

= 2.2.48 =
* Temporary fix for the cURL error 60: SSL certificate problem: certificate has expired error

= 2.2.45 =
* Fix issue with image size detection (filesize(): stat failed warning)
* Fix image size detection on post created outside WP admin dash (Fatal error: Call to undefined function PGMB\download_url())
* Fix warnings when location has no address or locality

= 2.2.44 =
> **Premium**
>
> * Now it was posting every hour, doh! Fixed

= 2.2.43 =
* Tested on WordPress 5.8
* Fixed issue with metabox not working in block editor

> **Premium**
>
> * Fixed issue with automatic re-post that made a post go out every minute(!) instead of every day of the month
> * Added "Daily" option to re-post feature

= 2.2.42 =
* Add Brazilian Portuguese translations (thanks to @valdemirmaran)

= 2.2.41 =
* Add selection function for location groups
* Add note for Block Editor CPTs without "custom-fields" support enabled
* Update Freemius SDK

= 2.2.39 =
* Fixes "Cannot declare class PGMB\Vendor\Cron\AbstractField, because the name is already in use" error when saving post

= 2.2.34 =
* Fix for image size detection logic

= 2.2.33 =
* Add extra error message for when Product post type is chosen
* Fix block editor issue for custom post types that don't have custom-fields enabled

= 2.2.32 =
* Use wp_get_http_headers instead of get_headers for better compatibility

= 2.2.31 =
* Testing on WP 5.6, small fixes for PHP8 compatibility

= 2.2.29 =
* Fixed some issues with checking of post image size
* Fixed image not showing up in editor when it was too small to have a "medium" thumbnail
* Updated Freemius SDK

= 2.2.28 =
* Fix compatibility issue with older version of plugin

= 2.2.27 =
* Fix date and time checker
* Fix checkbox state not being loaded since 5.5.1

= 2.2.26 =
* Added (a lot) of location-specific variables

= 2.2.25 =
* Make all GMB post types and WP post types available in free version
* Fix Gutenberg assets being loaded in post type that was disabled

> **Premium**
>
> * Fix campaigns feature being partially available in Pro version
> * Fix default location selector showing checkboxes instead of radiobuttons when refreshing

= 2.2.19 =
* Fixed handling of URLs containing non-ascii characters
* Added more info about product posts

= 2.2.18 =
* Temporarily remove check for isLocalPostApiDisabled due to COVID-19

= 2.2.17 =
* Added COVID-19 post
* Added full autopost template editor in settings to edit the default autopost template
* Added welcome message
* Added location-specific variables (%location_primaryPhone%, %location_websiteUrl% ...)
* Added new mbp_placeholder_decorators ($decorators, $parent_post_id) filter, and VariableInterface
* Add checks for image size, disable video (no longer allowed by Google)
* Simplified & improved auto-post logic
* Improved image uploader (ready for 10 images, but Google API doesn't support it yet)
* Improved error notices in metabox
* Improvements to multisite handling and activation/deactivation/delete routines
* Fixed error notice when fetching empty location groups
* Fixed Free version not deactivating when activating premium
* Fixes empty location group causing PHP notice
* Fixed Gutenberg double post bug and improve compatibility
* Fix Draft post showing incorrect publish date
* Fix notice when Google error doesn't include details
* Update Freemius SDK to fix multisite issue

> **Premium**
>
> * Add "Event all day" checkbox for events without a start and end time
> * Added feature to enable autopost per category or tag
> * Fixed Auto-post template editor button being shown on campaigns page
> * Fixed errors when when all post types are unchecked in the settings page

= 2.2.10 =
* Fix issue with paragraphs getting removed in auto-post

= 2.2.9 =
* Fix auto-post incorrectly throwing 1500 character error
* Hide "Save draft" button on already published post

> **Premium**
>
> * Fix caption on "Save Template" button switching to "Publish" when adding schedule

= 2.2.8 =
* Fix gutenberg issue caused by 2.2.7

= 2.2.7 =
* Fix compatibility issue with Yoast SEO & Classic Editor

= 2.2.6 =
* Updated .pot file and Grunt scripts
* Updated Dutch, Russian and Ukrainian translations
* Display post publish date in metabox

= 2.2.5 =
* Fix duplicate post issue

> **Premium**
>
> * Remove Auto-post checkbox being shown on campaigns page

= 2.2.4 =
* Fix issues with CTA URL on button
* Improve updater (again)
* Fix default value for CTA URL field
* Fix CTA url field disappearing when loading post

> **Premium**
>
> * Fix repost schedule being improperly parsed

= 2.2.3 =
* Improve updater

= 2.2.2 =
* Fix Form field parser allowing dates in the past

= 2.2.1 =
* Fix for Gutenberg autopost
* Restore filter functions
* Update Freemius SDK

= 2.2 =
* Moved API communication to an asynchronous process
* Added Auto-post template editor
* Added Debug info tab to settings page
* Added dialog with created posts
* Made UI more intuitive
* Added some fixes to improve compatibility with Gutenberg
* Added functionality to fetch image from content or use the featured image
* Tons of improvements and bug fixes "under the hood"
* Lots more to come in future updates!

> **Premium**
>
> * Made re-posting much more flexible
> * Re-posted posts will now appear as a separate post in the metabox

= 2.1.18 =
> **Premium**
>
> * Actually parse the relative datetimes on scheduled posts :)
> * Disable Product post support (removed from GMB api)
> * Improve display of datetimes, better timezone handling

= 2.1.17 =
> **Premium**
>
> * Allow relative time notation in datetimepickers

= 2.1.16 =
* Improved parsing of post content

= 2.1.11 =
* Improve development & deployment methods

= 2.1.10 =
* Update Freemius SDK
* Remove shortcodes from auto-post by default

= 2.1.9 =
* Security fix

= 2.1.8 =
* Fixes issue with image URL spinner with no image set (PHOTO media error)

= 2.1.7 =
* Properly delete child posts and schedules when deleting parent post

= 2.1.6 =
> **Premium**
>
> * Spin image URL

= 2.1.5 =
* Fix issue when trying to load more than 100 locations
* Apply filter mbp_get_locations filter to cached locations

= 2.1.4 =
* Fix settings page conflict caused by plugins using old version of WeDevs Settings API
* Fix Learn more link about grayed out locations

= 2.1.3 =
* Fix auto-post sending campaign posts
* Improve Gutenberg compatibility

= 2.1.2 =
* Fix auto-post being triggered too early
* Simplify business selector

> **Premium**
>
> * Fix repost sheduled events not getting deleted
> * Fix reposts not being published when recurrence was set to 0
> * Fix reposts being posted twice when not scheduled

= 2.1.1 =
* Increase API timeout
* Fix multiline posts
* Improve error messages
* Fix API token refresh requests when network activated

= 2.1.0 =
* Improves location loading (+100 Locations in account)
* Check whether locations have access to the Posts API
* Call Now button support
* Improves auto-post logic
* Restructuring code

> **Premium**
>
> * Improves posting to multiple locations at once
> * Product post support

= 2.0.10 =
* Update Freemius SDK
* Add filters to Auto post feature

> **Premium**
>
> * Add option to edit Auto post URL

= 2.0.9 =
* Fix 500 error on PHP 5.4 https://wordpress.org/support/topic/500-error-when-adding-a-new-post/

= 2.0.8 =
* Fix issue some settings getting deleted when updating from 2.0.6 to 2.0.7
* Improve compatibility with external publishing apps and services
* Made plugin settings page more intuitive

= 2.0.7 =
* Strip HTML from posts
* Cut posts to 1500 characters
* Added word and character counters
* Simplified business selector
* Removed user selector, now integrated with business selector
* Better support for grouped locations
* Allow filtering/searching of locations
* Remove references to datetimepicker

> **Premium**
>
> * Added buttons to Select/Deselect all locations at once
> * Fixed some issues with Pro features in trial

= 2.0.6 =
* Add ability to save posts as draft
* Add option to invert the "Quick Publish" checkbox. Allows you to automatically publish to GMB when the WordPress post is created externally
* Fix display issue when location has no thumbnail
* Add placeholder index.php files to plugin folders
* Show "No GMB posts found." again when last post is deleted.
* Close form when the post currently being edited is being deleted

> **Premium**
>
> * Fix Premium features not being enabled when in trial
> * Fix scheduled posts not being posted in Pro
> * Fix issue with post type settings causing error when no post type is selected and settings are saved
> * Check default location when creating new post
> * Fix Metabox not visible on post campaigns
> * Fix invisible month switching icons on the datetimepicker


= 2.0.5 =
* Fix location info when importing old posts
* Fix Google link not appearing when using quickpost

= 2.0.4 =
* Version function magically disappeared, fixed

= 2.0.3 =
* Fixes updating issue on multisite

= 2.0.2 =
* Fix issue causing fatal error on PHP 5.6 < https://wordpress.org/support/topic/2-0-1-update-crashes-site/

= 2.0.1 =
* Fixes issue with certain Google post fields not updating when updating post

= 2.0.0 =
* Improved metabox, easily create multiple GMB posts per WordPress post
* Supports new Google post types
* Added Quick post feature to create GMB posts based on a preset template. All you have to do is tick the checkbox to post!
* Fixed plugin conflict causing endless loop
* Improved and simplified settings page
* Improved business selector
* And much, much more!

> **New premium features**
>
> * Automatic reposting - Choose time interval and amount of times to repost
> * Post spinning and variables - Make your posts unique using Spintax and variables such as %site_name%, %post_title%
> * Video posts
>
> **[Learn more about Post to Google My Business Premium](https://tycoonmedia.net/?utm_source=repository&utm_medium=link&utm_campaign=learn_more&utm_content=changelog)**

= 1.2 =
* Added logic for cleaning up options when uninstalling plugin
* Improved admin/error notices

= 1.1.1 =
* Fixed PHP compatibility issue https://wordpress.org/support/topic/getting-parse-error-when-installing/

= 1.1 =
* Improved business location selector
* Fixed timepicker issues
* Javascript for metabox now in separate file
* Fixed incorrect language code causing issues when posting
* Various other small improvements and fixes

= 1.0 =
* Initial release

== Upgrade Notice ==