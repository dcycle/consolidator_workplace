#!/bin/bash
#
# Get a one-time login link to your development environment.
#

docker-compose exec -T web /bin/bash -c \
  "drush -l http://$(docker-compose port web 80) uli"
