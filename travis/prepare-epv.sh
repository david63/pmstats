#!/bin/bash
#
# PM Stats
#
# @copyright (c) 2020 David Wood
# @license GNU General Public License, version 2 (GPL-2.0)
#
set -e
set -x

NOTESTS=$1

if [ "$NOTESTS" == "1" ]
then
	cd phpBB
	composer remove sami/sami --dev --no-interaction
	composer require phpbb/epv:dev-master --dev --no-interaction --ignore-platform-reqs
	cd ../
fi
