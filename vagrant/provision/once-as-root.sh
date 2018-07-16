#!/usr/bin/env bash

app_path=$(echo "$1")
root_pass=$(echo "$2")
db_name=$(echo "$3")
db_user=$(echo "$4")
db_pass=$(echo "$5")

## update / upgrade

sudo apt update
sudo apt -y upgrade

## install MySQL
echo "mysql-server mysql-server/root_password password ${root_pass}" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password ${root_pass}" | debconf-set-selections
sudo apt -y install mysql-server

## Dev DB
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

## install apache2 and other tools
sudo apt -y install vim \
                    htop \
                    mc \
                    git \
                    curl

## install php and php-extensions
sudo apt -y install php \
                    php-mcrypt \
                    php-mysql \
                    php-xml \
                    php-curl \
                    php-mbstring

sudo service mysql restart

sudo apt -y autoremove

sudo apt clean

## create local config file (for PHP)
cp ${app_path}app/config/_local.php.default ${app_path}app/config/_local.php

## install Composer
sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer
