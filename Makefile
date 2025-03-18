PHPIZE ?= phpize
PHP_CONFIG ?= php-config

.PHONY: all build install clean test

all: build

build:
	cargo build --release
	$(PHPIZE) --force
	./configure --enable-php_parallel --with-php-config=$(PHP_CONFIG)
	make

install: build
	make install

test:
	make test

clean:
	cargo clean
	$(PHPIZE) --clean