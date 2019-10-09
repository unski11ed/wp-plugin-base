#!/usr/bin/env bash

docker container stop $(docker container ls -q --filter name=wp-plugin-base--mysql)
docker container stop $(docker container ls -q --filter name=wp-plugin-base--wordpress)

sudo chown -R 1000:1000 ./dist