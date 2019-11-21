<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationConfig;
use Softonic\GraphQL\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\Mutation\Item as MutationItem;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;

class MutationTest extends TestCase
{
    private $configMock;

    public function setUp()
    {
        $this->configMock = $this->getConfigMock()->get('ReplaceProgram');
    }

    /**
     * @test
     */
    public function whenThereAreNoChanges()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $expectedMutationData = [];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenThereAreChangesInRootLevel()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->id_developer = 'google';

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => 'google',
            'id_category'  => 'games',
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenThereAreVariousChangesInRootLevelSettingPropertiesNotInRead()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->id_developer      = 'google';
        $mutation->google_compliance = true;

        $expectedMutationData = [
            'id_program'        => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer'      => 'google',
            'id_category'       => 'games',
            'google_compliance' => true,
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenThereAreChangesInRootLevelWithSecondLevelInput()
    {
        $program = new QueryItem([
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

        $mutation = new Mutation($this->configMock, $program);

        $mutation->id_developer = 'google';

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => 'google',
            'id_category'  => 'games',
            'versions'     => [],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenANewItemIsAddedToACollection()
    {
        $program = new QueryItem([
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

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->add(
            [
                'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_version' => '3.0',
                'id_license' => 'VF',
            ]
        );

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '3.0',
                        'id_license' => 'VF',
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenANewItemIsAddedToACollectionNotPresentInTheQuery()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->add(
            [
                'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_version' => '3.0',
                'id_license' => 'VF',
            ]
        );
        $mutation->platforms->upsert->add(
            [
                'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_platform' => 'windows',
            ]
        );

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '3.0',
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
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenChangesAreDoneToAllTheItemsFromACollection()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => null,
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->set(['age' => 15]);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'age'        => 15,
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'age'        => 15,
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenFilteredItemsFromACollectionAreUpdated()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '3.0',
                    'id_license' => 'VF',
                    'age'        => null,
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->filter(['age' => null])->set([
            'id_license' => 'Fw',
            'age'        => 15,
        ]);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '3.0',
                        'id_license' => 'Fw',
                        'age'        => 15,
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'Fw',
                        'age'        => 15,
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenAnItemIsAddedAndAFilterIsAppliedToUpdateTheFilteredItemsAndFinallyAnotherItemIsAdded()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->add(
            [
                'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_version' => '3.0',
                'id_license' => 'VF',
            ]
        );
        $mutation->versions->upsert->filter(['age' => null])->set(['id_license' => 'Fw']);
        $mutation->versions->upsert->add(
            [
                'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_version' => '4.0',
                'id_license' => 'VF',
            ]
        );

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'Fw',
                        'age'        => null,
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '3.0',
                        'id_license' => 'Fw',
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '4.0',
                        'id_license' => 'VF',
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenFilterToUpdateAndAddAnotherItemAndAFilterToUpdate()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version'    => '2.0',
                    'id_license'    => 'VF',
                    'age'           => 18,
                    'about_license' => 'About2',
                ]),
                new QueryItem([
                    'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version'    => '1.0',
                    'id_license'    => 'VF',
                    'age'           => null,
                    'about_license' => 'About1',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->filter(['age' => null])->set(['id_license' => 'Fw']);
        $mutation->versions->upsert->add(
            [
                'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_version'    => '3.0',
                'id_license'    => 'VF',
                'about_license' => 'About3',
            ]
        );
        $mutation->versions->upsert->filter(['id_license' => 'VF'])->set(['about_license' => 'AboutNew']);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version'    => '2.0',
                        'id_license'    => 'VF',
                        'age'           => 18,
                        'about_license' => 'AboutNew',
                    ],
                    [
                        'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version'    => '1.0',
                        'id_license'    => 'Fw',
                        'age'           => null,
                        'about_license' => 'About1',
                    ],
                    [
                        'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version'    => '3.0',
                        'id_license'    => 'VF',
                        'about_license' => 'AboutNew',
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenAFilterIsAppliedToUpdateAndAnotherFilteredIsAppliedToUpdateToTheFirstFilteredItems()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version'    => '3.0',
                    'id_license'    => 'VF',
                    'age'           => 18,
                    'about_license' => null,
                ]),
                new QueryItem([
                    'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version'    => '2.0',
                    'id_license'    => 'VF',
                    'age'           => 18,
                    'about_license' => 'something',
                ]),
                new QueryItem([
                    'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version'    => '1.0',
                    'id_license'    => 'VF',
                    'age'           => null,
                    'about_license' => 'something',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $versionsFiltered = $mutation->versions->upsert->filter(['age' => 18]);
        $versionsFiltered->set(['id_license' => 'Fw']);
        $versionsFiltered->filter(['about_license' => 'something'])->set(['about_license' => 'aboutnew']);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version'    => '3.0',
                        'id_license'    => 'Fw',
                        'age'           => 18,
                        'about_license' => null,
                    ],
                    [
                        'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version'    => '2.0',
                        'id_license'    => 'Fw',
                        'age'           => 18,
                        'about_license' => 'aboutnew',
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenTwoFiltersAreAppliedToUpdateInSeparateActions()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version'    => '3.0',
                    'id_license'    => 'VF',
                    'age'           => 18,
                    'about_license' => null,
                ]),
                new QueryItem([
                    'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version'    => '2.0',
                    'id_license'    => 'VF',
                    'age'           => 18,
                    'about_license' => 'something',
                ]),
                new QueryItem([
                    'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version'    => '1.0',
                    'id_license'    => 'VF',
                    'age'           => null,
                    'about_license' => 'something',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->filter(['age' => 18])->set(['id_license' => 'Fw']);
        $mutation->versions->upsert->filter(['about_license' => 'something'])->set(['about_license' => 'aboutnew']);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version'    => '3.0',
                        'id_license'    => 'Fw',
                        'age'           => 18,
                        'about_license' => null,
                    ],
                    [
                        'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version'    => '2.0',
                        'id_license'    => 'Fw',
                        'age'           => 18,
                        'about_license' => 'aboutnew',
                    ],
                    [
                        'id_program'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version'    => '1.0',
                        'id_license'    => 'VF',
                        'age'           => null,
                        'about_license' => 'aboutnew',
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenThirdLevelItemsAreUpdated()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 1 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'de',
                            'program_name' => 'Test 1 DE',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->locales->upsert->set(['status' => 'hidden']);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'age'        => null,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test 1 EN',
                                    'status'       => 'hidden',
                                ],
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'de',
                                    'program_name' => 'Test 1 DE',
                                    'status'       => 'hidden',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenThirdLevelItemsAreUpdatedForTwoSecondLevelItems()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 2 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'fr',
                            'program_name' => 'Test 2 FR',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 1 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'de',
                            'program_name' => 'Test 1 DE',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->locales->upsert->set(['status' => 'hidden']);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '2.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test 2 EN',
                                    'status'       => 'hidden',
                                ],
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '2.0',
                                    'id_locale'    => 'fr',
                                    'program_name' => 'Test 2 FR',
                                    'status'       => 'hidden',
                                ],
                            ],
                        ],
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'age'        => null,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test 1 EN',
                                    'status'       => 'hidden',
                                ],
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'de',
                                    'program_name' => 'Test 1 DE',
                                    'status'       => 'hidden',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenThirdLevelItemsFilteredAreUpdated()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 2 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'fr',
                            'program_name' => 'Test 2 FR',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 1 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'de',
                            'program_name' => 'Test 1 DE',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->locales->upsert->filter(['id_locale' => 'en'])->set(['program_name' => 'Test EN new']);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '2.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test EN new',
                                    'status'       => 'online',
                                ],
                            ],
                        ],
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'age'        => null,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test EN new',
                                    'status'       => 'online',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenANewItemIsTriedToBeAddedToAThirdLevelCollectionWhichParentIsNotPresentInTheQuery()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => null,
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You cannot access a non existing collection');

        $mutation->versions->upsert->locales->upsert->add(
            [
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_version'   => '1.0',
                'id_locale'    => 'en',
                'program_name' => 'Test 1 EN',
                'status'       => 'online',
            ]
        );
    }

    /**
     * @test
     */
    public function whenANewItemIsTriedToBeAddedToAThirdLevelCollectionWhichParentIsPresentInTheQueryButKeyIsNot()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->locales->upsert->add(
            [
                'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_version'   => '1.0',
                'id_locale'    => 'en',
                'program_name' => 'Test 1 EN',
                'status'       => 'online',
            ]
        );

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test 1 EN',
                                    'status'       => 'online',
                                ],
                            ],
                        ],
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test 1 EN',
                                    'status'       => 'online',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenThirdLevelItemsHaveDifferentUpdates()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 2 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'fr',
                            'program_name' => 'Test 2 FR',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'it',
                            'program_name' => 'Test 2 IT',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 1 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'de',
                            'program_name' => 'Test 1 DE',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->locales->upsert->filter(['id_locale' => 'en'])->set(['program_name' => 'Test EN new']);
        $mutation->versions->upsert->filter(['id_version' => '1.0'])->locales->upsert->set(['status' => 'hidden']);
        $mutation->versions->upsert->locales->upsert->filter([
            'id_version' => '2.0',
            'id_locale'  => 'fr',
        ])->set(['id_binary' => '1234567890123456789012345678901234567890']);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '2.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test EN new',
                                    'status'       => 'online',
                                ],
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '2.0',
                                    'id_locale'    => 'fr',
                                    'program_name' => 'Test 2 FR',
                                    'status'       => 'online',
                                    'id_binary'    => '1234567890123456789012345678901234567890',
                                ],
                            ],
                        ],
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'age'        => null,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test EN new',
                                    'status'       => 'hidden',
                                ],
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'de',
                                    'program_name' => 'Test 1 DE',
                                    'status'       => 'hidden',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenAnItemIsTriedToBeAddedToAFilteredCollection()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 2 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'fr',
                            'program_name' => 'Test 2 FR',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 1 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'de',
                            'program_name' => 'Test 1 DE',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $this->expectException(\Error::class);

        $mutation->versions->upsert->locales->upsert->filter(['id_version' => '1.0'])->add([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_version'   => '1.0',
            'id_locale'    => 'es',
            'program_name' => 'Test 1 ES',
            'status'       => 'online',
        ]);
    }

    /**
     * @test
     */
    public function whenThirdLevelItemsAreAdded()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                    'locales'    => new QueryCollection([]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 1 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'de',
                            'program_name' => 'Test 1 DE',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->filter(['id_version' => '1.0'])->locales->upsert->add([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_version'   => '1.0',
            'id_locale'    => 'es',
            'program_name' => 'Test 1 ES',
            'status'       => 'online',
        ]);
        $mutation->versions->upsert->locales->upsert->add([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_version'   => 'last',
            'id_locale'    => 'it',
            'program_name' => 'Test last IT',
            'status'       => 'online',
        ]);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => 'last',
                                    'id_locale'    => 'it',
                                    'program_name' => 'Test last IT',
                                    'status'       => 'online',
                                ],
                            ],
                        ],
                    ],
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'age'        => null,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'es',
                                    'program_name' => 'Test 1 ES',
                                    'status'       => 'online',
                                ],
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => 'last',
                                    'id_locale'    => 'it',
                                    'program_name' => 'Test last IT',
                                    'status'       => 'online',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenThirdLevelItemsAreAddedAndThereWereNoneBeforeInTheCollection()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                    'locales'    => new QueryCollection([]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->locales->upsert->add([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_version'   => 'last',
            'id_locale'    => 'it',
            'program_name' => 'Test last IT',
            'status'       => 'online',
        ]);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => 'last',
                                    'id_locale'    => 'it',
                                    'program_name' => 'Test last IT',
                                    'status'       => 'online',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenFourthLevelItemsAreAdded()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '2.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 2 EN',
                            'status'       => 'online',
                            'urltypes'     => new QueryCollection([]),
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '2.0',
                            'id_locale'    => 'fr',
                            'program_name' => 'Test 2 FR',
                            'status'       => 'online',
                            'urltypes'     => new QueryCollection([]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => null,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 1 EN',
                            'status'       => 'online',
                        ]),
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'de',
                            'program_name' => 'Test 1 DE',
                            'status'       => 'online',
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->locales->upsert->filter(['id_locale' => 'en'])->urltypes->upsert->add([
            'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_version'  => '1.0',
            'id_locale'   => 'en',
            'id_url_type' => 'publisher_url',
            'id_url'      => 'ff8c77fa-7015-45f5-a108-11a9997edc3f',
        ]);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '2.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '2.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test 2 EN',
                                    'status'       => 'online',
                                    'urltypes'     => [
                                        'upsert' => [
                                            [
                                                'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_version'  => '1.0',
                                                'id_locale'   => 'en',
                                                'id_url_type' => 'publisher_url',
                                                'id_url'      => 'ff8c77fa-7015-45f5-a108-11a9997edc3f',
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
                        'age'        => null,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test 1 EN',
                                    'status'       => 'online',
                                    'urltypes'     => [
                                        'upsert' => [
                                            [
                                                'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_version'  => '1.0',
                                                'id_locale'   => 'en',
                                                'id_url_type' => 'publisher_url',
                                                'id_url'      => 'ff8c77fa-7015-45f5-a108-11a9997edc3f',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    /**
     * @test
     */
    public function whenFourthLevelItemsAreUpdated()
    {
        $program = new QueryItem([
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => new QueryCollection([
                new QueryItem([
                    'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_version' => '1.0',
                    'id_license' => 'VF',
                    'age'        => 18,
                    'locales'    => new QueryCollection([
                        new QueryItem([
                            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_version'   => '1.0',
                            'id_locale'    => 'en',
                            'program_name' => 'Test 1 EN',
                            'status'       => 'online',
                            'urltypes'     => new QueryCollection([
                                new QueryItem([
                                    'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'  => '1.0',
                                    'id_locale'   => 'en',
                                    'id_url_type' => 'publisher_url',
                                    'id_url'      => 'ff8c77fa-7015-45f5-a108-11a9997edc3f',
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->configMock, $program);

        $mutation->versions->upsert->locales->upsert->urltypes->upsert->set(['id_url' => '9e1a848e-af78-4bcb-8d81-bf3e8aded379']);

        $expectedMutationData = [
            'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_developer' => null,
            'id_category'  => 'games',
            'versions'     => [
                'upsert' => [
                    [
                        'id_program' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_version' => '1.0',
                        'id_license' => 'VF',
                        'age'        => 18,
                        'locales'    => [
                            'upsert' => [
                                [
                                    'id_program'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_version'   => '1.0',
                                    'id_locale'    => 'en',
                                    'program_name' => 'Test 1 EN',
                                    'status'       => 'online',
                                    'urltypes'     => [
                                        'upsert' => [
                                            [
                                                'id_program'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_version'  => '1.0',
                                                'id_locale'   => 'en',
                                                'id_url_type' => 'publisher_url',
                                                'id_url'      => '9e1a848e-af78-4bcb-8d81-bf3e8aded379',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    private function getConfigMock()
    {
        return new MutationConfig(
            [
                'ReplaceProgram' => [
                    'program' => [
                        'linksTo'  => '.',
                        'type'     => MutationItem::class,
                        'children' => [
                            'versions'  => [
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo'  => '.versions',
                                        'type'     => MutationCollection::class,
                                        'children' => [
                                            'locales' => [
                                                'type'     => MutationItem::class,
                                                'children' => [
                                                    'upsert' => [
                                                        'linksTo'  => '.versions.locales',
                                                        'type'     => MutationCollection::class,
                                                        'children' => [
                                                            'urltypes' => [
                                                                'type'     => MutationItem::class,
                                                                'children' => [
                                                                    'upsert' => [
                                                                        'linksTo' => '.versions.locales.urltypes',
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
                            'platforms' => [
                                'type'     => MutationItem::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.platforms',
                                        'type'    => MutationCollection::class,
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
