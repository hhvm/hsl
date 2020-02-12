#!/bin/sh
set -ex
hhvm --version

hhvm composer install

hh_client

vendor/bin/hacktest tests/

echo > .hhconfig
hh_server --check $(pwd)
