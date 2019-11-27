<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Query\Collection;
use Softonic\GraphQL\Query\Item;

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

        $dataObject = $this->builder->build($data);

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

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'search' => [],
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAnItem()
    {
        $data = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ],
        ];

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'book' => new Item([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAnArrayOfItems()
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

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'search' => new Collection([
                new Item([
                    'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_author' => 1234,
                    'genre'     => null,
                ]),
                new Item([
                    'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                    'id_author' => 1122,
                    'genre'     => 'drama',
                ]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAnItemWithEmptySecondLevel()
    {
        $data = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [],
            ],
        ];

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'book' => new Item([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection([]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAnItemWithSecondLevel()
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

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'book' => new Item([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection([
                    new Item([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 1,
                        'name'       => 'Chapter name 1',
                    ]),
                    new Item([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'name'       => 'Chapter name 2',
                    ]),
                ]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    public function testWhenDataHasAnItemWithThirdLevel()
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

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'book' => new Item([
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection([
                    new Item([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 1,
                        'name'       => 'Chapter name 1',
                        'pages'      => new Collection([
                            new Item([
                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter'        => 1,
                                'id_page'           => 1,
                                'has_illustrations' => false,
                            ]),
                            new Item([
                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter'        => 1,
                                'id_page'           => 2,
                                'has_illustrations' => false,
                            ]),
                        ]),
                    ]),
                    new Item([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'name'       => 'Chapter name 2',
                        'pages'      => new Collection([]),
                    ]),
                ]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }
}
