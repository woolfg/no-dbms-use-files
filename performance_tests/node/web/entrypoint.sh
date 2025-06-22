#!/bin/sh

# This script ensures that node_modules is preserved when mounting the volume
# It copies the node_modules from the image to the mounted volume

# Check if node_modules exists in the mounted volume
if [ ! -d "/app/node_modules" ]; then
    echo "Node modules directory not found in mounted volume. Setting up..."
    # Copy node_modules from the image to the mounted volume
    mkdir -p /tmp/node_modules
    cp -R /app/node_modules /tmp/
    # Now restore them to the mounted volume
    cp -R /tmp/node_modules/* /app/node_modules/
    echo "Node modules set up complete."
fi

# Start the application
exec "$@"