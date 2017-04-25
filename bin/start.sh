#!/bin/sh

#docker-machine start default
#docker-machine env default
#eval $(docker-machine env default)

docker-compose up --build -d
docker exec -it btc_ticker_php composer install

redisHost=$(docker inspect --format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' btc_ticker_redis)
redisHost="${redisHost} redis-server.dev"
docker exec -it btc_ticker_php /bin/sh -c "echo ${redisHost} >> /etc/hosts"

docker exec -d btc_ticker_php /bin/sh -c "bin/console btcticker:run"

/bin/echo -e "project has been configured. Try to run it on http://btc-ticker.dev:8083"