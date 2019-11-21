<?php


namespace Softonic\GraphQL\Traits;

use PHPUnit\Framework\TestCase;

class TestObject {
    use JsonPathAccessor;
}

class ObjectAccessorTest extends TestCase
{
    /**
     * @test
     */
    public function getRoot()
    {
        $obj = new TestObject();

        $this->assertSame($obj, $obj->get('.'));
    }

    /**
     * @test
     */
    public function getSimplePath()
    {
        $obj = new TestObject();
        $obj->foo = 'bar';

        $this->assertEquals('bar', $obj->get('.foo'));
    }

    /**
     * @test
     */
    public function getSecondLevelPath()
    {
        $obj = new TestObject();
        $obj->foo = new TestObject();
        $obj->foo->bar = 'bar';

        $this->assertEquals('bar', $obj->get('.foo.bar'));
    }
}
