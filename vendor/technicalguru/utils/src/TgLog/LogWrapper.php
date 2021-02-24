<?php

namespace TgLog;

/**
 * A wrapper that fulfills the logger interface.
 * @author ralph
 *        
 */
class LogWrapper implements Logger {

	/** The single instance of the log wrapper using the Log singleton */
	protected static $instance;
	
	/**
	 * Returns the single instance.
	 * @return LogWrapper - single Log instance 
	 */
    public static function instance() {
		if (self::$instance == null) {
			self::$instance = new LogWrapper(Log::instance());
		}
		return self::$instance;
	}
	
	/** The underlying log */
    protected $log;
    
    /**
     * Constructor.
     * @param Log $log - the underlying logger
     */
    public function __construct(Log $log) {
        if ($log == NULL) $log = Log::instance();
        $this->log = $log;
    }

    /**
     * @see \TgLog\Logger::warn()
     */
    public function warn($s, $object = NULL) {
        $this->log->logWarn($s, $object);
    }

    /**
     * @see \TgLog\Logger::debug()
     */
    public function debug($s, $object = NULL) {
        $this->log->logDebug($s, $object);
    }

    /**
     * @see \TgLog\Logger::error()
     */
    public function error($s, $object = NULL) {
        $this->log->logError($s, $object);
    }

    /**
     * @see \TgLog\Logger::info()
     */
    public function info($s, $object = NULL) {
        $this->log->logInfo($s, $object);
    }
}

