# CMS & Theme Templating PoC

Notes
* Associate templates with Themes, maybe use the theme.id as "package" name in template paths
* DBLoader performance? 
  * Pages are cached after 1st render
  * Cache invalidation?
  * Which pages are NOT cached?
* How does this impact other renderers, such as Email / App Script / Document?

Potential follow-ups
* Live preview of templates in Admin editor
* Create templates for new CMS Components
  * Create components (meta + options) via xml
  * Create their storefront counter-part as a custom template, without physical files
