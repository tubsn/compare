<?php

namespace app\importer;

use \flundr\cache\RequestCache;

class DPA_Drive_User
{

	const API_BASE_URL = 'https://user-api.me.drive-news.de';
	private $apiUser = DRIVE_USER_API_USERNAME;
	private $apiPassword = DRIVE_USER_API_PASSWORD;
	private $bearerToken = null;

	public function __construct() {
		$this->generate_bearer_token();
	}

	public function get($id) {

		$cache = new RequestCache('reader-' . $id , 60 * 60);

		$user = $cache->get();

		if ($user) {return $user;}

		$user = $this->curl('/user?id='.$id);

		if (empty($user)) {throw new \Exception("Unknown Error");}
		if (isset($user['detail'])) {throw new \Exception("User Not Found");} // Detail contains Error Message

		$cache->save($user);
		return $user;
	}


	private function generate_bearer_token() {

		$cache = new RequestCache('userbearertoken', 3 * 60 * 60);
		$cache->cacheDirectory = ROOT . 'cache' . DIRECTORY_SEPARATOR . 'tokens';
		$token = $cache->get();

		if ($token) {
			$this->bearerToken = $token;
			return;
		}

		$auth = [
			'username' => $this->apiUser,
			'password' => $this->apiPassword
		];

		$response = $this->curl_post('/token', $auth);
		$this->bearerToken = $response['access_token'];
		$cache->save($this->bearerToken);

	}


	private function curl($path) {

		$url = DPA_Drive_User::API_BASE_URL . $path;
		$authorization = 'Authorization: Bearer ' . $this->bearerToken;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		curl_close ($ch);
		return json_decode($recievedData, true);

	}



	private function curl_post($path, $auth) {

		$url = DPA_Drive_User::API_BASE_URL . $path;

		$auth = http_build_query($auth);

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $auth);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$recievedData = curl_exec($ch);
		if ($recievedData === false) {
			dd(curl_error($ch));
		}

		curl_close ($ch);
		return json_decode($recievedData, true);

	}

}
