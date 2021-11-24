SHELL=/bin/bash

all:
	@echo "Try sudo make install, if you are on a Debian or Ubuntu system"

checkdeps:
	@if ! which tidy &>/dev/null ; then echo "Tool tidy not found, try sudo apt-get install tidy" 1>&2 ; exit 1 ; fi
	@if ! which xmllint &>/dev/null ; then echo "Tool xmllint not found, try sudo apt-get install libxml2-utils" 1>&2 ; exit 1 ; fi
	@if ! which sponge &>/dev/null ; then echo "Tool sponge not found, try sudo apt-get install moreutils" 1>&2 ; exit 1 ; fi
	@if ! [ -d /usr/share/tt-rss/www/plugins/ ]; then echo "It doesn't look like tt-rss is installed" 1>&2 ; exit 1 ; fi

install: checkdeps
	install -d /usr/share/tt-rss/www/plugins/ff_xmllint
	install -m 644 init.php /usr/share/tt-rss/www/plugins/ff_xmllint/

.PHONY: all checkdeps install
