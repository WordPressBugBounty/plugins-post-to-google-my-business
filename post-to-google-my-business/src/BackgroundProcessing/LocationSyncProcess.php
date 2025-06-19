<?php

namespace PGMB\BackgroundProcessing;

use PGMB\API\ProxyAuthenticationAPI;
use PGMB\API\ProxyGMBAPI;
use PGMB\Notices\BrandedStickyNotice;
use PGMB\Vendor\TypistTech\WPAdminNotices\AbstractNotice;
use PGMB\Vendor\TypistTech\WPAdminNotices\StickyNotice;
use PGMB\Vendor\TypistTech\WPAdminNotices\Store as AdminNoticeStore;
use PGMB_Vendor_WP_Background_Process as BackgroundProcess;

class LocationSyncProcess extends BackgroundProcess {

	protected $action = 'pgmb_sync_locations';

	/**
	 * @var ProxyGMBAPI
	 */
	private $api;

	/**
	 * @var ProxyAuthenticationAPI
	 */
	private $auth_api;

	/**
	 * @var AdminNoticeStore
	 */
	private $admin_notice_store;

	protected $allowed_batch_data_classes = [
		AccountSyncQueueItem::class,
		LocationSyncQueueItem::class,
		GroupSyncQueueItem::class,
	];

	public function __construct(ProxyGMBAPI $api, ProxyAuthenticationAPI $auth_api, AdminNoticeStore $admin_notice_store) {
		parent::__construct();
		$this->api = $api;
		$this->auth_api = $auth_api;
		$this->admin_notice_store = $admin_notice_store;
	}

	/**
	 * @inheritDoc
	 */
	protected function task( $item ) {
		if(!$item instanceof AccountSyncQueueItem){
			return false;
		}

		$account_id = $item->get_account_id();

		try{
			$this->admin_notice_store->delete('location_import_error');
			delete_option('pgmb_location_import_last_error_'.$account_id);
			$this->api->set_access_token($this->auth_api->get_access_token($account_id));
			if($item instanceof LocationSyncQueueItem){
				return $this->sync_locations($item);
			}elseif($item instanceof GroupSyncQueueItem){
				return $this->sync_groups($item);
			}
		}catch(\Throwable $e){
			$error_message = sprintf(__("Something went wrong trying to load the Google Business Profile locations for this account: %s", 'post-to-google-my-business'), $e->getMessage());
			$link = sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=pgmb_settings#mbp_google_settings')), __('Check Google account settings', 'post-to-google-my-business'));
			$this->admin_notice_store->add(new BrandedStickyNotice('location_import_error', $error_message, $link, AbstractNotice::ERROR));
			$this->cancel();
			update_option('pgmb_location_import_last_error_'.$account_id, $error_message);
			return false;
		}

		update_option('pgmb_account_refresh_'.$account_id, current_time('mysql', true));

		return new GroupSyncQueueItem($account_id);
	}

	protected function update_in_latest_import($account_id){
		$latest_import_date = get_option('pgmb_account_refresh_'.$account_id);

		if(!$latest_import_date){
			return;
		}

		global $wpdb;

		//Join the groups with the locations table by a specific account ID so only the locations belonging to the specified account will be updated
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}pgmb_location_cache l INNER JOIN {$wpdb->prefix}pgmb_group_cache g ON l.group_id=g.id SET l.in_latest_import=0 WHERE g.account_id=%s AND l.imported_at < %s", $account_id, $latest_import_date));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}pgmb_group_cache SET in_latest_import=0 WHERE account_id=%s AND imported_at < %s", $account_id, $latest_import_date));

		delete_option('pgmb_account_refresh_'.$account_id);
	}

	/**
	 * @throws \Exception
	 */
	private function sync_groups( GroupSyncQueueItem $item ) {
		$request = $this->api->list_accounts('', 20, $item->getPageToken());

		global $wpdb;
		$placeholders = [];
		$values = [];

		$accounts = isset($request->accounts) && is_array($request->accounts) ? $request->accounts : null;
		if(!is_array($accounts) || count($accounts) < 1) {
			return false;
		}

		foreach($accounts as $account){
			$placeholders[] = "(%s, %s, %s, %s, %d)";
			$values[] = $item->get_account_id();
			$values[] = $account->name;
			$values[] = $account->accountName;
			$values[] = current_time('mysql', true);
			$values[] = 1;

			$this->push_to_queue(new LocationSyncQueueItem($item->get_account_id(), $account->name, null));
		}
		$this->save();

		$implode_placeholders = implode(',', $placeholders);

		$result = $wpdb->query( $wpdb->prepare(
			"INSERT INTO {$wpdb->prefix}pgmb_group_cache 
    				(
    					account_id, 
    				 	google_id, 
    				 	group_name,
    				 	imported_at,
    				 	in_latest_import
    				) VALUES
    				    {$implode_placeholders}
					ON DUPLICATE KEY UPDATE
	                	account_id = VALUES(account_id),
                        google_id = VALUES(google_id),
                        group_name = VALUES(group_name),
                        imported_at = VALUES(imported_at),
		                in_latest_import = VALUES(in_latest_import)
		            ",
			$values
		)
		);

		if($result === false){
			throw new \Exception("Failed to insert group cache: ".$wpdb->last_error);
		}

		$nextPageToken = isset($request->nextPageToken) && $request->nextPageToken ? $request->nextPageToken : null;
		if($nextPageToken){
			return new GroupSyncQueueItem($item->get_account_id(), $nextPageToken);
		}

		$this->update_in_latest_import($item->get_account_id());

		return false;
	}

	/**
	 * @throws \Exception
	 */
	private function sync_locations(LocationSyncQueueItem $item){
		global $wpdb;

		$group_id = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$wpdb->prefix}pgmb_group_cache WHERE google_id = %s", $item->get_parent()));
		if(!$group_id){
			throw new \Exception('Could not find group');
		}
		$readMask = 'name,languageCode,storeCode,title,websiteUri,storefrontAddress,metadata,serviceArea,regularHours,specialHours';

		$request = $this->api->list_locations($item->get_parent(), 100, $item->getPageToken(), null, null, $readMask);

		$placeholders = [];
		$values = [];

		$locations = isset($request->locations) && is_array($request->locations) ? $request->locations : null;
		if (!is_array( $locations ) || empty( $locations ) ) {
			return false;
		}

		foreach($locations as $location){
			$placeholders[] = "(%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d)";
			$values[] = $group_id->id;
			$values[] = $location->name;
			$values[] = !empty($location->storeCode) ? $location->storeCode : null;
			$values[] = $location->title;
			$values[] = !empty($location->languageCode) ? $location->languageCode : null;
			$values[] = !empty($location->websiteUri) ? $location->websiteUri : null;
			$values[] = !empty($location->regularHours) ? json_encode($location->regularHours) : null;
			$values[] = !empty($location->specialHours) ? json_encode($location->specialHours) : null;
			$values[] = !empty($location->labels) ? json_encode($location->labels) : null;
			$values[] = $location->metadata ? json_encode($location->metadata) : null;
			$values[] = !empty($location->storefrontAddress) ? json_encode($location->storefrontAddress) : null;
			$values[] = !empty($location->serviceArea) ? json_encode($location->serviceArea) : null;
			$values[] = current_time('mysql', true);
			$values[] = 1;
		}

		$implode_placeholders = implode(',', $placeholders);

		$result = $wpdb->query( $wpdb->prepare(
			"INSERT INTO {$wpdb->prefix}pgmb_location_cache
                (
					group_id,
					google_id,
					store_code,
					title,
					language_code,
					website_uri,
					regular_hours,
					special_hours,
					labels,
					metadata,
                    storefront_address,
					service_area,
					imported_at,
                 	in_latest_import
                ) VALUES
                    {$implode_placeholders}
				ON DUPLICATE KEY UPDATE
					group_id = VALUES(group_id),
	                google_id = VALUES(google_id),
                    store_code = VALUES(store_code),
                    title = VALUES(title),
                    language_code = VALUES(language_code),
                    website_uri = VALUES(website_uri),
                    regular_hours = VALUES(regular_hours),
                    special_hours = VALUES(special_hours),
                    labels = VALUES(labels),
                    metadata = VALUES(metadata),
                    storefront_address = VALUES(storefront_address),
                    service_area = VALUES(service_area),
                    imported_at = VALUES(imported_at),
                  	in_latest_import = VALUES(in_latest_import)
            ",
			$values
		)
		);

		if($result === false){
			throw new \Exception('Could not create location cache: '. $wpdb->last_error);
		}

		$nextPageToken = !empty($request->nextPageToken) ? $request->nextPageToken : null;
		if($nextPageToken){
			return new LocationSyncQueueItem($item->get_account_id(), $item->get_parent(), $nextPageToken);
		}

		//When all locations are imported from the account, update the in_latest_import flag
//		$this->update_in_latest_import($item->get_account_id());

		return false;
	}
}