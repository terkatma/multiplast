#!/usr/bin/env bash
clear
rm -rf temp/cache
mkdir -p temp/sessions
chmod 777 -R temp
chmod 777 -R log
./scripts/dependencies.sh
composer update
vendor/bin/phinx migrate -e production
