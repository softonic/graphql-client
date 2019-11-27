<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationsConfig;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;

class MutationBuilderTest extends TestCase
{
    private $simpleConfigMock;

    private $complexConfigMock;

    public function setUp()
    {
        $this->simpleConfigMock  = $this->getConfigMock()->get('ReplaceBookSimple');
        $this->complexConfigMock = $this->getConfigMock()->get('ReplaceBookComplex');
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
                'chapters'  => [
                    'upsert' => [],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutation->toArray());
    }

    public function testWhenThereAreChildren()
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

    private function getConfigMock()
    {
        return new MutationsConfig(
            [
                'ReplaceBookSimple'  => [
                    'book' => [
                        'linksTo'  => '.',
                        'children' => [
                            'chapters' => [
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.chapters',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'ReplaceBookComplex' => [
                    'book' => [
                        'linksTo'  => '.',
                        'children' => [
                            'chapters'  => [
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.chapters',
                                        'children' => [
                                            'pages' => [
                                                'children' => [
                                                    'upsert' => [
                                                        'linksTo'  => '.chapters.pages',
                                                        'children' => [
                                                            'lines' => [
                                                                'children' => [
                                                                    'upsert' => [
                                                                        'linksTo' => '.chapters.pages.lines',
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
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.languages',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
