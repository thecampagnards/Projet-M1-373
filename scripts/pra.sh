#!/bin/bash

# check droit root
if [[ $EUID -ne 0 ]]; then
   echo "\e[1;31mLe script doit être lancé en root.\e[0m" 1>&2
   exit 1
fi

# check arguments script
if [[ $# -ne 6 ]]; then
  echo -e "\e[1;31mLes arguments passés au script ne sont pas bons.
Ils doit y avoir le mot de passe du serveur mysql et le nom de domain du site.

exemple : ./pra.sh motdepassesql monnomdedomaine.fr utilisateurssh ipssh motdepassessh dossierinstall\e[0m" 1>&2
  exit 1
fi

# check du nom de domaines
if ! [[ $2 =~ ^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$ ]]; then
  echo -e "\e[1;31mLe nom de domaine n'est pas correct.\e[0m" 1>&2
  exit 1
fi

# paramètres du script
password_mysql="$1"
nom_domaine="$2"
utilisateur_ssh="$3"
ip_ssh="$4"
password_ssh="$5"
dossier_install="$6"
script_dir=$(dirname -- "$(readlink -e -- "$BASH_SOURCE")")

# sauvegarde de la bdd
dbname=$(grep "database_name" ./app/config/parameters.yml | cut -d " " -f 6)
dbuser=$(grep "database_user" ./app/config/parameters.yml | cut -d " " -f 6)
dbpassword=$(grep "database_password" ./app/config/parameters.yml | cut -d " " -f 6)
mysql -u $dbuser --password=$dbpassword $dbname < dump.sql

# recuperation des fichiers
scp -r $utilisateur_ssh:$password_ssh@$ip_ssh:$dossier_install .
# on remet la bdd
ssh $utilisateur_ssh:$password_ssh@$ip_ssh echo "source /$script_dir/dump.sql" | mysql -u $dbuser --password=$motdepassesql $dbname

# on lance le script d'install
ssh $utilisateur_ssh:$password_ssh@$ip_ssh ./install.sh $password_mysql $nom_domaine
