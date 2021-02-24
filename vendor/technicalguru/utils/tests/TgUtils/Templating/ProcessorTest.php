<?php declare(strict_types=1);

namespace TgUtils\Templating;

use PHPUnit\Framework\TestCase;

/**
 * Test the templating
 * @author ralph
 *        
 */
class ProcessorTest extends TestCase {

    public function testSnippetWithString(): void {
        $processor = self::createProcessor();
        $template  = 'This is the output: {{textSnippet}}';
        $result    = $processor->process($template);
        $this->assertEquals('This is the output: This is text snippet.', $result);
    }

    public function testSnippetWithSnippet(): void {
        $processor = self::createProcessor();
        $template  = 'This is the output: {{mySnippet}}';
        $result    = $processor->process($template);
        $this->assertEquals('This is the output: This is my-snippet.', $result);
    }

    public function testSnippetWithParameter(): void {
        $processor = self::createProcessor();
        $template  = 'This is the output: {{mySnippet:aParameter}}';
        $result    = $processor->process($template);
        $this->assertEquals('This is the output: This is my-snippet.aParameter', $result);
    }

    public function testSimpleAttribute(): void {
        $processor = self::createProcessor();
        $template  = 'This is the output: {{testObject.name}}';
        $result    = $processor->process($template);
        $this->assertEquals('This is the output: testObjectName', $result);
    }

    public function testFormatter(): void {
        $processor = self::createProcessor();
        $template  = 'This is the output: {{testObject.aDate:date:rfc822}}';
        $result    = $processor->process($template);
        $this->assertEquals('This is the output: Fri, 01 Jan 2021 00:00:00 +0000', $result);
    }

    protected static function createProcessor(): Processor {
        $testObject = new \stdClass;
        $testObject->name  = 'testObjectName';
        $testObject->aDate = new \TgUtils\Date(1609459200, 'UTC');
        
        $objects    = array(
            'testObject' => $testObject,
        );
        $snippets   = array(
            'textSnippet' => 'This is text snippet.',
            'mySnippet'   => new TestSnippet(),
        );
        $formatters = array(
            'date' => new DateFormatter(),
        );
        return new Processor($objects, $snippets, $formatters, 'en');
    }
}

class TestSnippet implements Snippet {

    public function getOutput($processor, $params) {
        return 'This is my-snippet.'.(count($params) > 0 ? $params[0] : '');
    }
}
