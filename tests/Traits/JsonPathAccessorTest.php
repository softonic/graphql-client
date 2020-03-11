<?php

namespace Softonic\GraphQL\Traits;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\Mutation\Collection;
use Softonic\GraphQL\Mutation\Item;

class JsonPathAccessorTest extends TestCase
{
    public function testWhenRootIsRetrieved()
    {
        $obj = new MutationTypeConfig();

        $this->assertSame($obj, $obj->get('.'));
    }

    public function testWhenChildrenAreRetrieved()
    {
        $upsertConfig          = new MutationTypeConfig();
        $upsertConfig->type    = Collection::class;
        $upsertConfig->linksTo = '.chapters';

        $chaptersConfig           = new MutationTypeConfig();
        $chaptersConfig->type     = Item::class;
        $chaptersConfig->children = ['upsert' => $upsertConfig];

        $typeConfig       = new MutationTypeConfig();
        $typeConfig->type = MutationTypeConfig::SCALAR_DATA_TYPE;

        $bookConfig           = new MutationTypeConfig();
        $bookConfig->type     = Item::class;
        $bookConfig->linksTo  = '.';
        $bookConfig->children = [
            'chapters' => $chaptersConfig,
            'type'     => $typeConfig,
        ];

        $this->assertEquals($chaptersConfig, $bookConfig->get('.chapters'));
        $this->assertEquals($upsertConfig, $bookConfig->get('.chapters.upsert'));
        // In this case, the child has the same name than a MutationTypeConfig attribute.
        $this->assertEquals($typeConfig, $bookConfig->get('.type'));
    }

    public function testWhenObjectAttributesAreRetrieved()
    {
        $bookConfig           = new MutationTypeConfig();
        $bookConfig->type     = Item::class;
        $bookConfig->linksTo  = '.';
        $bookConfig->children = [];

        $this->assertEquals(Item::class, $bookConfig->get('.type'));
        $this->assertEquals('.', $bookConfig->get('.linksTo'));
    }
}
