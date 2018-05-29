Consolidator Workplace
=====

[![CircleCI](https://circleci.com/gh/dcycle/consolidator_workplace.svg?style=svg)](https://circleci.com/gh/dcycle/consolidator_workplace)

A report meant to be used with the [Consolidator](https://github.com/dcycle/consolidator) module.

Usage
-----

Make sure you have a [Facebook Workplace](https://www.facebook.com/workplace) account and API key, and that your IP is white-listed with Workplace.

Put your API key in your settings file, like so:

    $conf['consolidator_workplace_graph_url'] = 'https://graph.facebook.com';
    $conf['consolidator_workplace_scim_url'] = 'https://www.facebook.com/company/abc123/scim';
    $conf['consolidator_workplace_api_key'] = 'abc123';

Install [Devel](https://www.drupal.org/project/devel)'s "devel_generate" module, and generate 100 or so users (unless you have users on your site already).

Install [Consolidator](https://github.com/dcycle/consolidator) and this module, go to /admin/reports/consolidator, and select the report "Facebook Workplace Users vs. Drupal users". Submit and view the report results.

Development and demos
-----

Install Docker and run:

    ./scripts/deploy.sh

This will give you a login link to a development environment.

Add your API by modifying the settings file on your container like so:

    docker-compose exec web /bin/bash

Once on your container modify the file with your API key as documented above:

    chmod u+w /var/www/html/sites/default/settings.php
    vi /var/www/html/sites/default/settings.php

To run a drush command, run:

    docker-compose exec web /bin/bash -c 'drush help'

To temporarily shut down your development environment, run:

    docker-compose down

Bring it back up by typing

    ./scripts/deploy.sh

To destroy your development environment, run:

    docker-compose down -v
