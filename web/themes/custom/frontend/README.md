# Front-end theme

[Bootstrap 5](https://www.drupal.org/project/edwt) subtheme.

## Requirements

* node
* npm
* nvm

## Development

### Initial setup.

1. Switch node version: `nvm use`
2. Run `npm install` in this theme

### CSS compilation

To compile once, use `rpm run build:css`
To run the watcher, use `npm run watch:css`

### Adding general CSS that is applied to every page (e.g. header, footer, fonts)

- Type your SCSS in an existing component in dev/pcss
- OR create a new component in dev/pcss and use it by adding it in frontend.libraries.yml under global-styling.

### Adding CSS specific to sections of the site

- Create a new library in dev/components/*/libraries.yml (see dev/components/frontpage/libraries.yml)
- Create your SCSS or JS files in the same folder
- Include this library wherever it is needed using Twig files: {{ attach_library('frontend/LIBRARY_NAME') }} (see block--inline-block--frontpage-banner.html.twig)
