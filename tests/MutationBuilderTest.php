<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationsConfig;
use Softonic\GraphQL\DataObjects\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\DataObjects\Mutation\Item as MutationItem;
use Softonic\GraphQL\DataObjects\Query\Collection as QueryCollection;
use Softonic\GraphQL\DataObjects\Query\Item as QueryItem;

class MutationBuilderTest extends TestCase
{
    /**
     * @var array
     */
    private $simpleConfigMock;

    /**
     * @var array
     */
    private $complexConfigMock;

    /**
     * @var array
     */
    private $sameQueryStructureConfigMock;

    /**
     * @var array
     */
    private $collectionConfigMock;

    protected function setUp(): void
    {
        $this->simpleConfigMock             = $this->getConfigMock()
            ->get('ReplaceBookSimple');
        $this->complexConfigMock            = $this->getConfigMock()
            ->get('ReplaceBookComplex');
        $this->sameQueryStructureConfigMock = $this->getConfigMock()
            ->get('ReplaceBookWithSameQueryStructure');
        $this->collectionConfigMock         = $this->getConfigMock()
            ->get('ReplaceBooks');
    }

    private function getConfigMock()
    {
        return new MutationsConfig(
            [
                'ReplaceBookSimple'                 => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => MutationItem::class,
                        'children' => [
                            'id_book'   => [],
                            'id_author' => [],
                            'genre'     => [],
                            'chapters'  => [
                                'linksTo'  => '.chapters',
                                'type'     => MutationCollection::class,
                                'children' => [
                                    'id_book'    => [],
                                    'id_chapter' => [],
                                    'name'       => [],
                                    'tags'       => [],
                                ],
                            ],
                        ],
                    ],
                ],
                'ReplaceBookComplex'                => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => MutationItem::class,
                        'children' => [
                            'id_book'     => [],
                            'id_author'   => [],
                            'genre'       => [],
                            'chapters'    => [
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.chapters',
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'id_book'    => [],
                                            'id_chapter' => [],
                                            'name'       => [],
                                            'pages'      => [
                                                'type'     => MutationItem::class,
                                                'children' => [
                                                    'upsert' => [
                                                        'linksTo'  => '.chapters.pages',
                                                        'type'     => MutationCollection::class,
                                                        'children' => [
                                                            'id_book'           => [],
                                                            'id_chapter'        => [],
                                                            'id_page'           => [],
                                                            'has_illustrations' => [],
                                                            'lines'             => [
                                                                'type'     => MutationItem::class,
                                                                'children' => [
                                                                    'upsert' => [
                                                                        'linksTo'  => '.chapters.pages.lines',
                                                                        'type'     => MutationCollection::class,
                                                                        'children' => [
                                                                            'id_book'     => [],
                                                                            'id_chapter'  => [],
                                                                            'id_page'     => [],
                                                                            'id_line'     => [],
                                                                            'words_count' => [],
                                                                        ],
                                                                    ],
                                                                    'delete' => [
                                                                        'type'     => MutationCollection::class,
                                                                        'children' => [
                                                                            'id_book'    => [],
                                                                            'id_chapter' => [],
                                                                            'id_page'    => [],
                                                                            'id_line'    => [],
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'delete' => [
                                                        'type'     => MutationCollection::class,
                                                        'children' => [
                                                            'id_book'    => [],
                                                            'id_chapter' => [],
                                                            'id_page'    => [],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'delete' => [
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'id_book'    => [],
                                            'id_chapter' => [],
                                        ],
                                    ],
                                ],
                            ],
                            'languages'   => [
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.languages',
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'id_book'     => [],
                                            'id_language' => [],
                                        ],
                                    ],
                                    'delete' => [
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'id_book'     => [],
                                            'id_language' => [],
                                        ],
                                    ],
                                ],
                            ],
                            'currentPage' => [
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.currentPage',
                                        'type'     => MutationItem::class,
                                        'children' => [
                                            'id_book'    => [],
                                            'id_chapter' => [],
                                            'id_page'    => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'ReplaceBookWithSameQueryStructure' => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => MutationItem::class,
                        'children' => [
                            'id_book'   => [],
                            'id_author' => [],
                            'genre'     => [],
                            'chapters'  => [
                                'linksTo'  => '.chapters',
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.chapters.upsert',
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'id_book'    => [],
                                            'id_chapter' => [],
                                            'name'       => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'ReplaceBooks'                      => [
                    'books' => [
                        'linksTo'  => '.',
                        'type'     => MutationCollection::class,
                        'children' => [
                            'id_book'   => [],
                            'id_author' => [],
                            'genre'     => [],
                            'chapters'  => [
                                'linksTo'  => '.chapters',
                                'type'     => MutationCollection::class,
                                'children' => [
                                    'id_book'    => [],
                                    'id_chapter' => [],
                                    'name'       => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public function testWhenThereAreOnlyArguments()
    {
        $queryItem = new QueryItem(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'invalid'   => 'nope',
            ]
        );

        $mutation = Mutation::build($this->simpleConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenThereIsAnEmptyChild()
    {
        $queryItem = new QueryItem(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryCollection([]),
            ]
        );

        $mutation = Mutation::build($this->simpleConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenThereAreChildrenWithSimpleConfig()
    {
        $queryItem = new QueryItem(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryCollection(
                    [
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter name 1',
                                'tags'       => new QueryCollection(['tag1', 'tag2']),
                                'invalid'    => 'nope',
                            ]
                        ),
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter name 2',
                                'tags'       => new QueryCollection([]),
                                'invalid'    => 'nope',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->simpleConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    [
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 1,
                        'name'       => 'Chapter name 1',
                        'tags'       => ['tag1', 'tag2'],
                    ],
                    [
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'name'       => 'Chapter name 2',
                        'tags'       => [],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenThereAreChildrenWithComplexConfig()
    {
        $queryItem = new QueryItem(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryCollection(
                    [
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter name 1',
                            ]
                        ),
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter name 2',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->complexConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name 1',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name 2',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenThereAreTwoChildren()
    {
        $queryItem = new QueryItem(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryCollection(
                    [
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter name 1',
                            ]
                        ),
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter name 2',
                            ]
                        ),
                    ]
                ),
                'languages' => new QueryCollection(
                    [
                        new QueryItem(
                            [
                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_language' => 'english',
                            ]
                        ),
                        new QueryItem(
                            [
                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_language' => 'italian',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->complexConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name 1',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name 2',
                        ],
                    ],
                ],
                'languages' => [
                    'upsert' => [
                        [
                            'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_language' => 'english',
                        ],
                        [
                            'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_language' => 'italian',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenThereIsAThirdLevel()
    {
        $queryItem = new QueryItem(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryCollection(
                    [
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter name 1',
                                'pages'      => new QueryCollection([]),
                            ]
                        ),
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter name 2',
                                'pages'      => new QueryCollection(
                                    [
                                        new QueryItem(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 2,
                                                'id_page'           => 1,
                                                'has_illustrations' => false,
                                            ]
                                        ),
                                        new QueryItem(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 2,
                                                'id_page'           => 2,
                                                'has_illustrations' => false,
                                            ]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 3,
                                'name'       => 'Chapter name 3',
                                'pages'      => new QueryCollection(
                                    [
                                        new QueryItem(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 3,
                                                'id_page'           => 1,
                                                'has_illustrations' => false,
                                            ]
                                        ),
                                        new QueryItem(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 3,
                                                'id_page'           => 2,
                                                'has_illustrations' => false,
                                            ]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->complexConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name 1',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name 2',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 2,
                                        'has_illustrations' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 3,
                            'name'       => 'Chapter name 3',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 3,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 3,
                                        'id_page'           => 2,
                                        'has_illustrations' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenThereIsAFourthLevel()
    {
        $queryItem = new QueryItem(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryCollection(
                    [
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter name 1',
                                'pages'      => new QueryCollection(
                                    [
                                        new QueryItem(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 1,
                                                'id_page'           => 1,
                                                'has_illustrations' => false,
                                                'lines'             => new QueryCollection(
                                                    [
                                                        new QueryItem(
                                                            [
                                                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                                'id_chapter'  => 1,
                                                                'id_page'     => 1,
                                                                'id_line'     => 1,
                                                                'words_count' => 30,
                                                            ]
                                                        ),
                                                        new QueryItem(
                                                            [
                                                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                                'id_chapter'  => 1,
                                                                'id_page'     => 1,
                                                                'id_line'     => 2,
                                                                'words_count' => 35,
                                                            ]
                                                        ),
                                                    ]
                                                ),
                                            ]
                                        ),
                                        new QueryItem(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 1,
                                                'id_page'           => 2,
                                                'has_illustrations' => false,
                                                'lines'             => new QueryCollection(
                                                    [
                                                        new QueryItem(
                                                            [
                                                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                                'id_chapter'  => 1,
                                                                'id_page'     => 2,
                                                                'id_line'     => 1,
                                                                'words_count' => 40,
                                                            ]
                                                        ),
                                                    ]
                                                ),
                                            ]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter name 2',
                                'pages'      => new QueryCollection(
                                    [
                                        new QueryItem(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 2,
                                                'id_page'           => 1,
                                                'has_illustrations' => false,
                                                'lines'             => new QueryCollection([]),
                                            ]
                                        ),
                                        new QueryItem(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 2,
                                                'id_page'           => 2,
                                                'has_illustrations' => false,
                                                'lines'             => new QueryCollection([]),
                                            ]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->complexConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name 1',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
                                        'lines'             => [
                                            'upsert' => [
                                                [
                                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                    'id_chapter'  => 1,
                                                    'id_page'     => 1,
                                                    'id_line'     => 1,
                                                    'words_count' => 30,
                                                ],
                                                [
                                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                    'id_chapter'  => 1,
                                                    'id_page'     => 1,
                                                    'id_line'     => 2,
                                                    'words_count' => 35,
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 2,
                                        'has_illustrations' => false,
                                        'lines'             => [
                                            'upsert' => [
                                                [
                                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                    'id_chapter'  => 1,
                                                    'id_page'     => 2,
                                                    'id_line'     => 1,
                                                    'words_count' => 40,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name 2',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 2,
                                        'has_illustrations' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenTheSourceHasItemsWithItemArguments()
    {
        $queryItem = new QueryItem(
            [
                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author'   => 1234,
                'genre'       => null,
                'currentPage' => new QueryItem(
                    [
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'id_page'    => 2,
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->complexConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author'   => 1234,
                'genre'       => null,
                'currentPage' => [
                    'upsert' => [
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'id_page'    => 2,
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenTheSourceHasTheSameStructureThanTheConfig()
    {
        $queryItem = new QueryItem(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryItem(
                    [
                        'upsert' => new QueryCollection(
                            [
                                new QueryItem(
                                    [
                                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter' => 1,
                                        'name'       => 'Chapter name 1',
                                    ]
                                ),
                                new QueryItem(
                                    [
                                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter' => 2,
                                        'name'       => 'Chapter name 2',
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->sameQueryStructureConfigMock, $queryItem, true);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name 1',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name 2',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenRootIsACollectionWithoutChildren()
    {
        $queryCollection = new QueryCollection(
            [
                new QueryItem(
                    [
                        'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_author' => 1234,
                        'genre'     => null,
                    ]
                ),
                new QueryItem(
                    [
                        'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                        'id_author' => 1122,
                        'genre'     => 'drama',
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->collectionConfigMock, $queryCollection, true);

        $expectedMutationArguments = [
            'books' => [
                [
                    'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_author' => 1234,
                    'genre'     => null,
                ],
                [
                    'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                    'id_author' => 1122,
                    'genre'     => 'drama',
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }

    public function testWhenRootIsACollectionWithChildren()
    {
        $queryCollection = new QueryCollection(
            [
                new QueryItem(
                    [
                        'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_author' => 1234,
                        'genre'     => null,
                        'chapters'  => new QueryCollection(
                            [
                                new QueryItem(
                                    [
                                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter' => 1,
                                        'name'       => 'Chapter name 1',
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
                new QueryItem(
                    [
                        'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                        'id_author' => 1122,
                        'genre'     => 'drama',
                        'chapters'  => new QueryCollection(
                            [
                                new QueryItem(
                                    [
                                        'id_book'    => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                                        'id_chapter' => 1,
                                        'name'       => 'Chapter name 1',
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->collectionConfigMock, $queryCollection, true);

        $expectedMutationArguments = [
            'books' => [
                [
                    'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_author' => 1234,
                    'genre'     => null,
                    'chapters'  => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name 1',
                        ],
                    ],
                ],
                [
                    'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                    'id_author' => 1122,
                    'genre'     => 'drama',
                    'chapters'  => [
                        [
                            'id_book'    => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name 1',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->jsonSerialize());
    }
}
