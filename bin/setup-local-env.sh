#!/bin/bash

# Setup local environment

# Check if the .env file exists
if [ ! -f .env ]; then
    echo "Error: .env file not found"
    exit 1
fi
    