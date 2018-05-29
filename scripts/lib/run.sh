#!/bin/bash
#
# This script is run in the docker container when it is ready. It prepares
# an environment for development or testing, which contains a full Drupal
# 7 installation with a running website and our custom modules.
#
set -e

echo "Will try to connect to MySQL container until it is up. This can take about 15 seconds."
OUTPUT="ERROR"
while [[ "$OUTPUT" == *"ERROR"* ]]
do
  OUTPUT=$(echo 'show databases'|{ mysql -h database -u drupal --password=drupal 2>&1 || true; })
  if [[ "$OUTPUT" == *"ERROR"* ]]; then
    echo "MySQL container is not available yet. Should not be long..."
    sleep 2
  else
    echo "MySQL is up! Moving on..."
  fi
done

OUTPUT=$(echo 'select * from users limit 1'|{ mysql --user=drupal --password=drupal --database=drupal --host=database 2>&1 || true; })
if [[ "$OUTPUT" == *"ERROR"* ]]; then
  echo "Installing Drupal because we did not find an entry in the users table"
  # In order to prevent the "unable to send mail" error, we are including
  # the "install_configure_form" line, which itself forces us to include the
  # profile (standard), which would otherwise be optional. See the output
  # of "drush help si" from where this is taken.
  cd /var/www/html && \
    drush si \
    -y \
    --db-url=mysql://drupal:drupal@database/drupal \
    --account-name=admin \
    --account-pass=admin \
    standard \
    install_configure_form.update_status_module='array(FALSE,FALSE)'
fi
echo "Assuming Drupal is already running, because there is a users table with at least one entry."
chmod -R 777 /var/www/html/sites/default/files
drush en -y consolidator_workplace devel devel_generate
drush cc all
