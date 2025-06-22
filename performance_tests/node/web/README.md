# Node.js Performance Test Web Server

This directory contains the Docker setup for the Node.js performance test web server.

## Directory Structure

- `Dockerfile` - Defines the Node.js container
- `init.sh` - Initialization script that runs when the container starts
- `src/` - Contains the Node.js application source code

## Building and Running

The container is built and run using the Makefile in the parent directory:

```
cd ..
make setup
```

This will build the Docker image, start the container, and generate test data.

## Troubleshooting

If you encounter any issues:

1. Check that all source files are in the `src/` directory
2. Ensure the volume mount is working correctly
3. Verify that `package.json` exists in the `src/` directory
4. Check Docker logs with `docker compose logs web`