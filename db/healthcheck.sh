#!/bin/bash
set -eo pipefail

if [ "$MYSQL_DATABASE" != "" ]; then
    mysql --user=$MYSQL_USER --password=$MYSQL_PASSWORD -e "USE $MYSQL_DATABASE;"
fi

mysqladmin ping -h localhost