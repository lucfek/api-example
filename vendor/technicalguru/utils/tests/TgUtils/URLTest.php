<?php declare(strict_types=1);

namespace TgUtils;

use PHPUnit\Framework\TestCase;

/**
 * Tests the various URL flavours and their correct parsing
 * @author ralph
 *        
 */
class URLTest extends TestCase {

    public function testWithHost(): void {
        $urlString = 'www.example.com';
        $url       = new URL($urlString);
        $this->testComponents($url, 'http', NULL, NULL, 'www.example.com', 0, '/', NULL, NULL, 'http://www.example.com/');
    }

    public function testWithSchemeHostPath(): void {
        $urlString = 'https://www.example.com/';
        $url       = new URL($urlString);
        $this->testComponents($url, 'https', NULL, NULL, 'www.example.com', 0, '/', NULL, NULL, 'https://www.example.com/');
    }
    
    public function testWithSchemeHostPath2(): void {
        $urlString = 'https://www.example.com/dev/index.php';
        $url       = new URL($urlString);
        $this->testComponents($url, 'https', NULL, NULL, 'www.example.com', 0, '/dev/index.php', NULL, NULL, $urlString);
    }
    
    public function testWithSchemeHostPathQuery(): void {
        $urlString = 'https://www.example.com/dev?name=value';
        $url       = new URL($urlString);
        $this->testComponents($url, 'https', NULL, NULL, 'www.example.com', 0, '/dev', 'name=value', NULL, $urlString);
    }
    
    public function testWithSchemeHostPathFragment(): void {
        $urlString = 'https://www.example.com/dev#anchor';
        $url       = new URL($urlString);
        $this->testComponents($url, 'https', NULL, NULL, 'www.example.com', 0, '/dev', NULL, 'anchor', $urlString);
    }
    
    public function testWithSchemeHostPathQueryFragment(): void {
        $urlString = 'https://www.example.com/dev?name=value#anchor';
        $url       = new URL($urlString);
        $this->testComponents($url, 'https', NULL, NULL, 'www.example.com', 0, '/dev', 'name=value', 'anchor', $urlString);
    }

    public function testWithSchemeUserHostPath(): void {
        $urlString = 'https://user@www.example.com/';
        $url       = new URL($urlString);
        $this->testComponents($url, 'https', 'user', NULL, 'www.example.com', 0, '/', NULL, NULL, $urlString);
    }

    public function testWithSchemeUserPasswordHostPath(): void {
        $urlString = 'https://user:password@www.example.com/';
        $url       = new URL($urlString);
        $this->testComponents($url, 'https', 'user', 'password', 'www.example.com', 0, '/', NULL, NULL, $urlString);
    }

    public function testWithSchemeUserHostPortPath(): void {
        $urlString = 'https://user:password@www.example.com:8443/';
        $url       = new URL($urlString);
        $this->testComponents($url, 'https', 'user', 'password', 'www.example.com', 8443, '/', NULL, NULL, $urlString);
    }

    protected function testComponents(URL $url, $scheme, $user, $pass, $host, $port, $path, $query, $fragment, $toString) {
        $this->assertEquals($scheme,   $url->getScheme());
        $this->assertEquals($user,     $url->getUser());
        $this->assertEquals($pass,     $url->getPassword());
        $this->assertEquals($host,     $url->getHost());
        $this->assertEquals($port,     $url->getPort());
        $this->assertEquals($path,     $url->getPath());
        $this->assertEquals($query,    $url->getQuery());
        $this->assertEquals($fragment, $url->getFragment());
        $this->assertEquals($toString, $url->__toString());
    }
    
}

