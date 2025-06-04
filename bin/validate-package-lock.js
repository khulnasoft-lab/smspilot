#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

const lockPath = path.resolve(__dirname, '../package-lock.json');
if (fs.existsSync(lockPath)) {
  console.log('package-lock.json found.');
  process.exit(0);
} else {
  console.error('package-lock.json NOT found!');
  process.exit(1);
}
