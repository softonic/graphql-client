<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\Mutation\Item as MutationItem;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;

class DataObjectBuilderTest extends TestCase
{
    private $builder;

    protected function setUp()
    {
        $this->builder = new DataObjectBuilder();
    }

    public function testWhenDataIsNull()
    {
        $data = [
            'book' => null,
        ];

        $dataObject = $this->builder->buildQuery($data);

        $expectedDataObject = [
            'book' => null,
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataIsAnEmptyArray()
    {
        $data = [
            'search' => [],
        ];

        $dataObject = $this->builder->buildQuery($data);

        $expectedDataObject = [
            'search' => new QueryCollection([]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAQueryItem()
    {
        $data = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ],
        ];

        $dataObject = $this->builder->buildQuery($data);

        $expectedDataObject = [
            'book' => new QueryItem([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAnArrayOfQueryItems()
    {
        $data = [
            'search' => [
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

        $dataObject = $this->builder->buildQuery($data);

        $expectedDataObject = [
            'search' => new QueryCollection([
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
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAQueryItemWithEmptySecondLevel()
    {
        $data = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [],
            ],
        ];

        $dataObject = $this->builder->buildQuery($data);

        $expectedDataObject = [
            'book' => new QueryItem([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new QueryCollection([]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAQueryItemWithSecondLevel()
    {
        $data = [
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

        $dataObject = $this->builder->buildQuery($data);

        $expectedDataObject = [
            'book' => new QueryItem([
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
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAQueryItemWithThirdLevel()
    {
        $data = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    [
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 1,
                        'name'       => 'Chapter name 1',
                        'pages'      => [
                            [
                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter'        => 1,
                                'id_page'           => 1,
                                'has_illustrations' => false,
                            ],
                            [
                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter'        => 1,
                                'id_page'           => 2,
                                'has_illustrations' => false,
                            ],
                        ],
                    ],
                    [
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'name'       => 'Chapter name 2',
                        'pages'      => [],
                    ],
                ],
            ],
        ];

        $dataObject = $this->builder->buildQuery($data);

        $expectedDataObject = [
            'book' => new QueryItem([
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
                            ]),
                            new QueryItem([
                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter'        => 1,
                                'id_page'           => 2,
                                'has_illustrations' => false,
                            ]),
                        ]),
                    ]),
                    new QueryItem([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'name'       => 'Chapter name 2',
                        'pages'      => new QueryCollection([]),
                    ]),
                ]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAQueryItemInsideAnotherQueryItem()
    {
        $data = [
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
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 2,
                                        'has_illustrations' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name 2',
                            'pages'      => [
                                'upsert' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $dataObject = $this->builder->buildMutation($data);

        $expectedDataObject = [
            'book' => new MutationItem([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new MutationItem([
                    'upsert' => new MutationCollection([
                        new MutationItem([
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name 1',
                            'pages'      => new MutationItem([
                                'upsert' => new MutationCollection([
                                    new MutationItem([
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
                                    ]),
                                    new MutationItem([
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 2,
                                        'has_illustrations' => false,
                                    ]),
                                ]),
                            ]),
                        ]),
                        new MutationItem([
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name 2',
                            'pages'      => new MutationItem([
                                'upsert' => new MutationCollection([]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }
}
