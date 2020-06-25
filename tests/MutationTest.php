<?php

namespace Softonic\GraphQL;

use Error;
use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Config\MutationsConfig;
use Softonic\GraphQL\DataObjects\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\DataObjects\Mutation\Item as MutationItem;
use Softonic\GraphQL\DataObjects\Query\Collection as QueryCollection;
use Softonic\GraphQL\DataObjects\Query\Item as QueryItem;
use Softonic\GraphQL\Exceptions\InaccessibleArgumentException;

class MutationTest extends TestCase
{
    /**
     * @var array
     */
    private $itemConfigMock;

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
        $this->itemConfigMock               = $this->getConfigMock()
            ->get('ReplaceBook');
        $this->sameQueryStructureConfigMock = $this->getConfigMock()
            ->get('ReplaceBookWithSameQueryStructure');
        $this->collectionConfigMock         = $this->getConfigMock()
            ->get('ReplaceBooks');
    }

    private function getConfigMock()
    {
        return new MutationsConfig([
            'ReplaceBook'                       => [
                'book' => [
                    'linksTo'  => '.',
                    'type'     => MutationItem::class,
                    'children' => [
                        'id_book'   => [],
                        'id_author' => [],
                        'genre'     => [],
                        'chapters'  => [
                            'type'     => MutationItem::class,
                            'children' => [
                                'upsert' => [
                                    'linksTo'  => '.chapters',
                                    'type'     => MutationCollection::class,
                                    'children' => [
                                        'id_book'    => [],
                                        'id_chapter' => [],
                                        'name'       => [],
                                        'pov'        => [],
                                        'header'     => [],
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
                                        'id_book'     => [],
                                        'id_language' => [],
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
                                        'pov'        => [],
                                        'header'     => [],
                                        'pages'      => [
                                            'linksTo'  => '.chapters.upsert.pages',
                                            'type'     => MutationItem::class,
                                            'children' => [
                                                'upsert' => [
                                                    'linksTo'  => '.chapters.upsert.pages.upsert',
                                                    'type'     => MutationCollection::class,
                                                    'children' => [
                                                        'id_book'           => [],
                                                        'id_chapter'        => [],
                                                        'id_page'           => [],
                                                        'has_illustrations' => [],
                                                        'lines'             => [
                                                            'linksTo'  => '.chapters.upsert.pages.upsert.lines',
                                                            'type'     => MutationItem::class,
                                                            'children' => [
                                                                'upsert' => [
                                                                    'linksTo'  => '.chapters.upsert.pages.upsert.lines.upsert',
                                                                    'type'     => MutationCollection::class,
                                                                    'children' => [
                                                                        'id_book'     => [],
                                                                        'id_chapter'  => [],
                                                                        'id_page'     => [],
                                                                        'id_line'     => [],
                                                                        'words_count' => [],
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
                                'pov'        => [],
                                'header'     => [],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testIfAnItemHasAnArgument()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $this->assertTrue($mutation->book->has('id_author'));
        $this->assertTrue($mutation->book->has('genre'));
        $this->assertFalse($mutation->book->has('invalid'));
    }

    public function testWhenThereAreNoChanges()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

    public function testWhenAnArgumentIsUnset()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

        unset($mutation->book->id_author);
        unset($mutation->book->not_existent);

        $expectedMutationData = [
            'book' => [
                'id_book' => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'genre'   => null,
            ],
        ];
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenThereAreMalformedCollectionsItShouldThrowAnError()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryCollection([
                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter' => 1,
                'name'       => 'Chapter name',
            ]),
        ]);

        $this->expectException(\InvalidArgumentException::class);

        Mutation::build($this->itemConfigMock, $book);
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->add([
            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter' => 3,
            'name'       => 'Chapter fail',
        ])
            ->set(['name' => 'Chapter name']);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->add([
            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter' => 1,
            'name'       => 'Chapter name',
        ]);
        $mutation->book->languages->upsert->add([
            'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_language' => 'english',
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

    public function testWhenChangesAreDoneToAllTheItemsFromACollectionWithIncompleteParentDataConfiguration()
    {
        $book = new QueryItem([
            'id_book'       => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'not_in_config' => 'test',
            'chapters'      => new QueryCollection([
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->set(['pov' => 'third person']);

        $expectedMutationData = [
            'book' => [
                'id_book'  => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'chapters' => [
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->filter(['pov' => null])
            ->set([
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->add([
            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter' => 3,
            'name'       => 'Chapter name',
        ]);
        $mutation->book->chapters->upsert->filter(['pov' => null])
            ->set(['name' => 'Test chapter']);
        $mutation->book->chapters->upsert->add([
            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter' => 4,
            'name'       => 'Chapter name',
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->filter(['pov' => null])
            ->set(['name' => 'Test chapter']);
        $mutation->book->chapters->upsert->add([
            'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter' => 3,
            'name'       => 'Chapter name',
            'pov'        => null,
            'header'     => 'Header 3',
        ]);
        $mutation->book->chapters->upsert->filter(['name' => 'Chapter name'])
            ->set(['header' => 'Header new']);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $chaptersFiltered = $mutation->book->chapters->upsert->filter(['pov' => 'first person']);
        $chaptersFiltered->set(['name' => 'Test chapter']);
        $chaptersFiltered->filter(['header' => 'something'])
            ->set(['header' => 'Header new']);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->filter(['pov' => 'first person'])
            ->set(['name' => 'Test chapter']);
        $mutation->book->chapters->upsert->filter(['header' => 'something'])
            ->set(['header' => 'Header new']);

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

    public function testHasMethodForThirdLevelItems()
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
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $this->assertTrue($mutation->book->has('chapters.upsert.pages'));
        $this->assertFalse($mutation->book->has('chapters.upsert.invalid'));
        $this->assertTrue($mutation->book->has('chapters.upsert.pages.upsert.has_illustrations'));
        $this->assertFalse($mutation->book->has('chapters.upsert.pages.upsert.invalid'));
        $this->assertTrue($mutation->book->chapters->has('upsert.pages.upsert.has_illustrations'));
        $this->assertFalse($mutation->book->chapters->has('upsert.pages.upsert.invalid'));
        $this->assertTrue($mutation->book->chapters->upsert->has('pages.upsert.has_illustrations'));
        $this->assertFalse($mutation->book->chapters->upsert->has('pages.upsert.invalid'));
        $this->assertFalse($mutation->book->has('not_existing.upsert.invalid'));
        $this->assertFalse($mutation->book->has('chapters.upsert.pages.upsert.has_illustrations.invalid'));
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->filter(['id_page' => 1])
            ->set(['has_illustrations' => true]);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $this->expectException(InaccessibleArgumentException::class);
        $this->expectExceptionMessage("You cannot access a non existing collection 'pages'");

        $mutation->book->chapters->upsert->pages->upsert->add([
            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'        => 1,
            'id_page'           => 1,
            'has_illustrations' => false,
        ]);
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->add([
            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'        => 1,
            'id_page'           => 1,
            'has_illustrations' => false,
        ]);

        $mutation->book->chapters->upsert->pages->upsert->add([
            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'        => 1,
            'id_page'           => 2,
            'has_illustrations' => false,
        ])
            ->set(['has_illustrations' => true]);

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
                            'pov'        => 'first person',
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $mutation->book->chapters->upsert->pages->upsert->filter(['id_page' => 1])
            ->set(['glossary' => 'Test']);
        $mutation->book->chapters->upsert->filter(['id_chapter' => 2])->pages->upsert->set(['has_illustrations' => true]);
        $mutation->book->chapters->upsert->pages->upsert->filter([
            'id_chapter' => 1,
            'id_page'    => 3,
        ])
            ->set(['new_property' => 'something']);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $this->expectException(Error::class);

        $mutation->book->chapters->upsert->pages->upsert->filter(['id_chapter' => 1])
            ->add([
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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

        $mutation = Mutation::build($this->itemConfigMock, $book);

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

    public function testWhenFourthLevelItemsAreIterated()
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
                                    'words_count' => 35,
                                ]),
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 2,
                                    'id_line'     => 2,
                                    'words_count' => 40,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 2,
                                    'id_page'     => 1,
                                    'id_line'     => 1,
                                    'words_count' => 45,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $expectedLines = [
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
                'id_page'     => 2,
                'id_line'     => 1,
                'words_count' => 35,
            ],
            [
                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter'  => 1,
                'id_page'     => 2,
                'id_line'     => 2,
                'words_count' => 40,
            ],
            [
                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter'  => 2,
                'id_page'     => 1,
                'id_line'     => 1,
                'words_count' => 45,
            ],
        ];

        $this->assertCount(4, $mutation->book->chapters->upsert->pages->upsert->lines->upsert);

        $i = 0;
        foreach ($mutation->book->chapters->upsert->pages->upsert->lines->upsert as $line) {
            $this->assertEquals($expectedLines[$i], $line->toArray());
            ++$i;
        }
    }

    public function testWhenIteratingACollectionAndOneItemIsEmpty()
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
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $expectedLine = [
            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'        => 2,
            'id_page'           => 1,
            'has_illustrations' => false,
        ];
        foreach ($mutation->book->chapters->upsert->pages->upsert as $line) {
            $this->assertEquals($expectedLine, $line->toArray());
        }
    }

    public function testWhenFourthLevelItemsAreCounted()
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
                                    'words_count' => 35,
                                ]),
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 2,
                                    'id_line'     => 2,
                                    'words_count' => 40,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 2,
                                    'id_page'     => 1,
                                    'id_line'     => 1,
                                    'words_count' => 45,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $this->assertEquals(4, $mutation->book->chapters->upsert->pages->upsert->lines->upsert->count());
    }

    public function testWhenCheckIsNotEmpty()
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
                                    'words_count' => 35,
                                ]),
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 2,
                                    'id_line'     => 2,
                                    'words_count' => 40,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 2,
                                    'id_page'     => 1,
                                    'id_line'     => 1,
                                    'words_count' => 45,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $this->assertFalse($mutation->book->chapters->upsert->pages->upsert->lines->upsert->isEmpty());
    }

    public function testWhenCheckIsEmpty()
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
                            'lines'             => new QueryCollection(),
                        ]),
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 1,
                            'id_page'           => 2,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection(),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection(),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book);

        $this->assertTrue($mutation->book->chapters->upsert->pages->upsert->lines->upsert->isEmpty());
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

        $mutation = Mutation::build($this->collectionConfigMock, $books);

        $mutation->books->filter(['id_book' => 'a53493b0-4a24-40c4-b786-317f8dfdf897'])
            ->set(['genre' => 'adventure']);

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

    public function testWhenSourceContainsMutationData()
    {
        $book = new QueryItem([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new QueryItem([
                'upsert' => new QueryCollection([
                    new QueryItem([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 1,
                        'name'       => 'Chapter name',
                        'pov'        => 'first person',
                        'pages'      => new QueryItem([
                            'upsert' => new QueryCollection([
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
                    ]),
                    new QueryItem([
                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_chapter' => 2,
                        'name'       => 'Chapter name',
                        'pov'        => null,
                        'pages'      => new QueryItem([
                            'upsert' => new QueryCollection([
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
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->sameQueryStructureConfigMock, $book, true);

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
                            'name'       => 'Chapter name',
                            'pov'        => null,
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
        $this->assertEquals($expectedMutationData, $mutation->jsonSerialize());
    }

    public function testWhenFourthLevelItemsPropertyIsUnset()
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
                                    'words_count' => 35,
                                ]),
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 2,
                                    'id_line'     => 2,
                                    'words_count' => 40,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 2,
                                    'id_page'     => 1,
                                    'id_line'     => 1,
                                    'words_count' => 45,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book, true);

        unset($mutation->book->chapters->upsert->pages->upsert->has_illustrations);
        unset($mutation->book->chapters->upsert->pages->upsert->lines->upsert->words_count);

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
                                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter' => 1,
                                        'id_page'    => 1,
                                        'lines'      => [
                                            'upsert' => [
                                                [
                                                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                    'id_chapter' => 1,
                                                    'id_page'    => 1,
                                                    'id_line'    => 1,
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter' => 1,
                                        'id_page'    => 2,
                                        'lines'      => [
                                            'upsert' => [
                                                [
                                                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                    'id_chapter' => 1,
                                                    'id_page'    => 2,
                                                    'id_line'    => 1,
                                                ],
                                                [
                                                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                    'id_chapter' => 1,
                                                    'id_page'    => 2,
                                                    'id_line'    => 2,
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
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter' => 2,
                                        'id_page'    => 1,
                                        'lines'      => [
                                            'upsert' => [
                                                [
                                                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                    'id_chapter' => 2,
                                                    'id_page'    => 1,
                                                    'id_line'    => 1,
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

    public function testWhenFourthLevelItemsExistenceIsChecked()
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
                                    'words_count' => 35,
                                ]),
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 2,
                                    'id_line'     => 2,
                                    'words_count' => 40,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 2,
                                    'id_page'     => 1,
                                    'id_line'     => 1,
                                    'words_count' => 45,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book, true);

        $lines = $mutation->book->chapters->upsert->pages->upsert->lines->upsert;

        $itemDataThatExists = [
            'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'  => 1,
            'id_page'     => 2,
            'id_line'     => 1,
            'words_count' => 35,
        ];
        $this->assertTrue($lines->hasItem($itemDataThatExists));
        $itemDataThatDoesNotExist = [
            'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_chapter'  => 2,
            'id_page'     => 1,
            'id_line'     => 2,
            'words_count' => 50,
        ];
        $this->assertFalse($lines->hasItem($itemDataThatDoesNotExist));
    }

    public function testWhenFourthLevelItemsAreUnset()
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
                                    'words_count' => 35,
                                ]),
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 2,
                                    'id_line'     => 2,
                                    'words_count' => 40,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 2,
                                    'id_page'     => 1,
                                    'id_line'     => 1,
                                    'words_count' => 45,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book, true);

        unset($mutation->book->chapters->upsert->pages->upsert->lines);

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
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
                            'pages'      => [
                                'upsert' => [
                                    [
                                        'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                        'id_chapter'        => 2,
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

    public function testWhenFourthLevelItemsAreRemoved()
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
                                    'words_count' => 35,
                                ]),
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 1,
                                    'id_page'     => 2,
                                    'id_line'     => 2,
                                    'words_count' => 40,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
                new QueryItem([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter name',
                    'pov'        => 'first person',
                    'pages'      => new QueryCollection([
                        new QueryItem([
                            'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                            'id_chapter'        => 2,
                            'id_page'           => 1,
                            'has_illustrations' => false,
                            'lines'             => new QueryCollection([
                                new QueryItem([
                                    'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                    'id_chapter'  => 2,
                                    'id_page'     => 1,
                                    'id_line'     => 1,
                                    'words_count' => 45,
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $mutation = Mutation::build($this->itemConfigMock, $book, true);

        foreach ($mutation->book->chapters->upsert->pages->upsert->lines->upsert as $key => $line) {
            if (35 === $line->words_count) {
                $mutation->book->chapters->upsert->pages->upsert->lines->upsert->remove($line);
                break;
            }
        }

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
                                                    'id_line'     => 2,
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
                            'name'       => 'Chapter name',
                            'pov'        => 'first person',
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
                                                    'id_chapter'  => 2,
                                                    'id_page'     => 1,
                                                    'id_line'     => 1,
                                                    'words_count' => 45,
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
}
