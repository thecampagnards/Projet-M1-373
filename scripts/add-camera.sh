#!/bin/bash

# check droit root
if [[ $EUID -ne 0 ]]; then
   echo "\e[1;31mLe script doit être lancé en root.\e[0m" 1>&2
   exit 1
fi

# check arguments script
if [[ $# -ne 2 ]]; then
  echo -e "\e[1;31mLes arguments passés au script ne sont pas bons.
Ils doit y avoir le nom de domain du site et l'ip de la caméra.

exemple : ./add-camera.sh monnomdedomaine.fr ipcamera \e[0m" 1>&2
  exit 1
fi

nom_domaine="$1"
ip_camera="$2"

echo "
<VirtualHost *:80>
  ServerName      $nom_domaine
  ServerAdmin     administrateur@$nom_domaine

  ProxyPass               / http://$ip_camera:80/ retry=0 Keepalive=On timeout=1600
  ProxyPassReverse        / http://$ip_camera:80/
  setenv proxy-initial-not-pooled 1
 </VirtualHost>
" > /etc/apache2/sites-available/$nom_domaine.conf

a2ensite $nom_domaine.conf
service apache2 reload
