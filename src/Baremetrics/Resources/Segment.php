<?php
namespace Baremetrics\Resources;

class Segment extends BaseResource {
	const PATH = 'segments';

	/**
	 * List all segments
	 * @return mixed
	 */
	public function retrieve () {
		return $this->client->httpGet(
			$this->getResourcePath()
		);
	}

	/**
	 * Show a single segment
	 * @param $segmentId
	 * @return mixed
	 */
	public function show ($segmentId) {
		return $this->client->httpGet(
			$this->getResourcePath(null, $segmentId)
		);
	}

	/* @todo: implement searchSegments */

	/**
	 * Create a segment
	 * @param $segmentData
	 * @return mixed
	 */
	public function create ($segmentData) {
		return $this->client->httpPost(
			$this->getResourcePath(), $segmentData
		);
	}

	/**
	 * Update a given segment
	 * @param $segmentId
	 * @param $segmentData
	 * @return mixed
	 */
	public function update ($segmentId, $segmentData) {
		return $this->client->httpPut(
			$this->getResourcePath(null, $segmentId), $segmentData
		);
	}

	/**
	 * Delete a segment
	 * @param $segmentId
	 * @return mixed
	 */
	public function delete ($segmentId) {
		return $this->client->httpDelete(
			$this->getResourcePath(null, $segmentId)
		);
	}
}
