#!/bin/bash
cd /var/www/mylocation
git stash push --include-untracked
git stash drop
git pull
composer install
npm install
npm run build
