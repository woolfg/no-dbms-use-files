# No DBMS, Use Files (Performance Tests)

This repository contains performance comparison tests between different data storage approaches:
- Database storage (MySQL)
- File-based storage (PHP) / In-memory storage (Node.js)
- SQLite storage

The tests measure and compare the performance characteristics of these different approaches in both PHP and Node.js environments.

## Project Structure

```
├── performance_test_results/   # Directory for storing performance test results
└── performance_tests/
    ├── node/                   # Node.js implementation
    └── php/                    # PHP implementation
```

## Key Components

1. **PHP Implementation**:
   - Uses file-based storage, MySQL, and SQLite
   - Each storage method has both regular and timing-enabled endpoints
   - Performance tests measure throughput and latency

2. **Node.js Implementation**:
   - Uses in-memory storage, MySQL, and SQLite
   - Each storage method has both regular and timing-enabled endpoints
   - Performance tests measure throughput and latency

3. **Docker Configuration**:
   - Each implementation uses Docker for consistent testing environments
   - Includes a MariaDB container for database tests
   - Web containers expose different ports (PHP: 8080, Node.js: 8081)

4. **Performance Testing**:
   - Uses Vegeta (https://github.com/tsenart/vegeta) for load testing
   - Tests include "warm-up" runs to prime caches
   - Results can be compared between PHP and Node.js implementations

## Running the Tests

### PHP Tests

```bash
cd performance_tests/php
make build        # Build the Docker images
make run          # Start the containers
make builddata    # Generate test data
make attack       # Run all performance tests
```

### Node.js Tests

```bash
cd performance_tests/node
make build        # Build the Docker images
make run          # Start the containers
make builddata    # Generate test data
make attack       # Run all performance tests
```

## Test Results

Performance test results are stored in the `performance_test_results` directory. The naming convention for result files is:
- `YYYYMMDD_node.out` - Node.js test results
- `YYYYMMDD_php.out` - PHP test results

## Data Generation

Both implementations generate the same test data structure:
- 10,000 sample "hotel" entries (can be adjusted)
- Each entry has an ID, name, features, and a text field
- Data is stored in all three storage formats (file/memory, MySQL, SQLite)

## License

See the [LICENSE](LICENSE) file for details.