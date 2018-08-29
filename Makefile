all: fresh build install

fresh:
	git pull

build: 
	echo build

install: build
#	cp -rvf src/FlexiPeeHP /usr/share/php/FlexiPeeHP
	
clean:
	rm -rf debian/flexipeehp-bricks
	rm -rf debian/flexipeehp-bricks-doc
	rm -rf debian/*.log
	rm -rf debian/*.substvars
	rm -rf docs/*

doc:
	VERSION=`cat debian/composer.json | grep version | awk -F'"' '{print $4}'`; \
	php5 -f /usr/bin/apigen generate --source FlexiPeeHP --destination docs --title "FlexiPeeHP-Bricks ${VERSION}" --charset UTF-8 --access-levels public --access-levels protected --php --tree

test:
	composer update
	phpunit --bootstrap testing/bootstrap.php

changelog:
	CHANGES=`git log -n 1 | tail -n+5` ; dch -b -v `cat debian/version`-`cat debian/revision` --package flexipeehp-bricks "$(CHANGES)"

deb: changelog
	dpkg-buildpackage -A -us -uc

rpm:
	rpmdev-bumpspec --comment="Build" --userstring="Vítězslav Dvořák <info@vitexsoftware.cz>" flexipeehp-bricks.spec
	rpmbuild -ba flexipeehp.spec 

verup:
	git commit debian/composer.json debian/version debian/revision  -m "`cat debian/version`-`cat debian/revision`"
	git push origin master

.PHONY : install
	
