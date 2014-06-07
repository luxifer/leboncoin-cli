TARGET = leboncoin-cli-$(shell cat VERSION)
ROOT = $(shell pwd)

all: dist

dist: dependencies
	mkdir /tmp/$(TARGET)
	cp -r . /tmp/$(TARGET)
	cd /tmp/$(TARGET) $$ \
		rm -rf .git && \
		find config -type f -name "*.yml" -exec rm {} \; && \
		rm -f var/database.sqlite
	cd /tmp && \
		tar czf $(TARGET).tar.gz $(TARGET)
	mv /tmp/$(TARGET).tar.gz $(ROOT)
	rm -rf /tmp/$(TARGET)

dependencies:
	if [ ! -e "composer.phar" ]; then curl -sS https://getcomposer.org/installer | php; fi
	php composer.phar install --prefer-dist --optimize-autoloader

.PHONY: all
