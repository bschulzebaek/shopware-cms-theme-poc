<?php

namespace CmsPoc\Storefront;

use CmsPoc\Core\Content\ThemeTemplate\ThemeTemplateEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class DatabaseTwigLoader implements LoaderInterface
{

    /**
     * @param EntityRepository<ThemeTemplateEntity> $themeTemplateRepository
     */
    public function __construct(
        private readonly EntityRepository $themeTemplateRepository,
    )
    {

    }

    public function getSourceContext($name): Source
    {
        /** @var ThemeTemplateEntity $template  */
        $template = $this->themeTemplateRepository->search($this->buildCriteria($name), Context::createDefaultContext())->first();

        if (!$template) {
            throw new \LogicException(sprintf('Failed to load template "%s"!', $name));
        }

        return new Source($template->getContent(), $name);
    }

    public function getCacheKey($name): string
    {
        $this->throwIfNotExists($name);

        return $name;
    }

    public function isFresh(string $name, int $time): bool
    {
        $this->throwIfNotExists($name);

        return false;
    }

    public function exists(string $name): bool
    {
        return $this->themeTemplateRepository->searchIds($this->buildCriteria($name), Context::createDefaultContext())->getTotal() > 0;
    }

    private function buildCriteria(string $name): Criteria
    {
        $criteria = new Criteria();
        // Filter for current sales_channel.theme.id -> add to Entity!
        $criteria->addFilter(new EqualsFilter('name', $name));
        $criteria->addFilter(new EqualsFilter('active', true));

        return $criteria;
    }

    private function throwIfNotExists(string $name): void
    {
        if (!$this->exists($name)) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }
    }
}
