#!/usr/bin/env bash

### ONLY FOR TEST SERVER

app_path=$(echo "$1")
root_pass=$(echo "$2")
db_name=$(echo "$3")
db_user=$(echo "$4")
db_pass=$(echo "$5")

## allow access from any ip (only DEV)
sudo sed -i 's/^bind-address.*127\.0\.0\.1/bind-address = 0\.0\.0\.0/g' /etc/mysql/mysql.conf.d/mysqld.cnf

## Test DB
mysql -u root -p${root_pass} -e "CREATE DATABASE ${db_name};
    CREATE USER '${db_user}'@'%' IDENTIFIED BY '${db_pass}';
    GRANT ALL PRIVILEGES ON *.* TO '${db_user}'@'%' IDENTIFIED BY '${db_pass}'
    WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
    GRANT ALL PRIVILEGES ON ${db_name}.* TO '${db_user}'@'%';
    CREATE TABLE IF NOT EXISTS ${db_name}.category (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        title VARCHAR(250) NOT NULL,
        lft INT UNSIGNED NULL,
        rgt INT UNSIGNED NULL,
        lvl INT UNSIGNED NULL,
        PRIMARY KEY (id),
        UNIQUE KEY idx_lft_rgt_lvl (lft, rgt, lvl)
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COLLATE=utf8_general_ci;";

## add PATH for test (ONLY FOR TEST SERVER)
eval "sudo sed -i '1s#PATH=\"#PATH=\"${app_path}vendor/bin:#' /etc/environment"

## create local config file (for PHP)
cp ${app_path}app/config/_test.php.default ${app_path}app/config/_test.php
