#!/bin/bash
DIR="$(cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd)"
cd "$DIR"

if [ "$PHP_BINARY" == "" ]; then
	if [ -f ./bin/php/bin/php ]; then
		export PHPRC=""
		PHP_BINARY="./bin/php/bin/php"
	elif type php 2>/dev/null; then
		PHP_BINARY=$(type -p php)
	else
		echo "找不到一个可以工作的 PHP 二进制文件."
		exit 1
	fi
fi

if [ "$PHQAGENT_FILE" == "" ]; then
	if [ -f ./PhQAgent.phar ]; then
		PHQAGENT_FILE="./PhQAgent.phar"
	elif [ -f ./src/phqagent/PhQAgent.php ]; then
		PHQAGENT_FILE="./src/phqagent/PhQAgent.php"
	else
		echo "找不到一个有效的 PhQAgent 实例."
		exit 1
	fi
fi

exec "$PHP_BINARY" "$PHQAGENT_FILE" $@
