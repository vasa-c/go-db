#!/usr/bin/env sh

cmd="/usr/bin/env phpcs --standard=PSR2 --encoding=utf-8 --ignore=vendor"

if [ "$#" -ne 0 ]; then
$cmd "$@"
else
$cmd .
fi
