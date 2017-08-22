#!/bin/sh
set -ex
hhvm --version
apt-get update -y
apt-get install -y wget curl git
curl https://getcomposer.org/installer | hhvm -d hhvm.jit=0 --php -- /dev/stdin --install-dir=/usr/local/bin --filename=composer

cd /var/source
hhvm -d hhvm.jit=0 /usr/local/bin/composer install

hh_server --check $(pwd)
hhvm -d hhvm.php7.all=0 -d hhvm.jit=0 vendor/bin/phpunit
hhvm -d hhvm.php7.all=1 -d hhvm.jit=0 vendor/bin/phpunit

hhvm -d hhvm.jit=0 bin/generate-docs.php $(mktemp -d)

if [ "$TRAVIS_EVENT_TYPE" = "cron" -a "$HHVM_VERSION" = "latest" ]; then
  hhvm -d hhvm.jit=0 bin/update-release.php
fi
