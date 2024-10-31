#!/usr/bin/env bash

docker build -t php-utils .
docker run -it --rm -v ./vendor:/opt/app/vendor --name php-utils-test php-utils