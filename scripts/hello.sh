#!/bin/sh
RED=`tput setaf 1`
GREEN=`tput setaf 2`
RESET=`tput sgr0`
BUILD=`grep '^[^\s]*' sys/build.txt | cut -d " " -f1`

echo "${GREEN}FlexyAdmin (${BUILD})\n==========\n${RESET}"