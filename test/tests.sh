#!/bin/sh

DEFAULT_OPTIONS=""

if [ $# -eq 0 ]
then
    test_path="."
    options=$DEFAULT_OPTIONS
else
    test_path=$@
    options=""
fi

/usr/bin/env phpunit --configuration "tests.xml" $options $test_path

