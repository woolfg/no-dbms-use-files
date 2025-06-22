#!/bin/sh

echo "Starting Node.js application..."

# Create a node_modules symlink for the mounted volume
if [ -d "/app/node_modules" ]; then
    # Copy the source files
    cp -r /var/www/html/*.js /app/
    
    echo "Node modules already installed in container"
    echo "Starting application..."
    exec node /app/index.js
else
    echo "ERROR: node_modules not found in container!"
    echo "This should not happen. Trying to install again..."
    npm install
    
    # Copy the source files
    cp -r /var/www/html/*.js /app/
    
    echo "Starting application..."
    exec node /app/index.js
fi