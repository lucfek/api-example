<?php

namespace TgLog;

/**
 * An error message for the log.
 * <p>This kind of objects are mainly used for later display to users.</p>
 */
class Error extends Message {

    /**
     * Constructs the error.
     * @param string $message - the message text.
     */
	public function __construct($message) {
		parent::__construct('error', $message);
	}

}