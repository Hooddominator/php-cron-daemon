#!/bin/bash
#docker pull php:5.6-cli
cd "$(dirname "$0")"

if [[ "$(docker images -q wiryonolau/php:5.6-cli 2> /dev/null)" == "" ]]; then
    docker build -t wiryonolau/php:5.6-cli .
fi
docker container stop php-cron-daemon
docker container rm php-cron-daemon

docker run -d --rm -it -v $(pwd)/../:/srv/php-cron-daemon -w /srv/php-cron-daemon -e COMPOSER_CACHE_DIR=/srv/php-cron-daemon/composer_cache -e USER_ID=1000 --name php-cron-daemon wiryonolau/php:5.6-cli

docker exec php-cron-daemon gosu 1000 php composer.phar self-update
docker exec php-cron-daemon gosu 1000 php composer.phar install --no-plugins --no-scripts --no-dev --prefer-dist -v
docker exec -it php-cron-daemon /bin/bash
