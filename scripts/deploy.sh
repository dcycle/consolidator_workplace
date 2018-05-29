#!/bin/bash
#
# Deploys an environment for development.
#
set -e

docker-compose build
docker-compose up -d
docker-compose exec web /bin/bash -c './scripts/lib/run.sh'

echo ''
echo 'If all went well you can now access your site with username admin and:'
echo 'password admin at:'
echo ''
echo ' => '"$(./scripts/uli.sh)"
echo ''
