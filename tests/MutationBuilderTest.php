<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationsConfig;
use Softonic\GraphQL\Config\MutationTypeConfig;
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
    private $collectionConfigMock;

    protected function setUp(): void
    {
        $this->simpleConfigMock     = $this->getConfigMock()
            ->get('ReplaceBookSimple');
        $this->complexConfigMock    = $this->getConfigMock()
            ->get('ReplaceBookComplex');
        $this->collectionConfigMock = $this->getConfigMock()
            ->get('ReplaceBooks');
    }

    private function getConfigMock()
    {
        return new MutationsConfig(
            [
                'ReplaceBookSimple'  => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => MutationItem::class,
                        'children' => [
                            'id_book'   => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'id_author' => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'genre'     => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'chapters'  => [
                                'linksTo'  => '.chapters',
                                'type'     => MutationCollection::class,
                                'children' => [
                                    'id_book'    => [
                                        'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                    ],
                                    'id_chapter' => [
                                        'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                    ],
                                    'name'       => [
                                        'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'ReplaceBookComplex' => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => MutationItem::class,
                        'children' => [
                            'id_book'   => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'id_author' => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'genre'     => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'chapters'  => [
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.chapters',
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'id_book'    => [
                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                            ],
                                            'id_chapter' => [
                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                            ],
                                            'name'       => [
                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                            ],
                                            'pages'      => [
                                                'type'     => MutationItem::class,
                                                'children' => [
                                                    'upsert' => [
                                                        'linksTo'  => '.chapters.pages',
                                                        'type'     => MutationCollection::class,
                                                        'children' => [
                                                            'id_book'           => [
                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                            ],
                                                            'id_chapter'        => [
                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                            ],
                                                            'id_page'           => [
                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                            ],
                                                            'has_illustrations' => [
                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                            ],
                                                            'lines'             => [
                                                                'type'     => MutationItem::class,
                                                                'children' => [
                                                                    'upsert' => [
                                                                        'linksTo'  => '.chapters.pages.lines',
                                                                        'type'     => MutationCollection::class,
                                                                        'children' => [
                                                                            'id_book'     => [
                                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                                            ],
                                                                            'id_chapter'  => [
                                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                                            ],
                                                                            'id_page'     => [
                                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                                            ],
                                                                            'id_line'     => [
                                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                                            ],
                                                                            'words_count' => [
                                                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                                                            ],
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'languages' => [
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.languages',
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'id_book'     => [
                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                            ],
                                            'id_language' => [
                                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'ReplaceBooks'       => [
                    'books' => [
                        'linksTo'  => '.',
                        'type'     => MutationCollection::class,
                        'children' => [
                            'id_book'   => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'id_author' => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'genre'     => [
                                'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                            ],
                            'chapters'  => [
                                'linksTo'  => '.chapters',
                                'type'     => MutationCollection::class,
                                'children' => [
                                    'id_book'    => [
                                        'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                    ],
                                    'id_chapter' => [
                                        'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                    ],
                                    'name'       => [
                                        'type' => MutationTypeConfig::SCALAR_DATA_TYPE,
                                    ],
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

        $mutation = Mutation::build($this->simpleConfigMock, $queryItem);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
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

        $mutation = Mutation::build($this->simpleConfigMock, $queryItem);

        $expectedMutationArguments = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
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
                                'invalid'    => 'nope',
                            ]
                        ),
                        new QueryItem(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter name 2',
                                'invalid'    => 'nope',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $mutation = Mutation::build($this->simpleConfigMock, $queryItem);

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
                    ],
                    [
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'name'       => 'Chapter name 2',
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
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

        $mutation = Mutation::build($this->complexConfigMock, $queryItem);

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
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
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

        $mutation = Mutation::build($this->complexConfigMock, $queryItem);

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
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
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

        $mutation = Mutation::build($this->complexConfigMock, $queryItem);

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
                                'upsert' => [],
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
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
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

        $mutation = Mutation::build($this->complexConfigMock, $queryItem);

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
                                        'lines'             => [
                                            'upsert' => [],
                                        ],
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 2,
                                        'has_illustrations' => false,
                                        'lines'             => [
                                            'upsert' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
    }

    public function testWhenTheSourceHasItemsWithItemArguments()
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

        $mutation = Mutation::build($this->complexConfigMock, $queryItem);

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
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
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

        $mutation = Mutation::build($this->collectionConfigMock, $queryCollection);

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
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
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

        $mutation = Mutation::build($this->collectionConfigMock, $queryCollection);

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
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
    }
}
