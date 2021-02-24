<?php

namespace TgLog;

/**
 * An info message for the log.
 * <p>This kind of objects are mainly used for later display to users.</p>
 */
class Info extends Message {

    /**
     * Constructs the info.
     * @param string $message - the message text.
     */
	public function __construct($message) {
		parent::__construct('info', $message);
	}

}