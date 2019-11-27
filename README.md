# PHP GraphQL Client

[![Latest Version](https://img.shields.io/github/release/softonic/graphql-client.svg?style=flat-square)](https://github.com/softonic/graphql-client/releases)
[![Software License](https://img.shields.io/badge/license-Apache%202.0-blue.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/softonic/graphql-client/master.svg?style=flat-square)](https://travis-ci.org/softonic/graphql-client)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/softonic/graphql-client.svg?style=flat-square)](https://scrutinizer-ci.com/g/softonic/graphql-client/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/softonic/graphql-client.svg?style=flat-square)](https://scrutinizer-ci.com/g/softonic/graphql-client)
[![Total Downloads](https://img.shields.io/packagist/dt/softonic/graphql-client.svg?style=flat-square)](https://packagist.org/packages/softonic/graphql-client)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/softonic/graphql-client.svg?style=flat-square)](http://isitmaintained.com/project/softonic/graphql-client "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/softonic/graphql-client.svg?style=flat-square)](http://isitmaintained.com/project/softonic/graphql-client "Percentage of issues still open")

PHP Client for [GraphQL](http://graphql.org/)

## Installation

Via composer:
```
composer require softonic/graphql-client
```

## Documentation

### Working with the client

To instantiate a client with an OAuth2 provider:

``` php
<?php

$options = [
    'clientId' => 'myclient',
    'clientSecret' => 'mysecret',
];

$provider = new Softonic\OAuth2\Client\Provider\Softonic($options);

$config = ['grant_type' => 'client_credentials', 'scope' => 'myscope'];

$cache = new \Symfony\Component\Cache\Adapter\FilesystemAdapter();

$client = \Softonic\GraphQL\ClientBuilder::buildWithOAuth2Provider(
    'https://catalog.swarm.pub.softonic.one/graphql',
    $provider,
    $config,
    $cache
);

// Query Example
$query = <<<'QUERY'
query GetFooBar($idFoo: String, $idBar: String) {
  foo(id: $idFoo) {
    id_foo
    bar (id: $idBar) {
      id_bar
    }
  }
}
QUERY;
$variables = ['idFoo' => 'foo', 'idBar' => 'bar'];
$response = $client->query($query, $variables);

// Mutation Example
$mutation = <<<'MUTATION'
mutation ($foo: ObjectInput!){
  CreateObjectMutation (object: $foo) {
    status
  }
}
MUTATION;
$variables = [
    'foo' => [
        'id_foo' => 'foo', 
        'bar' => [
            'id_bar' => 'bar'
        ]
    ]
];
$response = $client->query($mutation, $variables);
```

To instantiate a client without OAuth2:

``` php
<?php
$client = \Softonic\GraphQL\ClientBuilder::build('https://catalog.swarm.pub.softonic.one/graphql');

$query = <<<'QUERY'
query GetFooBar($idFoo: String, $idBar: String) {
  foo(id: $idFoo) {
    id_foo
    bar (id: $idBar) {
      id_bar
    }
  }
}
QUERY;

$variables = [
    'idFoo' => 'foo',
    'idBar' => 'bar',
];
$response = $client->query($query, $variables);
```

### From query result to mutation

The query result can be obtained as an object which will provide facilities to convert it to a mutation and modify the data easily.
At the end, the mutation object will be able to be used as the variables of the mutation query in the GraphQL client.

First we execute a "read" query and obtain the result as an object compound of Items and Collections.

``` php
$response = $client->query($query, $variables);

$data = $response->getDataObject();

/**
 * $data = new QueryItem([
 *      'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *      'id_author' => 1234,
 *      'genre'     => 'adventure',
 *      'chapters'  => new QueryCollection([
 *          new QueryItem([
 *              'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *              'id_chapter' => 1,
 *              'name'       => 'Chapter One',
 *              'pov'        => 'first person',
 *              'pages'      => new QueryCollection([]),
 *          ]),
 *          new QueryItem([
 *              'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *              'id_chapter' => 2,
 *              'name'       => 'Chapter two',
 *              'pov'        => 'third person',
 *              'pages'      => new QueryCollection([
 *                  new QueryItem([
 *                      'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                      'id_chapter'        => 2,
 *                      'id_page'           => 1,
 *                      'has_illustrations' => false,
 *                  ]),
 *                  new QueryItem([
 *                      'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                      'id_chapter'        => 2,
 *                      'id_page'           => 2,
 *                      'has_illustrations' => false,
 *                  ]),
 *              ]),
 *          ]),
 *      ]),
 *  ]);
 */
```

The we can generate the mutation variables object from the previous query results. This is build using a mutation config.
The config specifies the mutation object type (Item or Collection), the location in the query result object where the data can be obtained and the possible children it has.

``` php
$mutationConfig = [
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

$mutation = new Mutation($mutationConfig, $data);

/**
 * $data = new QueryMutation([
 *      'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *      'id_author' => 1234,
 *      'genre'     => 'adventure',
 *      'chapters'  => new QueryCollection([
 *          new QueryMutation([
 *              'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *              'id_chapter' => 1,
 *              'name'       => 'Chapter One',
 *              'pov'        => 'first person',
 *              'pages'      => new QueryCollection([]),
 *          ]),
 *          new QueryMutation([
 *              'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *              'id_chapter' => 2,
 *              'name'       => 'Chapter two',
 *              'pov'        => 'third person',
 *              'pages'      => new QueryCollection([
 *                  new QueryMutation([
 *                      'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                      'id_chapter'        => 2,
 *                      'id_page'           => 1,
 *                      'has_illustrations' => false,
 *                  ]),
 *                  new QueryMutation([
 *                      'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                      'id_chapter'        => 2,
 *                      'id_page'           => 2,
 *                      'has_illustrations' => false,
 *                  ]),
 *              ]),
 *          ]),
 *      ]),
 *  ]);
 */
```

Now we can modify the mutation data using the methods add(), set() and filter().
* add(): Adds an Item to a Collection.
* set(): Updates some values of an Item.
* filter(): Filters the Items of the Collection.

``` php
$mutation->book->chapters->upsert->filter(['id_chapter' => 2])->pages->upsert->add([
    'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
    'id_chapter'        => 2,
    'id_page'           => 3,
    'has_illustrations' => false,
]);
$mutation->book->chapters->upsert->pages->upsert->filter([
    'id_chapter' => 2,
    'id_page'    => 2,
])->set(['has_illustrations' => true]);

/**
 * $mutation = [
 *     'book' => [
 *         'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *         'id_author' => 1234,
 *         'genre'     => 'adventure',
 *         'chapters'  => [
 *             'upsert' => [
 *                 [
 *                     'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                     'id_chapter' => 1,
 *                     'name'       => 'Chapter One',
 *                     'pov'        => 'first person',
 *                     'pages'      => new QueryCollection([]),
 *                 ],
 *                 [
 *                     'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                     'id_chapter' => 2,
 *                     'name'       => 'Chapter two',
 *                     'pov'        => 'third person',
 *                     'pages'      => [
 *                         'upsert' => [
 *                             [
 *                                 'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                                 'id_chapter'        => 2,
 *                                 'id_page'           => 1,
 *                                 'has_illustrations' => false,
 *                             ],
 *                             [
 *                                 'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                                 'id_chapter'        => 2,
 *                                 'id_page'           => 2,
 *                                 'has_illustrations' => true,
 *                             ],
 *                             [
 *                                 'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                                 'id_chapter'        => 2,
 *                                 'id_page'           => 3,
 *                                 'has_illustrations' => false,
 *                             ],
 *                         ],
 *                     ],
 *                 ],
 *             ],
 *         ],
 *     ],
 * ];
 */
```

Finally, the modified mutation data can be passed to the GraphQL client to execute the mutation.

``` php
$mutationQuery = <<<'QUERY'
mutation ($book: BookInput!){
  ReplaceBook (book: $book) {
    status
  }
}
QUERY;

$client->query($mutationQuery, $mutation);
```

NOTE: When the query is executed, the mutation variables are encoded using json_encode(). This modifies the mutation data just returning the items changed and its parents.
So the final variables sent to the query would be:

``` php
/**
 * $mutation = [
 *     'book' => [
 *         'id_book'   => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *         'id_author' => 1234,
 *         'genre'     => 'adventure',
 *         'chapters'  => [
 *             'upsert' => [
 *                 [
 *                     'id_book'    => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                     'id_chapter' => 2,
 *                     'name'       => 'Chapter two',
 *                     'pov'        => 'third person',
 *                     'pages'      => [
 *                         'upsert' => [
 *                             [
 *                                 'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                                 'id_chapter'        => 2,
 *                                 'id_page'           => 2,
 *                                 'has_illustrations' => true,
 *                             ],
 *                             [
 *                                 'id_book'           => 'f7cfd732-e3d8-3642-a919-ace8c38c2c6d',
 *                                 'id_chapter'        => 2,
 *                                 'id_page'           => 3,
 *                                 'has_illustrations' => false,
 *                             ],
 *                         ],
 *                     ],
 *                 ],
 *             ],
 *         ],
 *     ],
 * ];
 */
```

## Testing

`softonic/graphql-client` has a [PHPUnit](https://phpunit.de) test suite and a coding style compliance test suite using [PHP CS Fixer](http://cs.sensiolabs.org/).

To run the tests, run the following command from the project folder.

``` bash
$ docker-compose run test
```

To run interactively using [PsySH](http://psysh.org/):
``` bash
$ docker-compose run psysh
```

## License

The Apache 2.0 license. Please see [LICENSE](LICENSE) for more information.

[PSR-2]: http://www.php-fig.org/psr/psr-2/
[PSR-4]: http://www.php-fig.org/psr/psr-4/
