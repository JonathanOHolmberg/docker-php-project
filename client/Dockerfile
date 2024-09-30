FROM node:alpine

# Install serve
RUN npm install -g serve

# Set working directory
WORKDIR /app

# Copy static files
COPY index.html .
COPY script.js .

# Expose port 5000
EXPOSE 5000

# Start serve
CMD ["serve", "-s", ".", "-l", "5000"]
