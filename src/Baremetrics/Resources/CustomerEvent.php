<?php
namespace Baremetrics\Resources;

class CustomerEvent extends Customer {

	public function retrieve ($sourceId, $customerId) {
		return $this->client->httpGet(
			$this->getResourcePath($sourceId, $customerId, 'events')
		);
	}
}
