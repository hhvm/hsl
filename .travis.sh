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
if (hhvm --version | grep -q -- -dev); then
  # Doesn't exist in master, but keep it here so that we can test release
  # branches on nightlies too
  rm -f composer.lock
fi
composer install

hh_client
vendor/bin/hacktest tests/
