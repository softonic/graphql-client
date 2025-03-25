<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Console\Mutation\GenerateConfig;
use Symfony\Component\Console\Tester\CommandTester;

class ConfigGeneratorTest extends TestCase
{
    public function testGenerateFromQuery(): void
    {
        $commandTester = new CommandTester(new GenerateConfig());
        $commandTester->execute(
            [
                'instrospection-result' => __DIR__ . '/fixtures/introspection.json',
                'mutation'              => 'replaceProgram',
            ]
        );
        $output = $commandTester->getDisplay();

        $expectedOutput = file_get_contents(__DIR__ . '/fixtures/config-from-query');
        $this->assertSame($expectedOutput, $output);
    }

    public function testGenerateFromMutation(): void
    {
        $commandTester = new CommandTester(new GenerateConfig());
        $commandTester->execute(
            [
                'instrospection-result' => __DIR__ . '/fixtures/introspection.json',
                'mutation'              => 'replaceProgram',
                '--from-same-mutation'  => true,
            ]
        );
        $output = $commandTester->getDisplay();

        $expectedOutput = file_get_contents(__DIR__ . '/fixtures/config-from-mutation');
        $this->assertSame($expectedOutput, $output);
    }
}
