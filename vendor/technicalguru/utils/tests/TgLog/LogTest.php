<?php declare(strict_types=1);

namespace TgLog;

use PHPUnit\Framework\TestCase;

/**
 * Tests the log functionality
 * @author ralph
 *        
 */
class LogTest extends TestCase {

    public function testLogCreation(): void {
        $log = new Log(Log::DEBUG, 'testLogCreation');
        $this->assertEquals(Log::DEBUG, $log->getLogLevel());
        $this->assertEquals('testLogCreation', $log->getAppName());
        $log->logInfo('Tested successfully');
    }

    public function testDefaultLog(): void {
        Log::setDefaultAppName('testDefaultLog');
        $this->assertEquals(Log::INFO, Log::instance()->getLogLevel());
        $this->assertEquals('testDefaultLog', Log::instance()->getAppName());
        Log::info('Tested successfully');
    }
}

