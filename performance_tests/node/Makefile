SHELL := /bin/bash
VERSION_TAG=$(shell git rev-parse --short HEAD)

.PHONY: help
help: ## This help message
	@echo -e "$$(grep -hE '^\S+:.*##' $(MAKEFILE_LIST) | sed -e 's/:.*##\s*/:/' -e 's/^\(.\+\):\(.*\)/\\x1b[36m\1\\x1b[m:\2/' | column -c2 -t -s :)"

.PHONY: build
build: check-files ## Build the Docker images
	@echo "Building Docker images..."
	docker compose build
	@echo "Build complete!"

.PHONY: clean-build
clean-build: check-files ## Force rebuild of Docker images
	@echo "Force rebuilding Docker images..."
	docker compose build --no-cache
	@echo "Clean build complete!"

.PHONY: check-files
check-files: ## Check if required files exist
	@echo "Checking if required files exist..."
	@if [ ! -f "./web/src/package.json" ]; then \
		echo "ERROR: package.json is missing in ./web/src/"; \
		echo "Make sure all Node.js source files are in place"; \
		exit 1; \
	fi
	@echo "All required files found!"

.PHONY: run
run: ## Start the Docker containers
	docker compose up -d
	@echo "Waiting for containers to start..."
	sleep 5

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
builddata: ## generates the data in the db, memory, and sqlite
	@echo "Generating test data..."
	curl -s http://localhost:8081/generate
	@echo "Data generation complete."

.PHONY: bash
bash: ## Open a shell inside the web container
	docker compose exec web /bin/sh

.PHONY: attack-db
attack-db: ## Attacks the db version
	docker run --network="host" --rm -i peterevans/vegeta sh -c "echo 'GET http://localhost:8081/fetch_db' | vegeta attack -rate 300 -duration 10s | vegeta report"	

.PHONY: attack-memory
attack-memory: ## Attacks the in-memory version
	docker run --network="host" --rm -i peterevans/vegeta sh -c "echo 'GET http://localhost:8081/fetch_memory' | vegeta attack -rate 300 -duration 10s | vegeta report"	

.PHONY: attack-sqlite
attack-sqlite: ## Attacks the sqlite version
	docker run --network="host" --rm -i peterevans/vegeta sh -c "echo 'GET http://localhost:8081/fetch_sqlite' | vegeta attack -rate 300 -duration 10s | vegeta report"	

.PHONY: warm-db
warm-db: ## Warms the DB cache by running two attacks
	@echo ""
	@echo "Warming up the DB cache..."
	$(MAKE) attack-db
	$(MAKE) attack-db
	@echo "============"
	@echo "WARMED DB - Final test will start"
	@echo "============"

.PHONY: warm-memory
warm-memory: ## Warms the memory cache by running two attacks
	@echo ""
	@echo "Warming up the memory cache..."
	$(MAKE) attack-memory
	$(MAKE) attack-memory
	@echo "============"
	@echo "MEMORY WARMED - Final test will start"
	@echo "============"

.PHONY: warm-sqlite
warm-sqlite: ## Warms the SQLite cache by running two attacks
	@echo ""
	@echo "Warming up the SQLite cache..."
	$(MAKE) attack-sqlite
	$(MAKE) attack-sqlite
	@echo "============"
	@echo "SQLITE WARMED - Final test will start"
	@echo "============"

.PHONY: attack
attack: warm-db attack-db warm-memory attack-memory warm-sqlite attack-sqlite ## warm and attack all variations (db, memory, sqlite)

.PHONY: test-php-node
test-php-node: ## Test both PHP and Node.js implementations
	@echo "Testing PHP implementation..."
	cd ../php && $(MAKE) attack
	@echo "Testing Node.js implementation..."
	$(MAKE) attack
	@echo "============"
	@echo "TESTING COMPLETE"
	@echo "============"

.PHONY: setup
setup: check-files clean-build run builddata ## Build, run, and generate data in a single command
	@echo "============"
	@echo "SETUP COMPLETE"
	@echo "Node.js test environment is ready at http://localhost:8081"
	@echo "============"

.PHONY: troubleshoot
troubleshoot: ## Display troubleshooting information
	@echo "============"
	@echo "TROUBLESHOOTING"
	@echo "============"
	@echo "Docker container status:"
	docker compose ps
	@echo "\nDocker logs:"
	docker compose logs web
	@echo "\nChecking Node.js package.json:"
	cat ./web/src/package.json
	@echo "\nChecking file permissions:"
	ls -la ./web/src/
	@echo "============"
	@echo "If you're still having issues, try:"
	@echo "make clean-build run"
	@echo "============"