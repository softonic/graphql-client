<?php

namespace Softonic\GraphQL\Query;

use BadMethodCallException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\DataObjects\Query\Collection;
use Softonic\GraphQL\DataObjects\Query\Item;

class CollectionTest extends TestCase
{
    public static function emptyCollectionProvider(): array
    {
        return [
            'Empty collection'  => [
                'collection' => new Collection(),
                'isEmpty'    => true,
            ],
            'Filled collection' => [
                'collection' => new Collection([new Item(['key' => 'value'])]),
                'isEmpty'    => false,
            ],
        ];
    }

    #[DataProvider('emptyCollectionProvider')]
    #[Test]
    public function checkEmptyCollection(Collection $collection, bool $isEmpty): void
    {
        $this->assertSame($isEmpty, $collection->isEmpty());
    }

    public static function filterProvider(): array
    {
        return [
            'Filter matches no item'        => [
                'filters'        => [
                    'genre' => 'adventure',
                ],
                'expectedResult' => new Collection([]),
            ],
            'Filter matches one item'       => [
                'filters'        => [
                    'id_book' => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                ],
                'expectedResult' => new Collection(
                    [
                        new Item(
                            [
                                'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                                'id_author' => 1122,
                                'genre'     => 'drama',
                            ]
                        ),
                    ]
                ),
            ],
            'Filter matches two items'      => [
                'filters'        => [
                    'id_author' => 1234,
                ],
                'expectedResult' => new Collection(
                    [
                        new Item(
                            [
                                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_author' => 1234,
                                'genre'     => null,
                            ]
                        ),
                        new Item(
                            [
                                'id_book'   => '8477244b-d939-4e34-8b45-446f85399a85',
                                'id_author' => 1234,
                                'genre'     => 'drama',
                            ]
                        ),
                    ]
                ),
            ],
            'Filter composed of two values' => [
                'filters'        => [
                    'id_author' => 1234,
                    'genre'     => 'drama',
                ],
                'expectedResult' => new Collection(
                    [
                        new Item(
                            [
                                'id_book'   => '8477244b-d939-4e34-8b45-446f85399a85',
                                'id_author' => 1234,
                                'genre'     => 'drama',
                            ]
                        ),
                    ]
                ),
            ],
            'Filter value is null'          => [
                'filters'        => [
                    'genre' => null,
                ],
                'expectedResult' => new Collection(
                    [
                        new Item(
                            [
                                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_author' => 1234,
                                'genre'     => null,
                            ]
                        ),
                    ]
                ),
            ],
        ];
    }

    /**
     * @dataProvider filterProvider
     */
    public function testFilter(array $filters, Collection $expectedResult): void
    {
        $books = new Collection(
            [
                new Item(
                    [
                        'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_author' => 1234,
                        'genre'     => null,
                    ]
                ),
                new Item(
                    [
                        'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                        'id_author' => 1122,
                        'genre'     => 'drama',
                    ]
                ),
                new Item(
                    [
                        'id_book'   => '8477244b-d939-4e34-8b45-446f85399a85',
                        'id_author' => 1234,
                        'genre'     => 'drama',
                    ]
                ),
            ]
        );

        $filteredBooks = $books->filter($filters);

        $this->assertEquals($expectedResult, $filteredBooks);
    }

    public function testFilterWhenRootIsAnItemAndTheFilterIsInSecondLevel(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter one',
                            ]
                        ),
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter two',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $book->chapters = $book->chapters->filter(['id_chapter' => 2]);

        $expectedResult = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter two',
                            ]
                        ),
                    ]
                ),
            ]
        );
        $this->assertEquals($expectedResult, $book);
    }

    public function testUniqueLevelToArray(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter one',
                            ]
                        ),
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter two',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $chapters = $book->chapters->toArray();

        $expectedResult = [
            [
                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter' => 1,
                'name'       => 'Chapter one',
            ],
            [
                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_chapter' => 2,
                'name'       => 'Chapter two',
            ],
        ];
        $this->assertEquals($expectedResult, $chapters);
    }

    public function testSiblingsToArray(): void
    {
        $book = new Collection(
            [
                new Item(
                    [
                        'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_author' => 1234,
                        'genre'     => null,
                        'chapters'  => new Collection(
                            [
                                new Item(
                                    [
                                        'id_book'    => 'ba828dd3-951f-4cb4-b731-b4601f19414f',
                                        'id_chapter' => 1,
                                        'name'       => 'Chapter one - Book one',
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
                new Item(
                    [
                        'id_book'   => '0c72d70e-3e24-4975-b8c2-704ac1723f5f',
                        'id_author' => 4321,
                        'genre'     => null,
                        'chapters'  => new Collection(
                            [
                                new Item(
                                    [
                                        'id_book'    => '2001fe69-e28a-4c2f-accf-7210d575051c',
                                        'id_chapter' => 1,
                                        'name'       => 'Chapter one - Book two',
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $chapters = $book->chapters->toArray();

        $expectedResult = [
            [
                'id_book'    => 'ba828dd3-951f-4cb4-b731-b4601f19414f',
                'id_chapter' => 1,
                'name'       => 'Chapter one - Book one',
            ],
            [
                'id_book'    => '2001fe69-e28a-4c2f-accf-7210d575051c',
                'id_chapter' => 1,
                'name'       => 'Chapter one - Book two',
            ],
        ];
        $this->assertEquals($expectedResult, $chapters);

        $this->assertEquals(
            [
                'Chapter one - Book one',
                'Chapter one - Book two',
            ],
            $book->chapters->name->toArray()
        );
    }

    public function testItemHas(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
            ]
        );

        $this->assertTrue($book->has('id_author'));
        $this->assertTrue($book->has('genre'));
        $this->assertFalse($book->has('invalid'));
    }

    public function testHasMethodForThirdLevelItems(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter name',
                                'pov'        => null,
                            ]
                        ),
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter name',
                                'pov'        => null,
                                'pages'      => new Collection(
                                    [
                                        new Item(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 1,
                                                'id_page'           => 1,
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

        $this->assertTrue($book->has('chapters.pages'));
        $this->assertFalse($book->has('chapters.invalid'));
        $this->assertTrue($book->has('chapters.pages.has_illustrations'));
        $this->assertFalse($book->has('chapters.pages.invalid'));
        $this->assertTrue($book->chapters->has('pages.has_illustrations'));
        $this->assertFalse($book->chapters->has('pages.invalid'));
        $this->assertTrue($book->chapters->has('pages.has_illustrations'));
        $this->assertFalse($book->chapters->has('pages.invalid'));
        $this->assertFalse($book->has('not_existing.invalid'));
    }

    public function testWhenFourthLevelItemsExistenceIsChecked(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter name',
                                'pov'        => 'first person',
                                'pages'      => new Collection(
                                    [
                                        new Item(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 1,
                                                'id_page'           => 1,
                                                'has_illustrations' => false,
                                                'lines'             => new Collection(
                                                    [
                                                        new Item(
                                                            [
                                                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                                'id_chapter'  => 1,
                                                                'id_page'     => 1,
                                                                'id_line'     => 1,
                                                                'words_count' => 30,
                                                            ]
                                                        ),
                                                    ]
                                                ),
                                            ]
                                        ),
                                        new Item(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 1,
                                                'id_page'           => 2,
                                                'has_illustrations' => false,
                                                'lines'             => new Collection(
                                                    [
                                                        new Item(
                                                            [
                                                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                                'id_chapter'  => 1,
                                                                'id_page'     => 2,
                                                                'id_line'     => 1,
                                                                'words_count' => 35,
                                                            ]
                                                        ),
                                                        new Item(
                                                            [
                                                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                                'id_chapter'  => 1,
                                                                'id_page'     => 2,
                                                                'id_line'     => 2,
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
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter name',
                                'pov'        => 'first person',
                                'pages'      => new Collection(
                                    [
                                        new Item(
                                            [
                                                'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                'id_chapter'        => 2,
                                                'id_page'           => 1,
                                                'has_illustrations' => false,
                                                'lines'             => new Collection(
                                                    [
                                                        new Item(
                                                            [
                                                                'id_book'     => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                                                'id_chapter'  => 2,
                                                                'id_page'     => 1,
                                                                'id_line'     => 1,
                                                                'words_count' => 45,
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
                    ]
                ),
            ]
        );

        $lines = $book->chapters->pages->lines;

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

    public function testArrayAccessOffsetSetShouldThrowABadMethodCallException(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter one',
                                'pov'        => 'first person',
                            ]
                        ),
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter two',
                                'pov'        => 'third person',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Try using add() instead');

        $book->chapters->name[0] = 'Chapter three';
    }

    public function testArrayAccessOffsetExists(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter one',
                                'pov'        => 'first person',
                            ]
                        ),
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter two',
                                'pov'        => 'third person',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->assertTrue(isset($book->chapters->name[1]));
        $this->assertFalse(isset($book->chapters->name[2]));
        $this->assertFalse(empty($book->chapters->name[1]));
        $this->assertTrue(empty($book->chapters->name[2]));
    }

    public function testArrayAccessOffsetUnsetShouldThrowABadMethodCallException(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter one',
                                'pov'        => 'first person',
                            ]
                        ),
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter two',
                                'pov'        => 'third person',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Try using remove() instead');

        unset($book->chapters->name[0]);
    }

    public function testArrayAccessOffsetGet(): void
    {
        $book = new Item(
            [
                'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                'id_author' => 1234,
                'genre'     => null,
                'chapters'  => new Collection(
                    [
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 1,
                                'name'       => 'Chapter one',
                                'pov'        => 'first person',
                            ]
                        ),
                        new Item(
                            [
                                'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                                'id_chapter' => 2,
                                'name'       => 'Chapter two',
                                'pov'        => 'third person',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->assertEquals('Chapter one', $book->chapters->name[0]);
        $this->assertEquals('Chapter two', $book->chapters->name[1]);
    }
}
