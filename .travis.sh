#!/bin/sh
set -ex
apt update -y
DEBIAN_FRONTEND=noninteractive apt install -y php-cli zip unzip
hhvm --version
php --version
if [ ! -e .git/refs/heads/master ]; then
  # - Travis clones with `--branch`, then moves to a detached HEAD state
  # - if we're on a detached HEAD, Composer uses master to resolve branch
  #   aliases.
  # So, create the master branch :p
  git branch master HEAD
fi

(
  cd $(mktemp -d)
  curl https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
)
composer install

hh_client
hhvm vendor/bin/hacktest tests/
