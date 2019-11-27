<?php

namespace Softonic\GraphQL\Traits;

use PHPUnit\Framework\TestCase;

class TestObject
{
    use JsonPathAccessor;
}

class JsonPathAccessorTest extends TestCase
{
    public function testWhenRootIsRetrieved()
    {
        $obj = new TestObject();

        $this->assertSame($obj, $obj->get('.'));
    }

    public function testWhenSimplePathIsRetrieved()
    {
        $obj = new TestObject();
        $obj->foo = 'bar';

        $this->assertEquals('bar', $obj->get('.foo'));
    }

    public function testWhenSecondLevelPathIsRetrieved()
    {
        $obj = new TestObject();
        $obj->foo = new TestObject();
        $obj->foo->bar = 'bar';

        $this->assertEquals('bar', $obj->get('.foo.bar'));
    }
}
