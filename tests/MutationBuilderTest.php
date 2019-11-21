<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationConfig;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;

class MutationBuilderTest extends TestCase
{
    private $simpleConfigMock;

    private $complexConfigMock;

    public function setUp()
    {
        $this->simpleConfigMock  = $this->getConfigMock()->get('ReplaceProgramSimple')['program'];
        $this->complexConfigMock = $this->getConfigMock()->get('ReplaceProgramComplex')['program'];
    }

    /**
     * @test
     */
    public function whenThereAreOnlyArguments()
    {
        $queryItem = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
        ]);

        $mutationBuilder = new MutationBuilder($this->simpleConfigMock, $queryItem);

        $expectedMutationArguments = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
        ];
        $this->assertEquals($expectedMutationArguments, $mutationBuilder->build()->toArray());
    }

    /**
     * @test
     */
    public function whenThereIsAnEmptyChild()
    {
        $queryItem = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([]),
        ]);

        $mutationBuilder = new MutationBuilder($this->simpleConfigMock, $queryItem);

        $expectedMutationArguments = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutationBuilder->build()->toArray());
    }

    /**
     * @test
     */
    public function whenThereAreChildren()
    {
        $queryItem = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->simpleConfigMock, $queryItem);

        $expectedMutationArguments = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
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
        $this->assertEquals($expectedMutationArguments, $mutationBuilder->build()->toArray());
    }

    /**
     * @test
     */
    public function whenThereAreTwoChildren()
    {
        $queryItem = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                ]),
            ]),
            'platforms'    => new QueryCollection([
                new QueryItem([
                    'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_platform' => 'windows',
                ]),
                new QueryItem([
                    'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_platform' => 'mac',
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->complexConfigMock, $queryItem);

        $expectedMutationArguments = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
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
            'platforms'    => [
                'upsert' => [
                    [
                        'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_platform' => 'windows',
                    ],
                    [
                        'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_platform' => 'mac',
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutationBuilder->build()->toArray());
    }

    /**
     * @test
     */
    public function whenThereIsAThirdLevel()
    {
        $queryItem = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '3.0',
                    'id_license' => 'VF',
                    'locales'    => new QueryCollection([]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version' => '2.0',
                            'id_locale'  => 'en',
                            'status'     => 'online',
                        ]),
                        new QueryItem([
                            'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version' => '2.0',
                            'id_locale'  => 'it',
                            'status'     => 'online',
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version' => '1.0',
                            'id_locale'  => 'en',
                            'status'     => 'online',
                        ]),
                        new QueryItem([
                            'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version' => '1.0',
                            'id_locale'  => 'fr',
                            'status'     => 'online',
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->complexConfigMock, $queryItem);

        $expectedMutationArguments = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '3.0',
                        'id_license' => 'VF',
                        'locales'    => [
                            'upsert' => [],
                        ],
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'locales'    => [
                            'upsert' => [
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
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version' => '1.0',
                                    'id_locale'  => 'en',
                                    'status'     => 'online',
                                ],
                                [
                                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version' => '1.0',
                                    'id_locale'  => 'fr',
                                    'status'     => 'online',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutationBuilder->build()->toArray());
    }

    /**
     * @test
     */
    public function whenThereIsAFourthLevel()
    {
        $queryItem = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version' => '2.0',
                            'id_locale'  => 'en',
                            'status'     => 'online',
                            'urltypes'    => new QueryCollection([
                                new QueryItem([
                                    'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'  => '2.0',
                                    'id_locale'   => 'en',
                                    'id_url_type' => 'publisher_url',
                                    'id_url'      => '0f8c77fa-7015-45f5-a108-11a9997edc3f',
                                ]),
                                new QueryItem([
                                    'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'  => '2.0',
                                    'id_locale'   => 'en',
                                    'id_url_type' => 'binary_publisher_url',
                                    'id_url'      => '1f8c77fa-7015-45f5-a108-11a9997edc3f',
                                ]),
                            ]),
                        ]),
                        new QueryItem([
                            'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version' => '2.0',
                            'id_locale'  => 'it',
                            'status'     => 'online',
                            'urltypes'    => new QueryCollection([
                                new QueryItem([
                                    'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'  => '2.0',
                                    'id_locale'   => 'it',
                                    'id_url_type' => 'publisher_url',
                                    'id_url'      => '2f8c77fa-7015-45f5-a108-11a9997edc3f',
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version' => '1.0',
                            'id_locale'  => 'en',
                            'status'     => 'online',
                            'urltypes'    => new QueryCollection([]),
                        ]),
                        new QueryItem([
                            'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version' => '1.0',
                            'id_locale'  => 'fr',
                            'status'     => 'online',
                            'urltypes'    => new QueryCollection([]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutationBuilder = new MutationBuilder($this->complexConfigMock, $queryItem);

        $expectedMutationArguments = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version' => '2.0',
                                    'id_locale'  => 'en',
                                    'status'     => 'online',
                                    'urltypes'   => [
                                        'upsert' => [
                                            [
                                                'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_version'  => '2.0',
                                                'id_locale'   => 'en',
                                                'id_url_type' => 'publisher_url',
                                                'id_url'      => '0f8c77fa-7015-45f5-a108-11a9997edc3f',
                                            ],
                                            [
                                                'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_version'  => '2.0',
                                                'id_locale'   => 'en',
                                                'id_url_type' => 'binary_publisher_url',
                                                'id_url'      => '1f8c77fa-7015-45f5-a108-11a9997edc3f',
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version' => '2.0',
                                    'id_locale'  => 'it',
                                    'status'     => 'online',
                                    'urltypes'   => [
                                        'upsert' => [
                                            [
                                                'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_version'  => '2.0',
                                                'id_locale'   => 'it',
                                                'id_url_type' => 'publisher_url',
                                                'id_url'      => '2f8c77fa-7015-45f5-a108-11a9997edc3f',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version' => '1.0',
                                    'id_locale'  => 'en',
                                    'status'     => 'online',
                                    'urltypes'   => [
                                        'upsert' => [],
                                    ],
                                ],
                                [
                                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version' => '1.0',
                                    'id_locale'  => 'fr',
                                    'status'     => 'online',
                                    'urltypes'   => [
                                        'upsert' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationArguments, $mutationBuilder->build()->toArray());
    }

    private function getConfigMock()
    {
        return new MutationConfig(
            [
                'ReplaceProgramSimple'  => [
                    'program' => [
                        'linksTo'  => '.',
                        'children' => [
                            'versions' => [
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.versions',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'ReplaceProgramComplex' => [
                    'program' => [
                        'linksTo'  => '.',
                        'children' => [
                            'versions'  => [
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.versions',
                                        'children' => [
                                            'locales' => [
                                                'children' => [
                                                    'upsert' => [
                                                        'linksTo'  => '.versions.locales',
                                                        'children' => [
                                                            'urltypes' => [
                                                                'children' => [
                                                                    'upsert' => [
                                                                        'linksTo' => '.versions.locales.utltypes',
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
                            'platforms' => [
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.platforms',
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
