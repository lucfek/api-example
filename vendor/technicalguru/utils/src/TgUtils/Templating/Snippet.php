<?php

namespace TgUtils\Templating;

/**
 * A snippet is a fixed, software-defined piece of text that can be inserted in
 * templates via the {{snippet-name:params}} variable.
 */
interface Snippet {

	/**
	 * Return the output of the template, using the given processor for objects and formatters.
	 * Parameters can be given
	 */
	public function getOutput(Processor $processor, $params);

}
