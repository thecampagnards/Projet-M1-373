#!/bin/bash

# check droit root
if [[ $EUID -ne 0 ]]; then
   echo "\e[1;31mLe script doit être lancé en root.\e[0m" 1>&2
   exit 1
fi

# check arguments script
if [[ $# -ne 2 ]]; then
  echo -e "\e[1;31mLes arguments passés au script ne sont pas bons.
Ils doit y avoir le mot de passe du serveur mysql et le nom de domain du site.

exemple : ./install.sh motdepassesql monnomdedomaine.fr\e[0m" 1>&2
  exit 1
fi

# check du nom de domaines
# if ! [[ $2 =~ ^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$ ]]; then
#   echo -e "\e[1;31mLe nom de domaine n'est pas correct.\e[0m" 1>&2
#   exit 1
# fi

# paramètres du script
password_mysql="$1"
nom_domaine="$2"
script_dir=$(dirname -- "$(readlink -e -- "$BASH_SOURCE")")/www

mkdir www
cd www

# install des packages
echo "
deb http://ftp.hosteurope.de/mirror/packages.dotdeb.org/ jessie all
deb-src http://ftp.hosteurope.de/mirror/packages.dotdeb.org/ jessie all
" >> /etc/apt/sources.list
wget https://www.dotdeb.org/dotdeb.gpg && apt-key add dotdeb.gpg && apt-get update
apt-get -y upgrade
debconf-set-selections <<< 'mysql-server mysql-server/root_password password ' $password_mysql
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password ' $password_mysql
apt-get -y install mysql-server
apt-get -y install php7.0 apache2 php-mysql git sendmail npm wget php7.0-imap php7.0-xml openssl whois
apt-get -y install python-certbot-apache -t jessie-backports
a2enmod rewrite
a2enmod ssl
sudo phpenmod imap
service apache2 force-reload
ln -s /usr/bin/nodejs /usr/bin/node

# configuration serveur mail
iptables -A OUTPUT -p udp --dport 993 -j ACCEPT
iptables -A OUTPUT -p tcp --dport 993 -j ACCEPT

# droits scripts
echo "www-data ALL =(ALL) NOPASSWD: $script_dir/src/AppBundle/Scripts/ftp.sh, /usr/sbin/useradd, /usr/sbin/deluser" >> /etc/sudoers

# https https://gist.github.com/bradland/1690807
# export PASSPHRASE=$(head -c 500 /dev/urandom | tr -dc a-z0-9A-Z | head -c 128; echo)
# subj="
# C=<FRANCE>
# ST=<FRANCE>
# O=<ISEN>
# localityName=<BREST>
# commonName=$nom_domaine
# organizationalUnitName=<ISEN>
# emailAddress=<administrateur@$nom_domaine>
# "
# openssl genrsa -des3 -out $nom_domaine.key -passout env:PASSPHRASE 2048
# openssl req \
#     -new \
#     -batch \
#     -subj "$(echo -n "$subj" | tr "\n" "/")" \
#     -key $nom_domaine.key \
#     -out $nom_domaine.csr \
#     -passin env:PASSPHRASE
# cp $nom_domaine.key $nom_domaine.key.fr
# openssl rsa -in $nom_domaine.key.fr -out $nom_domaine.key -passin env:PASSPHRASE
# openssl x509 -req -days 3650 -in $nom_domaine.csr -signkey $nom_domaine.key -out $nom_domaine.crt

# recupération du dépôt
git init
git remote add origin https://github.com/thecampagnards/Projet-M1-373.git
git fetch
git branch master origin/master
git checkout master
git pull
echo "CREATE DATABASE camera" | mysql -u root --password=$password_mysql

# installation du projet
echo "parameters:
    database_host: 127.0.0.1
    database_port: null
    database_name: camera
    database_user: root
    database_password: $password_mysql
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: no-reply@$nom_domaine
    mailer_password: null
    secret: 7cefea36bb067ca8d049edf2028080c8ebab1b60" > app/config/parameters.yml
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
./composer.phar install
npm cache clean -f
npm install -g n
n stable
npm install -g npm bower gulp
npm install
npm install gulp
npm rebuild node-sass
gulp default
bower install ./vendor/sonata-project/admin-bundle/bower.json  --allow-root

# installation bdd
php bin/console doctrine:schema:update --force

# installation tache cron
crontab -l > mycron
echo "00 00 * * * php $script_dir/bin/console app:camera-reset > /dev/null 2>&1" >> mycron
echo "*/5 * * * * wget --spider http://$nom_domaine/cron/mail > /dev/null 2>&1" >> mycron
echo "*/5 * * * * wget --spider http://$nom_domaine/cron/file > /dev/null 2>&1" >> mycron
crontab mycron
rm mycron

# config apache
echo "<VirtualHost *:80>
  ServerName $nom_domaine
  ServerAdmin administrateur@$nom_domaine
  DocumentRoot $script_dir/web
  <Directory $script_dir/web>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Require all granted
  </Directory>
  ErrorLog /var/log/apache2/$nom_domaine.error.log
  CustomLog /var/log/apache2/.$nom_domaine.access.log combined
</VirtualHost>
# <VirtualHost *:443>
#   ServerName $nom_domaine
#   ServerAdmin administrateur@$nom_domaine
#   DocumentRoot $script_dir/
#   <Directory $script_dir/>
#     Options Indexes FollowSymLinks MultiViews
#     AllowOverride All
#     Require all granted
#   </Directory>
#   ErrorLog /var/log/apache2/$nom_domaine.error.log
#   CustomLog /var/log/apache2/.$nom_domaine.access.log combined
#   SSLEngine on
#   SSLCertificateChainFile $script_dir/$nom_domaine.crt
#   SSLCertificateKeyFile $script_dir/$nom_domaine.key
# </VirtualHost>
" > /etc/apache2/sites-available/$nom_domaine.conf
a2ensite $nom_domaine.conf

certbot --apache

# clear du cache + redemarrage apache
php bin/console app:camera-reset
php bin/console cache:clear
php bin/console assets:install
service apache2 restart

#creation de l'admin
php bin/console fos:user:create --super-admin

# droits web
chown -R www-data:www-data .
