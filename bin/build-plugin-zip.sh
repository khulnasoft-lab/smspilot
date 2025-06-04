#!/bin/bash

# build-plugin-zip.sh
# Usage: ./build-plugin-zip.sh [plugin_directory]
# Zips the plugin directory into plugin.zip, excluding .git, node_modules, and *.log files.

PLUGIN_DIR="${1:-.}"
ZIP_NAME="plugin.zip"

# Go to the plugin directory
cd "$PLUGIN_DIR" || { echo "Directory $PLUGIN_DIR not found"; exit 1; }

# Remove any existing plugin.zip
rm -f "$ZIP_NAME"

# Create the zip, excluding unwanted files
zip -r "$ZIP_NAME" . \
  -x '*.git*' \
  -x 'node_modules/*' \
  -x '*.log' \
  -x "$ZIP_NAME"

if [ $? -eq 0 ]; then
  echo "Created $PLUGIN_DIR/$ZIP_NAME successfully."
else
  echo "Failed to create zip archive."
  exit 1
fi
