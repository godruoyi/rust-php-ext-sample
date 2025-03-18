PHPIZE ?= phpize
PHP_CONFIG ?= php-config

.PHONY: all build install clean test

all: build

build:
	cargo build --release
	$(PHPIZE) --force
	./configure --with-php-config=$(PHP_CONFIG)
	make

install: build
	make install

test: build
	php -d extension=./modules/php_parallel.so -m | grep parallel

clean:
	cargo clean
	$(PHPIZE) --clean