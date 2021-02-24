<?php declare(strict_types=1);

namespace TgUtils;

use PHPUnit\Framework\TestCase;

/**
 * Tests some slugify methods.
 * @author ralph
 *        
 */
class SlugifyTest extends TestCase {

    public function testGermanUmlauts(): void {
        if (class_exists("Normalizer", FALSE)) {
            $s = 'äöüßÄÖÜ-something';
            $this->assertEquals('aeoeuessaeoeue-something', Slugify::slugify($s));
        } else {
            $this->assertTrue(true);
        }
    }
    
    public function testLowercase(): void {
        if (class_exists("Normalizer", FALSE)) {
            $s = 'AmixedCaseText';
            $this->assertEquals('amixedcasetext', Slugify::slugify($s));
        } else {
            $this->assertTrue(true);
        }
    }
    
    public function testSpace(): void {
        if (class_exists("Normalizer", FALSE)) {
            $s = 'A text that has spaces';
            $this->assertEquals('a-text-that-has-spaces', Slugify::slugify($s));
        } else {
            $this->assertTrue(true);
        }
    }
    
}

