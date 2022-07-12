<?php

namespace Softonic\GraphQL\Config;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\DataObjects\Mutation\Collection;
use Softonic\GraphQL\DataObjects\Mutation\Item;

class MutationsConfigTest extends TestCase
{
    public function testWhenThereIsOneMutationWithOneVariable()
    {
        $mutationsConfig = new MutationsConfig(
            [
                'ReplaceBook' => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => Item::class,
                        'children' => [
                            'chapters' => [
                                'type'     => Item::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.chapters',
                                        'type'    => Collection::class,
                                    ],
                                ],
                            ],
                            'title'    => [],
                        ],
                    ],
                ],
            ]
        );

        $upsertConfig          = new MutationTypeConfig();
        $upsertConfig->type    = Collection::class;
        $upsertConfig->linksTo = '.chapters';

        $chaptersConfig           = new MutationTypeConfig();
        $chaptersConfig->type     = Item::class;
        $chaptersConfig->children = ['upsert' => $upsertConfig];

        $titleConfig = new MutationTypeConfig();

        $bookConfig           = new MutationTypeConfig();
        $bookConfig->type     = Item::class;
        $bookConfig->linksTo  = '.';
        $bookConfig->children = [
            'chapters' => $chaptersConfig,
            'title'    => $titleConfig,
        ];

        $this->assertEquals(['book' => $bookConfig], $mutationsConfig->get('ReplaceBook'));
    }

    public function testWhenThereIsOneMutationsWithTwoVariables()
    {
        $mutationsConfig = new MutationsConfig(
            [
                'ReplaceBook' => [
                    'book'   => [
                        'linksTo'  => '.',
                        'type'     => Item::class,
                        'children' => [
                            'chapters' => [
                                'type'     => Item::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.chapters',
                                        'type'    => Collection::class,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'author' => [
                        'linksTo' => '.',
                        'type'    => Item::class,
                    ],
                ],
            ]
        );

        $upsertConfig          = new MutationTypeConfig();
        $upsertConfig->type    = Collection::class;
        $upsertConfig->linksTo = '.chapters';

        $chaptersConfig           = new MutationTypeConfig();
        $chaptersConfig->type     = Item::class;
        $chaptersConfig->children = ['upsert' => $upsertConfig];

        $bookConfig           = new MutationTypeConfig();
        $bookConfig->type     = Item::class;
        $bookConfig->linksTo  = '.';
        $bookConfig->children = ['chapters' => $chaptersConfig];

        $authorConfig          = new MutationTypeConfig();
        $authorConfig->type    = Item::class;
        $authorConfig->linksTo = '.';

        $expectedMutationConfig = [
            'book'   => $bookConfig,
            'author' => $authorConfig,
        ];
        $this->assertEquals($expectedMutationConfig, $mutationsConfig->get('ReplaceBook'));
    }

    public function testWhenThereAreTwoMutations()
    {
        $mutationsConfig = new MutationsConfig(
            [
                'ReplaceBook'  => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => Item::class,
                        'children' => [
                            'chapters' => [
                                'type'     => Item::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.chapters',
                                        'type'    => Collection::class,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'ReplaceVideo' => [
                    'video' => [
                        'linksTo' => '.',
                        'type'    => Item::class,
                    ],
                ],
            ]
        );

        $upsertConfig          = new MutationTypeConfig();
        $upsertConfig->type    = Collection::class;
        $upsertConfig->linksTo = '.chapters';

        $chaptersConfig           = new MutationTypeConfig();
        $chaptersConfig->type     = Item::class;
        $chaptersConfig->children = ['upsert' => $upsertConfig];

        $bookConfig           = new MutationTypeConfig();
        $bookConfig->type     = Item::class;
        $bookConfig->linksTo  = '.';
        $bookConfig->children = ['chapters' => $chaptersConfig];

        $videoConfig          = new MutationTypeConfig();
        $videoConfig->type    = Item::class;
        $videoConfig->linksTo = '.';

        $this->assertEquals(['book' => $bookConfig], $mutationsConfig->get('ReplaceBook'));
        $this->assertEquals(['video' => $videoConfig], $mutationsConfig->get('ReplaceVideo'));
    }
}
