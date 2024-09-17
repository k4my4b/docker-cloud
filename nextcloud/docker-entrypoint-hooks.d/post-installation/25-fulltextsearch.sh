#!/bin/sh

[ ! "$FTS_ENABLE" = "true" ] && exit 0

FTS_APPS="fulltextsearch fulltextsearch_elasticsearch files_fulltextsearch"

IFS=' '
# shellcheck disable=SC2086
set -- $FTS_APPS

for app in "$@"; do
    php occ app:install "$app" || exit 1
done

php occ fulltextsearch:configure \
    '{"search_platform":"OCA\\FullTextSearch_Elasticsearch\\Platform\\ElasticSearchPlatform"}' || exit 1

php occ fulltextsearch_elasticsearch:configure \
    "{\"elastic_host\":\"http://$FTS_USER:$FTS_PASSWORD@$FTS_HOST:$FTS_PORT\",\"elastic_index\":\"docker-cloud\"}" || exit 1

php occ files_fulltextsearch:configure \
    "{\"files_pdf\":\"1\",\"files_office\":\"1\"}" || exit 1

php occ fulltextsearch:test &&
    php occ fulltextsearch:index || exit 1
