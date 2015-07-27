#!/usr/bin/env bash
# Run phpunit tests
cd tests
phpunit --configuration phpunit.xml

# Create coverage report
wget https://scrutinizer-ci.com/ocular.phar
php ocular.phar code-coverage:upload --format=php-clover clover.xml
