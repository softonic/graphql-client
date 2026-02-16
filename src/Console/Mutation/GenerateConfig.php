<?php

namespace Softonic\GraphQL\Console\Mutation;

use Softonic\GraphQL\DataObjects\Mutation\Collection;
use Softonic\GraphQL\DataObjects\Mutation\Item;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateConfig extends Command
{
    const SCALAR = 'SCALAR';

    const INPUT_OBJECT = 'INPUT_OBJECT';

    const LIST = 'LIST';

    protected static $defaultName = 'mutation:generate-config';

    protected $generatedFieldTypes = [];

    /**
     * @var bool|string|string[]|null
     */
    private $fromSameMutation;

    protected function configure(): void
    {
        $this->setDescription('Creates a mutation config.')
            ->setHelp(
                'This command allows you to create a mutation config based in the result of an introspection query'
                . ' for a specific mutation.'
            );

        $this->addArgument(
            'instrospection-result',
            InputArgument::REQUIRED,
            'Json file with the introspection query result'
        )
            ->addOption(
                'from-same-mutation',
                'z',
                InputOption::VALUE_NONE,
                'Flag to determine if the input for this config is the same mutation output'
            )
            ->addArgument(
                'mutation',
                InputArgument::REQUIRED,
                'Mutation name to extract to the configuration'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->checkArguments($input, $output)) {
            return 1;
        }

        $jsonSchema             = file_get_contents($input->getArgument('instrospection-result'));
        $mutation               = $input->getArgument('mutation');
        $this->fromSameMutation = $input->getOption('from-same-mutation');

        $mutationConfig = $this->generateConfig(json_decode($jsonSchema), $mutation);

        $output->writeln(var_export($mutationConfig, true));

        return 0;
    }

    private function checkArguments(InputInterface $input, OutputInterface $output): bool
    {
        $jsonPath = $input->getArgument('instrospection-result');

        if (!file_exists($jsonPath)) {
            $output->writeln("The file '{$jsonPath}' does not exist");

            return false;
        }

        return true;
    }

    private function generateConfig(StdClass $jsonSchema, string $mutation): array
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
                    'children' => $this->getMutationConfig($jsonSchema->data->__schema->types, $inputType),
                ],
            ],
        ];
    }

    private function getTypeFromField($field): array
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

    private function getMutationConfig(array $graphqlTypes, $inputType, string $parentLinksTo = ''): array
    {
        $children = [];
        foreach ($graphqlTypes as $graphqlType) {
            if ($graphqlType->name === $inputType) {
                foreach ($graphqlType->inputFields as $inputField) {
                    [
                        'type'         => $inputFieldType,
                        'isCollection' => $isCollection,
                    ] = $this->getTypeFromField($inputField);

                    if ($inputFieldType === self::SCALAR) {
                        $children[$inputField->name] = [];
                        continue;
                    }

                    // Avoid cyclic relations to define infinite configs.
                    if ($this->isFieldPreviouslyAdded($inputFieldType, $parentLinksTo)) {
                        continue;
                    }

                    $children[$inputField->name] = $this->getFieldInfo(
                        $graphqlTypes,
                        $inputFieldType,
                        $inputField->name,
                        $parentLinksTo,
                        $isCollection
                    );
                }

                break;
            }
        }

        return $children;
    }

    private function isFieldPreviouslyAdded($inputType, string $linksTo): bool
    {
        $linksParts = explode('.', $linksTo);
        for ($i=1, $iMax = count($linksParts); $i<= $iMax; $i++) {
            $parentLink = implode('.', array_slice($linksParts, 0, $i));
            if (
                in_array($inputType, $this->generatedFieldTypes[$parentLink] ?? [])
            ) {
                return true;
            }
        }

        $this->generatedFieldTypes[$linksTo][] = $inputType;

        return false;
    }

    private function getFieldInfo(
        array $graphqlTypes,
        $graphqlType,
        $inputFieldName,
        string $parentLinksTo,
        bool $isCollection
    ): array {
        if ($this->fromSameMutation) {
            return $this->defineConfigLinkedInputType(
                "{$parentLinksTo}.{$inputFieldName}",
                $isCollection,
                $graphqlTypes,
                $graphqlType
            );
        }

        $queryType       = $this->getQueryTypeFromInputType($graphqlType);
        $queryTypeExists = $this->queryTypeExists($queryType, $graphqlTypes);

        if ($queryTypeExists) {
            return $this->defineConfigLinkedInputType(
                $parentLinksTo,
                $isCollection,
                $graphqlTypes,
                $graphqlType
            );
        }

        return $this->defineConfigNotLinkedInputType(
            "{$parentLinksTo}.{$inputFieldName}",
            $isCollection,
            $graphqlTypes,
            $graphqlType
        );
    }

    private function getQueryTypeFromInputType($inputType): string
    {
        return preg_replace('/Input$/', '', $inputType);
    }

    private function queryTypeExists(string $queryType, array $types): bool
    {
        foreach ($types as $type) {
            if ($type->name === $queryType) {
                return true;
            }
        }

        return false;
    }

    private function defineConfigLinkedInputType(
        string $linksTo,
        bool $isCollection,
        array $graphqlTypes,
        string $type
    ): array {
        return [
            'linksTo'  => $linksTo,
            'type'     => $isCollection ? Collection::class : Item::class,
            'children' => $this->getMutationConfig($graphqlTypes, $type, $linksTo),
        ];
    }

    private function defineConfigNotLinkedInputType(
        string $linksTo,
        bool $isCollection,
        array $graphqlTypes,
        string $type
    ): array {
        return [
            'type'     => $isCollection ? Collection::class : Item::class,
            'children' => $this->getMutationConfig($graphqlTypes, $type, $linksTo),
        ];
    }
}
