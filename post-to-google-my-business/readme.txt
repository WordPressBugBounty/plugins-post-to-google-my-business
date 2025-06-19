=== Post to Google My Business (Google Business Profile) ===
Contributors: koen12344, valdemirmaran, freemius
Donate link: https://digitaldistortion.dev/?utm_source=repository&utm_medium=link&utm_campaign=donate
Tags: google my business, google business profile, gmb, local seo, google places
Requires at least: 4.9.0
Tested up to: 6.8.1
Stable tag: 3.2.5
Requires PHP: 7.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Auto-publish posts, pages & CPTs, plus manage Google Business Profile posts. All from your WordPress dashboard!

== Description ==

The **Posts** feature in **Google Business Profile** (formerly Google My Business) is a powerful way to enhance your business's presence on Google. However, constantly logging into your Google account to create new posts can be time-consuming, and easy to forget.

With the **Post to Google My Business** plugin, you can save time and maximize your SEO benefits by publishing directly from your WordPress dashboard!

= Effortless auto-posting & powerful features =

Use the **Auto-post** feature to instantly share your latest WordPress content to your Google Business Profile. Posts are generated based on a preset template and automatically include your post’s featured image, keeping your profile fresh with minimal effort.

= Why choose Post to Google My Business? =
✅ **Create, edit, and delete posts** without leaving WordPress
✅ **Auto-publish** your latest WordPress posts, pages and custom post types to your Google Business Profile
✅ **Beautiful, clean posts** – Automatically strips unnecessary HTML, shortcodes, and visual editor clutter (Divi, WPBakery, etc.)
✅ **Multisite support** – Works on both network and site level
✅ **Third-party integration** – Publish from **Zapier, IFTTT, Integromat, ManageWP, MainWP**, and more
✅ **Secure & reliable** – Uses the official **Google My Business API** with **oAuth** authentication
✅ **Developer-friendly** – Hooks, filters, and WordPress-native functions for easy customization
✅ **Translation-ready** – Fully compatible with WPML and other translation plugins
✅ **Modern image support** – Works with **WebP**, **AVIF**, and other optimized formats
✅ **Gutenberg-compatible** – Works seamlessly with the Block Editor

= Time-saving features available in Premium: =

Upgrade to **Post to Google My Business premium** for **even more automation and flexibility**:
⭐ **Product support** – Create and manage **real** Google Business Profile **Products** from WooCommerce or other content
⭐ **Scheduled posts** – Plan and **automate future post publishing**
⭐ **Multi-location & Multi-account support** – Publish posts across **multiple GBP locations** at once
⭐ **Auto re-posting (Recycling)** – Keep your content fresh by **automatically republishing** posts at set intervals
⭐ **Category- & tag-based auto-publishing** – Control exactly what gets posted
⭐ **Spintax support** – Generate **unique** content variations to avoid duplication
⭐ **Evergreen content** – Randomly publish from a selection of your best content
⭐ **Post campaigns** – Create GMB posts that aren’t tied to a specific WordPress post or page
⭐ **Multiple auto-post Templates** – Customize and manage different posting styles
⭐ **Agency support** – Manage Google My Business posts for **multiple clients**

**[Learn more about Post to Google My Business Premium](https://digitaldistortion.dev/?utm_source=repository&utm_medium=link&utm_campaign=learn_more&utm_content=description)**

= Great support! =
We're here to help in case you're having trouble using Post to Google My Business. Just ask in the support forum and we'll get back to you ASAP. Feedback and ideas to improve the plugin are always welcome.

== Installation ==

Installing and configuring Post to Google My Business is easy!

1. Upload the plugin files to the `/wp-content/plugins/post-to-google-my-business` directory, or install it through the **Plugins** section within your WordPress Dashboard.
2. Activate the plugin through the **Plugins** section in WordPress
3. Go to the **Post to GMB** > **Settings** > **Google Settings** page to configure the plugin
4. To allow your website to post to your Google Business Profile on your behalf, click **Connect to Google Business Profile**. Confirm the authorization using the Google account that holds the business location(s) you want to use. Make sure you check the checkbox to allow the plugin to manage your GMB locations.
5. You will be redirected back to the settings page. Select your business location in the **Default Location** section and press **Save Changes**.
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

= 3.2.5 =
* Bump minimum PHP version to 7.1
* Added: Link to post variables article near post text field
* Update: Freemius SDK 2.11 > 2.12
* Fix: Notices related to WpDateTime on PHP8 (Use native WP functions)

= 3.2.4 =
* Improved: Design of admin notices
* Improved: Hide location syncing error after successful sync
* Improved: Show more descriptive error when permission to manage locations hasn't been granted during authentication
* Improved: Route Google JWK keyset through backend instead of calling the Google URL directly
* Fix: Show error in locations field when refreshing of locations fails
* Fix: Delete location and group cache MySQL tables on plugin uninstall

= 3.2.3 =
* Improved: Error handling for retrieval of Google public keys
* Fix: Incorrect error message when trying to load uncached location

= 3.2.2 =
* Improved: Location cache and performance on accounts with large amounts of locations
* Fix: Pagination not working on created posts dialog
* Fix: Post being potentially created in context of wrong site on multisite
* Update: Freemius SDK

> **Premium**
>
> * Improved: Performance of "created posts" CSV download when dealing with many posts

= 3.1.28 =
* Tested on WordPress 6.7
* Update: Freemius SDK 2.9.0

> **Premium**
>
> * Fix: "Refresh locations" button not working on Auto-post settings page

= 3.1.27 =
* Added: AVIF image conversion, fall back on original uploaded image if it matches GMB requirements

= 3.1.26 =
* Clean up JS dependencies
* Test on WordPress 6.6
* Update Freemius SDK

== Upgrade Notice ==