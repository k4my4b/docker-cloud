#!/bin/sh

tail -f "/dev/null"

trap "exit 0" EXIT TERM

exit 0
