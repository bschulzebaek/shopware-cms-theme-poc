<?php

namespace CmsPoc\Storefront;

use CmsPoc\Core\TemplateIndexerSubscriber;
use Doctrine\DBAL\Connection;
use League\Flysystem\FilesystemOperator;
use Shopware\Core\Profiling\Profiler;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Service\ResetInterface;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class DatabaseTwigLoader implements LoaderInterface, ResetInterface
{
    // TODO: Delete these caches whenever the TemplateIndexerSubscriber is called
    final public const CACHE_KEY_INDEX = 'theme-templates-index';
    final public const CACHE_KEY_MOD = 'theme-templates-mod';

    public function __construct(
        private readonly Connection $connection,
        private readonly FilesystemOperator $fs,
        private readonly CacheInterface $cache
    )
    {
    }

    // TODO: When is this called? cache:clear? ..?
    public function reset(): void
    {
        $this->cache->delete(self::CACHE_KEY_INDEX);
        $this->cache->delete(self::CACHE_KEY_MOD);
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

    // TODO: Once we use theme IDs as namespace, we need to replace the prefix with the actual theme.id
    public function getCacheKey($name): string
    {
        return $name;
    }

    public function isFresh(string $name, int $time): bool
    {
        return $time > $this->cache->get(self::CACHE_KEY_MOD, fn () => $this->getLastIndexModification());
    }

    public function exists(string $name): bool
    {
        return Profiler::trace('DatabaseTwigLoader::exists', function () use ($name) {
//            return $this->exists_naive($name);
            return $this->exists_in_index_file($name);
//            return $this->exists_in_cache($name);
        }, 'ThemeTemplates');
    }

    private function exists_naive(string $name): bool
    {
        return $this->connection->fetchOne('SELECT 1 FROM theme_template WHERE name = :name AND active = 1', ['name' => $name]) !== false;
    }

    private function exists_in_index_file(string $name): bool
    {
        $content = $this->fs->read(TemplateIndexerSubscriber::INDEX_FILE);
        return str_contains($content, $name);
    }

    private function exists_in_cache(string $name): bool
    {
        $index = $this->cache->get(self::CACHE_KEY_INDEX, function () {
            if ($this->fs->has(TemplateIndexerSubscriber::INDEX_FILE) === false) {
                return [];
            }

            $content = $this->fs->read(TemplateIndexerSubscriber::INDEX_FILE);

            return array_fill_keys(array_filter(explode(PHP_EOL, $content)), true);
        });

        return $index[$name] ?? false;
    }

    private function getLastIndexModification(): int
    {
        if (!$this->fs->has(TemplateIndexerSubscriber::INDEX_FILE)) {
            return 0;
        }

        return $this->fs->lastModified(TemplateIndexerSubscriber::INDEX_FILE);
    }
}
