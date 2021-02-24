<?php

namespace TgLog;

/**
 * A log message.
 * <p>This kind of objects are mainly used for later display to users.</p>
 */
class Message {

	protected $type;
	protected $message;

	public function __construct($type, $message) {
		$this->type	   = $type;
		$this->message = $message;
	}

	/**
	 * Returns the type of this message.
	 * @return string the type of the messae
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Returns the actual message.
	 * @return string the message text.
	 */
	public function getMessage() {
		return $this->message;
	}
}