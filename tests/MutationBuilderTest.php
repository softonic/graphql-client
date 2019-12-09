<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationsConfig;
use Softonic\GraphQL\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\Mutation\Item as MutationItem;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;

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

    public function setUp()
    {
        $this->simpleConfigMock     = $this->getConfigMock()->get('ReplaceBookSimple');
        $this->complexConfigMock    = $this->getConfigMock()->get('ReplaceBookComplex');
        $this->collectionConfigMock = $this->getConfigMock()->get('ReplaceBooks');
    }

    public function testWhenThereAreOnlyArguments()
    {
        $queryItem = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutationBuilder = new MutationBuilder($this->simpleConfigMock, $queryItem);
        $mutation        = $mutationBuilder->build();

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
        $queryItem = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([]),
        ]);

        $mutationBuilder = new MutationBuilder($this->simpleConfigMock, $queryItem);
        $mutation        = $mutationBuilder->build();

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
        $queryItem = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name 1',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name 2',
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->simpleConfigMock, $queryItem);
        $mutation        = $mutationBuilder->build();

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
        $queryItem = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name 1',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name 2',
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->complexConfigMock, $queryItem);
        $mutation        = $mutationBuilder->build();

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
        $queryItem = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name 1',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name 2',
                ]),
            ]),
            'languages' => new QueryCollection([
                new QueryItem([
                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_language' => 'english',
                ]),
                new QueryItem([
                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_language' => 'italian',
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->complexConfigMock, $queryItem);
        $mutation        = $mutationBuilder->build();

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
        $queryItem = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name 1',
                    'pages'      => new QueryCollection([]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name 2',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                        ]),
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 2,
                            'has_illustrations' => false,
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 3,
                    'name'       => 'Chapter name 3',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 3,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                        ]),
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 3,
                            'id_page'           => 2,
                            'has_illustrations' => false,
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->complexConfigMock, $queryItem);
        $mutation        = $mutationBuilder->build();

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
        $queryItem = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name 1',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 1,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 1,
                                    'id_line'     => 1,
                                    'words_count' => 30,
                                ]),
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 1,
                                    'id_line'     => 2,
                                    'words_count' => 35,
                                ]),
                            ]),
                        ]),
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 1,
                            'id_page'           => 2,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 2,
                                    'id_line'     => 1,
                                    'words_count' => 40,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name 2',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([]),
                        ]),
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 2,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->complexConfigMock, $queryItem);
        $mutation        = $mutationBuilder->build();

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
        $queryItem = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryItem([
                'upsert' => new QueryCollection([
                    new QueryItem([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 1,
                        'name'       => 'Chapter name 1',
                    ]),
                    new QueryItem([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'name'       => 'Chapter name 2',
                    ]),
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->complexConfigMock, $queryItem);
        $mutation        = $mutationBuilder->build();

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
        $queryCollection = new QueryCollection([
            new QueryItem([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ]),
            new QueryItem([
                'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                'id_author' => 1122,
                'genre'     => 'drama',
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->collectionConfigMock, $queryCollection);
        $mutation        = $mutationBuilder->build();

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
        $queryCollection = new QueryCollection([
            new QueryItem([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryCollection([
                    new QueryItem([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 1,
                        'name'       => 'Chapter name 1',
                    ]),
                ]),
            ]),
            new QueryItem([
                'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                'id_author' => 1122,
                'genre'     => 'drama',
                'chapters'  => new QueryCollection([
                    new QueryItem([
                        'id_book'    => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                        'id_chapter' => 1,
                        'name'       => 'Chapter name 1',
                    ]),
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->collectionConfigMock, $queryCollection);
        $mutation        = $mutationBuilder->build();

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

    private function getConfigMock()
    {
        return new MutationsConfig(
            [
                'ReplaceBookSimple'  => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => MutationItem::class,
                        'children' => [
                            'chapters' => [
                                'linksTo' => '.chapters',
                                'type'    => MutationCollection::class,
                            ],
                        ],
                    ],
                ],
                'ReplaceBookComplex' => [
                    'book' => [
                        'linksTo'  => '.',
                        'type'     => MutationItem::class,
                        'children' => [
                            'chapters'  => [
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.chapters',
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'pages' => [
                                                'type'     => MutationItem::class,
                                                'children' => [
                                                    'upsert' => [
                                                        'linksTo'  => '.chapters.pages',
                                                        'type'     => MutationCollection::class,
                                                        'children' => [
                                                            'lines' => [
                                                                'type'     => MutationItem::class,
                                                                'children' => [
                                                                    'upsert' => [
                                                                        'linksTo' => '.chapters.pages.lines',
                                                                        'type'    => MutationCollection::class,
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
                                        'linksTo' => '.languages',
                                        'type'    => MutationCollection::class,
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
                            'chapters' => [
                                'linksTo' => '.chapters',
                                'type'    => MutationCollection::class,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
