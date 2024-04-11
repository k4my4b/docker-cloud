#!/bin/sh

# Function to convert PHP_MEMORY_LIMIT to MiB
convert_php_memory_limit_to_mib() {
    local php_memory_limit="$PHP_MEMORY_LIMIT"

    local php_memory_limit_numeric="$(echo "$php_memory_limit" | sed 's/[^0-9]//g' | tr '[:upper:]' '[:lower:]')"

    # Check if the unit is in GiB and convert it to MiB
    if [[ "$php_memory_limit" == *'G' ]]; then
        php_memory_limit_numeric=$((php_memory_limit_numeric * 1024))
    fi

    echo "$php_memory_limit_numeric"
}

# PHP-FPM configuration file
CONF_FILE="/usr/local/etc/php-fpm.d/www.conf"

# Server specifications
TOTAL_RAM_GB=$(( $(awk '/^MemTotal/ {print $2}' /proc/meminfo) / 1024 / 1024 ))
MAX_PHP_MEM_MB=$(convert_php_memory_limit_to_mib)

# Calculate parameters
# 75% of RAM, converted to MB
MAX_CHILDREN=$(($TOTAL_RAM_GB * 1024 * 3 / 4 / $MAX_PHP_MEM_MB)) 
# 20% of MAX_CHILDREN
START_SERVERS=$((MAX_CHILDREN / 5)) 
# 75% of START_SERVERS
MIN_SPARE_SERVERS=$((START_SERVERS * 3 / 4)) 
# 150% of START_SERVERS
MAX_SPARE_SERVERS=$((START_SERVERS * 3 / 2)) 

# Set parameters in PHP-FPM configuration file
sed -i "s/^pm.max_children.*/pm.max_children = $MAX_CHILDREN/" $CONF_FILE
sed -i "s/^pm.start_servers.*/pm.start_servers = $START_SERVERS/" $CONF_FILE
sed -i "s/^pm.min_spare_servers.*/pm.min_spare_servers = $MIN_SPARE_SERVERS/" $CONF_FILE
sed -i "s/^pm.max_spare_servers.*/pm.max_spare_servers = $MAX_SPARE_SERVERS/" $CONF_FILE

# Run original entrypoint 
sh /entrypoint.sh "$@"
