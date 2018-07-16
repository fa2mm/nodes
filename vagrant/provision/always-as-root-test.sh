#!/usr/bin/env bash

app_path=$(echo "$1")

cd ${app_path}vendor/bin \
    && codecept build \
    && codecept run