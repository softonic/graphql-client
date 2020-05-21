<?php

namespace Softonic\GraphQL\Query;

use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function emptyCollectionProvider()
    {
        return [
            'Empty collection' => [
                'collection' => new Collection(),
                'isEmpty'   => true,
            ],
            'Filled collection' => [
                'collection' => new Collection(['key' => 'value']),
                'isEmpty'   => false,
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider emptyCollectionProvider
     */
    public function checkEmptyCollection(Collection $collection, bool $isEmpty)
    {
        $this->assertSame($isEmpty, $collection->isEmpty());
    }

    public function filterProvider()
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
                'expectedResult' => new Collection([
                    new Item([
                        'id_book'   => 'a53493b0-4a24-40c4-b786-317f8dfdf897',
                        'id_author' => 1122,
                        'genre'     => 'drama',
                    ]),
                ]),
            ],
            'Filter matches two items'      => [
                'filters'        => [
                    'id_author' => 1234,
                ],
                'expectedResult' => new Collection([
                    new Item([
                        'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_author' => 1234,
                        'genre'     => null,
                    ]),
                    new Item([
                        'id_book'   => '8477244b-d939-4e34-8b45-446f85399a85',
                        'id_author' => 1234,
                        'genre'     => 'drama',
                    ]),
                ]),
            ],
            'Filter composed of two values' => [
                'filters'        => [
                    'id_author' => 1234,
                    'genre'     => 'drama',
                ],
                'expectedResult' => new Collection([
                    new Item([
                        'id_book'   => '8477244b-d939-4e34-8b45-446f85399a85',
                        'id_author' => 1234,
                        'genre'     => 'drama',
                    ]),
                ]),
            ],
            'Filter value is null'          => [
                'filters'        => [
                    'genre' => null,
                ],
                'expectedResult' => new Collection([
                    new Item([
                        'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                        'id_author' => 1234,
                        'genre'     => null,
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider filterProvider
     *
     * @param array      $filters
     * @param Collection $expectedResult
     */
    public function testFilter(array $filters, Collection $expectedResult)
    {
        $books = new Collection([
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
            new Item([
                'id_book'   => '8477244b-d939-4e34-8b45-446f85399a85',
                'id_author' => 1234,
                'genre'     => 'drama',
            ]),
        ]);

        $filteredBooks = $books->filter($filters);

        $this->assertEquals($expectedResult, $filteredBooks);
    }

    public function testFilterWhenRootIsAnItemAndTheFilterIsInSecondLevel()
    {
        $book = new Item([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new Collection([
                new Item([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 1,
                    'name'       => 'Chapter one',
                ]),
                new Item([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter two',
                ]),
            ]),
        ]);

        $book->chapters = $book->chapters->filter(['id_chapter' => 2]);

        $expectedResult = new Item([
            'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
            'id_author' => 1234,
            'genre'     => null,
            'chapters'  => new Collection([
                new Item([
                    'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
                    'id_chapter' => 2,
                    'name'       => 'Chapter two',
                ]),
            ]),
        ]);
        $this->assertEquals($expectedResult, $book);
    }
}
