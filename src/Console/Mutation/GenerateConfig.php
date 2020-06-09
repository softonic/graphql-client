<?php

namespace Softonic\GraphQL\Console\Mutation;

use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\DataObjects\Mutation\Collection;
use Softonic\GraphQL\DataObjects\Mutation\Item;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateConfig extends Command
{
    const SCALAR = 'SCALAR';

    const INPUT_OBJECT = 'INPUT_OBJECT';

    const LIST = 'LIST';

    protected static $defaultName = 'mutation:generate-config';

    protected $generatedFieldTypes = [];

    protected function configure()
    {
        $this->setDescription('Creates a mutation config.')
            ->setHelp('This command allows you to create a mutation config based in the result of a ' .
                'introspection query for a specific mutation.');

        $this
            ->addArgument(
                'instrospection-result',
                InputArgument::REQUIRED,
                'Json file with the introspection query result'
            )
            ->addArgument(
                'mutation',
                InputArgument::REQUIRED,
                'Mutation name to extract to the configuration'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->checkArguments($input, $output)) {
            return 1;
        }

        $jsonSchema = file_get_contents($input->getArgument('instrospection-result'));
        $mutation   = $input->getArgument('mutation');

        $mutationConfig = $this->generateConfig(json_decode($jsonSchema), $mutation);

        $output->writeln(var_export($mutationConfig, true));

        return 0;
    }

    private function checkArguments(InputInterface $input, OutputInterface $output)
    {
        $jsonPath = $input->getArgument('instrospection-result');

        if (!file_exists($jsonPath)) {
            $output->writeln("The file '{$jsonPath}' does not exist");

            return false;
        }

        return true;
    }

    private function generateConfig(StdClass $jsonSchema, string $mutation)
    {
        foreach ($jsonSchema->data->__schema->types as $type) {
            if ($type->name === 'Mutation' && $type->fields[0]->name === $mutation) {
                $initialMutationField = $type->fields[0]->args[0]->name;
                $inputType            = $type->fields[0]->args[0]->type->ofType->name;
                break;
            }
        }

        return [
            $mutation => [
                $initialMutationField => [
                    'linksTo'  => '.',
                    'type'     => Item::class,
                    'children' => $this->getMutationConfig($jsonSchema, $inputType),
                ],
            ],
        ];
    }

    private function getTypeFromField($field)
    {
        $isCollection = false;
        $type         = $field->type;

        while ($type->kind !== self::SCALAR && $type->kind !== self::INPUT_OBJECT) {
            if ($type->kind === self::LIST) {
                $isCollection = true;
            }
            $type = $type->ofType;
        }

        if ($type->kind === self::SCALAR) {
            return [
                'type'         => $type->kind,
                'isCollection' => $isCollection,
            ];
        }

        return [
            'type'         => $type->name,
            'isCollection' => $isCollection,
        ];
    }

    private function getMutationConfig(stdClass $jsonSchema, $inputType, $parentLinksTo = ''): array
    {
        $children = [];
        foreach ($jsonSchema->data->__schema->types as $type) {
            if ($type->name === $inputType) {
                foreach ($type->inputFields as $inputField) {
                    [
                        'type'         => $type,
                        'isCollection' => $isCollection,
                    ] = $this->getTypeFromField($inputField);

                    if ($type === self::SCALAR) {
                        $children[$inputField->name] = [];
                        continue;
                    }

                    if (!in_array($inputType, $this->generatedFieldTypes[$inputField->name] ?? [])) {
                        $this->generatedFieldTypes[$inputField->name][] = $inputType;
                        $linksTo                                        = "{$parentLinksTo}.{$inputField->name}";
                        $children[$inputField->name]                    = [
                            'linksTo'  => $linksTo,
                            'type'     => $isCollection ? Collection::class : Item::class,
                            'children' => $this->getMutationConfig($jsonSchema, $type, $linksTo),
                        ];
                    }
                }
                break;
            }
        }

        return $children;
    }
}
