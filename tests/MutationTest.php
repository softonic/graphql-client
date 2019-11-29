<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationsConfig;
use Softonic\GraphQL\Exceptions\InaccessibleArgumentException;
use Softonic\GraphQL\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\Mutation\Item as MutationItem;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;

class MutationTest extends TestCase
{
    /**
     * @var array
     */
    private $itemConfigMock;

    /**
     * @var array
     */
    private $collectionConfigMock;

    public function setUp()
    {
        $this->itemConfigMock       = $this->getConfigMock()->get('ReplaceBook');
        $this->collectionConfigMock = $this->getConfigMock()->get('ReplaceBooks');
    }

    public function testWhenThereAreNoChanges()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $expectedMutationData = [];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenThereAreChangesInRootLevel()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->genre = 'adventure';

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => 'adventure',
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenTryingToUpdateAnItemWithTheSameValueItAlreadyHas()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->id_author = 1234;
        $mutation->book->genre     = null;

        $expectedMutationData = [];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenThereAreVariousChangesInRootLevelSettingPropertiesNotInRead()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->genre = 'adventure';
        $mutation->book->title = 'Book title';

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => 'adventure',
                'title'     => 'Book title',
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenThereAreChangesInRootLevelWithSecondLevelInput()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->genre = 'adventure';

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => 'adventure',
                'chapters'  => [],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenANewItemIsAddedToACollection()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->add(
            [
                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter' => 3,
                'name'       => 'Chapter name',
            ]
        );

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 3,
                            'name'       => 'Chapter name',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenANewItemIsAddedToACollectionNotPresentInTheQuery()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->add(
            [
                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter' => 1,
                'name'       => 'Chapter name',
            ]
        );
        $mutation->book->languages->upsert->add(
            [
                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_language' => 'english',
            ]
        );

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                        ],
                    ],
                ],
                'languages' => [
                    'upsert' => [
                        [
                            'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_language' => 'english',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenChangesAreDoneToAllTheItemsFromACollection()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => null,
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->set(['pov' => 'third person']);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'third person',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name',
                            'pov'        => 'third person',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenFilteredItemsFromACollectionAreUpdated()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => null,
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 3,
                    'name'       => 'Chapter name',
                    'pov'        => null,
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->filter(['pov' => null])->set([
            'name' => 'Test chapter',
            'pov'  => 'third person',
        ]);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Test chapter',
                            'pov'        => 'third person',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 3,
                            'name'       => 'Test chapter',
                            'pov'        => 'third person',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenAnItemIsAddedAndAFilterIsAppliedToUpdateTheFilteredItemsAndFinallyAnotherItemIsAdded()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => null,
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->add(
            [
                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter' => 3,
                'name'       => 'Chapter name',
            ]
        );
        $mutation->book->chapters->upsert->filter(['pov' => null])->set(['name' => 'Test chapter']);
        $mutation->book->chapters->upsert->add(
            [
                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter' => 4,
                'name'       => 'Chapter name',
            ]
        );

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Test chapter',
                            'pov'        => null,
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 3,
                            'name'       => 'Test chapter',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 4,
                            'name'       => 'Chapter name',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenFilterToUpdateAndAddAnotherItemAndAFilterToUpdate()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'header'     => 'Header 1',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => null,
                    'header'     => 'Header 2',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->filter(['pov' => null])->set(['name' => 'Test chapter']);
        $mutation->book->chapters->upsert->add(
            [
                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter' => 3,
                'name'       => 'Chapter name',
                'pov'        => null,
                'header'     => 'Header 3',
            ]
        );
        $mutation->book->chapters->upsert->filter(['name' => 'Chapter name'])->set(['header' => 'Header new']);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'header'     => 'Header new',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Test chapter',
                            'pov'        => null,
                            'header'     => 'Header 2',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 3,
                            'name'       => 'Chapter name',
                            'pov'        => null,
                            'header'     => 'Header new',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenAFilterIsAppliedToUpdateAndAnotherFilteredIsAppliedToUpdateToTheFirstFilteredItems()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'header'     => null,
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'header'     => 'something',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 3,
                    'name'       => 'Chapter name',
                    'pov'        => null,
                    'header'     => 'something',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $chaptersFiltered = $mutation->book->chapters->upsert->filter(['pov' => 'first person']);
        $chaptersFiltered->set(['name' => 'Test chapter']);
        $chaptersFiltered->filter(['header' => 'something'])->set(['header' => 'Header new']);

        $expectedMutationData = [
            'book' => [
                'genre'     => null,
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Test chapter',
                            'pov'        => 'first person',
                            'header'     => null,
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Test chapter',
                            'pov'        => 'first person',
                            'header'     => 'Header new',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenTwoFiltersAreAppliedToUpdateInSeparateActions()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'header'     => null,
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'header'     => 'something',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 3,
                    'name'       => 'Chapter name',
                    'pov'        => null,
                    'header'     => 'something',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->filter(['pov' => 'first person'])->set(['name' => 'Test chapter']);
        $mutation->book->chapters->upsert->filter(['header' => 'something'])->set(['header' => 'Header new']);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Test chapter',
                            'pov'        => 'first person',
                            'header'     => null,
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Test chapter',
                            'pov'        => 'first person',
                            'header'     => 'Header new',
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 3,
                            'name'       => 'Chapter name',
                            'pov'        => null,
                            'header'     => 'Header new',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenThirdLevelItemsAreUpdated()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => null,
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
                            'id_page'           => 4,
                            'has_illustrations' => false,
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->set(['has_illustrations' => true]);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => null,
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => true,
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 4,
                                        'has_illustrations' => true,
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

    public function testWhenThirdLevelItemsAreUpdatedForTwoSecondLevelItems()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
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
                    'name'       => 'Chapter name',
                    'pov'        => null,
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
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->set(['has_illustrations' => true]);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => true,
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 2,
                                        'has_illustrations' => true,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name',
                            'pov'        => null,
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 1,
                                        'has_illustrations' => true,
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 2,
                                        'has_illustrations' => true,
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

    public function testWhenThirdLevelItemsFilteredAreUpdated()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
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
                    'name'       => 'Chapter name',
                    'pov'        => null,
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
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->filter(['id_page' => 1])->set(['has_illustrations' => true]);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => true,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name',
                            'pov'        => null,
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 1,
                                        'has_illustrations' => true,
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

    public function testWhenANewItemIsTriedToBeAddedToAThirdLevelCollectionWhichParentIsNotPresentInTheQuery()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => null,
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $this->expectException(InaccessibleArgumentException::class);
        $this->expectExceptionMessage('You cannot access a non existing collection');

        $mutation->book->chapters->upsert->pages->upsert->add(
            [
                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter'        => 1,
                'id_page'           => 1,
                'has_illustrations' => false,
            ]
        );
    }

    public function testWhenANewItemIsTriedToBeAddedToAThirdLevelCollectionWhichParentIsPresentInTheQueryButKeyIsNot()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->add(
            [
                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter'        => 1,
                'id_page'           => 1,
                'has_illustrations' => false,
            ]
        );

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
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

    public function testWhenThirdLevelItemsHaveDifferentUpdates()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
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
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 1,
                            'id_page'           => 3,
                            'has_illustrations' => false,
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => null,
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
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->filter(['id_page' => 1])->set(['glossary' => 'Test']);
        $mutation->book->chapters->upsert->filter(['id_chapter' => 2])->pages->upsert->set(['has_illustrations' => true]);
        $mutation->book->chapters->upsert->pages->upsert->filter([
            'id_chapter' => 1,
            'id_page'    => 3,
        ])->set(['new_property' => 'something']);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
                                        'glossary'          => 'Test',
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 3,
                                        'has_illustrations' => false,
                                        'new_property'      => 'something',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name',
                            'pov'        => null,
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 1,
                                        'has_illustrations' => true,
                                        'glossary'          => 'Test',
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 2,
                                        'has_illustrations' => true,
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

    public function testWhenAnItemIsTriedToBeAddedToAFilteredCollection()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
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
                    'name'       => 'Chapter name',
                    'pov'        => null,
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
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $this->expectException(\Error::class);

        $mutation->book->chapters->upsert->pages->upsert->filter(['id_chapter' => 1])->add([
            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'        => 1,
            'id_page'           => 3,
            'has_illustrations' => false,
        ]);
    }

    public function testWhenThirdLevelItemsAreAdded()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => null,
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
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->filter(['id_chapter' => 2])->pages->upsert->add([
            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'        => 2,
            'id_page'           => 3,
            'has_illustrations' => false,
        ]);
        $mutation->book->chapters->upsert->pages->upsert->add([
            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'        => 'last',
            'id_page'           => 4,
            'has_illustrations' => false,
        ]);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 'last',
                                        'id_page'           => 4,
                                        'has_illustrations' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name',
                            'pov'        => null,
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
                                        'id_page'           => 3,
                                        'has_illustrations' => false,
                                    ],
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 'last',
                                        'id_page'           => 4,
                                        'has_illustrations' => false,
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

    public function testWhenThirdLevelItemsAreAddedAndThereWereNoneBeforeInTheCollection()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->add([
            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'        => 1,
            'id_page'           => 1,
            'has_illustrations' => false,
        ]);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 1,
                                        'id_page'           => 1,
                                        'has_illustrations' => false,
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

    public function testWhenFourthLevelItemsAreAdded()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 1,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([]),
                        ]),
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 1,
                            'id_page'           => 2,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => null,
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
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->filter(['id_page' => 1])->lines->upsert->add([
            'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'  => 1,
            'id_page'     => 1,
            'id_line'     => 1,
            'words_count' => 30,
        ]);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
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
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 2,
                            'name'       => 'Chapter name',
                            'pov'        => null,
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
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

    public function testWhenFourthLevelItemsAreUpdated()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
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
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = new Mutation($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->lines->upsert->set(['words_count' => 35]);

        $expectedMutationData = [
            'book' => [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => [
                    'upsert' => [
                        [
                            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter' => 1,
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
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
                                                    'words_count' => 35,
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
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenThereAreChangesInARootLevelCollectionForOneItem()
    {
        $books = new QueryCollection([
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

        $mutation = new Mutation($this->collectionConfigMock, $books);

        $mutation->books->filter(['id_book' => 'a53493b0-4a24-40c4-b786-317f8dfdf897'])->set(['genre' => 'adventure']);

        $expectedMutationData = [
            'books' => [
                [
                    'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                    'id_author' => 1122,
                    'genre'     => 'adventure',
                ],
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    private function getConfigMock()
    {
        return new MutationsConfig(
            [
                'ReplaceBook'  => [
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
                'ReplaceBooks' => [
                    'books' => [
                        'linksTo' => '.',
                        'type'    => MutationCollection::class,
                    ],
                ],
            ]
        );
    }
}
