<?php
namespace Baremetrics\Resources;

class SegmentField extends BaseResource {
	const PATH = 'segments';

	/**
	 * List all available fields to use as filter for building segments
	 * @return mixed
	 */
	public function retrieve() {
		return $this->client->httpGet(
			$this->getResourcePath( null, null, 'fields' )
		);
	}
}
