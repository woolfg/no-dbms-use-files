# Node.js Performance Tests

This directory contains a Node.js implementation of the performance tests to compare:

1. MySQL database access
2. In-memory data structure access (instead of file-based in the PHP version)
3. SQLite database access

## Setup and Running

1. Build the Docker images:
   ```
   make build
   ```

2. Start the containers:
   ```
   make run
   ```

3. Generate test data:
   ```
   make builddata
   ```

4. Run performance tests:
   ```
   make attack
   ```

## Compare with PHP Implementation

To run both PHP and Node.js tests and compare results:
```
make test-php-node
```

## Endpoints

- `/fetch_db` - Fetches data from MySQL
- `/fetch_memory` - Fetches data from in-memory structure
- `/fetch_sqlite` - Fetches data from SQLite
- `/fetch_db_timing` - MySQL with timing details
- `/fetch_memory_timing` - In-memory with timing details
- `/fetch_sqlite_timing` - SQLite with timing details
- `/generate` - Generates test data