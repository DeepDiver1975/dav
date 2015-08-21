#!/usr/bin/env bash
find . -name \*.php -not -path './vendor/*' -exec php -l "{}" \;
