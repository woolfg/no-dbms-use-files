SHELL := /bin/bash
VERSION_TAG=$(shell git rev-parse --short HEAD)

.PHONY: help
help: ## This help message
	@echo -e "$$(grep -hE '^\S+:.*##' $(MAKEFILE_LIST) | sed -e 's/:.*##\s*/:/' -e 's/^\(.\+\):\(.*\)/\\x1b[36m\1\\x1b[m:\2/' | column -c2 -t -s :)"

.PHONY: build
build: ## Build the Docker images
	docker-compose build

.PHONY: run
run: ## Build the Docker images
	docker-compose up

.PHONY: stop
stop: ## Stop all containers
	docker-compose stop  

.PHONY: bash
bash: ## Open a shell inside the web container
	docker-compose exec web bash

.PHONY: attack-db
attack-db: ## Attacks the db version
	@echo 'GET http://localhost:8080/fetch_db.php' | vegeta attack -rate 300 -duration 10s | vegeta report	

.PHONY: attack-file
attack-file: ## Attacks the file version
	@echo 'GET http://localhost:8080/fetch_file.php' | vegeta attack -rate 300 -duration 10s | vegeta report	

.PHONY: warm-db
warm-db: attack-db attack-db ## Warms the DB cache by running two attacks
	@echo "DB WARMED"

.PHONY: warm-file
warm-file: attack-file attack-file ## Warms the file cache by running two attacks
	@echo "FILES WARMED"

.PHONY: attack
attack: warm-db attack-db warm-file attack-file ## Open a shell inside the web container
