#!/bin/bash

cmd="$1"
shift 1

DB_USER=$(php -r 'require "app/config/database.php"; echo get_class_vars("DATABASE_CONFIG")["default"]["login"];')
DB_PASS=$(php -r 'require "app/config/database.php"; echo get_class_vars("DATABASE_CONFIG")["default"]["password"];')

DB_PASS="$DB_PASS" DB_USER="$DB_USER" ./"$cmd" $*