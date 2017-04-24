#!/bin/bash

# check droit root
if [[ $EUID -ne 0 ]]; then
   echo "\e[1;31mLe script doit être lancé en root.\e[0m" 1>&2
   exit 1
fi

# check arguments script
if [ "$#" -ne 2 ]; then
  echo -e "\e[1;31mLes arguments passés au script ne sont pas bons.
Ils doit y avoir le mot de passe du serveur mysql et le nom de domain du site.

exemple : ./install.sh motdepassesql monnomdedomaine.fr\e[0m" 1>&2
  exit 1
fi

# check du nom de domaines


# paramètres du script
password_mysql="$1"
nom_domaine="$2"
script_dir=$(dirname -- "$(readlink -e -- "$BASH_SOURCE")")

# install des packages
apt-get update
apt-get -y upgrade
debconf-set-selections <<< 'mysql-server mysql-server/root_password password ' $password_mysql
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password ' $password_mysql
apt-get -y install mysql-server
apt-get -y install php apache2 php-mysql git sendmail npm
a2enmod rewrite

# configuration serveur mail

# recupération du dépôt
git init
git remote add origin https://github.com/thecampagnards/Projet-M1-373.git
git fetch
git branch master origin/master
git checkout master
git pull

# installation du projet
echo "parameters:
    database_host: 127.0.0.1
    database_port: null
    database_name: camera
    database_user: root
    database_password: $password_mysql
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: no-reply@camera.fr
    mailer_password: null
    secret: 7cefea36bb067ca8d049edf2028080c8ebab1b60" > app/config/parameters.yml
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
./composer.phar install
npm install -g gulp
npm install
gulp default
bower install ./vendor/sonata-project/admin-bundle/bower.json  --allow-root

# installation bdd
echo "CREATE DATABASE camera" | mysql -u root -p$password_mysql
php bin/console doctrine:schema:update --force

# installation tache cron
crontab -l > mycron
echo "00 00 * * * php $script_dir/bin/console app:camera-reset > /dev/null 2>&1" >> mycron
crontab mycron
rm mycron

# config apache
echo "<VirtualHost *:80>
  ServerName $nom_domaine
  ServerAdmin administrateur@$nom_domaine
  DocumentRoot $script_dir/
  <Directory $script_dir/>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Require all granted
  </Directory>
  ErrorLog /var/log/apache2/$nom_domaine.error.log
  CustomLog /var/log/apache2/.$nom_domaine.access.log combined
</VirtualHost>
" > /etc/apache2/sites-available/$nom_domaine.conf
a2ensite $nom_domaine.conf

# clear du cache + redemarrage apache
php bin/console app:camera-reset
php bin/console cache:clear
php bin/console assets:install
service apache2 restart

# droits web
chown -R www-data:www-data .
