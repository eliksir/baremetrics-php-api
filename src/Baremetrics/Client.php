<?php
namespace Baremetrics;

class Client {
	const LIVE_API_URL = 'https://api.baremetrics.com';
	const SANDBOX_API_URL = 'https://api-sandbox.baremetrics.com';

	private $apiKey;
	private $methods = ['cancel', 'create', 'delete', 'list', 'show', 'update'];

	/** @var \GuzzleHttp\Exception\ClientException $lastException */
	private $lastException;

	/**
	 * Create a new Baremetrics API client with given API key.
	 *
	 * By default creates a client against the API sandbox. Pass `true`
	 * for `$live` to create a client against the production API. The
	 * API key needs to match the API environment.
	 *
	 * @param string $apiKey The API key as retrieved from Baremetrics admin.
	 * @param bool $live     Whether or not the client should call the
	 *                       production API or not.
	 */
	public function __construct ($apiKey, $live = false) {
		$this->apiKey = $apiKey;
		$this->httpClient = new \GuzzleHttp\Client([
			'base_uri' => $live ? Client::LIVE_API_URL : Client::SANDBOX_API_URL,
		]);
	}

	/**
	 * Convenience handler to access resouce endpoints without having to
	 * create API resource instances manually.
	 *
	 * Allows call like `$client->listSubscriptions($sourceId)` instead of:
	 *   $subscriptions = new \Baremetrics\Subscription($client);
	 *   $subscriptions->retrieve();
	 *
	 * @param string $name Method name, camelcased as `methodResource`.
	 * @param array $args
	 * @return array JSON response data for successful API calls.
	 */
	public function __call ($name, $args) {
		preg_match('/^(?P<method>[a-z]+)(?P<resource>[A-Z].+)$/', $name, $matches);

		if (empty($matches)) {
			throw new \Exception('Unsupported API call');
		}

		$method = $matches['method'];

		$resourceName = $this->normalizeResourceName($matches['resource']);
		$ResourceClass = '\\Baremetrics\\Resources\\' . $resourceName;
		$resource = new $ResourceClass($this);

		if (in_array($method, $this->methods) &&
				(method_exists($resource, $method) || $method === 'list')) {
			return call_user_func_array([$resource, $matches['method']], $args);
		}

		throw new \Exception('Unsupported API method.');
	}

	/**
	 * Perform a HTTP DELETE call against the Baremetrics API.
	 *
	 * @param string $path The resource URL path.
	 * @return array
	 */
	public function httpDelete ($path) {
		return $this->sendRequest('DELETE', $path);
	}

	/**
	 * Perform a HTTP GET call against the Baremetrics API.
	 *
	 * @param string $path The resource URL path.
	 * @return array
	 */
	public function httpGet ($path) {
		return $this->sendRequest('GET', $path);
	}

	/**
	 * Perform a HTTP POST call against the Baremetrics API.
	 *
	 * @param string $path The resource URL path.
	 * @return array
	 */
	public function httpPost ($path, $data) {
		return $this->sendRequest('POST', $path, $data);
	}

	/**
	 * Perform a HTTP PUT call against the Baremetrics API.
	 *
	 * @param string $path The resource URL path.
	 * @return array
	 */
	public function httpPut ($path, $data) {
		return $this->sendRequest('PUT', $path, $data);
	}

	/**
	 * Internal API request method, ensuring correct headers and body.
	 *
	 * @param string $method The HTTP method for the API request.
	 * @param string $path The resource URL path.
	 * @param array $data Any HTTP POST or PUT request body data.
	 * @return array Decoded API response JSON body if successful.
	 */
	private function sendRequest ($method, $path, $data = null) {
		try {
			// flush old error messages
			$this->lastException = null;

			$path = "/v1/{$path}";
			$params = [
				'headers' => [
					'Authorization' => 'Bearer ' . $this->apiKey,
				]
			];

			if ($data !== null) {
				$params['json'] = $data;
			}

			$response = $this->httpClient->request($method, $path, $params);
		}
		catch (\GuzzleHttp\Exception\ClientException $e) {
			$this->lastException = $e;
			return null;
		}

		return json_decode((string)$response->getBody(), true);
	}

	/**
	 * Very simply utility to normalize resource name from method call.
	 *
	 * Allows calling `$client->listSubscriptions` which will be matched to
	 * the `Subscription` resource. Currently simply removes any postfix "s"
	 * in the name.
	 *
	 * @param string $name
	 * @return string
	 */
	private function normalizeResourceName ($name) {
		return preg_replace('/s$/', '', $name);
	}

	/**
	 * Returns false if no exception occured, otherwise a guzzle ClientException.
	 * @return bool|\GuzzleHttp\Exception\ClientException
	 */
	public function getLastException() {
		if($this->lastException == null) {
			return false;
		} else {
			return $this->lastException;
		}
	}
}
