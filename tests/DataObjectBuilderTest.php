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

    /**
     * @test
     */
    public function whenDataIsNull()
    {
        $data = [
            'program' => null,
        ];

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'program' => null,
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    /**
     * @test
     */
    public function whenDataIsAnEmptyArray()
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

    /**
     * @test
     */
    public function whenDataHasAnItem()
    {
        $data = [
            'program' => [
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_developer' => null,
                'id_category'  => 'games',
            ],
        ];

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'program' => new Item([
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_developer' => null,
                'id_category'  => 'games',
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    /**
     * @test
     */
    public function whenDataHasAnArrayOfItems()
    {
        $data = [
            'search' => [
                [
                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_developer' => null,
                    'id_category'  => 'games',
                ],
                [
                    'id_program'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                    'id_developer' => 'dev',
                    'id_category'  => 'tools',
                ],
            ],
        ];

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'search' => [
                new Item([
                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_developer' => null,
                    'id_category'  => 'games',
                ]),
                new Item([
                    'id_program'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                    'id_developer' => 'dev',
                    'id_category'  => 'tools',
                ]),
            ],
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    /**
     * @test
     */
    public function whenDataHasAnItemWithEmptySecondLevel()
    {
        $data = [
            'program' => [
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_developer' => null,
                'id_category'  => 'games',
                'versions'     => [],
            ],
        ];

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'program' => new Item([
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_developer' => null,
                'id_category'  => 'games',
                'versions'     => new Collection([]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    /**
     * @test
     */
    public function whenDataHasAnItemWithSecondLevel()
    {
        $data = [
            'program' => [
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_developer' => null,
                'id_category'  => 'games',
                'versions'     => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                    ],
                ],
            ],
        ];

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'program' => new Item([
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_developer' => null,
                'id_category'  => 'games',
                'versions'     => new Collection([
                    new Item([
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                    ]),
                    new Item([
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                    ]),
                ]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }

    /**
     * @test
     */
    public function whenDataHasAnItemWithThirdLevel()
    {
        $data = [
            'program' => [
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_developer' => null,
                'id_category'  => 'games',
                'versions'     => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'locales'    => [
                            [
                                'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_version' => '2.0',
                                'id_locale'  => 'en',
                                'status'     => 'online',
                            ],
                            [
                                'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_version' => '2.0',
                                'id_locale'  => 'it',
                                'status'     => 'online',
                            ],
                        ],
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'locales'    => [],
                    ],
                ],
            ],
        ];

        $dataObject = $this->builder->build($data);

        $expectedDataObject = [
            'program' => new Item([
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_developer' => null,
                'id_category'  => 'games',
                'versions'     => new Collection([
                    new Item([
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'locales'    => new Collection([
                            new Item([
                                'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_version' => '2.0',
                                'id_locale'  => 'en',
                                'status'     => 'online',
                            ]),
                            new Item([
                                'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_version' => '2.0',
                                'id_locale'  => 'it',
                                'status'     => 'online',
                            ]),
                        ]),
                    ]),
                    new Item([
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'locales'    => new Collection([]),
                    ]),
                ]),
            ]),
        ];
        $this->assertEquals($expectedDataObject, $dataObject);
    }
}
