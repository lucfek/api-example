<?php

namespace TgLog;

/**
 * A success message for the log.
 * <p>This kind of objects are mainly used for later display to users.</p>
 */
class Success extends Message {

    /**
     * Constructs the success message.
     * @param string $message - the message text.
     */
    public function __construct($message) {
		parent::__construct('success', $message);
	}

}