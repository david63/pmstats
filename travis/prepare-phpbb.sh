#!/bin/bash
#
# PM Stats
#
# @copyright (c) 2020 David Wood
# @license GNU General Public License, version 2 (GPL-2.0)
#
set -e
set -x

BRANCH=$1

# Copy extension to a temp folder
mkdir ../../tmp
cp -R . ../../tmp
cd ../../

# Clone phpBB
git clone --depth=1 "git://github.com/phpbb/phpbb.git" "phpBB3" --branch=$BRANCH
