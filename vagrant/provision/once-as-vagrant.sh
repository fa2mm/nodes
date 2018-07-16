#!/usr/bin/env bash

app_path=$(echo "$1")

# install Composer plugin
cd ${app_path} \
    && composer install