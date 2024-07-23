<?php declare(strict_types=1);

namespace CmsPoc;

use CmsPoc\Storefront\DatabaseTwigLoaderPass;
use Shopware\Core\Framework\Plugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class CmsPoc extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config/'));
        $loader->load('services.xml');

        $container->addCompilerPass(new DatabaseTwigLoaderPass());
    }
}
