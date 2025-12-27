# Default Dockerfile
#
# @link     https://www.hyperf.io
# @document https://hyperf.wiki
# @contact  group@hyperf.io
# @license  https://github.com/hyperf/hyperf/blob/master/LICENSE

FROM hyperf/hyperf:8.3-alpine-v3.19-swoole

ARG timezone

ENV TIMEZONE=${timezone:-"America/Sao_Paulo"}

# update
RUN set -ex \
    && apk add --no-cache \
       php83-dom \
       php83-xml \
       php83-xmlwriter \
       php83-mbstring \
       php83-tokenizer \
       php83-pecl-pcov \
    #  ---------- some config ----------
    && cd /etc/php* \
    # - config PHP
    && { \
        echo "extension=pcov.so"; \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
        echo "pcov.enabled=1"; \
        echo "pcov.directory=/opt/www/app"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

WORKDIR /opt/www

RUN git config --system --add safe.directory /opt/www

COPY . /opt/www
