FROM node:18-alpine

# Set working directory
WORKDIR /app

# Copy package.json and install dependencies
COPY ./src/package.json /app/
RUN npm install

# Expose port
EXPOSE 80

# Command to run the application
CMD ["sh", "-c", "node /app/index.js"]