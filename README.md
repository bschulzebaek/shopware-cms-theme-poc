# CMS & Theme Templating PoC

### Injecting Themes into the TemplateFinder 

The `Shopware\Core\Framework\Adapter\Twig\TemplateFinder` is checking for the existence of a `Resources/views` directory in each bundle. Every bundle with this directory is considered by TwigLoaders, including the `CmsPoc\Storefront\DatabaseTwigLoader`.
* Check the `Shopware\Core\Framework\Adapter\Twig\NamespaceHierarchy\BundleHierarchyBuilder` for details
* Just by having the directory (e.g. by adding a `.gitkeep`), we can use the `@CmsPoc` namespace for templates 
    * Namespace depends on platform implementation (e.g. 1 hard-coded namespace to look up all themes, namespaces per theme, etc.)
* ToDo: Differentiate by Themes (`theme.id`) by injecting them into the list of valid bundles
    * Directly impacts the number of lookups (`Twig\Loader\LoaderInterface::exists`) -> `Loaders * Themes * Templates per Page`
    * Create index per theme.id?

### Maintaining the template index

TODO

### Loader performance

TODO

### Notes
* How does this impact other renderers, such as Email / App Script / Document?
* Writing HTML to the Database
* Try app cache
