#!/usr/bin/env bash

docker build -t php-utils .
docker run -it --rm --name php-utils-test php-utils