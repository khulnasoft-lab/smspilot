#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

function printUsage() {
  console.log('Usage: node log-performance-results.js <results.json>');
}

const file = process.argv[2];
if (!file) {
  printUsage();
  process.exit(1);
}

const filePath = path.resolve(process.cwd(), file);
if (!fs.existsSync(filePath)) {
  console.error(`File not found: ${filePath}`);
  process.exit(1);
}

let data;
try {
  data = JSON.parse(fs.readFileSync(filePath, 'utf8'));
} catch (e) {
  console.error('Failed to parse JSON:', e.message);
  process.exit(1);
}

console.log('Performance Results Summary:');
if (data.duration !== undefined) {
  console.log(`- Duration: ${data.duration} ms`);
}
if (data.memory !== undefined) {
  console.log(`- Memory Usage: ${data.memory} MB`);
}
if (data.success !== undefined) {
  console.log(`- Successes: ${data.success}`);
}
if (data.failure !== undefined) {
  console.log(`- Failures: ${data.failure}`);
}
// Print any additional top-level metrics
Object.keys(data).forEach(key => {
  if (!["duration","memory","success","failure"].includes(key)) {
    console.log(`- ${key}: ${data[key]}`);
  }
});
