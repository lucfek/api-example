<?php

namespace TgUtils;

/**
 * Interface that marks an object as self-encoding. Means it provides its own ways to encode, e.g.
 * because it doesn't want to encode certain attributes.
 */
interface SelfJsonEncoder {

	/**
	 * Self-encodes this object into JSON format.
	 * @return string the JSON representation.
	 */
	public function json_encode();

}
