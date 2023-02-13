FROM public.ecr.aws/unicc/lap81-alpine:stable

USER root
# Remove default static files.
RUN rm -rf /var/www/html

# Install required software
RUN apk add git patch

USER nobody
RUN mkdir --parents /var/www/drupal/web/sites/default/files /var/www/private && \
    ln -s /var/www/drupal/web /var/www/html && \
    ln -s /var/www/private /var/www/drupal/private

WORKDIR /var/www/drupal
COPY --chown=nobody:nobody scripts/ /var/www/drupal/scripts/
COPY --chown=nobody:nobody patches/ /var/www/drupal/patches/
COPY --chown=nobody:nobody composer.* /var/www/drupal/

# Set composer home directory to writeable dir to allow user 'nobody' to create the cache dir necessary to clone from vcs.
ENV COMPOSER_HOME /tmp/

RUN composer -vvv install
RUN composer -vvv require newrelic/monolog-enricher

RUN touch .env

COPY --chown=nobody:nobody ./ /var/www/drupal/

RUN date -Iseconds > '/var/www/html/build.html'

CMD ["/var/www/drupal/commands.sh"]
