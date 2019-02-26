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

.PHONY: attack
attack: ## Open a shell inside the web container
	@echo "FILE BASED ATTACK:"
	@echo 'GET http://localhost:8080/fetch_file.php' | vegeta attack -rate 300 -duration 10s | vegeta report
	@echo "DB BASED ATTACK:"
	@echo 'GET http://localhost:8080/fetch_db.php' | vegeta attack -rate 300 -duration 10s | vegeta report
