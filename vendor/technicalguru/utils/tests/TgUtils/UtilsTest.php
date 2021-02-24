<?php declare(strict_types=1);

namespace TgUtils;

use PHPUnit\Framework\TestCase;

/**
 * Tests some utility methods.
 * @author ralph
 *        
 */
class UtilsTest extends TestCase {

    public function testIsEmptyNull(): void {
        $this->assertTrue(Utils::isEmpty(NULL));
    }
    
    public function testIsEmptyZero(): void {
        $this->assertTrue(Utils::isEmpty(''));
    }
    
    public function testIsEmptySpaces(): void {
        $this->assertTrue(Utils::isEmpty('    '));
    }
    
    public function testIsEmptyString(): void {
        $this->assertFalse(Utils::isEmpty('a'));
    }
    
}

