<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitea067c724201b211e291337ce8bd0abb
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TypistTech\\WPAdminNotices\\' => 26,
        ),
        'R' => 
        array (
            'Rarst\\WordPress\\DateTime\\' => 25,
        ),
        'P' => 
        array (
            'PGMB\\' => 5,
        ),
        'H' => 
        array (
            'Html2Text\\' => 10,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'C' => 
        array (
            'Cron\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TypistTech\\WPAdminNotices\\' => 
        array (
            0 => __DIR__ . '/..' . '/typisttech/wp-admin-notices/src',
        ),
        'Rarst\\WordPress\\DateTime\\' => 
        array (
            0 => __DIR__ . '/..' . '/rarst/wpdatetime/src',
        ),
        'PGMB\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Html2Text\\' => 
        array (
            0 => __DIR__ . '/..' . '/html2text/html2text/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Cron\\' => 
        array (
            0 => __DIR__ . '/..' . '/dragonmantank/cron-expression/src/Cron',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'PGMB\\API\\APIInterface' => __DIR__ . '/../..' . '/src/API/APIInterface.php',
        'PGMB\\API\\CachedGoogleMyBusiness' => __DIR__ . '/../..' . '/src/API/CachedGoogleMyBusiness.php',
        'PGMB\\API\\GoogleAPIError' => __DIR__ . '/../..' . '/src/API/GoogleAPIError.php',
        'PGMB\\API\\GoogleMyBusiness' => __DIR__ . '/../..' . '/src/API/GoogleMyBusiness.php',
        'PGMB\\API\\ProxyAuthenticationAPI' => __DIR__ . '/../..' . '/src/API/ProxyAuthenticationAPI.php',
        'PGMB\\API\\ProxyGMBAPI' => __DIR__ . '/../..' . '/src/API/ProxyGMBAPI.php',
        'PGMB\\Admin\\AbstractPage' => __DIR__ . '/../..' . '/src/Admin/AbstractPage.php',
        'PGMB\\Admin\\AdminPage' => __DIR__ . '/../..' . '/src/Admin/AdminPage.php',
        'PGMB\\Admin\\AjaxCallbackInterface' => __DIR__ . '/../..' . '/src/Admin/AjaxCallbackInterface.php',
        'PGMB\\Admin\\AutoPostTemplateUpsell' => __DIR__ . '/../..' . '/src/Admin/AutoPostTemplateUpsell.php',
        'PGMB\\Admin\\ConfigurablePageInterface' => __DIR__ . '/../..' . '/src/Admin/ConfigurablePageInterface.php',
        'PGMB\\Admin\\DashboardPage' => __DIR__ . '/../..' . '/src/Admin/DashboardPage.php',
        'PGMB\\Admin\\EnqueuesScriptsInterface' => __DIR__ . '/../..' . '/src/Admin/EnqueuesScriptsInterface.php',
        'PGMB\\Admin\\PostCampaignsUpsell' => __DIR__ . '/../..' . '/src/Admin/PostCampaignsUpsell.php',
        'PGMB\\ApiCache\\Group' => __DIR__ . '/../..' . '/src/ApiCache/Group.php',
        'PGMB\\ApiCache\\GroupCacheRepository' => __DIR__ . '/../..' . '/src/ApiCache/GroupCacheRepository.php',
        'PGMB\\ApiCache\\Location' => __DIR__ . '/../..' . '/src/ApiCache/Location.php',
        'PGMB\\ApiCache\\LocationCacheRepository' => __DIR__ . '/../..' . '/src/ApiCache/LocationCacheRepository.php',
        'PGMB\\BackgroundProcessing\\AccountSyncQueueItem' => __DIR__ . '/../..' . '/src/BackgroundProcessing/AccountSyncQueueItem.php',
        'PGMB\\BackgroundProcessing\\AccountsSyncQueueItem' => __DIR__ . '/../..' . '/src/BackgroundProcessing/AccountsSyncQueueItem.php',
        'PGMB\\BackgroundProcessing\\GroupSyncQueueItem' => __DIR__ . '/../..' . '/src/BackgroundProcessing/GroupSyncQueueItem.php',
        'PGMB\\BackgroundProcessing\\LocationSyncProcess' => __DIR__ . '/../..' . '/src/BackgroundProcessing/LocationSyncProcess.php',
        'PGMB\\BackgroundProcessing\\LocationSyncQueueItem' => __DIR__ . '/../..' . '/src/BackgroundProcessing/LocationSyncQueueItem.php',
        'PGMB\\BackgroundProcessing\\PostPublishProcess' => __DIR__ . '/../..' . '/src/BackgroundProcessing/PostPublishProcess.php',
        'PGMB\\Components\\BusinessSelector' => __DIR__ . '/../..' . '/src/Components/BusinessSelector.php',
        'PGMB\\Components\\GooglePostEntityListTable' => __DIR__ . '/../..' . '/src/Components/GooglePostEntityListTable.php',
        'PGMB\\Components\\PostEditor' => __DIR__ . '/../..' . '/src/Components/PostEditor.php',
        'PGMB\\Components\\PrefixedListTable' => __DIR__ . '/../..' . '/src/Components/PrefixedListTable.php',
        'PGMB\\Components\\SubPostListTable' => __DIR__ . '/../..' . '/src/Components/SubPostListTable.php',
        'PGMB\\Configuration\\AdminConfiguration' => __DIR__ . '/../..' . '/src/Configuration/AdminConfiguration.php',
        'PGMB\\Configuration\\BackgroundProcessConfiguration' => __DIR__ . '/../..' . '/src/Configuration/BackgroundProcessConfiguration.php',
        'PGMB\\Configuration\\ComponentConfiguration' => __DIR__ . '/../..' . '/src/Configuration/ComponentConfiguration.php',
        'PGMB\\Configuration\\EventManagementConfiguration' => __DIR__ . '/../..' . '/src/Configuration/EventManagementConfiguration.php',
        'PGMB\\Configuration\\MetaboxConfiguration' => __DIR__ . '/../..' . '/src/Configuration/MetaboxConfiguration.php',
        'PGMB\\Configuration\\NotificationManagerConfiguration' => __DIR__ . '/../..' . '/src/Configuration/NotificationManagerConfiguration.php',
        'PGMB\\Configuration\\PostTypeConfiguration' => __DIR__ . '/../..' . '/src/Configuration/PostTypeConfiguration.php',
        'PGMB\\Configuration\\ProxyAPIConfiguration' => __DIR__ . '/../..' . '/src/Configuration/ProxyAPIConfiguration.php',
        'PGMB\\Configuration\\RESTAPIConfiguration' => __DIR__ . '/../..' . '/src/Configuration/RESTAPIConfiguration.php',
        'PGMB\\Configuration\\SettingsConfiguration' => __DIR__ . '/../..' . '/src/Configuration/SettingsConfiguration.php',
        'PGMB\\DependencyInjection\\Container' => __DIR__ . '/../..' . '/src/DependencyInjection/Container.php',
        'PGMB\\DependencyInjection\\ContainerConfigurationInterface' => __DIR__ . '/../..' . '/src/DependencyInjection/ContainerConfigurationInterface.php',
        'PGMB\\EventManagement\\AbstractEventManagerAwareSubscriber' => __DIR__ . '/../..' . '/src/EventManagement/AbstractEventManagerAwareSubscriber.php',
        'PGMB\\EventManagement\\EventManager' => __DIR__ . '/../..' . '/src/EventManagement/EventManager.php',
        'PGMB\\EventManagement\\EventManagerAwareSubscriberInterface' => __DIR__ . '/../..' . '/src/EventManagement/EventManagerAwareSubscriberInterface.php',
        'PGMB\\EventManagement\\SubscriberInterface' => __DIR__ . '/../..' . '/src/EventManagement/SubscriberInterface.php',
        'PGMB\\FormFields' => __DIR__ . '/../..' . '/src/FormFields.php',
        'PGMB\\GoogleUserManager' => __DIR__ . '/../..' . '/src/GoogleUserManager.php',
        'PGMB\\Google\\AbstractGoogleJsonObject' => __DIR__ . '/../..' . '/src/Google/AbstractGoogleJsonObject.php',
        'PGMB\\Google\\CallToAction' => __DIR__ . '/../..' . '/src/Google/CallToAction.php',
        'PGMB\\Google\\Date' => __DIR__ . '/../..' . '/src/Google/Date.php',
        'PGMB\\Google\\LocalPost' => __DIR__ . '/../..' . '/src/Google/LocalPost.php',
        'PGMB\\Google\\LocalPostEditMask' => __DIR__ . '/../..' . '/src/Google/LocalPostEditMask.php',
        'PGMB\\Google\\LocalPostEvent' => __DIR__ . '/../..' . '/src/Google/LocalPostEvent.php',
        'PGMB\\Google\\LocalPostJsonDeserializeInterface' => __DIR__ . '/../..' . '/src/Google/LocalPostJsonDeserializeInterface.php',
        'PGMB\\Google\\LocalPostOffer' => __DIR__ . '/../..' . '/src/Google/LocalPostOffer.php',
        'PGMB\\Google\\MediaItem' => __DIR__ . '/../..' . '/src/Google/MediaItem.php',
        'PGMB\\Google\\Money' => __DIR__ . '/../..' . '/src/Google/Money.php',
        'PGMB\\Google\\NormalizeLocationName' => __DIR__ . '/../..' . '/src/Google/NormalizeLocationName.php',
        'PGMB\\Google\\PublishedLocalPost' => __DIR__ . '/../..' . '/src/Google/PublishedLocalPost.php',
        'PGMB\\Google\\TimeInterval' => __DIR__ . '/../..' . '/src/Google/TimeInterval.php',
        'PGMB\\Google\\TimeOfDay' => __DIR__ . '/../..' . '/src/Google/TimeOfDay.php',
        'PGMB\\MbString' => __DIR__ . '/../..' . '/src/MbString.php',
        'PGMB\\Metabox\\JSMetaboxInterface' => __DIR__ . '/../..' . '/src/Metabox/JSMetaboxInterface.php',
        'PGMB\\Metabox\\MetaboxInterface' => __DIR__ . '/../..' . '/src/Metabox/MetaboxInterface.php',
        'PGMB\\Metabox\\PostCreationMetabox' => __DIR__ . '/../..' . '/src/Metabox/PostCreationMetabox.php',
        'PGMB\\Metabox\\StorableDataMetaboxInterface' => __DIR__ . '/../..' . '/src/Metabox/StorableDataMetaboxInterface.php',
        'PGMB\\Notifications\\BasicNotification' => __DIR__ . '/../..' . '/src/Notifications/BasicNotification.php',
        'PGMB\\Notifications\\FeatureNotification' => __DIR__ . '/../..' . '/src/Notifications/FeatureNotification.php',
        'PGMB\\Notifications\\Notification' => __DIR__ . '/../..' . '/src/Notifications/Notification.php',
        'PGMB\\Notifications\\NotificationManager' => __DIR__ . '/../..' . '/src/Notifications/NotificationManager.php',
        'PGMB\\ParseFormFields' => __DIR__ . '/../..' . '/src/ParseFormFields.php',
        'PGMB\\Placeholders\\LocationVariables' => __DIR__ . '/../..' . '/src/Placeholders/LocationVariables.php',
        'PGMB\\Placeholders\\PostPermalink' => __DIR__ . '/../..' . '/src/Placeholders/PostPermalink.php',
        'PGMB\\Placeholders\\PostVariables' => __DIR__ . '/../..' . '/src/Placeholders/PostVariables.php',
        'PGMB\\Placeholders\\SiteVariables' => __DIR__ . '/../..' . '/src/Placeholders/SiteVariables.php',
        'PGMB\\Placeholders\\UserVariables' => __DIR__ . '/../..' . '/src/Placeholders/UserVariables.php',
        'PGMB\\Placeholders\\VariableInterface' => __DIR__ . '/../..' . '/src/Placeholders/VariableInterface.php',
        'PGMB\\Placeholders\\WooCommerceVariables' => __DIR__ . '/../..' . '/src/Placeholders/WooCommerceVariables.php',
        'PGMB\\Plugin' => __DIR__ . '/../..' . '/src/Plugin.php',
        'PGMB\\PostTypes\\AbstractRepository' => __DIR__ . '/../..' . '/src/PostTypes/AbstractRepository.php',
        'PGMB\\PostTypes\\AutoPostFactory' => __DIR__ . '/../..' . '/src/PostTypes/AutoPostFactory.php',
        'PGMB\\PostTypes\\EntityInterface' => __DIR__ . '/../..' . '/src/PostTypes/EntityInterface.php',
        'PGMB\\PostTypes\\GooglePostEntity' => __DIR__ . '/../..' . '/src/PostTypes/GooglePostEntity.php',
        'PGMB\\PostTypes\\GooglePostEntityRepository' => __DIR__ . '/../..' . '/src/PostTypes/GooglePostEntityRepository.php',
        'PGMB\\PostTypes\\PostTypeDefinition' => __DIR__ . '/../..' . '/src/PostTypes/PostTypeDefinition.php',
        'PGMB\\PostTypes\\SubPost' => __DIR__ . '/../..' . '/src/PostTypes/SubPost.php',
        'PGMB\\PostTypes\\SubPostDefinition' => __DIR__ . '/../..' . '/src/PostTypes/SubPostDefinition.php',
        'PGMB\\PostTypes\\SubPostRepository' => __DIR__ . '/../..' . '/src/PostTypes/SubPostRepository.php',
        'PGMB\\Premium\\API\\GMBCookieAPI' => __DIR__ . '/../..' . '/src/Premium/API/GMBCookieAPI.php',
        'PGMB\\Premium\\Admin\\PremiumAdminPage' => __DIR__ . '/../..' . '/src/Premium/Admin/PremiumAdminPage.php',
        'PGMB\\Premium\\BackgroundProcessing\\ProductPublishProcess' => __DIR__ . '/../..' . '/src/Premium/BackgroundProcessing/ProductPublishProcess.php',
        'PGMB\\Premium\\Components\\MultiAccountBusinessSelector' => __DIR__ . '/../..' . '/src/Premium/Components/MultiAccountBusinessSelector.php',
        'PGMB\\Premium\\Components\\PremiumPostEditor' => __DIR__ . '/../..' . '/src/Premium/Components/PremiumPostEditor.php',
        'PGMB\\Premium\\Google\\Product' => __DIR__ . '/../..' . '/src/Premium/Google/Product.php',
        'PGMB\\Premium\\Metabox\\AutopostTemplateMetabox' => __DIR__ . '/../..' . '/src/Premium/Metabox/AutopostTemplateMetabox.php',
        'PGMB\\Premium\\PostTypes\\PostCampaign' => __DIR__ . '/../..' . '/src/Premium/PostTypes/PostCampaign.php',
        'PGMB\\Premium\\PostTypes\\PostTypeAutoPostTemplate' => __DIR__ . '/../..' . '/src/Premium/PostTypes/PostTypeAutoPostTemplate.php',
        'PGMB\\Premium\\Subscribers\\CSVAdminPostSubscriber' => __DIR__ . '/../..' . '/src/Premium/Subscribers/CSVAdminPostSubscriber.php',
        'PGMB\\Premium\\Subscribers\\EvergreenBulkActionSubscriber' => __DIR__ . '/../..' . '/src/Premium/Subscribers/EvergreenBulkActionSubscriber.php',
        'PGMB\\Premium\\Subscribers\\PremiumEvergreenScheduleSubscriber' => __DIR__ . '/../..' . '/src/Premium/Subscribers/PremiumEvergreenScheduleSubscriber.php',
        'PGMB\\Premium\\Subscribers\\PremiumPostTypesSubscriber' => __DIR__ . '/../..' . '/src/Premium/Subscribers/PremiumPostTypesSubscriber.php',
        'PGMB\\Premium\\Subscribers\\PremiumSubmitboxMetaSubscriber' => __DIR__ . '/../..' . '/src/Premium/Subscribers/PremiumSubmitboxMetaSubscriber.php',
        'PGMB\\Premium\\Subscribers\\TaxonomySubscriber' => __DIR__ . '/../..' . '/src/Premium/Subscribers/TaxonomySubscriber.php',
        'PGMB\\Premium\\Taxonomies\\CampaignCategoryTaxonomy' => __DIR__ . '/../..' . '/src/Premium/Taxonomies/CampaignCategoryTaxonomy.php',
        'PGMB\\Premium\\Taxonomies\\CampaignTagTaxonomy' => __DIR__ . '/../..' . '/src/Premium/Taxonomies/CampaignTagTaxonomy.php',
        'PGMB\\Premium\\Taxonomies\\ListedTaxonomyField' => __DIR__ . '/../..' . '/src/Premium/Taxonomies/ListedTaxonomyField.php',
        'PGMB\\Premium\\Taxonomies\\TaxonomyField' => __DIR__ . '/../..' . '/src/Premium/Taxonomies/TaxonomyField.php',
        'PGMB\\Premium\\Taxonomies\\TaxonomyInterface' => __DIR__ . '/../..' . '/src/Premium/Taxonomies/TaxonomyInterface.php',
        'PGMB\\REST\\GetAccountsRoute' => __DIR__ . '/../..' . '/src/REST/GetAccountsRoute.php',
        'PGMB\\REST\\RouteInterface' => __DIR__ . '/../..' . '/src/REST/RouteInterface.php',
        'PGMB\\RateLimiter\\RateLimiter' => __DIR__ . '/../..' . '/src/RateLimiter/RateLimiter.php',
        'PGMB\\Subscriber\\AdminPageSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/AdminPageSubscriber.php',
        'PGMB\\Subscriber\\AdminStyleSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/AdminStyleSubscriber.php',
        'PGMB\\Subscriber\\AuthenticationAdminPostSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/AuthenticationAdminPostSubscriber.php',
        'PGMB\\Subscriber\\AutoPostSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/AutoPostSubscriber.php',
        'PGMB\\Subscriber\\BlockEditorAssetSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/BlockEditorAssetSubscriber.php',
        'PGMB\\Subscriber\\CalendarFeedAjaxSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/CalendarFeedAjaxSubscriber.php',
        'PGMB\\Subscriber\\ConditionalNotificationSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/ConditionalNotificationSubscriber.php',
        'PGMB\\Subscriber\\MetaboxSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/MetaboxSubscriber.php',
        'PGMB\\Subscriber\\PostEntityListAjaxSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/PostEntityListAjaxSubscriber.php',
        'PGMB\\Subscriber\\PostStatusSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/PostStatusSubscriber.php',
        'PGMB\\Subscriber\\PostSubmitBoxSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/PostSubmitBoxSubscriber.php',
        'PGMB\\Subscriber\\PostTypesSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/PostTypesSubscriber.php',
        'PGMB\\Subscriber\\RestAPISubscriber' => __DIR__ . '/../..' . '/src/Subscriber/RestAPISubscriber.php',
        'PGMB\\Subscriber\\SiteHealthSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/SiteHealthSubscriber.php',
        'PGMB\\Subscriber\\SubPostListAjaxSubscriber' => __DIR__ . '/../..' . '/src/Subscriber/SubPostListAjaxSubscriber.php',
        'PGMB\\Upgrader\\DistributedUpgrade' => __DIR__ . '/../..' . '/src/Upgrader/DistributedUpgrade.php',
        'PGMB\\Upgrader\\Upgrade' => __DIR__ . '/../..' . '/src/Upgrader/Upgrade.php',
        'PGMB\\Upgrader\\UpgradeBackgroundProcess' => __DIR__ . '/../..' . '/src/Upgrader/UpgradeBackgroundProcess.php',
        'PGMB\\Upgrader\\Upgrade_2_2_11' => __DIR__ . '/../..' . '/src/Upgrader/Upgrade_2_2_11.php',
        'PGMB\\Upgrader\\Upgrade_2_2_3' => __DIR__ . '/../..' . '/src/Upgrader/Upgrade_2_2_3.php',
        'PGMB\\Upgrader\\Upgrade_3_0_0' => __DIR__ . '/../..' . '/src/Upgrader/Upgrade_3_0_0.php',
        'PGMB\\Upgrader\\Upgrade_3_1_2' => __DIR__ . '/../..' . '/src/Upgrader/Upgrade_3_1_2.php',
        'PGMB\\Upgrader\\Upgrade_3_1_6' => __DIR__ . '/../..' . '/src/Upgrader/Upgrade_3_1_6.php',
        'PGMB\\Upgrader\\Upgrade_3_2_0' => __DIR__ . '/../..' . '/src/Upgrader/Upgrade_3_2_0.php',
        'PGMB\\Upgrader\\Upgrader' => __DIR__ . '/../..' . '/src/Upgrader/Upgrader.php',
        'PGMB\\Util\\UTF16CodeUnitsUtil' => __DIR__ . '/../..' . '/src/Util/UTF16CodeUnitsUtil.php',
        'PGMB\\Vendor\\Cron\\AbstractField' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/AbstractField.php',
        'PGMB\\Vendor\\Cron\\CronExpression' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/CronExpression.php',
        'PGMB\\Vendor\\Cron\\DayOfMonthField' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/DayOfMonthField.php',
        'PGMB\\Vendor\\Cron\\DayOfWeekField' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/DayOfWeekField.php',
        'PGMB\\Vendor\\Cron\\FieldFactory' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/FieldFactory.php',
        'PGMB\\Vendor\\Cron\\FieldInterface' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/FieldInterface.php',
        'PGMB\\Vendor\\Cron\\HoursField' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/HoursField.php',
        'PGMB\\Vendor\\Cron\\MinutesField' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/MinutesField.php',
        'PGMB\\Vendor\\Cron\\MonthField' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/MonthField.php',
        'PGMB\\Vendor\\Cron\\YearField' => __DIR__ . '/../..' . '/vendor-prefixed/dragonmantank/cron-expression/src/Cron/YearField.php',
        'PGMB\\Vendor\\Firebase\\JWT\\BeforeValidException' => __DIR__ . '/../..' . '/vendor-prefixed/firebase/php-jwt/src/BeforeValidException.php',
        'PGMB\\Vendor\\Firebase\\JWT\\ExpiredException' => __DIR__ . '/../..' . '/vendor-prefixed/firebase/php-jwt/src/ExpiredException.php',
        'PGMB\\Vendor\\Firebase\\JWT\\JWK' => __DIR__ . '/../..' . '/vendor-prefixed/firebase/php-jwt/src/JWK.php',
        'PGMB\\Vendor\\Firebase\\JWT\\JWT' => __DIR__ . '/../..' . '/vendor-prefixed/firebase/php-jwt/src/JWT.php',
        'PGMB\\Vendor\\Firebase\\JWT\\Key' => __DIR__ . '/../..' . '/vendor-prefixed/firebase/php-jwt/src/Key.php',
        'PGMB\\Vendor\\Firebase\\JWT\\SignatureInvalidException' => __DIR__ . '/../..' . '/vendor-prefixed/firebase/php-jwt/src/SignatureInvalidException.php',
        'PGMB\\Vendor\\Html2Text\\Html2Text' => __DIR__ . '/../..' . '/vendor-prefixed/html2text/html2text/src/Html2Text.php',
        'PGMB\\Vendor\\Rarst\\WordPress\\DateTime\\WpDateTime' => __DIR__ . '/../..' . '/vendor-prefixed/rarst/wpdatetime/src/WpDateTime.php',
        'PGMB\\Vendor\\Rarst\\WordPress\\DateTime\\WpDateTimeImmutable' => __DIR__ . '/../..' . '/vendor-prefixed/rarst/wpdatetime/src/WpDateTimeImmutable.php',
        'PGMB\\Vendor\\Rarst\\WordPress\\DateTime\\WpDateTimeInterface' => __DIR__ . '/../..' . '/vendor-prefixed/rarst/wpdatetime/src/WpDateTimeInterface.php',
        'PGMB\\Vendor\\Rarst\\WordPress\\DateTime\\WpDateTimeTrait' => __DIR__ . '/../..' . '/vendor-prefixed/rarst/wpdatetime/src/WpDateTimeTrait.php',
        'PGMB\\Vendor\\Rarst\\WordPress\\DateTime\\WpDateTimeZone' => __DIR__ . '/../..' . '/vendor-prefixed/rarst/wpdatetime/src/WpDateTimeZone.php',
        'PGMB\\Vendor\\Spintax' => __DIR__ . '/../..' . '/src/Vendor/Spintax.php',
        'PGMB\\Vendor\\TypistTech\\WPAdminNotices\\AbstractNotice' => __DIR__ . '/../..' . '/vendor-prefixed/typisttech/wp-admin-notices/src/AbstractNotice.php',
        'PGMB\\Vendor\\TypistTech\\WPAdminNotices\\Factory' => __DIR__ . '/../..' . '/vendor-prefixed/typisttech/wp-admin-notices/src/Factory.php',
        'PGMB\\Vendor\\TypistTech\\WPAdminNotices\\Notice' => __DIR__ . '/../..' . '/vendor-prefixed/typisttech/wp-admin-notices/src/Notice.php',
        'PGMB\\Vendor\\TypistTech\\WPAdminNotices\\NoticeInterface' => __DIR__ . '/../..' . '/vendor-prefixed/typisttech/wp-admin-notices/src/NoticeInterface.php',
        'PGMB\\Vendor\\TypistTech\\WPAdminNotices\\Notifier' => __DIR__ . '/../..' . '/vendor-prefixed/typisttech/wp-admin-notices/src/Notifier.php',
        'PGMB\\Vendor\\TypistTech\\WPAdminNotices\\StickyNotice' => __DIR__ . '/../..' . '/vendor-prefixed/typisttech/wp-admin-notices/src/StickyNotice.php',
        'PGMB\\Vendor\\TypistTech\\WPAdminNotices\\Store' => __DIR__ . '/../..' . '/vendor-prefixed/typisttech/wp-admin-notices/src/Store.php',
        'PGMB\\Vendor\\TypistTech\\WPAdminNotices\\StoreInterface' => __DIR__ . '/../..' . '/vendor-prefixed/typisttech/wp-admin-notices/src/StoreInterface.php',
        'PGMB\\Vendor\\WeDevsSettingsAPI' => __DIR__ . '/../..' . '/src/Vendor/WeDevsSettingsAPI.php',
        'PGMB\\WordPressInitializable' => __DIR__ . '/../..' . '/src/WordPressInitializable.php',
        'PGMB_Vendor_WP_Async_Request' => __DIR__ . '/../..' . '/vendor-prefixed/deliciousbrains/wp-background-processing/classes/wp-async-request.php',
        'PGMB_Vendor_WP_Background_Process' => __DIR__ . '/../..' . '/vendor-prefixed/deliciousbrains/wp-background-processing/classes/wp-background-process.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitea067c724201b211e291337ce8bd0abb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitea067c724201b211e291337ce8bd0abb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitea067c724201b211e291337ce8bd0abb::$classMap;

        }, null, ClassLoader::class);
    }
}
