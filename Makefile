DEPLOYFOLDER=/srv/http
SRCFOLDER=./src

default: deploy
	echo

deploy:
	rm -rf $(DEPLOYFOLDER)/*
	cp -R $(SRCFOLDER)/.* $(SRCFOLDER)/* $(DEPLOYFOLDER)
