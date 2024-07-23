<?php declare(strict_types=1);

namespace CmsPoc\Core\Content\ThemeTemplate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ThemeTemplateEntity $entity)
 * @method void set(string $key, ThemeTemplateEntity $entity)
 * @method ThemeTemplateEntity[] getIterator()
 * @method ThemeTemplateEntity[] getElements()
 * @method ThemeTemplateEntity|null get(string $key)
 * @method ThemeTemplateEntity|null first()
 * @method ThemeTemplateEntity|null last()
 */
class ThemeTemplateCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ThemeTemplateEntity::class;
    }
}
