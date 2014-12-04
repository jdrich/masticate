<?php

namespace Masticate\Tests;

use Masticate\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase {
    public function testMastication() {
        $server = $_SERVER;

        $this->assertTrue(isset($_GET));
        $this->assertTrue(isset($_POST));
        $this->assertTrue(isset($_FILES));
        $this->assertTrue(isset($_SERVER));

        $filter = Filter::masticate();

        $this->assertFalse(isset($_GET));
        $this->assertFalse(isset($_POST));
        $this->assertFalse(isset($_FILES));
        $this->assertFalse(isset($_SERVER));

        // PHPUnit Bitches if we don't have a $_SERVER;
        $_SERVER = $server;

        global $_SERVER;
    }

    public function testConstructorRegistersSuperGlobal() {
        $filter = new Filter(['foo' => ['bar' => 'baz']]);

        $this->assertEquals('baz', $filter->supers('foo', 'bar'));
    }

    public function testRegisterWorks() {
        $filter = new Filter();
        $filter->register('foo', ['bar' => 'baz']);

        $this->assertEquals('baz', $filter->supers('foo', 'bar'));
    }

    public function testCallOverrideIsUseful() {
        $filter = new Filter(['foo' => ['bar' => 'baz']]);

        $this->assertEquals('baz', $filter->foo('bar'));
    }

    public function testCallOverrideDoesExceptionStuff() {
        $filter = new Filter();

        $this->setExpectedException('InvalidArgumentException');

        $filter->foo('bar');
    }

    public function testHasActuallyWorks() {
        $filter = new Filter(['foo' => ['bar' => 'baz']]);

        $this->assertTrue($filter->has('foo', 'bar'));
        $this->assertFalse($filter->has('foo', 'baz'));
    }

    public function testSupersPerformsFiltering() {
        $filter = new Filter([
            'foo' => [
                'bar' => 'baz',
                'email' => 'foo@example.com',
                'notemail' => 'foo@:!@#$',
                'script' => '<script>asdf</script>',
                'url' => 'http://foo.com'
            ]
        ]);

        $this->assertEquals('baz', $filter->foo('bar'));
        $this->assertEquals('baz', $filter->foo('bar', 'SANITIZE_EMAIL'));

        $this->assertEquals('foo@example.com', $filter->foo('email', 'SANITIZE_EMAIL'));
        $this->assertEquals('foo@!@#$', $filter->foo('notemail', 'SANITIZE_EMAIL'));

        $this->assertEquals('asdf', $filter->foo('script'));
        $this->assertEquals('<script>asdf</script>', $filter->foo('script', 'UNSAFE_RAW'));

        $this->assertEquals('http://foo.com', $filter->foo('url'));
        $this->assertEquals('http%3A%2F%2Ffoo.com', $filter->foo('url', 'SANITIZE_ENCODED'));
    }

    public function testSupersPerformsArrayFiltering() {
        $filter = new Filter([
            'foo' => [
                'bar' => [
                    'asdf',
                    'ssdf',
                    '<script>qwer</script>',
                    'baka',
                    'baka',
                    'baka'
                ]
            ]
        ]);

        $bar = $filter->foo('bar');

        $this->assertEquals('asdf', $bar[0]);
        $this->assertEquals('qwer', $bar[2]);
        $this->assertEquals(6, count($bar));
    }
}
