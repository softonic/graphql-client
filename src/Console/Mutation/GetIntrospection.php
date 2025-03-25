<?php

namespace Softonic\GraphQL\Console\Mutation;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetIntrospection extends Command
{
    protected static $defaultName = 'mutation:introspection';

    protected function configure()
    {
        $this->setDescription('Returns a instrospection query.')
            ->setHelp('Returns the introspection query needed to execute in your GraphQL server in order ' .
                'to generate the needed file to generate the config.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            <<<'GQL'
query IntrospectionQuery {
  __schema {
    mutationType { name }
    types {
      ...FullType
    }
  }
}

fragment FullType on __Type {
  kind
  name
  description
  fields(includeDeprecated: true) {
    name
    description
    args {
      ...InputValue
    }
  }
  inputFields {
    ...InputValue
  }
}

fragment InputValue on __InputValue {
  name
  description
  type { ...TypeRef }
  defaultValue
}

fragment TypeRef on __Type {
  kind
  name
  ofType {
    kind
    name
    ofType {
      kind
      name
      ofType {
        kind
        name
        ofType {
          kind
          name
          ofType {
            kind
            name
            ofType {
              kind
              name
              ofType {
                kind
                name
              }
            }
          }
        }
      }
    }
  }
}
GQL
        );
    }
}
