#!/bin/sh -eu

while true
do 
  while php -f /var/www/html/cron.php 
    sleep 5m
  done
  echo "Something went wrong with cron.php"
done
