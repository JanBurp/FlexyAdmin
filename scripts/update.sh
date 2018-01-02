#!/bin/sh
. ./scripts/hello.sh

BRANCH_FLEXYADMIN=""
BRANCH_LOCAL=""

if [ -z "$BRANCH_FLEXYADMIN" ]
then
	echo "${RED} BRANCH_FLEXYADMIN not set!"
	exit 0
fi
if [ -z "$BRANCH_LOCAL" ]
then
	echo "${RED} BRANCH_LOCAL not set!"
	exit 0
fi

# Make sure we start local and stash our possible changes
echo "${GREEN}\nStash local changes:\n${RESET}"
git checkout ${BRANCH_LOCAL}
git stash

# Checkout FlexyAdmin & Pull latest update from FlexyAdmin
echo "${GREEN}\nPull latest FlexyAdmin:\n${RESET}"
git checkout ${BRANCH_FLEXYADMIN}
git pull

# Checkout Local & Merge with latest FlexyAdmin
echo "${GREEN}\nMerge with latest:\n${RESET}"
git checkout ${BRANCH_LOCAL}
git merge ${BRANCH_FLEXYADMIN}

# Resolve standard issues

# Return to latest state (stash)
echo "${GREEN}\nReturn last state (apply stash):\n${RESET}"
git stash apply