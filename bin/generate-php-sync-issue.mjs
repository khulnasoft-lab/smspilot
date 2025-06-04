#!/usr/bin/env node

// Usage: node generate-php-sync-issue.mjs --title "Bug Title" --description "Bug description"
import { argv } from 'node:process';

function getArg(flag) {
  const idx = argv.indexOf(flag);
  return idx !== -1 && argv[idx + 1] ? argv[idx + 1] : '';
}

const title = getArg('--title');
const description = getArg('--description');

if (!title || !description) {
  console.error('Usage: node generate-php-sync-issue.mjs --title "Bug Title" --description "Bug description"');
  process.exit(1);
}

const issue = `# PHP Sync Issue\n\n**Title:** ${title}\n\n**Description:**\n${description}\n`;
console.log(issue);
