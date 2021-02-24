<?php declare(strict_types=1);

namespace TgUtils;

use PHPUnit\Framework\TestCase;

/**
 * Tests some slugify methods.
 * @author ralph
 *        
 */
class FormatUtilsTest extends TestCase {

    public function testFormatPrice(): void {
        $value = 3000.643;
        $this->assertEquals('3.000,64 EUR', FormatUtils::formatPrice($value, 'EUR', 'de', ' '));
    }
    
    public function testFormatUnitWithBytes(): void {
        $value = 3000643;
        $this->assertEquals('2,86 MB', FormatUtils::formatUnit($value, 'B', 2, 'de'));
    }
    
    
    public function testFormatUnitWithDecimal(): void {
        $value = 303400643;
        $this->assertEquals('303,401 MW', FormatUtils::formatUnit($value, 'W', 3, 'de', FALSE));
    }
    
}

