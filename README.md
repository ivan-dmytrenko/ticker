
## BTC Ticker

Built on top of Silex and WebSockets with using Reactphp, Ratchet and Redis

## Running

The project is fully configured with Docker and running as multi-container Docker application.
It built on NGINX, PHP and REDIS containers.

You can run project and all the needed things by using one of the command. Which are placed under bin folder.


#### If you're using Docker Machine (more often on OS X and Windows):

1. Create and run [`Docker Machine`](https://docs.docker.com/machine/install-machine/)

2. Add new entry to your **hosts** (`file your_docker_machine_ip btc-ticker.dev`) (e.g. (`192.168.99.100 btc-ticker.dev`))

3. Run sh script by typing (`bin/start.sh`) from the project root


#### If your Docker is using Unix socket and runs with sudo (more often on Linux):

1. Add new entry to your **hosts** (`file your_docker_machine_ip btc-ticker.dev`) (e.g. (`127.0.0.1 btc-ticker.dev`))

2. Run sh script by typing (`bin/startAsRoot.sh`) from the project root

Now you're able to open project in the browser [`http://btc-ticker.dev:8083/`](http://btc-ticker.dev:8083/)

## Tests

Run (`vendor/bin/phpunit`) from project root for running tests