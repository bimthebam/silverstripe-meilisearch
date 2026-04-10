<?php

namespace BimTheBam\Meilisearch\Dev\Task;

use BimTheBam\Meilisearch\Index;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Throwable;

/**
 * Class RebuildAllIndexesTask
 * @package BimTheBam\Meilisearch\Dev\Task
 */
class RebuildAllIndexesTask extends BuildTask
{
    /**
     * @var string
     */
    protected static string $commandName = 'meilisearch-rebuild-all-indexes';

    /**
     * @var string
     */
    protected string $title = 'Rebuild all meilisearch indexes';

    /**
     * @var string
     */
    protected static string $description = 'Rebuilds all configured mailsearch indexes';

    /**
     * @param InputInterface $input
     * @param PolyOutput $output
     * @return int
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Throwable
     */
    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        foreach (ClassInfo::subclassesFor(Index::class, false) as $indexClass) {
            /** @var Index $index */
            $index = Injector::inst()->create($indexClass);

            $output->writeln('Rebuilding index: ' . $indexClass);
            $index->rebuild();
        }

        return Command::SUCCESS;
    }
}
