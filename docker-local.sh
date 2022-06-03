#!/bin/bash

if [[ $1 == "up" ]]; then
    user=${USER} uid=${UID} docker-compose -f docker-compose.yaml up -d
elif [[ $1 == "down" ]]; then
    user=${USER} uid=${UID} docker-compose -f docker-compose.yaml down
elif [[ $1 == "start" ]]; then
    user=${USER} uid=${UID} docker-compose -f docker-compose.yaml start
elif [[ $1 == "stop" ]]; then
    user=${USER} uid=${UID} docker-compose -f docker-compose.yaml stop
elif [[ $1 == "build" ]]; then
    sed -e "s/LOG_CHANNEL=.*/LOG_CHANNEL=single/g" \
        -e "s/DB_HOST=.*/DB_HOST=db/g" \
        -e "s/DB_CONNECTION=.*/DB_CONNECTION=pgsql/g" \
        -e "s/DB_DATABASE=.*/DB_DATABASE=streamlabs/g" \
        -e "s/DB_USERNAME=.*/DB_USERNAME=root/g" \
        -e "s/DB_PORT=.*/DB_PORT=5432/g" \
        -e "s/DB_PASSWORD=.*/DB_PASSWORD=password/g" \
        -e "s/CACHE_DRIVER=.*/CACHE_DRIVER=file/g" \
        -e "s/SESSION_DRIVER=.*/SESSION_DRIVER=file/g" \
        -e "s/REDIS_HOST=.*/REDIS_HOST=redis/g" .env.example > .env

    user=${USER} uid=${UID} docker-compose -f docker-compose.yaml up -d --build

    #docker-compose -f docker-compose.yaml exec app bash -c "rm -rf vendor"
    docker-compose -f docker-compose.yaml exec app bash -c "composer install"
    docker-compose -f docker-compose.yaml exec app bash -c "php artisan key:generate"
    docker-compose -f docker-compose.yaml exec app bash -c "php artisan migrate:fresh --seed"

fi
