<?php
namespace Baremetrics\Resources;

class CustomerEvent extends Customer {

	public function list ($sourceId, $customerId) {
		return $this->client->httpGet(
			$this->getResourcePath($sourceId, $customerId, 'events')
		);
	}
}
