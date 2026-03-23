<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\ObjectMapper;
use PHPUnit\Framework\TestCase;

class ObjectMapperTest extends TestCase
{
    /** @var ObjectMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ObjectMapper();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Passed JSON is not an object nor an array
     */
    public function testRejectsInvalidJson()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Passed JSON is not an object nor an array');
        $this->mapper->mapJsonToObject('invalid_json', new \stdClass());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Expected object, got string
     */
    public function testRejectsNonObjects()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Expected object, got string');
        /** @noinspection PhpParamsInspection */
        $this->mapper->mapJsonToObject('{}', 'non-object');
    }

    public function testSkipsUnknownProperties()
    {
        $object = new class
        {
            public $foo = '';
        };
        $json   = json_encode(['foo' => 'foo', 'bar' => 'bar']);
        $this->mapper->mapJsonToObject($json, $object);
        $this->assertEquals($object->foo, 'foo');
        $this->assertObjectNotHasAttribute('bar', $object);
    }

    public function testAssignsCompatibleTypes()
    {
        $object         = new class
        {
            public $foo;
            public $bar;
            public $baz;
            public $nested;
        };
        $object->nested = new class
        {
            public $foo;
        };
        $json           = json_encode(['foo' => 1, 'bar' => [1, 2, 3], 'baz' => ['foo' => 'bar'], 'nested' => ['foo' => 'foo']]);
        $this->mapper->mapJsonToObject($json, $object);
        $this->assertEquals(1, $object->foo);
        $this->assertEquals([1, 2, 3], $object->bar);
        $this->assertEquals(['foo' => 'bar'], $object->baz);
        $this->assertEquals('foo', $object->nested->foo);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp {^Mapping of property class@anonymous.*?->foo failed; got integer, expected string$}
     */
    public function testRejectsIncompatibleTypes()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/^Mapping of property class@anonymous.*?->foo failed; got integer, expected string$/');
        $object = new class
        {
            public $foo = '';
        };
        $json   = json_encode(['foo' => 1]);
        $this->mapper->mapJsonToObject($json, $object);
    }

    public function testAssignsObjectsAndArrays()
    {
        $object      = new class
        {
            public $foo = [];
            public $bar;
        };
        $object->bar = new class
        {
            public $baz;
        };
        $json        = json_encode(['foo' => ['baz' => 'baz'], 'bar' => ['baz' => 'baz']]);
        $this->mapper->mapJsonToObject($json, $object);
        $this->assertEquals(['baz' => 'baz'], $object->foo);
        $this->assertEquals('baz', $object->bar->baz);
    }

    public function testUnsetsEmptyObjects()
    {
        $object      = new class
        {
            public $foo;
            public $bar;
            public $baz;
        };
        $object->foo = new \stdClass();
        $object->bar = new \stdClass();
        $object->baz = new \stdClass();
        $json        = json_encode(['bar' => [], 'baz' => null]);
        $this->mapper->mapJsonToObject($json, $object);
        $this->assertNull($object->foo);
        $this->assertNotNull($object->bar);
        $this->assertNull($object->baz);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp {^Mapping of property class@anonymous.*?->foo failed; got integer, expected instance of stdClass$}
     */
    public function testRequiresCompatibleObjectType()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/^Mapping of property class@anonymous.*?->foo failed; got integer, expected instance of stdClass$/');
        $object = new class {
            public $foo;
        };
        $object->foo = new \stdClass();
        $json = json_encode(['foo'=>1]);
        $this->mapper->mapJsonToObject($json, $object);
    }
}
