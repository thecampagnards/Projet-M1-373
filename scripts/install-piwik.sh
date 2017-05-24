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

password_mysql="$1"
nom_domaine="$2"
script_dir=$(dirname -- "$(readlink -e -- "$BASH_SOURCE")")/www

#telechargement de piwik
wget https://builds.piwik.org/piwik.zip && unzip piwik.zip
mv piwik www

# creation db
echo "CREATE DATABASE piwik" | mysql -u root --password=$password_mysql

# config apache
echo "<VirtualHost *:80>
  ServerName $nom_domaine
  ServerAdmin administrateur@$nom_domaine
  DocumentRoot $script_dir
  <Directory $script_dir>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Require all granted
  </Directory>
  ErrorLog /var/log/apache2/$nom_domaine.error.log
  CustomLog /var/log/apache2/.$nom_domaine.access.log combined
</VirtualHost>
" > /etc/apache2/sites-available/$nom_domaine.conf
a2ensite $nom_domaine.conf
service apache2 restart

chown -R www-data:www-data .
