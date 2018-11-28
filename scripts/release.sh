#!/bin/sh
export RED=`tput setaf 1`
export GREEN=`tput setaf 2`
export RESET=`tput sgr0`
export BUILD=`grep '^[^\s]*' sys/build.txt | cut -d " " -f1`

echo "${GREEN}FlexyAdmin - Removing sources (${BUILD})\n==================\n${RESET}"

rm -rf sys/flexyadmin/assets/js
rm -rf sys/flexyadmin/assets/scss
rm -rf sys/flexyadmin/install
rm -f sys/.babel*
rm -f sys/.eslint*
rm -f sys/composer*
rm -f sys/webpack*
rm -rf userguide/vendor
rm -f userguide/composer*
rm -f userguide/*doc*