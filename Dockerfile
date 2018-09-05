FROM dcycle/drupal:7

# Make sure opcache is disabled during development so that our changes
# to PHP are reflected immediately.
RUN echo 'opcache.enable=0' >> /usr/local/etc/php/php.ini

# Download contrib modules
RUN drush dl devel -y

# Download consolidator
RUN apt-get -y install git
RUN echo 'docker cache buster 1426'
RUN cd sites/all/modules && git clone https://github.com/dcycle/consolidator

EXPOSE 80
