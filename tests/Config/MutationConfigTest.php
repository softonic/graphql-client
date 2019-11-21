<?php


namespace Softonic\GraphQL\Config;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Item;
use Softonic\GraphQL\MutationCollection;

class MutationConfigTest extends TestCase
{
    /**
     * @test
     */
    public function getMutation()
    {
        $mutationConfig = new MutationConfig(
            [
                'ReplaceProgram' => [
                    'program' => [
                        'linksTo'  => '.',
                        'type'     => Item::class,
                        'children' => [
                            'versions' => [
                                'type'     => Item::class,
                                'children' => [
                                    'upsert' => [
                                        'linksTo' => '.versions',
                                        'type'    => MutationCollection::class,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $upsertConfig          = new MutationConfigObject();
        $upsertConfig->type    = MutationCollection::class;
        $upsertConfig->linksTo = '.versions';

        $versionsConfig           = new MutationConfigObject();
        $versionsConfig->type     = Item::class;
        $versionsConfig->children = ['upsert' => $upsertConfig];

        $expectedMutationConfig           = new MutationConfigObject();
        $expectedMutationConfig->type     = Item::class;
        $expectedMutationConfig->linksTo  = '.';
        $expectedMutationConfig->children = ['versions' => $versionsConfig];

        $this->assertEquals(['program' => $expectedMutationConfig], $mutationConfig->get('ReplaceProgram'));
    }
}
