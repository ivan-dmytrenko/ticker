#!/bin/sh

sudo docker-compose up --build -d
sudo docker exec -it btc_ticker_php composer install
redisHost=$(sudo docker inspect --format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' btc_ticker_redis)
redisHost="${redisHost} redis-server.dev"
sudo docker exec -it btc_ticker_php /bin/sh -c "echo ${redisHost} >> /etc/hosts"

sudo docker exec -d btc_ticker_php /bin/sh -c "bin/console btcticker:run"

/bin/echo -e "\e[32mThe project has been configured. Try to run it on http://btc-ticker.dev:8083\033[0m"