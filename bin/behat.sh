#!/bin/sh
ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../" && pwd )"

cd $ROOT_DIR/tools/behat &&
$ROOT_DIR/vendor/bin/behat features
