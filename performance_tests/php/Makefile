SHELL := /bin/bash
VERSION_TAG=$(shell git rev-parse --short HEAD)

.PHONY: help
help: ## This help message
	@echo -e "$$(grep -hE '^\S+:.*##' $(MAKEFILE_LIST) | sed -e 's/:.*##\s*/:/' -e 's/^\(.\+\):\(.*\)/\\x1b[36m\1\\x1b[m:\2/' | column -c2 -t -s :)"

.PHONY: build
build: ## Build the Docker images
	docker compose build

.PHONY: run
run: ## Build the Docker images
	docker compose up -d

.PHONY: stop
stop: ## Stop all containers
	docker compose stop  

.PHONY: clean
clean: stop ## Stop and remove all containers incl volumes
	docker compose down --volumes --remove-orphans
	@echo "============"
	@echo "CONTAINERS REMOVED"
	@echo "============"

.PHONY: builddata
builddata: ## generates the hotels in the db and files
	wget -O- http://localhost:8080/generate.php

.PHONY: bash
bash: ## Open a shell inside the web container
	docker compose exec web bash

.PHONY: attack-db
attack-db: ## Attacks the db version
	docker run --network="host" --rm -i peterevans/vegeta sh -c "echo 'GET http://localhost:8080/fetch_db.php' | vegeta attack -rate 300 -duration 10s | vegeta report"	

.PHONY: attack-file
attack-file: ## Attacks the file version
	docker run --network="host" --rm -i peterevans/vegeta sh -c "echo 'GET http://localhost:8080/fetch_file.php' | vegeta attack -rate 300 -duration 10s | vegeta report"	

.PHONY: attack-sqlite
attack-sqlite: ## Attacks the sqlite version
	docker run --network="host" --rm -i peterevans/vegeta sh -c "echo 'GET http://localhost:8080/fetch_sqlite.php' | vegeta attack -rate 300 -duration 10s | vegeta report"	

.PHONY: warm-db
warm-db: ## Warms the DB cache by running two attacks
	$(MAKE) attack-db
	$(MAKE) attack-db
	@echo "============"
	@echo "DB WARMED"
	@echo "============"

.PHONY: warm-file
warm-file: ## Warms the file cache by running two attacks
	$(MAKE) attack-file
	$(MAKE) attack-file
	@echo "============"
	@echo "FILES WARMED"
	@echo "============"

.PHONY: warm-sqlite
warm-sqlite: ## Warms the SQLite cache by running two attacks
	$(MAKE) attack-sqlite
	$(MAKE) attack-sqlite
	@echo "============"
	@echo "SQLITE WARMED"
	@echo "============"

.PHONY: attack
attack: warm-db attack-db warm-file attack-file warm-sqlite attack-sqlite ## warm and attack all variations (db, file, sqlite)