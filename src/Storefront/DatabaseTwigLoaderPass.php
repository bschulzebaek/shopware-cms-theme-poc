<?php

namespace CmsPoc\Storefront;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DatabaseTwigLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
//        if (false === $container->hasDefinition('twig')) {
//            return;
//        }

//        $environment = $container->getDefinition('twig');
//        $environment->addMethodCall('setLoader', [new Reference('CmsPoc\Storefront\DatabaseTwigLoader')]);

//        if (false === $container->hasDefinition('twig.loader.chain')) {
//            throw new \RuntimeException('Twig\Loader\ChainLoader not found');
//        }
//
//        $chainLoader = $container->getDefinition('twig.loader.chain');
//        $chainLoader->addMethodCall('addLoader', [new Reference('CmsPoc\Storefront\DatabaseTwigLoader')]);
    }
}
