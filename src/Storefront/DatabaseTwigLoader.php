<?php

namespace CmsPoc\Storefront;

use CmsPoc\Core\TemplateIndexerSubscriber;
use Doctrine\DBAL\Connection;
use League\Flysystem\FilesystemOperator;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Profiling\Profiler;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class DatabaseTwigLoader implements LoaderInterface
{

    private array $templateIndex = [];
    private int $lastRead = 0;

    public function __construct(
        private readonly Connection $connection,
        private readonly FilesystemOperator $fs
    )
    {
    }

    public function getSourceContext($name): Source
    {
        return Profiler::trace('DatabaseTwigLoader::getSourceContext', function () use ($name) {
            $content = $this->connection->fetchAssociative('SELECT content FROM theme_template WHERE name = :name AND active = 1', ['name' => $name])['content'] ?? null;

            if (!$content) {
                throw new \LogicException(sprintf('Failed to load template "%s"!', $name));
            }

            return new Source($content, $name);
        }, 'ThemeTemplates');
    }

    public function getCacheKey($name): string
    {
        return $name;
    }

    public function isFresh(string $name, int $time): bool
    {
        return $time >= $this->lastRead;
    }

    public function exists(string $name): bool
    {
        return Profiler::trace('DatabaseTwigLoader::exists', function () use ($name) {
//            return $this->exists_naive($name);
            return $this->exists_in_index_file($name);
        }, 'ThemeTemplates');
    }

    private function exists_naive(string $name): bool
    {
        return $this->connection->fetchOne('SELECT 1 FROM theme_template WHERE name = :name AND active = 1', ['name' => $name]) !== false;
    }

    private function exists_in_index_file(string $name): bool
    {
        $lastMod = $this->getLastIndexModification();

        if ($lastMod > $this->lastRead) {
            $this->updateTemplateIndexFromFile($lastMod);
        }

        return $this->templateIndex[$name] ?? false;
    }

    private function updateTemplateIndexFromFile(int $lastMod): void
    {
        if ($this->fs->has(TemplateIndexerSubscriber::INDEX_FILE) === false) {
            $this->templateIndex = [];

            return;
        }

        $content = $this->fs->read(TemplateIndexerSubscriber::INDEX_FILE);
        $names = explode(PHP_EOL, $content);
        $names = array_filter($names);
        $names = array_fill_keys($names, true);

        $this->templateIndex = $names;
        $this->lastRead = $lastMod;
    }

    private function getLastIndexModification(): int
    {
        if (!$this->fs->has(TemplateIndexerSubscriber::INDEX_FILE)) {
            return 0;
        }

        return $this->fs->lastModified(TemplateIndexerSubscriber::INDEX_FILE);
    }

    private function buildCriteria(string $name): Criteria
    {
        return (new Criteria())
            ->addFilter(new EqualsFilter('name', $name))
            ->addFilter(new EqualsFilter('active', true));
    }
}
