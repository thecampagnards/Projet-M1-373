#!/bin/bash

if [[ $EUID -ne 0 ]]; then
   echo "\e[1;31mLe script doit être lancé en root.\e[0m" 1>&2
   exit 1
fi

# check arguments script
if [[ $# -ne 3 ]]; then
  echo -e "\e[1;31mLes arguments passés au script ne sont pas bons.
Ils doit y avoir le mot de passe du serveur mysql et le nom de domain du site.

exemple : ./ftp.sh nom motdepasse repertoire\e[0m" 1>&2
  exit 1
fi

# check du dossier
if [[ ! -d $3 ]]; then
  echo -e "\e[1;31mLe répertoire n'est pas correct.\e[0m" 1>&2
  exit 1
fi

username=$1
pass=$2
dir=$3

egrep "^$username" /etc/passwd >/dev/null
# si le user exist
if [ $? -eq 0 ]; then
  deluser $username
fi

passwordEnc=$(mkpasswd -H md5 $pass)
useradd $username -d $dir -G www-data -s /bin/false -p $passwordEnc
if [ $? -eq 0 ]; then
  echo "$username a été créé."
  exit 0
fi

echo "$username n'a pas été créé."
exit 1
