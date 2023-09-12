#!/bin/bash

if [[ -z "${CRON_INTERVAL}" ]]; then
  echo "CRON_INTERVAL was not set, defaulting to 5 minutes"
  SLEEP_DURATION=5m
elif ! [[ "${CRON_INTERVAL}" =~ ^[0-9]+[mshd]$ ]]; then
  echo "CRON_INTERVAL value is not valid, defaulting to 5 minutes"
  SLEEP_DURATION=5m
else
  SLEEP_DURATION="${CRON_INTERVAL}"
fi

while true; do
  if ! php -f /var/www/html/cron.php; then
    echo "Something went wrong with cron.php, will try again in ${SLEEP_DURATION}"
  fi
  sleep "${SLEEP_DURATION}"
done
