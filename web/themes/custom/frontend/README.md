# Front-end theme

[Bootstrap 5](https://www.drupal.org/project/edwt) subtheme.

## Development.

### Adding general CSS that is applied to every page (e.g. header, footer, fonts)

- Type your SCSS in an existing component in src/scss/components
- OR create a new component in src/scss/components and import it in src/scss/_components.scss

### Adding CSS specific to sections of the site

- Create a new library in templates/components/*/libraries.yml (see templates/components/frontpage/libraries.yml)
- Create your SCSS or JS files in the same folder
- Include this library wherever it is needed using Twig files: {{ attach_library('frontend/LIBRARY_NAME') }} (see block--inline-block--frontpage-banner.html.twig)

### CSS compilation.

When starting development, run `npm install`

To compile once, use `gulp`
To run the watcher, use `gulp watch`


