<?php

namespace TgUtils\Templating;

/**
 * A formatter can format a given value to some software-defined text, using parameters
 * and the given processor. The usage of the processor is discouraged but might be required.
 */
interface Formatter {

	/**
	 * Format the given object using the processor and the arguments.
	 */
	public function format($value, $params, Processor $processor);

}
