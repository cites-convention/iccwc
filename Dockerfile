FROM public.ecr.aws/unicc/lap80-alpine:stable

COPY --chown=nobody:nobody web /var/www/web
ADD --chown=nobody:nobody commands.sh /var/www/commands.sh

USER root
RUN rm -rf /var/www/html/ && ln -s /var/www/web /var/www/html
USER nobody

WORKDIR /var/www
CMD ["/var/www/commands.sh"]

