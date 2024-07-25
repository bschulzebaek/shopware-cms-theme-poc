<?php

namespace CmsPoc\Core;

use CmsPoc\Core\Content\ThemeTemplate\ThemeTemplateEntity;
use League\Flysystem\FilesystemOperator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeleteEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TemplateIndexerSubscriber implements EventSubscriberInterface
{
    final public const INDEX_FILE = 'template-index';
    /**
     * @param EntityRepository<ThemeTemplateEntity> $themeTemplateRepository
     */
    public function __construct(
        private readonly EntityRepository $themeTemplateRepository,
        private readonly FilesystemOperator $fs
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'theme_template.written' => 'onThemeTemplateWritten',
            'theme_template.delete' => 'onThemeTemplateDelete',
        ];
    }

    // TODO: This doesn't remove entries if theme_template.name changes
    public function onThemeTemplateWritten(EntityWrittenEvent $event): void
    {
        if ($event->getEntityName() !== ThemeTemplateEntity::ENTITY_NAME) {
            return;
        }

        $criteria = new Criteria($event->getIds());

        $templates = $this->themeTemplateRepository->search($criteria, $event->getContext());

        $toAdd = [];
        $toRemove = [];

        $templates->map(function (ThemeTemplateEntity $template) use (&$toAdd, &$toRemove){
            if ($template->isActive()) {
                $toAdd[] = $template->getName();
            } else {
                $toRemove[] = $template->getName();
            }
        });

        $this->appendToIndexFile($toAdd);
        $this->removeFromIndexFile($toRemove);
    }

    public function onThemeTemplateDelete(EntityDeleteEvent $event): void
    {
        $criteria = new Criteria([ $event->getIds(ThemeTemplateEntity::ENTITY_NAME) ]);
        $templates = $this->themeTemplateRepository->search($criteria, $event->getContext());

        $names = $templates->map(function (ThemeTemplateEntity $template) {
            return $template->getName();
        });

        $this->removeFromIndexFile($names);
    }

    private function appendToIndexFile(array $names): void {
        $content = '';

        if ($this->fs->has(self::INDEX_FILE)) {
            $content = $this->fs->read(self::INDEX_FILE);
        }

        foreach ($names as $name) {
            $line = $name . PHP_EOL;
            if (str_contains($content, $line)) {
                return;
            }

            $content .= $line;
        }

        $this->fs->write(self::INDEX_FILE, $content);
    }

    private function removeFromIndexFile(array $names): void {
        if (!$this->fs->has(self::INDEX_FILE)) {
            return;
        }

        $content = $this->fs->read(self::INDEX_FILE);

        foreach ($names as $name) {
            $content = str_replace($name . PHP_EOL, '', $content);
        }

        $this->fs->write(self::INDEX_FILE, $content);
    }
}
