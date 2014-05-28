
all: install-composer install-vendor

install-composer:

	@if [ ! -f bin/composer.phar ]; then curl -s http://getcomposer.org/installer \
	| php -d allow_url_fopen=1 -d date.timezone="Europe/Berlin" -- --install-dir=./bin; fi


install-vendor:

	@if [ -d vendor/agavi/agavi/ ]; then svn revert -R vendor/agavi/agavi/; fi
	@php -d allow_url_fopen=1 bin/composer.phar update --no-dev


.PHONY: install-composer install-vendor
