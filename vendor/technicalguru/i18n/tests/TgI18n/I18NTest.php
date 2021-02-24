<?php declare(strict_types=1);

namespace TgI18n;

use PHPUnit\Framework\TestCase;

/**
 * Tests the I18N localization.
 * @author ralph
 *
 */
final class I18NTest extends TestCase {

	public function testFindEmptyLocalization(): void {
	    I18N::reset();
	    $this->assertEquals('testKey', I18N::_('testKey'));
	    $this->assertNull(I18N::__('testKey'));
	}
	
	public function testLocalization(): void {
	    I18N::reset();
	    I18N::$i18nFile = __DIR__.'/i18n.php';
	    $this->assertEquals('testValue-EN', I18N::_('testKey'));
	    $this->assertEquals('testValue-DE', I18N::_('testKey', 'de'));
	    $this->assertEquals('testKey2',     I18N::_('testKey2'));
	}
	
	public function testAddWithOverriding(): void {
	    I18N::reset();
	    I18N::$i18nFile = __DIR__.'/i18n.php';
	    I18N::getValues('testKey');
	    
	    $newValues = array(
	        'testKey' => array(
	            'de' => 'newTestValue-DE',
	            'en' => 'newTestValue-EN',
	            'fr' => 'newTestValue-FR',
	        ),
	        'testKey2' => array(
	            'de' => 'newTestValue2-DE',
	            'en' => 'newTestValue2-EN',
	        ),
	    );
	    I18N::addValues($newValues);
	    $this->assertEquals('newTestValue-EN', I18N::_('testKey'));
	    $this->assertEquals('newTestValue-DE', I18N::_('testKey', 'de'));
	    $this->assertEquals('newTestValue-FR', I18N::_('testKey', 'fr'));
	    $this->assertEquals('newTestValue2-EN', I18N::_('testKey2'));
	    $this->assertEquals('newTestValue2-DE', I18N::_('testKey2', 'de'));    
	}
	
	public function testAddWithoutOverriding(): void {
	    I18N::reset();
	    I18N::$i18nFile = __DIR__.'/i18n.php';
	    I18N::getValues('testKey');
	    
	    $newValues = array(
	        'testKey' => array(
	            'de' => 'newTestValue-DE',
	            'en' => 'newTestValue-EN',
	            'fr' => 'newTestValue-FR',
	        ),
	        'testKey2' => array(
	            'de' => 'newTestValue2-DE',
	            'en' => 'newTestValue2-EN',
	        ),
	    );
	    I18N::addValues($newValues, FALSE);
	    $this->assertEquals('testValue-EN', I18N::_('testKey'));
	    $this->assertEquals('testValue-DE', I18N::_('testKey', 'de'));
	    $this->assertEquals('newTestValue-FR', I18N::_('testKey', 'fr'));
	    $this->assertEquals('newTestValue2-EN', I18N::_('testKey2'));
	    $this->assertEquals('newTestValue2-DE', I18N::_('testKey2', 'de'));    
	}
	
	
}