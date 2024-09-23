<?php


namespace PGMB\API;


use PGMB\Google\LocalPost;
use PGMB\Google\LocalPostEditMask;
use UnexpectedValueException;
use WP_Error;

class GoogleMyBusiness {
	protected $access_token;

	protected $transport;

	public function __construct(\WP_Http $transport){
		$this->transport = $transport;
	}

	public function set_access_token($token){
		$this->access_token = $token;
	}

	protected function do_request($url, $query_args = [], $method = 'GET', $body = []){
		$url = add_query_arg($query_args, $url);
		$response = $this->transport->request($url, [
			'headers'	=> [
				'Content-Type' => 'application/json',
				'Authorization' => "Bearer {$this->access_token}"
			],
			'method'    => $method,
			'body'      => $body ? json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : null,
			'timeout'   => 20
		]);
		return $this->handle_response($response);
	}

	protected function handle_response($response){
		if ($response instanceof WP_Error) {
			throw new UnexpectedValueException($response->get_error_message());
		}

		$data = json_decode($response['body']);
		if(!$data){
			throw new UnexpectedValueException(__('Could not parse JSON response from Google API.', 'post-to-google-my-business'));
		}

		if(!isset($data->error)){
			return $data;
		}elseif(is_object($data->error)) {
			throw new GoogleAPIError( $data );
		}else{
			throw new UnexpectedValueException((string)$data->error);
		}
	}

	public function list_accounts($parentAccount = '', $pageSize = 20, $pageToken = '', $filter = ''){
		return $this->do_request('https://mybusinessaccountmanagement.googleapis.com/v1/accounts', [
			'parentAccount' => $parentAccount,
			'pageSize'      => $pageSize,
			'pageToken'     => $pageToken,
			'filter'        => $filter,
		]);
	}

	public function list_locations($parent, $pageSize = 100, $pageToken = '', $filter = '', $orderBy = '', $readMask = ''){
		return $this->do_request("https://mybusinessbusinessinformation.googleapis.com/v1/{$parent}/locations", [
			'pageSize'      => $pageSize,
			'pageToken'     => $pageToken,
			'filter'        => $filter,
			'orderBy'       => $orderBy,
			'readMask'      => $readMask,
		]);
	}

	public function get_location($name, $readMask = ''){
		return $this->do_request("https://mybusinessbusinessinformation.googleapis.com/v1/{$name}", [
			'readMask'      => $readMask,
		]);
	}

	public function get_post($name){
		return $this->do_request("https://mybusiness.googleapis.com/v4/{$name}");
	}

	public function patch_post($name, LocalPost $localPost, LocalPostEditMask $updateMask){
		return $this->do_request("https://mybusiness.googleapis.com/v4/{$name}",
			[
				'updateMask' => $updateMask->getMask()
			],
			'PATCH',
			$localPost
		);
	}

	public function create_post($parent, $localPost){
		return $this->do_request("https://mybusiness.googleapis.com/v4/{$parent}/localPosts", [], 'POST', $localPost);
	}

	public function delete_post($name){
		return $this->do_request("https://mybusiness.googleapis.com/v4/{$name}", [], 'DELETE');
	}

	public function get_account($name){
		return $this->do_request("https://mybusinessaccountmanagement.googleapis.com/v1/{$name}");
	}

	public function revoke_token($refresh_token){
		return $this->transport->get("https://accounts.google.com/o/oauth2/revoke?token={$refresh_token}");
	}


}
