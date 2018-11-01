#!/bin/sh
export RED=`tput setaf 1`
export GREEN=`tput setaf 2`
export RESET=`tput sgr0`
export BUILD=`grep '^[^\s]*' sys/build.txt | cut -d " " -f1`

echo "${GREEN}FlexyAdmin (${BUILD})\n==================\n${RESET}"