<?php

namespace Softonic\GraphQL\Traits;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\DataObjects\Mutation\Collection;
use Softonic\GraphQL\DataObjects\Mutation\Item;

class JsonPathAccessorTest extends TestCase
{
    public function testWhenRootIsRetrieved(): void
    {
        $obj = new MutationTypeConfig();

        $this->assertSame($obj, $obj->get('.'));
    }

    public function testWhenChildrenAreRetrieved(): void
    {
        $upsertConfig          = new MutationTypeConfig();
        $upsertConfig->type    = Collection::class;
        $upsertConfig->linksTo = '.chapters';

        $chaptersConfig           = new MutationTypeConfig();
        $chaptersConfig->type     = Item::class;
        $chaptersConfig->children = ['upsert' => $upsertConfig];

        $typeConfig = new MutationTypeConfig();

        $bookConfig           = new MutationTypeConfig();
        $bookConfig->type     = Item::class;
        $bookConfig->linksTo  = '.';
        $bookConfig->children = [
            'chapters' => $chaptersConfig,
            'type'     => $typeConfig,
        ];

        $this->assertEquals($chaptersConfig, $bookConfig->get('.chapters'));
        $this->assertEquals($upsertConfig, $bookConfig->get('.chapters.upsert'));
    }

    public function testWhenObjectAttributesAreRetrieved(): void
    {
        $bookConfig           = new MutationTypeConfig();
        $bookConfig->type     = Item::class;
        $bookConfig->linksTo  = '.';
        $bookConfig->children = [];

        $this->assertEquals(Item::class, $bookConfig->get('.type'));
        $this->assertEquals('.', $bookConfig->get('.linksTo'));
    }
}
